<?php
declare(strict_types=1);
namespace App\Services;
use App\Helpers\Decimal as D;
use DomainException;
final class CustoMedioInsumoService
{
    public static function fator(string $origem,string $destino,mixed $explicito): string
    {
        if($origem===$destino)return '1';
        $fixos=['kg:g'=>'1000','g:kg'=>'0.001','l:ml'=>'1000','ml:l'=>'0.001'];
        if(isset($fixos[$origem.':'.$destino])) return $fixos[$origem.':'.$destino];
        if(in_array($origem,['pacote','caixa'],true))return D::valor($explicito,'0.00000001');
        throw new DomainException('Unidades incompatíveis. Use kg/g, L/ml ou informe o conteúdo da embalagem na unidade-base.');
    }
    public static function medio(string $saldo,string $custo,string $entrada,string $preco): string
    {
        $total=bcadd($saldo,$entrada,8);
        if(bccomp($total,'0',8)<=0)throw new DomainException('Quantidade deve ser positiva.');
        return bcdiv(bcadd(bcmul($saldo,$custo,12),bcmul($entrada,$preco,12),12),$total,8);
    }
}
