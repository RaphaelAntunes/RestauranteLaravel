<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isGarcom()) {
                return redirect()->route('garcom.index');
            } elseif ($user->isCozinha()) {
                return redirect()->route('cozinha.index');
            } elseif ($user->isCaixa()) {
                return redirect()->route('pdv.index');
            }

            return redirect()->route('home');
        }
        return view('auth.login');
    }

    /**
     * Processa o login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'O email é obrigatório',
            'email.email' => 'Digite um email válido',
            'password.required' => 'A senha é obrigatória',
        ]);

        // Buscar usuário
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->senha)) {
            return back()->withErrors([
                'email' => 'As credenciais não correspondem aos nossos registros.',
            ])->onlyInput('email');
        }

        if (!$user->ativo) {
            return back()->withErrors([
                'email' => 'Sua conta está inativa. Entre em contato com o administrador.',
            ])->onlyInput('email');
        }

        // Atualizar último acesso
        $user->update(['ultimo_acesso' => now()]);

        // Fazer login
        Auth::login($user, $request->filled('remember'));

        $request->session()->regenerate();

        // Redirecionar baseado na role
        $defaultRoute = 'home';

        if ($user->isGarcom()) {
            $defaultRoute = 'garcom.index';
        } elseif ($user->isCozinha()) {
            $defaultRoute = 'cozinha.index';
        } elseif ($user->isCaixa()) {
            $defaultRoute = 'pdv.index';
        }

        return redirect()->intended(route($defaultRoute))->with('success', 'Login realizado com sucesso!');
    }

    /**
     * Faz logout do usuário
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout realizado com sucesso!');
    }
}
