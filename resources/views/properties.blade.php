<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Properties - CRM</title>

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
  <h2>Properties Listing</h2>
  <hr>
</div>

<div class="container">

<!-- TOOLBAR -->
<form method="GET" action="{{ url('/properties') }}">
<div class="row">

  <div class="col-md-6">
    <input type="text"
           name="search"
           class="form-control"
           placeholder="Search Property"
           value="{{ request('search') }}">
  </div>

  <div class="col-md-3">
    <select name="type" class="form-control" onchange="this.form.submit()">
      <option value="">All Types</option>
      <option value="Apartment" {{ request('type') === 'Apartment' ? 'selected' : '' }}>Apartment</option>
      <option value="Villa" {{ request('type') === 'Villa' ? 'selected' : '' }}>Villa</option>
      <option value="House" {{ request('type') === 'House' ? 'selected' : '' }}>House</option>
      <option value="Commercial Office" {{ request('type') === 'Commercial Office' ? 'selected' : '' }}>Commercial Office</option>
      <option value="Commercial Shop" {{ request('type') === 'Commercial Shop' ? 'selected' : '' }}>Commercial Shop</option>
      <option value="Residential Plot" {{ request('type') === 'Residential Plot' ? 'selected' : '' }}>Residential Plot</option>
      <option value="Agricultural Land" {{ request('type') === 'Agricultural Land' ? 'selected' : '' }}>Agricultural Land</option>
    </select>
  </div>

  <div class="col-md-3">
    <a href="{{ url('/properties/create') }}"
       class="btn btn-primary btn-block">
       + Add New Property
    </a>
  </div>

</div>
</form>
  <br>
<!-- CARD VIEW -->


<div class="row">
  @forelse ($properties as $property)
    <div class="col-md-4">
      <div class="panel panel-default text-center">
        <div class="panel-body">

          @if($property->image)

<img src="{{ asset('property-images/'.$property->image) }}"
     class="img-thumbnail"
     style="width:220px;
            height:160px;
            object-fit:cover;">
@else

<p>No Image</p>

@endif

          
          <h4>
            {{ $property->property_name }}
            @if($property->video)
              <span  title="Video available" style="font-size: 14px; margin-left: 5px;"></span>
            @endif
          </h4>
          <p>{{ $property->location }}</p>
          <h4>{{ $property->price ? '₹'.number_format($property->price, 0) : '' }}</h4>

          <span class="label {{ strtolower($property->status) === 'available' ? 'label-success' : (strtolower($property->status) === 'booked' ? 'label-warning' : 'label-danger') }}">{{ $property->status ?? 'Unknown' }}</span>

          <p>
            <a href="{{ url('/properties/'.$property->id) }}"
   class="btn btn-default btn-sm">View Details</a>
          </p>

        </div>
      </div>
    </div>
  @empty
    <div class="col-md-12">
      <div class="alert alert-info">No properties found.</div>
    </div>
  @endforelse
</div>

<br>

<!-- TABLE VIEW -->
<h4>Table View</h4>

<table class="table table-bordered table-hover">
  <thead>
    <tr>
      <th>Property Name</th>
      <th>Location</th>
      <th>Price</th>
      <th>Type</th>
      <th>Bedrooms</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>

  <tbody>
    @forelse ($properties as $property)
      <tr>
        <td>
          {{ $property->property_name }}
          @if($property->video)
            <span  title="Video available" style="margin-left: 5px;"></span>
          @endif
        </td>
        <td>{{ $property->location }}</td>
        <td>{{ $property->price ? '₹'.number_format($property->price, 0) : '' }}</td>
        <td>{{ $property->property_type }}</td>
        <td>{{ $property->bedrooms }}</td>
        <td><span class="label {{ strtolower($property->status) === 'available' ? 'label-success' : (strtolower($property->status) === 'booked' ? 'label-warning' : 'label-danger') }}">{{ $property->status ?? 'Unknown' }}</span></td>
        <td>
          <a href="{{ url('/properties/edit/'.$property->id) }}" class="btn btn-info btn-sm">Edit</a>
          <button class="btn btn-danger btn-sm" onclick="event.preventDefault(); if(confirm('Delete this property?')) document.getElementById('delete-property-{{ $property->id }}').submit();">Delete</button>
          <form id="delete-property-{{ $property->id }}" action="{{ url('/properties/'.$property->id) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
          </form>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="7" class="text-center">No properties found.</td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="text-center">
  {{ $properties->appends(request()->query())->links() }}
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
