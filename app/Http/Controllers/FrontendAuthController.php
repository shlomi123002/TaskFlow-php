<?php
namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|confirmed',
        ]);

        [$user, $token] = $this->authService->register($data['name'], $data['email'], $data['password']);
        Auth::login($user);

        return redirect('/home')->with('success', 'Registered and logged in. Welcome to TaskFlow!');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            [$user, $token] = $this->authService->login($data['email'], $data['password']);
            Auth::login($user);
            return redirect('/home')->with('success', 'Logged in successfully. Welcome back!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $this->authService->logout($user);
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/')->with('success', 'Logged out.');
    }
}
