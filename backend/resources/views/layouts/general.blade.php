<!DOCTYPE html>
<html ng-app="BookDistribution">
{{--  This part includes all css and style needed for application--}}
@include('layouts.admin.head')
    <body class="hold-transition skin-blue sidebar-mini">
        <!-- Site wrapper -->
        <div class="wrapper">
            <div id="content-overlay"></div>
            <div style="position:fixed;width:100%;z-index:10;">
                @include('layouts.admin.header')
                <!-- =============================================== -->
                @isLogin
                @include('layouts.admin.sidebar')
                @endisLogin
            </div>
            <!-- =============================================== -->

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper" style="margin-top:50px">
                <!-- Content Header (Page header) -->
                @yield('content')
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <footer class="main-footer">
                <strong>Copyright &copy; {{ date('Y') }}, Ministry of Education.</strong> All Rights
                Reserved.
            </footer>

            <!-- Add the sidebar's background. This div must be placed
                 immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
        </div>
        <!-- ./wrapper -->


   {{-- <!-- general modal starts here -->
    <div id="general_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <!-- modal ends here -->
    <div id="timeoutOops" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Oops!</h4>
                </div>
                <div class="modal-body">
                    <p id="info">Your internet connection might be too slow or you might be disconnected. Please contact
                        your service provider.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="no" class="btn btn-success notext large" data-dismiss="modal"><i
                                class="fa fa-check"></i></button>
                </div>
            </div>
        </div>
    </div>--}}

        <script src="<?=asset('js/jquery.min.js')?>"></script>

    <!-- Morris.js charts -->
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>--}}
    <script src="{{ asset('plugins/morris/morris.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('plugins/sparkline/jquery.sparkline.min.js') }}"></script>
    <!-- jvectormap -->
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('plugins/knob/jquery.knob.js') }}"></script>
    <!-- daterangepicker -->
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>--}}
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- datepicker -->
    <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="{{ asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <!-- Slimscroll -->
    <script src="{{ asset('plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('plugins/fastclick/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/app.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <script src="{{ asset('dist/js/pace.min.js')}}"></script>
    <script src="{{ asset('jquery.confirm-master/jquery.confirm.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/select2.full.min.js') }}"></script>
    <!-- <script src="{{ asset('js/jquery.aCollapTable.min.js') }}"></script> -->
    <script src="{{ asset('jquery-confirm-master-corrected/js/jquery-confirm.js') }}"></script>
    <!-- <script src="{{ asset('js/jquery.freezeheader.js') }}"></script>
    <script src="{{ asset('js/jquery.floatThead.js') }}"></script> -->

    <!-- Load Javascript Libraries (AngularJS, JQuery, Bootstrap) -->
    <script src="<?=asset('js/angular/angular.min.js')?>"></script>
    <script src="<?=asset('js/angular/angular-route.min.js')?>"></script>
    <script src="<?=asset('js/angular/angular-animate.min.js')?>"></script>
    <script src="<?=asset('js/angular/angular-messages.min.js')?>"></script>
    <script src="<?=asset('js/angucomplete-alt.js')?>"></script>
    <script src="<?=asset('js/angular-flash.js')?>"></script>
    <link rel="stylesheet" href="{{ asset('js/angular-flash.css') }}">
    <link rel="stylesheet" href="{{ asset('js/angucomplete-alt.css') }}">
    <link rel="stylesheet" href="{{ asset('js/angular/ng-animation.css') }}">
    <script src="<?=asset('js/bootstrap.min.js')?>"></script>

    <script src="<?=asset('js/ng-file-upload-shim.min.js')?>"></script>
    <script src="<?=asset('js/ng-file-upload.min.js')?>"></script>







    @yield('page_scripts')

    <script>
        (function ($) {
            //Initialize Select2 Elements
            $('form').submit(function () {
                $(this).find("button[type='submit']").prop('disabled', true);
            });
            $(".datepick").datepicker();
        })(jQuery);
    </script>

    </body>

</html>
