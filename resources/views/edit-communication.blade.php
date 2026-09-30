<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Logged Communication - CRM</title>

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
      <li><a href="{{ url('/communications') }}">Communications</a></li>
      <li class="active"><a href="#">Edit Communication</a></li>
    </ul>
  </div>
</nav>

<!-- TITLE -->
<div class="container">
  <h2>Edit Logged Communication</h2>
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

  <form action="{{ url('/communications/'.$communication->id.'/update') }}" method="POST">
  @csrf

  <!-- CONTACT DETAILS -->
  <h4><span class="glyphicon glyphicon-user"></span> Contact Association</h4>
  <hr>

  <div class="row">
    <!-- CONTACT TYPE -->
    <div class="col-md-6 form-group">
      <label>Contact Module Type <span class="text-danger">*</span></label>
      @php
        $selectedType = $communication->lead_id ? 'Lead' : 'Client';
        if (old('contact_type')) {
            $selectedType = old('contact_type');
        }
      @endphp
      <select name="contact_type" id="contact_type" class="form-control" required>
        <option value="Lead" {{ $selectedType === 'Lead' ? 'selected' : '' }}>Lead</option>
        <option value="Client" {{ $selectedType === 'Client' ? 'selected' : '' }}>Client</option>
      </select>
    </div>

    <!-- RELATED CONTACT -->
    <div class="col-md-6 form-group">
      <label>Select Lead / Client <span class="text-danger">*</span></label>
      
      <!-- Leads Dropdown -->
      <select class="form-control contact-select" id="lead_id" name="lead_id">
        <option value="">-- Select Lead --</option>
        @foreach($leads as $lead)
          <option value="{{ $lead->id }}" {{ old('lead_id', $communication->lead_id) == $lead->id ? 'selected' : '' }}>
            {{ $lead->name }} ({{ $lead->email }})
          </option>
        @endforeach
      </select>

      <!-- Clients Dropdown -->
      <select class="form-control contact-select" id="client_id" name="client_id" style="display:none;">
        <option value="">-- Select Client --</option>
        @foreach($clients as $client)
          <option value="{{ $client->id }}" {{ old('client_id', $communication->client_id) == $client->id ? 'selected' : '' }}>
            {{ $client->name }} ({{ $client->email }})
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <!-- COMMUNICATION DETAILS -->
  <h4><span class="glyphicon glyphicon-earphones"></span> Log Details</h4>
  <hr>

  <div class="row">
    <!-- TYPE -->
    <div class="col-md-6 form-group">
      <label>Communication Type <span class="text-danger">*</span></label>
      <select name="communication_type" class="form-control" required>
        <option value="Call" {{ old('communication_type', $communication->communication_type) === 'Call' ? 'selected' : '' }}>Call</option>
        <option value="Email" {{ old('communication_type', $communication->communication_type) === 'Email' ? 'selected' : '' }}>Email</option>
        <option value="Meeting" {{ old('communication_type', $communication->communication_type) === 'Meeting' ? 'selected' : '' }}>Meeting</option>
        <option value="WhatsApp" {{ old('communication_type', $communication->communication_type) === 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
      </select>
    </div>

    <!-- DATE -->
    <div class="col-md-6 form-group">
      <label>Date Conducted <span class="text-danger">*</span></label>
      <input type="date" name="communication_date" class="form-control" value="{{ old('communication_date', $communication->communication_date) }}" required>
    </div>
  </div>

  <!-- NOTES -->
  <div class="form-group">
    <label>Summary / Discussion Notes <span class="text-danger">*</span></label>
    <textarea name="notes" class="form-control" rows="5" placeholder="Enter key points discussed, client feedback, follow-up actions..." required>{{ old('notes', $communication->notes) }}</textarea>
  </div>

  <!-- BUTTONS -->
  <div class="text-right" style="margin-top: 20px;">
    <a href="{{ url('/communications') }}" class="btn btn-warning">Cancel</a>
    <button type="submit" class="btn btn-primary">Update Log</button>
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
  function toggleContacts() {
    var type = $('#contact_type').val();
    $('.contact-select').hide().removeAttr('required');
    
    if (type === 'Lead') {
      $('#lead_id').show().attr('required', 'required');
    } else if (type === 'Client') {
      $('#client_id').show().attr('required', 'required');
    }
  }

  $('#contact_type').change(function() {
    // Reset specific selects on type switch
    $('.contact-select').val('');
    toggleContacts();
  });
  
  toggleContacts(); // Run on load
  
  // Set initial selected values based on DB values
  var initialType = '{{ $selectedType }}';
  if (initialType === 'Lead') {
    $('#lead_id').val('{{ $communication->lead_id }}');
  } else if (initialType === 'Client') {
    $('#client_id').val('{{ $communication->client_id }}');
  }
  toggleContacts();
});
</script>

</body>
</html>
