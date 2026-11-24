<?php

namespace App\Models\Scopes;

use App\Support\LojaContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

final class LojaScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $lojaId = app(LojaContext::class)->id();

        if ($lojaId !== null) {
            $builder->where($model->qualifyColumn('loja_id'), $lojaId);
        }
    }
}
