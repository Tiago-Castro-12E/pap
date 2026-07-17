# Prompt 01 — Estrutura base

## Prompt

Analisa o projeto `pap12etiagocastro` e corrige apenas a estrutura comum da aplicação. Cria um cabeçalho reutilizável com documento HTML válido, metadados, título configurável, CSS e navegação; ajusta o rodapé para fechar corretamente o documento. Elimina ou integra a duplicação entre `menu.php` e `includes/menu.php`. Evita caminhos absolutos rígidos sempre que possível e garante que os includes funcionam também dentro de `admin/`. O menu deve distinguir visitante, utilizador autenticado e administrador. Não implementes ainda ideias, votos, comentários ou administração. Preserva o aspeto existente tanto quanto possível.

Antes de terminar, verifica sintaxe PHP se o executável estiver disponível e procura includes, links ou recursos quebrados. Resume as alterações e indica como testar manualmente.

## Critérios de aceitação

- Todas as páginas implementadas produzem HTML válido e completo.
- CSS e navegação não são carregados em duplicado.
- Existe apenas uma implementação ativa do menu.
- O menu muda conforme a sessão e o tipo de utilizador.
- As páginas públicas e administrativas conseguem usar os mesmos componentes.

