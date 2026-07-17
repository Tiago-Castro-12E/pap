# Banco de Ideias da Comunidade

Aplicação PHP/MySQL desenvolvida como projeto de Prova de Aptidão Profissional para recolher, moderar e discutir propostas da comunidade.

## Requisitos

- PHP 8.0 ou superior com extensões `mysqli` e `mysqlnd`;
- MySQL 8 ou uma versão recente de MariaDB;
- servidor Apache (por exemplo, XAMPP) ou equivalente;
- browser moderno.

## Instalação

1. Colocar `pap12etiagocastro` na pasta pública do servidor.
2. Iniciar Apache e MySQL.
3. Importar `sql/criar_tabelas.sql`.
4. Importar `sql/inserts.sql` para carregar dados de demonstração.
5. Abrir a pasta da aplicação através do endereço do servidor local.

Por omissão, a aplicação usa `localhost`, utilizador `root`, password vazia e base de dados `banco_ideias`. É possível substituir estes valores através das variáveis de ambiente `DB_HOST`, `DB_USER`, `DB_PASSWORD` e `DB_NAME`.

## Contas de demonstração

| Perfil | Email | Palavra-passe |
|---|---|---|
| Administrador | `admin@bancodeideias.test` | `password` |
| Aluno | `ana@bancodeideias.test` | `password` |
| Professor | `manuel@bancodeideias.test` | `password` |

Estas credenciais servem apenas para desenvolvimento e devem ser removidas antes de uma publicação real.

## Funcionalidades

- registo, login, perfil e logout;
- submissão e consulta de ideias;
- pesquisa, categorias, ordenação e paginação;
- votos alternáveis e comentários;
- moderação de comentários pelo autor ou administrador;
- painel com estatísticas;
- moderação de ideias e gestão de categorias/utilizadores;
- formulário de contacto e consulta administrativa de mensagens;
- recuperação de palavra-passe mediada pelo administrador;
- interface responsiva e navegação por teclado.

## Documentação

Consultar `documentacao/` para decisões técnicas, plano de testes, segurança e histórico de execução dos prompts.

## Limitação atual

O código foi revisto estaticamente, mas não foi executado neste ambiente porque PHP e MySQL não estão disponíveis. A importação, sintaxe e fluxos completos devem ser confirmados através da checklist antes da apresentação.
