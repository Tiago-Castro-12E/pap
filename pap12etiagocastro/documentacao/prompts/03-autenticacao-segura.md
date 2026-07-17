# Prompt 03 — Autenticação segura

## Prompt

Implementa uma autenticação segura no projeto. No registo, valida nome, email, confirmação da palavra-passe, comprimento mínimo e duplicação do email; guarda a palavra-passe com `password_hash()`. No login, usa prepared statements, `password_verify()`, uma mensagem genérica para credenciais inválidas e `session_regenerate_id(true)` após autenticação. Protege o perfil, escapa toda a saída HTML e trata utilizadores inexistentes. Melhora o logout e centraliza funções repetidas de autenticação e autorização sem introduzir frameworks.

Adiciona proteção CSRF aos formulários alterados nesta etapa. Não implementes ainda recuperação de palavra-passe nem envio de emails. Se existirem dados antigos com palavras-passe em texto simples, documenta claramente a incompatibilidade ou cria uma migração controlada.

## Critérios de aceitação

- Nenhuma palavra-passe nova é guardada em texto simples.
- Login e registo usam prepared statements.
- A sessão é regenerada após login.
- Formulários sensíveis têm CSRF.
- Dados apresentados no perfil estão escapados.
- Visitantes não conseguem abrir páginas privadas.

