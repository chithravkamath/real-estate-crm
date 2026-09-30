<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $reportType }}</title>
<style>
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #333;
        font-size: 12px;
        line-height: 1.5;
        margin: 0;
        padding: 0;
    }
    .header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #337ab7;
        padding-bottom: 10px;
    }
    .header h2 {
        margin: 0;
        color: #337ab7;
        font-size: 24px;
    }
    .header p {
        margin: 5px 0 0 0;
        color: #666;
        font-size: 14px;
    }
    .meta-table {
        width: 100%;
        margin-bottom: 25px;
        border-collapse: collapse;
    }
    .meta-table td {
        padding: 4px 8px;
        border: 0;
    }
    .section-title {
        font-size: 16px;
        font-weight: bold;
        color: #337ab7;
        margin-top: 25px;
        margin-bottom: 10px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 4px;
    }
    .card-grid {
        width: 100%;
        margin-bottom: 20px;
    }
    .card-grid td {
        width: 33%;
        padding: 10px;
        vertical-align: top;
    }
    .card {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 12px;
        background-color: #f9f9f9;
        text-align: center;
    }
    .card h4 {
        margin: 0 0 8px 0;
        font-size: 12px;
        color: #555;
        text-transform: uppercase;
    }
    .card h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }
    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 25px;
    }
    .report-table th {
        background-color: #f5f5f5;
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        font-weight: bold;
        color: #555;
    }
    .report-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .report-table tr:nth-child(even) {
        background-color: #fafafa;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
    .text-success {
        color: #5cb85c;
    }
    .text-danger {
        color: #d9534f;
    }
</style>
</head>
<body>

<div class="header">
    <h2>{{ $reportType }}</h2>
    <p>Real Estate CRM Reports & Analytics</p>
</div>

<table class="meta-table">
    <tr>
        <td style="width: 15%; font-weight: bold;">Date Generated:</td>
        <td style="width: 35%;">{{ date('Y-m-d H:i:s') }}</td>
        <td style="width: 15%; font-weight: bold;">Date Range:</td>
        <td style="width: 35%;">
            @if($fromDate || $toDate)
                {{ $fromDate ?? 'Beginning' }} to {{ $toDate ?? 'Present' }}
            @else
                All Time
            @endif
        </td>
    </tr>
</table>

<!-- ========================================================= -->
<!-- 1. SALES REPORT SECTION -->
<!-- ========================================================= -->
@if($reportType === 'All Reports' || $reportType === 'Sales Report')
    <div class="section-title">Sales Performance</div>
    
    <table class="card-grid">
        <tr>
            <td>
                <div class="card">
                    <h4>Total Property Value Sold</h4>
                    <h3>₹{{ number_format($totalSales, 0) }}</h3>
                </div>
            </td>
            <td>
                <div class="card">
                    <h4>Deals Closed</h4>
                    <h3>{{ $dealsClosed }}</h3>
                </div>
            </td>
            <td>
                <div class="card">
                    <h4>Average Price</h4>
                    <h3>₹{{ number_format($averagePrice, 0) }}</h3>
                </div>
            </td>
        </tr>
    </table>

    <div style="font-weight: bold; margin-bottom: 5px;">Deals Close History</div>
    <table class="report-table">
        <thead>
            <tr>
                <th>Property</th>
                <th>Client</th>
                <th>Agent</th>
                <th>Date</th>
                <th>Deal Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deals as $dealItem)
                <tr>
                    <td>{{ $dealItem->property_name }}</td>
                    <td>{{ $dealItem->client_name }}</td>
                    <td>{{ $dealItem->agent_name ?? 'N/A' }}</td>
                    <td>{{ optional($dealItem->booking_date)->format('Y-m-d') ?? 'N/A' }}</td>
                    <td class="text-right">₹{{ number_format(floatval($dealItem->deal_amount), 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No deals found for this range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endif

<!-- ========================================================= -->
<!-- 2. REVENUE / COMMISSION REPORT SECTION -->
<!-- ========================================================= -->
@if($reportType === 'All Reports' || $reportType === 'Revenue Report' || $reportType === 'Commission Report')
    <div class="section-title">Revenue & Commissions</div>
    
    <table class="card-grid">
        <tr>
            <td>
                <div class="card">
                    <h4>Total Billings Revenue</h4>
                    <h3>₹{{ number_format($totalRevenue, 0) }}</h3>
                </div>
            </td>
            <td>
                <div class="card">
                    <h4>Total Commission Earned</h4>
                    <h3>₹{{ number_format($commissionEarned, 0) }}</h3>
                </div>
            </td>
            <td>
                <div class="card">
                    <h4>Paid Commission</h4>
                    <h3>₹{{ number_format($paidCommission, 0) }}</h3>
                </div>
            </td>
        </tr>
    </table>

    @if($revenueByClient->count() > 0)
        <div style="font-weight: bold; margin-bottom: 5px;">Top Revenue by Clients</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Total Contributed Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueByClient->take(5) as $client => $amount)
                    <tr>
                        <td>{{ $client }}</td>
                        <td class="text-right">₹{{ number_format($amount, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endif

<!-- ========================================================= -->
<!-- 3. FINANCIAL PROFITABILITY SECTION -->
<!-- ========================================================= -->
@if($reportType === 'All Reports' || $reportType === 'Revenue Report' || $reportType === 'Expense Report')
    <div class="section-title">Financial Profitability Summary</div>
    
    <table class="card-grid">
        <tr>
            <td>
                <div class="card">
                    <h4>Total Revenue</h4>
                    <h3 class="text-success">₹{{ number_format($totalRevenue, 0) }}</h3>
                </div>
            </td>
            <td>
                <div class="card">
                    <h4>Total Expenses</h4>
                    <h3 class="text-danger">₹{{ number_format($totalExpenses, 0) }}</h3>
                </div>
            </td>
            <td>
                <div class="card">
                    <h4>Net Profit</h4>
                    <h3 class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">₹{{ number_format($profit, 0) }}</h3>
                </div>
            </td>
        </tr>
    </table>

    <div style="font-weight: bold; margin-bottom: 5px;">Monthly Profitability Comparison</div>
    <table class="report-table">
        <thead>
            <tr>
                <th>Month</th>
                <th>Revenue</th>
                <th>Expenses</th>
                <th>Net Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlyProfits as $monthName => $data)
                <tr>
                    <td><strong>{{ $monthName }}</strong></td>
                    <td class="text-success">₹{{ number_format($data['revenue'], 0) }}</td>
                    <td class="text-danger">₹{{ number_format($data['expenses'], 0) }}</td>
                    <td>
                        <strong class="{{ $data['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            ₹{{ number_format($data['profit'], 0) }}
                        </strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No monthly profitability data found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endif

<!-- ========================================================= -->
<!-- 4. PROPERTY ANALYTICS SECTION -->
<!-- ========================================================= -->
@if($reportType === 'All Reports' || $reportType === 'Property Report')
    <div class="section-title">Property Analytics</div>
    
    <table class="report-table">
        <thead>
            <tr>
                <th>Property Type</th>
                <th>Total Properties</th>
                <th>Sold</th>
                <th>Available</th>
                <th>Booked</th>
                <th>Avg Listing Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse($propertyAnalytics as $type => $stats)
                <tr>
                    <td>{{ $type ?: 'Unknown' }}</td>
                    <td>{{ $stats['total'] }}</td>
                    <td>{{ $stats['sold'] }}</td>
                    <td>{{ $stats['available'] }}</td>
                    <td>{{ $stats['booked'] }}</td>
                    <td class="text-right">₹{{ number_format($stats['avg_price'], 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No property analytics data available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endif

<!-- ========================================================= -->
<!-- 5. CLIENT REPORT SECTION -->
<!-- ========================================================= -->
@if($reportType === 'All Reports' || $reportType === 'Client Report')
    <div class="section-title">Client Report</div>
    
    <table class="report-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Property Interest</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->phone ?? 'N/A' }}</td>
                    <td>{{ $c->property_interest ?? 'N/A' }}</td>
                    <td>{{ $c->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No clients found for this range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endif

<!-- ========================================================= -->
<!-- 6. LEAD REPORT SECTION -->
<!-- ========================================================= -->
@if($reportType === 'All Reports' || $reportType === 'Lead Report')
    <div class="section-title">Lead Conversion Metrics</div>
    
    <table class="report-table">
        <thead>
            <tr>
                <th>Metric</th>
                <th>Count</th>
                <th>Conversion Rate</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Leads</td>
                <td>{{ $leadConversion['total_leads'] }}</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Contacted</td>
                <td>{{ $leadConversion['contacted'] }}</td>
                <td>{{ $leadConversion['total_leads'] ? round(($leadConversion['contacted'] / $leadConversion['total_leads']) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Qualified</td>
                <td>{{ $leadConversion['qualified'] }}</td>
                <td>{{ $leadConversion['total_leads'] ? round(($leadConversion['qualified'] / $leadConversion['total_leads']) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Deals Closed</td>
                <td>{{ $leadConversion['closed'] }}</td>
                <td>{{ $leadConversion['total_leads'] ? round(($leadConversion['closed'] / $leadConversion['total_leads']) * 100, 1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>
@endif

<!-- ========================================================= -->
<!-- 7. EXPENSE REPORT SECTION -->
<!-- ========================================================= -->
@if($reportType === 'Expense Report')
    <div class="section-title">Expense Report Detail</div>
    
    <table class="report-table">
        <thead>
            <tr>
                <th>Expense Title</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Expense Date</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expensesList as $exp)
                <tr>
                    <td>{{ $exp->expense_title }}</td>
                    <td>{{ $exp->expense_category }}</td>
                    <td class="text-right" style="color:red;">₹{{ number_format($exp->amount, 2) }}</td>
                    <td>{{ optional($exp->expense_date)->format('Y-m-d') }}</td>
                    <td>{{ $exp->notes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No expenses found for this range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endif

<!-- ========================================================= -->
<!-- 8. PAYMENT REPORT SECTION -->
<!-- ========================================================= -->
@if($reportType === 'Payment Report')
    <div class="section-title">Payment Report Detail</div>
    
    <table class="report-table">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Client Name</th>
                <th>Property</th>
                <th>Invoice Amount</th>
                <th>Payment Status</th>
                <th>Issue Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($billings as $bill)
                <tr>
                    <td>{{ $bill->invoice_number }}</td>
                    <td>{{ $bill->client_name }}</td>
                    <td>{{ $bill->property_name }}</td>
                    <td class="text-right">₹{{ number_format(floatval($bill->payment_amount), 0) }}</td>
                    <td>{{ $bill->payment_status }}</td>
                    <td>{{ optional($bill->payment_date)->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No payments found for this range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endif

</body>
</html>
