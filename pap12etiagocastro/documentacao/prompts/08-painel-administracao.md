# Prompt 08 — Painel de administração

## Prompt

Implementa a área `admin/` com uma verificação central que permita acesso apenas a administradores. Cria um dashboard com contagens de utilizadores, ideias por estado, categorias, votos e comentários. Implementa moderação de ideias com listagem, filtros, detalhe e alteração controlada de estado. Implementa gestão de utilizadores sem permitir que o administrador remova ou despromova acidentalmente a última conta administrativa. Completa a gestão de categorias iniciada anteriormente.

Todas as ações destrutivas ou de alteração devem usar POST, CSRF, prepared statements e confirmação na interface. Regista mensagens de sucesso/erro e evita revelar detalhes técnicos da base de dados.

## Critérios de aceitação

- Visitantes e utilizadores comuns recebem acesso negado ao admin.
- O dashboard apresenta dados reais e coerentes.
- Aprovar/rejeitar uma ideia exige uma ação autenticada e protegida.
- Não se perde a última conta administrativa.
- Não existem alterações de dados através de links GET.

