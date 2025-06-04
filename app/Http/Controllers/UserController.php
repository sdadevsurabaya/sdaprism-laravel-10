<?php
namespace App\Http\Controllers;

use App\Models\Roles;
use App\Models\RolesUser;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data  = User::all();
        $roles = Roles::all();
        return view('user.CreateUser', compact('data', 'roles'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'roles_id' => 'required|exists:roles,id',
        ]);

        $userId = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        RolesUser::create([
            'users_id' => $userId->id,
            'roles_id' => $request->roles_id,
        ]);

        return redirect()->route('user.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $id,
            'password'      => 'nullable|string|min:8',
            'roles_id'      => 'required|exists:roles,id',
            'roles_user_id' => 'required|exists:roles_users,id', // tambahkan ini
        ]);

        $user = User::findOrFail($id);

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        // Update roles_users by its id
        $rolesUser           = RolesUser::findOrFail($request->roles_user_id);
        $rolesUser->roles_id = $request->roles_id;
        $rolesUser->save();

        return redirect()->route('user.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Hapus semua roles_users yang terkait
        RolesUser::where('users_id', $user->id)->delete();

        // Hapus user
        $user->delete();

        return redirect()->route('user.index')
            ->with('success', 'User deleted successfully.');
    }

}
