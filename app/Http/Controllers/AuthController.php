<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return back()
                ->with('error', 'Email atau password salah')
                ->withInput($request->only('email'));
        }

        $user = auth()->guard('api')->user();
        $cookie = cookie('token', $token, 60, null, null, false, true);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->withCookie($cookie);
        }

        return redirect()->route('home')->withCookie($cookie);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ], [
            'email.unique' => 'Email sudah digunakan, silakan gunakan email lain',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'name.required' => 'Nama wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $user = User::create([
            'username' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pelanggan',
        ]);

        $token = JWTAuth::fromUser($user);
        $cookie = cookie('token', $token, 60, null, null, false, true);

        return redirect()->route('home')->withCookie($cookie);
    }

    public function logout()
    {
        auth()->guard('api')->logout();
        return redirect('/')->withoutCookie('token');
    }
}
