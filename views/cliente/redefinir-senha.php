<?php
declare(strict_types=1);
$esc = static fn($v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="referrer" content="no-referrer"><title>Redefinir senha — Cantim do Lanche</title><link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/cliente/recuperacao.css"></head>
<body><main class="recuperacao"><img src="<?= BASE_URL ?>/assets/images/logo.webp" alt="Cantim do Lanche" width="150"><h1><?= !empty($sucesso) ? 'Senha atualizada' : 'Redefinir senha' ?></h1>
<?php if (!empty($erro)): ?><p class="aviso aviso--erro" role="alert"><?= $esc($erro) ?></p><?php endif; ?>
<?php if (!empty($sucesso)): ?><p role="status">Sua senha foi alterada e as sessões anteriores foram encerradas. Entre novamente para continuar.</p>
<?php elseif ($valido): ?><p>Escolha sua nova senha. Não compartilhe sua senha ou o link de recuperação.</p><form method="POST" action="<?= BASE_URL ?>/redefinir-senha"><input type="hidden" name="_token" value="<?= $esc(\App\Helpers\Csrf::gerarCliente()) ?>"><label>Nova senha<input type="password" name="senha" minlength="12" maxlength="72" autocomplete="new-password" required aria-describedby="regra-senha"></label><small id="regra-senha">Use pelo menos 12 caracteres. Limite de 72 bytes; acentos podem ocupar mais de um byte.</small><label>Confirmar nova senha<input type="password" name="confirmacao" minlength="12" maxlength="72" autocomplete="new-password" required></label><button type="submit">Salvar nova senha</button></form>
<?php else: ?><p>Este link é inválido, expirou ou já foi utilizado. Peça um novo link ao atendimento.</p><?php endif; ?>
<a href="<?= BASE_URL ?>/login">Voltar ao login</a></main></body></html>
