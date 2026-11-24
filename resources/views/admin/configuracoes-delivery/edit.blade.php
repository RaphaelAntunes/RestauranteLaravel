@extends('layouts.app')
@section('title', 'Configurações do delivery')

@push('styles')
<style>
.delivery-settings{max-width:920px;margin:0 auto;color:#111827}.delivery-settings .settings-intro{max-width:65ch;color:#6b7280;margin-bottom:24px}.delivery-settings form{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:28px}.delivery-settings fieldset{padding:0 0 24px;margin:0 0 24px;border-bottom:1px solid #e5e7eb}.delivery-settings legend{font-size:18px;font-weight:700;margin-bottom:16px}.delivery-settings .settings-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 24px}.delivery-settings label{display:block;font-size:14px;font-weight:600;margin-bottom:6px}.delivery-settings input:not([type=checkbox]),.delivery-settings select{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;background:#fff;color:#111827;min-height:44px}.delivery-settings :is(input,select,button):focus-visible{outline:3px solid #ef4444;outline-offset:3px}.delivery-settings .settings-help{font-size:13px;line-height:1.5;color:#6b7280;margin-top:6px}.delivery-settings .settings-days{display:flex;flex-wrap:wrap;gap:12px 20px}.delivery-settings .settings-days label{display:flex;align-items:center;gap:8px;font-weight:400;min-height:44px}.delivery-settings .settings-actions{display:flex;justify-content:flex-end;align-items:center;gap:20px}.delivery-settings button[type=submit]{background:#dc2626;color:#fff;font-weight:600;border:0;border-radius:8px;padding:12px 20px;min-height:44px}.delivery-settings .settings-errors{border:1px solid #ef4444;padding:16px;margin-bottom:20px;border-radius:8px}.delivery-settings a{text-decoration:underline}@media(prefers-color-scheme:dark){.delivery-settings{color:#f9fafb}.delivery-settings form{background:#1f2937;border-color:#374151}.delivery-settings fieldset{border-color:#374151}.delivery-settings .settings-intro,.delivery-settings .settings-help{color:#d1d5db}.delivery-settings input:not([type=checkbox]),.delivery-settings select{background:#111827;border-color:#4b5563;color:#f9fafb}}@media(max-width:640px){.delivery-settings form{padding:20px 16px}.delivery-settings .settings-grid{grid-template-columns:1fr}.delivery-settings .settings-actions{align-items:stretch;flex-direction:column-reverse}.delivery-settings .settings-actions a{text-align:center}}
</style>
@endpush

@section('content')
<div class="delivery-settings">
    <p class="settings-intro">Defina quando os clientes podem pedir entrega, o valor do frete e o tempo de preparo.</p>
    @if($errors->any())
        <div class="settings-errors" role="alert">
            <p><strong>Revise os campos antes de salvar.</strong></p>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.configuracoes-delivery.update') }}">
        @csrf
        @method('PUT')
        <fieldset>
            <legend>Atendimento</legend>
            <div class="settings-grid">
                <div>
                    <label for="ativo">Receber pedidos de delivery</label>
                    <select id="ativo" name="ativo" required>
                        <option value="1" @selected((string) old('ativo', (int) $config->ativo) === '1')>Ativado</option>
                        <option value="0" @selected((string) old('ativo', (int) $config->ativo) === '0')>Pausado</option>
                    </select>
                </div>
                <div>
                    <label for="tempo_medio_preparo">Tempo médio de preparo (minutos)</label>
                    <input id="tempo_medio_preparo" name="tempo_medio_preparo" type="number" min="10" max="180" required value="{{ old('tempo_medio_preparo', $config->tempo_medio_preparo) }}">
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>Frete e pedido mínimo</legend>
            <div class="settings-grid">
                <div>
                    <label for="tipo_taxa">Regra do frete</label>
                    <select id="tipo_taxa" name="tipo_taxa" required>
                        <option value="fixa" @selected(old('tipo_taxa', $config->tipo_taxa) === 'fixa')>Taxa fixa</option>
                        <option value="gratis_acima" @selected(old('tipo_taxa', $config->tipo_taxa) === 'gratis_acima')>Grátis a partir de um valor</option>
                    </select>
                </div>
                <div>
                    <label for="valor_taxa_fixa">Taxa de entrega (R$)</label>
                    <input id="valor_taxa_fixa" name="valor_taxa_fixa" type="number" min="0" step="0.01" value="{{ old('valor_taxa_fixa', $config->valor_taxa_fixa) }}">
                </div>
                <div>
                    <label for="valor_minimo_gratis">Frete grátis a partir de (R$)</label>
                    <input id="valor_minimo_gratis" name="valor_minimo_gratis" type="number" min="0" step="0.01" value="{{ old('valor_minimo_gratis', $config->valor_minimo_gratis) }}">
                    <p class="settings-help">Usado somente na regra de frete grátis.</p>
                </div>
                <div>
                    <label for="pedido_minimo">Pedido mínimo (R$)</label>
                    <input id="pedido_minimo" name="pedido_minimo" type="number" min="0" step="0.01" value="{{ old('pedido_minimo', $config->pedido_minimo) }}">
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>Dias e horários</legend>
            <div class="settings-grid">
                <div>
                    <label for="horario_inicio">Abertura</label>
                    <input id="horario_inicio" name="horario_inicio" type="time" value="{{ old('horario_inicio', substr($config->horario_inicio ?? '', 0, 5)) }}">
                </div>
                <div>
                    <label for="horario_fim">Fechamento</label>
                    <input id="horario_fim" name="horario_fim" type="time" value="{{ old('horario_fim', substr($config->horario_fim ?? '', 0, 5)) }}">
                </div>
            </div>
            <p class="settings-help">Deixe os horários em branco para não limitar o atendimento por hora.</p>
            <div class="settings-days">
                @foreach(['seg' => 'Segunda', 'ter' => 'Terça', 'qua' => 'Quarta', 'qui' => 'Quinta', 'sex' => 'Sexta', 'sab' => 'Sábado', 'dom' => 'Domingo'] as $value => $label)
                    <label><input type="checkbox" name="dias_funcionamento[]" value="{{ $value }}" @checked(in_array($value, old('dias_funcionamento', $config->dias_funcionamento ?? [])))> {{ $label }}</label>
                @endforeach
            </div>
            <p class="settings-help">Sem seleção de dias, o delivery funciona todos os dias da semana.</p>
        </fieldset>
        <div class="settings-actions">
            <a href="{{ route('admin.pedidos-online.index') }}">Voltar aos pedidos</a>
            <button type="submit">Salvar configurações</button>
        </div>
    </form>
</div>
@endsection
