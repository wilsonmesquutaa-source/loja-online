<?php
declare(strict_types=1);
namespace App\Services;
use App\Helpers\Decimal;
use DomainException;
final class CadastroFinanceiro
{
    // Campo: rótulo, tipo, opções (listas fechadas ou tabela de referência).
    public static function definicoes(): array
    {
        return [
        'unidades'=>['unidades_medida','Unidades de medida',['codigo'=>['Código','text'],'nome'=>['Nome','text'],'tipo'=>['Tipo','select',['massa'=>'Massa','volume'=>'Volume','unidade'=>'Unidade','outro'=>'Outro']],'casas_decimais'=>['Casas decimais','number'],'ativo'=>['Ativo','check']]],
        'categorias-insumos'=>['categorias_insumos','Categorias de insumos',['nome'=>['Nome','text'],'slug'=>['Identificador','text'],'ativo'=>['Ativo','check']]],
        'insumos'=>['insumos','Insumos',['nome'=>['Nome','text'],'descricao'=>['Descrição','optional'],'categoria_insumo_id'=>['Categoria','ref','categorias_insumos'],'unidade_base_id'=>['Unidade-base','ref','unidades_medida'],'estoque_minimo'=>['Estoque mínimo na unidade-base','decimal'],'metodo_custeio'=>['Método de custo','select',['medio_ponderado'=>'Médio ponderado','ultimo_preco'=>'Último preço']],'perecivel'=>['Perecível','check'],'controla_estoque'=>['Controlar estoque','check'],'ativo'=>['Ativo','check']]],
        'fornecedores'=>['fornecedores','Fornecedores',['nome_fantasia'=>['Nome fantasia','text'],'razao_social'=>['Razão social','optional'],'documento'=>['CPF/CNPJ','optional'],'telefone'=>['Telefone','optional'],'email'=>['E-mail','email'],'observacao'=>['Observações','optional'],'ativo'=>['Ativo','check']]],
        'vinculos'=>['fornecedor_insumos','Insumos por fornecedor',['fornecedor_id'=>['Fornecedor','ref','fornecedores'],'insumo_id'=>['Insumo','ref','insumos'],'codigo_fornecedor'=>['Código no fornecedor','optional'],'marca'=>['Marca','optional'],'preferencial'=>['Preferencial','check']]],
        'custos-indiretos'=>['custos_indiretos','Custos indiretos',['nome'=>['Nome','text'],'categoria'=>['Categoria','select',array_combine(['energia','gas','agua','mao_de_obra','limpeza','manutencao','outro'],['Energia','Gás','Água','Mão de obra','Limpeza','Manutenção','Outro'])],'valor_mensal'=>['Valor mensal (R$)','decimal'],'forma_rateio'=>['Rateio','select',['por_unidade'=>'Por unidade produzida','por_receita'=>'Por receita','por_tempo_producao'=>'Por minuto de produção','percentual_custo_direto'=>'Percentual do custo direto','manual'=>'Não rateado (despesa do período)']],'base_mensal'=>['Produção mensal prevista (unidades, receitas ou minutos)','decimal'],'percentual_rateio'=>['Percentual do custo direto (%)','percent'],'inicio_vigencia'=>['Início da vigência','date'],'fim_vigencia'=>['Fim da vigência (opcional)','date_optional'],'ativo'=>['Ativo','check']]],
        'despesas'=>['despesas_operacionais','Despesas operacionais',['competencia'=>['Competência (data do lançamento)','date'],'categoria'=>['Categoria','text'],'descricao'=>['Descrição','text'],'valor'=>['Valor (R$)','decimal'],'recorrente'=>['Recorrente (lembrete; lançar cada competência)','check'],'observacao'=>['Observações','optional']]]
        ];
    }
    public static function validar(array $campos,array $p): array
    {
        $v=[];
        foreach($campos as $k=>$c){
            $raw=$p[$k]??'';if(!is_scalar($raw))throw new DomainException('Campo inválido: '.$c[0]);
            $s=trim((string)$raw);
            $v[$k]=match($c[1]){
                'check'=>isset($p[$k])?1:0,
                'decimal'=>Decimal::valor($s===''?'0':$s,'0','999999999',4),
                'percent'=>Decimal::valor($s===''?'0':$s,'0','100',4),
                'number'=>Decimal::valor($s,'0','8',0),
                'ref'=>(int)$s,
                default=>$s
            };
            if($c[1]==='text' && ($s==='' || mb_strlen($s)>100))throw new DomainException($c[0].': use entre 1 e 100 caracteres.');
            if(in_array($c[1],['optional','email']) && mb_strlen($s)>180)throw new DomainException($c[0].': máximo 180 caracteres.');
            if($c[1]==='email' && $s!=='' && !filter_var($s,FILTER_VALIDATE_EMAIL))throw new DomainException('E-mail inválido.');
            $limites=['documento'=>20,'telefone'=>30,'codigo'=>20,'slug'=>120];
            if(isset($limites[$k]) && strlen($s)>$limites[$k])throw new DomainException($c[0].': máximo '.$limites[$k].' caracteres.');
            if($c[1]==='select' && !array_key_exists($s,$c[2]))throw new DomainException('Opção inválida.');
            if(str_starts_with($c[1],'date')){
                if($s===''&&$c[1]==='date_optional'){$v[$k]=null;continue;}
                if(!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$s)||date('Y-m-d',strtotime($s))!==$s)throw new DomainException('Data inválida: '.$c[0]);
            }
        }
        if(isset($v['fim_vigencia']) && $v['fim_vigencia']<$v['inicio_vigencia'])throw new DomainException('Fim da vigência anterior ao início.');
        if(isset($v['forma_rateio']) && in_array($v['forma_rateio'],['por_unidade','por_receita','por_tempo_producao'],true) && bccomp($v['base_mensal'],'0',4)<=0)throw new DomainException('Informe a produção mensal prevista para o rateio.');
        return $v;
    }
}
