# Ajuste de imagens do painel

Produtos, cards de categorias, fundos de categorias e banners da Home aceitam zoom de 10% a 200%. Novas imagens começam em 100%, mostrando a imagem inteira proporcionalmente ajustada à área de prévia; esse percentual não representa os pixels originais do arquivo. Valores menores deixam margens e valores maiores podem recortar a imagem. Os valores salvos continuam sendo carregados na edição.

O site público usa a escala salva em produtos, categorias, destaques e banners. As imagens mantêm suas proporções, sem esticar para preencher o quadro. Os controles das categorias ficam agrupados junto à prévia e se reorganizam em telas menores.

## Banco e publicação

As tabelas `produto_imagens` e `categoria_imagens` já possuem `escala`. Os novos controles usam também `escala DECIMAL(4,2) DEFAULT 1.00` em `categoria_banners` e `banners_home`.

Executar `php database/aplicar_zoom_imagens.php` antes de publicar em outro ambiente. Esse comando só adiciona as colunas ausentes e já foi aplicado no banco local. Não altera os valores de enquadramentos existentes.

## Verificação

`php tests/imagens-admin.php` verifica limites, validação dos controllers e consultas de leitura do catálogo. Uma pasta de destino opcional gera páginas de prévia com dados fictícios para conferir os controles no navegador.
