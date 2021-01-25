<ul class="nav">
        <li class="nav-heading ">
                <span>Navigation</span>
        </li>

    @canRead('role')
        <li class="">
                <a href="{{url('lccrole')}}" title="Roles">
                        <em class="fa fa-user"></em>
                        <span>Roles</span>
                </a>
        </li>
    @endcanRead
    @canRead('resource')
        <li class="">
                <a href="{{url('lccresource')}}" title="Resources">
                        <em class="fa fa-database"></em>
                        <span>Resources</span>
                </a>
        </li>
    @endcanRead
    @canRead('permission')
        <li class="">
                <a href="{{url('lccpermission')}}" title="Permissions">
                        <em class="fa fa-key"></em>
                        <span>Permissions</span>
                </a>
        </li>
    @endcanRead
    @canRead('user')
    <li class="">
        <a href="{{url('lccuser')}}" title="Users">
            <em class="fa fa-key"></em>
            <span>Users</span>
        </a>
    </li>
    @endcanRead
</ul>