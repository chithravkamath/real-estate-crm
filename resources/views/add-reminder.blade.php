<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ isset($reminder) ? 'Edit Reminder - CRM' : 'Add Reminder - CRM' }}</title>

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
      <li><a href="{{ url('/reminders') }}">Reminders</a></li>
      <li class="{{ !isset($reminder) ? 'active' : '' }}"><a href="{{ url('/reminders/create') }}">Add Reminder</a></li>
      @if(isset($reminder))
        <li class="active"><a href="#">Edit Reminder</a></li>
      @endif
    </ul>
  </div>
</nav>

<!-- TITLE -->
<div class="container">
  <h2>{{ isset($reminder) ? 'Edit CRM Reminder' : 'Add New CRM Reminder' }}</h2>
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

  @php $isEdit = isset($reminder); @endphp

  <form action="{{ $isEdit ? url('/reminders/'.$reminder->id.'/update') : url('/reminders/store') }}" method="POST">
  @csrf

  <!-- REMINDER DETAIL -->
  <h4><span class="glyphicon glyphicon-bell"></span> Reminder Information</h4>
  <hr>

  <div class="row">
    <!-- TITLE -->
    <div class="col-md-12 form-group">
      <label>Reminder Title <span class="text-danger">*</span></label>
      <input type="text" name="title" class="form-control" placeholder="e.g. Lead Follow-up Call, Site Visit Reminder" value="{{ old('title', $reminder->title ?? '') }}" required>
    </div>
  </div>

  <div class="row">
    <!-- DATE -->
    <div class="col-md-6 form-group">
      <label>Reminder Date <span class="text-danger">*</span></label>
      <input type="date" name="reminder_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('reminder_date', isset($reminder) ? $reminder->reminder_date : '') }}" required>
    </div>

    <!-- TIME -->
    <div class="col-md-6 form-group">
      <label>Reminder Time <span class="text-danger">*</span></label>
      <input type="time" name="reminder_time" class="form-control" value="{{ old('reminder_time', isset($reminder) ? $reminder->reminder_time : '') }}" required>
    </div>
  </div>

  <!-- MODULE INTEGRATION -->
  <h4><span class="glyphicon glyphicon-link"></span> Related CRM Module</h4>
  <hr>

  <div class="row">
    <!-- RELATED TYPE -->
    <div class="col-md-6 form-group">
      <label>Related Module Type <span class="text-danger">*</span></label>
      @php
        $defaultType = '';
        if (isset($preselected_lead_id) || old('related_type') === 'Lead') {
            $defaultType = 'Lead';
        } elseif (isset($preselected_client_id) || old('related_type') === 'Client') {
            $defaultType = 'Client';
        }
      @endphp
      <select name="related_type" id="related_type" class="form-control" required>
        <option value="">-- Select Module Type --</option>
        <option value="Lead" {{ old('related_type', $reminder->related_type ?? '') === 'Lead' || $defaultType === 'Lead' ? 'selected' : '' }}>Lead</option>
        <option value="Client" {{ old('related_type', $reminder->related_type ?? '') === 'Client' || $defaultType === 'Client' ? 'selected' : '' }}>Client</option>
        <option value="Site Visit" {{ old('related_type', $reminder->related_type ?? '') === 'Site Visit' ? 'selected' : '' }}>Site Visit</option>
        <option value="Deal" {{ old('related_type', $reminder->related_type ?? '') === 'Deal' ? 'selected' : '' }}>Deal</option>
      </select>
    </div>

    <!-- RELATED ITEM -->
    <div class="col-md-6 form-group">
      <label>Related Item <span class="text-danger">*</span></label>
      
      <!-- Leads Selector -->
      <select class="form-control related-item-select" id="related_id_Lead" style="display:none;">
        <option value="">-- Select Lead --</option>
        @foreach($leads as $l)
          <option value="{{ $l->id }}" {{ (old('related_type') === 'Lead' && old('related_id') == $l->id) || ($isEdit && $reminder->related_type === 'Lead' && $reminder->related_id == $l->id) || (isset($preselected_lead_id) && $preselected_lead_id == $l->id) ? 'selected' : '' }}>
            {{ $l->name }} (Interested in: {{ $l->interested_property ?? 'None' }})
          </option>
        @endforeach
      </select>

      <!-- Clients Selector -->
      <select class="form-control related-item-select" id="related_id_Client" style="display:none;">
        <option value="">-- Select Client --</option>
        @foreach($clients as $c)
          <option value="{{ $c->id }}" {{ (old('related_type') === 'Client' && old('related_id') == $c->id) || ($isEdit && $reminder->related_type === 'Client' && $reminder->related_id == $c->id) || (isset($preselected_client_id) && $preselected_client_id == $c->id) ? 'selected' : '' }}>
            {{ $c->name }} (Budget: {{ $c->budget ?? 'None' }})
          </option>
        @endforeach
      </select>

      <!-- Site Visits Selector -->
      <select class="form-control related-item-select" id="related_id_Site_Visit" style="display:none;">
        <option value="">-- Select Site Visit --</option>
        @foreach($siteVisits as $v)
          <option value="{{ $v->id }}" {{ (old('related_type') === 'Site Visit' && old('related_id') == $v->id) || ($isEdit && $reminder->related_type === 'Site Visit' && $reminder->related_id == $v->id) ? 'selected' : '' }}>
            {{ $v->client_name }} - {{ $v->property_name }} ({{ $v->visit_date ? \Carbon\Carbon::parse($v->visit_date)->format('Y-m-d') : '' }})
          </option>
        @endforeach
      </select>

      <!-- Deals Selector -->
      <select class="form-control related-item-select" id="related_id_Deal" style="display:none;">
        <option value="">-- Select Deal --</option>
        @foreach($deals as $d)
          <option value="{{ $d->id }}" {{ (old('related_type') === 'Deal' && old('related_id') == $d->id) || ($isEdit && $reminder->related_type === 'Deal' && $reminder->related_id == $d->id) ? 'selected' : '' }}>
            {{ $d->client_name }} - {{ $d->property_name }} (₹{{ $d->deal_amount }})
          </option>
        @endforeach
      </select>

      <!-- Placeholder Dropdown when no type is selected -->
      <select class="form-control" id="related_id_placeholder" readonly>
        <option value="">Select a Module Type First</option>
      </select>

      <!-- Hidden field which will hold the final submitted value -->
      <input type="hidden" name="related_id" id="related_id" value="{{ old('related_id', $reminder->related_id ?? ($preselected_lead_id ?? ($preselected_client_id ?? ''))) }}" required>
    </div>
  </div>

  <!-- NOTES & STATUS -->
  <h4><span class="glyphicon glyphicon-pencil"></span> Additional Details</h4>
  <hr>

  <div class="row">
    <!-- STATUS -->
    <div class="col-md-6 form-group">
      <label>Status <span class="text-danger">*</span></label>
      <select name="status" class="form-control" required>
        <option value="Pending" {{ old('status', $reminder->status ?? 'Pending') === 'Pending' ? 'selected' : '' }}>Pending</option>
        <option value="Completed" {{ old('status', $reminder->status ?? 'Pending') === 'Completed' ? 'selected' : '' }}>Completed</option>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label>Notes / Follow-up Details</label>
    <textarea name="notes" class="form-control" rows="4" placeholder="Enter specific reminder details, follow-up topics, or description...">{{ old('notes', $reminder->notes ?? '') }}</textarea>
  </div>

  <!-- BUTTONS -->
  <div class="text-right" style="margin-top: 20px;">
    <button type="reset" class="btn btn-default">Clear</button>
    @php
      $cancelUrl = url('/reminders');
      if (isset($preselected_lead_id)) {
          $cancelUrl = url('/leads/'.$preselected_lead_id);
      } elseif (isset($preselected_client_id)) {
          $cancelUrl = url('/clients/'.$preselected_client_id);
      }
    @endphp
    <a href="{{ $cancelUrl }}" class="btn btn-warning">Cancel</a>
    <button type="submit" class="btn btn-primary">
      {{ $isEdit ? 'Update Reminder' : 'Add Reminder' }}
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

<script type="text/javascript">
$(document).ready(function() {
  function toggleRelatedItems() {
    var type = $('#related_type').val();
    $('.related-item-select').hide();
    $('#related_id_placeholder').hide();
    
    var activeSelect = null;
    if (type === 'Lead') {
      activeSelect = $('#related_id_Lead');
    } else if (type === 'Client') {
      activeSelect = $('#related_id_Client');
    } else if (type === 'Site Visit') {
      activeSelect = $('#related_id_Site_Visit');
    } else if (type === 'Deal') {
      activeSelect = $('#related_id_Deal');
    } else {
      $('#related_id_placeholder').show();
    }

    if (activeSelect) {
      activeSelect.show();
      // Sync the hidden related_id input when the active select changes
      var val = activeSelect.val();
      $('#related_id').val(val);
    } else {
      $('#related_id').val('');
    }
  }

  // Trigger change on select box updates
  $('#related_type').change(function() {
    // Reset specific selector values when changing type
    $('.related-item-select').val('');
    toggleRelatedItems();
  });

  $('.related-item-select').change(function() {
    var val = $(this).val();
    $('#related_id').val(val);
  });

  // Initial call to set correct states (useful on validation error back or edit)
  toggleRelatedItems();
  
  // Set initial selected values if editing or preselected
  var initialType = '{{ old("related_type", $reminder->related_type ?? ($preselected_lead_id ? "Lead" : ($preselected_client_id ? "Client" : ""))) }}';
  var initialId = '{{ old("related_id", $reminder->related_id ?? ($preselected_lead_id ?? ($preselected_client_id ?? ""))) }}';
  
  if (initialType && initialId) {
    if (initialType === 'Site Visit') {
      $('#related_id_Site_Visit').val(initialId);
    } else {
      $('#related_id_' + initialType).val(initialId);
    }
    $('#related_id').val(initialId);
  }
});
</script>

</body>
</html>
