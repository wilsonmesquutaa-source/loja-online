<?php
declare(strict_types=1);
namespace App\Services;
use App\Repositories\ModuloRepository;
use DateTimeImmutable;
use DateTimeZone;
use PDO;
final class TemaResolverService
{
    private ModuloRepository $r;
    private static array $cache=[];
    public function __construct(PDO $pdo){$this->r=new ModuloRepository($pdo);}
    public static function invalidar(): void {self::$cache=[];}
    public function resolver(?int $preview=null,?DateTimeImmutable $agora=null): array
    {
        $agora??=new DateTimeImmutable('now',new DateTimeZone('America/Fortaleza'));
        $key=($preview??0).':'.$agora->format('Y-m-d H:i:s');if(isset(self::$cache[$key]))return self::$cache[$key];
        $padrao=$this->r->um("SELECT * FROM temas_loja WHERE tipo='padrao' LIMIT 1")??['id'=>0,'nome'=>'Padrão','slug'=>'padrao','tipo'=>'padrao'];
        $ativo=$preview?$this->r->um("SELECT * FROM temas_loja WHERE id=? AND status<>'arquivado'",[$preview]):$this->r->um("SELECT * FROM temas_loja WHERE status='publicado' AND ativo_manual=1 ORDER BY prioridade DESC,id ASC LIMIT 1");
        if(!$ativo&&!$preview){
            $candidatos=$this->r->todos("SELECT a.*,t.prioridade prioridade_tema FROM tema_agendamentos a JOIN temas_loja t ON t.id=a.tema_id WHERE a.ativo=1 AND t.status='publicado' AND t.permitir_agendamento=1 ORDER BY a.prioridade DESC,t.prioridade DESC,t.id ASC,a.id ASC");
            foreach($candidatos as $a)if(TemaCalendarioService::vigente($a,$agora)){$ativo=$this->r->um('SELECT * FROM temas_loja WHERE id=?',[$a['tema_id']]);break;}
        }
        $ativo??=$padrao;$paleta=[];$assets=[];$componentes=[];$config=[];
        if ($preview === null) {
            $this->r->transacao(function() use ($ativo) {
                $estado=$this->r->um('SELECT tema_id FROM tema_estado_resolvido WHERE id=1 FOR UPDATE');
                if ((int)($estado['tema_id']??0)!==(int)$ativo['id']) {
                    $this->r->executar('UPDATE tema_estado_resolvido SET tema_id=? WHERE id=1',[$ativo['id']]);
                    $this->r->inserir('tema_publicacoes',['tema_id'=>$ativo['id'],'acao'=>'ativado','origem'=>$ativo['tipo']==='padrao'||($ativo['ativo_manual']??0)?'sistema':'agendamento','detalhes'=>'Tema resolvido na primeira visita após a mudança de vigência.']);
                }
            });
        }
        foreach(array_unique([(int)$padrao['id'],(int)$ativo['id']]) as $id){
            foreach(($this->r->um('SELECT * FROM tema_paletas WHERE tema_id=?',[$id])??[]) as $k=>$v)if(str_starts_with($k,'cor_')&&preg_match('/^#[0-9a-f]{6}$/i',(string)$v))$paleta[$k]=$v;
            foreach($this->r->todos('SELECT * FROM tema_assets WHERE tema_id=? AND ativo=1 ORDER BY ordem,id',[$id]) as $a)if(TemaAssetService::existente($a['url_arquivo']))$assets[$a['tipo']]=$a;
            foreach($this->r->todos('SELECT * FROM tema_componentes WHERE tema_id=? ORDER BY ordem,id',[$id]) as $c)$componentes[$c['componente']]=$c;
            foreach($this->r->todos('SELECT chave,valor FROM tema_configuracoes WHERE tema_id=?',[$id]) as $c)$config[$c['chave']]=$c['valor'];
        }
        // Enfeites de página pertencem ao tema escolhido, sem herdar os de outra temporada.
        foreach ($assets as $tipo => $asset) {
            if (str_starts_with($tipo, 'home_') && (int)$asset['tema_id'] !== (int)$ativo['id']) {
                unset($assets[$tipo]);
            }
        }
        return self::$cache[$key]=['tema'=>$ativo,'paleta'=>$paleta,'assets'=>$assets,'componentes'=>$componentes,'config'=>$config,'preview'=>$preview!==null];
    }
}
