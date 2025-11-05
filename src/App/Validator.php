<?php
declare(strict_types=1);

namespace App;

final class Validator
{
    /**
     * Valida dados de um paciente.
     * Retorna um array com mensagens de erro (vazio se estiver tudo certo)
     */
    public static function validarPaciente(array $data): array
    {
        $errors = [];

        $name = trim($data['name'] ?? '');
        $birth = trim($data['birth_date'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $cell = trim($data['cellphone'] ?? '');
        $email = trim($data['email'] ?? '');

        // ✅ Nome: apenas letras e espaços, mínimo de 3 caracteres
        if ($name === '' || mb_strlen($name) < 3) {
            $errors[] = 'O nome deve ter ao menos 3 caracteres.';
        } elseif (!preg_match('/^[\p{L}\s]+$/u', $name)) {
            $errors[] = 'O nome deve conter apenas letras e espaços.';
        }

        // ✅ Telefone fixo: se informado, deve conter exatamente 10 dígitos
        if ($phone !== '') {
            $phoneDigits = preg_replace('/\D/', '', $phone);
            if (!preg_match('/^\d{10}$/', $phoneDigits)) {
                $errors[] = 'O telefone fixo deve conter exatamente 10 dígitos numéricos.';
            }
        }

        // ✅ Celular: se informado, deve conter exatamente 11 dígitos
        if ($cell !== '') {
            $cellDigits = preg_replace('/\D/', '', $cell);
            if (!preg_match('/^\d{11}$/', $cellDigits)) {
                $errors[] = 'O celular deve conter exatamente 11 dígitos numéricos.';
            }
        }

        // ✅ Data de nascimento: formato válido, data real e anterior à de hoje
        if ($birth !== '') {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth)) {
                $errors[] = 'A data deve estar no formato YYYY-MM-DD.';
            } else {
                [$year, $month, $day] = array_map('intval', explode('-', $birth));
                if (!checkdate($month, $day, $year)) {
                    $errors[] = 'A data de nascimento informada é inválida.';
                } else {
                    $birthTs = strtotime($birth);
                    $today = strtotime(date('Y-m-d'));
                    if ($birthTs === false || $birthTs >= $today) {
                        $errors[] = 'A data de nascimento deve ser anterior à data de hoje.';
                    }
                }
            }
        }

        // ✅ E-mail (opcional, mas deve ser válido se informado)
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'O e-mail informado é inválido.';
        }

        return $errors;
    }
}
