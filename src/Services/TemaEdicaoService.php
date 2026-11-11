<?php
declare(strict_types=1);
namespace App\Services;
use App\Repositories\ModuloRepository;
use DomainException;
use PDO;

final class TemaEdicaoService
{
    public function __construct(private PDO $pdo) {}

    public function alterarEstado(int $id, string $acao, int $admin): void
    {
        if (!in_array($acao, ['reabrir', 'excluir'], true)) throw new DomainException('Ação inválida.');
        $r = new ModuloRepository($this->pdo);
        $r->transacao(function () use ($r, $id, $acao, $admin) {
            $tema = $r->um('SELECT * FROM temas_loja WHERE id=? FOR UPDATE', [$id]);
            if (!$tema) throw new DomainException('Tema não encontrado.');
            if ($tema['tipo'] === 'padrao') throw new DomainException('O tema padrão é permanente.');
            $novo = ['status' => $acao === 'excluir' ? 'arquivado' : 'rascunho', 'ativo_manual' => 0];
            if ($tema['status'] === $novo['status'] && !$tema['ativo_manual']) return;
            $r->atualizar('temas_loja', $id, $novo);
            if ($acao === 'excluir') {
                $agendas = $r->todos('SELECT * FROM tema_agendamentos WHERE tema_id=? AND ativo=1', [$id]);
                $r->executar('UPDATE tema_agendamentos SET ativo=0 WHERE tema_id=?', [$id]);
                foreach ($agendas as $agenda) $r->auditar('temas', 'tema_agendamentos', (int)$agenda['id'], 'cancelado_por_exclusao', $agenda, ['ativo'=>0], $admin);
            }
            $r->inserir('tema_publicacoes', ['tema_id'=>$id, 'acao'=>$acao === 'excluir' ? 'desativado' : 'restaurado', 'admin_id'=>$admin, 'origem'=>'manual', 'detalhes'=>$acao === 'excluir' ? 'Tema removido do painel e enviado aos excluídos. Agendamentos cancelados.' : 'Mesma versão reaberta como rascunho para edição.']);
            $r->auditar('temas', 'temas_loja', $id, $acao, $tema, $novo, $admin);
        });
        TemaResolverService::invalidar();
    }
}
