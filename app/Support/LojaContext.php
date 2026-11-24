<?php

namespace App\Support;

use App\Models\Loja;

final class LojaContext
{
    private ?Loja $loja = null;

    public function set(Loja $loja): void
    {
        $this->loja = $loja;
    }

    public function clear(): void
    {
        $this->loja = null;
    }

    public function loja(): ?Loja
    {
        return $this->loja;
    }

    public function id(): ?int
    {
        return $this->loja?->getKey();
    }

    public function has(): bool
    {
        return $this->loja !== null;
    }
}
