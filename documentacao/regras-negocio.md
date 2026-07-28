# Regras de negócio

Um produto inativo não poderá aparecer no catálogo de produtos disponíveis para os clientes.
Um produto indisponível não poderá ser adicionado ao carrinho.
A quantidade de produtos adicionada ao carrinho não poderá ultrapassar a quantidade disponível em estoque.
O valor total do pedido deverá ser calculado pelo servidor, utilizando os valores cadastrados no banco de dados.
Somente clientes cadastrados e autenticados poderão finalizar pedidos.
O pedido deverá possuir cliente, itens, endereço de entrega e forma de pagamento antes de ser confirmado.
Um pedido deverá ser registrado no sistema antes de ser enviado para processamento de pagamento.
Um pedido somente poderá ser considerado confirmado após a validação da forma de pagamento.
Somente administradores poderão cadastrar, alterar ou remover produtos.
Somente administradores poderão alterar preços, categorias e disponibilidade dos produtos.
O cliente poderá visualizar apenas seus próprios dados e histórico de pedidos.
Um cliente não poderá alterar informações de pedidos já finalizados.
Um pedido cancelado não poderá avançar para os status de preparação, entrega ou finalização.
Um pedido finalizado não poderá retornar para status anteriores.
A exclusão de clientes que possuem pedidos registrados deverá preservar o histórico das vendas realizadas.
Os itens de um pedido deverão manter o preço registrado no momento da compra, mesmo que o produto tenha seu valor alterado posteriormente.
O sistema deverá impedir pedidos sem itens cadastrados.
O sistema deverá impedir a finalização de pedidos sem endereço de entrega válido.
Somente administradores poderão visualizar todos os pedidos realizados na plataforma.
O estoque deverá ser atualizado após a confirmação do pedido.