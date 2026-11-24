<?php

namespace Tests;

use App\Models\Loja;
use App\Support\LojaContext;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

abstract class TestCase extends BaseTestCase
{
    protected ?Loja $loja = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('lojas')) {
            return;
        }

        $this->loja = Loja::query()->firstOrCreate(
            ['slug' => 'loja-teste'],
            ['nome' => 'Loja de Teste', 'status' => 'ativa', 'plano' => 'teste']
        );

        app(LojaContext::class)->set($this->loja);
        URL::defaults(['loja' => $this->loja->slug]);
    }
}
