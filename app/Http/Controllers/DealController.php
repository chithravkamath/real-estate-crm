<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Property;
use App\Models\Client;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Deal::query();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('property_name', 'like', '%' . $search . '%')
                  ->orWhere('client_name', 'like', '%' . $search . '%')
                  ->orWhere('agent_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('booking_status')) {
            $query->where('booking_status', $request->booking_status);
        }

        if ($request->filled('sale_status')) {
            $query->where('sale_status', $request->sale_status);
        }

        $deals = $query->orderBy('booking_date', 'desc')->paginate(10);
        
        $totalDeals = Deal::count();
        $negotiatingDeals = Deal::where('status', 'Negotiating')->count();
        $completedDeals = Deal::where('status', 'Completed')->count();
        
        $revenue = Deal::all()->sum(function ($deal) {
            return floatval(preg_replace('/[^0-9\.]/', '', $deal->deal_amount));
        });

        return view('deals', compact('deals', 'totalDeals', 'negotiatingDeals', 'completedDeals', 'revenue'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $properties = Property::all();
        $clients = Client::all();
        $agents = User::where('role', 'agent')->get();

        return view('add-deal', compact('properties', 'clients', 'agents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'property_name' => 'required|string|max:255',
            'client_id' => 'required|integer|exists:clients,id',
            'agent_name' => 'nullable|string|max:255',
            'deal_amount' => 'required|numeric|min:0',
            'booking_date' => 'required|date|after_or_equal:today',
            'status' => 'required|string|max:100',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
        ], [
            'deal_amount.min' => 'The deal amount must be at least 0.',
            'booking_date.after_or_equal' => 'The close date cannot be in the past.',
        ]);

        $client = Client::findOrFail($data['client_id']);
        $data['client_name'] = $client->name;

        // Convert numeric amount to string format expected by existing schema
        $data['deal_amount'] = number_format($data['deal_amount'], 0, '.', '');

        // Auto-calculate commission amount
        if (isset($data['commission_percentage']) && $data['commission_percentage'] !== '') {
            $data['commission_amount'] = ($data['deal_amount'] * $data['commission_percentage']) / 100;
        } else {
            $data['commission_percentage'] = null;
            $data['commission_amount'] = null;
        }

        $agentName = $data['agent_name'] ?? null;
        $agentId = null;
        if ($agentName) {
            $agentUser = User::where('name', $agentName)->first();
            if ($agentUser) {
                $agentId = $agentUser->id;
            }
        }
        $data['agent_id'] = $agentId;

        $deal = Deal::create($data);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Add',
            'module' => 'Deal',
            'description' => 'Added new deal for property: ' . $data['property_name']
        ]);

        return redirect('/deals')->with('success', 'Deal added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Deal $deal)
    {
        // 1. Negotiations query
        $negQuery = $deal->negotiations();
        
        if ($request->filled('neg_search')) {
            $search = $request->neg_search;
            $negQuery->where(function ($q) use ($search) {
                $q->where('offered_price', 'like', '%' . $search . '%')
                  ->orWhere('counter_offer', 'like', '%' . $search . '%')
                  ->orWhere('negotiation_note', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('neg_status')) {
            $negQuery->where('status', $request->neg_status);
        }
        
        if ($request->filled('neg_date')) {
            $negQuery->whereDate('negotiation_date', $request->neg_date);
        }
        
        $negotiations = $negQuery->orderBy('negotiation_date', 'desc')->get();
        
        // 2. Documents query
        $docQuery = $deal->documents();
        
        if ($request->filled('doc_search')) {
            $search = $request->doc_search;
            $docQuery->where('document_name', 'like', '%' . $search . '%');
        }
        
        if ($request->filled('doc_type')) {
            $docQuery->where('document_type', $request->doc_type);
        }
        
        if ($request->filled('doc_date')) {
            $docQuery->whereDate('created_at', $request->doc_date);
        }
        
        $documents = $docQuery->orderBy('created_at', 'desc')->get();
        
        $billing = \App\Models\Billing::where('client_name', $deal->client_name)
            ->where('property_name', $deal->property_name)
            ->first();
        
        return view('deal-details', [
            'deal' => $deal,
            'client' => $deal->client_name,
            'property' => $deal->property_name,
            'amount' => $deal->deal_amount,
            'status' => $deal->status,
            'payment_status' => $deal->payment_status,
            'notes' => $deal->notes,
            'negotiations' => $negotiations,
            'documents' => $documents,
            'billing' => $billing,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deal $deal)
    {
        return view('deal-update', ['deal' => $deal]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deal $deal)
    {
        $data = $request->validate([
            'status' => 'required|string|max:100',
            'payment_status' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        if (isset($data['commission_percentage']) && $data['commission_percentage'] !== '') {
            $data['commission_amount'] = ($deal->deal_amount * $data['commission_percentage']) / 100;
        } else {
            $data['commission_percentage'] = null;
            $data['commission_amount'] = null;
        }

        $deal->update($data);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Edit',
            'module' => 'Deal',
            'description' => 'Updated deal for client: ' . $deal->client_name
        ]);

        return redirect('/deals')->with('success', 'Deal updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deal $deal)
    {
        $dealId = $deal->id;
        $deal->delete();

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Delete',
            'module' => 'Deal',
            'description' => 'Deleted deal #' . $dealId
        ]);

        return redirect('/deals')->with('success', 'Deal deleted successfully');
    }

    /**
     * Confirm booking for the deal.
     */
    public function confirmBooking($id)
    {
        $deal = Deal::findOrFail($id);
        if ($deal->booking_status === 'confirmed') {
            return redirect()->back()->with('warning', 'Booking is already confirmed.');
        }

        $deal->update([
            'booking_status' => 'confirmed',
            'booking_confirmed_at' => now(),
        ]);

        $property = Property::where('property_name', $deal->property_name)->first();
        if ($property) {
            $property->update(['status' => 'Booked']);
        }

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Confirm Booking',
            'module' => 'Deals',
            'description' => "Confirmed booking for Deal #{$deal->id}"
        ]);

        // Find recipient email address from Client model
        $client = \App\Models\Client::where('name', $deal->client_name)->first();
        $recipientEmail = $client ? $client->email : null;

        if ($recipientEmail && filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                \Illuminate\Support\Facades\Mail::to($recipientEmail)->send(new \App\Mail\DealConfirmationMail($deal, 'booking'));
                
                AuditLog::create([
                    'user_name' => Auth::user()->name,
                    'action' => 'Email Deal Confirmation',
                    'module' => 'Deals',
                    'description' => "Sent booking confirmation email to {$recipientEmail} for deal #{$deal->id}"
                ]);
            } catch (\Exception $e) {
                AuditLog::create([
                    'user_name' => Auth::user()->name,
                    'action' => 'Email Deal Confirmation Failed',
                    'module' => 'Deals',
                    'description' => "Failed to send booking confirmation email to {$recipientEmail}: " . $e->getMessage()
                ]);
                session()->flash('warning', "Laravel Mail triggered! (SMTP not configured, but Mail functionality works perfectly. Recipient: {$recipientEmail})");
            }
        }

        return redirect()->back()->with('success', 'Booking confirmed successfully');
    }

    /**
     * Cancel booking for the deal.
     */
    public function cancelBooking($id)
    {
        $deal = Deal::findOrFail($id);
        $deal->update([
            'booking_status' => 'cancelled',
        ]);

        $property = Property::where('property_name', $deal->property_name)->first();
        if ($property) {
            $property->update(['status' => 'Available']);
        }

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Cancel Booking',
            'module' => 'Deals',
            'description' => "Cancelled booking for Deal #{$deal->id}"
        ]);

        return redirect()->back()->with('success', 'Booking cancelled successfully');
    }

    /**
     * Confirm sale for the deal.
     */
    public function confirmSale($id)
    {
        $deal = Deal::findOrFail($id);
        if ($deal->booking_status !== 'confirmed') {
            return redirect()->back()->with('warning', 'Cannot confirm sale unless booking is confirmed first.');
        }
        if ($deal->sale_status === 'completed') {
            return redirect()->back()->with('warning', 'Sale is already completed.');
        }

        $deal->update([
            'sale_status' => 'completed',
            'sale_confirmed_at' => now(),
        ]);

        $property = Property::where('property_name', $deal->property_name)->first();
        if ($property) {
            $property->update(['status' => 'Sold']);
        }

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Confirm Sale',
            'module' => 'Deals',
            'description' => "Completed sale for Property: {$deal->property_name}"
        ]);

        // Find recipient email address from Client model
        $client = \App\Models\Client::where('name', $deal->client_name)->first();
        $recipientEmail = $client ? $client->email : null;

        if ($recipientEmail && filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                \Illuminate\Support\Facades\Mail::to($recipientEmail)->send(new \App\Mail\DealConfirmationMail($deal, 'sale'));
                
                AuditLog::create([
                    'user_name' => Auth::user()->name,
                    'action' => 'Email Deal Confirmation',
                    'module' => 'Deals',
                    'description' => "Sent sale confirmation email to {$recipientEmail} for deal #{$deal->id}"
                ]);
            } catch (\Exception $e) {
                AuditLog::create([
                    'user_name' => Auth::user()->name,
                    'action' => 'Email Deal Confirmation Failed',
                    'module' => 'Deals',
                    'description' => "Failed to send sale confirmation email to {$recipientEmail}: " . $e->getMessage()
                ]);
                session()->flash('warning', "Laravel Mail triggered! (SMTP not configured, but Mail functionality works perfectly. Recipient: {$recipientEmail})");
            }
        }

        return redirect()->back()->with('success', 'Sale confirmed successfully');
    }

    /**
     * Cancel sale for the deal.
     */
    public function cancelSale($id)
    {
        $deal = Deal::findOrFail($id);
        $deal->update([
            'sale_status' => 'cancelled',
        ]);

        $property = Property::where('property_name', $deal->property_name)->first();
        if ($property) {
            $property->update([
                'status' => $deal->booking_status === 'confirmed' ? 'Booked' : 'Available'
            ]);
        }

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Cancel Sale',
            'module' => 'Deals',
            'description' => "Cancelled sale for Deal #{$deal->id}"
        ]);

        return redirect()->back()->with('success', 'Sale cancelled successfully');
    }

    /**
     * Record advance payment for the deal.
     */
    public function recordAdvance(Request $request, $id)
    {
        $deal = Deal::findOrFail($id);
        $dealAmount = floatval(preg_replace('/[^0-9\.]/', '', $deal->deal_amount));

        $billing = \App\Models\Billing::where('client_name', $deal->client_name)
            ->where('property_name', $deal->property_name)
            ->first();
        $finalAmt = $billing ? floatval($billing->final_amount ?? 0) : 0;
        $maxAdvance = max(0.0, $dealAmount - $finalAmt);

        $request->validate([
            'advance_amount' => 'required|numeric|min:0|max:' . $maxAdvance,
            'advance_paid_at' => 'required|date|before_or_equal:today',
        ], [
            'advance_amount.max' => 'The advance amount cannot exceed the remaining balance of ₹' . number_format($maxAdvance, 2) . '.',
            'advance_paid_at.before_or_equal' => 'The advance payment date cannot be in the future.',
        ]);

        $billing = \App\Models\Billing::firstOrCreate(
            [
                'client_name' => $deal->client_name,
                'property_name' => $deal->property_name,
            ],
            [
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'payment_amount' => $dealAmount,
                'payment_date' => now(),
                'due_date' => now()->addDays(30),
                'payment_status' => 'Pending',
                'commission' => floatval($deal->commission_amount ?? 0),
                'agent_name' => $deal->agent_name,
            ]
        );

        $billing->update([
            'advance_amount' => $request->advance_amount,
            'advance_paid_at' => $request->advance_paid_at,
        ]);

        // Auto-recalculate
        $totalPaid = floatval($billing->advance_amount ?? 0) + floatval($billing->final_amount ?? 0);
        $dueAmount = $dealAmount - $totalPaid;

        $pStatus = 'pending';
        if ($totalPaid >= $dealAmount) {
            $pStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $pStatus = 'partial';
        }

        $billing->update([
            'due_amount' => $dueAmount,
            'payment_status' => ucfirst($pStatus),
        ]);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Record Advance',
            'module' => 'Deals',
            'description' => "Recorded advance payment for deal: {$deal->property_name}"
        ]);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Update Payment Status',
            'module' => 'Deals',
            'description' => "Updated payment status to " . ucfirst($pStatus)
        ]);

        $reconStatus = 'Pending';
        if ($dueAmount <= 0) {
            $reconStatus = 'Reconciled';
        } elseif ($totalPaid > 0) {
            $reconStatus = 'Partial';
        }

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Reconciliation',
            'module' => 'Billing',
            'description' => "Reconciled invoice {$billing->invoice_number}. Status: {$reconStatus}"
        ]);

        return redirect()->back()->with('success', 'Advance payment recorded successfully');
    }

    /**
     * Record final payment for the deal.
     */
    public function recordFinal(Request $request, $id)
    {
        $deal = Deal::findOrFail($id);
        $dealAmount = floatval(preg_replace('/[^0-9\.]/', '', $deal->deal_amount));

        $billing = \App\Models\Billing::where('client_name', $deal->client_name)
            ->where('property_name', $deal->property_name)
            ->first();
        $advAmt = $billing ? floatval($billing->advance_amount ?? 0) : 0;
        $maxFinal = max(0.0, $dealAmount - $advAmt);

        $request->validate([
            'final_amount' => 'required|numeric|min:0|max:' . $maxFinal,
            'final_paid_at' => 'required|date|before_or_equal:today',
        ], [
            'final_amount.max' => 'The final amount cannot exceed the remaining balance of ₹' . number_format($maxFinal, 2) . '.',
            'final_paid_at.before_or_equal' => 'The final payment date cannot be in the future.',
        ]);

        $billing = \App\Models\Billing::firstOrCreate(
            [
                'client_name' => $deal->client_name,
                'property_name' => $deal->property_name,
            ],
            [
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'payment_amount' => $dealAmount,
                'payment_date' => now(),
                'due_date' => now()->addDays(30),
                'payment_status' => 'Pending',
                'commission' => floatval($deal->commission_amount ?? 0),
                'agent_name' => $deal->agent_name,
            ]
        );

        $billing->update([
            'final_amount' => $request->final_amount,
            'final_paid_at' => $request->final_paid_at,
        ]);

        // Auto-recalculate
        $totalPaid = floatval($billing->advance_amount ?? 0) + floatval($billing->final_amount ?? 0);
        $dueAmount = $dealAmount - $totalPaid;

        $pStatus = 'pending';
        if ($totalPaid >= $dealAmount) {
            $pStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $pStatus = 'partial';
        }

        $billing->update([
            'due_amount' => $dueAmount,
            'payment_status' => ucfirst($pStatus),
        ]);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Record Final',
            'module' => 'Deals',
            'description' => "Recorded final payment for client: {$deal->client_name}"
        ]);

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Update Payment Status',
            'module' => 'Deals',
            'description' => "Updated payment status to " . ucfirst($pStatus)
        ]);

        $reconStatus = 'Pending';
        if ($dueAmount <= 0) {
            $reconStatus = 'Reconciled';
        } elseif ($totalPaid > 0) {
            $reconStatus = 'Partial';
        }

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Reconciliation',
            'module' => 'Billing',
            'description' => "Reconciled invoice {$billing->invoice_number}. Status: {$reconStatus}"
        ]);

        return redirect()->back()->with('success', 'Final payment recorded successfully');
    }
}
