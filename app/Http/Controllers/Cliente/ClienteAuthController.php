<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\CarrinhoItem;
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

    public function showLoginForm(Request $request)
    {
        if ($request->query('origem') === 'checkout') {
            $request->session()->put('url.intended', route('cliente.cardapio', [
                'checkout' => 1,
            ]));
        }

        return view('cliente.auth.login', ['demo' => \App\Support\EntradaDemonstracao::ativa()]);
    }

    public function enviarOtp(Request $request)
    {
        $request->validate([
            'celular' => ['required', 'string', 'max:20'],
        ], [
            'celular.required' => 'O número de celular é obrigatório.',
        ]);

        $celular = preg_replace('/\D/', '', $request->celular);

        if (!preg_match('/^\d{10,15}$/', $celular)) {
            return back()->withErrors(['celular' => 'Informe um celular com DDD e de 10 a 15 números.'])->withInput();
        }

        if (\App\Support\EntradaDemonstracao::ativa()) {
            // Keep unverified visits isolated, even when the phone matches an existing customer.
            $cliente = Cliente::create([
                'celular' => '~' . bin2hex(random_bytes(7)),
                'nome' => 'Visitante',
                'ativo' => true,
            ]);
            $request->session()->put('demo_cliente_celular', $celular);
            return $this->entrar($request, $cliente, false);
        }

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

            if (!$cliente->ativo) {
                return back()->with('error', 'Este cadastro está temporariamente indisponível.');
            }

            return $this->entrar($request, $cliente, true);
        }

        return back()->with('error', 'Código inválido ou expirado. Tente novamente.');
    }

    private function entrar(Request $request, Cliente $cliente, bool $lembrar)
    {
        Auth::guard('cliente')->login($cliente, $lembrar);

        // Preserve items added before login so the customer can finish checkout.
        $carrinhoSessionId = session('carrinho_session_id');
        if ($carrinhoSessionId) {
            CarrinhoItem::query()
                ->whereNull('cliente_id')
                ->where('session_id', $carrinhoSessionId)
                ->update(['cliente_id' => $cliente->id]);
        }

        $cliente->update(['ultimo_acesso' => now()]);

        session()->forget('otp_celular');
        $request->session()->regenerate();

        return redirect()->intended(route('cliente.cardapio', [
            'loja' => $request->segment(2),
        ]))->with('success', 'Login realizado com sucesso!');
    }

    public function logout(Request $request)
    {
        $clienteId = Auth::guard('cliente')->id();
        $carrinhoSessionId = $request->session()->get('carrinho_session_id');

        if ($clienteId && $carrinhoSessionId) {
            CarrinhoItem::query()
                ->where('cliente_id', $clienteId)
                ->where('session_id', $carrinhoSessionId)
                ->update(['cliente_id' => null]);
        }

        Auth::guard('cliente')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($carrinhoSessionId) {
            $request->session()->put('carrinho_session_id', $carrinhoSessionId);
        }

        return redirect()->route('cliente.cardapio')->with('success', 'Você saiu da conta. Sua sacola continua salva neste dispositivo.');
    }
}
