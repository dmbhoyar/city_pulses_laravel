<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $users = User::orderByDesc('created_at')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile_number' => 'nullable|string|max:20',
            'role' => 'required|string|in:normal,shopowner,service_provider,shopworker,superadmin,admin',
            'password' => 'required|string|min:6|max:255',
        ]);

        User::create([
            'first_name' => $validated['first_name'],
            'last_name' => trim((string) ($validated['last_name'] ?? '')),
            'email' => $validated['email'],
            'mobile_number' => trim((string) ($validated['mobile_number'] ?? '')),
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('notice', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:20',
            'role' => 'required|string|in:normal,shopowner,service_provider,shopworker,superadmin,admin',
            'password' => 'nullable|string|min:6|max:255',
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = trim((string) ($validated['last_name'] ?? ''));
        $user->email = $validated['email'];
        $user->mobile_number = trim((string) ($validated['mobile_number'] ?? ''));
        $user->role = $validated['role'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect()->route('admin.users.index')->with('notice', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('notice', 'User removed.');
    }
}
