<?php
namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('login');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // dump($credentials);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            ActivityLogger::log('Authentication', 'Failed Login', "Percobaan login gagal untuk email: {$credentials['email']}", ['status' => 'failed', 'email' => $credentials['email']]);
            return back()->withErrors(['email' => 'Login failed']);
        }

        Auth::login($user);
        ActivityLogger::log('Authentication', 'Login', "User {$user->name} ({$user->email}) berhasil login.", ['status' => 'success'], $user);

        return redirect()->route('dashboard')->with('login', 'Login Successfully.');
    }

    public function loginas(Request $request)
    {
        $id = $request->id;
        $adminUser = Auth::user();
        Auth::guard('web')->logout();
        $user = User::find($id);

        if ($user) {
            Auth::login($user);
            ActivityLogger::log('Authentication', 'Login As', "Admin " . ($adminUser?->name ?? '') . " login sebagai {$user->name} (ID: {$user->id}).", ['status' => 'success', 'target_user_id' => $user->id, 'target_user_name' => $user->name], $user);
            return redirect()->route('dashboard')->with('login', "Login As {$user->name} Successfully.");
        } else {
            ActivityLogger::log('Authentication', 'Failed Login As', "Gagal login sebagai user ID: {$id}", ['status' => 'failed', 'target_user_id' => $id], $adminUser);
            return back()->withErrors([
                'id' => 'The provided credentials do not match our records.',
            ]);
        }
    }

    public function logout()
    {
        $currentUser = Auth::user();
        if ($currentUser) {
            ActivityLogger::log('Authentication', 'Logout', "User {$currentUser->name} logout dari sistem.", ['status' => 'success'], $currentUser);
        }
        Auth::guard('web')->logout();            // atau 'admin', 'user', dll
        request()->session()->invalidate();      // optional, recommended
        request()->session()->regenerateToken(); // optional, recommended

        return redirect()->route('login');
    }

}
