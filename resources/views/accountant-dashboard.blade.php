<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accountant Dashboard - Real Estate CRM</title>

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

<!-- 🔹 TITLE -->
<div class="container">
  <h2 style="color:black;">Accountant Dashboard Overview</h2>
  <hr>
</div>

<div class="container">

<!-- 🔹 CORE FINANCIAL CARDS -->
<div class="row text-center">
  <div class="col-md-4">
    <div class="panel panel-primary" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #337ab7;">
        <span class="glyphicon glyphicon-piggy-bank" style="font-size: 24px; color: #337ab7;"></span>
        <h2>₹{{ number_format($totalRevenue, 0) }}</h2>
        <p><strong>Total Revenue</strong></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-danger" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #d9534f;">
        <span class="glyphicon glyphicon-export" style="font-size: 24px; color: #d9534f;"></span>
        <h2>₹{{ number_format($totalExpenses, 0) }}</h2>
        <p><strong>Total Expenses</strong></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-success" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #5cb85c;">
        <span class="glyphicon glyphicon-usd" style="font-size: 24px; color: #5cb85c;"></span>
        <h2>₹{{ number_format($netProfit, 0) }}</h2>
        <p><strong>Net Profit</strong></p>
      </div>
    </div>
  </div>
</div>

<!-- 🔹 PAYMENT RECONCILIATION SUMMARY CARDS -->
<div class="row text-center" style="margin-top: 10px; margin-bottom: 20px;">
  <div class="col-md-4">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #f0ad4e;">
        <span class="glyphicon glyphicon-hourglass" style="font-size: 24px; color: #f0ad4e;"></span>
        <h2>{{ $totalPendingPayments }}</h2>
        <p><strong>Total Pending Payments</strong></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #5cb85c;">
        <span class="glyphicon glyphicon-ok-sign" style="font-size: 24px; color: #5cb85c;"></span>
        <h2>{{ $totalReconciledPayments }}</h2>
        <p><strong>Total Reconciled Payments</strong></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #d9534f;">
        <span class="glyphicon glyphicon-exclamation-sign" style="font-size: 24px; color: #d9534f;"></span>
        <h2>₹{{ number_format($outstandingBalance, 0) }}</h2>
        <p><strong>Outstanding Balance</strong></p>
      </div>
    </div>
  </div>
</div>

<!-- ROW 3: RECENT TRANSACTIONS LEDGERS -->
<div class="row">

  <!-- RECENT PAYMENTS -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-list"></span> <strong>Recent Payments / Billings</strong>
        <a href="{{ url('/billing') }}" class="btn btn-default btn-xs pull-right">View Invoices</a>
      </div>
      <div class="panel-body table-responsive" style="max-height: 280px; overflow-y: auto;">
        <table class="table table-bordered table-hover" style="margin-bottom:0;">
          <thead>
            <tr style="background-color: #f9f9f9;">
              <th>Invoice</th>
              <th>Client</th>
              <th>Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentBillings as $bill)
              <tr>
                <td><strong>{{ $bill->invoice_number }}</strong></td>
                <td>{{ $bill->client_name }}</td>
                <td><strong>₹{{ number_format(floatval($bill->payment_amount), 0) }}</strong></td>
                <td>
                  <span class="label {{ $bill->payment_status === 'Paid' ? 'label-success' : ($bill->payment_status === 'Partial' ? 'label-warning' : 'label-danger') }}">
                    {{ $bill->payment_status }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">No recent billing logs found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- RECENT EXPENSES -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-list"></span> <strong>Recent Expenses Logs</strong>
        <a href="{{ url('/billing') }}" class="btn btn-default btn-xs pull-right">Manage Expenses</a>
      </div>
      <div class="panel-body table-responsive" style="max-height: 280px; overflow-y: auto;">
        <table class="table table-bordered table-hover" style="margin-bottom:0;">
          <thead>
            <tr style="background-color: #f9f9f9;">
              <th>Title</th>
              <th>Category</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentExpenses as $expense)
              <tr>
                <td><strong>{{ $expense->expense_title }}</strong></td>
                <td>
                  <span class="label {{ $expense->expense_category === 'Marketing' ? 'label-primary' : ($expense->expense_category === 'Travel' ? 'label-info' : ($expense->expense_category === 'Maintenance' ? 'label-warning' : 'label-success')) }}">
                    {{ $expense->expense_category }}
                  </span>
                </td>
                <td><strong class="text-danger">₹{{ number_format($expense->amount, 0) }}</strong></td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted">No expenses recorded yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ROW 4: MONTHLY REVENUE & EXPENSE breakdowns -->
<div class="row" style="margin-bottom: 20px;">

  <!-- MONTHLY REVENUE SUMMARY -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-stats"></span> <strong>Monthly Billings Revenue Summary</strong>
      </div>
      <div class="panel-body" style="max-height: 250px; overflow-y: auto;">
        <ul class="list-group" style="margin-bottom:0;">
          @forelse($monthlyRevenue as $item)
            <li class="list-group-item">
              <span class="badge" style="background-color:#5cb85c; color:#fff; font-weight:bold;">₹{{ number_format($item->total, 0) }}</span>
              <strong>{{ $item->month }}</strong>
            </li>
          @empty
            <li class="list-group-item text-center text-muted">No monthly billing revenue data available.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>

  <!-- MONTHLY EXPENSE SUMMARY -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-stats"></span> <strong>Monthly Expense Summary</strong>
      </div>
      <div class="panel-body" style="max-height: 250px; overflow-y: auto;">
        <ul class="list-group" style="margin-bottom:0;">
          @forelse($monthlyExpenses as $item)
            <li class="list-group-item">
              <span class="badge" style="background-color:#d9534f; color:#fff; font-weight:bold;">₹{{ number_format($item->total, 0) }}</span>
              <strong>{{ $item->month }}</strong>
            </li>
          @empty
            <li class="list-group-item text-center text-muted">No monthly expense data available.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>

</div>

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
        <li><a href="{{ url('/billing') }}">Billing & Commission</a></li>
        <li><a href="{{ url('/reports') }}">Reports</a></li>
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

</body>
</html>
