<?php
declare(strict_types=1);
namespace App\Controllers\Admin;
use App\Controllers\Controller;
use App\Helpers\Csrf;
use App\Helpers\Decimal as D;
use App\Repositories\FinanceiroRepository;
use App\Services\CadastroFinanceiro;
use App\Services\CompraInsumoService;
use App\Services\CustoProdutoService;
use DomainException;
use Throwable;
final class FinanceiroController extends Controller
{
    private function proteger(bool $post=false): void
    {
        if(empty($_SESSION['usuario_admin']['id']))$this->redirecionar('/login-admin');
        header('Cache-Control: private, no-store');
        if($post&&!Csrf::validar(is_string($_POST['_token']??null)?$_POST['_token']:null)){http_response_code(403);exit('Sessão inválida. Atualize a página.');}
    }
    public function index(): void { $this->tela('visao-geral'); }
    public function cadastro(): void { $this->tela((string)($_GET['secao']??'insumos')); }
    public function compras(): void {$this->tela('compras');}
    public function fichas(): void {$this->tela('fichas');}
    public function rentabilidade(): void {$this->tela('rentabilidade');}
    public function historico(): void {$this->tela('historico');}
    public function relatorios(): void {$this->tela('relatorios');}
    private function tela(string $secao): void
    {
        $this->proteger();$r=new FinanceiroRepository($this->pdo);$defs=CadastroFinanceiro::definicoes();
        if(!isset($defs[$secao])&&!in_array($secao,['visao-geral','compras','fichas','rentabilidade','historico','relatorios'],true)){$secao='insumos';}
        $referencias=[];
        foreach(['insumos','fornecedores','unidades_medida','categorias_insumos','produtos'] as $t)$referencias[$t]=$r->todos('SELECT * FROM '.$t.' ORDER BY id DESC');
        try{$periodo=$r->periodo($_GET);}catch(DomainException $e){$_SESSION['modulos_aviso']=$e->getMessage();$periodo=$r->periodo([]);}
        $dados=compact('secao','defs','referencias','periodo');
        if(isset($defs[$secao])){
            [$t]=$defs[$secao];$dados['linhas']=$r->todos('SELECT * FROM '.$t.' ORDER BY id DESC LIMIT 500');
            $dados['editar']=$r->um('SELECT * FROM '.$t.' WHERE id=?',[(int)($_GET['editar']??0)]);
        }
        if(in_array($secao,['visao-geral','relatorios'],true)){
            $dados['resumo']=$r->resumo($periodo['inicio'],$periodo['fim']);
            $dados['graficos']=$r->graficos($dados['resumo']);
            $dados['anterior']=$r->resumo($periodo['anterior_inicio'],$periodo['anterior_fim']);
            $dados['criticos']=$r->todos('SELECT i.*,u.codigo FROM insumos i JOIN unidades_medida u ON u.id=i.unidade_base_id WHERE i.ativo=1 AND i.controla_estoque=1 AND i.estoque_atual<=i.estoque_minimo');
            $dados['aumentos']=$r->todos("SELECT h.*,i.nome FROM insumo_precos_historico h JOIN insumos i ON i.id=h.insumo_id JOIN compra_insumo_itens ci ON ci.id=h.compra_item_id JOIN compras_insumos c ON c.id=ci.compra_id WHERE c.status='confirmada' AND h.variacao_percentual>0 ORDER BY h.id DESC LIMIT 10");
            $dados['rentabilidade']=$r->rentabilidade();
        }
        if($secao==='compras'){$dados['compras']=$r->todos('SELECT c.*,f.nome_fantasia FROM compras_insumos c LEFT JOIN fornecedores f ON f.id=c.fornecedor_id ORDER BY c.id DESC LIMIT 200');$dados['itensCompra']=$r->todos('SELECT ci.*,i.nome,u.codigo FROM compra_insumo_itens ci JOIN insumos i ON i.id=ci.insumo_id JOIN unidades_medida u ON u.id=ci.unidade_compra_id ORDER BY ci.id DESC LIMIT 1000');}
        if(in_array($secao,['fichas','rentabilidade'],true))$dados['rentabilidade']=$r->rentabilidade();
        if($secao==='fichas'){
            $dados['fichas']=$r->todos('SELECT f.*,p.nome produto FROM fichas_tecnicas f JOIN produtos p ON p.id=f.produto_id ORDER BY f.id DESC LIMIT 200');
            $dados['calculo']=(new CustoProdutoService($this->pdo))->calcular((int)($_GET['produto']??0));
        }
        if($secao==='historico'){
            $dados['historico']=$r->todos('SELECT h.*,i.nome FROM insumo_precos_historico h JOIN insumos i ON i.id=h.insumo_id ORDER BY h.id DESC LIMIT 300');
            $dados['snapshots']=$r->todos('SELECT s.*,p.nome FROM produto_custos_snapshot s JOIN produtos p ON p.id=s.produto_id ORDER BY s.id DESC LIMIT 200');
            $dados['movimentos']=$r->todos('SELECT m.*,i.nome FROM estoque_insumos_movimentacoes m JOIN insumos i ON i.id=m.insumo_id ORDER BY m.id DESC LIMIT 200');
            $dados['auditoria']=$r->todos("SELECT * FROM modulos_auditoria WHERE modulo='financeiro' ORDER BY id DESC LIMIT 100");
        }
        $dados['config']=$r->configuracoes();
        $this->view('admin/financeiro/index',$dados);
    }
    public function acao(): void
    {
        $this->proteger(true);$r=new FinanceiroRepository($this->pdo);$admin=(int)$_SESSION['usuario_admin']['id'];$acao=(string)($_POST['acao']??'');$destino='/admin/financeiro';
        try{
            if($acao==='cadastro'){
                $secao=(string)($_POST['secao']??'');$def=CadastroFinanceiro::definicoes()[$secao]??null;
                if(!$def)throw new DomainException('Cadastro inválido.');
                [$t,$titulo,$campos]=$def;$v=CadastroFinanceiro::validar($campos,$_POST);$id=(int)($_POST['id']??0);
                $destino.='/cadastros?secao='.urlencode($secao);
                $r->transacao(function()use($r,$t,$v,$id,$admin){
                    $antes=$id?$r->um('SELECT * FROM '.$t.' WHERE id=? FOR UPDATE',[$id]):null;
                    if($id&&!$antes)throw new DomainException('Registro não encontrado.');
                    if($t==='insumos' && $antes && $antes['unidade_base_id']!=$v['unidade_base_id'])throw new DomainException('A unidade-base de um insumo existente não pode mudar. Cadastre outro insumo.');
                    if($t==='unidades_medida'&&$antes&&($antes['codigo']!==$v['codigo']||$antes['tipo']!==$v['tipo']))throw new DomainException('Código e tipo de unidades existentes são permanentes.');
                    if($id)$r->atualizar($t,$id,$v);else $id=$r->inserir($t,$v);
                    $r->auditar('financeiro',$t,$id,$antes?'alterado':'criado',$antes,$v,$admin);
                });
            }elseif($acao==='compra'){
                (new CompraInsumoService($this->pdo))->salvar($_POST,$admin);$destino.='/compras';
            }elseif(in_array($acao,['confirmar','cancelar'],true)){
                (new CompraInsumoService($this->pdo))->{$acao}((int)($_POST['id']??0),$admin);$destino.='/compras';
            }elseif($acao==='ficha'){
                (new CustoProdutoService($this->pdo))->salvarFicha($_POST,$admin);$destino.='/fichas?produto='.(int)$_POST['produto_id'];
            }elseif($acao==='snapshot'){
                $c=(new CustoProdutoService($this->pdo))->snapshot((int)($_POST['produto_id']??0));
                if(!$c)throw new DomainException('Cadastre a ficha e os preços de todos os insumos antes de registrar o custo.');
                $r->auditar('financeiro','produto_custos_snapshot',(int)$c['id'],'recalculo',null,$c,$admin);$destino.='/fichas?produto='.(int)$_POST['produto_id'];
            }elseif($acao==='config'){
                $r->transacao(function()use($r,$admin){$antes=$r->configuracoes();$novo=[];foreach(array_keys($antes) as $k){$novo[$k]=D::valor($_POST[$k]??'','0','100',4);$r->executar('UPDATE configuracoes_financeiras SET valor=? WHERE chave=?',[$novo[$k],$k]);}$r->auditar('financeiro','configuracoes_financeiras',0,'alterado',$antes,$novo,$admin);});
            }elseif($acao==='movimento'){
                $r->transacao(function()use($r,$admin){
                    $id=(int)($_POST['insumo_id']??0);$i=$r->um('SELECT * FROM insumos WHERE id=? FOR UPDATE',[$id]);if(!$i)throw new DomainException('Insumo inválido.');
                    $tipo=(string)($_POST['tipo']??'');if(!in_array($tipo,['consumo_producao','perda','ajuste_positivo','ajuste_negativo','devolucao'],true))throw new DomainException('Movimento inválido.');
                    $q=D::valor($_POST['quantidade']??'','0.0001','999999999',4);$motivo=trim((string)($_POST['motivo']??''));if(mb_strlen($motivo)<5||mb_strlen($motivo)>255)throw new DomainException('Informe o motivo (5 a 255 caracteres).');
                    $saldo=$tipo==='ajuste_positivo'?bcadd($i['estoque_atual'],$q,4):bcsub($i['estoque_atual'],$q,4);if(bccomp($saldo,'0',4)<0)throw new DomainException('Estoque insuficiente.');
                    $r->atualizar('insumos',$id,['estoque_atual'=>$saldo]);$r->inserir('estoque_insumos_movimentacoes',['insumo_id'=>$id,'tipo'=>$tipo,'quantidade'=>$q,'saldo_anterior'=>$i['estoque_atual'],'saldo_posterior'=>$saldo,'motivo'=>$motivo,'admin_id'=>$admin]);$r->auditar('financeiro','insumos',$id,$tipo,$i,['estoque_atual'=>$saldo,'motivo'=>$motivo],$admin);
                });$destino.='/cadastros?secao=insumos';
            }else throw new DomainException('Ação inválida.');
            $_SESSION['modulos_aviso']='Alteração salva com sucesso.';
        }catch(DomainException $e){$_SESSION['modulos_aviso']=$e->getMessage();}
        catch(Throwable $e){error_log('Financeiro: '.$e->getMessage());$_SESSION['modulos_aviso']='Não foi possível salvar. Confira campos, duplicidades e vínculos.';}
        $this->redirecionar($destino);
    }
}
