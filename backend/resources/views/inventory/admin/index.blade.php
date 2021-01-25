@extends('layouts.general')
@section('content')
    @isLogin
        <!-- MAIN CONTENT AND INJECTED VIEWS -->
    <div id="main">
        <!-- this is where content will be injected -->
        <section class="content">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="row" style="padding:10px; min-height:400px">
                        @canRead('book_dis_admin')
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box" style="background-color:#c00432;color:#fff">
                                <span class="info-box-icon"><span class="fa glyphicon glyphicon-sort-by-attributes" style="top:8px"></span></span>
                                <a href="{{url('ACRbookdis/admin/level')}}" style="color:white">
                                    <div class="info-box-content">
                                        <label >Node levels</label>
                                    </div>
                                </a>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        @endcanRead
                        @canRead('book_dis_admin')
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box" style="background-color:#c00432;color:#fff">
                                <span class="info-box-icon"><span class="fa glyphicon glyphicon-sort-by-attributes" style="top:8px"></span></span>
                                <a href="{{url('ACRbookdis/admin/node')}}" style="color:white">
                                    <div class="info-box-content">
                                        <label >Nodes</label>
                                    </div>
                                </a>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        @endcanRead
                    </div>
                </div>
            </div>
        </section>
    </div>
    @endisLogin
@endsection
@section('page_scripts')
    <script src="<?= asset('app/controllers/book.controller.js') ?>"></script>
    <script src="<?= asset('app/controllers/books.controller.js') ?>"></script>
@stop
