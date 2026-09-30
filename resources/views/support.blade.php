<!DOCTYPE html>
<html>
<head>
<title>Support</title>
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

<div class="container">
<h2>Support</h2>
<hr>

<p>If you face any issues, contact our support team:</p>

<ul>
<li>Email: support@realestatecrm.com</li>
<li>Phone: +91 9876543210</li>
</ul>

<h4>FAQs</h4>

<p><b>Q: How to add a property?</b></p>
<p>A: Go to Add Property page and fill the form.</p>

<p><b>Q: How to manage users?</b></p>
<p>A: Go to Users module and add/edit users.</p>
<hr>

<h4>Contact Support</h4>

<form>
  <div class="form-group">
    <input type="text" class="form-control" placeholder="Your Name">
  </div>

  <div class="form-group">
    <input type="email" class="form-control" placeholder="Your Email">
  </div>

  <div class="form-group">
    <textarea class="form-control" rows="3" placeholder="Describe your issue"></textarea>
  </div>

  <button class="btn btn-primary">Submit</button>
</form>
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
