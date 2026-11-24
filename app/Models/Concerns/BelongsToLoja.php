<?php

namespace App\Models\Concerns;

use App\Models\Loja;
use App\Models\Scopes\LojaScope;
use App\Support\LojaContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToLoja
{
    protected static function bootBelongsToLoja(): void
    {
        static::addGlobalScope(new LojaScope());

        static::creating(function ($model): void {
            if (!$model->loja_id && app(LojaContext::class)->has()) {
                $model->loja_id = app(LojaContext::class)->id();
            }
        });
    }

    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }
}
