<?php

namespace App\Http\Controllers;

use App\Models\Sabor;
use App\Models\Categoria;
use Illuminate\Http\Request;

class SaborController extends Controller
{
    public function index()
    {
        $sabores = Sabor::with('categoria')
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();

        return view('sabores.index', compact('sabores'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nome')->get();
        return view('sabores.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ingredientes' => 'nullable|string',
            'preco_p' => 'nullable|numeric|min:0',
            'preco_m' => 'nullable|numeric|min:0',
            'preco_g' => 'nullable|numeric|min:0',
            'preco_gg' => 'nullable|numeric|min:0',
            'imagem' => 'nullable|image|max:2048',
            'ativo' => 'boolean',
            'ordem' => 'nullable|integer',
        ]);

        if ($request->hasFile('imagem')) {
            $validated['imagem'] = $request->file('imagem')->store('sabores', 'public');
        }

        $validated['ativo'] = $request->has('ativo');
        $validated['ordem'] = $validated['ordem'] ?? 0;

        // Converter valores vazios para null nos campos de preço
        foreach (['preco_p', 'preco_m', 'preco_g', 'preco_gg'] as $campo) {
            if (isset($validated[$campo]) && $validated[$campo] === '') {
                $validated[$campo] = null;
            }
        }

        Sabor::create($validated);

        return redirect()->route('sabores.index')
            ->with('success', 'Sabor criado com sucesso!');
    }

    public function edit(Sabor $sabor)
    {
        $categorias = Categoria::orderBy('nome')->get();
        return view('sabores.edit', compact('sabor', 'categorias'));
    }

    public function update(Request $request, Sabor $sabor)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ingredientes' => 'nullable|string',
            'preco_p' => 'nullable|numeric|min:0',
            'preco_m' => 'nullable|numeric|min:0',
            'preco_g' => 'nullable|numeric|min:0',
            'preco_gg' => 'nullable|numeric|min:0',
            'imagem' => 'nullable|image|max:2048',
            'ativo' => 'boolean',
            'ordem' => 'nullable|integer',
        ]);

        if ($request->hasFile('imagem')) {
            // Deletar imagem antiga se existir
            if ($sabor->imagem) {
                \Storage::disk('public')->delete($sabor->imagem);
            }
            $validated['imagem'] = $request->file('imagem')->store('sabores', 'public');
        }

        $validated['ativo'] = $request->has('ativo');
        $validated['ordem'] = $validated['ordem'] ?? 0;

        // Converter valores vazios para null nos campos de preço
        foreach (['preco_p', 'preco_m', 'preco_g', 'preco_gg'] as $campo) {
            if (isset($validated[$campo]) && $validated[$campo] === '') {
                $validated[$campo] = null;
            }
        }

        $sabor->update($validated);

        return redirect()->route('sabores.index')
            ->with('success', 'Sabor atualizado com sucesso!');
    }

    public function destroy(Sabor $sabor)
    {
        if ($sabor->imagem) {
            \Storage::disk('public')->delete($sabor->imagem);
        }

        $sabor->delete();

        return redirect()->route('sabores.index')
            ->with('success', 'Sabor excluído com sucesso!');
    }
}
