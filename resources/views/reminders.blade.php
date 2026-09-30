<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reminders - CRM</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>

<!--  TOP HEADER -->
<nav class="navbar navbar-default">
  <div class="container">

    <div class="navbar-header">
      <p class="navbar-brand" style="color:black;"><strong>Real Estate CRM</strong></p>
    </div>

    <ul class="nav navbar-nav navbar-right">
      <li><a href="#">Welcome, {{ Auth::user()->name }}</a></li>
      <li><a href="{{ url('/') }}">Logout</a></li>
    </ul>

  </div>
</nav>

<!-- 🔹 MAIN NAV -->
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
  <h2>Reminder Management</h2>
  <hr>
</div>

<!-- MESSAGES -->
<div class="container">
  @if(session('success'))
    <div class="alert alert-success">
      <span class="glyphicon glyphicon-ok-sign"></span> {{ session('success') }}
    </div>
  @endif
  
  @if(session('warning'))
    <div class="alert alert-warning">
      <span class="glyphicon glyphicon-info-sign"></span> {{ session('warning') }}
    </div>
  @endif
</div>

<!-- TOOLBAR / FILTERING -->
<div class="container">
  <form method="GET" action="{{ url('/reminders') }}">
    <div class="row">
      <div class="col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Search by title, notes..." value="{{ request('search') }}">
      </div>

      <div class="col-md-3">
        <select name="status" class="form-control" onchange="this.form.submit()">
          <option value="">Filter by Status</option>
          <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
          <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
        </select>
      </div>

      <div class="col-md-3">
        <a href="{{ url('/reminders/create') }}" class="btn btn-primary btn-block">+ Add New Reminder</a>
      </div>
    </div>
  </form>
</div>

<br>

<!-- REMINDERS TABLE -->
<div class="container">
  <div class="panel panel-default">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-list"></span> <strong>CRM Reminders Checklist</strong>
    </div>
    <div class="panel-body table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr style="background-color: #f9f9f9;">
            <th>Title</th>
            <th>Date</th>
            <th>Time</th>
            <th>Module</th>
            <th>Related Item</th>
            <th>Notes</th>
            <th>Status</th>
            <th style="width: 230px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reminders as $reminder)
            <tr>
              <td><strong>{{ $reminder->title }}</strong></td>
              <td>{{ $reminder->reminder_date ? \Carbon\Carbon::parse($reminder->reminder_date)->format('Y-m-d') : 'N/A' }}</td>
              <td>{{ $reminder->reminder_time ? \Carbon\Carbon::parse($reminder->reminder_time)->format('H:i') : '' }}</td>
              <td>
                <span class="label {{ $reminder->related_type === 'Lead' ? 'label-warning' : ($reminder->related_type === 'Client' ? 'label-success' : ($reminder->related_type === 'Site Visit' ? 'label-info' : 'label-primary')) }}">
                  {{ $reminder->related_type }}
                </span>
              </td>
              <td>
                @if($reminder->related_item)
                  {{ $reminder->related_name }}
                @else
                  <span class="text-muted"><em>{{ $reminder->related_name }}</em></span>
                @endif
              </td>
              <td>
                @if($reminder->notes)
                  <span title="{{ $reminder->notes }}">{{ \Illuminate\Support\Str::limit($reminder->notes, 30) }}</span>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                <span class="label {{ $reminder->status === 'Completed' ? 'label-success' : 'label-warning' }}">
                  {{ $reminder->status }}
                </span>
              </td>
              <td>
                <!-- Mark Complete -->
                @if($reminder->status === 'Pending')
                  <a href="{{ url('/reminders/'.$reminder->id.'/complete') }}" class="btn btn-success btn-sm" title="Mark Completed">
                    <span class="glyphicon glyphicon-ok"></span> Done
                  </a>
                @endif

                <!-- Send Email -->
                <a href="{{ url('/reminders/'.$reminder->id.'/send-email') }}" class="btn btn-default btn-sm" title="Send Email Notification">
                  <span class="glyphicon glyphicon-envelope"></span> Email
                </a>

                <!-- Edit -->
                <a href="{{ url('/reminders/'.$reminder->id.'/edit') }}" class="btn btn-info btn-sm" title="Edit Reminder">
                  <span class="glyphicon glyphicon-pencil"></span> Edit
                </a>

                <!-- Delete -->
                <a href="{{ url('/reminders/'.$reminder->id.'/delete') }}" class="btn btn-danger btn-sm" title="Delete Reminder" onclick="return confirm('Are you sure you want to delete this reminder?');">
                  <span class="glyphicon glyphicon-trash"></span>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center">No reminders found matching the criteria.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- PAGINATION -->
<div class="container text-center">
  {{ $reminders->appends(request()->query())->links() }}
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
