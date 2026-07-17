# Recuperação administrativa da palavra-passe

## Fluxo

1. O utilizador seleciona “Esqueci-me da palavra-passe” no login.
2. Introduz o email e recebe sempre uma resposta genérica.
3. Se a conta existir e estiver ativa, é criado um pedido pendente.
4. O administrador confirma presencialmente a identidade.
5. Ao resolver o pedido, o sistema gera uma password temporária aleatória.
6. A base de dados guarda apenas a hash; o valor aparece ao administrador uma única vez.
7. O utilizador inicia sessão e é obrigado a definir uma password definitiva.

## Regras de segurança

- apenas um pedido pendente por utilizador;
- não é revelado publicamente se o email existe;
- apenas administradores tratam pedidos;
- resolução e recusa usam POST, CSRF e confirmação;
- a password temporária tem 12 caracteres hexadecimais aleatórios;
- a password temporária nunca é guardada em texto simples;
- o administrador não escolhe nem conhece a password definitiva;
- a mudança obrigatória impede navegar pela aplicação com a password temporária.

## Instalação

- instalação nova: executar normalmente `sql/criar_tabelas.sql`;
- base criada antes desta funcionalidade: executar uma vez `sql/migracoes/001_recuperacao_password.sql`.

## Teste manual

1. Criar um pedido com uma conta existente e outro com email inexistente; a mensagem deve ser igual.
2. Confirmar que pedidos repetidos não criam várias linhas pendentes.
3. Tratar o pedido como administrador e copiar a password apresentada.
4. Confirmar que a password não reaparece ao atualizar a página.
5. Entrar com a password temporária e confirmar o redirecionamento obrigatório.
6. Definir uma password com pelo menos 8 caracteres.
7. Confirmar que a temporária deixa de funcionar e a definitiva funciona.

