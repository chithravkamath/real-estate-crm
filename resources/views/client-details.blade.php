<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Client Details - CRM</title>

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

<!-- TITLE -->
<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h2>Client Profile: {{ $client->name }}</h2>
    </div>
    <div class="col-md-4 text-right" style="margin-top: 20px;">
      <a href="{{ url('/clients') }}" class="btn btn-default"><span class="glyphicon glyphicon-arrow-left"></span> Back to Clients</a>
      <a href="{{ url('/clients/'.$client->id.'/edit') }}" class="btn btn-info"><span class="glyphicon glyphicon-pencil"></span> Edit Profile</a>
    </div>
  </div>
  <hr>
</div>

<!-- SUCCESS/WARNING MESSAGES -->
<div class="container">
  @if(session('success'))
    <div class="alert alert-success">
      <span class="glyphicon glyphicon-ok-sign"></span> {{ session('success') }}
    </div>
  @endif

  @if(session('warning'))
    <div class="alert alert-warning">
      <span class="glyphicon glyphicon-info-sign"></span> {{ session('warning') }}
    </div>
  @endif
</div>

<div class="container">
  <div class="row">
    
    <!-- LEFT COLUMN: PROFILE & SITE VISITS -->
    <div class="col-md-5">
      
      <!-- CLIENT PROFILE PANEL -->
      <div class="panel panel-success">
        <div class="panel-heading">
          <span class="glyphicon glyphicon-user"></span> <strong>Client Information</strong>
        </div>
        <div class="panel-body">
          <table class="table table-striped" style="margin-bottom: 0;">
            <tr>
              <th style="border-top: 0; width: 40%;">Status</th>
              <td style="border-top: 0;">
                <span class="label {{ strtolower($client->status) === 'active' ? 'label-success' : (strtolower($client->status) === 'inactive' ? 'label-danger' : 'label-warning') }}">
                  {{ $client->status ?? 'Active' }}
                </span>
              </td>
            </tr>
            <tr>
              <th>Email</th>
              <td>{{ $client->email }}</td>
            </tr>
            <tr>
              <th>Phone</th>
              <td>{{ $client->phone }}</td>
            </tr>
            <tr>
              <th>Property Interest</th>
              <td>{{ $client->property_interest ?? 'None specified' }}</td>
            </tr>
            <tr>
              <th>Budget</th>
              <td>{{ $client->budget ?? 'None specified' }}</td>
            </tr>
            <tr>
              <th>Joining Date</th>
              <td>{{ $client->joining_date ?? 'N/A' }}</td>
            </tr>
          </table>
          
          @if($client->notes)
            <div style="margin-top: 15px; padding: 10px; background-color: #f9f9f9; border-left: 3px solid #5cb85c;">
              <strong>Client Notes:</strong>
              <p style="margin: 5px 0 0 0; font-style: italic; color: #555;">{{ $client->notes }}</p>
            </div>
          @endif
        </div>
      </div>

      <!-- RELATED SITE VISITS -->
      <div class="panel panel-info">
        <div class="panel-heading">
          <div class="row">
            <div class="col-xs-6" style="line-height: 30px;">
              <span class="glyphicon glyphicon-eye-open"></span> <strong>Related Site Visits</strong>
            </div>
            <div class="col-xs-6 text-right">
              <a href="{{ url('/visit/create') }}" class="btn btn-default btn-xs">
                <span class="glyphicon glyphicon-plus"></span> Schedule Visit
              </a>
            </div>
          </div>
        </div>
        <div class="panel-body table-responsive">
          <table class="table table-bordered table-hover" style="font-size: 12px; margin-bottom: 0;">
            <thead>
              <tr style="background-color: #f9f9f9;">
                <th>Property</th>
                <th>Date / Time</th>
                <th>Agent</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($siteVisits as $visit)
                <tr>
                  <td><strong>{{ $visit->property_name }}</strong></td>
                  <td>{{ $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format('Y-m-d') : 'N/A' }} {{ $visit->visit_time ? \Carbon\Carbon::parse($visit->visit_time)->format('H:i') : '' }}</td>
                  <td>{{ $visit->agent_name }}</td>
                  <td>
                    <span class="label {{ $visit->status === 'Completed' ? 'label-success' : ($visit->status === 'Cancelled' ? 'label-danger' : 'label-warning') }}">
                      {{ $visit->status }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">No site visits scheduled yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN: REMINDERS & TIMELINE -->
    <div class="col-md-7">
      
      <!-- REMINDER CHECKLIST -->
      <div class="panel panel-warning">
        <div class="panel-heading">
          <div class="row">
            <div class="col-xs-6" style="line-height: 30px;">
              <span class="glyphicon glyphicon-bell"></span> <strong>CRM Reminders</strong>
            </div>
            <div class="col-xs-6 text-right">
              <a href="{{ url('/reminders/create?client_id='.$client->id) }}" class="btn btn-warning btn-sm">
                <span class="glyphicon glyphicon-plus"></span> Set Reminder
              </a>
            </div>
          </div>
        </div>
        <div class="panel-body table-responsive">
          <table class="table table-bordered table-hover" style="font-size: 12px; margin-bottom: 0;">
            <thead>
              <tr style="background-color: #f9f9f9;">
                <th>Reminder Details</th>
                <th>Date / Time</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($reminders as $reminder)
                <tr>
                  <td>
                    <strong>{{ $reminder->title }}</strong>
                    @if($reminder->notes)
                      <p style="margin: 3px 0 0 0; font-size:11px; color:#666; font-style:italic;">"{{ $reminder->notes }}"</p>
                    @endif
                  </td>
                  <td>{{ $reminder->reminder_date ? \Carbon\Carbon::parse($reminder->reminder_date)->format('Y-m-d') : 'N/A' }} {{ $reminder->reminder_time ? \Carbon\Carbon::parse($reminder->reminder_time)->format('H:i') : '' }}</td>
                  <td>
                    <span class="label {{ $reminder->status === 'Completed' ? 'label-success' : 'label-warning' }}">
                      {{ $reminder->status }}
                    </span>
                  </td>
                  <td>
                    <!-- Actions -->
                    @if($reminder->status === 'Pending')
                      <a href="{{ url('/reminders/'.$reminder->id.'/complete') }}" class="btn btn-success btn-sm" title="Done"><span class="glyphicon glyphicon-ok"></span></a>
                    @endif
                    <a href="{{ url('/reminders/'.$reminder->id.'/send-email') }}" class="btn btn-default btn-sm" title="Email Notification"><span class="glyphicon glyphicon-envelope"></span></a>
                    <a href="{{ url('/reminders/'.$reminder->id.'/edit') }}" class="btn btn-info btn-sm" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="{{ url('/reminders/'.$reminder->id.'/delete') }}" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Delete this reminder?');"><span class="glyphicon glyphicon-trash"></span></a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">No pending or completed reminders.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- CHRONOLOGICAL COMMUNICATIONS TIMELINE -->
      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="row">
            <div class="col-xs-6" style="line-height: 30px;">
              <span class="glyphicon glyphicon-time"></span> <strong>Communication History</strong>
            </div>
            <div class="col-xs-6 text-right">
              <a href="{{ url('/communications/create?client_id='.$client->id) }}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-plus"></span> Log Communication
              </a>
            </div>
          </div>
        </div>
        <div class="panel-body" style="max-height: 400px; overflow-y: auto;">
          @forelse($communications as $comm)
            <div style="position: relative; margin-bottom: 20px; padding: 12px 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #fcfcfc;">
              <div style="margin-bottom: 5px;">
                @php
                  $icon = 'phone';
                  $color = '#3498db';
                  if ($comm->communication_type === 'Email') { $icon = 'envelope'; $color = '#e74c3c'; }
                  elseif ($comm->communication_type === 'Meeting') { $icon = 'calendar'; $color = '#9b59b6'; }
                  elseif ($comm->communication_type === 'WhatsApp') { $icon = 'comment'; $color = '#2ecc71'; }
                @endphp
                <span class="glyphicon glyphicon-{{ $icon }}" style="color: {{ $color }}; margin-right: 5px; font-size: 15px;"></span>
                <strong>{{ $comm->communication_type }}</strong>
                
                <span class="text-muted pull-right" style="font-size: 11px;">
                  <span class="glyphicon glyphicon-calendar"></span> {{ \Carbon\Carbon::parse($comm->communication_date)->format('Y-m-d') }}
                </span>
              </div>
              
              <div style="font-size: 13px; color: #333; margin-top: 8px; font-style: italic;">
                "{{ $comm->notes }}"
              </div>
              
              <hr style="margin: 8px 0;">
              <div style="font-size: 11px; color: #777;">
                <span class="glyphicon glyphicon-user"></span> Logged by: <strong>{{ $comm->created_by }}</strong>
                
                <div class="pull-right" style="margin-top: -5px;">
                  <a href="{{ url('/communications/'.$comm->id.'/edit') }}" class="btn btn-info btn-sm" title="Edit Log"><span class="glyphicon glyphicon-pencil"></span></a>
                  <a href="{{ url('/communications/'.$comm->id.'/delete') }}" class="btn btn-danger btn-sm" title="Delete Log" onclick="return confirm('Delete this communication log?');"><span class="glyphicon glyphicon-trash"></span></a>
                </div>
              </div>
            </div>
          @empty
            <div class="alert alert-info text-center" style="margin-bottom: 0;">
              <span class="glyphicon glyphicon-info-sign"></span> No communication logs recorded for this client yet.
            </div>
          @endforelse
        </div>
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
