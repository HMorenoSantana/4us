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

    // Data de nascimento obrigatória e anterior a hoje
    if (empty($data['birth_date'])) {
      $errors[] = 'A data de nascimento é obrigatória.';
    } elseif (strtotime($data['birth_date']) > time()) {
      $errors[] = 'A data de nascimento não pode ser no futuro.';
    }

    // Telefone: só números, parênteses, hífen e espaço
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
