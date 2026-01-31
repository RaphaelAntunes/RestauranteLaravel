<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracaoDelivery;
use Illuminate\Http\Request;

class ConfiguracaoDeliveryController extends Controller
{
    public function edit()
    {
        $config = ConfiguracaoDelivery::obter();

        return view('admin.configuracoes-delivery.edit', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'tipo_taxa' => 'required|in:fixa,gratis_acima',
            'valor_taxa_fixa' => 'required_if:tipo_taxa,fixa|nullable|numeric|min:0',
            'valor_minimo_gratis' => 'required_if:tipo_taxa,gratis_acima|nullable|numeric|min:0',
            'tempo_medio_preparo' => 'required|integer|min:10|max:180',
            'pedido_minimo' => 'nullable|numeric|min:0',
            'ativo' => 'required|boolean',
            'horario_inicio' => 'nullable|date_format:H:i',
            'horario_fim' => 'nullable|date_format:H:i',
            'dias_funcionamento' => 'nullable|array',
            'dias_funcionamento.*' => 'in:seg,ter,qua,qui,sex,sab,dom',
        ], [
            'tipo_taxa.required' => 'Selecione o tipo de taxa.',
            'valor_taxa_fixa.required_if' => 'Informe o valor da taxa fixa.',
            'valor_minimo_gratis.required_if' => 'Informe o valor mínimo para frete grátis.',
            'tempo_medio_preparo.required' => 'Informe o tempo médio de preparo.',
        ]);

        $config = ConfiguracaoDelivery::obter();

        $config->update([
            'tipo_taxa' => $request->tipo_taxa,
            'valor_taxa_fixa' => $request->tipo_taxa === 'fixa' ? $request->valor_taxa_fixa : $config->valor_taxa_fixa,
            'valor_minimo_gratis' => $request->tipo_taxa === 'gratis_acima' ? $request->valor_minimo_gratis : $config->valor_minimo_gratis,
            'tempo_medio_preparo' => $request->tempo_medio_preparo,
            'pedido_minimo' => $request->pedido_minimo ?? 0,
            'ativo' => $request->ativo,
            'horario_inicio' => $request->horario_inicio,
            'horario_fim' => $request->horario_fim,
            'dias_funcionamento' => $request->dias_funcionamento,
        ]);

        return back()->with('success', 'Configurações atualizadas com sucesso!');
    }
}
