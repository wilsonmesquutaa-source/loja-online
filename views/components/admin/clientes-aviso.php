<?php
$aviso = $_SESSION['clientes_aviso'] ?? null;
unset($_SESSION['clientes_aviso']);
if (is_array($aviso)):
?>
<p role="status" class="clientes-aviso <?= $aviso['tipo'] === 'erro' ? 'clientes-aviso--erro' : '' ?>"><?= htmlspecialchars($aviso['texto'], ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
