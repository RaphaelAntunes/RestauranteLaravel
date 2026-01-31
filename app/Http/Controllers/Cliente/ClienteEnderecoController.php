<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ClienteEndereco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteEnderecoController extends Controller
{
    public function index()
    {
        $cliente = Auth::guard('cliente')->user();
        $enderecos = $cliente->enderecos;

        return view('cliente.enderecos.index', compact('enderecos'));
    }

    public function create()
    {
        return view('cliente.enderecos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_endereco' => 'required|string|max:50',
            'cep' => 'required|string|regex:/^\d{5}-\d{3}$/',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|size:2',
            'referencia' => 'nullable|string|max:255',
            'padrao' => 'nullable|boolean',
        ]);

        $cliente = Auth::guard('cliente')->user();

        $endereco = ClienteEndereco::create([
            'cliente_id' => $cliente->id,
            'nome_endereco' => $request->nome_endereco,
            'cep' => $request->cep,
            'logradouro' => $request->logradouro,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'referencia' => $request->referencia,
            'padrao' => $request->has('padrao') ? true : false,
        ]);

        if ($endereco->padrao) {
            $endereco->marcarComoPadrao();
        }

        return redirect()->route('cliente.enderecos.index')->with('success', 'Endereço cadastrado com sucesso!');
    }

    public function edit(ClienteEndereco $endereco)
    {
        if ($endereco->cliente_id !== Auth::guard('cliente')->id()) {
            abort(403);
        }

        return view('cliente.enderecos.edit', compact('endereco'));
    }

    public function update(Request $request, ClienteEndereco $endereco)
    {
        if ($endereco->cliente_id !== Auth::guard('cliente')->id()) {
            abort(403);
        }

        $request->validate([
            'nome_endereco' => 'required|string|max:50',
            'cep' => 'required|string|regex:/^\d{5}-\d{3}$/',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|size:2',
            'referencia' => 'nullable|string|max:255',
            'padrao' => 'nullable|boolean',
        ]);

        $endereco->update([
            'nome_endereco' => $request->nome_endereco,
            'cep' => $request->cep,
            'logradouro' => $request->logradouro,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'referencia' => $request->referencia,
        ]);

        if ($request->has('padrao')) {
            $endereco->marcarComoPadrao();
        }

        return redirect()->route('cliente.enderecos.index')->with('success', 'Endereço atualizado com sucesso!');
    }

    public function destroy(ClienteEndereco $endereco)
    {
        if ($endereco->cliente_id !== Auth::guard('cliente')->id()) {
            abort(403);
        }

        if ($endereco->padrao) {
            return back()->with('error', 'Não é possível excluir o endereço padrão. Defina outro endereço como padrão primeiro.');
        }

        if ($endereco->pedidos()->exists()) {
            return back()->with('error', 'Não é possível excluir um endereço que possui pedidos associados.');
        }

        $endereco->delete();

        return back()->with('success', 'Endereço excluído com sucesso!');
    }

    public function marcarPadrao(ClienteEndereco $endereco)
    {
        if ($endereco->cliente_id !== Auth::guard('cliente')->id()) {
            abort(403);
        }

        $endereco->marcarComoPadrao();

        return back()->with('success', 'Endereço marcado como padrão!');
    }
}
