
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Property Details</title>

<link rel="stylesheet"
href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

</head>

<body>

<div class="container">

<h2>Property Details</h2>

<hr>

<div class="panel panel-default">

<div class="panel-body">

<div class="row">

<div class="col-md-6">

@if($property->image)

<img src="{{ asset('property-images/'.$property->image) }}"
     class="img-responsive img-thumbnail"
     style="width:100%;
            height:300px;
            object-fit:cover;">

@endif

@if($property->video)
<br><br>
<video width="100%" controls class="img-thumbnail" style="max-height:300px; background:#000;">
    <source src="{{ asset('property-videos/'.$property->video) }}" type="video/mp4">
    Your browser does not support the video tag.
</video>
@endif

<br><br>

@if($property->document)

<a href="{{ asset('property-documents/'.$property->document) }}"
   target="_blank"
   class="btn btn-info">

   View Document

</a>

@endif

</div>

<div class="col-md-6">

<h3>{{ $property->property_name }}</h3>

<hr>

<p>
<strong>Property Type:</strong>
{{ $property->property_type }}
</p>

<p>
<strong>Location:</strong>
{{ $property->location }}
</p>

<p>
<strong>Price:</strong>
₹{{ number_format($property->price) }}
</p>

<p>
<strong>Area:</strong>
{{ $property->area }} sq ft
</p>

<p>
<strong>Status:</strong>

<span class="label label-success">
{{ $property->status }}
</span>

</p>

<p>
<strong>Bedrooms:</strong>
{{ $property->bedrooms }}
</p>

<p>
<strong>Bathrooms:</strong>
{{ $property->bathrooms }}
</p>

<p>
<strong>Garages:</strong>
{{ $property->garages }}
</p>

<p>
<strong>Owner Name:</strong>
{{ $property->owner_name }}
</p>

<p>
<strong>Owner Phone:</strong>
{{ $property->owner_phone }}
</p>

<p>
<strong>Owner Email:</strong>
{{ $property->owner_email }}
</p>

<p>
<strong>Description:</strong>
{{ $property->description }}
</p>

<br>

<a href="{{ url('/properties/edit/'.$property->id) }}"
   class="btn btn-info btn-sm">

   Edit

</a>

<a href="{{ url('/properties/delete/'.$property->id) }}"
   class="btn btn-danger btn-sm"
   onclick="return confirm('Delete this property?')">

   Delete

</a>

<a href="{{ url('/properties') }}"
   class="btn btn-default btn-sm">

   Back

</a>

</div>

</div>

</div>

</div>

  <!-- PROPERTY LOCATION MAP -->
  <div class="panel panel-default" style="margin-top: 20px;">
    <div class="panel-heading">
      <span class="glyphicon glyphicon-map-marker"></span> <strong>Property Location Map</strong>
    </div>
    <div class="panel-body" style="padding: 0;">
      @if(!empty($property->location))
          <iframe
              width="100%"
              height="300"
              style="border:0;"
              loading="lazy"
              allowfullscreen
              src="https://www.google.com/maps?q={{ urlencode($property->location) }}&output=embed">
          </iframe>
      @else
          <div class="alert alert-warning" style="margin: 15px;">
              Location map unavailable.
          </div>
      @endif
    </div>
  </div>

  <!-- AGREEMENT & DOCUMENT MANAGEMENT -->
  <div class="panel panel-info" style="margin-top: 20px;">
    <div class="panel-heading">
      <div class="row">
        <div class="col-xs-6" style="line-height: 30px;">
          <span class="glyphicon glyphicon-folder-open"></span> <strong>Agreements & Documents</strong>
        </div>
        <div class="col-xs-6 text-right">
          <a href="{{ url('/documents/upload?property_id='.$property->id) }}" class="btn btn-info btn-sm">
            <span class="glyphicon glyphicon-upload"></span> Upload Document
          </a>
        </div>
      </div>
    </div>
    <div class="panel-body table-responsive">
      <!-- Inline search & filter form -->
      <form method="GET" action="{{ url('/properties/'.$property->id) }}" style="margin-bottom: 15px;">
        <div class="row">
          <div class="col-md-8">
            <input type="text" name="doc_search" class="form-control input-sm" placeholder="Search documents..." value="{{ request('doc_search') }}">
          </div>
          <div class="col-md-4">
            <select name="doc_type" class="form-control input-sm" onchange="this.form.submit()">
              <option value="">Filter by Type</option>
              <option value="agreement document" {{ request('doc_type') === 'agreement document' ? 'selected' : '' }}>Agreement Document</option>
              <option value="booking confirmation" {{ request('doc_type') === 'booking confirmation' ? 'selected' : '' }}>Booking Confirmation</option>
              <option value="sale document" {{ request('doc_type') === 'sale document' ? 'selected' : '' }}>Sale Document</option>
              <option value="invoice" {{ request('doc_type') === 'invoice' ? 'selected' : '' }}>Invoice</option>
              <option value="ID proof" {{ request('doc_type') === 'ID proof' ? 'selected' : '' }}>ID Proof</option>
            </select>
          </div>
        </div>
      </form>

      <table class="table table-bordered table-hover" style="font-size: 12px; margin-bottom: 0;">
        <thead>
          <tr style="background-color: #f9f9f9;">
            <th>Document Name</th>
            <th>Type</th>
            <th>Uploaded By</th>
            <th>Upload Date</th>
            <th style="width: 90px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($documents as $doc)
            <tr>
              <td><strong>{{ $doc->document_name }}</strong></td>
              <td>
                <span class="label label-default">{{ ucwords($doc->document_type) }}</span>
              </td>
              <td>{{ $doc->uploaded_by }}</td>
              <td>{{ $doc->created_at->format('Y-m-d') }}</td>
              <td>
                <!-- Download -->
                <a href="{{ url('/documents/'.$doc->id.'/download') }}" class="btn btn-default btn-sm" title="Download"><span class="glyphicon glyphicon-download-alt"></span></a>
                <!-- Delete -->
                <a href="{{ url('/documents/'.$doc->id.'/delete') }}" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Delete this document?');"><span class="glyphicon glyphicon-trash"></span></a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted">No agreements or documents uploaded yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</body>
</html>

