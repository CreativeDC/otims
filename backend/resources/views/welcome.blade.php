@extends('layouts.general')

@section('content')
    @isLogin
    <script >
        window.location.href = window.location.href + "lcc"
</script>
    @endisLogin
	<script >
        window.location.href = window.location.href + "login"
    </script>
@endsection
