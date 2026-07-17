# Prompt 07 — Votos e comentários

## Prompt

Implementa votos e comentários na página de detalhe da ideia. Apenas utilizadores autenticados podem participar. Cada utilizador pode ter no máximo um voto por ideia; define claramente se o segundo clique remove o voto ou apresenta aviso e mantém esse comportamento consistente. Usa transações quando forem necessárias, prepared statements, CSRF e restrições da base de dados como segunda camada de proteção.

Permite adicionar comentários com validação de tamanho e lista-os por ordem cronológica. Define regras de remoção: autor pode remover o próprio comentário e administrador pode moderar. Usa POST para todas as alterações e redireciona após sucesso para impedir reenvio do formulário.

## Critérios de aceitação

- Não é possível criar votos duplicados.
- Contagens permanecem corretas após várias ações.
- Comentários vazios ou excessivos são rejeitados.
- Todas as alterações usam POST e CSRF.
- Autorizações são verificadas no servidor.
- O refresh não repete uma ação concluída.

