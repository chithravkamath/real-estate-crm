<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Real Estate CRM</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>

<div class="container">

  <!-- SPACE ABOVE -->
  <div class="row">
    <div class="col-md-12 text-center">
      <br><br><br><br><br><br>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      <div class="panel panel-default">

        <div class="panel-heading text-center">
          <h3>Create Account</h3>
        </div>

        <div class="panel-body">

          <!-- ✅ UPDATED FORM -->
          <form action="{{ url('/register/store') }}" method="POST">
            @csrf

            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
            </div>

            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
              <label>Password</label>
              <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>

            <div class="form-group">
              <label>Confirm Password</label>
              <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
            </div>

            <button type="submit" class="btn btn-success btn-block">
              Register
            </button>

          </form>

        </div>

        <div class="panel-footer text-center">
          Already have an account? 
          <!-- ✅ FIXED -->
          <a href="{{ url('/login') }}">Login</a>
        </div>

      </div>

      <p class="text-center">&copy; 2026 Real Estate CRM</p>

    </div>

  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</body>
</html>