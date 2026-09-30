<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Audit Logs</title>

<link rel="stylesheet"
href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

</head>
<script>

/*setTimeout(function () {

    location.reload();

}, 5000);*/ 

</script>
<body>

<!-- TOP NAVBAR -->

<nav class="navbar navbar-default">

<div class="container">

<div class="navbar-header">

<p class="navbar-brand">
<strong>Real Estate CRM</strong>
</p>

</div>

<ul class="nav navbar-nav navbar-right">

<li>
<a href="#">
Welcome, {{ Auth::user()->name }}
</a>
</li>

<li>
<a href="{{ url('/') }}">
Logout
</a>
</li>

</ul>

</div>

</nav>

<!-- MAIN NAV -->

<nav class="navbar navbar-default">

<div class="container">

@include('partials.main-nav')

</div>

</nav>

<div class="container">

<h2>Audit Logs</h2>

<hr>

<!-- SEARCH + FILTER -->

<form method="GET" action="{{ url('/audit-logs') }}">

<div class="row">

<div class="col-md-8">

<input type="text"
       name="search"
       class="form-control input-sm"
       placeholder="Search logs..."
       value="{{ request('search') }}">

</div>

<div class="col-md-4">

<select name="module"
        class="form-control input-sm"
        onchange="this.form.submit()">

<option value="">All Modules</option>

<option value="Property" {{ request('module') === 'Property' ? 'selected' : '' }}>Property</option>

<option value="Client" {{ request('module') === 'Client' ? 'selected' : '' }}>Client</option>

<option value="Lead" {{ request('module') === 'Lead' ? 'selected' : '' }}>Lead</option>

<option value="Site Visit" {{ request('module') === 'Site Visit' ? 'selected' : '' }}>Site Visit</option>

<option value="Deal" {{ request('module') === 'Deal' ? 'selected' : '' }}>Deal</option>

<option value="Billing" {{ request('module') === 'Billing' ? 'selected' : '' }}>Billing</option>

<option value="User" {{ request('module') === 'User' ? 'selected' : '' }}>User</option>

</select>
</div>

</div>

</form>

<br>

<!-- TABLE -->

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>User</th>

<th>Action</th>

<th>Module</th>

<th>Description</th>

<th>Date & Time</th>

</tr>

</thead>

<tbody>

@forelse($logs as $log)

<tr>

<td>{{ $log->user_name }}</td>

<td>{{ $log->action }}</td>

<td>{{ $log->module }}</td>

<td>{{ $log->description }}</td>

<td>{{ $log->created_at->diffForHumans() }}</td>

</tr>

@empty

<tr>

<td colspan="5" class="text-center">

No logs found

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

{{ $logs->appends(request()->query())->links() }}

</div>
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

<p class="text-center">
&copy; 2026 Real Estate CRM
</p>

</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</body>
</html>
