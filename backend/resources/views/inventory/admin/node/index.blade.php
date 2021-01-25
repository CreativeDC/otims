@extends('layouts.general')
@section('content')
    @isLogin
            <!-- MAIN CONTENT AND INJECTED VIEWS -->
    <div id="main">
        <!-- this is where content will be injected -->
        <div ng-view></div>
    </div>
    @endisLogin
@endsection
@section('page_scripts')
    {{-- Core AngularJS file for application --}}
    <script src="<?= asset('app/module/adminnode.module.js') ?>"></script>
    <script src="<?= asset('app/controllers/adminnodes.controller.js') ?>"></script>
    <script src="<?= asset('app/controllers/adminnode.controller.js') ?>"></script>
@stop
