<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

</head>

<body>

<!--  TOP HEADER -->
<nav class="navbar navbar-default">
  <div class="container">

    <div class="navbar-header">
      <p class="navbar-brand" style="color:black;"><strong>Real Estate CRM</strong></p>
    </div>

    <ul class="nav navbar-nav navbar-right">
      <li><a href="#">Welcome, {{ Auth::user()->name }}</a></li>
      <li><a href="{{ url('/') }}">Logout</a></li>
    </ul>

  </div>
</nav>

<!-- 🔹 MAIN NAV -->
<nav class="navbar navbar-default">
  <div class="container">

    <div class="navbar-header">
      <button class="navbar-toggle" data-toggle="collapse" data-target="#mainNav">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>

    <div class="collapse navbar-collapse" id="mainNav">
      @include('partials.main-nav')
    </div>

  </div>
</nav>

<div class="container">

<h2>Reports & Analytics</h2>
<hr>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- FILTER -->
<form method="GET" action="{{ url('/reports') }}" id="reportFilterForm">
<div class="panel panel-default">
<div class="panel-body">

<div class="row">

<div class="col-md-2">
<label for="from_date">From Date</label>
<input type="date" id="from_date" name="from_date" class="form-control" value="{{ request('from_date') }}" max="{{ date('Y-m-d') }}">
</div>

<div class="col-md-2">
<label for="to_date">To Date</label>
<input type="date" id="to_date" name="to_date" class="form-control" value="{{ request('to_date') }}" max="{{ date('Y-m-d') }}">
</div>

<div class="col-md-4">
<label for="report_type">Report Type</label>
<select id="report_type" name="report_type" class="form-control">
<option value="All Reports" {{ request('report_type') == 'All Reports' ? 'selected' : '' }}>All Reports</option>
<option value="Sales Report" {{ request('report_type') == 'Sales Report' ? 'selected' : '' }}>Sales Report</option>
<option value="Revenue Report" {{ request('report_type') == 'Revenue Report' ? 'selected' : '' }}>Revenue Report</option>
<option value="Commission Report" {{ request('report_type') == 'Commission Report' ? 'selected' : '' }}>Commission Report</option>
<option value="Property Report" {{ request('report_type') == 'Property Report' ? 'selected' : '' }}>Property Report</option>
<option value="Client Report" {{ request('report_type') == 'Client Report' ? 'selected' : '' }}>Client Report</option>
<option value="Lead Report" {{ request('report_type') == 'Lead Report' ? 'selected' : '' }}>Lead Report</option>
<option value="Expense Report" {{ request('report_type') == 'Expense Report' ? 'selected' : '' }}>Expense Report</option>
<option value="Payment Report" {{ request('report_type') == 'Payment Report' ? 'selected' : '' }}>Payment Report</option>
</select>
</div>

<div class="col-md-4" style="margin-top:25px;">
<button type="submit" class="btn btn-primary">Generate</button>
<a href="{{ url('/reports/export?' . http_build_query(request()->query())) }}" class="btn btn-default">Export PDF</a>
</div>

</div>

</div>
</div>
</form>

<!-- SALES PERFORMANCE -->
@if($reportType === 'All Reports' || $reportType === 'Sales Report')
<h3>Sales Performance</h3>

<div class="row">

<div class="col-md-6">
<div class="panel panel-default">
<div class="panel-body">

<h4>Total Sales</h4>
<hr>

<p>Total Property Values Sold</p>
<h3>₹{{ number_format($totalSales, 0) }}</h3>

<p>Deals Closed</p>
<h3>{{ $dealsClosed }}</h3>

<p>Average Price</p>
<h3>₹{{ number_format($averagePrice, 0) }}</h3>

</div>
</div>
</div>

<div class="col-md-6">
<div class="panel panel-default">
<div class="panel-body">

<h4>Monthly Sales Trend</h4>
<hr>

@forelse($monthlySales as $month => $amount)
  @php
    $maxAmount = max($monthlySales->max(), 1);
    $width = min(100, round(($amount / $maxAmount) * 100));
  @endphp
  <p>{{ $month }}</p>
  <h4>₹{{ number_format($amount, 0) }}</h4>
  <div class="progress">
    <div class="progress-bar" style="width:{{ $width }}%"></div>
  </div>
@empty
  <p>No sales data found.</p>
@endforelse

</div>
</div>
</div>

</div>

@if($reportType === 'Sales Report')
<h3>Deals Close History</h3>
<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead>
<tr style="background-color: #f9f9f9;">
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
<td>₹{{ number_format(floatval($dealItem->deal_amount), 0) }}</td>
</tr>
@empty
<tr>
<td colspan="5" class="text-center text-muted">No deals found for this range.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
@endif
@endif

<!-- COMMISSION -->
@if($reportType === 'All Reports' || $reportType === 'Revenue Report' || $reportType === 'Commission Report')
<h3>Commission & Revenue</h3>

<div class="row">

<div class="col-md-6">
<div class="panel panel-default">
<div class="panel-body">

<h4>Commission Earned</h4>
<hr>

<p>Total Commission</p>
<h3>₹{{ number_format($commissionEarned, 0) }}</h3>

<p>Commission Rate</p>
<h3>5%</h3>

<p>Paid Commission</p>
<h3>₹{{ number_format($paidCommission, 0) }}</h3>

</div>
</div>
</div>

<div class="col-md-6">
<div class="panel panel-default">
<div class="panel-body">

<h4>Revenue by Client</h4>
<hr>

@forelse($revenueByClient->take(3) as $client => $amount)

  <p>{{ $client }} - ₹{{ number_format($amount, 0) }}</p>

  <div class="progress">
    <div class="progress-bar"
         style="width:{{ min(100, round(($amount / max($revenueByClient->max(), 1)) * 100)) }}%">
    </div>
  </div>

@empty

  <p>No revenue data found.</p>

@endforelse

</div>
</div>
</div>

</div>
@endif

<!-- 🔹 FINANCIAL PROFITABILITY -->
@if($reportType === 'All Reports' || $reportType === 'Revenue Report' || $reportType === 'Expense Report')
<h3>Financial Profitability (Revenue - Expenses)</h3>

<div class="row">

  <!-- SUMMARY OVERVIEW -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-body">
        <h4>Net Profitability Summary</h4>
        <hr>

        <p>Total Billings Revenue</p>
        <h3>₹{{ number_format($totalRevenue, 0) }}</h3>

        <p>Total Recorded Expenses</p>
        <h3 class="text-danger">₹{{ number_format($totalExpenses, 0) }}</h3>

        <hr>
        <p>Net Calculated Profit</p>
        <h3 class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
          ₹{{ number_format($profit, 0) }}
        </h3>
      </div>
    </div>
  </div>

  <!-- MONTHLY TREND -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Monthly Profitability Comparison</div>
      <div class="panel-body table-responsive" style="max-height: 250px; overflow-y: auto;">
        <table class="table table-bordered table-striped" style="margin-bottom:0;">
          <thead>
            <tr style="background-color: #f9f9f9;">
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
                <td colspan="4" class="text-center text-muted">No monthly profitability data available.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endif

<!-- PROPERTY ANALYTICS -->
@if($reportType === 'All Reports' || $reportType === 'Property Report')
<h3>Property Analytics</h3>

<div class="table-responsive">
<table class="table table-bordered table-hover">

<thead>
<tr style="background-color: #f9f9f9;">
<th>Type</th>
<th>Total</th>
<th>Sold</th>
<th>Available</th>
<th>Booked</th>
<th>Avg Price</th>
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
<td>₹{{ number_format($stats['avg_price'], 0) }}</td>
</tr>
@empty
<tr>
<td colspan="6" class="text-center">No property analytics available.</td>
</tr>
@endforelse
</tbody>

</table>
</div>
@endif

<!-- LEAD CONVERSION -->
@if($reportType === 'All Reports' || $reportType === 'Lead Report')
<h3>Lead Conversion</h3>

<div class="table-responsive">
<table class="table table-bordered table-hover">

<thead>
<tr style="background-color: #f9f9f9;">
<th>Metric</th>
<th>Count</th>
<th>Conversion</th>
<th>Trend</th>
</tr>
</thead>

<tbody>

<tr>
<td>Total Leads</td>
<td>{{ $leadConversion['total_leads'] }}</td>
<td>-</td>
<td class="text-success">↑ {{ $leadConversion['total_leads'] ? round(($leadConversion['contacted'] / $leadConversion['total_leads']) * 100, 1) : 0 }}%</td>
</tr>

<tr>
<td>Contacted</td>
<td>{{ $leadConversion['contacted'] }}</td>
<td>{{ $leadConversion['total_leads'] ? round(($leadConversion['contacted'] / $leadConversion['total_leads']) * 100, 1) : 0 }}%</td>
<td class="text-success">↑ {{ $leadConversion['total_leads'] ? round((($leadConversion['contacted'] - $leadConversion['qualified']) / max($leadConversion['total_leads'], 1)) * 100, 1) : 0 }}%</td>
</tr>

<tr>
<td>Qualified</td>
<td>{{ $leadConversion['qualified'] }}</td>
<td>{{ $leadConversion['total_leads'] ? round(($leadConversion['qualified'] / $leadConversion['total_leads']) * 100, 1) : 0 }}%</td>
<td class="text-success">↑ {{ $leadConversion['total_leads'] ? round((($leadConversion['qualified'] - $leadConversion['closed']) / max($leadConversion['total_leads'], 1)) * 100, 1) : 0 }}%</td>
</tr>

<tr>
<td>Deals Closed</td>
<td>{{ $leadConversion['closed'] }}</td>
<td>{{ $leadConversion['total_leads'] ? round(($leadConversion['closed'] / $leadConversion['total_leads']) * 100, 1) : 0 }}%</td>
<td class="text-success">↑ {{ $leadConversion['total_leads'] ? round((($leadConversion['closed'] / max($leadConversion['total_leads'], 1)) * 100), 1) : 0 }}%</td>
</tr>

</tbody>

</table>
</div>
@endif

<!-- CLIENTS REPORT DETAIL -->
@if($reportType === 'All Reports' || $reportType === 'Client Report')
<h3>Client Report</h3>
<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead>
<tr style="background-color: #f9f9f9;">
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
<td colspan="5" class="text-center">No clients found for this range.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
@endif

<!-- EXPENSE REPORT DETAIL -->
@if($reportType === 'Expense Report')
<h3>Expense Report Detail</h3>
<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead>
<tr style="background-color: #f9f9f9;">
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
<td class="text-danger">₹{{ number_format($exp->amount, 2) }}</td>
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
</div>
@endif

<!-- PAYMENT REPORT DETAIL -->
@if($reportType === 'Payment Report')
<h3>Payment Report Detail</h3>
<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead>
<tr style="background-color: #f9f9f9;">
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
<td>₹{{ number_format(floatval($bill->payment_amount), 0) }}</td>
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
</div>
@endif


<!-- 🔹 FOOTER -->
<footer class="container">
  <hr>

  <div class="row">

    <div class="col-md-4">
      <h4>Company Information</h4>
      <ul class="list-unstyled">
        <li><a href="{{ url('/about') }}">About Us</a></li>
        <li><a href="{{ url('/contact') }}">Contact Us</a></li>
        <li><a href="{{ url('/privacy') }}">Privacy Policy</a></li>
      </ul>
    </div>

    <div class="col-md-4">
      <h4>CRM Modules</h4>
      <ul class="list-unstyled">
        <li><a href="{{ url('/properties') }}">Properties</a></li>
        <li><a href="{{ url('/clients') }}">Clients</a></li>
        <li><a href="{{ url('/leads') }}">Leads</a></li>
      </ul>
    </div>

    <div class="col-md-4">
      <h4>Quick Links</h4>
      <ul class="list-unstyled">
        <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
        <li><a href="{{ url('/support') }}">Support</a></li>
      </ul>
    </div>

  </div>

  <p class="text-center">&copy; 2026 Real Estate CRM</p>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    function syncDates() {
        var fromDate = $('#from_date').val();
        if (fromDate) {
            $('#to_date').attr('min', fromDate);
        } else {
            $('#to_date').removeAttr('min');
        }
    }
    
    $('#from_date').on('change', syncDates);
    syncDates(); // Run on page load
});
</script>

</body>
</html>
