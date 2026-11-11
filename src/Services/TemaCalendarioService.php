<?php
declare(strict_types=1);
namespace App\Services;
use DateTimeImmutable;
use DateTimeZone;
use DomainException;
final class TemaCalendarioService
{
    public static function data(string $regra,int $ano): DateTimeImmutable
    {
        $tz=new DateTimeZone('America/Fortaleza');
        $base=new DateTimeImmutable($ano.'-01-01 00:00:00',$tz);
        $pascoa=$base->setDate($ano,3,21)->modify('+'.easter_days($ano,CAL_EASTER_ALWAYS_GREGORIAN).' days');
        return match($regra){
            'easter'=>$pascoa,'carnival'=>$pascoa->modify('-47 days'),
            'mothers_day_br'=>new DateTimeImmutable('second sunday of May '.$ano,$tz),
            'fathers_day_br'=>new DateTimeImmutable('second sunday of August '.$ano,$tz),
            'black_friday'=>(new DateTimeImmutable('fourth thursday of November '.$ano,$tz))->modify('+1 day'),
            'cyber_monday'=>(new DateTimeImmutable('fourth thursday of November '.$ano,$tz))->modify('+4 days'),
            default=>throw new DomainException('Regra de calendário inválida.')
        };
    }
    public static function vigente(array $a,DateTimeImmutable $agora): bool
    {
        $agora=$agora->setTimezone(new DateTimeZone('America/Fortaleza'));
        if(!$a['ativo'])return false;
        if($a['tipo_regra']==='intervalo_fixo')return $agora>=new DateTimeImmutable($a['inicio_datetime'],$agora->getTimezone()) && $agora<=new DateTimeImmutable($a['fim_datetime'],$agora->getTimezone());
        if($a['tipo_regra']==='anual_fixo'){
            $md=$agora->format('m-d');$ini=sprintf('%02d-%02d',$a['mes_inicio'],$a['dia_inicio']);$fim=sprintf('%02d-%02d',$a['mes_fim'],$a['dia_fim']);
            return $ini<=$fim?($md>=$ini&&$md<=$fim):($md>=$ini||$md<=$fim);
        }
        if($a['tipo_regra']==='regra_calendario'){
            foreach([(int)$agora->format('Y')-1,(int)$agora->format('Y'),(int)$agora->format('Y')+1] as $ano){
                $d=self::data($a['regra_codigo'],$ano);
                if($agora>=$d->modify('-'.(int)$a['dias_antes'].' days') && $agora<=$d->modify('+'.(int)$a['dias_depois'].' days')->setTime(23,59,59))return true;
            }
        }
        return false;
    }
}
