<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Expense - CRM</title>

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
      <li class="active"><a href="{{ url('/billing/expenses/create') }}">Add Expense</a></li>
    </ul>
  </div>
</nav>

<!-- TITLE -->
<div class="container">
  <h2>Add New Expense</h2>
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

  <form action="{{ url('/billing/expenses/store') }}" method="POST">
    @csrf

    <div class="row">
      <!-- TITLE -->
      <div class="col-md-6 form-group">
        <label>Expense Title <span class="text-danger">*</span></label>
        <input type="text" name="expense_title" class="form-control" placeholder="e.g. Facebook Ads Campaign" value="{{ old('expense_title') }}" required>
      </div>

      <!-- CATEGORY -->
      <div class="col-md-6 form-group">
        <label>Expense Category <span class="text-danger">*</span></label>
        <select name="expense_category" class="form-control" required>
          <option value="Marketing" {{ old('expense_category') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
          <option value="Travel" {{ old('expense_category') === 'Travel' ? 'selected' : '' }}>Travel</option>
          <option value="Maintenance" {{ old('expense_category') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
          <option value="Office" {{ old('expense_category') === 'Office' ? 'selected' : '' }}>Office</option>
          <option value="Miscellaneous" {{ old('expense_category') === 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
        </select>
      </div>
    </div>

    <div class="row">
      <!-- AMOUNT -->
      <div class="col-md-6 form-group">
        <label>Amount (₹) <span class="text-danger">*</span></label>
        <input type="number" name="amount" class="form-control" placeholder="e.g. 15000" min="0" step="0.01" value="{{ old('amount') }}" required>
      </div>

      <!-- DATE -->
      <div class="col-md-6 form-group">
        <label>Expense Date <span class="text-danger">*</span></label>
        <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
      </div>
    </div>

    <!-- NOTES -->
    <div class="form-group">
      <label>Notes</label>
      <textarea name="notes" class="form-control" rows="4" placeholder="Enter details, reference or invoice receipt details...">{{ old('notes') }}</textarea>
    </div>

    <!-- BUTTONS -->
    <div class="text-right">
      <a href="{{ url('/billing') }}" class="btn btn-default">Cancel</a>
      <button type="submit" class="btn btn-primary">Add Expense</button>
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
