<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Real Estate CRM</title>

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
  <h2 style="color:black;">Dashboard Overview</h2>
  <hr>
</div>

<div class="container">

 <!-- CARDS -->
<div class="row text-center">

  <div class="col-md-3">
    <div class="panel panel-primary">
      <div class="panel-body">
        <span class="glyphicon glyphicon-home"></span>
        <h2>{{ $totalProperties }}</h2>
        <p>Total Properties</p>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="panel panel-success">
      <div class="panel-body">
        <span class="glyphicon glyphicon-user"></span>
        <h2>{{ $totalClients }}</h2>
        <p>Total Clients</p>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="panel panel-warning">
      <div class="panel-body">
        <span class="glyphicon glyphicon-list-alt"></span>
        <h2>{{ $totalLeads }}</h2>
        <p>Active Leads</p>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="panel panel-danger">
      <div class="panel-body">
        <span class="glyphicon glyphicon-briefcase"></span>
        <h2>{{ $totalDeals }}</h2>
        <p>Ongoing Deals</p>
      </div>
    </div>
  </div>

</div>

<!-- 🔹 FINANCIAL SUMMARY CARDS -->
<div class="row text-center" style="margin-top: 15px;">
  <div class="col-md-4">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #337ab7;">
        <span class="glyphicon glyphicon-piggy-bank" style="font-size: 24px; color: #337ab7;"></span>
        <h2>₹{{ number_format($totalRevenue, 0) }}</h2>
        <p><strong>Total Revenue</strong></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #d9534f;">
        <span class="glyphicon glyphicon-export" style="font-size: 24px; color: #d9534f;"></span>
        <h2>₹{{ number_format($totalExpenses, 0) }}</h2>
        <p><strong>Total Expenses</strong></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #5cb85c;">
        <span class="glyphicon glyphicon-usd" style="font-size: 24px; color: #5cb85c;"></span>
        <h2>₹{{ number_format($netProfit, 0) }}</h2>
        <p><strong>Net Profit</strong></p>
      </div>
    </div>
  </div>
</div>

<!-- 🔹 PAYMENT RECONCILIATION SUMMARY -->
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

<!-- ROW 2 -->
<div class="row">

  <!-- RECENT PROPERTIES -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Recent Properties Added</div>
      <div class="panel-body">

        <table class="table table-bordered table-hover">
          <tr>
            <th>Property Name</th>
            <th>Location</th>
            <th>Status</th>
          </tr>

          @forelse($recentProperties as $property)
            <tr>
              <td>{{ $property->property_name }}</td>
              <td>{{ $property->location }}</td>
              <td><span class="label {{ strtolower($property->status) === 'available' ? 'label-success' : (strtolower($property->status) === 'booked' ? 'label-warning' : (strtolower($property->status) === 'sold' ? 'label-danger' : 'label-default')) }}">{{ $property->status ?? 'Unknown' }}</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center">No recent properties found.</td>
            </tr>
          @endforelse

        </table>

      </div>
    </div>
  </div>

  <!-- RECENT ACTIVITIES -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Recent Activities</div>

      <div class="list-group">
        @foreach($recentActivities as $activity)
          <a class="list-group-item">{{ $activity['message'] }}</a>
        @endforeach
        @if($recentActivities->isEmpty())
          <div class="list-group-item">No recent activity yet.</div>
        @endif
      </div>

    </div>
  </div>

</div>

<!-- ROW 3: REMINDERS -->
<div class="row">

  <!-- TODAY'S REMINDERS -->
  <div class="col-md-6">
    <div class="panel panel-danger">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-alert"></span> Today's CRM Reminders
      </div>
      <div class="panel-body table-responsive">

        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Title</th>
              <th>Time</th>
              <th>Related To</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($todaysReminders as $reminder)
              <tr>
                <td><strong>{{ $reminder->title }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($reminder->reminder_time)->format('H:i') }}</td>
                <td>
                  <span class="label {{ $reminder->related_type === 'Lead' ? 'label-warning' : ($reminder->related_type === 'Client' ? 'label-success' : ($reminder->related_type === 'Site Visit' ? 'label-info' : 'label-primary')) }}">
                    {{ $reminder->related_type }}
                  </span>
                  {{ $reminder->related_name }}
                </td>
                <td>
                  <a href="{{ url('/reminders/'.$reminder->id.'/complete') }}" class="btn btn-success btn-xs" title="Mark Done">
                    <span class="glyphicon glyphicon-ok"></span> Done
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">No pending reminders for today.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

      </div>
    </div>
  </div>

  <!-- UPCOMING REMINDERS -->
  <div class="col-md-6">
    <div class="panel panel-info">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-calendar"></span> Upcoming CRM Reminders
      </div>
      <div class="panel-body table-responsive">

        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Title</th>
              <th>Date / Time</th>
              <th>Related To</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($upcomingReminders as $reminder)
              <tr>
                <td><strong>{{ $reminder->title }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($reminder->reminder_date)->format('Y-m-d') }} {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('H:i') }}</td>
                <td>
                  <span class="label {{ $reminder->related_type === 'Lead' ? 'label-warning' : ($reminder->related_type === 'Client' ? 'label-success' : ($reminder->related_type === 'Site Visit' ? 'label-info' : 'label-primary')) }}">
                    {{ $reminder->related_type }}
                  </span>
                  {{ $reminder->related_name }}
                </td>
                <td>
                  <a href="{{ url('/reminders/'.$reminder->id.'/edit') }}" class="btn btn-info btn-xs" title="Edit">
                    <span class="glyphicon glyphicon-pencil"></span>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">No upcoming reminders scheduled.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

      </div>
    </div>
  </div>

</div>

<!-- ROW 4: RECENT COMMUNICATIONS -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-comment"></span> <strong>Recent Communications History Logs</strong>
        <a href="{{ url('/communications') }}" class="btn btn-default btn-xs pull-right">View All History</a>
      </div>
      <div class="panel-body table-responsive">
        <table class="table table-bordered table-hover" style="margin-bottom:0;">
          <thead>
            <tr style="background-color: #f9f9f9;">
              <th style="width: 120px;">Type</th>
              <th style="width: 120px;">Date</th>
              <th style="width: 220px;">Contact</th>
              <th>Discussion Details / Notes</th>
              <th style="width: 150px;">Logged By</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentCommunications as $comm)
              <tr>
                <td>
                  @php
                    $labelClass = 'label-default';
                    if ($comm->communication_type === 'Call') $labelClass = 'label-info';
                    elseif ($comm->communication_type === 'Email') $labelClass = 'label-danger';
                    elseif ($comm->communication_type === 'Meeting') $labelClass = 'label-primary';
                    elseif ($comm->communication_type === 'WhatsApp') $labelClass = 'label-success';
                  @endphp
                  <span class="label {{ $labelClass }}">{{ $comm->communication_type }}</span>
                </td>
                <td>{{ \Carbon\Carbon::parse($comm->communication_date)->format('Y-m-d') }}</td>
                <td>
                  @if($comm->lead_id && $comm->lead)
                    <a href="{{ url('/leads/'.$comm->lead_id) }}"><strong>Lead:</strong> {{ $comm->lead->name }}</a>
                  @elseif($comm->client_id && $comm->client)
                    <a href="{{ url('/clients/'.$comm->client_id) }}"><strong>Client:</strong> {{ $comm->client->name }}</a>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>{{ \Illuminate\Support\Str::limit($comm->notes, 100) }}</td>
                <td><strong>{{ $comm->created_by }}</strong></td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">No communication logs recorded yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
</div>

<!-- 🔹 ROW 5: EXPENSE TRACKING OVERVIEW -->
<div class="row" style="margin-top: 20px;">
  <!-- RECENT EXPENSES -->
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-list"></span> <strong>Recent Expenses Logs</strong>
        <a href="{{ url('/billing') }}" class="btn btn-default btn-xs pull-right">Manage Expenses</a>
      </div>
      <div class="panel-body table-responsive">
        <table class="table table-bordered table-hover" style="margin-bottom:0;">
          <thead>
            <tr style="background-color: #f9f9f9;">
              <th>Title</th>
              <th>Category</th>
              <th>Amount</th>
              <th>Date</th>
              <th>Created By</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentExpenses as $expense)
              <tr>
                <td><strong>{{ $expense->expense_title }}</strong></td>
                <td>
                  @php
                    $catClass = 'label-default';
                    if ($expense->expense_category === 'Marketing') $catClass = 'label-primary';
                    elseif ($expense->expense_category === 'Travel') $catClass = 'label-info';
                    elseif ($expense->expense_category === 'Maintenance') $catClass = 'label-warning';
                    elseif ($expense->expense_category === 'Office') $catClass = 'label-success';
                  @endphp
                  <span class="label {{ $catClass }}">{{ $expense->expense_category }}</span>
                </td>
                <td><strong class="text-danger">₹{{ number_format($expense->amount, 0) }}</strong></td>
                <td>{{ optional($expense->expense_date)->format('Y-m-d') }}</td>
                <td>{{ $expense->created_by }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">No expenses recorded yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- MONTHLY EXPENSE SUMMARY -->
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-stats"></span> <strong>Monthly Expense Summary</strong>
      </div>
      <div class="panel-body">
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

</body>
</html>
