<div class="modal-header">
     <button type="button" class="close" data-dismiss="modal">&times;</button>
     <h4 class="modal-title">Add New Roles</h4>
</div>
 <div class="modal-body">

    {!! Form::open(['url' => url('lccuser')."/".$user->id.'/manage_user_roles']) !!}
     <div class="row">
         <div class="col-md-6">
             <select name="roles[]" multiple style="width: 100%;" size="20" id="roles">
                 <?php
                 $userRoles = $user->roles;
                 $userRoleIds = array();
                 foreach($userRoles as $r){
                     $userRoleIds[] = $r->id;
                 }
                 ?>
                 @foreach(\Hamedmehryar\Laracancan\Models\Role::all() as $role)

                     {{$role->id."<br>"}}

                     @if(in_array($role->id, $userRoleIds))
                         <option class="role-user" value="{{$role->id}}" selected>{{$role->display_name}}</option>
                     @else
                         <option class="role-user" value="{{$role->id}}" >{{$role->display_name}}</option>
                     @endif
                 @endforeach

             </select>
         </div>
         <div class="col-md-6">

         </div>
     </div>
     <hr>

     <div class="form-group pull-right">
        <button class="btn btn-success notext large" type="submit"><i class="fa fa-save"></i></button>
     </div>

     {!! Form::close() !!}
     <br><br>
 </div>

