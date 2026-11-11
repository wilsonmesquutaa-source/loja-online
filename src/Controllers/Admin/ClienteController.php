<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Repositories\ClienteAdminRepository;
use App\Services\ClienteAdminService;
use App\Helpers\Csrf;
use DomainException;
use Throwable;

final class ClienteController extends Controller
{
    private function validarAcao(): void
    {
        $this->proteger();
        if (!Csrf::validar(is_string($_POST['_token'] ?? null) ? $_POST['_token'] : null)) {
            http_response_code(403);
            exit('Sessão de segurança inválida. Recarregue a página.');
        }
    }

    public function recuperarSenha(int $id): void
    {
        $this->validarAcao();
        try {
            if (($_POST['solicitacao_confirmada'] ?? '') !== '1') throw new DomainException('Confirme que o cliente solicitou a recuperação.');
            (new ClienteAdminService($this->pdo))->enviarRecuperacao($id, (int) $_SESSION['usuario_admin']['id']);
            $_SESSION['clientes_aviso'] = ['tipo' => 'sucesso', 'texto' => 'Link enviado ao e-mail cadastrado. Validade: 30 minutos.'];
        } catch (DomainException $erro) {
            $_SESSION['clientes_aviso'] = ['tipo' => 'erro', 'texto' => $erro->getMessage()];
        } catch (Throwable $erro) {
            error_log('Falha na recuperação administrativa: ' . get_class($erro));
            $_SESSION['clientes_aviso'] = ['tipo' => 'erro', 'texto' => 'Não foi possível concluir o envio. Confira a configuração de e-mail ou tente novamente mais tarde.'];
        }
        $this->redirecionar('/admin/clientes/' . $id);
    }

    public function excluir(int $id): void
    {
        $this->validarAcao();
        try {
            if (($_POST['confirmacao'] ?? '') !== (string) $id || ($_POST['ciente'] ?? '') !== '1') {
                throw new DomainException('Confirme a exclusão e informe o número correto do cadastro.');
            }
            $motivo = is_string($_POST['motivo'] ?? null) ? $_POST['motivo'] : '';
            (new ClienteAdminService($this->pdo))->excluir($id, (int) $_SESSION['usuario_admin']['id'], $motivo);
            $_SESSION['clientes_aviso'] = ['tipo' => 'sucesso', 'texto' => 'Cadastro excluído. A ação foi registrada.'];
        } catch (DomainException $erro) {
            $_SESSION['clientes_aviso'] = ['tipo' => 'erro', 'texto' => $erro->getMessage()];
            $this->redirecionar('/admin/clientes/' . $id);
        } catch (Throwable $erro) {
            error_log('Falha na exclusão administrativa: ' . get_class($erro));
            $_SESSION['clientes_aviso'] = ['tipo' => 'erro', 'texto' => 'Não foi possível concluir a exclusão. Atualize a página e confira os vínculos do cadastro.'];
            $this->redirecionar('/admin/clientes/' . $id);
        }
        $this->redirecionar('/admin/clientes');
    }
    private function proteger(): void
    {
        if (empty($_SESSION['usuario_admin']['id'])) {
            $this->redirecionar('/login-admin');
        }
        header('Cache-Control: no-store, private');
        header('Referrer-Policy: no-referrer');
        header('X-Robots-Tag: noindex, nofollow');
    }

    private function parametro(string $nome): string
    {
        return isset($_GET[$nome]) && is_string($_GET[$nome]) ? trim($_GET[$nome]) : '';
    }

    public function index(): void
    {
        $this->proteger();
        $busca = mb_substr($this->parametro('busca'), 0, 150);
        $verificacao = $this->parametro('verificacao');
        if (!in_array($verificacao, ['0', '1'], true)) {
            $verificacao = '';
        }
        $repository = new ClienteAdminRepository($this->pdo);
        $resultado = $repository->listar($busca, $verificacao, (int) $this->parametro('pagina'));
        $this->view('admin/clientes', $resultado + compact('busca', 'verificacao'));
    }

    public function detalhes(int $id): void
    {
        $this->proteger();
        $repository = new ClienteAdminRepository($this->pdo);
        $cliente = $repository->buscar($id);
        if ($cliente === null) {
            http_response_code(404);
            $this->view('admin/cliente-detalhes', ['cliente' => null]);
            return;
        }
        $resultado = $repository->pedidos($id, (int) $this->parametro('pagina'));
        $this->view('admin/cliente-detalhes', $resultado + compact('cliente'));
    }
}
