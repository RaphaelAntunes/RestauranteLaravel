<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function enviarCodigo(string $celular, ?string $ip = null): OtpCode
    {
        OtpCode::where('celular', $celular)->delete();

        $otp = OtpCode::gerarCodigo($celular, $ip);
        // Só expõe o código em log no ambiente local para facilitar o desenvolvimento.
        if (app()->environment(['local', 'testing'])) {
            Log::debug('Código OTP gerado para autenticação local.', [
                'celular' => $celular,
                'codigo' => $otp->codigo,
            ]);
        }

        return $otp;
    }

    public function validarCodigo(string $celular, string $codigo): bool
    {
        $otp = OtpCode::where('celular', $celular)
            ->validos()
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otp) {
            return false;
        }

        return $otp->validar($codigo);
    }

    public function limparCodigosExpirados(): void
    {
        OtpCode::where('created_at', '<', now()->subHour())->delete();
    }
}
