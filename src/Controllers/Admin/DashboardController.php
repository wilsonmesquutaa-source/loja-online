<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\Csrf;

final class DashboardController extends Controller
{
    public function index(): void
    {
        // Verifica se o administrador está logado
        if (empty($_SESSION['usuario_admin']['id'])) {
            $this->redirecionar('/login-admin');
        }

             // Conexão com o banco
            $pdo = require APP_ROOT . '/database/conexao.php';

        // Busca os indicadores
        $indicadores = [
            'produtos' => (int) $pdo->query(
                "SELECT COUNT(*) FROM produtos"
            )->fetchColumn(),

            'clientes' => (int) $pdo->query(
                "SELECT COUNT(*) FROM clientes"
            )->fetchColumn(),

            'pedidos' => (int) $pdo->query(
                "SELECT COUNT(*) FROM pedidos"
            )->fetchColumn(),

            'categorias' => (int) $pdo->query(
                "SELECT COUNT(*) FROM categorias"
            )->fetchColumn(),
        ];

        // Carrega a view
        $this->view(
            'admin/dashboard',
            [
                'tituloPagina' => 'Dashboard administrativo',

                'usuarioAdmin' => $_SESSION['usuario_admin'],

                'csrfToken' => Csrf::gerar(),

                'indicadores' => $indicadores,
            ]
        );
    }
}