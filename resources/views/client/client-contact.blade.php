<!DOCTYPE html>
<html>
<head>
<title>Contact Us</title>
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
<li><a href="#">Welcome, {{ auth()->user()->name }}</a></li>
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

<div class="container">
<h2>Contact Us</h2>
<hr>

<div class="panel panel-default">
<div class="panel-body">
<p><strong>Email:</strong> support@realestatecrm.com</p>
<p><strong>Phone:</strong> +91 9876543210</p>
<p><strong>Address:</strong> Bangalore, India</p>
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
