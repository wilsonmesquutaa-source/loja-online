<?php
declare(strict_types=1);
namespace App\Services;
use App\Repositories\ModuloRepository;
use App\Helpers\Decimal as D;
use DomainException;
use PDO;
final class CompraInsumoService
{
    private ModuloRepository $r;
    public function __construct(PDO $pdo) { $this->r=new ModuloRepository($pdo); }
    public function salvar(array $p,int $admin): int
    {
        return $this->r->transacao(function()use($p,$admin){
            $data=(string)($p['data_compra']??'');
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$data)||date('Y-m-d',strtotime($data))!==$data)throw new DomainException('Data da compra inválida.');
            $linhas=$p['itens']??[];
            if(!is_array($linhas)||count($linhas)<1||count($linhas)>100)throw new DomainException('Adicione de 1 a 100 itens.');
            $itens=[];$soma='0';$usados=[];
            foreach($linhas as $i){
                $ins=$this->r->um('SELECT i.*,u.codigo FROM insumos i JOIN unidades_medida u ON u.id=i.unidade_base_id WHERE i.id=? AND i.ativo=1',[(int)($i['insumo_id']??0)]);
                $un=$this->r->um('SELECT * FROM unidades_medida WHERE id=? AND ativo=1',[(int)($i['unidade_compra_id']??0)]);
                if(!$ins||!$un)throw new DomainException('Selecione um insumo e uma unidade válidos.');
                if(isset($usados[$ins['id']]))throw new DomainException('Agrupe cada insumo em uma única linha por compra.');
                $usados[$ins['id']]=true;
                $q=D::valor($i['quantidade_comprada']??'','0.0001','999999999',4);
                $f=CustoMedioInsumoService::fator($un['codigo'],$ins['codigo'],$i['fator_para_unidade_base']??'');
                $base=D::valor(bcmul($q,$f,4),'0.0001','999999999',4);
                $valor=D::valor($i['valor_total_item']??'','0.01','999999999',2);
                $validade=trim((string)($i['validade']??''));
                if($validade!=='' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$validade)||date('Y-m-d',strtotime($validade))!==$validade))throw new DomainException('Validade inválida.');
                $itens[]=['insumo_id'=>$ins['id'],'unidade_compra_id'=>$un['id'],'quantidade_comprada'=>$q,'fator_para_unidade_base'=>$f,'quantidade_base'=>$base,'valor_total_item'=>$valor,'lote'=>mb_substr(trim((string)($i['lote']??'')),0,100),'validade'=>$validade?:null];
                $soma=bcadd($soma,$valor,2);
            }
            $frete=D::valor($p['frete']??0,'0','999999999',2);$desconto=D::valor($p['desconto']??0,'0',$soma,2);$outros=D::valor($p['outros_custos']??0,'0','999999999',2);
            $total=bcsub(bcadd(bcadd($soma,$frete,2),$outros,2),$desconto,2);
            if(bccomp($total,'0',2)<=0)throw new DomainException('Total da compra deve ser positivo.');
            $id=$this->r->inserir('compras_insumos',['fornecedor_id'=>(int)($p['fornecedor_id']??0)?:null,'numero_documento'=>mb_substr(trim((string)($p['numero_documento']??'')),0,100),'data_compra'=>$data,'valor_produtos'=>$soma,'frete'=>$frete,'desconto'=>$desconto,'outros_custos'=>$outros,'total'=>$total,'observacao'=>mb_substr((string)($p['observacao']??''),0,5000),'status'=>'rascunho','criado_por_admin_id'=>$admin]);
            $distribuido='0';
            foreach($itens as $n=>$i){
                $alocado=$n===count($itens)-1?bcsub($total,$distribuido,2):bcdiv(bcmul($total,$i['valor_total_item'],10),$soma,2);
                $distribuido=bcadd($distribuido,$alocado,2);
                $i['custo_unitario_base']=bcdiv($alocado,$i['quantidade_base'],8);
                $this->r->inserir('compra_insumo_itens',['compra_id'=>$id]+$i);
            }
            $this->r->auditar('financeiro','compras_insumos',$id,'rascunho',null,$itens,$admin);
            return $id;
        });
    }
    public function confirmar(int $id,int $admin): void
    {
        $this->r->transacao(function()use($id,$admin){
            $c=$this->r->um('SELECT * FROM compras_insumos WHERE id=? FOR UPDATE',[$id]);
            if(!$c||$c['status']!=='rascunho')throw new DomainException('Somente rascunhos podem ser confirmados.');
            $itens=$this->r->todos('SELECT * FROM compra_insumo_itens WHERE compra_id=? ORDER BY insumo_id',[$id]);
            foreach($itens as $i){
                $ins=$this->r->um('SELECT * FROM insumos WHERE id=? FOR UPDATE',[$i['insumo_id']]);
                if(!$ins['ativo'])throw new DomainException('Há um insumo inativo nesta compra.');
                $saldo=bcadd($ins['estoque_atual'],$i['quantidade_base'],4);
                $novo=$ins['metodo_custeio']==='ultimo_preco'?$i['custo_unitario_base']:CustoMedioInsumoService::medio($ins['estoque_atual'],$ins['custo_medio_unitario'],$i['quantidade_base'],$i['custo_unitario_base']);
                $ultimo=$this->r->um("SELECT h.custo_unitario FROM insumo_precos_historico h JOIN compra_insumo_itens ci ON ci.id=h.compra_item_id JOIN compras_insumos c ON c.id=ci.compra_id WHERE h.insumo_id=? AND c.status='confirmada' ORDER BY h.id DESC LIMIT 1",[$ins['id']]);
                $anterior=$ultimo['custo_unitario']??null;
                $variacao=$anterior && bccomp($anterior,'0',8)>0?bcdiv(bcmul(bcsub($i['custo_unitario_base'],$anterior,8),'100',8),$anterior,4):null;
                $this->r->atualizar('compra_insumo_itens',(int)$i['id'],['custo_anterior'=>$ins['custo_medio_unitario']]);
                $this->r->atualizar('insumos',(int)$ins['id'],['estoque_atual'=>$saldo,'custo_medio_unitario'=>$novo]);
                $this->r->inserir('insumo_precos_historico',['insumo_id'=>$ins['id'],'compra_item_id'=>$i['id'],'custo_unitario'=>$i['custo_unitario_base'],'custo_anterior'=>$anterior,'variacao_percentual'=>$variacao,'data_referencia'=>$c['data_compra'].' 12:00:00']);
                $this->r->inserir('estoque_insumos_movimentacoes',['insumo_id'=>$ins['id'],'tipo'=>'entrada_compra','quantidade'=>$i['quantidade_base'],'saldo_anterior'=>$ins['estoque_atual'],'saldo_posterior'=>$saldo,'compra_item_id'=>$i['id'],'admin_id'=>$admin,'motivo'=>'Compra #'.$id]);
            }
            $this->r->atualizar('compras_insumos',$id,['status'=>'confirmada']);
            $this->r->auditar('financeiro','compras_insumos',$id,'confirmada',$c,['status'=>'confirmada'],$admin);
        });
    }
    public function cancelar(int $id,int $admin): void
    {
        $this->r->transacao(function()use($id,$admin){
            $c=$this->r->um('SELECT * FROM compras_insumos WHERE id=? FOR UPDATE',[$id]);
            if(!$c||$c['status']==='cancelada')throw new DomainException('Compra inexistente ou já cancelada.');
            if($c['status']==='confirmada'){
                foreach($this->r->todos('SELECT * FROM compra_insumo_itens WHERE compra_id=? ORDER BY insumo_id',[$id]) as $i){
                    $ins=$this->r->um('SELECT * FROM insumos WHERE id=? FOR UPDATE',[$i['insumo_id']]);
                    $ultima=$this->r->um('SELECT * FROM estoque_insumos_movimentacoes WHERE insumo_id=? ORDER BY id DESC LIMIT 1',[$ins['id']]);
                    if(!$ultima || (int)$ultima['compra_item_id']!==(int)$i['id'] || $ultima['tipo']!=='entrada_compra')throw new DomainException('Há movimentações posteriores. Registre uma devolução ou ajuste com motivo para preservar o histórico.');
                    $saldo=bcsub($ins['estoque_atual'],$i['quantidade_base'],4);
                    if(bccomp($saldo,'0',4)<0)throw new DomainException('Saldo insuficiente para estornar.');
                    $this->r->atualizar('insumos',(int)$ins['id'],['estoque_atual'=>$saldo,'custo_medio_unitario'=>$i['custo_anterior']]);
                    $this->r->inserir('estoque_insumos_movimentacoes',['insumo_id'=>$ins['id'],'tipo'=>'devolucao','quantidade'=>$i['quantidade_base'],'saldo_anterior'=>$ins['estoque_atual'],'saldo_posterior'=>$saldo,'compra_item_id'=>$i['id'],'admin_id'=>$admin,'motivo'=>'Cancelamento da compra #'.$id]);
                }
            }
            $this->r->atualizar('compras_insumos',$id,['status'=>'cancelada']);
            $this->r->auditar('financeiro','compras_insumos',$id,'cancelada',$c,['status'=>'cancelada'],$admin);
        });
    }
}
