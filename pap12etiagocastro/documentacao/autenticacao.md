# Autenticação e sessões

## Funcionalidades implementadas

- sessão iniciada com cookies `HttpOnly` e `SameSite=Lax`;
- atributo `Secure` ativado automaticamente quando o site usa HTTPS;
- funções comuns em `includes/autenticacao.php`;
- registo com validação no servidor e confirmação da palavra-passe;
- armazenamento através de `password_hash(..., PASSWORD_DEFAULT)`;
- login através de `password_verify()` e mensagem de erro genérica;
- regeneração do identificador da sessão depois do login;
- prepared statements no registo, login e perfil;
- tokens CSRF no registo, login e logout;
- logout exclusivamente através de POST;
- escape HTML dos dados apresentados no perfil e no menu;
- proteção reutilizável para páginas privadas e administrativas.

## Compatibilidade das palavras-passe

As palavras-passe que tenham sido guardadas em texto simples pela versão antiga não são aceites pelo novo login. Não foi criada uma migração automática porque não é seguro continuar a tratar essas palavras-passe como texto simples.

Durante o desenvolvimento existem duas opções:

1. executar `sql/inserts.sql` e usar as contas de demonstração;
2. criar novamente as contas através do formulário de registo.

Em ambos os casos, a base de dados deve já ter o esquema do Prompt 02.

## Testes manuais pendentes

Quando PHP e MySQL estiverem operacionais:

1. Abrir o registo e confirmar que palavras-passe com menos de 8 caracteres são rejeitadas.
2. Confirmar que duas palavras-passe diferentes são rejeitadas.
3. Criar uma conta válida e confirmar que a coluna `senha` contém uma hash, não o texto introduzido.
4. Confirmar que um email duplicado é rejeitado.
5. Iniciar sessão com credenciais válidas e abrir o perfil.
6. Tentar iniciar sessão com email inexistente e palavra-passe errada; ambas devem produzir a mesma mensagem.
7. Abrir `perfil.php` sem sessão e confirmar o redirecionamento para login.
8. Confirmar que nome, email e tipo aparecem corretamente escapados.
9. Tentar abrir `logout.php` diretamente; deve responder com método não permitido.
10. Terminar a sessão pelo botão e confirmar que o perfil volta a ficar protegido.
11. Alterar ou remover um token CSRF num formulário e confirmar a rejeição.
12. Iniciar sessão com uma conta desativada e confirmar que o acesso é recusado.

## Contas de demonstração

Depois de executar `sql/inserts.sql`, as três contas documentadas em `base-de-dados.md` usam a palavra-passe `password`. Devem existir apenas em ambiente de desenvolvimento.

