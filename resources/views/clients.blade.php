<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clients - CRM</title>

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
  <h2>Client Management</h2>
  <hr>

  @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif
</div>

<!-- TOOLBAR -->
<div class="container">
  <form method="GET" action="{{ url('/clients') }}">
    <div class="row">
      <div class="col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Search clients..." value="{{ request('search') }}">
      </div>

      <div class="col-md-3">
        <select name="status" class="form-control" onchange="this.form.submit()">
          <option value="">Filter by Status</option>
          <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
          <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
          <option value="VIP" {{ request('status') === 'VIP' ? 'selected' : '' }}>VIP</option>
        </select>
      </div>

      <div class="col-md-3">
        <a href="{{ url('/clients/create') }}" class="btn btn-primary btn-block">+ Add New Client</a>
      </div>
    </div>
  </form>
</div>

<br>
<!-- CARDS -->
<div class="container">
  <h3>Client Profiles</h3>

  <div class="row">
    @forelse ($clients as $client)
      <div class="col-md-4 col-sm-6">
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>{{ $client->name }} <span class="label {{ strtolower($client->status) === 'active' ? 'label-success' : (strtolower($client->status) === 'inactive' ? 'label-danger' : 'label-warning') }} pull-right">{{ $client->status ?? 'Unknown' }}</span></h4>
            <p>Email: {{ $client->email }}</p>
            <p>Phone: {{ $client->phone }}</p>
            <p>Property: {{ $client->property_interest }}</p>
            <p>Budget: {{ $client->budget }}</p>
            <p class="text-right">
              <a href="{{ url('/clients/'.$client->id) }}" class="btn btn-default btn-sm">View</a>
              <a href="{{ url('/clients/'.$client->id.'/edit') }}" class="btn btn-info btn-sm">Edit</a>
              <button class="btn btn-danger btn-sm" onclick="event.preventDefault(); if(confirm('Delete this client?')) document.getElementById('delete-client-card-{{ $client->id }}').submit();">Delete</button>
            </p>
            <form id="delete-client-card-{{ $client->id }}" action="{{ url('/clients/'.$client->id) }}" method="POST" style="display:none;">
              @csrf
              @method('DELETE')
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-md-12">
        <div class="alert alert-info">No clients found yet.</div>
      </div>
    @endforelse
  </div>
</div>

<!-- TABLE -->
<div class="container">
  <h3>All Clients</h3>

  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Property</th>
        <th>Budget</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      @forelse ($clients as $client)
      <tr>
        <td>{{ $client->name }}</td>
        <td>{{ $client->email }}</td>
        <td>{{ $client->phone }}</td>
        <td>{{ $client->property_interest }}</td>
        <td>{{ $client->budget }}</td>
        <td><span class="label {{ strtolower($client->status) === 'active' ? 'label-success' : (strtolower($client->status) === 'inactive' ? 'label-danger' : 'label-warning') }}">{{ $client->status ?? 'Unknown' }}</span></td>
        <td>
          <a href="{{ url('/clients/'.$client->id) }}" class="btn btn-default btn-sm">View</a>
          <a href="{{ url('/clients/'.$client->id.'/edit') }}" class="btn btn-info btn-sm">Edit</a>
          <button class="btn btn-danger btn-sm" onclick="event.preventDefault(); if(confirm('Delete this client?')) document.getElementById('delete-client-row-{{ $client->id }}').submit();">Delete</button>
          <form id="delete-client-row-{{ $client->id }}" action="{{ url('/clients/'.$client->id) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="7" class="text-center">No clients found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="container text-center">
  {{ $clients->appends(request()->query())->links() }}
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
