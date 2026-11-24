<?php

namespace App\Support;

use Illuminate\Validation\Rule;

final class LojaValidation
{
    public static function exists(string $table, string $column = 'id')
    {
        return Rule::exists($table, $column)
            ->where(fn ($query) => $query->where('loja_id', app(LojaContext::class)->id()));
    }

    public static function unique(string $table, string $column, mixed $ignore = null)
    {
        $rule = Rule::unique($table, $column)
            ->where(fn ($query) => $query->where('loja_id', app(LojaContext::class)->id()));

        return $ignore === null ? $rule : $rule->ignore($ignore);
    }
}
