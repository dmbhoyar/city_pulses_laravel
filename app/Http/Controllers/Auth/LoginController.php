<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        $redirectTo = (string) request()->query('redirect_to', '');
        $lockSellerRole = request()->boolean('seller');

        return view('auth.login', compact('redirectTo', 'lockSellerRole'));
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'redirect_to' => 'nullable|string|max:2048',
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $redirectTo = trim((string) ($validated['redirect_to'] ?? ''));

            if ($redirectTo !== '') {
                return redirect()->to($redirectTo)->with('notice', 'Welcome back!');
            }

            // Determine default route based on user role
            $defaultRoute = route('home');
            if ($user && method_exists($user, 'isSuperadmin') && $user->isSuperadmin()) {
                $defaultRoute = route('admin.dashboard');
            } elseif ($user && method_exists($user, 'isShopowner') && $user->isShopowner()) {
                $defaultRoute = route('myshop');
            } elseif ($user && method_exists($user, 'isServiceProvider') && $user->isServiceProvider()) {
                $defaultRoute = route('myservice');
            }

            return redirect()->intended($defaultRoute)->with('notice', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'remember', 'redirect_to'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('offers');
    }
}
