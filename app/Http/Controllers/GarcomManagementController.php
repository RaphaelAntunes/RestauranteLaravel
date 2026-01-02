<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class GarcomManagementController extends Controller
{
    public function index()
    {
        $garcomRole = Role::where('nome', 'garcom')->first();
        $garcons = User::where('role_id', $garcomRole->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('garcons.index', compact('garcons'));
    }

    public function create()
    {
        return view('garcons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'senha' => 'required|min:6',
            'nome' => 'required|string|max:255',
        ]);

        $garcomRole = Role::where('nome', 'garcom')->first();

        User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->senha),
            'role_id' => $garcomRole->id,
            'ativo' => true,
            'pode_lancar_pedidos' => true,
            'pode_fechar_mesas' => true,
            'pode_cancelar_itens' => false,
            'pode_cancelar_pedidos' => false,
        ]);

        return redirect()
            ->route('garcons.index')
            ->with('success', 'Garçom cadastrado com sucesso!');
    }

    public function edit(User $garcon)
    {
        if (!$garcon->isGarcom()) {
            abort(404);
        }

        return view('garcons.edit', compact('garcon'));
    }

    public function update(Request $request, User $garcon)
    {
        if (!$garcon->isGarcom()) {
            abort(404);
        }

        $request->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($garcon->id)],
            'nome' => 'required|string|max:255',
            'senha' => 'nullable|min:6',
        ]);

        $data = [
            'nome' => $request->nome,
            'email' => $request->email,
        ];

        if ($request->filled('senha')) {
            $data['senha'] = Hash::make($request->senha);
        }

        $garcon->update($data);

        return redirect()
            ->route('garcons.index')
            ->with('success', 'Garçom atualizado com sucesso!');
    }

    public function destroy(User $garcon)
    {
        if (!$garcon->isGarcom()) {
            abort(404);
        }

        // Verificar se tem pedidos associados
        if ($garcon->pedidos()->count() > 0) {
            // Desativar o garçom permanentemente ao invés de excluir
            $garcon->update(['ativo' => false]);

            return redirect()
                ->route('garcons.index')
                ->with('warning', 'O garçom possui pedidos associados e foi desativado permanentemente. Ele não poderá mais acessar o sistema.');
        }

        $garcon->delete();

        return redirect()
            ->route('garcons.index')
            ->with('success', 'Garçom removido com sucesso!');
    }

    public function updatePermissions(Request $request, User $garcon)
    {
        if (!$garcon->isGarcom()) {
            return response()->json(['error' => 'Usuário não é garçom'], 404);
        }

        $request->validate([
            'pode_lancar_pedidos' => 'required|boolean',
            'pode_fechar_mesas' => 'required|boolean',
            'pode_cancelar_itens' => 'required|boolean',
            'pode_cancelar_pedidos' => 'required|boolean',
            'facial_obrigatorio' => 'required|boolean',
        ]);

        $garcon->update([
            'pode_lancar_pedidos' => $request->pode_lancar_pedidos,
            'pode_fechar_mesas' => $request->pode_fechar_mesas,
            'pode_cancelar_itens' => $request->pode_cancelar_itens,
            'pode_cancelar_pedidos' => $request->pode_cancelar_pedidos,
            'facial_obrigatorio' => $request->facial_obrigatorio,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permissões atualizadas com sucesso!'
        ]);
    }

    public function toggleStatus(User $garcon)
    {
        if (!$garcon->isGarcom()) {
            return response()->json(['error' => 'Usuário não é garçom'], 404);
        }

        $garcon->update([
            'ativo' => !$garcon->ativo
        ]);

        return response()->json([
            'success' => true,
            'ativo' => $garcon->ativo,
            'message' => $garcon->ativo ? 'Garçom ativado com sucesso!' : 'Garçom desativado com sucesso!'
        ]);
    }
}
