# Estado do projeto

Este ficheiro regista a execução dos prompts definidos em `documentacao/prompts/`.

## Prompt 01 — Estrutura base

Estado: **concluído em 17 de julho de 2026**.

Alterações realizadas:

- documento HTML centralizado em `includes/menu.php`;
- título de página configurável e metadados comuns;
- rodapé responsável pelo fecho de `body` e `html`;
- remoção do `menu.php` duplicado da raiz;
- caminhos calculados a partir da localização pública da aplicação;
- compatibilidade dos componentes comuns com páginas em `admin/`;
- menu distinto para visitante, utilizador autenticado e administrador;
- indicação da página ativa na navegação;
- includes das páginas implementadas baseados em `__DIR__`;
- remoção de referências a imagens que não existiam no projeto.

Verificação:

- pesquisa estática de includes, documentos HTML, caminhos rígidos e recursos;
- o executável PHP não está disponível neste ambiente, pelo que não foi possível executar `php -l`;
- a visualização no browser deve ser confirmada quando o servidor local estiver disponível.

## Prompt 02 — Base de dados

Estado: **concluído por revisão estática em 17 de julho de 2026**.

Alterações realizadas:

- criação e seleção automática da base de dados;
- tabelas InnoDB com `utf8mb4`;
- datas automáticas, índices, restrições nomeadas e estados atualizados;
- regras explícitas `RESTRICT` e `CASCADE`;
- utilizadores e categorias com desativação lógica;
- dados repetíveis de demonstração com hashes bcrypt;
- ideias, comentários e votos coerentes para testes;
- dez consultas comentadas para listagens, filtros, detalhe e dashboard;
- guia de instalação e decisões em `documentacao/base-de-dados.md`.

Verificação:

- relações, nomes de campos e consultas foram cruzados estaticamente;
- MySQL/MariaDB não está disponível, pelo que a importação real continua pendente;
- o login das contas com hash depende da implementação do Prompt 03.

## Prompt 03 — Autenticação segura

Estado: **concluído por revisão estática em 17 de julho de 2026**.

Alterações realizadas:

- funções centralizadas de sessão, URLs, autenticação, autorização, CSRF e escape;
- cookies de sessão `HttpOnly`, `SameSite=Lax` e `Secure` em HTTPS;
- registo com validação, confirmação e `password_hash()`;
- login com prepared statement, `password_verify()` e erro genérico;
- regeneração do identificador após autenticação;
- perfil protegido, consulta preparada e saída escapada;
- logout apenas por POST, protegido por CSRF e com remoção do cookie;
- ligação à base de dados atualizada para `utf8mb4` sem exposição do erro técnico;
- plano de testes e incompatibilidade das passwords antigas documentados.

Verificação:

- pesquisa estática confirmou a ausência das antigas comparações de texto simples;
- todos os formulários desta etapa possuem token CSRF;
- PHP e MySQL continuam indisponíveis, portanto os testes de execução estão pendentes.

## Prompt 04 — Interface responsiva

Estado: **concluído por revisão estática em 17 de julho de 2026**.

Alterações realizadas:

- reconstrução da folha de estilos com variáveis e componentes consistentes;
- correspondência entre as classes da homepage e o CSS;
- estilos para botões, cartões, formulários, mensagens, perfil, tabelas e dashboard;
- rodapé completo e responsivo;
- breakpoints para desktop, tablet, telemóvel e ecrãs a partir de 320 px;
- foco visível, labels associadas e respeito por redução de movimento;
- homepage organizada semanticamente dentro de `main`;
- ícones convertidos em entidades HTML e ausência de imagens inexistentes;
- convenções de interface documentadas em `documentacao/interface.md`.

Verificação:

- pesquisa estática confirmou que não existem referências locais a imagens;
- regras responsivas e componentes foram cruzados com o HTML atual;
- a renderização em browser continua pendente por indisponibilidade do servidor PHP.

## Prompt 05 — Categorias e submissão

Estado: **concluído por revisão estática em 17 de julho de 2026**.

Alterações realizadas:

- formulário de submissão exclusivo para utilizadores autenticados;
- validação de título, descrição e categoria ativa;
- criação de ideias pendentes com autor obtido da sessão;
- CSRF, prepared statements e redirecionamento após sucesso;
- preservação dos campos válidos quando existe erro;
- gestão administrativa para criar, listar, ativar e desativar categorias;
- categorias associadas a ideias nunca são eliminadas pela interface.

## Prompt 06 — Listagem e detalhe

Estado: **concluído por revisão estática em 17 de julho de 2026**.

Alterações realizadas:

- listagem apenas de ideias aprovadas ou implementadas;
- pesquisa, categoria, ordenação e paginação combináveis;
- cartões com autor, categoria, data, estado, votos e comentários;
- contagens sem duplicações provocadas por joins;
- detalhe criado em `ideia.php` com prepared statement;
- regras de visibilidade para público, autor e administrador;
- respostas 404 uniformes para ideias inexistentes ou não autorizadas;
- estilos responsivos para formulários, administração, filtros, cartões e detalhe;
- funcionamento e testes documentados em `documentacao/ideias-e-categorias.md`.

Verificação:

- todos os formulários de alteração destas etapas têm CSRF;
- parâmetros externos usados em SQL são enviados por prepared statements ou validados contra listas fechadas;
- o CSS mantém o mesmo número de chavetas de abertura e fecho;
- PHP e MySQL continuam indisponíveis, pelo que os testes reais permanecem pendentes.

Próxima etapa: `07-votos-e-comentarios.md`.
