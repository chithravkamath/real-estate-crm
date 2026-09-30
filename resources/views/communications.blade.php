<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Communications - CRM</title>

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
  <h2>Communication Logs</h2>
  <hr>
</div>

<!-- SUCCESS MESSAGES -->
<div class="container">
  @if(session('success'))
    <div class="alert alert-success">
      <span class="glyphicon glyphicon-ok-sign"></span> {{ session('success') }}
    </div>
  @endif
</div>

<!-- TOOLBAR / FILTERING -->
<div class="container">
  <form method="GET" action="{{ url('/communications') }}">
    <div class="row">
      <div class="col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Search contact name, notes..." value="{{ request('search') }}">
      </div>

      <div class="col-md-3">
        <select name="type" class="form-control" onchange="this.form.submit()">
          <option value="">Filter by Type</option>
          <option value="Call" {{ request('type') === 'Call' ? 'selected' : '' }}>Call</option>
          <option value="Email" {{ request('type') === 'Email' ? 'selected' : '' }}>Email</option>
          <option value="Meeting" {{ request('type') === 'Meeting' ? 'selected' : '' }}>Meeting</option>
          <option value="WhatsApp" {{ request('type') === 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
        </select>
      </div>

      <div class="col-md-3">
        <a href="{{ url('/communications/create') }}" class="btn btn-primary btn-block">+ Log New Communication</a>
      </div>
    </div>
  </form>
</div>

<br>

<!-- COMMUNICATIONS TABLE -->
<div class="container">
  <div class="panel panel-default">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-list"></span> <strong>History Log</strong>
    </div>
    <div class="panel-body table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr style="background-color: #f9f9f9;">
            <th>Type</th>
            <th>Date</th>
            <th>Related Contact</th>
            <th>Notes / Log Details</th>
            <th>Created By</th>
            <th style="width: 130px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($communications as $comm)
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
              <td>{{ $comm->communication_date ? \Carbon\Carbon::parse($comm->communication_date)->format('Y-m-d') : 'N/A' }}</td>
              <td>
                @if($comm->lead_id && $comm->lead)
                  <a href="{{ url('/leads/'.$comm->lead_id) }}"><strong>Lead:</strong> {{ $comm->lead->name }}</a>
                @elseif($comm->client_id && $comm->client)
                  <a href="{{ url('/clients/'.$comm->client_id) }}"><strong>Client:</strong> {{ $comm->client->name }}</a>
                @else
                  <span class="text-muted">N/A</span>
                @endif
              </td>
              <td>{{ $comm->notes }}</td>
              <td><strong>{{ $comm->created_by }}</strong></td>
              <td>
                <!-- Edit -->
                <a href="{{ url('/communications/'.$comm->id.'/edit') }}" class="btn btn-info btn-sm" title="Edit Log">
                  <span class="glyphicon glyphicon-pencil"></span> Edit
                </a>

                <!-- Delete -->
                <a href="{{ url('/communications/'.$comm->id.'/delete') }}" class="btn btn-danger btn-sm" title="Delete Log" onclick="return confirm('Are you sure you want to delete this communication log?');">
                  <span class="glyphicon glyphicon-trash"></span>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center">No communication logs found matching the criteria.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- PAGINATION -->
<div class="container text-center">
  {{ $communications->appends(request()->query())->links() }}
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
