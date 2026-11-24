<?php

namespace App\Support;

final class EntradaDemonstracao
{
    public static function ativa(): bool
    {
        return app()->environment(['local', 'staging', 'testing'])
            && config('demonstracao.entrada_cliente') === true;
    }
}
