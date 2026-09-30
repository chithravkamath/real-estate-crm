<?php
 
namespace App\Http\Controllers;
 
use App\Models\Billing;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Client;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
 
class ReportsController extends Controller
 {
     public function index(Request $request)
     {
         $request->validate([
             'from_date' => 'nullable|date|before_or_equal:today',
             'to_date' => 'nullable|date|before_or_equal:today|after_or_equal:from_date',
         ], [
             'from_date.before_or_equal' => 'The start date cannot be in the future.',
             'to_date.before_or_equal' => 'The end date cannot be in the future.',
             'to_date.after_or_equal' => 'The end date must be after or equal to the start date.',
         ]);

         $fromDate = $request->input('from_date');
         $toDate = $request->input('to_date');
         $reportType = $request->input('report_type', 'All Reports');
 
         $data = $this->getReportData($fromDate, $toDate, $reportType);
 
         return view('reports', $data);
     }
 
     public function exportPdf(Request $request)
     {
         $request->validate([
             'from_date' => 'nullable|date|before_or_equal:today',
             'to_date' => 'nullable|date|before_or_equal:today|after_or_equal:from_date',
         ], [
             'from_date.before_or_equal' => 'The start date cannot be in the future.',
             'to_date.before_or_equal' => 'The end date cannot be in the future.',
             'to_date.after_or_equal' => 'The end date must be after or equal to the start date.',
         ]);

         $fromDate = $request->input('from_date');
         $toDate = $request->input('to_date');
         $reportType = $request->input('report_type', 'All Reports');
 
         $data = $this->getReportData($fromDate, $toDate, $reportType);
 
         $pdf = Pdf::loadView('reports-pdf', $data);
         
         $filename = strtolower(str_replace(' ', '-', $reportType)) . '.pdf';
         
         return $pdf->download($filename);
     }
 
     private function getReportData($fromDate, $toDate, $reportType)
     {
         // 1. Fetch Deals with dynamic date filter
         $deals = Deal::query()
             ->when($fromDate, function ($query) use ($fromDate) {
                 return $query->whereDate('booking_date', '>=', $fromDate);
             })
             ->when($toDate, function ($query) use ($toDate) {
                 return $query->whereDate('booking_date', '<=', $toDate);
             })
             ->get();
 
         // 2. Fetch Billings with dynamic date filter
         $billings = Billing::query()
             ->when($fromDate, function ($query) use ($fromDate) {
                 return $query->whereDate('payment_date', '>=', $fromDate);
             })
             ->when($toDate, function ($query) use ($toDate) {
                 return $query->whereDate('payment_date', '<=', $toDate);
             })
             ->get();
 
         // 3. Fetch Properties with dynamic date filter
         $properties = Property::query()
             ->when($fromDate, function ($query) use ($fromDate) {
                 return $query->whereDate('created_at', '>=', $fromDate);
             })
             ->when($toDate, function ($query) use ($toDate) {
                 return $query->whereDate('created_at', '<=', $toDate);
             })
             ->get();
 
         // 4. Fetch Leads with dynamic date filter
         $leads = Lead::query()
             ->when($fromDate, function ($query) use ($fromDate) {
                 return $query->whereDate('created_at', '>=', $fromDate);
             })
             ->when($toDate, function ($query) use ($toDate) {
                 return $query->whereDate('created_at', '<=', $toDate);
             })
             ->get();
 
         // 5. Fetch Clients with dynamic date filter
         $clients = Client::query()
             ->when($fromDate, function ($query) use ($fromDate) {
                 return $query->whereDate('created_at', '>=', $fromDate);
             })
             ->when($toDate, function ($query) use ($toDate) {
                 return $query->whereDate('created_at', '<=', $toDate);
             })
             ->get();
 
         // 6. Fetch Expenses with dynamic date filter
         $expensesList = Expense::query()
             ->when($fromDate, function ($query) use ($fromDate) {
                 return $query->whereDate('expense_date', '>=', $fromDate);
             })
             ->when($toDate, function ($query) use ($toDate) {
                 return $query->whereDate('expense_date', '<=', $toDate);
             })
             ->get();
 
         // --- CALCULATIONS AND SUMMARIES ---
 
         $completedDeals = $deals->where('status', 'Accepted');
         $totalSales = $completedDeals->sum(function ($deal) {
             return floatval(preg_replace('/[^0-9\.]/', '', $deal->deal_amount));
         });
 
         $dealsClosed = $completedDeals->count();
         $averagePrice = $dealsClosed ? $totalSales / $dealsClosed : 0;
 
         $monthlySales = $completedDeals->groupBy(function ($deal) {
             return optional($deal->booking_date)->format('F Y') ?: 'Unknown';
         })->map(function ($group) {
             return $group->sum(function ($deal) {
                 return floatval(preg_replace('/[^0-9\.]/', '', $deal->deal_amount));
             });
         })->sortDesc();
 
         $commissionEarned = $deals->sum(function ($deal) {
             return floatval(preg_replace('/[^0-9\.]/', '', $deal->commission_amount ?: ($deal->deal_amount * 0.05)));
         });
         
         $paidCommission = $deals->where('payment_status', 'Paid')->sum(function ($deal) {
             return floatval(preg_replace('/[^0-9\.]/', '', $deal->commission_amount ?: ($deal->deal_amount * 0.05)));
         });
 
         $propertyAnalytics = $properties->groupBy('property_type')->map(function ($group) {
             $total = $group->count();
             return [
                 'total' => $total,
                 'sold' => $group->where('status', 'Sold')->count(),
                 'available' => $group->where('status', 'Available')->count(),
                 'booked' => $group->where('status', 'Booked')->count(),
                 'avg_price' => $group->avg(function ($property) {
                     return floatval($property->price ?? 0);
                 }),
             ];
         });
 
         $leadConversion = [
             'total_leads' => $leads->count(),
             'contacted' => $leads->where('status', 'Contacted')->count(),
             'qualified' => $leads->where('status', 'Qualified')->count(),
             'closed' => $leads->where('status', 'Closed')->count(),
         ];
 
         $revenueByClient = $deals->groupBy('client_name')->map(function ($group) {
             return $group->sum(function ($deal) {
                 return floatval(preg_replace('/[^0-9\.]/', '', $deal->deal_amount));
             });
         })->sortDesc();
 
         // Profitability computations
         $totalRevenue = $billings->reduce(function ($carry, $billing) {
             return $carry + floatval(preg_replace('/[^0-9\.]/', '', $billing->payment_amount));
         }, 0);
 
         $totalExpenses = $expensesList->sum('amount');
         $profit = $totalRevenue - $totalExpenses;
 
         $monthlyRevenue = $billings->groupBy(function ($billing) {
             return optional($billing->payment_date)->format('F Y') ?: 'Unknown';
         })->map(function ($group) {
             return $group->sum(function ($billing) {
                 return floatval(preg_replace('/[^0-9\.]/', '', $billing->payment_amount));
             });
         });
 
         $monthlyExpenses = $expensesList->groupBy(function ($exp) {
             return optional($exp->expense_date)->format('F Y') ?: 'Unknown';
         })->map(function ($group) {
             return $group->sum('amount');
         });
 
         $monthlyProfits = collect();
         $allMonths = $monthlyRevenue->keys()->merge($monthlyExpenses->keys())->unique();
 
         foreach ($allMonths as $month) {
             $rev = $monthlyRevenue->get($month, 0);
             $exp = $monthlyExpenses->get($month, 0);
             $monthlyProfits->put($month, [
                 'revenue' => $rev,
                 'expenses' => $exp,
                 'profit' => $rev - $exp,
             ]);
         }
         $monthlyProfits = $monthlyProfits->sortKeysDesc();
 
         return [
             'reportType' => $reportType,
             'fromDate' => $fromDate,
             'toDate' => $toDate,
             'totalSales' => $totalSales,
             'dealsClosed' => $dealsClosed,
             'averagePrice' => $averagePrice,
             'monthlySales' => $monthlySales,
             'commissionEarned' => $commissionEarned,
             'paidCommission' => $paidCommission,
             'propertyAnalytics' => $propertyAnalytics,
             'leadConversion' => $leadConversion,
             'revenueByClient' => $revenueByClient,
             'totalRevenue' => $totalRevenue,
             'totalExpenses' => $totalExpenses,
             'profit' => $profit,
             'monthlyProfits' => $monthlyProfits,
             'deals' => $deals,
             'billings' => $billings,
             'properties' => $properties,
             'leads' => $leads,
             'clients' => $clients,
             'expensesList' => $expensesList,
         ];
     }
 }
