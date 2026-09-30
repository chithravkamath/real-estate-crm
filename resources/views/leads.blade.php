<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leads - CRM</title>

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
  <h2>Lead Management</h2>
  <hr>

  <!-- Success Notifications -->
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

<!-- TOOLBAR -->
<div class="container">
  <form method="GET" action="{{ url('/leads') }}">
    <div class="row">
      <div class="col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Search leads..." value="{{ request('search') }}">
      </div>

      <div class="col-md-3">
        <select name="status" class="form-control" onchange="this.form.submit()">
          <option value="">Filter by Status</option>
          <option value="New" {{ request('status') === 'New' ? 'selected' : '' }}>New</option>
          <option value="Contacted" {{ request('status') === 'Contacted' ? 'selected' : '' }}>Contacted</option>
          <option value="Qualified" {{ request('status') === 'Qualified' ? 'selected' : '' }}>Qualified</option>
          <option value="Negotiating" {{ request('status') === 'Negotiating' ? 'selected' : '' }}>Negotiating</option>
          <option value="Converted" {{ request('status') === 'Converted' ? 'selected' : '' }}>Converted</option>
        </select>
      </div>

      <div class="col-md-3">
        <a href="{{ url('/leads/create') }}" class="btn btn-primary btn-block">+ Add New Lead</a>
      </div>
    </div>
  </form>
</div>

<br>

<!-- STATS -->
<div class="container">
  <div class="row text-center">

    <div class="col-md-3">
      <div class="panel panel-default">
        <div class="panel-body">
          <h2>{{ $totalLeads }}</h2>
          <p>Total Leads</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="panel panel-info">
        <div class="panel-body">
          <h2>{{ $newLeads }}</h2>
          <p>New Leads</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="panel panel-warning">
        <div class="panel-body">
          <h2>{{ $contactedLeads }}</h2>
          <p>Contacted</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="panel panel-success">
        <div class="panel-body">
          <h2>{{ $qualifiedLeads }}</h2>
          <p>Qualified</p>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- TABLE -->
<div class="container">
  <h3>Lead Details</h3>

  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Property</th>
        <th>Status</th>
        <th>Agent</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      @forelse($leads as $lead)
      <tr>
        <td>{{ $lead->name }}</td>
        <td>{{ $lead->email }}</td>
        <td>{{ $lead->phone }}</td>
        <td>{{ $lead->interested_property }}</td>
        <td>
          @php
            $labelClass = 'label-default';
            if ($lead->status === 'New') $labelClass = 'label-info';
            if ($lead->status === 'Contacted') $labelClass = 'label-warning';
            if ($lead->status === 'Qualified') $labelClass = 'label-success';
            if ($lead->status === 'Closed') $labelClass = 'label-primary';
          @endphp
          <span class="label {{ $labelClass }}">{{ $lead->status }}</span>
        </td>
        <td>{{ $lead->agent }}</td>
        <td>{{ optional($lead->follow_up_date)->format('Y-m-d') }}</td>
        <td>
          <a href="{{ url('/leads/'.$lead->id) }}" class="btn btn-default btn-sm">View</a>
          <a href="{{ url('/leads/'.$lead->id.'/edit') }}" class="btn btn-info btn-sm">Edit</a>
          <form action="{{ url('/leads/'.$lead->id) }}" method="POST" style="display:inline; margin:0; padding:0;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this lead?')">Delete</button>
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="8" class="text-center">No leads found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="container text-center">
  {{ $leads->appends(request()->query())->links() }}
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
