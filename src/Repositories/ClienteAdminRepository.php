<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ClienteAdminRepository
{
    public function __construct(private PDO $pdo) {}

    public function listar(string $busca, string $verificacao, int $pagina): array
    {
        $condicoes = [];
        $parametros = [];
        if ($busca !== '') {
            // Busca literal: os caracteres de LIKE digitados não viram curingas.
            $termo = '%' . strtr($busca, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
            $condicoes[] = "(c.nome LIKE :nome ESCAPE '!' OR c.email LIKE :email ESCAPE '!')";
            $parametros['nome'] = $termo;
            $parametros['email'] = $termo;
        }
        if ($verificacao !== '') {
            $condicoes[] = 'c.email_verificado = :verificado';
            $parametros['verificado'] = (int) $verificacao;
        }
        $where = $condicoes ? ' WHERE ' . implode(' AND ', $condicoes) : '';
        $contagem = $this->pdo->prepare('SELECT COUNT(*) FROM clientes c' . $where);
        $contagem->execute($parametros);
        $total = (int) $contagem->fetchColumn();
        $paginas = max(1, (int) ceil($total / 20));
        $pagina = min(max(1, $pagina), $paginas);
        $offset = ($pagina - 1) * 20;
        $consulta = $this->pdo->prepare(
            'SELECT c.id, c.nome, c.email, c.email_verificado, c.criado_em,
                (SELECT COUNT(*) FROM pedidos p WHERE p.cliente_id = c.id) AS pedidos
             FROM clientes c' . $where . ' ORDER BY c.criado_em DESC, c.id DESC LIMIT 20 OFFSET ' . $offset
        );
        $consulta->execute($parametros);
        $clientes = $consulta->fetchAll(PDO::FETCH_ASSOC);
        // O e-mail completo não é enviado à listagem, nem em atributos ocultos.
        foreach ($clientes as &$cliente) {
            $partes = explode('@', (string) $cliente['email'], 2);
            $cliente['email_resumido'] = mb_substr($partes[0], 0, 1) . '***@' . ($partes[1] ?? '***');
            unset($cliente['email']);
        }
        unset($cliente);
        return compact('clientes', 'total', 'pagina', 'paginas');
    }

    public function buscar(int $id): ?array
    {
        $consulta = $this->pdo->prepare(
            "SELECT id, nome, email, email_verificado, criado_em,
             CASE WHEN senha_hash IS NOT NULL AND senha_hash <> '' THEN 1 ELSE 0 END AS possui_senha
             FROM clientes WHERE id = :id LIMIT 1"
        );
        $consulta->execute(['id' => $id]);
        return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function pedidos(int $clienteId, int $pagina): array
    {
        $consulta = $this->pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE cliente_id = :id');
        $consulta->execute(['id' => $clienteId]);
        $total = (int) $consulta->fetchColumn();
        $paginas = max(1, (int) ceil($total / 20));
        $pagina = min(max(1, $pagina), $paginas);
        $offset = ($pagina - 1) * 20;
        $consulta = $this->pdo->prepare(
            'SELECT id, codigo, status, total, criado_em FROM pedidos WHERE cliente_id = :id
             ORDER BY criado_em DESC, id DESC LIMIT 20 OFFSET ' . $offset
        );
        $consulta->execute(['id' => $clienteId]);
        $pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return compact('pedidos', 'total', 'pagina', 'paginas');
    }
}
