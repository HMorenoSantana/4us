<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
use App\Health;
use App\Db;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
function h(string $s): string
{
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
function json_out($data, int $code = 200): void
{
  http_response_code($code);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}
if ($method === 'GET' && $path === '/health') {
  json_out(Health::status() + ['ts' => gmdate('c')]);
}
if ($method === 'GET' && $path === '/Db-check') {
  try {
    $pdo = Db::conn();
    $one = $pdo->query('SELECT 1 AS ok')->fetch();
    json_out(['Db' => 'ok', 'result' => $one]);
  } catch (Throwable $e) {
    json_out(['Db' => 'error', 'message' => $e->getMessage()], 500);

  }
}

if ($method === 'POST' && $path === '/patients') {
  $name = trim($_POST['name'] ?? '');
  $birth = trim($_POST['birth_date'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $cell = trim($_POST['cellphone'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $err = [];
  if (mb_strlen($name) < 3)
    $err[] = 'Nome deve ter ao menos 3 caracteres.';
  if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
    $err[] = 'E-mail inválido.';
  if ($birth !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth))
    $err[] = 'Data no formato YYYY-MM-DD.';
  if ($err) {
    $msg = '<div class="alert error"><strong>Erro:</strong><ul><li>' . implode('</li><li>', array_map('h', $err)) . '</li></ul></div>';
    echo page_form($msg, compact('name', 'birth', 'phone', 'cell', 'email'));
    exit;
  }
  try {
    $pdo = Db::conn();
    $st = $pdo->prepare('INSERT INTO patients (name, birth_date, phone, cellphone, email) VALUES (:n,:b,:p,:c,:e)');
    $st->execute([':n' => $name ?: null, ':b' => $birth ?: null, ':p' => $phone ?: null, ':c' => $cell ?: null, ':e' => $email ?: null]);
    echo page_form('<div class="alert success">Paciente cadastrado com sucesso.</div>');
    exit;
  } catch (Throwable $e) {
    echo page_form('<div class="alert error"><strong>Erro ao salvar:</strong> ' . h($e->getMessage()) . '</div>', compact('name', 'birth', 'phone', 'cell', 'email'));
    exit;
  }
}

/*if ($method === 'POST' && $path === '/patients') {
  $dados = [
      'name'       => $_POST['name'] ?? '',
      'birth_date' => $_POST['birth_date'] ?? '',
      'phone'      => $_POST['phone'] ?? '',
      'cellphone'  => $_POST['cellphone'] ?? '',
      'email'      => $_POST['email'] ?? '',
  ];

  //  Usa o validador centralizado
  $err = Validator::validarPaciente($dados);

  if ($err) {
      $msg = '<div class="alert error"><strong>Erro:</strong><ul><li>'
           . implode('</li><li>', array_map('h', $err))
           . '</li></ul></div>';
      echo page_form($msg, $dados);
      exit;
  }

  try {
      $pdo = Db::conn();
      $st = $pdo->prepare('INSERT INTO patients (name, birth_date, phone, cellphone, email)
                           VALUES (:n, :b, :p, :c, :e)');
      $st->execute([
          ':n' => $dados['name'] ?: null,
          ':b' => $dados['birth_date'] ?: null,
          ':p' => $dados['phone'] ?: null,
          ':c' => $dados['cellphone'] ?: null,
          ':e' => $dados['email'] ?: null
      ]);

      echo page_form('<div class="alert success">Paciente cadastrado com sucesso.</div>');
      exit;
  } catch (Throwable $e) {
      echo page_form(
          '<div class="alert error"><strong>Erro ao salvar:</strong> ' . h($e->getMessage()) . '</div>',
          $dados
      );
      exit;
  }
}*/

if ($method === 'GET' && $path === '/') {
  echo page_form();
  exit;
}
http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo "Not Found";
function page_form(string $flash = '', array $old = []): string
{
  $name = h($old['name'] ?? '');
  $birth = h($old['birth'] ?? '');
  $phone = h($old['phone'] ?? '');
  $cell = h($old['cell'] ?? '');
  $email = h($old['email'] ?? '');
  return <<<HTML
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
      Cadastro de Pacientes
    </title>

<!-- =========================== -->
<!-- ==== ALTERAÇÕES DO CSS ==== -->
<!-- =========================== -->

  <style>

      body{
        font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
        background-color: black;
        margin:2rem;
      }
        .container{
          max-width: 640px;
          margin: 0 auto;
          padding: 20px;
          border-radius: 50px;
          background-color: #151633;
        }
        h1{
            margin-bottom:.5rem;
            color: #ffffff;
            font-weight: bold;
        }
        p.desc{
            color:#000;
            margin-top:0;
            font-weight: bold;
        }
        form{
            display:grid;
            gap:12px;
            margin-top:16px;
        }
              label{
                    font-weight:600;
                    font-weight: bold;
                    color: #fff;
              }
              input[type=text],
              input[type=date],
              input[type=email],
              input[type=tel]
                  {width:80%;
                  padding:15px;
                  border:1px solid #ddd;
                  border-radius:8px
                  }
        button
            {
            padding:12px 16px;
            border:0;
            border-radius:8px;
            cursor:pointer;
            font-weight: bold;
            }
            
            button.primary
            {
              background-color: #e4e4e950;
              font-weight: bold;
            }

        .alert
            {
            padding:12px 14px;
            border-radius:8px;
            margin:8px 0 4px;
            }
            .alert.success
                {
                background:#e6f4ea;
                color:#1e7e34;
                border:1px solid #b7e1c1;
                }
            .alert.error
                {
                background:#fdecea;
                color:#a11;
                border:1px solid #f5c2c7
                }
        small.hint
            {
            color:#fff;
            }
        .muted
            {
            color:#fff;
            font-size:14px;
            }
        .row
            {
            display:grid;
            gap:12px;
            grid-template-columns:1fr 1fr;
            }
        
      </style>

  </head>

<!-- =================================== -->
<!-- ==== FIM DAS ALTERAÇÕES DO CSS ==== -->
<!-- =================================== -->
          <header>
            <img src="assets/img/logo.ub.png" alt="Logo Universidade Brasil" title="Universidade Brasil">
          </header>
<body>
      <div class="container">
        <h1>Cadastro de Pacientes</h1>
        <p class="desc">Preencha seus dados para contato e agendamento.</p>{$flash}
        <form method="post" action="/patients" novalidate>
        <div>
          <label for="name">Nome completo *</label>
          <input type="text" id="name" name="name" value="{$name}" placeholder="Nome Completo" required>
        </div>
        <div>
          <label for="birth_date">Data de nascimento</label>
          <input type="date" id="birth_date" name="birth_date" value="{$birth}" placeholder="DD-MM-YYYY">
        </div>
        <div class="row">
          <div>
            <label for="phone">Telefone (fixo)</label>
            <input type="tel" id="phone" name="phone" placeholder="7070-7070" value="{$phone}">
        </div>
        <div>
          <label for="cellphone">Celular</label>
          <input type="tel" id="cellphone" name="cellphone" placeholder="+55 11 97070-7070" value="{$cell}">
        </div>
      </div>
      <div>
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="{$email}" placeholder="voce@exemplo.com">
      </div>
      <div>
        <button class="primary" type="submit">Enviar cadastro</button>
      </div>
      <p class="muted">
        <small class="hint">Ao enviar, você concorda com o uso dos seus dados para contato e agendamento.</small>
      </p>
    </form>

  <script>
  //Impede letras no telefone e celular (permite apenas números)
  document.querySelectorAll('#phone, #cellphone').forEach(function (input) {
    input.addEventListener('input', function () {
      //Remove tudo que não for número
      this.value = this.value.replace(/\D/g, '');
    });
  });

  //Impede data de nascimento maior que hoje
  const birthInput = document.getElementById('birth_date');
  const today = new Date().toISOString().split('T')[0];
  birthInput.setAttribute('max', today);

  //Validação adicional: impede ano futuro e data inválida
  birthInput.addEventListener('change', function () {
    const value = this.value;
    if (!value) return;

    const selectedDate = new Date(value);
    const selectedYear = selectedDate.getFullYear();
    const currentYear = new Date().getFullYear();

    if (selectedYear > currentYear) {
      alert('O ano de nascimento não pode ser maior que o ano atual.');
      this.value = '';
      this.focus();
      return;
    }

    const parts = value.split('-');
    if (parts.length === 3) {
      const [year, month, day] = parts.map(Number);
      const validDate = new Date(year, month - 1, day);
      if (
        validDate.getFullYear() !== year ||
        validDate.getMonth() + 1 !== month ||
        validDate.getDate() !== day
      ) {
        alert('A data de nascimento informada é inválida.');
        this.value = '';
        this.focus();
      }
    }
  });

  //Validação no envio do formulário
  document.querySelector('form').addEventListener('submit', function (e) {
    const phone = document.getElementById('phone').value.replace(/\D/g, '');
    const cell = document.getElementById('cellphone').value.replace(/\D/g, '');

    //Telefone fixo deve ter 10 dígitos se informado
    if (phone && phone.length !== 10) {
      alert('O telefone fixo deve conter exatamente 10 dígitos numéricos.');
      e.preventDefault();
      document.getElementById('phone').focus();
      return false;
    }

    //Celular deve ter 11 dígitos se informado
    if (cell && cell.length !== 11) {
      alert('O celular deve conter exatamente 11 dígitos numéricos.');
      e.preventDefault();
      document.getElementById('cellphone').focus();
      return false;
    }

    return true;
  });
</script>
  <p class="muted">Endpoints: 
    <code>/health</code> • 
    <code>/Db-check</code> • 
    <code>POST /patients</code>
  </p>
</div>
<link rel="stylesheet" href="<?php URL_BASE ?>assets/css/estilo.css">
</body>
</html>
HTML;
}