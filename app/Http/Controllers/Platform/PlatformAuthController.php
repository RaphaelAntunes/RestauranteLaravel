<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PlatformAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::user()?->is_platform_admin) {
            return redirect()->route('platform.lojas.index');
        }

        return view('platform.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::withoutGlobalScopes()
            ->where('email', $credentials['email'])
            ->where('is_platform_admin', true)
            ->first();

        if (!$user || !$user->ativo || !Hash::check($credentials['password'], $user->senha)) {
            return back()->withErrors(['email' => 'Acesso inválido para a gestão da plataforma.'])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('platform.lojas.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('platform.login');
    }
}
