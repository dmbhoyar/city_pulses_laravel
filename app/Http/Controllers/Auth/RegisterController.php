<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'email'         => 'required|string|email|max:255|unique:users',
            'mobile_number' => 'required|string|max:20',
            'role'          => 'required|in:normal,shopowner,shopworker,service_provider',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name'    => $validated['first_name'],
            'last_name'     => $validated['last_name'] ?? '',
            'email'         => $validated['email'],
            'mobile_number' => $validated['mobile_number'],
            'password'      => Hash::make($validated['password']),
            'role'          => $validated['role'],
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('home')->with('notice', 'Registration successful!');
    }
}
