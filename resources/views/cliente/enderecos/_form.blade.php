@php($editando = isset($endereco))
<form action="{{ $editando ? route('cliente.enderecos.update', ['endereco' => $endereco]) : route('cliente.enderecos.store') }}" method="POST" class="customer-panel customer-form">
    @csrf
    @if($editando) @method('PUT') @endif

    <div class="customer-form-grid">
        <div class="customer-field">
            <label for="cep">CEP</label>
            <input id="cep" name="cep" value="{{ old('cep', $endereco->cep ?? '') }}" placeholder="00000-000" inputmode="numeric" maxlength="9" autocomplete="postal-code" required>
            <p id="cep-status" class="customer-field-help" aria-live="polite">Digite o CEP para preencher o endereço.</p>
            @error('cep') <p class="customer-field-error">{{ $message }}</p> @enderror
        </div>
        <div class="customer-field customer-field-wide">
            <label for="logradouro">Rua ou avenida</label>
            <input id="logradouro" name="logradouro" value="{{ old('logradouro', $endereco->logradouro ?? '') }}" maxlength="255" autocomplete="address-line1" required>
            @error('logradouro') <p class="customer-field-error">{{ $message }}</p> @enderror
        </div>
        <div class="customer-field">
            <label for="numero">Número</label>
            <input id="numero" name="numero" value="{{ old('numero', $endereco->numero ?? '') }}" maxlength="10" required>
            @error('numero') <p class="customer-field-error">{{ $message }}</p> @enderror
        </div>
        <div class="customer-field">
            <label for="complemento">Complemento <span>(opcional)</span></label>
            <input id="complemento" name="complemento" value="{{ old('complemento', $endereco->complemento ?? '') }}" maxlength="100" autocomplete="address-line2">
            @error('complemento') <p class="customer-field-error">{{ $message }}</p> @enderror
        </div>
        <div class="customer-field">
            <label for="bairro">Bairro</label>
            <input id="bairro" name="bairro" value="{{ old('bairro', $endereco->bairro ?? '') }}" maxlength="100" autocomplete="address-level3" required>
            @error('bairro') <p class="customer-field-error">{{ $message }}</p> @enderror
        </div>
        <div class="customer-field">
            <label for="cidade">Cidade</label>
            <input id="cidade" name="cidade" value="{{ old('cidade', $endereco->cidade ?? '') }}" maxlength="100" autocomplete="address-level2" required>
            @error('cidade') <p class="customer-field-error">{{ $message }}</p> @enderror
        </div>
        <div class="customer-field">
            <label for="estado">Estado</label>
            <input id="estado" name="estado" value="{{ old('estado', $endereco->estado ?? '') }}" maxlength="2" placeholder="RN" autocomplete="address-level1" required>
            @error('estado') <p class="customer-field-error">{{ $message }}</p> @enderror
        </div>
        <div class="customer-field customer-field-wide">
            <label for="referencia">Ponto de referência <span>(opcional)</span></label>
            <textarea id="referencia" name="referencia" rows="3" maxlength="255" placeholder="Portão azul, próximo à praça...">{{ old('referencia', $endereco->referencia ?? '') }}</textarea>
            @error('referencia') <p class="customer-field-error">{{ $message }}</p> @enderror
        </div>
        <label class="customer-check customer-field-wide">
            <input type="checkbox" name="padrao" value="1" @checked(old('padrao', $endereco->padrao ?? false))>
            <span>Usar como endereço padrão</span>
        </label>
    </div>

    <div class="customer-form-actions">
        <button type="submit" class="customer-button customer-button-primary">{{ $editando ? 'Salvar endereço' : 'Cadastrar endereço' }}</button>
        <a href="{{ route('cliente.enderecos.index') }}" class="customer-button customer-button-secondary">Cancelar</a>
    </div>
</form>

@push('scripts')
<script>
(() => {
    const cepInput = document.getElementById('cep');
    if (!cepInput) return;

    const status = document.getElementById('cep-status');
    const campos = {
        logradouro: document.getElementById('logradouro'),
        bairro: document.getElementById('bairro'),
        cidade: document.getElementById('cidade'),
        estado: document.getElementById('estado'),
        numero: document.getElementById('numero'),
    };
    let ultimaConsulta = '';
    let consultaAtual = null;

    function mensagem(texto, erro = false) {
        status.textContent = texto;
        status.classList.toggle('customer-field-error', erro);
    }

    function formatarCep(valor) {
        const digitos = valor.replace(/\D/g, '').slice(0, 8);
        return digitos.length > 5 ? `${digitos.slice(0, 5)}-${digitos.slice(5)}` : digitos;
    }

    async function consultarCep() {
        const cep = cepInput.value.replace(/\D/g, '');
        if (cep.length !== 8 || cep === ultimaConsulta) return;

        consultaAtual?.abort();
        consultaAtual = new AbortController();
        cepInput.setAttribute('aria-busy', 'true');
        mensagem('Buscando endereço...');

        try {
            const resposta = await fetch(`https://viacep.com.br/ws/${cep}/json/`, {
                signal: consultaAtual.signal,
                headers: { Accept: 'application/json' },
            });
            if (!resposta.ok) throw new Error('consulta_indisponivel');

            const endereco = await resposta.json();
            if (endereco.erro) {
                ultimaConsulta = cep;
                mensagem('CEP não encontrado. Confira os números ou preencha manualmente.', true);
                return;
            }

            if (endereco.logradouro) campos.logradouro.value = endereco.logradouro;
            if (endereco.bairro) campos.bairro.value = endereco.bairro;
            if (endereco.localidade) campos.cidade.value = endereco.localidade;
            if (endereco.uf) campos.estado.value = endereco.uf.toUpperCase();

            ultimaConsulta = cep;
            mensagem(endereco.logradouro
                ? 'Endereço preenchido. Informe o número.'
                : 'Cidade localizada. Complete a rua e o número.');
            (endereco.logradouro ? campos.numero : campos.logradouro).focus();
        } catch (erro) {
            if (erro.name !== 'AbortError') {
                mensagem('Não foi possível consultar agora. Você pode preencher manualmente.', true);
            }
        } finally {
            cepInput.removeAttribute('aria-busy');
        }
    }

    cepInput.addEventListener('input', () => {
        cepInput.value = formatarCep(cepInput.value);
        if (cepInput.value.replace(/\D/g, '').length === 8) consultarCep();
    });
    cepInput.addEventListener('blur', consultarCep);
})();
</script>
@endpush
