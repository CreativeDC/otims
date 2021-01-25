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
    <script src="<?= asset('app/controllers/books.controller.js') ?>"></script>
@stop
