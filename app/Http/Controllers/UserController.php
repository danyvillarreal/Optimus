<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $users = DB::table('users')
            ->where('users.organization_id', $user->organization_id)
            ->paginate(10);
        return view('user.index', ['users'=> $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = \App\Role::all(['id', 'name']);
        return view('user.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|max:255',
            'name' => 'required|max:255',
        ]);
        $userLogin = auth()->user();
        $user = new \App\User;
        $user->first_name=$request->get('name');
        $user->email=$request->get('email');
        $user->password=$request->get('password');
        $user->organization_id=$userLogin->organization_id;
        $user->save();
        return redirect('users')->with('success', 'Information has been added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userLogin = auth()->user();
        $user = \App\User::find($id);
        if ($user->organization_id == $userLogin->organization_id) {
            return view('user.show',compact('user'));
        } else {
            return redirect('/home');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userLogin = auth()->user();
        $user = \App\User::find($id);
        if ($user->organization_id == $userLogin->organization_id) {
            $userRoles = DB::table('role_users')->where('user_id', $id)->get();
            $currentRoles = [];
            foreach ($userRoles as $role) {
                array_push($currentRoles, $role->role_id);
            }
            // echo "<pre>";
            // var_dump($currentRoles);
            // echo "</pre>";
            $roles = \App\Role::all(['id', 'name']);
            return view('user.edit',compact('user','id','roles','currentRoles'));
        } else {
            return redirect('/home');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userLogin = auth()->user();
        $user= \App\User::find($id);
        $message = 'Success';
        if ($user->organization_id == $userLogin->organization_id) {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'name' => 'required|max:255',
            ]);
            if ($request->get('role') !== null) {
                if ($request->get('email') !== $user->email) {
                    $alreadyExists = DB::table('users')
                        ->where('email', $request->get('email'))->first();
                    if ($alreadyExists) {
                        $message = 'This email already exists';
                    } else {
                        $user->email=$request->get('email');
                    }
                }
                $user->name=$request->get('name');

                $userRoles = DB::table('role_users')
                    ->where('role_users.user_id', $user->id)->get();
                $currentRoles = [];
                foreach ($userRoles as $roleUser) {
                    $currentRoles[$roleUser->id] = $roleUser->role_id;
                }
                foreach ($request->get('role') as $roleId) {
                    $role = DB::table('roles')
                        ->where('roles.name', $roleId)->first();
                    $newRoles[] = $role->id;
                    if (!array_search($role->id, $currentRoles)) {
                        $roleUser= new \App\RoleUser;
                        $roleUser->role_id=$role->id;
                        $roleUser->user_id=$user->id;
                        $roleUser->save();
                    }
                }
                foreach ($currentRoles as $key => $roleId) {
                    if (!in_array($roleId, $newRoles)) {
                        $newRole = \App\RoleUser::find($key);
                        $newRole->delete();
                    }
                }
                // $user->password=$request->get('password');
                $user->save();
            } else {
                return back()->with('errors', collect(['roleRequired' => 'You must select at least a role']));
            }
        } else {
            $message = 'You are not authorized to performe this action';
        }
        return redirect('users')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userLogin = auth()->user();
        $user = \App\User::find($id);
        if ($user->organization_id == $userLogin->organization_id) {
            try {
                $user->delete();
            } catch (\Exception $e) {
                $user->status=0;
                $user->save();
            }
        }
        return redirect('users')->with('success','Information has been deleted');
    }
}
