<div class="login-box">
  <div class="login-logo">
    <a href="{{url('/')}}">ACR</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Please login to continue to this action.</p>
    <form method="post" action="{{ url('/login') }}">
	{{ csrf_field() }}
      <div class="form-group has-feedback">	  
        <input type="email" class="form-control" name="email" placeholder="Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
		@if ($errors->has('email'))
		<span class="help-block">
		<strong>{{ $errors->first('email') }}</strong>
	   @endif
      </div>
      <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
        <input type="password" class="form-control" name="password" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
		@if ($errors->has('password'))
		<span class="help-block">
			<strong>{{ $errors->first('password') }}</strong>
		</span>
		@endif
      </div>
      <div class="row">        
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
	<a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
  </div>
  <!-- /.login-box-body -->
</div>