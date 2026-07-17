# Prompt 02 — Base de dados

## Prompt

Revê e melhora os ficheiros SQL do projeto sem implementar ainda as páginas PHP funcionais. Atualiza `sql/criar_tabelas.sql` para criar e selecionar a base de dados, usar `utf8mb4`, datas automáticas adequadas, restrições, índices e regras coerentes de integridade referencial. Mantém as entidades utilizador, categoria, ideia, comentário e voto. Decide e documenta o comportamento de eliminação das relações. Torna nomes de categorias únicos e mantém um voto por utilizador e ideia.

Preenche `sql/inserts.sql` com categorias, utilizadores de demonstração seguros e algumas ideias/comentários/votos coerentes. Se forem necessárias hashes de palavras-passe, explica como foram geradas e quais são as credenciais apenas de desenvolvimento. Preenche `sql/consultas.sql` com consultas úteis e comentadas para listagens, contagens, filtros e dashboard. Faz os scripts repetíveis quando isso for razoável e documenta a ordem de execução.

## Critérios de aceitação

- Uma instalação vazia pode ser preparada seguindo uma ordem documentada.
- Todas as tabelas usam `utf8mb4` e InnoDB.
- As relações e eliminações têm comportamento explícito.
- Existem dados suficientes para testar a aplicação.
- As consultas de demonstração correspondem ao esquema final.

