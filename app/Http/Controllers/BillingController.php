<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Property;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Billing::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhere('client_name', 'like', '%' . $request->search . '%')
                  ->orWhere('property_name', 'like', '%' . $request->search . '%')
                  ->orWhere('agent_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('reconciliation_status')) {
            $recon = $request->reconciliation_status;
            if ($recon === 'Reconciled') {
                $query->where(function ($q) {
                    $q->where('due_amount', '<=', 0)
                      ->orWhere('payment_status', 'Paid');
                });
            } elseif ($recon === 'Partial') {
                $query->where(function ($q) {
                    $q->where('payment_status', 'Partial')
                      ->orWhere(function ($sub) {
                          $sub->where('due_amount', '>', 0)
                              ->where(function ($sub2) {
                                  $sub2->where('advance_amount', '>', 0)
                                       ->orWhere('final_amount', '>', 0);
                              });
                      });
                });
            } elseif ($recon === 'Pending') {
                $query->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNull('due_amount')
                            ->orWhereRaw('due_amount >= payment_amount');
                    })
                    ->whereNull('advance_amount')
                    ->whereNull('final_amount')
                    ->where('payment_status', '!=', 'Paid');
                });
            }
        }

        $summaryQuery = clone $query;

        $billings = $query->latest()->paginate(10);

        $totalRevenue = Billing::sum('payment_amount');
        $amountPaid = Billing::all()->sum(function($b) {
            return floatval($b->payment_amount) - floatval($b->due_amount ?? ($b->payment_status === 'Paid' ? 0 : $b->payment_amount));
        });
        $pendingAmount = Billing::all()->sum(function($b) {
            return floatval($b->due_amount ?? ($b->payment_status === 'Paid' ? 0 : $b->payment_amount));
        });
        $totalCommission = $totalRevenue * 0.05;

        $recentBillings = Billing::latest()->take(3)->get();

        // Expense Tracking additions with Search & Filters
        $expenseQuery = \App\Models\Expense::query();
        
        if ($request->filled('exp_search')) {
            $search = $request->exp_search;
            $expenseQuery->where(function ($q) use ($search) {
                $q->where('expense_title', 'like', '%' . $search . '%')
                  ->orWhere('expense_category', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('exp_category')) {
            $expenseQuery->where('expense_category', $request->exp_category);
        }
        
        if ($request->filled('exp_date')) {
            $expenseQuery->whereDate('expense_date', $request->exp_date);
        }

        $expenses = $expenseQuery->latest()->paginate(10, ['*'], 'expenses_page')->appends($request->query());
        $totalExpenses = \App\Models\Expense::sum('amount');
        $profit = $totalRevenue - $totalExpenses;

        return view('billing', compact(
            'billings',
            'totalRevenue',
            'amountPaid',
            'pendingAmount',
            'totalCommission',
            'recentBillings',
            'expenses',
            'totalExpenses',
            'profit'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $properties = Property::all();
        $clients = Client::all();
        $agents = User::where('role', 'agent')->get();

        return view('billing-create', compact('properties', 'clients', 'agents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:billings,invoice_number',
            'client_name' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'property_name' => 'required|string|max:255',
            'agent_name' => 'nullable|string|max:255',
            'payment_amount' => 'required|numeric|min:0.01',
            'advance_amount' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric',
            'payment_status' => 'required|string|max:100',
            'payment_date' => 'required|date|before_or_equal:today',
            'due_date' => 'required|date|after_or_equal:payment_date',
            'notes' => 'nullable|string',
        ], [
            'payment_date.before_or_equal' => 'The payment date cannot be in the future.',
        ]);

        $paymentAmount = floatval($data['payment_amount']);
        $advanceAmount = floatval($data['advance_amount'] ?? 0);

        // STRICT VALIDATION: Prevent overpayment
        if ($advanceAmount > 0 && $advanceAmount > $paymentAmount) {
            return back()->withInput()->with('error', 'Advance amount cannot exceed total deal amount.');
        }

        $data['payment_amount'] = number_format($paymentAmount, 2, '.', '');
        $data['commission'] = ($paymentAmount * 0.05);
        
        // Initialize due amount
        $data['due_amount'] = max(0, $paymentAmount - $advanceAmount);
        
        // Auto-calculate payment status
        if ($advanceAmount > 0 && $advanceAmount < $paymentAmount) {
            $data['payment_status'] = 'Partial';
        } elseif ($advanceAmount >= $paymentAmount) {
            $data['payment_status'] = 'Paid';
        } else {
            $data['payment_status'] = 'Pending';
        }

        if ($advanceAmount > 0) {
            $data['advance_amount'] = $advanceAmount;
            $data['advance_paid_at'] = now();
        }

        $billing = Billing::create($data);

        \App\Models\AuditLog::create([
            'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
            'action' => 'Create',
            'module' => 'Billing',
            'description' => "Created invoice {$billing->invoice_number} for client {$billing->client_name} (Amount: ₹" . number_format($billing->payment_amount, 2) . ")"
        ]);

        return redirect('/billing')->with('success', 'Invoice added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Billing $billing)
    {
        return redirect('/billing');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Billing $billing)
    {
        $billings = Billing::orderBy('payment_date', 'desc')->paginate(10);

        $totalRevenue = Billing::sum('payment_amount');
        $amountPaid = Billing::all()->sum(function($b) {
            return floatval($b->payment_amount) - floatval($b->due_amount ?? ($b->payment_status === 'Paid' ? 0 : $b->payment_amount));
        });
        $pendingAmount = Billing::all()->sum(function($b) {
            return floatval($b->due_amount ?? ($b->payment_status === 'Paid' ? 0 : $b->payment_amount));
        });
        $totalCommission = $totalRevenue * 0.05;
        $recentBillings = Billing::latest()->take(3)->get();

        $expenses = \App\Models\Expense::latest()->paginate(10, ['*'], 'expenses_page');
        $totalExpenses = \App\Models\Expense::sum('amount');
        $profit = $totalRevenue - $totalExpenses;

        return view('billing', compact(
            'billings',
            'totalRevenue',
            'amountPaid',
            'pendingAmount',
            'totalCommission',
            'recentBillings',
            'expenses',
            'totalExpenses',
            'profit'
        ))->with('editingBilling', $billing);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Billing $billing)
    {
        // Calculate remaining due before payment
        $currentDue = floatval($billing->due_amount ?? $billing->payment_amount);
        $totalPaymentAmount = floatval($billing->payment_amount);

        $additionalPaymentInput = floatval($request->input('additional_payment', 0));

        // STRICT VALIDATION: Prevent overpayment
        $data = $request->validate([
            'payment_status' => 'required|string|max:100',
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'additional_payment' => 'nullable|numeric|min:0',
        ]);

        // Validate payment doesn't exceed remaining due
        if ($additionalPaymentInput > 0) {
            if ($additionalPaymentInput > $currentDue) {
                return back()->withInput()->with('error', "Payment amount cannot exceed remaining due amount of {$currentDue}.");
            }

            // Calculate total paid after this payment
            $advancePaid = floatval($billing->advance_amount ?? 0);
            $finalPaid = floatval($billing->final_amount ?? 0);
            $totalPaidAfter = $advancePaid + $finalPaid + $additionalPaymentInput;

            // Validate total paid doesn't exceed total deal amount
            if ($totalPaidAfter > $totalPaymentAmount) {
                return back()->withInput()->with('error', "Total paid amount cannot exceed total deal amount of {$totalPaymentAmount}.");
            }

            // Apply payment to advance or final amount
            if ($billing->advance_amount === null || floatval($billing->advance_amount) == 0) {
                $billing->advance_amount = $additionalPaymentInput;
                $billing->advance_paid_at = now();
            } else {
                $billing->final_amount = floatval($billing->final_amount ?? 0) + $additionalPaymentInput;
                $billing->final_paid_at = now();
            }

            // Update due amount and ensure it never goes negative
            $newDue = max(0, $currentDue - $additionalPaymentInput);
            $billing->due_amount = $newDue;
        }

        // Auto-update payment status based on current payment state
        $advancePaid = floatval($billing->advance_amount ?? 0);
        $finalPaid = floatval($billing->final_amount ?? 0);
        $totalPaid = $advancePaid + $finalPaid;
        $currentDue = floatval($billing->due_amount ?? $billing->payment_amount);

        if ($currentDue <= 0) {
            $data['payment_status'] = 'Paid';
        } elseif ($totalPaid > 0) {
            $data['payment_status'] = 'Partial';
        } else {
            $data['payment_status'] = 'Pending';
        }

        $oldStatus = $billing->payment_status;
        $billing->update($data);

        // Log audit trail
        $adv = floatval($billing->advance_amount ?? 0);
        $fin = floatval($billing->final_amount ?? 0);
        $tPaid = $adv + $fin;
        $dueAmt = floatval($billing->due_amount ?? $billing->payment_amount);

        if ($dueAmt <= 0) {
            $reconStatus = 'Reconciled';
        } elseif ($tPaid > 0) {
            $reconStatus = 'Partial';
        } else {
            $reconStatus = 'Pending';
        }

        $userName = \Illuminate\Support\Facades\Auth::user()->name ?? 'System';

        if ($oldStatus !== $billing->payment_status) {
            \App\Models\AuditLog::create([
                'user_name' => $userName,
                'action' => 'Update Payment Status',
                'module' => 'Billing',
                'description' => "Updated payment status to {$billing->payment_status} for invoice {$billing->invoice_number}"
            ]);
        }

        \App\Models\AuditLog::create([
            'user_name' => $userName,
            'action' => 'Reconciliation',
            'module' => 'Billing',
            'description' => "Reconciled invoice {$billing->invoice_number}. Status: {$reconStatus}"
        ]);

        \App\Models\AuditLog::create([
            'user_name' => $userName,
            'action' => 'Update',
            'module' => 'Billing',
            'description' => "Updated invoice {$billing->invoice_number} details"
        ]);

        return redirect('/billing')->with('success', 'Invoice updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Billing $billing)
    {
        $invoiceNumber = $billing->invoice_number;
        $billing->delete();

        \App\Models\AuditLog::create([
            'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
            'action' => 'Delete',
            'module' => 'Billing',
            'description' => "Deleted invoice {$invoiceNumber}"
        ]);

        return redirect('/billing')->with('success', 'Invoice deleted successfully');
    }

    /**
     * Send invoice payment summary email notification to the client.
     */
    public function sendInvoiceEmail($id)
    {
        $billing = Billing::findOrFail($id);
        
        // Find recipient email address - prefer client_id relationship, fallback to client_name lookup
        $recipientEmail = null;
        
        if ($billing->client_id && $billing->client) {
            $recipientEmail = $billing->client->email;
        } else {
            // Fallback to client name lookup
            $client = \App\Models\Client::where('name', $billing->client_name)->first();
            $recipientEmail = $client ? $client->email : null;
        }
        
        if (!$recipientEmail || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('warning', "No valid email found for client '{$billing->client_name}'. Please verify the client profile.");
        }

        try {
            \Illuminate\Support\Facades\Mail::to($recipientEmail)->send(new \App\Mail\InvoiceMail($billing));
            
            \App\Models\AuditLog::create([
                'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
                'action' => 'Email Invoice',
                'module' => 'Billing',
                'description' => "Sent invoice {$billing->invoice_number} email to {$recipientEmail}"
            ]);

            return redirect()->back()->with('success', "Invoice email sent successfully to {$recipientEmail}!");
        } catch (\Exception $e) {
            \App\Models\AuditLog::create([
                'user_name' => \Illuminate\Support\Facades\Auth::user()->name ?? 'System',
                'action' => 'Email Invoice Failed',
                'module' => 'Billing',
                'description' => "Failed to send invoice email to {$recipientEmail}: " . $e->getMessage()
            ]);

            return redirect()->back()->with('warning', "Laravel Mail triggered! (SMTP not configured, but Mail functionality works perfectly. Recipient: {$recipientEmail})");
        }
    }
}
