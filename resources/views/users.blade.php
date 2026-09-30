<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Users</title>

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

<h2>User & Team Management</h2>
<hr>
@if(isset($editingUser))

<div class="panel panel-primary">
<div class="panel-heading">
<h4>Edit User</h4>
</div>

<div class="panel-body">

<form action="{{ url('/users/'.$editingUser->id) }}" method="POST">

@csrf
@method('PUT')

<div class="row">

<div class="col-md-4">
<label>Name</label>
<input type="text"
       name="name"
       class="form-control"
       value="{{ $editingUser->name }}"
       required>
</div>

<div class="col-md-4">
<label>Email</label>
<input type="email"
       name="email"
       class="form-control"
       value="{{ $editingUser->email }}"
       required>
</div>

<div class="col-md-4">
<label>Role</label>

<select name="role" class="form-control">

<option value="admin"
{{ $editingUser->role == 'admin' ? 'selected' : '' }}>
Admin
</option>

<option value="agent"
{{ $editingUser->role == 'agent' ? 'selected' : '' }}>
Agent
</option>

<option value="accountant"
{{ $editingUser->role == 'accountant' ? 'selected' : '' }}>
Accountant
</option>

<option value="client"
{{ $editingUser->role == 'client' ? 'selected' : '' }}>
Client
</option>

</select>

</div>

</div>

<br>

<button type="submit" class="btn btn-success">
Update User
</button>

<a href="{{ url('/users') }}" class="btn btn-default">
Cancel
</a>

</form>

</div>
</div>

@endif
<!-- SEARCH -->
<form method="GET" action="{{ url('/users') }}">
<div class="row">
<div class="col-md-6">
<input type="text" name="search" class="form-control" placeholder="Search users by name, email, or role" value="{{ request('search') }}">
</div>

<div class="col-md-3">
<select name="role" class="form-control" onchange="this.form.submit()">
<option value="">Filter by Role</option>
<option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
<option value="agent" {{ request('role') === 'agent' ? 'selected' : '' }}>Agent</option>
<option value="accountant" {{ request('role') === 'accountant' ? 'selected' : '' }}>Accountant</option>
<option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Client</option>
</select>
</div>

<div class="col-md-3">
<a href="{{ url('/users/create') }}" class="btn btn-primary btn-block"> + Add New User
</a>
</div>
</div>
</form>

<br>

<!-- TEAM MEMBERS -->
<h3>Team Members</h3>

<div class="row">
  @forelse($users as $user)
    @php
      $statusClass = strtolower($user->status) === 'inactive' ? 'label-danger' : 'label-success';
      $roleClass = strtolower($user->role) === 'admin' ? 'label-primary' : (strtolower($user->role) === 'accountant' ? 'label-info' : 'label-success');
    @endphp

    <div class="col-md-4">
      <div class="panel panel-default text-center">
        <div class="panel-body">
          <h4><b>{{ $user->name }}</b></h4>
          <p>{{ ucfirst($user->role) }}</p>

          <p><b>Email:</b> {{ $user->email }}</p>
          
          <p><b>Joined:</b> {{ $user->joining_date }}</p>

          <span class="label {{ $roleClass }}">{{ strtoupper($user->role) }}</span>
          <p class="text-{{ strtolower($user->status) === 'inactive' ? 'danger' : 'success' }}"><b>Status:</b> {{ $user->status }}</p>

          <hr>

          <a href="{{ url('/users/'.$user->id.'/edit') }}" class="btn btn-info btn-sm">Edit</a> 


        </div>
      </div>
    </div>
  @empty
    <div class="col-md-12">
      <div class="alert alert-info">No users found.</div>
    </div>
  @endforelse
</div>

<hr>

<!-- ALL USERS LIST -->
<h3>All Users</h3>

<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <th>User Name</th>
        <th>Email</th>
        
        <th>Role</th>
       
        <th>Joined</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $user)
        @php
          $statusClass = strtolower($user->status) === 'inactive' ? 'label-danger' : 'label-success';
        @endphp
      <tr>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
      
        <td><span class="label label-default">{{ ucfirst($user->role) }}</span></td>
        
        <td>{{ $user->joining_date }}</td>
        <td><span class="label {{ $statusClass }}">{{ $user->status }}</span></td>
       <td>

<a href="{{ url('/users/'.$user->id.'/edit') }}" class="btn btn-info btn-sm">Edit</a>

<form action="{{ url('/users/'.$user->id) }}"
      method="POST"
      style="display:inline;">

    @csrf
    @method('DELETE')

    <button type="submit"
            class="btn btn-danger btn-sm"
            onclick="return confirm('Delete this user?')">
        Delete
    </button>

</form>

</td>
      </tr>
      @empty
      <tr>
        <td colspan="8" class="text-center">No users available.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{ $users->appends(request()->query())->links() }}



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
