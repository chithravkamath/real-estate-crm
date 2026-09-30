<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log Communication - CRM</title>

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
      <li class="active"><a href="{{ url('/communications/create') }}">Log Communication</a></li>
    </ul>
  </div>
</nav>

<!-- TITLE -->
<div class="container">
  <h2>Log Communication History</h2>
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

  <form action="{{ url('/communications/store') }}" method="POST">
  @csrf

  <!-- CONTACT DETAILS -->
  <h4><span class="glyphicon glyphicon-user"></span> Contact Association</h4>
  <hr>

  <div class="row">
    <!-- CONTACT TYPE -->
    <div class="col-md-6 form-group">
      <label>Contact Module Type <span class="text-danger">*</span></label>
      @php
        $defaultType = 'Lead';
        if (isset($preselected_client_id) || old('contact_type') === 'Client') {
            $defaultType = 'Client';
        }
      @endphp
      <select name="contact_type" id="contact_type" class="form-control" required>
        <option value="Lead" {{ $defaultType === 'Lead' ? 'selected' : '' }}>Lead</option>
        <option value="Client" {{ $defaultType === 'Client' ? 'selected' : '' }}>Client</option>
      </select>
    </div>

    <!-- RELATED CONTACT -->
    <div class="col-md-6 form-group">
      <label>Select Lead / Client <span class="text-danger">*</span></label>
      
      <!-- Leads Dropdown -->
      <select class="form-control contact-select" id="lead_id" name="lead_id">
        <option value="">-- Select Lead --</option>
        @foreach($leads as $lead)
          <option value="{{ $lead->id }}" {{ (isset($preselected_lead_id) && $preselected_lead_id == $lead->id) || old('lead_id') == $lead->id ? 'selected' : '' }}>
            {{ $lead->name }} ({{ $lead->email }})
          </option>
        @endforeach
      </select>

      <!-- Clients Dropdown -->
      <select class="form-control contact-select" id="client_id" name="client_id" style="display:none;">
        <option value="">-- Select Client --</option>
        @foreach($clients as $client)
          <option value="{{ $client->id }}" {{ (isset($preselected_client_id) && $preselected_client_id == $client->id) || old('client_id') == $client->id ? 'selected' : '' }}>
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
        <option value="Call" {{ old('communication_type') === 'Call' ? 'selected' : '' }}>Call</option>
        <option value="Email" {{ old('communication_type') === 'Email' ? 'selected' : '' }}>Email</option>
        <option value="Meeting" {{ old('communication_type') === 'Meeting' ? 'selected' : '' }}>Meeting</option>
        <option value="WhatsApp" {{ old('communication_type') === 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
      </select>
    </div>

    <!-- DATE -->
    <div class="col-md-6 form-group">
      <label>Date Conducted <span class="text-danger">*</span></label>
      <input type="date" name="communication_date" class="form-control" value="{{ old('communication_date', date('Y-m-d')) }}" required>
    </div>
  </div>

  <!-- NOTES -->
  <div class="form-group">
    <label>Summary / Discussion Notes </label>
    <textarea name="notes" class="form-control" rows="5" placeholder="Enter key points discussed, client feedback, follow-up actions...">{{ old('notes') }}</textarea>
  </div>

  <!-- BUTTONS -->
  <div class="text-right" style="margin-top: 20px;">
    <button type="reset" class="btn btn-default">Clear</button>
    
    @php
      $cancelUrl = url('/communications');
      if (isset($preselected_lead_id)) {
          $cancelUrl = url('/leads/'.$preselected_lead_id);
      } elseif (isset($preselected_client_id)) {
          $cancelUrl = url('/clients/'.$preselected_client_id);
      }
    @endphp
    <a href="{{ $cancelUrl }}" class="btn btn-warning">Cancel</a>
    
    <button type="submit" class="btn btn-primary">Save Log</button>
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
      $('#client_id').val('');
    } else if (type === 'Client') {
      $('#client_id').show().attr('required', 'required');
      $('#lead_id').val('');
    }
  }

  $('#contact_type').change(toggleContacts);
  toggleContacts(); // Run on load
});
</script>

</body>
</html>
