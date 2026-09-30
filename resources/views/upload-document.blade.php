<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Document - CRM</title>

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
  <h2>Upload Document & Agreement</h2>
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

  <form action="{{ url('/documents/store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <!-- DOCUMENT METADATA -->
  <h4><span class="glyphicon glyphicon-file"></span> Document Details</h4>
  <hr>

  <div class="row">
    <!-- DOCUMENT NAME -->
    <div class="col-md-6 form-group">
      <label>Document Name / Label <span class="text-danger">*</span></label>
      <input type="text" name="document_name" class="form-control" placeholder="e.g. Booking Agreement Draft" value="{{ old('document_name') }}" required>
    </div>

    <!-- DOCUMENT TYPE -->
    <div class="col-md-6 form-group">
      <label>Document Type <span class="text-danger">*</span></label>
      <select name="document_type" class="form-control" required>
        <option value="agreement document" {{ old('document_type') === 'agreement document' ? 'selected' : '' }}>Agreement Document</option>
        <option value="booking confirmation" {{ old('document_type') === 'booking confirmation' ? 'selected' : '' }}>Booking Confirmation</option>
        <option value="sale document" {{ old('document_type') === 'sale document' ? 'selected' : '' }}>Sale Document</option>
        <option value="invoice" {{ old('document_type') === 'invoice' ? 'selected' : '' }}>Invoice</option>
        <option value="ID proof" {{ old('document_type') === 'ID proof' ? 'selected' : '' }}>ID Proof</option>
      </select>
    </div>
  </div>

  <div class="row">
    <!-- FILE INPUT -->
    <div class="col-md-12 form-group">
      <label>Select Document File <span class="text-danger">*</span> (Allowed types: PDF, DOCX, JPG, PNG | Max: 10MB)</label>
      <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
    </div>
  </div>

  <!-- CONTACT / ASSOCIATION -->
  <h4 style="margin-top: 30px;"><span class="glyphicon glyphicon-link"></span> Link Association</h4>
  <hr>

  <div class="row">
    <!-- ASSOCIATION TYPE -->
    <div class="col-md-6 form-group">
      <label>Associate Document With <span class="text-danger">*</span></label>
      @php
        $defaultAssoc = 'Deal';
        if (isset($preselected_property_id) || old('assoc_type') === 'Property') {
            $defaultAssoc = 'Property';
        }
      @endphp
      <select name="assoc_type" id="assoc_type" class="form-control" required>
        <option value="Deal" {{ $defaultAssoc === 'Deal' ? 'selected' : '' }}>Deal / Sales</option>
        <option value="Property" {{ $defaultAssoc === 'Property' ? 'selected' : '' }}>Property</option>
      </select>
    </div>

    <!-- RELATED ITEM -->
    <div class="col-md-6 form-group">
      <label>Select Related Record <span class="text-danger">*</span></label>
      
      <!-- Deals dropdown -->
      <select class="form-control assoc-select" id="deal_id" name="deal_id">
        <option value="">-- Select Deal --</option>
        @foreach($deals as $dealItem)
          <option value="{{ $dealItem->id }}" {{ (isset($preselected_deal_id) && $preselected_deal_id == $dealItem->id) || old('deal_id') == $dealItem->id ? 'selected' : '' }}>
            {{ $dealItem->client_name }} - {{ $dealItem->property_name }} (₹{{ number_format(floatval($dealItem->deal_amount), 0) }})
          </option>
        @endforeach
      </select>

      <!-- Properties dropdown -->
      <select class="form-control assoc-select" id="property_id" name="property_id" style="display:none;">
        <option value="">-- Select Property --</option>
        @foreach($properties as $propertyItem)
          <option value="{{ $propertyItem->id }}" {{ (isset($preselected_property_id) && $preselected_property_id == $propertyItem->id) || old('property_id') == $propertyItem->id ? 'selected' : '' }}>
            {{ $propertyItem->property_name }} - {{ $propertyItem->location }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <!-- BUTTONS -->
  <div class="text-right" style="margin-top: 30px;">
    @php
      $cancelUrl = url('/dashboard');
      if (isset($preselected_deal_id)) {
          $cancelUrl = url('/deal-details/'.$preselected_deal_id);
      } elseif (isset($preselected_property_id)) {
          $cancelUrl = url('/properties/'.$preselected_property_id);
      }
    @endphp
    <a href="{{ $cancelUrl }}" class="btn btn-warning">Cancel</a>
    <button type="submit" class="btn btn-primary">Upload Document</button>
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
  function toggleAssociations() {
    var type = $('#assoc_type').val();
    $('.assoc-select').hide().removeAttr('required');
    
    if (type === 'Deal') {
      $('#deal_id').show().attr('required', 'required');
      $('#property_id').val('');
    } else if (type === 'Property') {
      $('#property_id').show().attr('required', 'required');
      $('#deal_id').val('');
    }
  }

  $('#assoc_type').change(toggleAssociations);
  toggleAssociations(); // Run on load
});
</script>

</body>
</html>
