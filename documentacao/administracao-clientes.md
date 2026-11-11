# Administração de clientes e privacidade

A página `/admin/clientes` permite localizar cadastros para atendimento, consultar a verificação do e-mail e abrir os pedidos de um cliente. Não concede novos privilégios: usa a autenticação administrativa existente.

## Decisões desta implementação

- Busca por nome ou e-mail, filtro de verificação e paginação de 20 registros, processados no servidor.
- E-mail parcialmente oculto na listagem, inclusive no HTML; contato completo somente nos detalhes. A máscara reduz exposição, mas não anonimiza os dados.
- Consultas com campos explícitos: senhas, hashes, tokens de autenticação, identificadores do Google e dados de cartão não são carregados.
- Endereços não aparecem na listagem; os dados de entrega continuam vinculados ao atendimento de cada pedido, na página existente.
- Respostas de clientes usam `Cache-Control: no-store`, `Referrer-Policy: no-referrer` e `X-Robots-Tag: noindex, nofollow`. Autenticação, e não essas diretivas, controla o acesso.
- Sem exportação em massa, impersonação, confirmação manual de e-mail ou edição direta de dados. Recuperação de senha e exclusão controlada estão descritas abaixo.
- Interface própria em CSS e JavaScript, sem carregar o framework CSS do Bootstrap. O cabeçalho compartilhado conserva a biblioteca independente de ícones Bootstrap Icons usada no painel.

## Uso adequado do painel

Consultar os dados estritamente necessários para atender o cliente, cumprir o pedido ou outra finalidade legítima e documentada. Restringir cada acesso às responsabilidades do administrador. A existência de um cadastro não autoriza qualquer uso dos dados.

Correção, exportação para o titular e eliminação podem ser ações legítimas, mas precisam de identificação do solicitante, análise do pedido e registro do atendimento. Não são proibidas pela LGPD. Um futuro fluxo deve prever essas etapas e critérios de retenção: excluir uma conta não implica apagar indiscriminadamente registros sujeitos a obrigação legal. A confirmação de e-mail depende de comprovação de controle do endereço, não da preferência do operador.

Não reutilizar cadastros para campanhas, compartilhar listas, classificar pessoas de forma discriminatória ou consultar informações por curiosidade. Consentimento não é a única base legal possível; a base adequada depende da finalidade. Nunca exibir credenciais ou dados completos de pagamento ao operador.

## Limites e próximas decisões

O projeto atualmente identifica administradores por sessão. As novas ações registram auditoria; não há perfis por função, canal de solicitações dos titulares nem política automática de retenção. Esses controles precisam ser definidos conforme a operação; esta página, isoladamente, não certifica conformidade com a LGPD. Pesquisas por e-mail podem permanecer no histórico local e nos logs de acesso, por usarem GET: esses registros também exigem acesso restrito e retenção definida.

## Recuperação de senha e exclusão

Nos detalhes de cada cliente, “Enviar link de redefinição” fica disponível para contas com senha local e e-mail verificado. O administrador confirma que recebeu a solicitação. O sistema usa o SMTP existente e `APP_URL` do `.env`, nunca o cabeçalho Host da requisição. Em produção, APP_URL exige HTTPS; HTTP é aceito apenas em localhost/127.0.0.1.

O link contém 32 bytes aleatórios; o banco armazena apenas SHA-256. Validade de 30 minutos, uso único, revogação do link anterior a cada reenvio, intervalo mínimo de 5 minutos e máximo de 3 envios por cliente por dia UTC e 10 por administrador por hora. Uma falha SMTP desfaz a alteração no banco. Senha ou e-mail alterados invalidam o link. A página de recuperação não carrega conteúdo de terceiros, impede referência externa e tira o token da URL por redirecionamento. Logs do servidor ainda podem conter a URL inicial e precisam de proteção e retenção adequadas.

O cliente define uma senha de 12 a 72 bytes, sem preenchimento ou conhecimento do administrador. A gravação da senha, o consumo do token e o incremento da versão da sessão ocorrem na mesma transação. Sessões antigas são rejeitadas na próxima requisição e não há login automático pela redefinição. Contas exclusivamente Google recuperam o acesso pelo Google. A troca assistida de e-mail e o encerramento de contas com pedidos não fazem parte deste fluxo.

A exclusão só ocorre sem pedidos, com motivo enumerado, número do cadastro e confirmação explícita. O servidor verifica novamente o vínculo em transação com bloqueio; a chave estrangeira também impede apagar clientes vinculados a pedidos. Endereços, carrinhos/itens, dispositivos e tokens são removidos pelas cascatas do banco; a foto local exclusiva é removida após a transação. Falhas na limpeza da foto geram aviso no log para tratamento. A exclusão invalida a sessão do cliente na próxima requisição.

`cliente_admin_auditoria` registra o ID do administrador (ou NULL na conclusão pelo cliente), ID histórico do cliente, ação, motivo padronizado e instante. Não guarda nome, e-mail, senha ou token. A retenção dessa auditoria precisa ser definida pela operação; a exclusão do cadastro não apaga automaticamente registros de auditoria.

### Instalação e testes

Aplicar `database/05_clientes_seguranca.sql` antes de publicar o código, ou executar `php database/aplicar_migracao_clientes.php` pela linha de comando. A migração cria somente `cliente_recuperacao` e `cliente_admin_auditoria`, preserva os dados atuais e pode ser reaplicada. Já foi aplicada no banco local durante a implementação. Em outra hospedagem, aplicar a mesma migração e conferir APP_URL/SMTP antes do uso.

`php tests/clientes-acoes.php` testa com SQLite em memória e remetente simulado: uso único, expiração, limites, rollback de falha SMTP, revogação de sessão e exclusão protegida. Não envia e-mail nem exclui clientes reais. `php tests/admin-clientes.php --mysql` confere as consultas de leitura no banco configurado.

## Fontes consultadas em 17/09/2026

- [LGPD — texto compilado, especialmente arts. 6, 7, 16, 18, 37 e 46](https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709compilado.htm): princípios, bases legais, retenção, direitos, registros e segurança.
- [ANPD — Titular de Dados](https://www.gov.br/anpd/pt-br/assuntos/titular-de-dados): direitos e atendimento de solicitações.
- [ANPD — Perguntas frequentes](https://www.gov.br/anpd/pt-br/acesso-a-informacao/perguntas-frequentes): tratamento de dados e orientações de segurança.

As escolhas de interface acima são decisões de implementação orientadas por esses princípios, não requisitos literais de disposição de telas impostos pela lei.
