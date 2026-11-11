# Financeiro e temas sazonais

## Banco de dados

Arquivo completo: `database/07_financeiro_temas.sql`.

A migração foi aplicada no banco local configurado no projeto. Nesse mesmo banco não é necessário importar novamente. O arquivo SQL permite instalar os módulos em outra cópia da loja.

Para importar pelo phpMyAdmin:

1. Exporte um backup do banco existente.
2. Selecione o banco usado pela loja (o mesmo configurado em `.env`).
3. Abra **Importar**, selecione **07_financeiro_temas.sql** e execute.
4. Atualize o painel administrativo. Os menus **Financeiro** e **Aparência da loja** estarão disponíveis.

A migração acrescenta tabelas, índices, chaves estrangeiras, unidades, categorias de insumos, configurações e temas em rascunho. Não apaga produtos, clientes, pedidos ou imagens existentes; não inventa custos para vendas antigas; não ativa campanhas. Usa InnoDB e utf8mb4, compatível com MariaDB do XAMPP e MySQL com suporte a colunas geradas. Não cria nem troca o banco selecionado.

Alternativa pelo terminal, na pasta do projeto:

```powershell
C:\xampp\php\php.exe database/aplicar_financeiro_temas.php
```

PHP precisa das extensões BCMath, PDO MySQL, GD, Fileinfo, Mbstring e Calendar. O instalador é exclusivo do terminal.

## Começar pelo financeiro

1. Em **Insumos**, cadastre ingredientes e embalagens usando uma unidade-base. Cadastre fornecedores e vínculos, se necessário.
2. Em **Compras**, informe quantidade, unidade e valor total de cada item. Para pacote/caixa, informe seu conteúdo na unidade-base. kg/g e L/ml têm conversão automática; massa não se converte em volume.
3. Salve o rascunho, confira e confirme a entrada. Somente a confirmação altera estoque e custo. Frete, desconto e outros custos são rateados proporcionalmente, com o resíduo de centavos no último item.
4. Em **Fichas técnicas**, informe ingredientes, embalagens, rendimento, perdas e a quantidade física contida em cada item vendido. Exemplo: 25 para uma porção de 25 salgados, 100 para um cento. O custo da unidade física é diferente do custo de um item vendido.
5. Cadastre custos indiretos, sua vigência e a produção mensal prevista para o rateio. Use o campo de rateio manual da ficha para valores atribuídos diretamente à receita. Não duplique valores já rateados automaticamente.
6. Cadastre despesas em cada competência. A opção recorrente identifica a recorrência; não gera lançamentos automaticamente.
7. Configure taxas estimadas de Pix/cartão e metas no final da visão geral. As taxas começam em zero e precisam ser ajustadas à sua operação.

Cada gravação de ficha cria uma versão; versões anteriores permanecem no banco. Ingredientes sem preço impedem a gravação de um custo completo. A perda geral reduz o rendimento; a perda extra aumenta o consumo do ingrediente. Nunca se arredonda o custo por grama para centavos: cálculo com BCMath e armazenamento DECIMAL.

Compras confirmadas podem ser canceladas quando sua entrada é a última movimentação de cada insumo. Isso restaura saldo e custo anterior. Havendo movimentações posteriores, use devolução/ajuste com motivo; o sistema não reescreve a sequência histórica. Consumo de produção, perdas e ajustes são registrados na tela de insumos. A venda não dá baixa automática em matéria-prima.

Os novos itens de pedido recebem um custo calculado e imutável na criação do pedido, dentro da transação existente do checkout. Também ficam registradas as taxas configuradas naquele momento. Pedidos antigos e itens sem ficha/preços aparecem como **Não disponível** nos indicadores que dependem de custo. Uma ficha cadastrada hoje não preenche retroativamente esses pedidos.

## Indicadores e relatórios

O período considera a aprovação de pagamentos. Pedidos cancelados/reembolsados são excluídos. Receita líquida é subtotal dos produtos menos descontos, sem frete. Taxas são estimativas sobre o total cobrado, e não conciliação do extrato da operadora.

- Lucro bruto: receita líquida menos custo histórico vendido.
- Margem bruta: lucro bruto dividido pela receita líquida.
- Contribuição: receita líquida menos custos diretos variáveis e taxas; exclui os rateios indiretos tratados como fixos nesta estimativa.
- Resultado operacional estimado: lucro bruto menos taxas, despesas e custos indiretos não rateados.
- Custos indiretos mensais são proporcionais aos dias de vigência no período; o valor já rateado nos itens é descontado para evitar dupla contagem.
- Comparação usa intervalo anterior de mesma duração.
- Rentabilidade por produto usa custo atual e preço da categoria, incluindo preço de revenda. Não altera preços automaticamente.
- Os nove gráficos usam dados do banco, com SVG/JavaScript locais e tabelas acessíveis. Não dependem de Chart.js ou CDN.
- Sem histórico suficiente, gráficos/indicadores não exibem um resultado fictício.
- A tabela de pedidos pode ser exportada para CSV; a página pode ser impressa em PDF pelo navegador.

Histórico inclui compras, preços, movimentações, snapshots e auditoria com administrador, data, antes e depois. Despesas recorrentes e produção mensal são parâmetros informados pelo administrador; o módulo não constitui escrituração contábil nem conciliação bancária automática.

## Aparência da loja

Existem um tema padrão permanente e 13 temas sazonais iniciais em rascunho, com paletas e decorações leves. As imagens de mascotes/logos específicos devem ser enviadas à biblioteca; os temas usam os arquivos originais quando não há substituição.

1. Abra um rascunho e configure nome, cores, efeitos e selo.
2. Envie imagens à biblioteca ou associe uma imagem existente ao tema. Ajuste escala de 10% a 200%, posição e opacidade.
3. Abra **Pré-visualizar na loja**. A rota `/admin/temas/preview/{id}` exige login administrativo e não publica o tema. A prévia mostra a home; os links normais levam à versão pública.
4. Publique para disponibilizar o tema ao agendamento, ou use **Ativar agora** para fixá-lo manualmente.
5. Para alterar um tema publicado, clique em **Editar novamente**. Ele volta a rascunho mantendo o mesmo ID, imagens e configurações, sem criar cópias. Enquanto edita, a loja usa outro tema vigente ou o padrão. Publique novamente ao concluir. **Criar outra cópia** é opcional.
6. **Excluir tema** retira o tema da lista principal e da loja e cancela seus agendamentos. O histórico e os arquivos compartilhados são preservados. A lista **Temas excluídos** permite restaurar como rascunho. O tema padrão não pode ser excluído.
7. **Voltar ao padrão** fixa o padrão manualmente. **Retomar agendamentos** libera a escolha automática.

Prioridade: tema manual publicado; agenda vigente com maior prioridade; prioridade do tema; menor ID em empates; tema padrão. Recurso visual: tema ativo → tema padrão → arquivo original. Arquivos ausentes ou inválidos são ignorados.

Agenda por intervalo de data/hora, intervalo anual (inclusive dezembro/janeiro) ou data móvel. Páscoa, Carnaval, Dia das Mães, Dia dos Pais, Black Friday e Cyber Monday são calculados para cada ano. Fuso: America/Fortaleza. A mudança acontece na próxima requisição da loja; não depende de cron. O histórico automático registra a primeira resolução após a troca.

Cache é restrito à requisição: publicação/ativação aparece na próxima visita, sem cache persistente obsoleto. Prévia usa cabeçalho privado/no-store e não altera o estado público nem seu histórico.

Uploads: PNG/JPG/WebP/AVIF suportados pelo GD, máximo 5 MB, 6000 px por lado e 16 milhões de pixels. Os arquivos são decodificados e regravados em WebP, com nome aleatório. SVG, HTML e scripts não são aceitos. O diretório tem execução e listagem bloqueadas.

Efeitos têm poucos elementos, não recebem cliques, são ocultos das tecnologias assistivas e respeitam redução de movimento. O contraste de texto/fundo explicitamente definido é validado. Confira também a legibilidade das imagens enviadas na prévia.

## Validação

```powershell
C:\xampp\php\php.exe tests/financeiro-temas.php
C:\xampp\php\php.exe tests/imagens-admin.php
```

Os testes financeiros/de temas trabalham dentro de transação e revertem os dados de teste. Cobrem unidades, custo médio, frete/desconto, dupla confirmação, estorno, perdas, versões, snapshots, pedidos cancelados, calendário móvel, fronteiras de vigência, virada de ano, desempate, prévia e fallback.

Novas telas administrativas usam CSS e JavaScript próprios, sem Bootstrap. A infraestrutura visual já existente da loja pública foi mantida.
# Atualização dos enfeites do menu

No editor, selecione a temporada em **Decoração do menu**. A faixa muda imediatamente na prévia do formulário. Há 12 modelos locais em SVG, inspirados na referência visual, além da opção sem decoração.

- **Salvar rascunho** guarda a edição.
- **Salvar e pré-visualizar** guarda e abre a versão atualizada, restrita ao administrador.
- **Salvar e aplicar** guarda, publica e ativa o tema para a loja na mesma transação.
- **Ver versão salva** mostra apenas o que já foi salvo.

Para alterar um tema publicado, use **Editar novamente**, que mantém o mesmo registro. A exclusão e a restauração continuam disponíveis. As faixas ficam dentro do menu e se adaptam ao celular. Imagens de fundo do cabeçalho têm prioridade sobre a paleta.

Esta atualização usa `tema_configuracoes.decoracao_header` nas tabelas existentes; não requer nova migração SQL nem cria cópias dos temas.
