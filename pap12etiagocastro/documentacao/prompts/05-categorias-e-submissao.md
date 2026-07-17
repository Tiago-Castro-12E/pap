# Prompt 05 — Categorias e submissão de ideias

## Prompt

Implementa a submissão de ideias em `submeter.php`. Apenas utilizadores autenticados podem submeter. Carrega categorias ativas da base de dados, valida título, descrição e categoria no servidor, usa prepared statements e proteção CSRF. A nova ideia deve ficar pendente e usar a data definida pela base de dados. Mostra mensagens de erro específicas e preserva os valores válidos após uma falha.

Implementa também a base da gestão de categorias para administradores, incluindo criar e listar categorias, validação de duplicados e proteção de autorização. Não permitas remover uma categoria que esteja a ser usada sem apresentar uma decisão segura e clara.

## Critérios de aceitação

- Visitantes são encaminhados para login ao tentar submeter.
- Uma submissão válida cria uma ideia pendente ligada ao autor.
- Categoria inválida ou manipulada é rejeitada no servidor.
- O formulário tem CSRF e prepared statements.
- Só administradores gerem categorias.

