<?php
declare(strict_types=1);
namespace App\Repositories;
use PDO;

final class ClienteSegurancaRepository
{
    public function __construct(private PDO $pdo) {}
    public function bloquear(string $tabela, int $id, string $campo = 'id'): ?array
    {
        $permitidos = ['clientes' => 'id', 'usuarios_admin' => 'id', 'cliente_recuperacao' => 'cliente_id'];
        if (($permitidos[$tabela] ?? null) !== $campo) throw new \InvalidArgumentException('Tabela inválida.');
        $sufixo = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql' ? ' FOR UPDATE' : '';
        $stmt = $this->pdo->prepare("SELECT * FROM $tabela WHERE $campo = ?" . $sufixo);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function auditar(?int $admin, int $cliente, string $acao, string $motivo): void
    {
        $this->pdo->prepare('INSERT INTO cliente_admin_auditoria (admin_id, cliente_id, acao, motivo, criado_em) VALUES (?, ?, ?, ?, ?)')->execute([$admin, $cliente, $acao, $motivo, time()]);
    }
    public function enviosRecentes(int $admin): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM cliente_admin_auditoria WHERE admin_id = ? AND acao = 'recuperacao_enviada' AND criado_em > ?");
        $stmt->execute([$admin, time() - 3600]);
        return (int) $stmt->fetchColumn();
    }
    public function criarRecuperacao(int $id): void
    {
        $this->pdo->prepare('INSERT INTO cliente_recuperacao (cliente_id) VALUES (?)')->execute([$id]);
    }
    public function atualizarToken(int $id, string $hash, string $credencial, int $agora, string $dia, int $quantidade): void
    {
        $this->pdo->prepare('UPDATE cliente_recuperacao SET token_hash = ?, credencial_hash = ?, expira_em = ?, ultimo_envio = ?, dia_envio = ?, envios_dia = ? WHERE cliente_id = ?')->execute([$hash, $credencial, $agora + 1800, $agora, $dia, $quantidade, $id]);
    }
    public function clientePorToken(string $hash): ?array
    {
        $stmt = $this->pdo->prepare('SELECT c.id, c.email, c.senha_hash, c.email_verificado, r.credencial_hash FROM cliente_recuperacao r JOIN clientes c ON c.id = r.cliente_id WHERE r.token_hash = ? AND r.expira_em > ?');
        $stmt->execute([$hash, time()]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function salvarSenha(int $id, string $hash): void
    {
        $this->pdo->prepare('UPDATE clientes SET senha_hash = ? WHERE id = ?')->execute([$hash, $id]);
        $this->pdo->prepare('UPDATE cliente_recuperacao SET token_hash = NULL, credencial_hash = NULL, expira_em = NULL, versao_sessao = versao_sessao + 1 WHERE cliente_id = ?')->execute([$id]);
    }
    public function quantidadePedidos(int $id): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE cliente_id = ?');
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }
    public function excluir(int $id): void
    {
        $this->pdo->prepare('DELETE FROM clientes WHERE id = ?')->execute([$id]);
    }
    public function fotoEmUso(string $foto): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM clientes WHERE foto_url = ?');
        $stmt->execute([$foto]);
        return (int) $stmt->fetchColumn() > 0;
    }
    public function versaoSessao(int $id): ?int
    {
        $stmt = $this->pdo->prepare('SELECT COALESCE(r.versao_sessao, 0) FROM clientes c LEFT JOIN cliente_recuperacao r ON r.cliente_id = c.id WHERE c.id = ?');
        $stmt->execute([$id]);
        $valor = $stmt->fetchColumn();
        return $valor === false ? null : (int) $valor;
    }
}
