<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Deals</title>

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

<h2>Deals & Sales Management</h2>
<hr>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<form method="GET" action="{{ url('/deals') }}">
<div class="row" style="margin-bottom:20px;">
  <div class="col-md-6">
    <input type="text" class="form-control" placeholder="Search deals..." name="q" value="{{ request('q') }}">
  </div>
  <div class="col-md-3">
    <select class="form-control" name="status" onchange="this.form.submit()">
      <option value="">All Status</option>
      <option value="Negotiating" {{ request('status') === 'Negotiating' ? 'selected' : '' }}>Negotiating</option>
      <option value="Offer Made" {{ request('status') === 'Offer Made' ? 'selected' : '' }}>Offer Made</option>
      <option value="Accepted" {{ request('status') === 'Accepted' ? 'selected' : '' }}>Accepted</option>
      <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
    </select>
  </div>
  <div class="col-md-3">
    <a href="{{ url('/deals/create') }}" class="btn btn-primary btn-block">+ Add New Deal</a>
  </div>
</div>
</form>

<!-- STATS -->
<div class="row text-center">

<div class="col-md-3">
  <div class="panel panel-default">
    <div class="panel-body">
      <h2>{{ $totalDeals }}</h2>
      <p>Total Deals</p>
    </div>
  </div>
</div>

<div class="col-md-3">
  <div class="panel panel-default">
    <div class="panel-body">
      <h2>{{ $negotiatingDeals }}</h2>
      <p>Negotiating</p>
    </div>
  </div>
</div>

<div class="col-md-3">
  <div class="panel panel-default">
    <div class="panel-body">
      <h2>{{ $completedDeals }}</h2>
      <p>Completed</p>
    </div>
  </div>
</div>

<div class="col-md-3">
  <div class="panel panel-default">
    <div class="panel-body">
      <h2>₹{{ number_format($revenue, 0) }}</h2>
      <p>Revenue</p>
    </div>
  </div>
</div>

</div>

</div>

<!-- ACTIVE DEALS -->
<div class="container">
  <div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title">Active Deals</h3>
    </div>
    <div class="panel-body">
      <div class="row">
  @forelse($deals as $deal)
    @php
      $statusClass = 'label-default';
      $progress = 30;

      if ($deal->status === 'Negotiating') {
          $statusClass = 'label-warning';
          $progress = 40;
      }
      if ($deal->status === 'Offer Made') {
          $statusClass = 'label-info';
          $progress = 60;
      }
      if ($deal->status === 'Accepted') {
          $statusClass = 'label-success';
          $progress = 80;
      }
      if ($deal->status === 'Completed') {
          $statusClass = 'label-primary';
          $progress = 100;
      }

      $amount = floatval(preg_replace('/[^0-9\.]/', '', $deal->deal_amount));
    @endphp
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-body">

          <h4>
            <b>{{ $deal->property_name }}</b>
            <span class="pull-right text-success">{{ $amount ? '₹'.number_format($amount, 0) : 'N/A' }}</span>
          </h4>

          <p><b>Client:</b> {{ $deal->client_name }}</p>
          <p><b>Offer Made:</b> {{ optional($deal->booking_date)->format('Y-m-d') }}</p>
          <p><b>Status:</b> <span class="label {{ $statusClass }}">{{ $deal->status }}</span></p>

          <p><b>Progress:</b> {{ $progress }}%</p>
          <div class="progress">
            <div class="progress-bar {{ $deal->status === 'Completed' ? 'progress-bar-primary' : ($deal->status === 'Accepted' ? 'progress-bar-success' : ($deal->status === 'Offer Made' ? 'progress-bar-info' : 'progress-bar-warning')) }}" style="width:{{ $progress }}%"></div>
          </div>

        </div>
      </div>
    </div>
  @empty
    <div class="col-md-12">
      <div class="alert alert-info">No deals available.</div>
    </div>
  @endforelse
      </div>
    </div>
  </div>
</div>

<!-- TABLE -->
<div class="container">
  <h3>Deal Summary</h3>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">

    <thead>
<tr>
<th>Property</th>
<th>Client</th>
<th>Value</th>
<th>Commission %</th>
<th>Commission Amount</th>
<th>Status</th>
<th>Agent</th>
<th>Close Date</th>
<th>Actions</th>
</tr>
</thead>

<tbody>
@forelse($deals as $deal)
@php
    $labelClass = 'label-default';
    if ($deal->status === 'Negotiating') $labelClass = 'label-warning';
    if ($deal->status === 'Offer Made') $labelClass = 'label-info';
    if ($deal->status === 'Accepted') $labelClass = 'label-success';
    if ($deal->status === 'Completed') $labelClass = 'label-primary';
@endphp
<tr>
<td>{{ $deal->property_name }}</td>
<td>{{ $deal->client_name }}</td>
<td>{{ $deal->deal_amount }}</td>
<td>{{ $deal->commission_percentage ? (float)$deal->commission_percentage . '%' : 'N/A' }}</td>
<td>{{ $deal->commission_amount ? '₹' . number_format($deal->commission_amount, 0) : 'N/A' }}</td>
<td><span class="label {{ $labelClass }}">{{ $deal->status }}</span></td>
<td>{{ $deal->agent_name ?? 'N/A' }}</td>
<td>{{ $deal->booking_date ? $deal->booking_date->format('Y-m-d') : 'N/A' }}</td>
<td>
<a href="{{ url('/deal-details/'.$deal->id) }}" class="btn btn-default btn-sm">View</a>
<a href="{{ url('/deal-update/'.$deal->id) }}" class="btn btn-info btn-sm">Update</a>
<form action="{{ url('/deals/'.$deal->id) }}" method="POST" style="display:inline; margin:0; padding:0;">
@csrf
@method('DELETE')
<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this deal?')">Delete</button>
</form>
</td>
</tr>
@empty
<tr>
<td colspan="9" class="text-center">No deals found.</td>
</tr>
@endforelse
</tbody>
  </table>
</div>
</div>

<div class="container text-center">
  {{ $deals->appends(request()->query())->links() }}
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
