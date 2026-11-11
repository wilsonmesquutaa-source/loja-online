<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use DomainException;
use Throwable;
use App\Repositories\ClienteSegurancaRepository;

final class ClienteAdminService
{
    public const MOTIVOS = ['solicitacao_titular' => 'Solicitação do titular verificada', 'cadastro_teste' => 'Cadastro de teste', 'cadastro_duplicado' => 'Cadastro duplicado confirmado'];

    private ClienteSegurancaRepository $repository;
    public function __construct(private PDO $pdo)
    {
        $this->repository = new ClienteSegurancaRepository($pdo);
    }

    private function bloquear(string $tabela, int $id, string $campo = 'id'): ?array
    {
        return $this->repository->bloquear($tabela, $id, $campo);
    }

    private function auditar(?int $admin, int $cliente, string $acao, string $motivo): void
    {
        $this->repository->auditar($admin, $cliente, $acao, $motivo);
    }

    private function fingerprint(array $cliente): string
    {
        return hash('sha256', $cliente['email'] . "\0" . ($cliente['senha_hash'] ?? ''));
    }

    public function enviarRecuperacao(int $id, int $admin, ?callable $enviar = null): void
    {
        $urlBase = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/');
        $url = parse_url($urlBase);
        if (
            !$url || !isset($url['host'], $url['scheme']) || isset($url['user']) || isset($url['query']) || isset($url['fragment'])
            || ($url['scheme'] !== 'https' && !($url['scheme'] === 'http' && in_array($url['host'], ['localhost', '127.0.0.1'], true)))
        ) {
            throw new DomainException('Configure APP_URL com o endereço HTTPS da loja (HTTP apenas para localhost).');
        }
        $this->pdo->beginTransaction();
        try {
            if (!$this->bloquear('usuarios_admin', $admin)) throw new DomainException('Administrador não encontrado.');
            $cliente = $this->bloquear('clientes', $id);
            if (!$cliente) throw new DomainException('Cliente não encontrado.');
            if (empty($cliente['senha_hash'])) throw new DomainException('Esta conta usa o Google e não possui senha local. A recuperação deve ser feita no Google.');
            if (empty($cliente['email_verificado'])) throw new DomainException('O cliente precisa confirmar o e-mail cadastrado antes de recuperar a senha.');
            if ($this->repository->enviosRecentes($admin) >= 10) throw new DomainException('Limite de 10 envios por hora para este administrador. Aguarde.');
            $recuperacao = $this->bloquear('cliente_recuperacao', $id, 'cliente_id');
            if (!$recuperacao) {
                $this->repository->criarRecuperacao($id);
                $recuperacao = ['ultimo_envio' => 0, 'dia_envio' => null, 'envios_dia' => 0];
            }
            $agora = time();
            $dia = gmdate('Y-m-d');
            $quantidade = $recuperacao['dia_envio'] === $dia ? (int) $recuperacao['envios_dia'] : 0;
            if ($agora - (int) $recuperacao['ultimo_envio'] < 300 || $quantidade >= 3) {
                throw new DomainException('Aguarde 5 minutos entre envios. O limite é de 3 envios por cliente por dia.');
            }
            $token = bin2hex(random_bytes(32));
            $this->repository->atualizarToken($id, hash('sha256', $token), $this->fingerprint($cliente), $agora, $dia, $quantidade + 1);
            $link = $urlBase . '/redefinir-senha?token=' . $token;
            // O remetente é substituível apenas pelo código de teste; nunca por dados da requisição.
            ($enviar ?? static function ($email, $nome, $link): void {
                (new EmailService())->enviarRecuperacaoSenha($email, $nome, $link);
            })($cliente['email'], $cliente['nome'], $link);
            $this->auditar($admin, $id, 'recuperacao_enviada', 'solicitacao_titular');
            $this->pdo->commit();
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $erro;
        }
    }

    public function tokenValido(string $hash): bool
    {
        if (!preg_match('/^[a-f0-9]{64}$/D', $hash)) return false;
        $cliente = $this->repository->clientePorToken($hash);
        return $cliente && !empty($cliente['email_verificado']) && !empty($cliente['senha_hash'])
            && hash_equals((string) $cliente['credencial_hash'], $this->fingerprint($cliente));
    }

    public function redefinir(string $hash, string $senha): void
    {
        if (strlen($senha) < 12 || strlen($senha) > 72) throw new DomainException('Use uma senha de 12 a 72 bytes (letras acentuadas podem ocupar mais de um byte).');
        $novaSenha = password_hash($senha, PASSWORD_DEFAULT);
        $this->pdo->beginTransaction();
        try {
            $id = (int) ($this->repository->clientePorToken($hash)['id'] ?? 0);
            $cliente = $this->bloquear('clientes', $id);
            $recuperacao = $this->bloquear('cliente_recuperacao', $id, 'cliente_id');
            if (
                !$cliente || !$recuperacao || !hash_equals((string) $recuperacao['token_hash'], $hash)
                || (int) $recuperacao['expira_em'] <= time() || empty($cliente['email_verificado']) || empty($cliente['senha_hash'])
                || !hash_equals((string) $recuperacao['credencial_hash'], $this->fingerprint($cliente))
            ) {
                throw new DomainException('Link inválido, expirado ou já utilizado. Solicite um novo link ao atendimento.');
            }
            $this->repository->salvarSenha($id, $novaSenha);
            $this->auditar(null, $id, 'senha_redefinida', 'link_verificado');
            $this->pdo->commit();
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $erro;
        }
    }

    public function excluir(int $id, int $admin, string $motivo): void
    {
        if (!isset(self::MOTIVOS[$motivo])) throw new DomainException('Selecione o motivo da exclusão.');
        $this->pdo->beginTransaction();
        try {
            if (!$this->bloquear('usuarios_admin', $admin)) throw new DomainException('Administrador não encontrado.');
            $cliente = $this->bloquear('clientes', $id);
            if (!$cliente) throw new DomainException('Cliente não encontrado.');
            if ($this->repository->quantidadePedidos($id) > 0) throw new DomainException('Este cliente possui pedidos e não pode ser excluído por esta opção.');
            $this->auditar($admin, $id, 'cliente_excluido', $motivo);
            $this->repository->excluir($id);
            $this->pdo->commit();
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $erro;
        }
        // Remove somente a foto local exclusiva do cliente, nunca URLs ou caminhos externos.
        try {
            $foto = (string) ($cliente['foto_url'] ?? '');
            if (preg_match('~^/assets/uploads/clientes/[a-zA-Z0-9_.-]+\.(?:webp|png|jpe?g)$~D', $foto)) {
                $arquivo = realpath(APP_ROOT . '/public' . $foto);
                $pasta = realpath(APP_ROOT . '/public/assets/uploads/clientes');
                if (!$this->repository->fotoEmUso($foto) && $arquivo && $pasta && dirname($arquivo) === $pasta && !@unlink($arquivo)) {
                    error_log('Não foi possível remover foto de cadastro excluído.');
                }
            }
        } catch (Throwable $erro) {
            error_log('Limpeza de foto pendente após exclusão de cliente.');
        }
    }
}
