# Base de dados

## Requisitos

- MySQL 8 ou uma versão recente de MariaDB;
- extensão `mysqli` ativa no PHP;
- permissões para criar a base de dados `banco_ideias`.

## Instalação limpa

Executar pela seguinte ordem:

1. `sql/criar_tabelas.sql`;
2. `sql/inserts.sql`;
3. opcionalmente, executar consultas individuais de `sql/consultas.sql` para confirmar os dados.

O primeiro script cria e seleciona a base de dados. O segundo pode ser executado novamente sem duplicar os utilizadores, categorias, ideias, comentários ou votos de demonstração previstos pelo próprio script.

`CREATE TABLE IF NOT EXISTS` não altera automaticamente tabelas antigas. Se já existir uma versão anterior da base de dados com informação importante, deve ser feita uma cópia de segurança e criada uma migração antes de aplicar o novo esquema. Durante o desenvolvimento, se não existirem dados a preservar, pode ser feita uma instalação limpa.

## Credenciais de desenvolvimento

Todas as contas seguintes usam temporariamente a palavra-passe `password`:

| Tipo | Email |
|---|---|
| Administrador | `admin@bancodeideias.test` |
| Aluno | `ana@bancodeideias.test` |
| Professor | `manuel@bancodeideias.test` |

As palavras-passe estão guardadas como hashes bcrypt compatíveis com `password_verify()` do PHP. Estas contas são exclusivamente de demonstração, têm uma palavra-passe intencionalmente previsível e nunca devem ser usadas em produção.

O login usa `password_verify()` e aceita estas hashes. Registos antigos que ainda contenham palavras-passe em texto simples devem ser recriados; não existe migração insegura automática.

## Integridade referencial

- Um utilizador com ideias ou comentários não pode ser eliminado (`RESTRICT`); deve ser desativado através de `ativo`.
- Uma categoria associada a ideias não pode ser eliminada (`RESTRICT`); deve ser desativada através de `ativa`.
- Ao eliminar uma ideia, os seus comentários e votos são eliminados (`CASCADE`).
- Ao eliminar um utilizador que não tenha conteúdo protegido, os seus votos são eliminados (`CASCADE`).
- Existe apenas um voto por cada combinação de utilizador e ideia.
- O nome da categoria e o email do utilizador são únicos.

## Decisões do esquema

- Todas as tabelas usam InnoDB, `utf8mb4` e `utf8mb4_unicode_ci`.
- Datas de criação usam `TIMESTAMP` e `CURRENT_TIMESTAMP`.
- Registos editáveis possuem uma data de atualização automática quando aplicável.
- Utilizadores e categorias são desativados em vez de removidos quando têm conteúdo relacionado.
- Uma ideia pode estar `Pendente`, `Aprovada`, `Rejeitada` ou `Implementada`.
- Mensagens do formulário de contacto ficam em `mensagem_contacto` com estado `Nova` ou `Lida`.

## Verificação pendente

Os scripts foram revistos estaticamente, mas ainda não foram executados porque o serviço MySQL não está disponível. Quando estiver operacional, devem ser confirmados:

- criação de todas as tabelas e chaves estrangeiras;
- segunda execução de `inserts.sql` sem duplicações;
- autenticação das três contas depois do Prompt 03;
- resultados das dez consultas de referência e consulta administrativa das mensagens;
- compatibilidade exata com a versão de MySQL/MariaDB instalada no XAMPP.
