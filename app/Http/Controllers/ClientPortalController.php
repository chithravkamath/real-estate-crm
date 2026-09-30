<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Property;
use App\Models\SiteVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ClientPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        if (!$client) {
            $client = \App\Models\Client::where('email', $user->email)->first();
        }
        $clientName = $client ? $client->name : $user->name;

        if ($client) {
            $bookingsQuery = Deal::where(function ($query) use ($client) {
                $query->where('client_id', $client->id)
                      ->orWhere(function ($q) use ($client) {
                          $q->whereNull('client_id')
                            ->where('client_name', $client->name);
                      });
            });
        } else {
            $bookingsQuery = Deal::where('client_name', $clientName);
        }

        $bookings = (clone $bookingsQuery)->latest('booking_date')->take(3)->get();
        
        // Fetch all billing records for the client (both by ID and name)
        $payments = collect();
        if ($client) {
            $paymentsById = Billing::where('client_id', $client->id)->latest()->get();
            $paymentsByName = Billing::where('client_name', $clientName)
                ->where(function ($query) use ($client) {
                    $query->whereNull('client_id')->orWhere('client_id', '!=', $client->id);
                })
                ->latest()
                ->get();
            $payments = $paymentsById->concat($paymentsByName);
        } else {
            $payments = Billing::where('client_name', $clientName)->latest()->get();
        }
        
        if ($client) {
            $visits = SiteVisit::where('client_id', $client->id)
                ->orWhere(function ($query) use ($client, $clientName) {
                    $query->whereNull('client_id')->where('client_name', $clientName);
                })
                ->latest('visit_date')
                ->take(3)
                ->get();

            $propertiesViewed = SiteVisit::where('client_id', $client->id)
                ->orWhere(function ($query) use ($client, $clientName) {
                    $query->whereNull('client_id')->where('client_name', $clientName);
                })
                ->count();
        } else {
            $visits = SiteVisit::where('client_name', $clientName)->latest('visit_date')->take(3)->get();
            $propertiesViewed = SiteVisit::where('client_name', $clientName)->count();
        }
        $myBookings = (clone $bookingsQuery)->count();
        $pendingPayments = $payments->where('payment_status', 'Pending')->count();

        // Fetch related reminders and communication logs if the Client record is found
        $reminders = collect();
        $communications = collect();
        if ($client) {
            $reminders = \App\Models\Reminder::where('related_type', 'Client')
                ->where('related_id', $client->id)
                ->latest('reminder_date')
                ->take(3)
                ->get();
            $communications = \App\Models\Communication::where('client_id', $client->id)
                ->latest('communication_date')
                ->take(3)
                ->get();
        }

        // Build Chronological Recent Activity log
        $activityReminders = $reminders->map(function ($reminder) {
            $dt = \Carbon\Carbon::parse($reminder->reminder_date);
            if ($reminder->reminder_time) {
                $parts = explode(':', $reminder->reminder_time);
                if (count($parts) >= 2) {
                    $dt->setTime($parts[0], $parts[1], $parts[2] ?? 0);
                }
            }
            return [
                'message' => "Reminder: {$reminder->title} on " . \Carbon\Carbon::parse($reminder->reminder_date)->format('F d, Y'),
                'date' => $dt
            ];
        });

        $activityCommunications = $communications->map(function ($comm) {
            return [
                'message' => "Communication: {$comm->communication_type} - " . \Illuminate\Support\Str::limit($comm->notes, 40),
                'date' => \Carbon\Carbon::parse($comm->communication_date)
            ];
        });

        $activityVisits = $visits->map(function ($visit) {
            $dt = \Carbon\Carbon::parse($visit->visit_date);
            if ($visit->visit_time) {
                $parts = explode(':', $visit->visit_time);
                if (count($parts) >= 2) {
                    $dt->setTime($parts[0], $parts[1], $parts[2] ?? 0);
                }
            }
            return [
                'message' => "Site visit scheduled for {$visit->property_name} on " . \Carbon\Carbon::parse($visit->visit_date)->format('F d, Y'),
                'date' => $dt
            ];
        });

        $activityBookings = $bookings->map(function ($deal) {
            $action = $deal->booking_status === 'confirmed' ? 'confirmed' : 'updated';
            return [
                'message' => "Booking {$action} for property {$deal->property_name}",
                'date' => \Carbon\Carbon::parse($deal->booking_date ?? $deal->created_at)
            ];
        });

        $activityPayments = $payments->map(function ($billing) {
            return [
                'message' => "Payment update: Invoice {$billing->invoice_number} is " . ($billing->payment_status ?? 'Pending'),
                'date' => \Carbon\Carbon::parse($billing->payment_date ?? $billing->created_at)
            ];
        });

        $recentActivity = collect()
            ->concat($activityReminders)
            ->concat($activityCommunications)
            ->concat($activityVisits)
            ->concat($activityBookings)
            ->concat($activityPayments)
            ->sortByDesc(function ($act) {
                return $act['date']->timestamp;
            })
            ->take(5)
            ->values();

        return view('client.client-dashboard', compact(
            'propertiesViewed',
            'myBookings',
            'pendingPayments',
            'recentActivity',
            'bookings'
        ));
    }

    public function properties()
    {
        $properties = Property::orderBy('property_name')->get();

        return view('client.client-properties', compact('properties'));
    }

    public function propertyDetails(Request $request)
    {
        $user = Auth::user();
        $client = \App\Models\Client::where('email', $user->email)->first();
        $clientName = $client ? $client->name : $user->name;
        $propertyId = $request->query('property_id');

        $property = Property::find($propertyId);

        if (! $property) {
            if ($client) {
                $siteVisitPropNames = SiteVisit::where('client_id', $client->id)
                    ->orWhere(function ($query) use ($client, $clientName) {
                        $query->whereNull('client_id')->where('client_name', $clientName);
                    })
                    ->pluck('property_name');
            } else {
                $siteVisitPropNames = SiteVisit::where('client_name', $clientName)->pluck('property_name');
            }

            $propertyNames = Deal::where('client_name', $clientName)
                ->pluck('property_name')
                ->merge($siteVisitPropNames)
                ->unique()
                ->filter();

            $property = Property::whereIn('property_name', $propertyNames)->first() ?? Property::first();
        }

        return view('client.client-property-details', compact('property'));
    }

    public function bookings()
    {
        $client = Client::where('user_id', auth()->id())->first();

        if ($client) {
            $bookings = Deal::with('property')
                ->where(function ($query) use ($client) {
                    $query->where('client_id', $client->id)
                          ->orWhere(function ($q) use ($client) {
                              $q->whereNull('client_id')
                                ->where('client_name', $client->name);
                          });
                })
                ->latest()
                ->get();
        } else {
            $bookings = collect();
        }
      
        return view(
            'client.client-bookings',
            compact('bookings')
        );
    }

    public function payments()
    {
        $user = Auth::user();
        $client = Client::where('user_id', auth()->id())->first();

        $payments = collect();
        
        if ($client) {
            // Fetch billing records by client_id (primary relationship)
            $paymentsById = Billing::where('client_id', $client->id)->latest()->get();
            
            // Also fetch by client name to capture older records that may not have client_id set
            $paymentsByName = Billing::where('client_name', $client->name)
                ->where(function ($query) use ($client) {
                    $query->whereNull('client_id')->orWhere('client_id', '!=', $client->id);
                })
                ->latest()
                ->get();
            
            // Merge both collections and remove duplicates
            $payments = $paymentsById->concat($paymentsByName);
        }

        return view('client.client-payments', compact('payments'));
    }

    public function updates()
    {
        $client = Client::where('user_id', auth()->id())->first();

        $bookings = collect();
        $payments = collect();
        $visits = collect();
        $reminders = collect();
        $communications = collect();

        if ($client) {
            $bookings = Deal::where('client_id', $client->id)->latest()->get();
            
            // Fetch billing records by client_id (primary relationship)
            $paymentsById = Billing::where('client_id', $client->id)->latest()->get();
            
            // Also fetch by client name to capture older records that may not have client_id set
            $paymentsByName = Billing::where('client_name', $client->name)
                ->where(function ($query) use ($client) {
                    $query->whereNull('client_id')->orWhere('client_id', '!=', $client->id);
                })
                ->latest()
                ->get();
            
            // Merge both collections
            $payments = $paymentsById->concat($paymentsByName);
            
            $visits = SiteVisit::where('client_id', $client->id)
                ->orWhere(function ($query) use ($client) {
                    $query->whereNull('client_id')->where('client_name', $client->name);
                })
                ->latest('visit_date')
                ->get();
            $reminders = \App\Models\Reminder::where('related_type', 'Client')
                ->where('related_id', $client->id)
                ->latest('reminder_date')
                ->get();
            $communications = \App\Models\Communication::where('client_id', $client->id)
                ->latest('communication_date')
                ->get();
        }

        // Build Chronological Recent Activity log
        $activityReminders = $reminders->map(function ($reminder) {
            $dt = \Carbon\Carbon::parse($reminder->reminder_date);
            if ($reminder->reminder_time) {
                $parts = explode(':', $reminder->reminder_time);
                if (count($parts) >= 2) {
                    $dt->setTime($parts[0], $parts[1], $parts[2] ?? 0);
                }
            }
            return [
                'date' => $dt,
                'message' => "Reminder: {$reminder->title} - " . ($reminder->notes ?? 'No notes available.'),
                'type' => 'Reminder',
                'label_class' => 'label-warning'
            ];
        });

        $activityCommunications = $communications->map(function ($comm) {
            return [
                'date' => \Carbon\Carbon::parse($comm->communication_date),
                'message' => "Communication log: {$comm->communication_type} - " . ($comm->notes ?? 'No notes logged.'),
                'type' => ucfirst($comm->communication_type) ?: 'Communication',
                'label_class' => 'label-info'
            ];
        });

        $activityVisits = $visits->map(function ($visit) {
            $dt = \Carbon\Carbon::parse($visit->visit_date);
            if ($visit->visit_time) {
                $parts = explode(':', $visit->visit_time);
                if (count($parts) >= 2) {
                    $dt->setTime($parts[0], $parts[1], $parts[2] ?? 0);
                }
            }
            return [
                'date' => $dt,
                'message' => "Site visit scheduled for {$visit->property_name} with agent {$visit->agent_name}.",
                'type' => 'Site Visit',
                'label_class' => 'label-primary'
            ];
        });

        $activityBookings = $bookings->map(function ($deal) {
            $action = $deal->booking_status === 'confirmed' ? 'confirmed' : 'updated';
            return [
                'date' => \Carbon\Carbon::parse($deal->booking_date ?? $deal->created_at),
                'message' => "Booking {$action} for property {$deal->property_name} (Deal Status: {$deal->status}).",
                'type' => 'Booking',
                'label_class' => 'label-primary'
            ];
        });

        $activityPayments = $payments->map(function ($billing) {
            return [
                'date' => \Carbon\Carbon::parse($billing->payment_date ?? $billing->created_at),
                'message' => "Payment update: Invoice {$billing->invoice_number} is " . ($billing->payment_status ?? 'Pending') . " (Due Amount: Rs. " . number_format(floatval($billing->due_amount ?? $billing->payment_amount), 2) . ").",
                'type' => 'Payment',
                'label_class' => 'label-success'
            ];
        });

        $updates = collect()
            ->concat($activityReminders)
            ->concat($activityCommunications)
            ->concat($activityVisits)
            ->concat($activityBookings)
            ->concat($activityPayments)
            ->sortByDesc(function ($act) {
                return $act['date']->timestamp;
            })
            ->take(30)
            ->values();

        return view('client.client-updates', compact('updates'));
    }

    /**
     * Request a site visit for a property.
     */
    public function requestVisit(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $user = Auth::user();
        $client = \App\Models\Client::where('email', $user->email)->first();
        $clientName = $client ? $client->name : $user->name;

        $property = \App\Models\Property::findOrFail($request->property_id);

        $propStatus = strtolower($property->status ?? 'available');
        if ($propStatus === 'booked') {
            return redirect()->back()->with('warning', "Visit requests are unavailable for booked properties.");
        } elseif ($propStatus === 'sold') {
            return redirect()->back()->with('warning', "This property has already been sold.");
        }

        // Check if there is already a pending visit request for this property by this client
        $exists = \App\Models\SiteVisit::where(function ($query) use ($client, $clientName) {
                if ($client) {
                    $query->where('client_id', $client->id)
                          ->orWhere(function ($q) use ($clientName) {
                              $q->whereNull('client_id')->where('client_name', $clientName);
                          });
                } else {
                    $query->where('client_name', $clientName);
                }
            })
            ->where('property_name', $property->property_name)
            ->where('status', 'Pending')
            ->exists();

        if ($exists) {
            return redirect()->back()->with('warning', "You have already requested a visit for this property.");
        }

        // Find the default agent to assign this request to
        $agent = \App\Models\User::where('role', 'agent')->first();
        $agentName = $agent ? $agent->name : 'Unassigned';
        $agentId = $agent ? $agent->id : null;

        \App\Models\SiteVisit::create([
            'client_id' => $client ? $client->id : null,
            'client_name' => $clientName,
            'property_name' => $property->property_name,
            'visit_date' => now()->toDateString(),
            'visit_time' => now()->format('H:i:s'),
            'agent_name' => $agentName,
            'agent_id' => $agentId,
            'status' => 'Pending',
            'notes' => 'Visit requested by client online.',
        ]);

        \App\Models\AuditLog::create([
            'user_name' => $user->name,
            'action' => 'Request Visit',
            'module' => 'Client Portal',
            'description' => "Requested a site visit for property: {$property->property_name}"
        ]);

        return redirect()->back()->with('success', "Visit request submitted successfully! An agent will contact you shortly.");
    }

    /**
     * Mark a property as interested.
     */
    public function interestedProperty(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $user = Auth::user();
        $client = \App\Models\Client::where('email', $user->email)->first();
        $clientName = $client ? $client->name : $user->name;
        $clientPhone = $client ? $client->phone : ($user->phone ?? 'N/A');

        $property = \App\Models\Property::findOrFail($request->property_id);

        $propStatus = strtolower($property->status ?? 'available');
        if ($propStatus === 'sold') {
            return redirect()->back()->with('warning', "This property has already been sold.");
        }

        // Check if a lead with this email already exists to prevent SQL duplicate entry errors
        $lead = \App\Models\Lead::where('email', $user->email)->first();

        if ($lead) {
            // Check if they are already interested in this specific property
            if ($lead->interested_property === $property->property_name) {
                return redirect()->back()->with('warning', "You have already marked this property as interested.");
            }

            // Lead exists but for a different property. Update their interest safely
            $lead->update([
                'interested_property' => $property->property_name,
                'budget' => $property->price,
                'status' => 'New',
                'notes' => ($lead->notes ? $lead->notes . "\n" : "") . "[Update] Expressed interest in {$property->property_name} online via Client Portal."
            ]);
        } else {
            // Find default agent
            $agent = \App\Models\User::where('role', 'agent')->first();
            $agentName = $agent ? $agent->name : 'Unassigned';
            $agentId = $agent ? $agent->id : null;

            // No lead exists, create a new one safely
            \App\Models\Lead::create([
                'name' => $clientName,
                'email' => $user->email,
                'phone' => $clientPhone,
                'source' => 'Website Interest',
                'status' => 'New',
                'interested_property' => $property->property_name,
                'budget' => $property->price,
                'notes' => 'Expressed interest online via Client Portal.',
                'agent' => $agentName,
                'assigned_agent_id' => $agentId,
            ]);
        }

        \App\Models\AuditLog::create([
            'user_name' => $user->name,
            'action' => 'Interest Expressed',
            'module' => 'Client Portal',
            'description' => "Marked interest in property: {$property->property_name}"
        ]);

        return redirect()->back()->with('success', "Property marked as interested! Added to follow-up workflow.");
    }

    /**
     * Download client payment invoice.
     */
    public function downloadInvoice($id)
    {
        $billing = Billing::findOrFail($id);

        $client = Client::where('user_id', Auth::id())->first();
        if (!$client) {
            $client = Client::where('email', Auth::user()->email)->first();
        }

        // Ensure only authenticated clients can download their own invoices
        if (!$client || ($billing->client_id !== $client->id && $billing->client_name !== $client->name)) {
            abort(403, 'Unauthorized action.');
        }

        $pdf = Pdf::loadView('client.invoice-pdf', compact('billing'));
        return $pdf->download("invoice-{$billing->invoice_number}.pdf");
    }
}
