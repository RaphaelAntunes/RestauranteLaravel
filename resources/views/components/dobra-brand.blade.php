@if(isset($lojaAtual) && $lojaAtual->logo_path)
<span class="db-brand"><img src="{{ asset('storage/'.$lojaAtual->logo_path) }}" alt="" style="width:2rem;height:2rem;object-fit:contain;border-radius:.45rem">{{ $lojaAtual->nome }}</span>
@else
<span class="db-brand"><x-dobra-icon name="fold"/>{{ $lojaAtual->nome ?? 'Dobra' }}<span class="db-dot" aria-hidden="true">.</span></span>
@endif
