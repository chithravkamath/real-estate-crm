<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Deal - CRM</title>

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

<!-- SECOND NAVBAR -->
<nav class="navbar navbar-default">
  <div class="container">
    <ul class="nav navbar-nav">
      <li><a href="{{ url('/deals') }}">Deals</a></li>
      <li class="active"><a href="{{ url('/deals/create') }}">Add Deal</a></li>
    </ul>
  </div>
</nav>

<!-- TITLE -->
<div class="container">
  <h2>Add New Deal</h2>
  <hr>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ url('/deals/store') }}" method="POST">
  @csrf

  <div class="row">
    <div class="col-md-6 form-group">
      <label>Property</label>
      @php $selectedProperty = old('property_name'); @endphp
      <select name="property_name" class="form-control" required>
        <option value="">-- Select Property --</option>
        @foreach($properties as $property)
          <option value="{{ $property->property_name }}" {{ $selectedProperty === $property->property_name ? 'selected' : '' }}>{{ $property->property_name }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-md-6 form-group">
      <label>Client</label>
      @php $selectedClient = old('client_id'); @endphp
      <select name="client_id" class="form-control" required>
        <option value="">-- Select Client --</option>
        @foreach($clients as $client)
          <option value="{{ $client->id }}" {{ (string)$selectedClient === (string)$client->id ? 'selected' : '' }}>{{ $client->name }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 form-group">
      <label>Agent</label>
      @php $selectedAgent = old('agent_name'); @endphp
      <select name="agent_name" class="form-control">
        <option value="">-- Select Agent --</option>
        @foreach($agents as $agent)
          <option value="{{ $agent->name }}" {{ $selectedAgent === $agent->name ? 'selected' : '' }}>{{ $agent->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-md-6 form-group">
      <label>Deal Value</label>
      <input type="number" name="deal_amount" class="form-control" value="{{ old('deal_amount') }}" required>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 form-group">
      <label>Commission Percentage (%)</label>
      <input type="number" step="0.01" name="commission_percentage" class="form-control" value="{{ old('commission_percentage') }}" min="0" max="100">
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 form-group">
      <label>Status</label>
      @php $selectedStatus = old('status'); @endphp
      <select name="status" class="form-control" required>
        <option value="Negotiating" {{ $selectedStatus === 'Negotiating' ? 'selected' : '' }}>Negotiating</option>
        <option value="Offer Made" {{ $selectedStatus === 'Offer Made' ? 'selected' : '' }}>Offer Made</option>
        <option value="Accepted" {{ $selectedStatus === 'Accepted' ? 'selected' : '' }}>Accepted</option>
        <option value="Completed" {{ $selectedStatus === 'Completed' ? 'selected' : '' }}>Completed</option>
      </select>
    </div>

    <div class="col-md-6 form-group">
      <label>Close Date</label>
      <input type="date" name="booking_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('booking_date') }}" required>
    </div>
  </div>

  <div class="text-right">
    <button type="reset" class="btn btn-default">Clear</button>
    <button type="submit" class="btn btn-primary">Add Deal</button>
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
