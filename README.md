# Portal de Pacientes (PHP) — Clean Start
Stack: GitHub Actions + Render + Neon

## Subir rapidamente
1. Crie repo no GitHub e **suba tudo descompactado**.
2. Actions deve rodar e passar.
3. Render → Web Service → Build from repository (branch main).
4. Environment: `APP_ENV=prod` e depois `DATABASE_URL` do Neon.
5. No Neon, rode `sql/patients.sql`.
6. Teste: `/health`, `/db-check`, formulário `/`.

## DADOS

Cadastro > https://fourus-fh7s.onrender.com/patients
Banco de dados > https://fourus-fh7s.onrender.com/admin/login.php
 
Projeto Thiago: 
 
Login no Banco: 4UsAll
Senha no Banco: UB2025