<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Updates - Real Estate CRM</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>

<!-- TOP NAV -->
<nav class="navbar navbar-default">
<div class="container">
<div class="navbar-header">
<a class="navbar-brand"><strong>Real Estate CRM</strong></a>
</div>
<ul class="nav navbar-nav navbar-right">
<li><a href="#">Welcome, {{ Auth::user()->name }}</a></li>
<li>
  <form method="POST" action="{{ route('logout') }}" style="display:inline;">
    @csrf
    <button type="submit" class="btn btn-link navbar-btn">Logout</button>
  </form>
</li>
</ul>
</div>
</nav>

<!-- MAIN NAV -->
<nav class="navbar navbar-default">
<div class="container">
<ul class="nav navbar-nav">
<li class="{{ request()->is('client/dashboard*') ? 'active' : '' }}"><a href="{{ url('/client/dashboard') }}">Dashboard</a></li>
<li class="{{ (request()->is('client/properties*') || request()->is('client/property-details*')) ? 'active' : '' }}"><a href="{{ url('/client/properties') }}">Properties</a></li>
<li class="{{ request()->is('client/bookings*') ? 'active' : '' }}"><a href="{{ url('/client/bookings') }}">My Bookings</a></li>
<li class="{{ request()->is('client/payments*') ? 'active' : '' }}"><a href="{{ url('/client/payments') }}">Payments</a></li>
<li class="{{ request()->is('client/updates*') ? 'active' : '' }}"><a href="{{ url('/client/updates') }}">Updates</a></li>
@if(request()->is('client/about*'))
<li class="active"><a href="{{ url('/client/about') }}">About Us</a></li>
@endif
@if(request()->is('client/contact*'))
<li class="active"><a href="{{ url('/client/contact') }}">Contact Us</a></li>
@endif
@if(request()->is('client/privacy*'))
<li class="active"><a href="{{ url('/client/privacy') }}">Privacy Policy</a></li>
@endif
@if(request()->is('client/support*'))
<li class="active"><a href="{{ url('/client/support') }}">Support</a></li>
@endif
</ul>
</div>
</nav>

<!-- TITLE -->
<div class="container">
  <h2>Updates</h2>
  <hr>
</div>

<div class="container">

  <div class="panel panel-default">
    <div class="panel-heading">
      <strong>Recent Updates</strong>
    </div>

    <div class="panel-body">

      <table class="table table-bordered table-hover text-center">
  <thead>
    <tr style="background-color: #f9f9f9;">
      <th style="text-align: center;">Date</th>
      <th style="text-align: center;">Message</th>
      <th style="text-align: center;">Type</th>
    </tr>
  </thead>

  <tbody>
    @forelse($updates as $update)
      <tr>
        <td>{{ $update['date']->format('Y-m-d') }}</td>
        <td style="text-align: left;">{{ $update['message'] }}</td>
        <td><span class="label {{ $update['label_class'] }}">{{ $update['type'] }}</span></td>
      </tr>
    @empty
      <tr>
        <td colspan="3">No recent updates found for your account.</td>
      </tr>
    @endforelse
  </tbody>
</table>

    </div>
  </div>

</div>

<!-- FOOTER -->
<footer class="container">
  <hr>
  <div class="row">

    <div class="col-md-4">
      <h4>Company Information</h4>
      <ul class="list-unstyled">
        <li><a href="{{ url('/client/about') }}">About Us</a></li>
        <li><a href="{{ url('/client/contact') }}">Contact Us</a></li>
        <li><a href="{{ url('/client/privacy') }}">Privacy Policy</a></li>
      </ul>
    </div>

    <div class="col-md-4">
      <h4>Client Modules</h4>
      <ul class="list-unstyled">
        <li><li><a href="{{ url('/client/properties') }}">Properties</a></li>
        <li><a href="{{ url('/client/bookings') }}">My Bookings</a></li>
        <li><a href="{{ url('/client/payments') }}">Payments</a></li>
      </ul>
    </div>

    <div class="col-md-4">
      <h4>Quick Links</h4>
      <ul class="list-unstyled">
        <li><a href="{{ url('/client/dashboard') }}">Dashboard</a></li>
        <li><a href="{{ url('/client/support') }}">Support</a></li>
      </ul>
    </div>

  </div>

  <p class="text-center">&copy; 2026 Real Estate CRM</p>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</body>
</html>
