# Plano de testes manuais

Preencher a coluna **Resultado** com `Passou`, `Falhou` ou `Não executado` e acrescentar observações.

## Instalação

| Teste | Resultado | Observações |
|---|---|---|
| Importar `criar_tabelas.sql` numa instalação vazia | Não executado | MySQL indisponível |
| Importar `inserts.sql` duas vezes sem duplicar dados | Não executado | MySQL indisponível |
| Abrir homepage sem avisos PHP | Não executado | PHP indisponível |
| Confirmar acentos e emojis corretamente | Não executado | Requer browser |

## Visitante

| Teste | Resultado | Observações |
|---|---|---|
| Consultar homepage, Sobre, ideias e contactos | Não executado | |
| Pesquisar, filtrar, ordenar e paginar ideias | Não executado | |
| Abrir apenas ideias aprovadas/implementadas | Não executado | |
| Receber 404 ao tentar abrir ideia pendente | Não executado | |
| Ser redirecionado ao tentar submeter | Não executado | |
| Ser impedido de votar/comentar | Não executado | |
| Enviar contacto válido e rejeitar dados inválidos | Não executado | |
| Ser impedido de entrar em qualquer página `admin/` | Não executado | |

## Utilizador autenticado

| Teste | Resultado | Observações |
|---|---|---|
| Registar com validações e hash na base de dados | Não executado | |
| Login válido, inválido e conta desativada | Não executado | |
| Consultar perfil e terminar sessão | Não executado | |
| Submeter ideia válida e vê-la pendente como autor | Não executado | |
| Rejeitar categoria inativa ou manipulada | Não executado | |
| Adicionar voto e retirar no segundo clique | Não executado | |
| Impedir segundo voto simultâneo pela restrição única | Não executado | |
| Publicar comentário entre 2 e 2000 caracteres | Não executado | |
| Remover comentário próprio e não remover comentário alheio | Não executado | |
| Rejeitar formulários com CSRF ausente ou alterado | Não executado | |

## Administrador

| Teste | Resultado | Observações |
|---|---|---|
| Abrir dashboard e comparar todos os totais | Não executado | |
| Criar, duplicar, ativar e desativar categorias | Não executado | |
| Aprovar, rejeitar e marcar ideia como implementada | Não executado | |
| Filtrar ideias por estado | Não executado | |
| Alterar tipo e estado de utilizadores | Não executado | |
| Impedir desativação/despromoção do último administrador | Não executado | |
| Moderar comentário de outro utilizador | Não executado | |
| Consultar mensagens e marcá-las como lidas | Não executado | |
| Confirmar que alterações por GET não são possíveis | Não executado | |
| Tratar pedido de recuperação e gerar password temporária | Passou | Testado localmente via Apache/MySQL |
| Confirmar que a password temporária aparece apenas uma vez | Passou | Valor transportado apenas na sessão flash |
| Obrigar o utilizador a definir nova password no primeiro login | Passou | Redirecionamento e alteração confirmados |

## Interface e acessibilidade

| Teste | Resultado | Observações |
|---|---|---|
| Testar a 320, 375, 768, 1024 e 1440 px | Não executado | Requer browser |
| Navegar apenas com teclado e verificar foco | Não executado | Requer browser |
| Confirmar labels e mensagens anunciáveis | Não executado | Requer browser/leitor |
| Confirmar contraste e ausência de scroll horizontal indevido | Não executado | Requer browser |
