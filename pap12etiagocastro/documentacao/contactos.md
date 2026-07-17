# Estratégia de contactos

Não foi encontrada configuração de servidor SMTP ou serviço de email no projeto. Para não simular entregas que não aconteceriam, o formulário guarda as mensagens na tabela `mensagem_contacto`.

## Fluxo

1. O visitante preenche nome, email, assunto e mensagem.
2. O servidor valida limites, email e token CSRF.
3. A mensagem é guardada como `Nova` através de prepared statement.
4. Um administrador consulta `admin/mensagens.php` e pode marcá-la como `Lida`.

Todos os dados são escapados ao serem apresentados. Não são recolhidos IP, localização ou outros dados desnecessários.

Antes de uma publicação real deve ser definida uma política de privacidade e de retenção/eliminação destas mensagens.

