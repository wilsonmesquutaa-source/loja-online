<?php

declare(strict_types=1);

$tituloPagina = $tituloPagina ?? 'Administração';

$baseUrl = defined('BASE_URL')
    ? BASE_URL
    : '';

?>

<!doctype html>
<html lang="pt-BR">

<head>

<meta charset="utf-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">


<title>
<?= htmlspecialchars(
    $tituloPagina,
    ENT_QUOTES,
    'UTF-8'
) ?>
</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
rel="stylesheet">


<link
rel="stylesheet"
href="<?= $baseUrl ?>/assets/css/admin.css">


</head>


<body class="admin-body">



<!-- MENU LATERAL -->

<aside id="sidebar" class="sidebar">



<div class="sidebar-header">


    <img
    src="<?= $baseUrl ?>/assets/images/logo.png"
    class="logo-admin"
    alt="Logo">


</div>





<nav class="sidebar-menu">



<a href="<?= $baseUrl ?>/admin">

    <span>
        🏠
    </span>

    <span class="menu-text">
        Dashboard
    </span>

</a>





<a href="<?= $baseUrl ?>/admin/produtos">

    <span>
        📦
    </span>

    <span class="menu-text">
        Produtos
    </span>

</a>





<a href="<?= $baseUrl ?>/admin/clientes">

    <span>
        👤
    </span>

    <span class="menu-text">
        Clientes
    </span>

</a>





<a href="<?= $baseUrl ?>/admin/pedidos">

    <span>
        🛒
    </span>

    <span class="menu-text">
        Pedidos
    </span>

</a>





<a href="<?= $baseUrl ?>/admin/categorias">

    <span>
        📂
    </span>

    <span class="menu-text">
        Categorias
    </span>

</a>





<a href="<?= $baseUrl ?>/">

    <span>
        🌎
    </span>

    <span class="menu-text">
        Loja
    </span>

</a>





</nav>





<button
class="btn-toggle"
onclick="toggleMenu()">

☰

</button>





</aside>







<!-- CONTEÚDO -->

<div id="content" class="content">





<header class="admin-topbar">


<h4>

<?= htmlspecialchars(
    $tituloPagina,
    ENT_QUOTES,
    'UTF-8'
) ?>

</h4>


</header>