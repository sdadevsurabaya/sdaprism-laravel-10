<?php
namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
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
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'roles_id.required' => 'Role wajib dipilih.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        RolesUser::create([
            'users_id' => $user->id,
            'roles_id' => $request->roles_id,
        ]);

        $role = Roles::find($request->roles_id);

        ActivityLogger::log(
            'User Management',
            'Create',
            "Menambahkan user baru: {$user->name} ({$user->email}) dengan Role: " . ($role?->name ?? '-'),
            ['new_values' => ['name' => $user->name, 'email' => $user->email, 'role' => $role?->name]]
        );

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
            'roles_user_id' => 'required|exists:roles_users,id',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.min'      => 'Password minimal 8 karakter.',
            'roles_id.required' => 'Role wajib dipilih.',
        ]);

        $user = User::findOrFail($id);
        $oldData = ['name' => $user->name, 'email' => $user->email];

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        $rolesUser           = RolesUser::findOrFail($request->roles_user_id);
        $rolesUser->roles_id = $request->roles_id;
        $rolesUser->save();

        $role = Roles::find($request->roles_id);

        ActivityLogger::log(
            'User Management',
            'Update',
            "Mengubah data user: {$user->name} ({$user->email})",
            [
                'old_values' => $oldData,
                'new_values' => ['name' => $user->name, 'email' => $user->email, 'role' => $role?->name, 'password_changed' => $request->filled('password')]
            ]
        );

        return redirect()->route('user.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $name = $user->name;
        $email = $user->email;

        RolesUser::where('users_id', $user->id)->delete();
        $user->delete();

        ActivityLogger::log(
            'User Management',
            'Delete',
            "Menghapus user: {$name} ({$email})",
            ['deleted_values' => ['name' => $name, 'email' => $email]]
        );

        return redirect()->route('user.index')
            ->with('success', 'User deleted successfully.');
    }
}
