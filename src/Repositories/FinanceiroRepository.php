<?php
declare(strict_types=1);
namespace App\Repositories;
use App\Services\CustoProdutoService;
use DateTimeImmutable;
use DomainException;
final class FinanceiroRepository extends ModuloRepository
{
    public function configuracoes(): array {return array_column($this->todos('SELECT chave,valor FROM configuracoes_financeiras'),'valor','chave');}

    public function graficos(array $resumo): array
    {
        $pedidos=$resumo['pedidos'];$ids=array_column($pedidos,'id');$composicao=[];$categorias=[];$mensal=[];$medio=[];
        foreach($resumo['serie'] as $dia=>$v){$mes=substr($dia,0,7);$mensal[$mes]=($mensal[$mes]??0)+$v['lucro'];}
        foreach($pedidos as $p){$dia=substr($p['aprovado_em']??$p['criado_em'],0,10);$medio[$dia]??=['soma'=>0,'n'=>0];$medio[$dia]['soma']+=(float)$p['cpv'];$medio[$dia]['n']++;}
        if($ids){
            $marcas=implode(',',array_fill(0,count($ids),'?'));
            $linhas=$this->todos("SELECT i.*,p.subtotal pedido_subtotal,p.desconto,c.nome categoria,ic.custo_total_no_momento,s.* FROM pedido_itens i JOIN pedidos p ON p.id=i.pedido_id JOIN produtos pr ON pr.id=i.produto_id JOIN categorias c ON c.id=pr.categoria_id LEFT JOIN pedido_item_custos ic ON ic.pedido_item_id=i.id LEFT JOIN produto_custos_snapshot s ON s.id=ic.snapshot_produto_custo_id WHERE i.pedido_id IN ($marcas)",$ids);
            foreach($linhas as $i){
                $receita=(float)$i['subtotal'];if((float)$i['pedido_subtotal']>0)$receita-=(float)$i['desconto']*$receita/(float)$i['pedido_subtotal'];
                $categorias[$i['categoria']]=($categorias[$i['categoria']]??0)+$receita;
                if((float)($i['custo_total']??0)>0)foreach(['custo_ingredientes'=>'Ingredientes','custo_embalagem'=>'Embalagens','custo_perdas'=>'Perdas extras','custo_indireto_rateado'=>'Custos indiretos'] as $k=>$l)$composicao[$l]=($composicao[$l]??0)+(float)$i['custo_total_no_momento']*(float)$i[$k]/(float)$i['custo_total'];
            }
        }
        if(!$resumo['sem_taxa'])$composicao['Taxas']=(float)$resumo['cards']['Taxas de pagamento'];
        $ranking=[];$impacto=[];
        foreach($this->rentabilidade() as $p){
            if($p['margem']!==null)$ranking[$p['nome']]=$p['margem'];
            foreach($p['calculo']['itens']??[] as $i)$impacto[$i['nome']]=($impacto[$i['nome']]??0)+(float)$i['custo'];
        }
        arsort($ranking);arsort($impacto);$ranking=array_slice($ranking,0,15,true);$impacto=array_slice($impacto,0,10,true);
        $insumo=(int)($_GET['insumo_grafico']??0);
        if(!$insumo)$insumo=(int)($this->um('SELECT insumo_id FROM insumo_precos_historico ORDER BY id DESC LIMIT 1')['insumo_id']??0);
        $precos=$this->todos('SELECT h.data_referencia,h.custo_unitario,i.nome FROM insumo_precos_historico h JOIN insumos i ON i.id=h.insumo_id WHERE h.insumo_id=? ORDER BY h.data_referencia,h.id LIMIT 200',[$insumo]);
        $hist=[];foreach($precos as $n=>$p)$hist[$p['data_referencia'].' #'.($n+1)]=(float)$p['custo_unitario'];
        $dados=[
            'Composição do custo vendido'=>$resumo['sem_custo']?[]:$composicao,
            'Produtos por margem bruta (%)'=>$ranking,
            'Evolução do preço por unidade-base'.($precos?' · '.$precos[0]['nome']:'')=>$hist,
            'Insumos nas fichas ativas (R$ por conjunto de receitas)'=>$impacto,
            'Receita líquida por categoria'=>$categorias,
            'Lucro bruto mensal'=>$resumo['sem_custo']?[]:$mensal,
            'Custo médio por pedido'=>$resumo['sem_custo']?[]:array_map(fn($v)=>$v['n']?$v['soma']/$v['n']:0,$medio)
        ];
        $result=[];foreach($dados as $titulo=>$valores)$result[]=['titulo'=>$titulo,'dados'=>['labels'=>array_keys($valores),'series'=>['Valor'=>array_values($valores)]]];return $result;
    }
    public function periodo(array $p): array
    {
        $hoje=new DateTimeImmutable('today');$fim=$hoje;
        $inicio=match($p['periodo']??'mes'){'hoje'=>$hoje,'7dias'=>$hoje->modify('-6 days'),'30dias'=>$hoje->modify('-29 days'),'ano'=>$hoje->modify('first day of january this year'),default=>$hoje->modify('first day of this month')};
        if(($p['periodo']??'')==='personalizado'){
            foreach(['inicio','fim'] as $k){$v=$p[$k]??'';if(!is_string($v)||!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$v)||date('Y-m-d',strtotime($v))!==$v)throw new DomainException('Informe datas válidas.');}
            $inicio=new DateTimeImmutable($p['inicio']);$fim=new DateTimeImmutable($p['fim']);
        }
        if($fim<$inicio||$inicio->diff($fim)->days>1096)throw new DomainException('Selecione um intervalo de até três anos.');
        $dias=$inicio->diff($fim)->days+1;
        return ['inicio'=>$inicio->format('Y-m-d'),'fim'=>$fim->format('Y-m-d'),'anterior_inicio'=>$inicio->modify('-'.$dias.' days')->format('Y-m-d'),'anterior_fim'=>$inicio->modify('-1 day')->format('Y-m-d')];
    }
    public function pedidos(string $inicio,string $fim): array
    {
        return $this->todos("SELECT p.*,pg.metodo,pg.aprovado_em,COALESCE(c.cpv,0) cpv,COALESCE(c.rateado,0) rateado,COALESCE(c.sem_custo,0) sem_custo,
        tf.taxa_pix,tf.taxa_cartao
        FROM pedidos p JOIN pagamentos pg ON pg.id=(SELECT MAX(p2.id) FROM pagamentos p2 WHERE p2.pedido_id=p.id AND p2.status='aprovado')
        LEFT JOIN pedido_taxas_snapshot tf ON tf.pedido_id=p.id
        LEFT JOIN (SELECT i.pedido_id,SUM(ic.custo_total_no_momento) cpv,SUM(ic.custo_indireto_no_momento) rateado,SUM(ic.id IS NULL) sem_custo FROM pedido_itens i LEFT JOIN pedido_item_custos ic ON ic.pedido_item_id=i.id GROUP BY i.pedido_id)c ON c.pedido_id=p.id
        WHERE p.status<>'cancelado' AND NOT EXISTS(SELECT 1 FROM pagamentos re WHERE re.pedido_id=p.id AND re.status='reembolsado')
        AND COALESCE(pg.aprovado_em,pg.criado_em)>=? AND COALESCE(pg.aprovado_em,pg.criado_em)<? ORDER BY pg.aprovado_em",[$inicio,$this->diaSeguinte($fim)]);
    }
    private function diaSeguinte(string $data): string {return (new DateTimeImmutable($data))->modify('+1 day')->format('Y-m-d');}
    public function resumo(string $inicio,string $fim): array
    {
        $pedidos=$this->pedidos($inicio,$fim);$bruto=0;$descontos=0;$cpv=0;$rateado=0;$taxas=0;$sem=0;$semTaxa=0;$serie=[];
        foreach($pedidos as $p){
            $bruto+=(float)$p['subtotal'];$descontos+=(float)$p['desconto'];$cpv+=(float)$p['cpv'];$rateado+=(float)$p['rateado'];$sem+=(int)$p['sem_custo'];
            $taxa=$p['metodo']==='pix'?$p['taxa_pix']:$p['taxa_cartao'];if($taxa===null)$semTaxa++;
            $taxas+=(float)$p['total']*(float)($taxa??0)/100;
            $dia=substr($p['aprovado_em']??$p['criado_em'],0,10);$serie[$dia]??=['receita'=>0,'custo'=>0,'lucro'=>0];
            $liquido=(float)$p['subtotal']-(float)$p['desconto'];$serie[$dia]['receita']+=$liquido;$serie[$dia]['custo']+=(float)$p['cpv'];$serie[$dia]['lucro']+=$liquido-(float)$p['cpv'];
        }
        $despesas=$this->todos('SELECT categoria,SUM(valor) valor FROM despesas_operacionais WHERE competencia>=? AND competencia<=? GROUP BY categoria',[$inicio,$fim]);
        $despesa=array_sum(array_column($despesas,'valor'));
        $indiretos=0;
        foreach($this->todos('SELECT * FROM custos_indiretos WHERE inicio_vigencia<=? AND (fim_vigencia IS NULL OR fim_vigencia>=?)',[$fim,$inicio]) as $c){
            $d=new DateTimeImmutable(max($inicio,$c['inicio_vigencia']));$ate=new DateTimeImmutable(min($fim,$c['fim_vigencia']??$fim));
            for(;$d<=$ate;$d=$d->modify('+1 day'))$indiretos+=(float)$c['valor_mensal']/(int)$d->format('t');
        }
        $naoRateado=max(0,$indiretos-$rateado);$receita=$bruto-$descontos;$lucro=$receita-$cpv;$contrib=$receita-($cpv-$rateado)-$taxas;
        return ['cards'=>['Faturamento bruto'=>$bruto,'Descontos'=>$descontos,'Receita líquida'=>$receita,'Custo dos produtos vendidos'=>$sem?null:$cpv,'Lucro bruto'=>$sem?null:$lucro,'Margem bruta (%)'=>$sem||!$receita?null:$lucro/$receita*100,'Taxas de pagamento'=>$semTaxa?null:$taxas,'Despesas'=>$despesa,'Custos indiretos não rateados'=>$naoRateado,'Resultado operacional estimado'=>$sem||$semTaxa?null:$lucro-$taxas-$despesa-$naoRateado,'Ticket médio'=>count($pedidos)?$receita/count($pedidos):0,'Pedidos pagos'=>count($pedidos),'Margem de contribuição'=>$sem||$semTaxa?null:$contrib,'Margem média por pedido'=>$sem||!count($pedidos)?null:$lucro/count($pedidos)],'sem_custo'=>$sem,'sem_taxa'=>$semTaxa,'serie'=>$serie,'despesas'=>$despesas,'pedidos'=>$pedidos];
    }
    public function rentabilidade(): array
    {
        $lista=$this->todos('SELECT p.id,p.nome,c.preco,c.preco_revenda FROM produtos p JOIN categorias c ON c.id=p.categoria_id ORDER BY p.nome');
        $s=new CustoProdutoService($this->pdo);
        foreach($lista as &$p){
            $c=$s->calcular((int)$p['id']);$p['calculo']=$c;
            $p['custo']=$c&&!$c['pendentes']?(float)$c['custo_por_venda']:null;
            $p['margem']=$p['custo']!==null&&(float)$p['preco']>0?((float)$p['preco']-$p['custo'])/(float)$p['preco']*100:null;
            $p['margem_revenda']=$p['custo']!==null&&(float)$p['preco_revenda']>0?((float)$p['preco_revenda']-$p['custo'])/(float)$p['preco_revenda']*100:null;
        }unset($p);return $lista;
    }
}
