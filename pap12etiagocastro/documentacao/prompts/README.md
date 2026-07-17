# Plano de execução por prompts

Cada ficheiro contém um prompt pronto a usar numa nova etapa do desenvolvimento.

## Ordem recomendada

1. `01-estrutura-base.md` — corrigir HTML, includes, caminhos e navegação.
2. `02-base-dados.md` — consolidar o esquema e preparar dados de teste.
3. `03-autenticacao-segura.md` — proteger registo, login, sessões e perfil.
4. `04-interface-responsiva.md` — completar o design e a adaptação a telemóvel.
5. `05-categorias-e-submissao.md` — implementar categorias e criação de ideias.
6. `06-listagem-e-detalhe.md` — mostrar, pesquisar e filtrar ideias.
7. `07-votos-e-comentarios.md` — implementar participação dos utilizadores.
8. `08-painel-administracao.md` — moderação, utilizadores e estatísticas.
9. `09-contactos-e-paginas.md` — completar páginas institucionais e contactos.
10. `10-seguranca-testes-documentacao.md` — revisão final, testes e instalação.

## Regras comuns

- Preservar funcionalidades já existentes que estejam corretas.
- Não guardar palavras-passe em texto simples.
- Usar prepared statements nas consultas com valores externos.
- Escapar dados apresentados em HTML.
- Validar permissões no servidor, nunca apenas na interface.
- Não começar a etapa seguinte com erros conhecidos na etapa atual.
- Explicar no fim os ficheiros alterados e como testar.

