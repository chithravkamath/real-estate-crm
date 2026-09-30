<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Client Dashboard - Real Estate CRM</title>

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
  <h2>Client Dashboard</h2>
  <hr>
</div>

<div class="container">

  <!-- CARDS -->
  <div class="row text-center">

    <div class="col-md-4">
      <div class="panel panel-primary">
        <div class="panel-body">
          <h1><span class="glyphicon glyphicon-home"></span></h1>
          <h2>{{ $propertiesViewed }}</h2>
          <p>Properties Viewed</p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="panel panel-success">
        <div class="panel-body">
          <h1><span class="glyphicon glyphicon-calendar"></span></h1>
          <h2>{{ $myBookings }}</h2>
          <p>My Bookings</p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="panel panel-warning">
        <div class="panel-body">
          <h1><span class="glyphicon glyphicon-credit-card"></span></h1>
          <h2>{{ $pendingPayments }}</h2>

          <p>Pending Payments</p>
        </div>
      </div>
    </div>

  </div>

  <!-- QUICK ACTIONS -->
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">Quick Actions</div>
        <div class="panel-body text-center">
          <a href="{{ url('/client/properties') }}" class="btn btn-primary">View Properties</a>
          <a href="{{ url('/client/bookings') }}" class="btn btn-success">My Bookings</a>
          <a href="{{ url('/client/payments') }}" class="btn btn-warning">View Payments</a>
        </div>
      </div>
    </div>
  </div>

<!-- RECENT ACTIVITY -->
<div class="row">

  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Recent Activity</div>
      <div class="list-group">
        @forelse($recentActivity as $activity)
          <a class="list-group-item">{{ $activity['message'] }}</a>
        @empty
          <div class="list-group-item">No recent activity yet.</div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- BOOKING STATUS -->
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">My Recent Bookings</div>
      <div class="panel-body">

        <table class="table table-bordered">
          <tr>
            <th>Property</th>
            <th>Status</th>
          </tr>
          @forelse($bookings as $booking)
            <tr>
              <td>{{ $booking->property_name }}</td>
              <td><span class="label {{ strtolower($booking->status) === 'booked' ? 'label-success' : (strtolower($booking->status) === 'pending' ? 'label-warning' : 'label-default') }}">{{ $booking->status }}</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="2" class="text-center">No bookings found.</td>
            </tr>
          @endforelse
        </table>

      </div>
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
