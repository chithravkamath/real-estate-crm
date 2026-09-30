<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Site Visit</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>

<!-- HEADER -->
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

<nav class="navbar navbar-default">
  <div class="container">
    <ul class="nav navbar-nav">
      <li><a href="{{ url('/site-visits') }}">Site Visits</a></li>
      <li class="active"><a href="{{ url('/visit/create') }}">Schedule Visit</a></li>
    </ul>
  </div>
</nav>
<div class="container">
  @if($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- PAGE TITLE -->
  <div class="page-header">
    <h2>{{ isset($visit) ? 'Edit Site Visit' : 'Schedule Site Visit' }}</h2>
  </div>
<!-- FORM PANEL -->
<div class="panel panel-default">
  <div class="panel-body">

    <form action="{{ isset($visit) ? url('/site-visits/'.$visit->id) : url('/site-visits') }}" method="POST">
      @csrf
      @if(isset($visit))
        @method('PUT')
      @endif

  <div class="row">

    <!-- PROPERTY -->
    <div class="col-md-6 form-group">
      <label>Property</label>
      @php $selectedProperty = old('property', $visit->property_name ?? ''); @endphp
      <select class="form-control" name="property">
        <option value="">Select Property</option>
        @foreach($properties as $p)
          <option value="{{ $p->property_name }}" {{ $selectedProperty === $p->property_name ? 'selected' : '' }}>
              {{ $p->property_name }} ({{ $p->location }})
          </option>
        @endforeach
      </select>
    </div>

    <!-- CLIENT -->
    <div class="col-md-6 form-group">
      <label>Client</label>
      @php $selectedClientId = old('client_id', $visit->client_id ?? ''); @endphp
      <select class="form-control" name="client_id">
        <option value="">Select Client</option>
        @foreach($clients as $c)
          <option value="{{ $c->id }}" {{ (string)$selectedClientId === (string)$c->id ? 'selected' : '' }}>
              {{ $c->name }}
          </option>
        @endforeach
      </select>
    </div>

  </div>

  <div class="row">

    <!-- DATE -->
    <div class="col-md-6 form-group">
      <label>Date</label>
      <input type="date" class="form-control" name="date" min="{{ date('Y-m-d') }}" value="{{ old('date', (isset($visit) && $visit->visit_date) ? $visit->visit_date->format('Y-m-d') : '') }}">
    </div>

    <!-- TIME -->
    <div class="col-md-6 form-group">
      <label>Time</label>
      <input type="time" class="form-control" name="time" value="{{ old('time', $visit->visit_time ?? '') }}">
    </div>

  </div>

  <div class="row">

    <!-- AGENT -->
    <div class="col-md-6 form-group">
      <label>Assign Agent</label>
      @php $selectedAgentName = old('agent', $visit->agent_name ?? ''); @endphp
      <select class="form-control" name="agent">
        <option value="">Select Agent</option>
        @foreach($agents as $a)
          <option value="{{ $a->name }}" {{ $selectedAgentName === $a->name ? 'selected' : '' }}>
              {{ $a->name }}
          </option>
        @endforeach
      </select>
    </div>

    <!-- STATUS -->
    <div class="col-md-6 form-group">
      <label>Status</label>
      @php $status = old('status', $visit->status ?? 'Scheduled'); @endphp
      <select class="form-control" name="status">
        <option value="Scheduled" {{ $status === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
        <option value="Completed" {{ $status === 'Completed' ? 'selected' : '' }}>Completed</option>
        <option value="Cancelled" {{ $status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
      </select>
    </div>

  </div>

  <!-- NOTES -->
  <div class="form-group">
    <label>Notes</label>
    <textarea class="form-control" rows="3" name="notes"
      placeholder="Enter visit details (e.g., client requirements, budget ₹, feedback)">{{ old('notes', $visit->notes ?? '') }}</textarea>
  </div>

  <!-- BUTTONS -->
  <div class="text-right">
    <button type="reset" class="btn btn-default">Clear</button>
    <button type="submit" class="btn btn-primary">{{ isset($visit) ? 'Update Visit' : 'Save Visit' }}</button>
  </div>

</form>

  </div>
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
