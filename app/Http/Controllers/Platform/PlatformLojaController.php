<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use App\Models\ConfiguracaoDelivery;
use App\Models\Loja;
use App\Models\Role;
use App\Models\User;
use App\Support\LojaContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PlatformLojaController extends Controller
{
    public function index()
    {
        $lojas = Loja::withCount(['users', 'produtos'])->latest()->get();

        return view('platform.lojas.index', compact('lojas'));
    }

    public function create()
    {
        return view('platform.lojas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'alpha_dash:ascii', 'max:120', Rule::unique('lojas', 'slug')],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'owner_nome' => ['required', 'string', 'max:100'],
            'owner_email' => ['required', 'email', 'max:100'],
            'owner_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $loja = DB::transaction(function () use ($data) {
            $loja = Loja::create([
                'nome' => $data['nome'],
                'slug' => $data['slug'],
                'descricao' => ($data['descricao'] ?? null) ?: 'Escolha seus favoritos e faça seu pedido.',
                'titulo_cardapio' => 'Cardápio da casa',
                'exibir_textos_banner' => true,
                'telefone' => $data['telefone'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? null,
                'status' => 'ativa',
                'plano' => 'piloto',
            ]);

            $adminRole = Role::where('nome', 'admin')->firstOrFail();
            User::withoutGlobalScopes()->create([
                'loja_id' => $loja->id,
                'nome' => $data['owner_nome'],
                'email' => $data['owner_email'],
                'senha' => Hash::make($data['owner_password']),
                'role_id' => $adminRole->id,
                'ativo' => true,
                'is_platform_admin' => false,
            ]);

            app(LojaContext::class)->set($loja);
            try {
                Configuracao::definir('nome_restaurante', $loja->nome);
                ConfiguracaoDelivery::create([
                    'loja_id' => $loja->id,
                    'tipo_taxa' => 'fixa',
                    'valor_taxa_fixa' => 5,
                    'tempo_medio_preparo' => 40,
                    'pedido_minimo' => 0,
                    'ativo' => true,
                ]);
            } finally {
                app(LojaContext::class)->clear();
            }

            return $loja;
        });

        return redirect()->route('platform.lojas.index')
            ->with('success', "Loja {$loja->nome} criada. O proprietário já pode entrar no painel.");
    }

    public function status(Request $request, Loja $loja)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['ativa', 'suspensa'])]]);
        $loja->update($data);

        return back()->with('success', 'Situação da loja atualizada.');
    }
}
