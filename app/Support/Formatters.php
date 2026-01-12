<?php

namespace App\Support;

class Formatters
{
    public static function cpfCnpj(?string $value): string
    {
        if (!$value) {
            return '';
        }

        // Remove tudo que não é número
        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) === 11) {
            // CPF
            return preg_replace(
                '/(\d{3})(\d{3})(\d{3})(\d{2})/',
                '$1.$2.$3-$4',
                $digits
            );
        }

        if (strlen($digits) === 14) {
            // CNPJ
            return preg_replace(
                '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
                '$1.$2.$3/$4-$5',
                $digits
            );
        }

        // fallback (caso inválido)
        return $value;
    }

    public static function telefone(?string $value): string
    {
        if (!$value) {
            return '';
        }

        // Remove tudo que não é número
        $digits = preg_replace('/\D/', '', $value);

        // Fixo (10 dígitos)
        if (strlen($digits) === 10) {
            return preg_replace(
                '/(\d{2})(\d{4})(\d{4})/',
                '($1) $2-$3',
                $digits
            );
        }

        // Celular (11 dígitos)
        if (strlen($digits) === 11) {
            return preg_replace(
                '/(\d{2})(\d{5})(\d{4})/',
                '($1) $2-$3',
                $digits
            );
        }

        // fallback
        return $value;
    }
}
