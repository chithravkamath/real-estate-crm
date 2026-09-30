<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Lead - Real Estate CRM</title>

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
      <li><a href="{{ url('/leads') }}">Leads</a></li>
      <li class="active"><a href="{{ url('/leads/create') }}">Add Lead</a></li>
    </ul>
  </div>
</nav>
<!-- TITLE -->
<div class="container">
  <div class="page-header">
    <h2>{{ isset($lead) ? 'Edit Lead' : 'Add New Lead' }}</h2>
  </div>
</div>
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
<form action="{{ isset($lead) ? url('/leads/'.$lead->id) : url('/leads') }}" method="POST">
  @csrf
  @if(isset($lead))
    @method('PUT')
  @endif

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Full Name *</label>
      <input type="text" name="name" class="form-control" value="{{ old('name', $lead->name ?? '') }}" required>
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label>Email *</label>
      <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email ?? '') }}" required>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Phone *</label>
      <input type="text" name="phone" class="form-control" value="{{ old('phone', $lead->phone ?? '') }}" required>
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label>Lead Source</label>
      @php $source = old('source', $lead->source ?? 'Website'); @endphp
      <select name="source" class="form-control">
        <option value="Website" {{ $source === 'Website' ? 'selected' : '' }}>Website</option>
        <option value="Call" {{ $source === 'Call' ? 'selected' : '' }}>Call</option>
        <option value="Walk-in" {{ $source === 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
        <option value="Referral" {{ $source === 'Referral' ? 'selected' : '' }}>Referral</option>
      </select>
    </div>
  </div>
</div>

<hr>

<h4>Lead Details</h4>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Interested Property</label>
      <input type="text" name="property" class="form-control" value="{{ old('property', $lead->interested_property ?? '') }}">
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label>Budget</label>
      <input type="text" name="budget" class="form-control" value="{{ old('budget', $lead->budget ?? '') }}">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Status *</label>
      @php $status = old('status', $lead->status ?? 'New'); @endphp
      <select name="status" class="form-control">
        <option value="New" {{ $status === 'New' ? 'selected' : '' }}>New</option>
        <option value="Contacted" {{ $status === 'Contacted' ? 'selected' : '' }}>Contacted</option>
        <option value="Qualified" {{ $status === 'Qualified' ? 'selected' : '' }}>Qualified</option>
        <option value="Closed" {{ $status === 'Closed' ? 'selected' : '' }}>Closed</option>
      </select>
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label>Assign Agent</label>
      <input type="text" name="agent" class="form-control" value="{{ old('agent', $lead->agent ?? '') }}">
    </div>
  </div>
</div>

<hr>

<h4>Follow-up</h4>

<div class="form-group">
  <label>Next Follow-up Date</label>
  <input type="date" name="follow_up_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('follow_up_date', isset($lead) ? optional($lead->follow_up_date)->format('Y-m-d') : '') }}">
</div>
<div class="form-group">
  <label>Notes</label>
  <textarea name="notes" class="form-control" rows="3">{{ old('notes', $lead->notes ?? '') }}</textarea>
</div>

<div class="text-right">
  <button type="reset" class="btn btn-default">Clear</button>
  <button type="submit" class="btn btn-primary">{{ isset($lead) ? 'Update Lead' : 'Add Lead' }}</button>
</div>

</form>

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
