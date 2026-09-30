<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ isset($property) ? 'Edit Property - CRM' : 'Add Property - CRM' }}</title>

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
      <li><a href="{{ url('/properties') }}">Properties</a></li>
      <li class="active"><a href="{{ url('/properties/create') }}">Add Property </a></li>
    </ul>
  </div>
</nav>

<!-- TITLE -->
<div class="container">
  <h2>{{ isset($property) ? 'Edit Property' : 'Add New Property' }}</h2>
  <hr>
</div>

<!-- FORM -->
<div class="container">
  <div class="col-md-8 col-md-offset-2">

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

   <form action="{{ isset($property) ? url('/properties/'.$property->id.'/update') : url('/properties/store') }}"
      method="POST"
      enctype="multipart/form-data">
            @csrf

      <!-- BASIC INFO -->
      <h4><span class="glyphicon glyphicon-home"></span> Basic Information</h4>
      <hr>

      <div class="row">
        <div class="col-md-6 form-group">
          <label>Property Name</label>
          <input type="text" name="property_name" class="form-control" value="{{ old('property_name', $property->property_name ?? '') }}" required>
        </div>

        <div class="col-md-6 form-group">
          <label>Property Type</label>
          <select name="property_type" class="form-control">
            <option value="Apartment" {{ old('property_type', $property->property_type ?? '') === 'Apartment' ? 'selected' : '' }}>
    Apartment
</option>

<option value="Villa" {{ old('property_type', $property->property_type ?? '') === 'Villa' ? 'selected' : '' }}>
    Villa
</option>

<option value="House" {{ old('property_type', $property->property_type ?? '') === 'House' ? 'selected' : '' }}>
    House
</option>

<option value="Commercial Office" {{ old('property_type', $property->property_type ?? '') === 'Commercial Office' ? 'selected' : '' }}>
    Commercial Office
</option>

<option value="Commercial Shop" {{ old('property_type', $property->property_type ?? '') === 'Commercial Shop' ? 'selected' : '' }}>
    Commercial Shop
</option>

<option value="Residential Plot" {{ old('property_type', $property->property_type ?? '') === 'Residential Plot' ? 'selected' : '' }}>
    Residential Plot
</option>

<option value="Agricultural Land" {{ old('property_type', $property->property_type ?? '') === 'Agricultural Land' ? 'selected' : '' }}>
    Agricultural Land
</option>          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 form-group">
          <label>Price</label>
          <input type="number" name="price" class="form-control" value="{{ old('price', $property->price ?? '') }}" required>
        </div>

        <div class="col-md-6 form-group">
          <label>Location</label>
          <input type="text" name="location" class="form-control" value="{{ old('location', $property->location ?? '') }}" required>
        </div>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $property->description ?? '') }}</textarea>
      </div>

      <!-- DETAILS -->
      <h4><span class="glyphicon glyphicon-list-alt"></span> Property Details</h4>
      <hr>

      <div class="row">
        <div class="col-md-6 form-group">
          <label>Area (sq ft)</label>
          <input type="number" name="area" class="form-control" value="{{ old('area', $property->area ?? '') }}">
        </div>

        <div class="col-md-6 form-group">
          <label>Year Built</label>
          <input type="number" name="year_built" class="form-control" value="{{ old('year_built', $property->year_built ?? '') }}">
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 form-group">
          <label>Bedrooms</label>
          <input type="number" name="bedrooms" class="form-control" value="{{ old('bedrooms', $property->bedrooms ?? '') }}">
        </div>

        <div class="col-md-4 form-group">
          <label>Bathrooms</label>
          <input type="number" name="bathrooms" class="form-control" value="{{ old('bathrooms', $property->bathrooms ?? '') }}">
        </div>

        <div class="col-md-4 form-group">
          <label>Garages</label>
          <input type="number" name="garages" class="form-control" value="{{ old('garages', $property->garages ?? '') }}">
        </div>
      </div>

      <!-- AMENITIES -->
      <h4><span class="glyphicon glyphicon-star"></span> Amenities & Features</h4>
      <hr>

      @php $amenities = old('amenities', $property->amenities ?? []); @endphp
      <div class="checkbox">
        <label><input type="checkbox" name="amenities[]" value="Parking" {{ in_array('Parking', $amenities) ? 'checked' : '' }}> Parking</label>
        <label><input type="checkbox" name="amenities[]" value="Garden" {{ in_array('Garden', $amenities) ? 'checked' : '' }}> Garden</label>
        <label><input type="checkbox" name="amenities[]" value="Swimming Pool" {{ in_array('Swimming Pool', $amenities) ? 'checked' : '' }}> Swimming Pool</label>
        <label><input type="checkbox" name="amenities[]" value="Gym" {{ in_array('Gym', $amenities) ? 'checked' : '' }}> Gym</label>
      </div>

      <div class="checkbox">
        <label><input type="checkbox" name="amenities[]" value="CCTV" {{ in_array('CCTV', $amenities) ? 'checked' : '' }}> CCTV</label>
        <label><input type="checkbox" name="amenities[]" value="Furnished" {{ in_array('Furnished', $amenities) ? 'checked' : '' }}> Furnished</label>
        <label><input type="checkbox" name="amenities[]" value="Central AC" {{ in_array('Central AC', $amenities) ? 'checked' : '' }}> Central AC</label>
      </div>

      <!-- STATUS -->
      <h4><span class="glyphicon glyphicon-stats"></span> Status & Ownership</h4>
      <hr>

      <div class="row">
        <div class="col-md-6 form-group">
          <label>Status</label>
          <select name="status" class="form-control">
            <option value="Available" {{ old('status', $property->status ?? '') === 'Available' ? 'selected' : '' }}>Available</option>
            <option value="Booked" {{ old('status', $property->status ?? '') === 'Booked' ? 'selected' : '' }}>Booked</option>
            <option value="Sold" {{ old('status', $property->status ?? '') === 'Sold' ? 'selected' : '' }}>Sold</option>
          </select>
        </div>

        <div class="col-md-6 form-group">
          <label>Owner Name</label>
          <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $property->owner_name ?? '') }}">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 form-group">
          <label>Owner Phone</label>
          <input type="text" name="owner_phone" class="form-control" value="{{ old('owner_phone', $property->owner_phone ?? '') }}">
        </div>

        <div class="col-md-6 form-group">
          <label>Owner Email</label>
          <input type="email" name="owner_email" class="form-control" value="{{ old('owner_email', $property->owner_email ?? '') }}">
        </div>
      </div>
      <!-- PROPERTY IMAGE -->
<h4><span class="glyphicon glyphicon-picture"></span> Property Media</h4>
<hr>

<div class="form-group">
  <label>Property Image</label>

  <input type="file"
         name="image"
         class="form-control"
         accept=".jpg,.jpeg,.png">
</div>
<div class="form-group">
    <label>Property Document</label>

    <input type="file"
           name="document"
           class="form-control"
           accept=".pdf,.doc,.docx">
</div>
<div class="form-group">
    <label>Property Video</label>
    @if(isset($property) && $property->video)
        <p class="help-block">Current Video: <a href="{{ asset('property-videos/'.$property->video) }}" target="_blank">{{ $property->video }}</a></p>
    @endif
    <input type="file"
           name="video"
           class="form-control"
           accept=".mp4,.mov,.avi">
</div>
      <!-- BUTTONS -->
      <div class="text-right">
        <button type="reset" class="btn btn-default">Clear Form</button>
        <button type="submit" class="btn btn-primary">{{ isset($property) ? 'Update Property' : 'Add Property' }}</button>
      </div>

    </form>

  </div>
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
