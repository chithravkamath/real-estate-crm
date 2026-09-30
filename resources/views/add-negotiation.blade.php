<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Negotiation Record - CRM</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>

<!-- TOP NAVBAR -->
<nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <p class="navbar-brand"><strong>Real Estate CRM</strong></p>
    </div>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="#">Welcome, {{ Auth::user()->name }}</a></li>
      <li><a href="{{ url('/') }}">Logout</a></li>
    </ul>
  </div>
</nav>

<!-- MAIN NAV -->
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
  <h2>Add Negotiation Record</h2>
  <h4>For Deal: {{ $deal->client_name }} - {{ $deal->property_name }}</h4>
  <hr>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ url('/deals/'.$deal->id.'/negotiations/store') }}" method="POST">
  @csrf

  <div class="row">
    <!-- OFFERED PRICE -->
    <div class="col-md-6 form-group">
      <label>Offered Price (₹) <span class="text-danger">*</span></label>
      <input type="text" name="offered_price" class="form-control" placeholder="e.g. 4800000" value="{{ old('offered_price') }}" required>
    </div>

    <!-- COUNTER OFFER -->
    <div class="col-md-6 form-group">
      <label>Counter Offer (₹)</label>
      <input type="text" name="counter_offer" class="form-control" placeholder="e.g. 4950000" value="{{ old('counter_offer') }}">
    </div>
  </div>

  <div class="row">
    <!-- NEGOTIATION DATE -->
    <div class="col-md-6 form-group">
      <label>Negotiation Date <span class="text-danger">*</span></label>
      <input type="date" name="negotiation_date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ old('negotiation_date', date('Y-m-d')) }}" required>
    </div>

    <!-- STATUS -->
    <div class="col-md-6 form-group">
      <label>Negotiation Status <span class="text-danger">*</span></label>
      <select name="status" class="form-control" required>
        <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
        <option value="Approved" {{ old('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
        <option value="Rejected" {{ old('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
        <option value="Countered" {{ old('status') === 'Countered' ? 'selected' : '' }}>Countered</option>
      </select>
    </div>
  </div>

  <!-- NOTES -->
  <div class="form-group">
    <label>Negotiation Discussion Notes</label>
    <textarea name="negotiation_note" class="form-control" rows="5" placeholder="Enter offer details, terms, client feedback, conditions discussed..." >{{ old('negotiation_note') }}</textarea>
  </div>

  <!-- BUTTONS -->
  <div class="text-right" style="margin-top: 20px;">
    <a href="{{ url('/deal-details/'.$deal->id) }}" class="btn btn-warning">Cancel</a>
    <button type="submit" class="btn btn-primary">Save Record</button>
  </div>

  </form>
</div>

<!-- FOOTER -->
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
