@extends('layouts.general')
@section('content')
    @isLogin
        <!-- MAIN CONTENT AND INJECTED VIEWS -->
    <div id="main"> </div>
        {{--attaching test id from controller to view --}}
        <input type="text"  name="is_parent" ng-model="is_parent" style="display: none;" ng-init="is_parent = '{!!$is_parent!!}'"/>
        <input type="text"  name="has_parent" ng-model="has_parent" style="display: none;" ng-init="has_parent = '{!!$has_parent!!}'"/>
        <input type="text"  name="has_child" ng-model="has_child" style="display: none;" ng-init="has_child = '{!!$has_child!!}'"/>

        @php
            $locale = App::getLocale();
        @endphp

        <input type="text" id="locale_string" name="locale_string" ng-model="locale_string" style="display: none;" ng-init="locale_string = '{{$locale}}'">


        <div ng-view>

        <!-- this is where content will be injected -->
            <section class="content">

                <div class="row">
                    <!-- Left col -->
                    <div class="col-md-8">

                    </div>

                </div>


            </section>
        </div>
    </div>
    @endisLogin
@endsection
@section('page_scripts')
    {{-- Core AngularJS file for application --}}
    <script src="<?= asset('app/module/adminreport.module.js') ?>"></script>
    <script src="<?= asset('app/controllers/adminreport.controller.js') ?>"></script>
@stop