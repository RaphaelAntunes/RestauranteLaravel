<?php

namespace App\Http\Controllers;

use App\Models\ProdutoTamanho;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoTamanhoController extends Controller
{
    public function index()
    {
        $tamanhos = ProdutoTamanho::with('produto')
            ->orderBy('produto_id')
            ->orderBy('ordem')
            ->get();

        return view('produto-tamanhos.index', compact('tamanhos'));
    }

    public function create()
    {
        $produtos = Produto::where('ativo', true)->orderBy('nome')->get();
        return view('produto-tamanhos.create', compact('produtos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:255',
            'preco' => 'required|numeric|min:0',
            'max_sabores' => 'required|integer|min:1|max:5',
            'ativo' => 'boolean',
            'ordem' => 'nullable|integer',
        ]);

        $validated['ativo'] = $request->has('ativo');
        $validated['ordem'] = $validated['ordem'] ?? 0;

        ProdutoTamanho::create($validated);

        return redirect()->route('produto-tamanhos.index')
            ->with('success', 'Tamanho criado com sucesso!');
    }

    public function edit(ProdutoTamanho $produtoTamanho)
    {
        $produtos = Produto::where('ativo', true)->orderBy('nome')->get();
        return view('produto-tamanhos.edit', compact('produtoTamanho', 'produtos'));
    }

    public function update(Request $request, ProdutoTamanho $produtoTamanho)
    {
        $validated = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:255',
            'preco' => 'required|numeric|min:0',
            'max_sabores' => 'required|integer|min:1|max:5',
            'ativo' => 'boolean',
            'ordem' => 'nullable|integer',
        ]);

        $validated['ativo'] = $request->has('ativo');
        $validated['ordem'] = $validated['ordem'] ?? 0;

        $produtoTamanho->update($validated);

        return redirect()->route('produto-tamanhos.index')
            ->with('success', 'Tamanho atualizado com sucesso!');
    }

    public function destroy(ProdutoTamanho $produtoTamanho)
    {
        $produtoTamanho->delete();

        return redirect()->route('produto-tamanhos.index')
            ->with('success', 'Tamanho excluído com sucesso!');
    }
}
