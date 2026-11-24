<dialog id="modalSimples" class="ficha-item" aria-labelledby="modalSimplesNome">
    <div class="ficha-corpo">
        <header class="ficha-topo">
            <div id="ficha-miniatura" aria-hidden="true"></div>
            <button type="button" class="ficha-fechar" onclick="fecharModalSimples()" aria-label="Fechar detalhes do item" autofocus>×</button>
        </header>
        <h2 id="modalSimplesNome"></h2>
        <p id="ficha-descricao"></p>
        <p class="ficha-unitario"><strong id="modalSimplesPreco"></strong> por unidade</p>
        <div class="ficha-linha">
            <span id="ficha-quantidade">Quantidade</span>
            <div class="ficha-passo" role="group" aria-labelledby="ficha-quantidade">
                <button type="button" id="ficha-menos" onclick="alterarQuantidadeSimples(-1)" aria-label="Diminuir quantidade">−</button>
                <output id="simples-quantidade" aria-live="polite">1</output>
                <button type="button" id="ficha-mais" onclick="alterarQuantidadeSimples(1)" aria-label="Aumentar quantidade">+</button>
            </div>
        </div>
        <label class="ficha-rotulo" for="simples-observacoes">Observações <small>Opcional</small></label>
        <textarea id="simples-observacoes" rows="2" placeholder="Algum detalhe para a cozinha?"></textarea>
        <p id="ficha-erro" role="alert" hidden></p>
    </div>
    <footer class="ficha-rodape">
        <button type="button" onclick="confirmarProdutoSimples()" id="btn-adicionar-simples"><span><x-dobra-icon name="bag"/><span id="ficha-acao">Adicionar à sacola</span></span><strong id="simples-preco-total" aria-label="Total"></strong></button>
    </footer>
</dialog>

@push('styles')
<style>
.ficha-item{color-scheme:light;position:fixed;inset:0;margin:auto;width:min(450px,calc(100% - 32px));max-height:90vh;max-height:90dvh;padding:0;border:0;border-radius:22px;background:var(--papel);color:var(--tinta);box-shadow:0 18px 70px #131c4229;overscroll-behavior:contain}
.ficha-item[open]{display:flex;flex-direction:column}.ficha-item::backdrop{background:rgb(24 30 52 / 45%)}.ficha-corpo{min-height:0;overflow-y:auto;padding:24px 26px 18px}
.ficha-topo,.ficha-linha,.ficha-rotulo,.ficha-rodape button,.ficha-rodape button>span{display:flex;align-items:center;justify-content:space-between;gap:12px}
.ficha-topo{margin-bottom:14px}.ficha-item .db-thumb{width:54px}.ficha-fechar{align-self:flex-start;border:1px solid var(--linha);border-radius:50%;background:white;width:44px;height:44px;font:400 26px/1 var(--texto);color:var(--sec)}
.ficha-item h2{font:750 clamp(1.65rem,5vw,2rem)/1.07 var(--display);letter-spacing:-.045em;overflow-wrap:anywhere}.ficha-item #ficha-descricao{margin-top:6px;color:var(--sec);line-height:1.4}.ficha-unitario{font-size:.88rem;color:var(--sec);margin-top:9px}.ficha-unitario strong{color:var(--louca);font-weight:740}
.ficha-linha{border-top:1px solid var(--linha);margin-top:20px;padding:16px 0}.ficha-linha>span,.ficha-rotulo{font-weight:680}.ficha-passo{display:flex;align-items:center;border:1px solid #c4cbe3;border-radius:12px;overflow:hidden}.ficha-passo button{width:44px;height:44px;color:var(--louca);font-size:1.4rem}.ficha-passo button:disabled{color:#838894;background:var(--fundo)}.ficha-passo output{min-width:24px;text-align:center;font-weight:700;font-variant-numeric:tabular-nums}.ficha-rotulo small{color:var(--sec);font-size:.8rem;font-weight:400}
.ficha-item textarea{width:100%;margin-top:7px;padding:10px 12px;border:1px solid #bcc4c8;border-radius:10px;background:var(--fundo);font-size:1rem;resize:vertical}
.ficha-rodape{flex:none;background:var(--papel);padding:13px 26px 18px;border-top:1px solid var(--linha)}.ficha-rodape button{width:100%;min-height:50px;padding:10px 16px;border-radius:12px;background:var(--louca);color:white;font-weight:680}.ficha-rodape button:hover{background:#20328e}.ficha-rodape button:disabled{opacity:.75}.ficha-rodape button>span{gap:8px}.ficha-rodape strong{font-variant-numeric:tabular-nums;white-space:nowrap}.ficha-item #ficha-erro{margin-top:8px;color:#923b30}
@media(max-width:640px){.ficha-item{inset:auto 0 0;width:100%;max-width:none;max-height:94vh;max-height:94dvh;border-radius:22px 22px 0 0;margin:0}.ficha-corpo{padding:18px 20px 12px}.ficha-rodape{padding:12px 20px max(14px,env(safe-area-inset-bottom))}}
</style>
@endpush
