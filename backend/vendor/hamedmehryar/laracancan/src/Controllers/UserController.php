<?php namespace Hamedmehryar\Laracancan\Controllers;

use App\User;
use Hamedmehryar\Laracancan\Models\Resource;
use Hamedmehryar\Laracancan\Models\Role;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    protected $user;
    public function construct()
    {
        $this->user = Auth::user();
    }
    /**
     * Display a listing of the Users.
     *
     * @return Response
     */
    public function index()
    {
        if (Auth::user() && Auth::user()->id == Config::get('laracancan.super_admin')) {
            $users = User::all();
            return view('laracancan::user.list')
                ->with('users', $users);
        }
        return response(view('laracancan::master.401'), 401);
    }

    /**
     * Show the form for creating a new User.
     *
     *
     * @return Response
     */
    public function create()
    {
        if (Auth::user() && Auth::user()->id == Config::get('laracancan.super_admin')) {
            return view('laracancan::user.add');
        }
        return response(view('laracancan::master.401'), 401);
    }
    /**
     * Store a newly created User in storage.
     *
     * @return Response
     */
    public function store()
    {
        if (Auth::user() && Auth::user()->id == Config::get('laracancan.super_admin')) {

            $data = Input::all();
            $rules = [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|min:6|confirmed',
            ];
            $validator = Validator::make($data, $rules);
            $error_msg = $validator->errors();
            if (count($error_msg) != 0) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            //User Creation
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);
            return redirect()->back()->with('flash_success', 'User added Successfully!');
        }
        return response(view('laracancan::master.401'), 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        // left Blank intentionaly
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (Auth::user() && Auth::user()->id == Config::get('laracancan.super_admin')) {

            $user = User::find($id);
            return view('laracancan::user.edit')
                ->with('user', $user);

        }
        return response(view('laracancan::master.401'), 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        if (Auth::user() && Auth::user()->id == Config::get('laracancan.super_admin')) {
            $data = Input::all();
            $rules = [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'password' => 'required|min:6|confirmed',
            ];
            $validator = Validator::make($data, $rules);
            $error_msg = $validator->errors();
            if (count($error_msg) != 0) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $user = User::where('email', '=', $data['email'])->where('id', '!=', $id)->get();
            if (count($user) > 0) {
                return redirect()->back()->with('flash_error', 'User already exists!');
            }
            $user = User::findOrFail($id);

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();
            return redirect()->back()->with('flash_success', 'User Edited edited Successfully !');

        }

        return response(view('laracancan::master.401'), 401);
    }

    /**
     * Trash the specified User from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (Auth::user() && Auth::user()->id == Config::get('laracancan.super_admin')) {

            $user = User::find($id);

            if ($user->id == Config::get('laracancan.super_admin')) {
                return redirect()->back()->with('flash_error', 'This User is Main Super Admin ,so it can not be trashed!');
            }

            User::destroy($id);
            return redirect()->back()->with('flash_success', 'User trashed successfully!');

        }

        return response(view('laracancan::master.401'), 401);
    }
    /**
     * mange the specified user's from roles.
     *
     * @param  int  $id
     * @return Response
     */
    public function manageRoleUsers($id)
    {
        if (Auth::user() && Auth::user()->id == Config::get('laracancan.super_admin')) {
            $user = User::find($id);
            return view('laracancan::user.manage_roles')
                ->with('user', $user);

        }

        return response(view('laracancan::master.401'), 401);
    }
    /**
     *
     * POST mange the specified user's from roles.
     *
     * @param  int  $id
     * @return Response
     */
    public function manageRoleUsersAction($id)
    {

        if (Auth::user() && Auth::user()->id == Config::get('laracancan.super_admin')) {

            $user = User::find($id);
            $user->roles()->detach();
            $selected_role_ids = Input::get("roles");

            foreach ($selected_role_ids as $role_id) {
                $role = Role::where('id', '=', $role_id)->first();
                if ($role != null) {
                    $user->roles()->attach([$role_id]);
                }
            }
            return redirect()->back()->with('flash_success', 'Roles assigned successfully!');
        }
        return response(view('laracancan::master.401'), 401);
    }
}
