<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    public function index()
    {
        $produtos = Produto::with('categoria')
            ->where('ativo', true)
            ->latest()
            ->paginate(20);
        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        $categorias = Categoria::where('ativo', true)->orderBy('ordem')->get();
        return view('produtos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'tempo_preparo' => 'required|integer|min:1',
            'imagem' => 'nullable|image|max:2048',
            'ativo' => 'boolean',
            'destaque' => 'boolean',
            'ordem' => 'nullable|integer|min:0',
        ], [
            'categoria_id.required' => 'A categoria é obrigatória',
            'nome.required' => 'O nome é obrigatório',
            'preco.required' => 'O preço é obrigatório',
            'tempo_preparo.required' => 'O tempo de preparo é obrigatório',
        ]);

        if ($request->hasFile('imagem')) {
            $validated['imagem'] = $request->file('imagem')->store('produtos', 'public');
        }

        $validated['ordem'] = $validated['ordem'] ?? 0;

        Produto::create($validated);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function show(Produto $produto)
    {
        $produto->load('categoria');
        return view('produtos.show', compact('produto'));
    }

    public function edit(Produto $produto)
    {
        $categorias = Categoria::where('ativo', true)->orderBy('ordem')->get();
        return view('produtos.edit', compact('produto', 'categorias'));
    }

    public function update(Request $request, Produto $produto)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome' => 'required|string|max:150',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'tempo_preparo' => 'required|integer|min:1',
            'imagem' => 'nullable|image|max:2048',
            'ativo' => 'boolean',
            'destaque' => 'boolean',
            'ordem' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('imagem')) {
            // Deletar imagem antiga
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }
            $validated['imagem'] = $request->file('imagem')->store('produtos', 'public');
        }

        $validated['ordem'] = $validated['ordem'] ?? 0;

        $produto->update($validated);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        // Verificar se tem pedidos
        if ($produto->pedidoItens()->count() > 0) {
            // Desativar o produto permanentemente ao invés de excluir
            $produto->update(['ativo' => false]);

            return redirect()->route('produtos.index')
                ->with('warning', 'O produto possui pedidos associados e foi desativado permanentemente. Ele não aparecerá mais no cardápio.');
        }

        // Deletar imagem
        if ($produto->imagem) {
            Storage::disk('public')->delete($produto->imagem);
        }

        $produto->delete();

        return redirect()->route('produtos.index')
            ->with('success', 'Produto excluído com sucesso!');
    }
}
