<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta content="Ahmad Hussein Rezae" name="Auther">
    <meta content="Book Distribution System, designed and developed by ACR." name="Description">
    <!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="{{ asset('/other/fontawesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <!-- Theme style -->
    <?php
    $locale = App::getLocale();
    ?>

    @if($locale != 'en')
        <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap-rtl.min.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/Style_RTL.min.css') }}"
    @endif

    @if($locale == 'en')
        <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">
    @endif
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/iCheck/flat/blue.css') }}">
    <!-- Morris chart -->
    <link rel="stylesheet" href="{{ asset('plugins/morris/morris.css') }}">
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
    <!-- Date Picker -->
    <link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
    {{--Pace style--}}
    <link href="{{asset('dist/css/pace.css')}}" rel="stylesheet">
    {{--Page custom styles--}}
    <link href="{{asset('dist/css/acr.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plugins/datatables/jquery.dataTables.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('jquery-confirm-master-corrected/css/jquery-confirm.css') }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

  {{--  <!-- jQuery 2.2.3 -->
    <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    --}}{{--<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>--}}{{--
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <!-- Bootstrap 3.3.6 -->
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>

    --}}{{--   this is the file needed for angularJs for the application  --}}{{--
    <script src="{{ asset('/app/lib/angular/angular.min.js') }}"></script>
    <script src="{{ asset('/app/lib/angular/angular-animate.min.js') }}"></script>
    <script src="{{ asset('/app/lib/angular/angular-route.min.js') }}"></script>

    --}}{{--   AngularJS scripts --}}{{--
    <script src="{{ asset('/app/application.js') }}"></script>
    <script src="{{ asset('/app/controllers/book_inventory.js') }}"></script>--}}
    @yield('page_styles')
</head>
<style>
.sidebar-toggle {
    /* display: none; */
}
    .skin-blue .main-header .navbar .sidebar-toggle {
        color: rgb(210, 214, 222);
    }

    .skin-blue .main-header .navbar > a:hover,
    .skin-blue .main-header .navbar > a:active,
    .skin-blue .main-header .navbar > a:focus,
    .skin-blue .main-header .navbar > a,
    .skin-blue .main-header .navbar > a:hover,
    .skin-blue .main-header .navbar > a:focus,
    .skin-blue .main-header .navbar > .active > a {
        background: rgba(204, 204, 204, 0.12);
        color: rgb(210, 214, 222);
    }

    .skin-blue .main-header .navbar .sidebar-toggle {
        color: rgb(210, 214, 222);
    }

    .skin-blue .main-header .navbar .sidebar-toggle:hover {
        color: rgb(210, 214, 222);
        background: rgb(236, 240, 245);
    }

    @media (max-width: 767px) {
        .logo {
            height: 205px !important
        }
    }

</style>