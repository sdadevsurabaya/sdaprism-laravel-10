<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Roles::all();
        return view('user.roles-list-create-edit', compact('data'));
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
            'name' => 'required|string|max:255',
        ]);

        Roles::create([
            'name' => $request->name,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Roles created successfully.');
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
            'name' => 'required|string|max:255',
        ]);

        $role = Roles::findOrFail($id);
        $role->update(['name' => $request->name]);

        return redirect()->route('roles.index')
            ->with('success', 'Roles created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $roles = Roles::findOrFail($id);
        $roles->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Roles deleted successfully.');
    }
}
