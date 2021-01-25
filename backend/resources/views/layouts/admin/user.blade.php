@isLogin

    <div class="user-panel">
        <div class="pull-left image">
            <img src="{{ asset('/dist/img/avatar04.png') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info" style="margin-top: 6px;">
            <p><font color="white">{!! Auth::user()->name !!}</font> </p>
        </div>
    </div>
    @endisLogin
    <!-- search form -->
