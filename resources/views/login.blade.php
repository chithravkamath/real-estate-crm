<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Real Estate CRM</title>

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
          <h3>Real Estate CRM</h3>
          <p>Property Management System</p>
        </div>

        <div class="panel-body">

          @if(session('success'))
            <div class="alert alert-success">
              {{ session('success') }}
            </div>
          @endif

          @if(session('status'))
            <div class="alert alert-success">
              {{ session('status') }}
            </div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger">
              <ul style="margin: 0; padding-left: 15px;">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- ✅ UPDATED FORM -->
          <form action="{{ url('/login') }}" method="POST">
  @csrf

  <div class="form-group">
    <label>Email Address</label>
    <input type="email" name="email" class="form-control" required>
  </div>

  <div class="form-group">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <a href="{{ url('/forgot-password') }}" class="pull-left" style="font-size: 12px; font-weight: normal;">Forgot Password?</a>
  <br><br>

  <button type="submit" class="btn btn-primary btn-block">Login</button>
</form>

        </div>

        <div class="panel-footer text-center">
          Don't have an account? 
          <!-- ✅ UPDATED -->
          <a href="{{ url('/register') }}">Sign up here</a>
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