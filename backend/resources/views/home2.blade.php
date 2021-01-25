<!DOCTYPE HTML>
<html class="no-js" style="height: 100vh;">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>OTIMS: Online Textbooks Inventory Management System</title>

  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
  <meta name="format-detection" content="telephone=no">
<!-- CSS
  ================================================== -->
  <link rel="stylesheet" href="{{ asset('/other/fontawesome/css/font-awesome.min.css') }}">


  <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">


  <?php
$locale = App::getLocale();
?>

  @if($locale != 'en')
  <!-- <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap-rtl.min.css') }}"> -->
  @endif


  <link rel="stylesheet" href="{{ asset('/home-page/style.css') }}">
  <link rel="stylesheet" href="{{ asset('/home-page/owl-carousel/css/owl.carousel.css') }}">
  <link rel="stylesheet" href="{{ asset('/home-page/owl-carousel/css/owl.theme.css') }}">
  <!-- CUSTOM STYLESHEET FOR STYLING -->
  <link rel="stylesheet" href="{{ asset('home-page/custom.css') }}">
  <!-- SLIDER REVOLUTION 4.x CSS SETTINGS -->
  <link rel="stylesheet" href="{{ asset('home-page/revslider/css/settings.css') }}">
  <!-- GOOGLE FONTS -->
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800' rel='stylesheet' type='text/css'>
  <link href="home-page/style-switcher/css/style-switcher.css" rel="stylesheet" type="text/css">
  <!-- ================================================== -->
  <style>

  .uuu{padding:0px !important; margin:0px !important;margin-top:21px}
  .uuu li{list-style:none !important;  border-bottom:1px solid #e4e4e4 !important; }
  .uuu li a{text-align:left !important; padding:10px 0px !important;
    display:block !important;text-decoration:none !important; color:#363636 !important;margin-left:10px; cursor:pointer !important;float:none !important;border:none !important}
    .uuu li:hover{background-color: #e4e4e4 !important}
    @media(max-width:767px){
     #acr-title-h4{font-size:14px; margin-top:8px !important}
     #img-btn{display:none}
     #non-img-btn{display:block !important}
     #menu-ul{background:#fff;min-height:155px !important; margin-left:-103px}
   }
 </style>
 <style>
 .about{min-height:100px;padding-top:15px}
 .about h1{font-size:30px;text-align:center;color:$404040}
 .pr{position: relative;
  background: url(/files/writing-book-and-pencil.jpg) 50% 0 fixed;
  width: 100%;}
  .pro{padding-top: 50px;
    padding-bottom: 50px;
    float: right;}
    .plogo{display: block;
      margin: 50px auto 10px;}
      .services{float: right;}
      .services span{float: left;
        font-size: 55px;
        color: #3D98D0;
        margin-left: 40px;}
        .services h2{float: right;
          color: #3D98D0;
          font-size: 24px;}
          .services p{clear: both;
            font: 18px/28px bmitra;
            color: #8E8E8E;
            float: right;
            margin-top: 10px;
            text-align: justify;}
          </style>
        </head>
        <body class="home" style="height: 100vh; background-color: #f8f9fa;">
          <div class="body">
           <!-- Start Site Header -->
           <div class="site-header-wrapper">
            <header class="site-header">
              <div class="container-fluid sp-cont" style="margin-top:0px">
                <div class="row">
                  <div class="col-md-4 col-xs-3 site-logo">
                    <h1><a href="#"><img src="dist/img/logo.png" alt="Logo"></a></h1>
                  </div>
                  <span class="col-md-4 col-xs-7" style="text-align:center"><h4 style="color: #002a6c;font-weight:700;font-family:inherit;margin-top:16px;" id="acr-title-h4">
                    @lang('home.ministry')
                  </h4></span>

                  <div class="col-md-4 col-xs-2 header-right" style="height:58px;">

                    <div class="navbar-custom-menu">
                      <ul class="nav navbar-nav" style="width:95%; float:right; margin-right:-104px">
                       <!-- Messages: style can be found in dropdown.less-->



                       <li class="dropdown user user-menu" style="width: 35%;">
                        <a href="#" id="img-btn" class="dropdown-toggle" data-toggle="dropdown" style="color: #000000;padding:0;height:58px;">

                          <span class="hidden-xs" style="line-height: 58px; padding-left: 5px;">
                            <i class="fa fa-fw fa-language" aria-hidden="true"></i>

                            @if($locale == "en")
                            English
                            @endif


                            @if($locale == "ps")
                            پښتو
                            @endif


                            @if($locale == "fa")
                            دری
                            @endif
                          </span>
                        </a>
                        <a id="non-img-btn" style="display:none;color: #000000;" data-toggle="dropdown" class="dropdown-toggle btn btn-social-icon btn-defualt btn-xs"><i class="fa fa-ellipsis-v"></i></a>
                        <ul class="dropdown-menu" id="menu-ul" style="right:0;width:100%; padding-left:10px; padding-right:10px">

                         <li class="user-header" style="text-align:center">
                          <a href="{{url('language/en')}}" title="English">
                            <label style="color: #000000;">English</label>
                          </a>
                        </li>

                        <li class="user-header" style="text-align:center">
                          <a href="{{url('language/ps')}}" title="Pashto">
                            <label style="color: #000000;">پښتو</label>
                          </a>
                        </li>

                        <li class="user-header" style="text-align:center">
                          <a href="{{url('language/fa')}}" title="Dari">
                            <label style="color: #000000;">دری</label>
                          </a>
                        </li>


                      </ul>
                    </li>















                    @isLogin
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu" style="width: 50%;">
                      <a href="#" id="img-btn" class="dropdown-toggle" data-toggle="dropdown" style="color: #000000;padding:0;height:58px;">
                       <img src="dist/img/avatar04.png" class="user-image" style="width:58px" alt="User Image">
                       <span class="hidden-xs">{!! Auth::user()->name !!}</span>
                     </a>
                     <a id="non-img-btn" style="display:none;color: #000000;" data-toggle="dropdown" class="dropdown-toggle btn btn-social-icon btn-defualt btn-xs"><i class="fa fa-ellipsis-v"></i></a>
                     <ul class="dropdown-menu" id="menu-ul" style="right:0; width: 92%; padding-left:10px; padding-right:10px">
                       <!-- User image -->
                       <li class="user-header" style="text-align:center">
                        <img src="dist/img/avatar04.png" class="img-circle" style="width:58px" alt="User Image">

                        <label style="color: #000000;">
                        {!! Auth::user()->name !!}
                        <br>
                         <small>{!! Auth::user()->email !!}</small>
                       </label>
                     </li>

                     <!-- Menu Footer-->
                     <li class="user-footer">
                      <div class="pull-left">
                       <a href="{{url('logout')}}" class="btn btn-default btn-sm" style="color: #000000;">Log out</a>
                     </div>
                   </li>
                 </ul>
               </li>
               @endisLogin
               @isLogout
               <li style="100%">

                 <a href="#loginModal" data-toggle="modal" style="color: #000000; height: 58px; padding: 0;">
                 <span class="hidden-xs" style="line-height: 58px; padding-left: 5px; padding-right: 8px;">
                  <i class="fa fa-fw fa-user" aria-hidden="true"></i>
                  <b>@lang('home.login')</b>
                </span>
                </a></li>
                @endisLogout

              </ul>

            </div>
          </div>


        </div>
      </div>
    </header>
  </div>
  <div class="hero-area">
    <!-- START REVOLUTION SLIDER 4.5.0 fullwidth mode -->
    <div class="tp-banner-container">
      <div class="tp-banner">
       <ul>	<!-- SLIDE  -->
        <li data-transition="slidevertical" data-slotamount="1" data-masterspeed="1000" data-thumb=""  data-saveperformance="off"  data-title="Slide 1">
          <!-- MAIN IMAGE -->
          <img src="home-page/images/slide1.jpg"  alt="fullslide1"  data-bgposition="center bottom" data-bgfit="cover" data-bgrepeat="no-repeat">
          <!-- LAYERS -->

          <!-- LAYER NR. 1 -->
          <div class="tp-caption light_heavy_70_shadowed lfb ltt tp-resizeme"
          data-x="left" data-hoffset="20"
          data-y="center" data-voffset="-70"
          data-speed="600"
          data-start="800"
          data-easing="Power4.easeOut"
          data-splitin="none"
          data-splitout="none"
          data-elementdelay="0.01"
          data-endelementdelay="0.1"
          data-endspeed="500"
          data-endeasing="Power4.easeIn"
          style="z-index: 2; max-width: auto; max-height: auto; white-space: nowrap; color:white; font-size:40px;">
          OTIMS: Online Textbooks Inventory Management System
        </div>

        <!-- LAYER NR. 2 -->
        <div class="tp-caption light_medium_30_shadowed lfb ltt tp-resizeme"
        data-x="left" data-hoffset="20"
        data-y="center" data-voffset="0"
        data-speed="600"
        data-start="900"
        data-easing="Power4.easeOut"
        data-splitin="none"
        data-splitout="none"
        data-elementdelay="0.01"
        data-endelementdelay="0.1"
        data-endspeed="500"
        data-endeasing="Power4.easeIn"
        style="z-index: 3; max-width: auto; max-height: auto; white-space: nowrap;font-size:25px; color:white;">
        .. is efficient, reliable and cost-effective.
      </div>
    </li>
    <!-- SLIDE  -->

<!-- SLIDE  -->
<li data-transition="slidevertical" data-slotamount="1" data-masterspeed="1000" data-thumb=""  data-saveperformance="off"  data-title="Slide 2">
  <!-- MAIN IMAGE -->
  <img src="home-page/images/slide3.jpg"  alt="fullslide6"  data-bgposition="center top" data-bgfit="cover" data-bgrepeat="no-repeat">
  <!-- LAYERS -->

  <!-- LAYER NR. 1 -->
  <div class="tp-caption light_heavy_70_shadowed lfb ltt tp-resizeme"
  data-x="left" data-hoffset="20"
  data-y="center" data-voffset="-70"
  data-speed="600"
  data-start="800"
  data-easing="Power4.easeOut"
  data-splitin="none"
  data-splitout="none"
  data-elementdelay="0.01"
  data-endelementdelay="0.1"
  data-endspeed="500"
  data-endeasing="Power4.easeIn"
  style="z-index: 2; max-width: auto; max-height: auto; white-space: nowrap;font-size:35px;color:white;">
    OTIMS: Online Textbooks Inventory Management System
</div>

<!-- LAYER NR. 2 -->
<div class="tp-caption light_medium_30_shadowed lfb ltt tp-resizeme"
data-x="left" data-hoffset="20"
data-y="center" data-voffset="0"
data-speed="600"
data-start="900"
data-easing="Power4.easeOut"
data-splitin="none"
data-splitout="none"
data-elementdelay="0.01"
data-endelementdelay="0.1"
data-endspeed="500"
data-endeasing="Power4.easeIn"
style="z-index: 3; max-width: auto; max-height: auto; white-space: nowrap;font-size:25px;color:white;">
 .. is used for record-keeping and tracking of school textbooks distribution.
</div>
</li>
</ul>
<div class="tp-bannertimer"></div>
</div>
</div>
</div>
<!-- <div class="container">
  <div class="row about">
   <h1>@lang('home.welcome')</h1>
   <span class="glyphicon glyphicon-chevron-down" style="display:block;text-align:center;color:#404040"></span>
 </div>
</div> -->
<div class="container">
  <div class="row" style="margin-bottom: 50px;">
   <div class="col-md-12 col-xs-12">
     <p style="font-size:16px; font-weight: bold;">
     @lang('home.description')
     </p>
   </div>




 <div class="row" style="padding-bottom: 50px;">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
     <img src="files/otims_screen.png" class="plogo">

    </div>

    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="padding-top: 46px;">

    <div style="padding-left: 5px; padding-right: 5px;">

    <p style="font-size:15px">
The Online Textbooks Inventory Management System is developed by the USAID funded Afghan Children Read Program (ACR) for the Ministry of Educaton of Afghanistan for the record-keeping and tracking of school textbooks distribution.
          </p>

      <p style="font-size:15px">
Ministry of Education (MoE) has exercised a manual paper-based textbook distribution process with complexities in tracking and monitoring of record keeping and distribution. Afghan Children Read (ACR) conducted a rapid assessment of the ministry’s distribution system, and, in view of its findings, introduced an Online Textbook Inventory Management System (OTIMS) that is digitized, efficient, reliable and cost-effective for replacing the paper-based system.
          </p>
          </div>
    </div>

</div>



 </div>
</div>


 <footer style="background: #fff; padding: 15px; color: #444; border-top: 1px solid #d2d6de;">
                <strong>Copyright &copy; {{ date('Y') }}, Ministry of Education.</strong> All Rights
                Reserved.
            </footer>


<!-- End site footer -->
<a id="back-to-top"><i class="fa fa-angle-double-up"></i></a>
</div>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>@lang('home.login_to_account')</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ url('/login') }}">
         {{ csrf_field() }}
         <div class="input-group" style="margin-bottom:15px;">
          <span class="input-group-addon"><i class="fa fa-user"></i></span>
          <input type="email" class="form-control" placeholder="Username" name="email" value="{{ old('email') }}">
        </div>
        @if ($errors->has('email'))
        <span class="help-block">
          <strong>{{ $errors->first('email') }}</strong>
        </span>
        @endif
        <div class="input-group" style="margin-bottom:15px;">
          <span class="input-group-addon"><i class="fa fa-key"></i></span>
          <input type="password" class="form-control" placeholder="Password" name="password">
        </div>
        @if ($errors->has('password'))
        <span class="help-block">
          <strong>{{ $errors->first('password') }}</strong>
        </span>
        @endif
        <div class="form-group" style="margin-bottom:15px;">
          <div class="checkbox">
           <label>
            <input type="checkbox" name="remember">
             @lang('home.remember_me')
          </label>
        </div>
      </div>

    </div>
    <div class="modal-footer">
      <div class="form-group">
       <button type="submit" class="btn btn-primary">
        <i class="fa fa-btn fa-sign-in"></i> @lang('home.login')
      </button>

      <a class="btn btn-link" href="{{ url('/password/reset') }}">@lang('home.forgot_password')</a>
    </div>
  </form>
</div>
</div>
</div>
</div>
<script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

<script src="{{ asset('home-page/owl-carousel/js/owl.carousel.min.js') }}"></script>

<script src="home-page/ui-plugins.js"></script>
<script src="home-page/helper-plugins.js"></script>
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
<!-- <script src="{{ asset('home-page/js/init.js') }}"></script> -->
<script src="home-page/flexslider/js/jquery.flexslider.html"></script>
<script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>

<!-- SLIDER REVOLUTION 4.x SCRIPTS  -->
<script src="{{ asset('home-page/revslider/js/jquery.themepunch.tools.min.js') }}"></script>
<script src="{{ asset('home-page/revslider/js/jquery.themepunch.revolution.min.js') }}"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.tp-banner').show().revolution(
		{
			dottedOverlay:"none",
			delay:9000,
			startwidth:1170,
			startheight:550,
			hideThumbs:200,

			thumbWidth:100,
			thumbHeight:50,
			thumbAmount:5,

			navigationType:"none",
			navigationArrows:"solo",
			navigationStyle:"preview2",

			touchenabled:"on",
			onHoverStop:"on",

			swipe_velocity: 0.7,
			swipe_min_touches: 1,
			swipe_max_touches: 1,
			drag_block_vertical: false,


			keyboardNavigation:"on",

			navigationHAlign:"center",
			navigationVAlign:"bottom",
			navigationHOffset:0,
			navigationVOffset:20,

			soloArrowLeftHalign:"left",
			soloArrowLeftValign:"center",
			soloArrowLeftHOffset:20,
			soloArrowLeftVOffset:0,

			soloArrowRightHalign:"right",
			soloArrowRightValign:"center",
			soloArrowRightHOffset:20,
			soloArrowRightVOffset:0,

			shadow:0,
			fullWidth:"on",
			fullScreen:"off",

			spinner:"spinner0",

			stopLoop:"off",
			stopAfterLoops:-1,
			stopAtSlide:-1,

			shuffle:"off",

			autoHeight:"off",
			forceFullWidth:"off",



			hideThumbsOnMobile:"off",
			hideNavDelayOnMobile:1500,
			hideBulletsOnMobile:"off",
			hideArrowsOnMobile:"off",
			hideThumbsUnderResolution:0,

			hideSliderAtLimit:0,
			hideCaptionAtLimit:0,
			hideAllCaptionAtLilmit:0,
			startWithSlide:0
		});
	});	//ready
</script>
<script src="home-page/style-switcher/js/jquery_cookie.js"></script>
<script src="home-page/style-switcher/js/script.js"></script>



 @isLogin
<!-- Style Switcher Start -->
<div class="styleswitcher">
  <div class="arrow-box"><a class="switch-button"><span class="glyphicon glyphicon-menu-hamburger" style="margin-top:8px;"></span></a> </div>

  <ul class="uuu">

    {{--  Book distribution Start  --}}
    @canRead('book_dis_admin')
    <li class="treeview @if(session('menu')=='book_admin') active @endif">
      <a href="{{url('ACRbookdis/admin')}}">
        <i class="fa fa-bar-chart"></i> <span>@lang('home.book_settings')</span>
      </a>
    </li>
    @endcanRead

 @if (Auth::check())
    @canRead('book_dis_admin')
    <li class="treeview @if(session('menu')=='book_admin_report') active @endif">
      <a href="{{url('ACRbookdis/admin/report')}}">
        <i class="fa fa-bar-chart"></i> <span>@lang('home.book_report')</span>
      </a>
    </li>
    @endcanRead


    @canRead('book_distribution_staff')
    <li class="treeview @if(session('menu')=='book_admin') active @endif">
      <a href="{{url('ACRbookdis/record')}}">
        <i class="fa fa-bar-chart"></i> <span>@lang('home.book_distribution')</span>
      </a>
    </li>
    @endcanRead
    @endif
  </ul>
</div>
@endisLogin



</body>
</html>
