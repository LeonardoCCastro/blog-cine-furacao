# Funcionalidades da Aplicacao

## Area publica do blog
- Listagem da home com os 6 posts mais recentes publicados.
- Pagina "Todas as postagens" com paginacao.
- Busca de posts por titulo, resumo ou conteudo.
- Filtro por categoria e subcategoria.
- Pagina de detalhe do post por slug amigavel.
- Exibicao de imagem de capa, autor, categoria/subcategoria e data.
- Contador de visualizacoes por post (incrementa uma vez por sessao).
- Comentarios em posts (com nome e mensagem).
- Suporte a comentarios de visitantes e usuarios autenticados.
- Tema claro/escuro com preferencia salva no navegador.

## Autenticacao e conta
- Login e logout.
- Recuperacao de senha (esqueci a senha + redefinicao por token).
- Fluxo de verificacao de email (rotas disponiveis).
- Atualizacao de perfil (nome e email).
- Alteracao de senha da conta autenticada.
- Exclusao de conta com validacao de senha atual.
- Regra de seguranca: impede excluir a ultima conta administradora.

## Painel administrativo
- Acesso restrito por middleware para usuarios com `is_admin`.
- Dashboard com metricas:
  - total de posts
  - posts publicados
  - rascunhos
  - total de visualizacoes
  - total de comentarios
- Listas de apoio no dashboard:
  - posts recentes
  - top posts por visualizacoes

## Gestao de posts (admin)
- CRUD completo de posts.
- Publicacao e rascunho.
- Upload de imagem de capa do post.
- Remocao automatica da imagem antiga ao atualizar/substituir.
- Slug unico gerado automaticamente a partir do titulo.
- Editor rico (Quill) para conteudo.
- Upload de imagens dentro do editor (inclusive colar/arrastar).
- Insercao de videos por URL (com normalizacao para YouTube embed).
- Normalizacao de links locais de imagem para `/storage/...`.
- Listagem administrativa com:
  - categoria/subcategoria
  - autor
  - status (publicado/rascunho)
  - visualizacoes
  - quantidade de comentarios

## Gestao de categorias (admin)
- Criacao, edicao e exclusao de categorias.
- Suporte a hierarquia categoria -> subcategoria.
- Criacao de subcategoria direto no formulario de post.
- Slug unico para categorias e subcategorias.
- Bloqueio de exclusao de categoria pai com subcategorias existentes.
- Exibicao de contagem de posts por categoria/subcategoria.

## Gestao de comentarios (admin)
- Remocao de comentarios diretamente na edicao do post.

## Notificacoes administrativas
- Notificacao para todos os administradores quando um novo comentario e enviado.
- Listagem de notificacoes recentes no contexto admin.
- Abertura da notificacao levando ao post e comentario correspondente.
- Acao para marcar todas as notificacoes como lidas.

## Gestao de administradores
- Listagem de administradores.
- Criacao de novo administrador.
- Edicao de dados do administrador.
- Redefinicao opcional de senha ao editar.
- Exclusao de administrador.
- Regra de seguranca para evitar autoexclusao quando houver apenas 1 admin.
