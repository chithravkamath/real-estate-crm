<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Property;
use App\Models\SiteVisit;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'accountant') {
            $totalRevenue = Billing::all()->reduce(function ($carry, $billing) {
                return $carry + floatval(preg_replace('/[^0-9\.]/', '', $billing->payment_amount));
            }, 0);

            $totalExpenses = \App\Models\Expense::sum('amount');
            $netProfit = $totalRevenue - $totalExpenses;

            $outstandingBalance = Billing::all()->sum(function ($b) {
                return floatval($b->due_amount ?? ($b->payment_status === 'Paid' ? 0 : $b->payment_amount));
            });

            $totalPendingPayments = Billing::all()->filter(function ($b) {
                $totalPaid = floatval($b->advance_amount ?? 0) + floatval($b->final_amount ?? 0);
                $due = floatval($b->due_amount ?? $b->payment_amount);
                return $due > 0 && $totalPaid == 0;
            })->count();

            $totalReconciledPayments = Billing::all()->filter(function ($b) {
                $due = floatval($b->due_amount ?? $b->payment_amount);
                return $due <= 0;
            })->count();

            $recentBillings = Billing::latest()->take(5)->get();
            $recentExpenses = \App\Models\Expense::latest()->take(5)->get();

            $monthlyExpenses = \App\Models\Expense::selectRaw('DATE_FORMAT(expense_date, "%M %Y") as month, SUM(amount) as total')
                ->groupBy('month')
                ->orderByRaw('MIN(expense_date) desc')
                ->get();

            $monthlyRevenue = Billing::selectRaw('DATE_FORMAT(payment_date, "%M %Y") as month, SUM(payment_amount) as total')
                ->groupBy('month')
                ->orderByRaw('MIN(payment_date) desc')
                ->get();

            return view('accountant-dashboard', compact(
                'totalRevenue',
                'totalExpenses',
                'netProfit',
                'outstandingBalance',
                'totalPendingPayments',
                'totalReconciledPayments',
                'recentBillings',
                'recentExpenses',
                'monthlyExpenses',
                'monthlyRevenue'
            ));
        }

        if (auth()->user()->role === 'agent') {
            $assignedLeads = Lead::where('assigned_agent_id', auth()->id())->count();
            
            $clientNames = Deal::where('agent_id', auth()->id())->pluck('client_name')->unique();
            $activeClientsCount = Client::whereIn('name', $clientNames)->count();

            $siteVisits = SiteVisit::where('agent_id', auth()->id())->count();
            $ongoingDeals = Deal::where('agent_id', auth()->id())->where('status', '!=', 'Accepted')->count();
            
            $leadIds = Lead::where('assigned_agent_id', auth()->id())->pluck('id');
            $dealIds = Deal::where('agent_id', auth()->id())->pluck('id');
            
            $pendingReminders = \App\Models\Reminder::where('status', 'Pending')
                ->where(function ($q) use ($leadIds, $dealIds) {
                    $q->where(function ($sq) use ($leadIds) {
                        $sq->where('related_type', 'Lead')->whereIn('related_id', $leadIds);
                    })->orWhere(function ($sq) use ($dealIds) {
                        $sq->where('related_type', 'Deal')->whereIn('related_id', $dealIds);
                    })->orWhereNotIn('related_type', ['Lead', 'Deal']);
                })
                ->count();
                
            $totalCommunications = \App\Models\Communication::where('created_by', auth()->user()->name)->count();

            $todaysFollowups = Lead::where('assigned_agent_id', auth()->id())
                ->whereDate('follow_up_date', today())
                ->get();

            $upcomingVisits = SiteVisit::where('agent_id', auth()->id())
                ->whereDate('visit_date', '>=', today())
                ->orderBy('visit_date', 'asc')
                ->orderBy('visit_time', 'asc')
                ->get();

            $todaysReminders = \App\Models\Reminder::whereDate('reminder_date', today())
                ->where('status', 'Pending')
                ->where(function ($q) use ($leadIds, $dealIds) {
                    $q->where(function ($sq) use ($leadIds) {
                        $sq->where('related_type', 'Lead')->whereIn('related_id', $leadIds);
                    })->orWhere(function ($sq) use ($dealIds) {
                        $sq->where('related_type', 'Deal')->whereIn('related_id', $dealIds);
                    })->orWhereNotIn('related_type', ['Lead', 'Deal']);
                })
                ->orderBy('reminder_time', 'asc')
                ->get();

            $upcomingReminders = \App\Models\Reminder::whereDate('reminder_date', '>', today())
                ->where('status', 'Pending')
                ->where(function ($q) use ($leadIds, $dealIds) {
                    $q->where(function ($sq) use ($leadIds) {
                        $sq->where('related_type', 'Lead')->whereIn('related_id', $leadIds);
                    })->orWhere(function ($sq) use ($dealIds) {
                        $sq->where('related_type', 'Deal')->whereIn('related_id', $dealIds);
                    })->orWhereNotIn('related_type', ['Lead', 'Deal']);
                })
                ->orderBy('reminder_date', 'asc')
                ->orderBy('reminder_time', 'asc')
                ->take(5)
                ->get();

            $recentCommunications = \App\Models\Communication::with(['lead', 'client'])
                ->where('created_by', auth()->user()->name)
                ->latest()
                ->take(5)
                ->get();

            return view('agent-dashboard', compact(
                'assignedLeads',
                'activeClientsCount',
                'siteVisits',
                'ongoingDeals',
                'pendingReminders',
                'totalCommunications',
                'todaysFollowups',
                'upcomingVisits',
                'todaysReminders',
                'upcomingReminders',
                'recentCommunications'
            ));
        }

        $totalProperties = Property::count();
        $totalClients = Client::count();
        $totalLeads = Lead::count();
        $totalDeals = Deal::count();

        $totalRevenue = Billing::all()->reduce(function ($carry, $billing) {
            return $carry + floatval(preg_replace('/[^0-9\.]/', '', $billing->payment_amount));
        }, 0);

        $totalCommission = $totalRevenue * 0.05;

        $recentProperties = Property::latest('created_at')->take(3)->get();

        $recentActivities = Collection::make()
            ->concat(Lead::latest('created_at')->take(2)->get()->map(function ($lead) {
                return ['message' => "New Lead: {$lead->name} ({$lead->interested_property})"];
            }))
            ->concat(Deal::latest('booking_date')->take(1)->get()->map(function ($deal) {
                return ['message' => "Deal Updated: {$deal->property_name} for {$deal->client_name}"];
            }))
            ->concat(SiteVisit::latest('visit_date')->take(1)->get()->map(function ($visit) {
                return ['message' => "Site Visit Scheduled: {$visit->property_name}"];
            }))
            ->take(4);

        $todaysReminders = \App\Models\Reminder::whereDate('reminder_date', today())
            ->where('status', 'Pending')
            ->orderBy('reminder_time', 'asc')
            ->get();

        $upcomingReminders = \App\Models\Reminder::whereDate('reminder_date', '>', today())
            ->where('status', 'Pending')
            ->orderBy('reminder_date', 'asc')
            ->orderBy('reminder_time', 'asc')
            ->take(5)
            ->get();

        $recentCommunications = \App\Models\Communication::with(['lead', 'client'])
            ->latest()
            ->take(5)
            ->get();

        // Expense Tracking analytics
        $totalExpenses = \App\Models\Expense::sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;
        $recentExpenses = \App\Models\Expense::latest()->take(5)->get();
        $monthlyExpenses = \App\Models\Expense::selectRaw('DATE_FORMAT(expense_date, "%M %Y") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderByRaw('MIN(expense_date) desc')
            ->get();

        // Payment Reconciliation analytics
        $outstandingBalance = Billing::all()->sum(function ($b) {
            return floatval($b->due_amount ?? ($b->payment_status === 'Paid' ? 0 : $b->payment_amount));
        });
        $totalPendingPayments = Billing::all()->filter(function ($b) {
            $totalPaid = floatval($b->advance_amount ?? 0) + floatval($b->final_amount ?? 0);
            $due = floatval($b->due_amount ?? $b->payment_amount);
            return $due > 0 && $totalPaid == 0;
        })->count();
        $totalReconciledPayments = Billing::all()->filter(function ($b) {
            $due = floatval($b->due_amount ?? $b->payment_amount);
            return $due <= 0;
        })->count();

        return view('dashboard', compact(
            'totalProperties',
            'totalClients',
            'totalLeads',
            'totalDeals',
            'totalRevenue',
            'totalCommission',
            'recentProperties',
            'recentActivities',
            'todaysReminders',
            'upcomingReminders',
            'recentCommunications',
            'totalExpenses',
            'netProfit',
            'recentExpenses',
            'monthlyExpenses',
            'outstandingBalance',
            'totalPendingPayments',
            'totalReconciledPayments'
        ));
    }
}
