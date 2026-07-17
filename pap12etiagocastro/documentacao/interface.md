# Interface e responsividade

## Identidade visual

A aplicação mantém a identidade verde original, com variáveis CSS para cores, sombras, raios e transições. A fonte principal continua a ser Poppins, com Arial como alternativa quando a fonte externa não estiver disponível.

Não existem dependências visuais adicionais. As referências às imagens inexistentes foram removidas e a zona principal usa um fundo criado apenas com CSS.

## Componentes preparados

- cabeçalho, navegação e identificação da página ativa;
- botões primários, secundários e de sessão;
- hero, cartões de funcionalidades e frase de destaque;
- páginas internas, cartões e perfil;
- inputs, selects, textareas e labels;
- mensagens de erro, sucesso e aviso;
- tabelas com contentor de deslocamento horizontal;
- grelha de cartões para o futuro dashboard;
- rodapé em três colunas;
- foco visível para navegação por teclado;
- redução de animações conforme a preferência do sistema.

## Breakpoints

- acima de 1050 px: navegação e ações na mesma linha;
- até 1050 px: navegação ocupa uma segunda linha e os cartões adaptam-se;
- até 700 px: cabeçalho vertical, menu em grelha e conteúdo numa coluna;
- até 420 px: menu em duas colunas, botões da homepage a toda a largura e cartões com menor espaçamento;
- largura mínima suportada: 320 px.

## Convenções para páginas futuras

- envolver o conteúdo principal em `<main class="page">`;
- usar `.container` para limitar a largura;
- usar `.card` ou um cartão específico para blocos destacados;
- envolver tabelas numa `<div class="table-responsive">`;
- usar `.erro`, `.sucesso` ou `.aviso` com `role` adequado;
- associar sempre cada `label` ao respetivo campo através de `for` e `id`;
- usar `.btn`, `.btn-primary` ou `.btn-outline` nas ações apresentadas como botões.

## Verificação pendente

A estrutura e as regras CSS foram verificadas estaticamente, mas não foi possível renderizar as páginas porque o servidor PHP não está disponível. Quando o XAMPP estiver operacional, devem ser verificadas visualmente as larguras de 320, 375, 768, 1024 e 1440 px, além da navegação integral por teclado.

