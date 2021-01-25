<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Add New Role</h4>
</div>
<div class="modal-body">

    {!! Form::open(['route' => 'lccuser.store']) !!}
    <div class="form-group">
        <label>Name:<span style="color:red; margin-left:2px;" >*</span></label>
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        <label>email<span style="color:red; margin-left:2px;" >*</span></label>
        {!! Form::text('email', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        <label>Password</label>
        {!! Form::password('password', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        <label>Confirm Password</label>
        {!! Form::password('password_confirmation', null, ['class' => 'form-control']) !!}
    </div>

    <hr>

    <div class="form-group pull-right">
        <button class="btn btn-success notext large" type="submit" id="submit_user_btn" ><i class="fa fa-save"></i></button>
    </div>
    {!! Form::close() !!}
    <br><br>

</div>