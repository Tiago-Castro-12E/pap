# Prompt 09 — Contactos e páginas institucionais

## Prompt

Completa `sobre.php` e `contactos.php` de forma coerente com o propósito da PAP. A página Sobre deve explicar problema, objetivos, público-alvo, funcionamento e tecnologias sem inventar informações pessoais ou institucionais. O formulário de contacto deve validar nome, email, assunto e mensagem no servidor, usar CSRF e apresentar feedback acessível.

Antes de escolher armazenamento ou envio de email, verifica a infraestrutura disponível. Se não houver servidor de email configurado, guarda as mensagens numa tabela própria e cria uma listagem administrativa protegida, documentando essa decisão. Não inclua credenciais ou dados pessoais fictícios tratados como reais.

## Critérios de aceitação

- As duas páginas deixam de estar vazias.
- O conteúdo explica claramente o projeto.
- O contacto tem validação, CSRF e tratamento seguro da saída.
- A estratégia de entrega/armazenamento de mensagens está documentada.
- Apenas administradores podem consultar mensagens guardadas.

