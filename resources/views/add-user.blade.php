<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add User</title>

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
      <li><a href="{{ url('/users') }}">Users</a></li>
      <li class="active"><a href="{{ url('/users/create') }}">Add User</a></li>
    </ul>
  </div>
</nav>
<div class="container">

<h2>Add New User</h2>
<hr>

<div class="panel panel-default">
<div class="panel-body">

<form action="{{ url('/users/store') }}" method="POST">
  @csrf

  <!-- NAME -->
  <div class="form-group">
    <label>Full Name</label>
    <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
  </div>

  <!-- EMAIL -->
  <div class="form-group">
    <label>Email</label>
    <input type="email" name="email" class="form-control" placeholder="Enter email" required>
  </div>

  <!-- PHONE -->
  <div class="form-group">
    <label>Phone</label>
    <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
  </div>

  <!-- PASSWORD -->
  <div class="form-group">
    <label>Password</label>
    <input type="password" name="password" class="form-control" placeholder="Enter password">
  </div>

  <!-- ROLE -->
  <div class="form-group">
    <label>Role</label>
    <select class="form-control" name="role">
      <option>Agent</option>
      <option>Admin</option>
      <option>Accountant</option>
    </select>
  </div>

  <!-- DEPARTMENT -->
  <div class="form-group">
    <label>Department</label>
    <select class="form-control" name="department">
      <option>Sales</option>
      <option>Management</option>
      <option>Finance</option>
      <option>IT</option>
    </select>
  </div>

  <!-- JOINING DATE -->
  <div class="form-group">
    <label>Joining Date</label>
    <input type="date" name="joining_date" class="form-control">
  </div>

  <!-- STATUS -->
  <div class="form-group">
    <label>Status</label>
    <select class="form-control" name="status">
      <option>Active</option>
      <option>Inactive</option>
    </select>
  </div>

  <!-- BUTTONS -->
  <div class="form-group">
    <button type="submit" class="btn btn-primary">Add User</button>
    <button type="reset" class="btn btn-default">Clear</button>
  </div>

</form>

</div>
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
