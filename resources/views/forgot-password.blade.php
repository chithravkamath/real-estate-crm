<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password - Real Estate CRM</title>

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
          <p>Reset Your Password</p>
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

          <p class="text-muted text-center" style="font-size: 13px; margin-bottom: 20px;">
            Enter your registered email address and we will generate a password reset link for you.
          </p>

          <form action="{{ url('/forgot-password') }}" method="POST">
            @csrf

            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="e.g. admin@example.com" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
          </form>

        </div>

        <div class="panel-footer text-center">
          Remembered your password? 
          <a href="{{ url('/login') }}">Login here</a>
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
