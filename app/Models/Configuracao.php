<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracao extends Model
{
    use HasFactory;

    protected $table = 'configuracoes';

    protected $fillable = [
        'chave',
        'valor',
        'tipo',
        'descricao',
    ];

    public static function obter(string $chave, $default = null)
    {
        return Cache::remember("config_{$chave}", 3600, function () use ($chave, $default) {
            $config = self::where('chave', $chave)->first();

            if (!$config) {
                return $default;
            }

            return self::converterValor($config->valor, $config->tipo);
        });
    }

    public static function definir(string $chave, $valor, string $tipo = 'string'): void
    {
        $valorString = is_array($valor) ? json_encode($valor) : (string) $valor;

        self::updateOrCreate(
            ['chave' => $chave],
            [
                'valor' => $valorString,
                'tipo' => $tipo,
            ]
        );

        Cache::forget("config_{$chave}");
    }

    private static function converterValor($valor, string $tipo)
    {
        return match($tipo) {
            'number' => (float) $valor,
            'boolean' => filter_var($valor, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($valor, true),
            default => $valor,
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($config) {
            Cache::forget("config_{$config->chave}");
        });

        static::deleted(function ($config) {
            Cache::forget("config_{$config->chave}");
        });
    }
}
