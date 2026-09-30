<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Property Details - Real Estate CRM</title>

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
  <h2>Property Details</h2>
  <hr>

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

  <div class="panel panel-default">
    <div class="panel-body">

      <div class="row">

        <!-- IMAGE -->
        <div class="col-md-6">
          <img src="{{ $property->image ? asset('property-images/' . $property->image) : 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80' }}" class="img-responsive img-thumbnail" style="width: 100%; max-height: 400px; object-fit: cover;" alt="Property">
        </div>

        <!-- DETAILS -->
        <div class="col-md-6">

          <h3>{{ $property->property_name ?? 'Property Details' }}</h3>
          <hr>

          <p><strong>Price:</strong> <span class="text-primary">{{ $property->price ? '₹'.number_format($property->price, 0) : 'N/A' }}</span></p>
          <p><strong>Location:</strong> {{ $property->location ?? 'N/A' }}</p>
          <p><strong>Type:</strong> {{ $property->property_type ?? 'N/A' }}</p>
          <p><strong>Status:</strong> <span class="label {{ strtolower($property->status) === 'available' ? 'label-success' : (strtolower($property->status) === 'booked' ? 'label-warning' : 'label-danger') }}">{{ $property->status ?? 'Unknown' }}</span></p>

          <br>

          <div class="row">
            @php
              $status = strtolower($property->status ?? 'available');
            @endphp

            @if($status === 'available')
              <div class="col-md-6">
                <form method="POST" action="{{ url('/client/request-visit') }}" style="margin: 0; padding: 0;">
                  @csrf
                  <input type="hidden" name="property_id" value="{{ $property->id }}">
                  <button type="submit" class="btn btn-success btn-block">
                    <span class="glyphicon glyphicon-calendar"></span> Request Visit
                  </button>
                </form>
              </div>
              <div class="col-md-6">
                <form method="POST" action="{{ url('/client/interested-property') }}" style="margin: 0; padding: 0;">
                  @csrf
                  <input type="hidden" name="property_id" value="{{ $property->id }}">
                  <button type="submit" class="btn btn-info btn-block">
                    <span class="glyphicon glyphicon-heart"></span> Interested
                  </button>
                </form>
              </div>
            @elseif($status === 'booked')
              <div class="col-md-6">
                <button class="btn btn-success btn-block" disabled title="Currently Booked" style="opacity: 0.65; cursor: not-allowed;">
                  <span class="glyphicon glyphicon-calendar"></span> Request Visit
                </button>
                <div class="text-center" style="margin-top: 5px;">
                  <span class="label label-warning">Currently Booked</span>
                </div>
              </div>
              <div class="col-md-6">
                <form method="POST" action="{{ url('/client/interested-property') }}" style="margin: 0; padding: 0;">
                  @csrf
                  <input type="hidden" name="property_id" value="{{ $property->id }}">
                  <button type="submit" class="btn btn-info btn-block">
                    <span class="glyphicon glyphicon-heart"></span> Interested
                  </button>
                </form>
              </div>
            @elseif($status === 'sold')
              <div class="col-md-6">
                <button class="btn btn-success btn-block" disabled title="Property Sold" style="opacity: 0.65; cursor: not-allowed;">
                  <span class="glyphicon glyphicon-calendar"></span> Request Visit
                </button>
                <div class="text-center" style="margin-top: 5px;">
                  <span class="label label-danger">Property Sold</span>
                </div>
              </div>
              <div class="col-md-6">
                <button class="btn btn-info btn-block" disabled title="Property Sold" style="opacity: 0.65; cursor: not-allowed;">
                  <span class="glyphicon glyphicon-heart"></span> Interested
                </button>
              </div>
            @endif
          </div>

        </div>

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
        <li><a href="{{ url('/client/properties') }}">Properties</a></li>
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
