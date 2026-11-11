<?php
declare(strict_types=1);
namespace App\Controllers\Cliente;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Services\ClienteAdminService;
use DomainException;
use Throwable;

final class RecuperacaoController extends Controller
{
    private function cabecalhos(): void
    {
        header('Cache-Control: no-store, private');
        header('Referrer-Policy: no-referrer');
        header('X-Robots-Tag: noindex, nofollow');
        header("Content-Security-Policy: default-src 'none'; style-src 'self'; img-src 'self'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'");
    }

    public function formulario(): void
    {
        $this->cabecalhos();
        if (isset($_GET['token'])) {
            $token = is_string($_GET['token']) ? $_GET['token'] : '';
            $_SESSION['recuperacao_hash'] = preg_match('/^[a-f0-9]{64}$/D', $token) ? hash('sha256', $token) : '';
            Csrf::renovarCliente();
            // Remove o segredo da barra de endereço antes de renderizar a página.
            $this->redirecionar('/redefinir-senha');
        }
        $valido = (new ClienteAdminService($this->pdo))->tokenValido((string) ($_SESSION['recuperacao_hash'] ?? ''));
        $sucesso = !empty($_SESSION['recuperacao_sucesso']);
        unset($_SESSION['recuperacao_sucesso']);
        $this->view('cliente/redefinir-senha', compact('valido', 'sucesso'));
    }

    public function salvar(): void
    {
        $this->cabecalhos();
        if (!Csrf::validarCliente(is_string($_POST['_token'] ?? null) ? $_POST['_token'] : null)) {
            http_response_code(403);
            $this->view('cliente/redefinir-senha', ['valido' => false, 'erro' => 'Sessão inválida. Abra novamente o link recebido.']);
            return;
        }
        $service = new ClienteAdminService($this->pdo);
        $hash = (string) ($_SESSION['recuperacao_hash'] ?? '');
        try {
            $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
            if ($senha !== ($_POST['confirmacao'] ?? null)) throw new DomainException('As senhas não coincidem.');
            $service->redefinir($hash, $senha);
            unset($_SESSION['recuperacao_hash']);
            \App\Helpers\SessaoCliente::validar($this->pdo);
            Csrf::renovarCliente();
            $_SESSION['recuperacao_sucesso'] = true;
            $this->redirecionar('/redefinir-senha');
        } catch (DomainException $erro) {
            $mensagem = $erro->getMessage();
        } catch (Throwable $erro) {
            error_log('Falha ao redefinir senha: ' . get_class($erro));
            $mensagem = 'Não foi possível redefinir a senha. Tente novamente mais tarde.';
        }
        $this->view('cliente/redefinir-senha', ['valido' => $service->tokenValido($hash), 'erro' => $mensagem]);
    }
}
