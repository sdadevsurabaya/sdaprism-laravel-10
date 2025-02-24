<?php

namespace App\Http\Controllers\Back\OnepointUsers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('back.Onepoint_user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //tambahkan untuk role menggunakan role spatie
        $roles = Role::pluck('name', 'name')->all();

        return view('back.Onepoint_user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validasi inputan
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:password_confirmation',
            'roles' => 'required',
            'email_verified_at' => 'required',
        ]);

        //buat user baru
        $input = $request->all();
        // dd($input);
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        //tambahkan role
        $user->assignRole($request->input('roles'));

        //redirect ke halaman index
        return redirect()->route('onepoint_user.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $user = User::find($id);


        if (!$user) {
            return redirect()->route('onepoint_user.index')->with('error', 'User not found');
        }
        $roles = Role::all();

        return view('back.Onepoint_user.edit', compact('user', 'roles'));
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
        // Validate input
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|same:password_confirmation',
            'role' => 'required',
            'email_verified_at' => 'required'
        ]);

        // Find the user
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return redirect()->route('onepoint_user.index')->with('error', 'User not found');
        }

        // Update the user's name and email
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->email_verified_at = $request->input('email_verified_at');

        // Update the user's password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Update the user's role
        $user->syncRoles([$request->input('role')]);

        // Save the changes
        $user->save();

        // Redirect to the index page with a success message
        return redirect()->route('onepoint_user.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
