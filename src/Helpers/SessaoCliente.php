<?php
declare(strict_types=1);
namespace App\Helpers;
use PDO;

final class SessaoCliente
{
    public static function versao(PDO $pdo, int $id): ?int
    {
        return (new \App\Repositories\ClienteSegurancaRepository($pdo))->versaoSessao($id);
    }

    public static function validar(PDO $pdo): void
    {
        if (empty($_SESSION['cliente_id'])) return;
        $versao = self::versao($pdo, (int) $_SESSION['cliente_id']);
        if ($versao === null || $versao !== (int) ($_SESSION['cliente_versao_sessao'] ?? 0)) {
            foreach (array_keys($_SESSION) as $chave) {
                if (str_starts_with($chave, 'cliente_') || $chave === '_csrf_cliente') unset($_SESSION[$chave]);
            }
            session_regenerate_id(true);
        }
    }
}
