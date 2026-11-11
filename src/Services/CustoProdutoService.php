<?php
declare(strict_types=1);
namespace App\Services;
use App\Repositories\ModuloRepository;
use App\Helpers\Decimal as D;
use DomainException;
use PDO;
final class CustoProdutoService
{
    private ModuloRepository $r;
    public function __construct(private PDO $pdo){$this->r=new ModuloRepository($pdo);}
    public function salvarFicha(array $p,int $admin): int
    {
        return $this->r->transacao(function()use($p,$admin){
            $produto=$this->r->um('SELECT id FROM produtos WHERE id=? FOR UPDATE',[(int)($p['produto_id']??0)]);
            if(!$produto)throw new DomainException('Produto inválido.');
            $itens=$p['itens']??[];
            if(!is_array($itens)||!count($itens)||count($itens)>100)throw new DomainException('A ficha precisa de ingredientes.');
            $anterior=$this->r->um('SELECT * FROM fichas_tecnicas WHERE produto_id=? ORDER BY versao DESC LIMIT 1',[$produto['id']]);
            $rendimento=D::valor($p['rendimento_quantidade']??'','0.0001','99999999',4);
            $perda=D::valor($p['percentual_perda']??0,'0','99',4);
            $venda=D::valor($p['unidades_por_venda']??1,'0.0001','999999',4);
            $this->r->executar('UPDATE fichas_tecnicas SET ativo=0 WHERE produto_id=?',[$produto['id']]);
            $nome=mb_substr(trim((string)($p['nome']??'')),0,150);
            if($nome==='')throw new DomainException('Informe o nome da ficha.');
            $id=$this->r->inserir('fichas_tecnicas',['produto_id'=>$produto['id'],'nome'=>$nome,'rendimento_quantidade'=>$rendimento,'rendimento_unidade'=>'unidade','unidades_por_venda'=>$venda,'percentual_perda'=>$perda,'tempo_preparo_minutos'=>D::valor($p['tempo_preparo_minutos']??0,'0','999999',0),'versao'=>($anterior['versao']??0)+1,'observacao'=>mb_substr((string)($p['observacao']??''),0,5000)]);
            $this->r->atualizar('fichas_tecnicas',$id,['custo_indireto_manual'=>D::valor($p['custo_indireto_manual']??0,'0','99999999',4)]);
            foreach($itens as $n=>$i){
                if(!$this->r->um('SELECT id FROM insumos WHERE id=? AND ativo=1',[(int)($i['insumo_id']??0)]))throw new DomainException('Insumo inválido.');
                $this->r->inserir('ficha_tecnica_itens',['ficha_tecnica_id'=>$id,'insumo_id'=>(int)$i['insumo_id'],'quantidade_base'=>D::valor($i['quantidade_base']??'','0.0001','999999999',4),'percentual_perda_extra'=>D::valor($i['percentual_perda_extra']??0,'0','99',4),'tipo'=>($i['tipo']??'')==='embalagem'?'embalagem':'ingrediente','ordem'=>$n+1]);
            }
            $this->r->auditar('financeiro','fichas_tecnicas',$id,'nova_versao',$anterior,$p,$admin);
            return $id;
        });
    }
    public function calcular(int $produto): ?array
    {
        $f=$this->r->um('SELECT * FROM fichas_tecnicas WHERE produto_id=? AND ativo=1',[$produto]);
        if(!$f)return null;
        $itens=$this->r->todos('SELECT t.*,i.nome,i.custo_medio_unitario,u.codigo FROM ficha_tecnica_itens t JOIN insumos i ON i.id=t.insumo_id JOIN unidades_medida u ON u.id=i.unidade_base_id WHERE t.ficha_tecnica_id=? ORDER BY t.ordem',[$f['id']]);
        $ing='0';$emb='0';$perdas='0';$pendentes=[];
        foreach($itens as &$i){
            $base=bcmul($i['quantidade_base'],$i['custo_medio_unitario'],8);
            $extra=bcmul($base,bcdiv($i['percentual_perda_extra'],'100',8),8);
            $perdas=bcadd($perdas,$extra,8);$i['custo']=bcadd($base,$extra,8);
            if($i['tipo']==='embalagem')$emb=bcadd($emb,$base,8);else $ing=bcadd($ing,$base,8);
            if(bccomp($i['custo_medio_unitario'],'0',8)<=0)$pendentes[]=$i['nome'];
        } unset($i);
        $direto=bcadd(bcadd($ing,$emb,8),$perdas,8);
        // Perda percentual é redução do rendimento; não se soma novamente aos ingredientes.
        $real=bcmul($f['rendimento_quantidade'],bcsub('1',bcdiv($f['percentual_perda'],'100',8),8),8);
        if(bccomp($real,'0',8)<=0)throw new DomainException('Rendimento real deve ser positivo.');
        $ind=$f['custo_indireto_manual'];$rateios=[];
        foreach($this->r->todos('SELECT * FROM custos_indiretos WHERE ativo=1 AND inicio_vigencia<=CURDATE() AND (fim_vigencia IS NULL OR fim_vigencia>=CURDATE())') as $c){
            $valor='0';
            if($c['forma_rateio']==='percentual_custo_direto')$valor=bcmul($direto,bcdiv($c['percentual_rateio']??'0','100',8),8);
            elseif($c['forma_rateio']!=='manual' && bccomp($c['base_mensal'],'0',4)>0){
                $peso=match($c['forma_rateio']){'por_unidade'=>$real,'por_tempo_producao'=>(string)($f['tempo_preparo_minutos']??0),default=>'1'};
                if($c['forma_rateio']==='por_tempo_producao' && bccomp($peso,'0',4)<=0)$pendentes[]='Tempo de preparo para '.$c['nome'];
                $valor=bcmul(bcdiv($c['valor_mensal'],$c['base_mensal'],8),$peso,8);
            }
            $ind=bcadd($ind,$valor,8);$rateios[]=['nome'=>$c['nome'],'categoria'=>$c['categoria'],'valor'=>$valor];
        }
        $total=bcadd($direto,$ind,8);$unit=bcdiv($total,$real,8);
        return ['ficha'=>$f,'itens'=>$itens,'rateios'=>$rateios,'pendentes'=>$pendentes,'rendimento_real'=>$real,'custo_ingredientes'=>$ing,'custo_embalagem'=>$emb,'custo_perdas'=>$perdas,'custo_indireto_rateado'=>$ind,'custo_total'=>$total,'custo_unitario'=>$unit,'custo_por_venda'=>bcmul($unit,$f['unidades_por_venda'],8)];
    }
    public function snapshot(int $produto,string $origem='recalculo'): ?array
    {
        $c=$this->calcular($produto);
        if(!$c||$c['pendentes'])return null;
        $v=['produto_id'=>$produto,'ficha_tecnica_id'=>$c['ficha']['id'],'data_calculo'=>date('Y-m-d H:i:s'),'origem'=>$origem];
        foreach(['custo_ingredientes','custo_embalagem','custo_perdas','custo_indireto_rateado','custo_total','custo_unitario','custo_por_venda'] as $k)$v[$k]=$c[$k];
        $v['id']=$this->r->inserir('produto_custos_snapshot',$v);
        return $v;
    }
    public function congelarItem(int $item,int $produto,int $quantidade): void
    {
        $this->r->executar("INSERT IGNORE INTO pedido_taxas_snapshot(pedido_id,taxa_pix,taxa_cartao) SELECT pedido_id,COALESCE((SELECT valor FROM configuracoes_financeiras WHERE chave='percentual_taxa_pix'),0),COALESCE((SELECT valor FROM configuracoes_financeiras WHERE chave='percentual_taxa_cartao'),0) FROM pedido_itens WHERE id=?",[$item]);
        if($this->r->um('SELECT id FROM pedido_item_custos WHERE pedido_item_id=?',[$item]))return;
        $c=$this->snapshot($produto,'automatico');if(!$c)return;
        $f=$this->r->um('SELECT * FROM fichas_tecnicas WHERE id=?',[$c['ficha_tecnica_id']]);
        $real=bcmul($f['rendimento_quantidade'],bcsub('1',bcdiv($f['percentual_perda'],'100',8),8),8);
        $ind=bcmul(bcmul(bcdiv($c['custo_indireto_rateado'],$real,8),$f['unidades_por_venda'],8),(string)$quantidade,8);
        $this->r->inserir('pedido_item_custos',['pedido_item_id'=>$item,'produto_id'=>$produto,'quantidade'=>$quantidade,'custo_unitario_no_momento'=>$c['custo_por_venda'],'custo_total_no_momento'=>bcmul($c['custo_por_venda'],(string)$quantidade,8),'custo_indireto_no_momento'=>$ind,'snapshot_produto_custo_id'=>$c['id']]);
    }
}
