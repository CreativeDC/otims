
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        @include('layouts.admin.user')
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">@lang('home.main_navigation')</li>
            @if(Auth::check())
                {{--  Book distribution Start  --}}

                <?php if(Auth::user()->canRead('book_dis_admin')):?>
                <li class="treeview">
                    <a href="{{url('ACRbookdis/admin')}}">
                        <i class="fa fa-bar-chart"></i> <span>@lang('home.book_settings')</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if(Auth::user()->canRead('book_dis_admin')):?>
                <li class="treeview">
                    <a href="{{url('ACRbookdis/dashboard')}}">
                        <i class="fa fa-bar-chart"></i> <span>@lang('home.book_dashboard')</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if(Auth::user()->canRead('book_dis_admin')):?>
                <li class="treeview">
                    <a href="{{url('ACRbookdis/admin/report')}}">
                        <i class="fa fa-bar-chart"></i> <span>@lang('home.book_report')</span>
                    </a>
                </li>
                <?php endif; ?>


                <?php if(Auth::user()->canRead('book_distribution_staff')):?>
                <li class="treeview">
                    <a href="{{url('ACRbookdis/record')}}">
                        <i class="fa fa-bar-chart"></i> <span>@lang('home.book_distribution')</span>
                    </a>
                </li>
                <?php endif; ?>


                {{--  Book distribution End  --}}

                <?php //if(laracancan::canCreate("approve")):?>
                    <!--li class="treeview">
                        <a href="{{url('ACRapprove/index')}}">
                            <i class="fa fa-list"></i> <span>Not Approved list</span>
                        </a>
                    </li-->
                <?php //endif; ?>

            @endif

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>