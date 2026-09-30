<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments - Real Estate CRM</title>

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

<!-- TITLE -->
<div class="container">
  <h2>Payments</h2>
  <hr>
</div>

<div class="container">

  <div class="panel panel-default">
    <div class="panel-heading">
      <strong>Payment History</strong>
    </div>

    <div class="panel-body">

      <table class="table table-bordered table-hover text-center">
        <thead>
          <tr>
            <th>Property</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Invoice</th>
          </tr>
        </thead>
<tbody>

  @forelse($payments as $payment)
  <tr>
    <td>{{ $payment->property_name ?? 'N/A' }} / {{ $payment->invoice_number }}</td>
    <td>Rs. {{ number_format(floatval($payment->payment_amount), 2) }}</td>
    <td><span class="label {{ strtolower($payment->payment_status) === 'paid' ? 'label-success' : (strtolower($payment->payment_status) === 'pending' ? 'label-warning' : 'label-danger') }}">{{ $payment->payment_status }}</span></td>
    <td>
      <a href="{{ url('/client/payments/'.$payment->id.'/download') }}" class="btn btn-primary btn-sm">
        <span class="glyphicon glyphicon-download"></span> Download
      </a>
    </td>
  </tr>
  @empty
  <tr>
    <td colspan="4" class="text-center">No payments recorded.</td>
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
