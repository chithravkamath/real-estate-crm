<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Site Visits - CRM</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body style="background:#f5f7fa;">


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

<div class="page-header">
  <h2>Site Visit Scheduling</h2>
</div>

<!-- TOOLBAR -->
<form method="GET" action="{{ url('/site-visits') }}">
<div class="row">
  <div class="col-md-6">
    <input type="text" name="search" class="form-control" placeholder="Search by property or client name" value="{{ request('search') }}">
  </div>

  <div class="col-md-3">
    <select name="status" class="form-control" onchange="this.form.submit()">
      <option value="">Filter by Status</option>
      <option value="Scheduled" {{ request('status') === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
      <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
      <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
    </select>
  </div>

  <div class="col-md-3">
    <a href="{{ url('/visit/create') }}" class="btn btn-primary btn-block">+ Schedule Visit</a>
  </div>
</div>
</form>

<br>
<h3 class="page-header">Upcoming Visits</h3>

<div class="row">
  @forelse($visits->take(4) as $visit)
    @php
      $statusClass = 'label-default';
      if ($visit->status === 'Scheduled') $statusClass = 'label-info';
      if ($visit->status === 'Completed') $statusClass = 'label-success';
      if ($visit->status === 'Cancelled') $statusClass = 'label-danger';
    @endphp

    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-body visit-card">

          <h4>
            <b>{{ $visit->property_name }}</b>
            <span class="pull-right text-primary">{{ $visit->visit_time }}</span>
          </h4>

          <p><b>Client:</b> {{ $visit->client_name }}</p>
          <p><b>Date:</b> {{ $visit->visit_date->format('F d, Y') }}</p>
          <p><b>Agent:</b> {{ $visit->agent_name }}</p>
          <p><b>Address:</b> --</p>
          <p><b>Status:</b> <span class="label {{ $statusClass }}">{{ $visit->status }}</span></p>

          <div class="text-right">
            <a href="{{ url('/site-visits/'.$visit->id.'/edit') }}" class="btn btn-info btn-sm">Reschedule</a>
            <form action="{{ url('/site-visits/'.$visit->id) }}" method="POST" style="display:inline; margin:0; padding:0;">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this visit?')">Cancel</button>
            </form>
          </div>

        </div>
      </div>
    </div>
  @empty
    <div class="col-md-12">
      <div class="alert alert-info text-center">No upcoming visits scheduled.</div>
    </div>
  @endforelse
</div>

<h3>30-Day Visit Schedule</h3>

<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead>
<tr>
<th>Property</th>
<th>Client</th>
<th>Date</th>
<th>Time</th>
<th>Agent</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>

<tbody>
  @forelse($visits as $visit)
    @php
      $statusClass = 'label-default';
      if ($visit->status === 'Scheduled') $statusClass = 'label-info';
      if ($visit->status === 'Completed') $statusClass = 'label-success';
      if ($visit->status === 'Cancelled') $statusClass = 'label-danger';
    @endphp
    <tr>
      <td>{{ $visit->property_name }}</td>
      <td>{{ $visit->client_name }}</td>
      <td>{{ $visit->visit_date->format('Y-m-d') }}</td>
      <td>{{ $visit->visit_time }}</td>
      <td>{{ $visit->agent_name }}</td>
      <td><span class="label {{ $statusClass }}">{{ $visit->status }}</span></td>
      <td>
    <a href="{{ url('/site-visits/'.$visit->id.'/edit') }}"
       class="btn btn-info btn-sm">
       Edit
    </a>

    <form action="{{ url('/site-visits/'.$visit->id) }}"
          method="POST"
          style="display:inline; margin:0; padding:0;">

        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-danger btn-sm"
                onclick="return confirm('Delete this visit?')">
            Delete
        </button>

    </form>
</td>
    </tr>
  @empty
    <tr>
      <td colspan="7" class="text-center">No site visits scheduled yet.</td>
    </tr>
  @endforelse
</tbody>
  </table>
</div>

<div class="text-center">
  {{ $visits->appends(request()->query())->links() }}
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
