<?php
// views/admin/logout.php
session_start();

// Destrói todas as informações da sessão atual
session_destroy();

// Redireciona de volta para a tela de login na raiz do projeto
header("Location: ../../loginadm.php");
exit;