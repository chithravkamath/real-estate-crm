<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Client Login - Real Estate CRM</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

</head>

<body style="background-color:#f5f5f5;">

<div class="container">
  <div class="row">
    
    <div class="col-md-4 col-md-offset-4">
      
      <div class="panel panel-default" style="margin-top:80px;">
        
        <!-- HEADER -->
        <div class="panel-heading text-center">
          <h3>Real Estate CRM</h3>
          <p>Property Management System</p>
        </div>

        <!-- BODY -->
        <div class="panel-body">

          <form>

            <div class="form-group">
              <label>Email Address</label>
              <input type="email" class="form-control" placeholder="Enter your email">
            </div>

            <div class="form-group">
              <label>Password</label>
              <input type="password" class="form-control" placeholder="Enter your password">
            </div>

            <div class="checkbox">
              <label>
                <input type="checkbox"> Remember me
              </label>
            </div>

            <p class="text-right">
              <a href="#">Forgot Password?</a>
            </p>

            <a href="client-dashboard.html" class="btn btn-primary btn-block">
              Login
            </a>

          </form>

        </div>

        <!-- FOOTER -->
        <div class="panel-footer text-center">
          Don't have an account? <a href="{{ url('client/register') }}">Sign up here</a>
        </div>

      </div>

      <!-- COPYRIGHT -->
      <p class="text-center">
        © 2026 Real Estate CRM
      </p>

    </div>

  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


</body>
</html>