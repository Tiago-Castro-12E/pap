# Revisão de segurança

## Controlos existentes

- passwords guardadas com `password_hash()` e verificadas com `password_verify()`;
- regeneração do identificador depois do login;
- cookies de sessão `HttpOnly`, `SameSite=Lax` e `Secure` em HTTPS;
- headers `nosniff`, `SAMEORIGIN` e política de referência;
- prepared statements para todos os valores externos usados em SQL;
- ordenações e estados limitados a listas fechadas;
- escape HTML centralizado em `escapar()`;
- CSRF em todas as alterações de dados e logout;
- alterações exclusivamente através de POST;
- permissões verificadas no servidor;
- proteção contra remoção do último administrador ativo;
- restrição única de voto como segunda camada;
- erros técnicos enviados para log e mensagens genéricas apresentadas ao público;
- credenciais da base configuráveis por variáveis de ambiente.

## Resultado da auditoria estática

Não foram identificadas vulnerabilidades críticas conhecidas na revisão estática final. Foram pesquisadas concatenação de dados externos em SQL, saídas de dados sem escape, ações por GET, formulários sem CSRF, acessos administrativos sem autorização e passwords em texto simples.

Isto não substitui testes dinâmicos. PHP/MySQL indisponíveis impediram testes de concorrência de votos, sessões, importação SQL, permissões reais e respostas HTTP.

## Limitações e melhorias futuras

- adicionar limitação de tentativas no login e formulário de contacto;
- configurar HTTPS e cookies `Secure` no servidor de produção;
- usar uma conta MySQL própria com privilégios mínimos;
- adicionar Content Security Policy depois de remover eventos JavaScript inline;
- implementar política de retenção/eliminação das mensagens de contacto;
- adicionar recuperação de password por canal verificado;
- criar testes automatizados e análise de dependências caso o projeto cresça.

## Publicação

Antes de publicar:

1. Remover contas e dados de demonstração.
2. Definir variáveis `DB_*` e não usar `root`.
3. Ativar HTTPS.
4. Desativar `display_errors` e manter logs fora da pasta pública.
5. Executar integralmente `documentacao/testes.md`.

