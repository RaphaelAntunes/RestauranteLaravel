<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Support\LojaValidation;
use Illuminate\Http\Request;

class ClientePerfilController extends Controller
{
    public function edit()
    {
        $cliente = auth('cliente')->user();

        return view('cliente.perfil.edit', compact('cliente'));
    }

    public function update(Request $request)
    {
        $cliente = auth('cliente')->user();

        $request->merge([
            'cpf' => $request->filled('cpf') ? preg_replace('/\D/', '', $request->input('cpf')) : null,
        ]);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100', LojaValidation::unique('clientes', 'email', $cliente->id)],
            'cpf' => ['nullable', 'digits:11', LojaValidation::unique('clientes', 'cpf', $cliente->id)],
        ], [
            'nome.required' => 'Informe seu nome.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'cpf.digits' => 'Informe um CPF com 11 números.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
        ]);

        $cliente->update($dados);

        return redirect()->route('cliente.perfil.edit')->with('success', 'Seus dados foram atualizados.');
    }
}
