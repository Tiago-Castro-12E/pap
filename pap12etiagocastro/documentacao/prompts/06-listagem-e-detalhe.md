# Prompt 06 — Listagem e detalhe das ideias

## Prompt

Implementa `ideias.php` para listar apenas ideias visíveis ao público, com título, resumo, categoria, autor, data, estado e número de votos/comentários. Acrescenta pesquisa por texto, filtro por categoria e ordenação por recentes ou mais votadas. Usa parâmetros validados, prepared statements e paginação.

Cria uma página de detalhe para uma ideia, indicada por um identificador numérico, mostrando descrição completa e informação relacionada. Trata identificadores inválidos, ideias inexistentes e ideias pendentes/rejeitadas sem revelar conteúdo indevido. Administradores e o autor podem ver o estado das suas ideias conforme regras documentadas.

## Critérios de aceitação

- A listagem não duplica ideias devido aos joins.
- Pesquisa, filtros, ordenação e paginação podem ser combinados.
- Entradas inválidas não produzem erros SQL.
- Ideias não públicas respeitam as regras de visibilidade.
- Todo o conteúdo vindo da base de dados está escapado.

