# Categorias e ideias

## Submissão

- apenas utilizadores autenticados podem abrir `submeter.php`;
- título entre 5 e 150 caracteres;
- descrição entre 20 e 5000 caracteres;
- a categoria é confirmada na base de dados e tem de estar ativa;
- a ideia é associada ao identificador guardado na sessão;
- estado e data não são aceites do formulário: a base de dados aplica `Pendente` e a data atual;
- o formulário usa CSRF, prepared statements e redirecionamento depois do sucesso;
- valores válidos são preservados quando existe um erro.

## Gestão de categorias

- disponível apenas a administradores;
- permite criar, listar, ativar e desativar categorias;
- nomes duplicados são impedidos pela aplicação e pela restrição única da base de dados;
- categorias com ideias não são removidas;
- desativar uma categoria retira-a de novos formulários e filtros, sem eliminar ideias existentes;
- todas as alterações usam POST, CSRF e prepared statements.

## Listagem pública

`ideias.php` mostra exclusivamente ideias com estado `Aprovada` ou `Implementada`.

Inclui:

- pesquisa no título e descrição;
- filtro por categoria ativa;
- ordenação por data ou número de votos;
- paginação de nove ideias;
- autor, categoria, data, estado e totais de votos/comentários;
- consultas sem duplicação por joins, usando subconsultas para as contagens;
- preservação dos filtros ao mudar de página.

## Visibilidade do detalhe

- qualquer visitante pode abrir uma ideia aprovada ou implementada;
- o autor autenticado pode consultar as próprias ideias pendentes ou rejeitadas;
- um administrador pode consultar qualquer ideia;
- para os restantes utilizadores, uma ideia privada responde como não encontrada, sem revelar título, estado ou autor;
- identificadores inválidos e ideias inexistentes recebem igualmente resposta 404.

## Testes pendentes

Quando PHP e MySQL estiverem disponíveis:

1. Confirmar que um visitante é enviado para login ao abrir `submeter.php`.
2. Testar títulos e descrições abaixo/acima dos limites.
3. Manipular o identificador da categoria e confirmar a rejeição.
4. Submeter uma ideia e confirmar estado `Pendente`, autor e data.
5. Reenviar/atualizar a página depois do sucesso e confirmar que não duplica a ideia.
6. Entrar como administrador e criar, duplicar, ativar e desativar categorias.
7. Confirmar que categorias inativas desaparecem da submissão.
8. Combinar pesquisa, categoria, ordenação e várias páginas.
9. Confirmar que a listagem só mostra estados públicos.
10. Abrir uma ideia pendente como visitante, autor, outro utilizador e administrador.
11. Testar identificadores vazios, negativos, texto e inexistentes.
12. Confirmar visualmente cartões, filtros, tabelas e detalhe entre 320 e 1440 px.

