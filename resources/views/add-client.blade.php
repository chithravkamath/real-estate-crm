<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Client - CRM</title>

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
      <li><a href="{{ url('/clients') }}">Clients</a></li>
      <li class="active"><a href="{{ url('/clients/create') }}">Add Client</a></li>
    </ul>
  </div>
</nav>

<!-- TITLE -->
<div class="container">
  <h2>Add New Client</h2>
  <hr>

  @php $isEdit = isset($client); @endphp

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ $isEdit ? url('/clients/'.$client->id.'/update') : url('/clients/store') }}" method="POST">
  @csrf

  <!-- BASIC INFO -->
  <h4><span class="glyphicon glyphicon-user"></span> Client Information</h4>
  <hr>

  <div class="row">
    <div class="col-md-6 form-group">
      <label>Full Name</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $client->name ?? '') }}" required>
    </div>

    <div class="col-md-6 form-group">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email', $client->email ?? '') }}" required>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 form-group">
      <label>Phone</label>
      <input type="text" name="phone" class="form-control" value="{{ old('phone', $client->phone ?? '') }}" required>
    </div>

    <div class="col-md-6 form-group">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="Active" {{ old('status', $client->status ?? '') === 'Active' ? 'selected' : '' }}>Active</option>
        <option value="Inactive" {{ old('status', $client->status ?? '') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
        <option value="VIP" {{ old('status', $client->status ?? '') === 'VIP' ? 'selected' : '' }}>VIP</option>
      </select>
    </div>
  </div>

  <!-- PROPERTY INTEREST -->
  <h4><span class="glyphicon glyphicon-home"></span> Property Interest</h4>
  <hr>

  <div class="row">
    <div class="col-md-6 form-group">
      <label>Interested Property</label>
      <input type="text" name="property" class="form-control" value="{{ old('property', $client->property_interest ?? '') }}">
    </div>

    <div class="col-md-6 form-group">
      <label>Budget Range</label>
      <input type="text" name="budget" class="form-control" value="{{ old('budget', $client->budget ?? '') }}">
    </div>
  </div>

  <!-- NOTES -->
  <h4><span class="glyphicon glyphicon-pencil"></span> Notes</h4>
  <hr>

  <div class="form-group">
    <label>Additional Notes</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $client->notes ?? '') }}</textarea>
  </div>

  <!-- BUTTONS -->
  <div class="text-right">
    <button type="reset" class="btn btn-default">Clear</button>
    <button type="submit" class="btn btn-primary">
      {{ $isEdit ? 'Update Client' : 'Add Client' }}
    </button>
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