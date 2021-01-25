@extends('laracancan::master.master')
@section('content')
    @include('laracancan::master.error_list')
            <div class="row">
                <div class="col-md-6">
                    <h2>Users</h2>
                </div>
                <div class="col-md-6">
                    <button href="#" id="add_user_btn" class="btn btn-danger bottom_buttons notext large"><i class="fa fa-plus"></i></button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <ol class="breadcrumb" class="pull-right">
                      <li class="active"><i class="fa fa-user"></i>&nbsp;Roles</li>
                    </ol>
                </div>
            </div>
             <div class="row">
                 <div class="col-md-12">
                     <div class="panel panel-promote">
                         <!-- /.panel-heading -->
                         <div class="panel-body">
                             <!--nav tabs-->
                             <ul class="nav nav-tabs nav-justified">
                                 <li role="presentation" class="active"><a href="#active" data-toggle="tab">All({{$users->count()}})</a></li>
                             </ul>
                             <!--end-->
                             <div class="tab-content">
                                 <!--first tab content-->
                                 <div class="tab-pane active" id="active">
                                     <table class="table table-striped table-hover">
                                         <thead>
                                         <tr>
                                             <th>#</th>
                                             <th>Name</th>
                                             <th>Email Address</th>
                                             <th>Creation Date</th>
                                             <th>Action</th>
                                         </tr>
                                         </thead>
                                         <tbody>
                                         <?php $i =1; ?>
                                         @foreach($users as $user)
                                             <tr class="gradeA">
                                                 <td>{{$i++}}</td>
                                                 <td>{{$user->name}}</td>
                                                 <td>{{$user->email}}</td>
                                                 <td>{{str_limit($user->created_at, 50)}}</td>
                                                 <td align="center">

                                                     <div class="btn-group">
                                                         <button type="button" class="btn btn-xs btn-primary dropdown-toggle notext small" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-tasks"></span>
                                                         </button>
                                                         <ul class="dropdown-menu pull-right">
                                                             <li><a class="edit_user" user-id="{{$user->id}}"><i class="fa fa-edit"></i> Edit {{$user->name}}</a></li>
                                                             <li><a class="manag_user_roles" user-id="{{$user->id}}"><i class="fa fa-key"></i> Manage {{$user->name}}'s Roles</a></li>
                                                             <li><a class="delete_user" user-id="{{$user->id}}" href="#" ><i class="fa fa-trash-o"></i> Delete {{$user->name}}</a></li>
                                                             {!!Form::open(array('route' => array('lccuser.destroy', $user->id), 'method' => 'delete', 'id'=>'delete_user_'.$user->id))!!}
                                                             {!!Form::close()!!}
                                                         </ul>
                                                     </div>
                                                 </td>
                                             </tr>
                                         @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                                 <!--end-->
                             </div>
                         </div>
                         <!-- /.panel-body -->
                     </div>
                 </div>
             </div>
            <!-- /.col-lg-12 -->
@stop
@section('page_specific_scripts')
    <script src="{{ asset('hamedmehryar/laracancan/jquery.confirm-master/jquery.confirm.min.js') }}"></script>
    <script>
        $('#add_user_btn').click(function(){
            showModalUntilAjaxResponse("general_modal");
            getContentWithAjax("{{url('lccuser/create')}}", "modal-content", true);
        });

        $(".edit_user").click(function(){
            showModalUntilAjaxResponse("general_modal");
            getContentWithAjax("{{url('lccuser')}}/"+$(this).attr('user-id')+"/edit", "modal-content", true);
        });

        $(".manag_user_roles").click(function(){
            showModalUntilAjaxResponse("general_modal");
            getContentWithAjax("{{url('lccuser')}}/"+$(this).attr('user-id')+"/manage_user_roles", "modal-content", true);
        });

        $(".delete_user").confirm({
            text: "Are You Sure You Want To Delete This Item?",
            title: "Confirmation Required",
            confirm: function(button) {
                $('#delete_user_'+$(button).attr('user-id')).submit();
            },
            cancel: function(button) {
                // nothing to do
            },
            confirmButton: "",
            cancelButton: "",
            post: true,
            confirmButtonClass: "btn-success notext large fa fa-check",
            cancelButtonClass: "btn-danger notext large fa fa-close",
            dialogClass: "modal-dialog modal-lg" // Bootstrap classes for large modal
        });

    </script>
    <script>
        $(document).ready(function(){
            $('.table').dataTable();
        });
    </script>
@stop