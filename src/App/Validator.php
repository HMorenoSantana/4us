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
      // Tenta converter a data
      $timestamp = strtotime($birthDate);

      if ($timestamp === false) {
        $errors[] = 'A data de nascimento é inválida.';
      } else {
        // Se for uma data no futuro
        if ($timestamp > time()) {
          $errors[] = 'A data de nascimento não pode ser no futuro.';
        }
      }
    }

    // Telefone: apenas números, (), -, espaço
    if (!empty($data['phone']) && !preg_match('/^[0-9()\-\s]+$/', $data['phone'])) {
      $errors[] = 'O telefone contém caracteres inválidos.';
    }

    // Celular: mesma validação
    if (!empty($data['cellphone']) && !preg_match('/^[0-9()\-\s]+$/', $data['cellphone'])) {
      $errors[] = 'O celular contém caracteres inválidos.';
    }

    // E-mail válido
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'O e-mail informado é inválido.';
    }

    return $errors;
  }
}
