<?php
declare(strict_types=1);
namespace App\Services;
use App\Repositories\ModuloRepository;
use DomainException;
use PDO;
final class TemaPublicacaoService
{
    public function __construct(private PDO $pdo) {}
    public function publicarAtivar(int $id,int $admin): void
    {
        $r=new ModuloRepository($this->pdo);
        $r->transacao(function()use($r,$id,$admin){
            $tema=$r->um('SELECT * FROM temas_loja WHERE id=? FOR UPDATE',[$id]);
            if(!$tema||$tema['status']==='arquivado'||$tema['tipo']==='padrao')throw new DomainException('Abra um tema válido antes de aplicar.');
            foreach($r->todos('SELECT * FROM tema_assets WHERE tema_id=? AND ativo=1',[$id]) as $a){
                if(!TemaAssetService::existente($a['url_arquivo']))throw new DomainException('Uma imagem do tema não está disponível. Remova ou substitua o arquivo antes de aplicar.');
            }
            $r->um("SELECT id FROM temas_loja WHERE tipo='padrao' FOR UPDATE");
            $r->executar('UPDATE temas_loja SET ativo_manual=0 WHERE ativo_manual=1');
            $r->atualizar('temas_loja',$id,['status'=>'publicado','ativo_manual'=>1]);
            foreach(['publicado','ativado'] as $acao)$r->inserir('tema_publicacoes',['tema_id'=>$id,'acao'=>$acao,'admin_id'=>$admin,'origem'=>'manual','detalhes'=>'Tema publicado e aplicado à loja.']);
            $r->auditar('temas','temas_loja',$id,'publicado_e_ativado',$tema,['status'=>'publicado','ativo_manual'=>1],$admin);
        });
        TemaResolverService::invalidar();
    }
}
