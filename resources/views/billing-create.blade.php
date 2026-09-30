<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Invoice - CRM</title>

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
      <li><a href="{{ url('/billing') }}">Billing</a></li>
      <li class="active"><a href="{{ url('/billing/create') }}">Add Invoice</a></li>
    </ul>
  </div>
</nav>

<!-- TITLE -->
<div class="container">
  <h2>Add New Invoice</h2>
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

  <form action="{{ url('/billings') }}" method="POST">
    @csrf

    <div class="row">
      <div class="col-md-6 form-group">
        <label>Invoice Number</label>
        <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number') }}" required>
      </div>
      <div class="col-md-6 form-group">
        <label>Client</label>
        <select name="client_name" class="form-control" required>
          <option value="">-- Select Client --</option>
          @foreach($clients as $client)
            <option value="{{ $client->name }}" {{ old('client_name') === $client->name ? 'selected' : '' }}>{{ $client->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 form-group">
        <label>Property</label>
        <select name="property_name" class="form-control" required>
          <option value="">-- Select Property --</option>
          @foreach($properties as $property)
            <option value="{{ $property->property_name }}" {{ old('property_name') === $property->property_name ? 'selected' : '' }}>{{ $property->property_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6 form-group">
        <label>Agent</label>
        <select name="agent_name" class="form-control">
          <option value="">-- Select Agent --</option>
          @foreach($agents as $agent)
            <option value="{{ $agent->name }}" {{ old('agent_name') === $agent->name ? 'selected' : '' }}>{{ $agent->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 form-group">
        <label>Payment Amount</label>
        <input type="number" name="payment_amount" class="form-control" value="{{ old('payment_amount') }}" required>
      </div>
      <div class="col-md-4 form-group">
        <label>Commission</label>
        <input type="number" name="commission" class="form-control" value="{{ old('commission') }}" required>
      </div>
      <div class="col-md-4 form-group">
        <label>Payment Status</label>
        <select name="payment_status" class="form-control" required>
          <option value="Pending" {{ old('payment_status') === 'Pending' ? 'selected' : '' }}>Pending</option>
          <option value="Paid" {{ old('payment_status') === 'Paid' ? 'selected' : '' }}>Paid</option>
          <option value="Overdue" {{ old('payment_status') === 'Overdue' ? 'selected' : '' }}>Overdue</option>
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 form-group">
        <label>Payment Date</label>
        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ old('payment_date') }}" max="{{ date('Y-m-d') }}" required>
      </div>
      <div class="col-md-6 form-group">
        <label>Due Date</label>
        <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date') }}" required>
      </div>
    </div>

    <div class="form-group">
      <label>Notes</label>
      <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
    </div>

    <div class="text-right">
      <a href="{{ url('/billing') }}" class="btn btn-default">Cancel</a>
      <button type="submit" class="btn btn-primary">Add Invoice</button>
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
<script type="text/javascript">
$(document).ready(function() {
  $('#payment_date').change(function() {
      $('#due_date').attr('min', $(this).val());
  });
  if ($('#payment_date').val()) {
      $('#due_date').attr('min', $('#payment_date').val());
  }
});
</script>
</body>
</html>
