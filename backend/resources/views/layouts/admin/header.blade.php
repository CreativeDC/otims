<header class="main-header">


        <!-- Logo -->
        <!-- <a href="javascript:void(0)" class="logo">
            <span class="logo-mini"> <img src="/dist/img/logo.png" class="img-responsive"  width="32%"></span>
            <span class="logo-lg" style="margin:-10px 10px 50px -5px"> <img class="img-responsive" src="/dist/img/logo.png"  width="32%"></span>
        </a> -->

        <div class="logo" style="background-color: #222D32; border-bottom: 2px solid #1A2226;">
<!-- <span class="logo-lg">OTIMS</span> -->
<!-- <span class="logo-mini" style="font-size: 12px;">OTIMS</span> -->

        <span class="logo-lg">
            <div class="pull-left info" style="width: 104%; height: 100%; margin-left: -9px;">
                <div class="form-group" >

                        <?php
$locale = App::getLocale();
?>

                        <select class="form-control form-control-lg" style="width: 100%; margin-top: 10px; margin-left: 5px;" onchange="changeLanguage('{{url('/language')}}' + '/' + this.value)">
                            <option value="en" @if($locale == "en") selected @endif>English</option>
                            <option value="ps" @if($locale == "ps") selected @endif>پښتو</option>
                            <option value="fa" @if($locale == "fa") selected @endif>دری</option>
                        </select>
                </div>
            </div>
    </span>


</div>


        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            @isLogin
			<a href="javascript:void(0)" class="sidebar-toggle" data-toggle="offcanvas" role="button" id="collapse_me">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
			@endisLogin
            <div class="pull-left info" style="" id="acr-title">
			    <span class="logo-mini" style=""><img class="img-responsive" src="{{ asset('dist/img/logo.png') }}" style="float: left;" width="18%"></span>
                <!-- <h4 style="color: #002a6c;font-weight:700;font-family:inherit" id="acr-title-h4">@lang('home.ministry')</h4>                 -->
            </div>


            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    <!-- Messages: style can be found in dropdown.less-->
                    @isLogin
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu" >
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" style="color: #000000;">
                            <img src="{{ asset('dist/img/avatar04.png') }}" class="user-image" alt="User Image">
                            <span class="hidden-xs">{!! Auth::user()->name !!}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="{{ asset('dist/img/avatar04.png') }}" class="img-circle" alt="User Image">

                                <label style="color: #000000;">{!! Auth::user()->name !!} -
                                    <small>{!! Auth::user()->email !!}</small>
                                </label>
                            </li>

                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="{{url('logout')}}" class="btn btn-default btn-flat" style="color: #000000;">Log out</a>
                                </div>

                            </li>
                        </ul>
                    </li>
                    @endisLogin
                    @isLogout
                    <li><a href="{{ url('/login') }}" style="color: #000000;">
                                <b>@lang('home.login')</b>
                            </a></li>
                    @endisLogout

                </ul>
            </div>
        </nav>
    </header>

    <script>

function changeLanguage(lang)
{
    console.log(lang);
    window.location.assign(lang);
}
    </script>