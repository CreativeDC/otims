@include('layouts.admin.head')
<style>
body{background-color:#ecf0f5}
.main-header{background-color:white;}
#acr-title{left:25% !important;}
</style>
<body>
	<!-- Main content -->
	@include('layouts.admin.header')
    <section class="content" style="margin-top:5%;">
      <div class="error-page">
        @if(Auth::user())
		<h2 class="headline text-red">401</h2>
        <div class="error-content">
          <h3><i class="fa fa-warning text-red"></i> Oops! You are not authorized.</h3>
          <p>
            Sorry, You are not authorized to access this page, please go back and continue.
          </p>
		   <a type="button" href="{{URL::previous()}}" class="btn btn-primary pull-left" id="submit_user_btn">Login </a>
        </div>
		@else
		  @include('layouts.admin.login-page')
		@endif
      </div>
      <!-- /.error-page -->
    </section>

    <!-- <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script> -->
</body>
<!-- <script>
$(document).ready(function(){
	$('body').css({"pointer-events":"none","opacity":"0.4"});
})

</script> -->
