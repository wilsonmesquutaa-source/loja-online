<?php
declare(strict_types=1);
namespace App\Services;
final class TemaDecoracaoService
{
    public const ESTILOS=[
        'nenhuma'=>'Sem decoração','natal'=>'Natal · folhagens, laço e luzes',
        'ano-novo'=>'Ano Novo · estrelas douradas','namorados'=>'Dia dos Namorados · corações',
        'sao-joao'=>'São João · bandeirinhas e balão','maes'=>'Dia das Mães · flores e folhagens',
        'ferias'=>'Férias · sol, praia e viagem','pascoa'=>'Páscoa · coelhos e ovos',
        'carnaval'=>'Carnaval · máscaras e serpentinas','pais'=>'Dia dos Pais · gravatas e presentes',
        'criancas'=>'Dia das Crianças · brinquedos','halloween'=>'Halloween · abóboras, morcegos e fantasmas','black-friday'=>'Black Friday · preto e dourado'
    ];
    public static function estilo(array $tema): string
    {
        $escolhido=$tema['config']['decoracao_header']??null;
        if(is_string($escolhido)&&isset(self::ESTILOS[$escolhido]))return $escolhido;
        foreach(['header_guirlanda'=>'natal','header_coracoes'=>'namorados','header_flores'=>'maes','header_bandeirinhas'=>'sao-joao','header_serpentina'=>'carnaval','header_luzes'=>'natal'] as $key=>$estilo)if(!empty($tema['componentes'][$key]['ativo']))return $estilo;
        $slug=$tema['tema']['slug']??'';
        if($slug==='halloween'||str_starts_with($slug,'halloween-'))return 'halloween';
        foreach(['dia-dos-namorados'=>'namorados','dia-das-maes'=>'maes','dia-dos-pais'=>'pais','dia-das-criancas'=>'criancas','natal'=>'natal','ano-novo'=>'ano-novo','sao-joao'=>'sao-joao','ferias'=>'ferias','pascoa'=>'pascoa','carnaval'=>'carnaval','black-friday'=>'black-friday'] as $prefix=>$estilo)if($slug===$prefix||str_starts_with($slug,$prefix.'-'))return $estilo;
        return 'nenhuma';
    }
}
