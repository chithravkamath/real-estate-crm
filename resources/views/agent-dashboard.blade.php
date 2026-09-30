<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agent Dashboard - Real Estate CRM</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('css/crm-theme.css') }}">
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
  <h2 style="color:black;">Agent Dashboard Overview</h2>
  <hr>
</div>

<div class="container">

 <!-- CORE METRICS CARDS -->
<div class="row text-center">

  <div class="col-md-4">
    <div class="panel panel-warning">
      <div class="panel-body">
        <span class="glyphicon glyphicon-list-alt" style="font-size: 24px;"></span>
        <h2>{{ $assignedLeads }}</h2>
        <p>Assigned Leads</p>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="panel panel-success">
      <div class="panel-body">
        <span class="glyphicon glyphicon-user" style="font-size: 24px;"></span>
        <h2>{{ $activeClientsCount }}</h2>
        <p>Active Clients</p>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="panel panel-danger">
      <div class="panel-body">
        <span class="glyphicon glyphicon-briefcase" style="font-size: 24px;"></span>
        <h2>{{ $ongoingDeals }}</h2>
        <p>Ongoing Deals</p>
      </div>
    </div>
  </div>

</div>

<!-- 🔹 OPERATIONAL PROCESS CARDS -->
<div class="row text-center" style="margin-top: 15px; margin-bottom: 20px;">
  <div class="col-md-4">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #31708f;">
        <span class="glyphicon glyphicon-map-marker" style="font-size: 24px; color: #31708f;"></span>
        <h2>{{ $siteVisits }}</h2>
        <p><strong>Scheduled Site Visits</strong></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #f0ad4e;">
        <span class="glyphicon glyphicon-hourglass" style="font-size: 24px; color: #f0ad4e;"></span>
        <h2>{{ $pendingReminders }}</h2>
        <p><strong>Pending Reminders</strong></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
      <div class="panel-body" style="border-left: 5px solid #5cb85c;">
        <span class="glyphicon glyphicon-comment" style="font-size: 24px; color: #5cb85c;"></span>
        <h2>{{ $totalCommunications }}</h2>
        <p><strong>Logged Communications</strong></p>
      </div>
    </div>
  </div>
</div>

<!-- ROW 2: DAILY ALERTS & CALENDAR -->
<div class="row">

  <!-- TODAY'S FOLLOW-UPS -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading" style="background-color: #fcf8e3; color: #8a6d3b; font-weight: bold;">
        <span class="glyphicon glyphicon-time"></span> Today's Follow-up Leads
      </div>
      <div class="panel-body table-responsive" style="max-height: 250px; overflow-y: auto;">
        <table class="table table-bordered table-hover" style="margin-bottom:0;">
          <thead>
            <tr style="background-color: #f9f9f9;">
              <th>Lead Name</th>
              <th>Phone</th>
              <th>Property</th>
            </tr>
          </thead>
          <tbody>
            @forelse($todaysFollowups as $lead)
              <tr>
                <td><a href="{{ url('/leads/'.$lead->id) }}"><strong>{{ $lead->name }}</strong></a></td>
                <td>{{ $lead->phone }}</td>
                <td>{{ $lead->interested_property ?? 'N/A' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted">No follow-ups scheduled for today.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- UPCOMING SITE VISITS -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading" style="background-color: #d9edf7; color: #31708f; font-weight: bold;">
        <span class="glyphicon glyphicon-map-marker"></span> Upcoming Site Visits
      </div>
      <div class="panel-body table-responsive" style="max-height: 250px; overflow-y: auto;">
        <table class="table table-bordered table-hover" style="margin-bottom:0;">
          <thead>
            <tr style="background-color: #f9f9f9;">
              <th>Client Name</th>
              <th>Property Name</th>
              <th>Date / Time</th>
            </tr>
          </thead>
          <tbody>
            @forelse($upcomingVisits as $visit)
              <tr>
                <td><strong>{{ $visit->client_name }}</strong></td>
                <td>{{ $visit->property_name }}</td>
                <td>{{ optional($visit->visit_date)->format('Y-m-d') }} {{ $visit->visit_time }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted">No upcoming site visits scheduled.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ROW 3: CRM REMINDERS -->
<div class="row">

  <!-- TODAY'S REMINDERS -->
  <div class="col-md-6">
    <div class="panel panel-danger">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-alert"></span> Today's CRM Reminders
      </div>
      <div class="panel-body table-responsive" style="max-height: 280px; overflow-y: auto;">

        <table class="table table-bordered table-hover" style="margin-bottom: 0;">
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
      <div class="panel-body table-responsive" style="max-height: 280px; overflow-y: auto;">

        <table class="table table-bordered table-hover" style="margin-bottom: 0;">
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
<div class="row" style="margin-bottom: 20px;">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-comment"></span> <strong>Your Recent Communications Logs</strong>
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
                <td>{{ \Illuminate\Support\Str::limit($comm->notes, 150) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">No communication logs recorded yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
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
