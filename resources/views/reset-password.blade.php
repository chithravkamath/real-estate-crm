<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - Real Estate CRM</title>

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
          <p>Set a New Password</p>
        </div>

        <div class="panel-body">

          @if(session('success'))
            <div class="alert alert-success">
              {{ session('success') }}
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

          <form action="{{ url('/reset-password') }}" method="POST">
            @csrf

            <!-- Hidden reset token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- EMAIL ADDRESS -->
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $email) }}" placeholder="e.g. admin@example.com" required autofocus>
            </div>

            <!-- NEW PASSWORD -->
            <div class="form-group">
              <label>New Password</label>
              <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="form-group">
              <label>Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
          </form>

        </div>

        <div class="panel-footer text-center">
          Back to login? 
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
