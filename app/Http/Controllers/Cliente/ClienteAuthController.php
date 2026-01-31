<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteAuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function showLoginForm()
    {
        return view('cliente.auth.login');
    }

    public function enviarOtp(Request $request)
    {
        $request->validate([
            'celular' => ['required', 'string', 'min:10', 'max:15'],
        ], [
            'celular.required' => 'O número de celular é obrigatório.',
            'celular.min' => 'O celular deve ter pelo menos 10 dígitos.',
        ]);

        // Normalizar o celular (remover formatação se houver)
        $celular = preg_replace('/[^0-9]/', '', $request->celular);

        $this->otpService->enviarCodigo($celular, $request->ip());

        session(['otp_celular' => $celular]);

        return redirect()->route('cliente.otp.form')->with('success', 'Código enviado com sucesso! Verifique seu celular.');
    }

    public function showVerificarOtpForm()
    {
        if (!session('otp_celular')) {
            return redirect()->route('cliente.login')->with('error', 'Sessão expirada. Por favor, solicite um novo código.');
        }

        return view('cliente.auth.verificar-otp');
    }

    public function verificarOtp(Request $request)
    {
        $request->validate([
            'codigo' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ], [
            'codigo.required' => 'O código é obrigatório.',
            'codigo.size' => 'O código deve ter 6 dígitos.',
            'codigo.regex' => 'O código deve conter apenas números.',
        ]);

        $celular = session('otp_celular');

        if (!$celular) {
            return redirect()->route('cliente.login')->with('error', 'Sessão expirada. Por favor, solicite um novo código.');
        }

        if ($this->otpService->validarCodigo($celular, $request->codigo)) {
            $cliente = Cliente::firstOrCreate(
                ['celular' => $celular],
                ['nome' => 'Cliente ' . substr($celular, -4), 'ativo' => true]
            );

            Auth::guard('cliente')->login($cliente, true);

            $cliente->update(['ultimo_acesso' => now()]);

            session()->forget('otp_celular');

            return redirect()->route('cliente.cardapio')->with('success', 'Login realizado com sucesso!');
        }

        return back()->with('error', 'Código inválido ou expirado. Tente novamente.');
    }

    public function logout()
    {
        Auth::guard('cliente')->logout();
        return redirect()->route('cliente.login')->with('success', 'Logout realizado com sucesso!');
    }
}
