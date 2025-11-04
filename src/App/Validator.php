<?php
declare(strict_types=1);

namespace App;

final class Validator
{
  /**
   * Valida dados do paciente.
   * Retorna um array com mensagens de erro (vazio se estiver tudo certo)
   */
  public static function validarPaciente(array $data): array
  {
    $errors = [];

    // Nome obrigatório
    if (empty(trim($data['name'] ?? ''))) {
      $errors[] = 'O nome é obrigatório.';
    }

    // ===== Validação da data de nascimento =====
    $birthDate = $data['birth_date'] ?? '';

    if (empty($birthDate)) {
      $errors[] = 'A data de nascimento é obrigatória.';
    } else {
      // Converte a data para timestamp
      $timestamp = strtotime($birthDate);

      if ($timestamp === false) {
        $errors[] = 'A data de nascimento é inválida.';
      } else {
        $birthYear = (int) date('Y', $timestamp);
        $currentYear = (int) date('Y');

        // ✅ Impede ano futuro
        if ($birthYear > $currentYear) {
          $errors[] = 'O ano de nascimento não pode ser maior que o ano atual.';
        }
      }
    }

    // ===== Validação de telefone =====
    if (!empty($data['phone']) && !preg_match('/^[0-9()\-\s]+$/', $data['phone'])) {
      $errors[] = 'O telefone contém caracteres inválidos.';
    }

    // ===== Validação de celular =====
    if (!empty($data['cellphone']) && !preg_match('/^[0-9()\-\s]+$/', $data['cellphone'])) {
      $errors[] = 'O celular contém caracteres inválidos.';
    }

    // ===== Validação de e-mail =====
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'O e-mail informado é inválido.';
    }

    return $errors;
  }
}
