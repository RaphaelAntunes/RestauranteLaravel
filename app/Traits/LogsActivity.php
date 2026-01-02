<?php

namespace App\Traits;

use App\Models\Log;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            if (auth()->check()) {
                Log::registrar(
                    acao: 'criar',
                    tabela: $model->getTable(),
                    registroId: $model->id,
                    dadosNovos: $model->toArray()
                );
            }
        });

        static::updated(function ($model) {
            if (auth()->check() && $model->wasChanged()) {
                Log::registrar(
                    acao: 'atualizar',
                    tabela: $model->getTable(),
                    registroId: $model->id,
                    dadosAnteriores: $model->getOriginal(),
                    dadosNovos: $model->getChanges()
                );
            }
        });

        static::deleted(function ($model) {
            if (auth()->check()) {
                Log::registrar(
                    acao: 'deletar',
                    tabela: $model->getTable(),
                    registroId: $model->id,
                    dadosAnteriores: $model->toArray()
                );
            }
        });
    }
}
