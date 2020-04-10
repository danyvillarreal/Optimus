<!DOCTYPE html>
<html>
    <head>
        <title>Bootstrap Example</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
            <div class="col-xs-12 col-md-12 col-lg-12">
				<h3 align="center">Simple Login System in Laravel</h3><br />
				<!-- @if(isset(Auth::user()->email))
				<script>window.location="/login/successlogin";</script>
				@endif -->

				@if ($message = Session::get('error'))
				<div class="alert alert-danger alert-block">
					<button type="button" class="close" data-dismiss="alert">×</button>
					<strong>{{ $message }}</strong>
				</div>
				@endif

				@if (count($errors) > 0)
				<div class="alert alert-danger">
					<ul>
					@foreach($errors->all() as $error)
					<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
				@endif

				<form method="post" action="{{ url('/login/checklogin') }}">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Enter Email</label>
						<input type="email" name="email" class="form-control" />
					</div>
					<div class="form-group">
						<label>Enter Password</label>
						<input type="password" name="password" class="form-control" />
					</div>
					<div class="form-group">
						<input type="submit" name="login" class="btn btn-primary" value="Login" />
					</div>
				</form>
            </div>
        </div>
    </body>
</html>