<?php

declare(strict_types=1);

namespace App\Services;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

final class PedidoAgendaService
{
    /*
    =================================
    CONFIGURAÇÕES DA LOJA
    =================================
    */

    private const TIMEZONE =
        'America/Fortaleza';

    private const HORA_ABERTURA =
        8;

    private const HORA_FECHAMENTO =
        17;

    private const INTERVALO_MINUTOS =
        30;


    /*
    =================================
    ENDEREÇO DE RETIRADA
    =================================
    */

    private const ENDERECO_RETIRADA =
        'Rua Dragão do Mar, 608, Praia de Iracema, Fortaleza - CE';


    /*
    =================================
    ANALISA O PEDIDO
    =================================

    Retorna as regras que serão usadas
    para calcular os horários disponíveis.
    */

    public function analisarPedido(
        array $itens
    ): array {

        if (
            $itens === []
        ) {

            throw new InvalidArgumentException(
                'O pedido não possui itens.'
            );
        }


        /*
        =================================
        TOTAIS
        =================================
        */

        $quantidadeTradicionaisFritos =
            0;


        $quantidadeTradicionaisForno =
            0;


        $quantidadeGrandesFritos =
            0;


        $quantidadeGrandesForno =
            0;


        $quantidadeFolhados =
            0;


        $quantidadeEmpadoes =
            0;


        foreach (
            $itens as $item
        ) {

            $categoriaId =
                (int)
                (
                    $item[
                        'categoria_id'
                    ]
                    ?? 0
                );


            $quantidade =
                (int)
                (
                    $item[
                        'quantidade'
                    ]
                    ?? 0
                );


            $tipoPreparo =
                strtolower(
                    trim(
                        (string)
                        (
                            $item[
                                'tipo_preparo'
                            ]
                            ?? ''
                        )
                    )
                );


            if (
                $quantidade <= 0
            ) {

                continue;
            }


            /*
            =================================
            CENTO TRADICIONAL
            =================================
            */

            if (
                $categoriaId === 1
            ) {

                if (
                    $tipoPreparo ===
                    'frito'
                ) {

                    $quantidadeTradicionaisFritos +=
                        $quantidade;

                } else {

                    /*
                    Todos os tradicionais
                    classificados como forno
                    exigem 24h.
                    */

                    $quantidadeTradicionaisForno +=
                        $quantidade;
                }


                continue;
            }


            /*
            =================================
            CENTO FOLHADO
            =================================
            */

            if (
                $categoriaId === 2
            ) {

                $quantidadeFolhados +=
                    $quantidade;

                continue;
            }


            /*
            =================================
            SALGADOS GRANDES
            =================================
            */

            if (
                $categoriaId === 3
            ) {

                if (
                    $tipoPreparo ===
                    'frito'
                ) {

                    $quantidadeGrandesFritos +=
                        $quantidade;

                } else {

                    $quantidadeGrandesForno +=
                        $quantidade;
                }


                continue;
            }


            /*
            =================================
            EMPADÕES
            =================================
            */

            if (
                $categoriaId === 4
            ) {

                $quantidadeEmpadoes +=
                    $quantidade;
            }
        }


        /*
        =================================
        CENTOS FRITOS TRADICIONAIS
        =================================

        Cada cento permite 4 sabores/partes.

        Exemplo:

        1 + 1 + 1 + 1 = 1 cento
        2 + 1 + 1 + 2 = 2 centos
        */

        $centosTradicionaisFritos =
            $this->calcularCentos(
                $quantidadeTradicionaisFritos,
                4
            );


        /*
        =================================
        CALCULA TEMPO DOS TRADICIONAIS
        =================================
        */

        $tempoTradicionaisFritos =
            $this->calcularTempoTradicionaisFritos(
                $centosTradicionaisFritos
            );


        /*
        =================================
        24 HORAS
        =================================
        */

        $exige24Horas =
            (
                $quantidadeTradicionaisForno > 0
                ||
                $quantidadeFolhados > 0
                ||
                $quantidadeEmpadoes > 0
                ||
                $quantidadeGrandesForno > 0
            );


        $antecedenciaMinima =
            $exige24Horas
                ? 1440
                : 0;


        /*
        =================================
        TEMPO DE PRODUÇÃO
        =================================

        Tradicionais fritos:
        conforme número de centos.

        Grandes fritos:
        30 minutos.

        Produtos de 24h:
        a regra comercial é antecedência,
        e não 24h de produção contínua.
        */

        $tempoProducao =
            $tempoTradicionaisFritos;


        if (
            $quantidadeGrandesFritos > 0
            &&
            30 >
            $tempoProducao
        ) {

            $tempoProducao =
                30;
        }


        /*
        =================================
        DESCRIÇÃO
        =================================
        */

        $regras =
            [];


        if (
            $centosTradicionaisFritos > 0
        ) {

            $regras[] =
                'Tradicionais fritos: '
                . $centosTradicionaisFritos
                . (
                    $centosTradicionaisFritos === 1
                        ? ' cento'
                        : ' centos'
                )
                . ' → '
                . $this->formatarDuracao(
                    $tempoTradicionaisFritos
                );
        }


        if (
            $quantidadeTradicionaisForno > 0
        ) {

            $regras[] =
                'Produtos tradicionais de forno '
                . '→ mínimo de 24 horas de antecedência.';
        }


        if (
            $quantidadeFolhados > 0
        ) {

            $regras[] =
                'Folhados '
                . '→ mínimo de 24 horas de antecedência.';
        }


        if (
            $quantidadeEmpadoes > 0
        ) {

            $regras[] =
                'Empadões '
                . '→ mínimo de 24 horas de antecedência.';
        }


        if (
            $quantidadeGrandesFritos > 0
        ) {

            $regras[] =
                'Salgados grandes fritos '
                . '→ 30 minutos de preparo.';
        }


        if (
            $quantidadeGrandesForno > 0
        ) {

            $regras[] =
                'Salgados grandes de forno '
                . '→ mínimo de 24 horas de antecedência.';
        }


        return [

            'centos_tradicionais_fritos' =>
                $centosTradicionaisFritos,

            'quantidade_tradicionais_forno' =>
                $quantidadeTradicionaisForno,

            'quantidade_folhados' =>
                $quantidadeFolhados,

            'quantidade_empadoes' =>
                $quantidadeEmpadoes,

            'quantidade_grandes_fritos' =>
                $quantidadeGrandesFritos,

            'quantidade_grandes_forno' =>
                $quantidadeGrandesForno,

            'tempo_producao_minutos' =>
                $tempoProducao,

            'antecedencia_minima_minutos' =>
                $antecedenciaMinima,

            'exige_24h' =>
                $exige24Horas,

            'regras' =>
                $regras,

            'endereco_retirada' =>
                self::ENDERECO_RETIRADA,
        ];
    }


    /*
    =================================
    PRIMEIRO HORÁRIO POSSÍVEL
    =================================
    */

    public function calcularPrimeiroHorario(
        array $itens,
        ?DateTimeImmutable $agora = null
    ): array {

        $agora =
            $agora
            ?? $this->agora();


        $analise =
            $this->analisarPedido(
                $itens
            );


        /*
        =================================
        PRAZO DE PRODUÇÃO
        =================================
        */

        $fimMinimoProducao =
            $this->calcularFimDaProducao(
                $agora,
                $analise[
                    'tempo_producao_minutos'
                ]
            );


        /*
        =================================
        ANTECEDÊNCIA
        =================================
        */

        $limiteAntecedencia =
            $agora->add(
                new DateInterval(
                    'PT'
                    . $analise[
                        'antecedencia_minima_minutos'
                    ]
                    . 'M'
                )
            );


        /*
        =================================
        PRIMEIRO MOMENTO
        =================================
        */

        $primeiroMomento =
            $this->maiorData(
                $fimMinimoProducao,
                $limiteAntecedencia
            );


        /*
        =================================
        AJUSTE DE EXPEDIENTE
        =================================
        */

        $primeiroMomento =
            $this->ajustarParaExpediente(
                $primeiroMomento
            );


        /*
        =================================
        REGRA DE FIM DE SEMANA
        =================================
        */

        $primeiroMomento =
            $this->ajustarRegraFimDeSemana(
                $agora,
                $primeiroMomento
            );


        return [

            'agora' =>
                $agora,

            'primeiro_horario' =>
                $primeiroMomento,

            'primeiro_horario_formatado' =>
                $this->formatarDataHora(
                    $primeiroMomento
                ),

            'inicio_expediente' =>
                self::HORA_ABERTURA,

            'fim_expediente' =>
                self::HORA_FECHAMENTO,

            'tempo_producao_minutos' =>
                $analise[
                    'tempo_producao_minutos'
                ],

            'tempo_producao' =>
                $this->formatarDuracao(
                    $analise[
                        'tempo_producao_minutos'
                    ]
                ),

            'antecedencia_minima_minutos' =>
                $analise[
                    'antecedencia_minima_minutos'
                ],

            'exige_24h' =>
                $analise[
                    'exige_24h'
                ],

            'regras' =>
                $analise[
                    'regras'
                ],
        ];
    }


    /*
    =================================
    HORÁRIOS DE RETIRADA
    =================================
    */

    public function gerarHorariosRetirada(
        array $itens,
        ?DateTimeImmutable $agora = null,
        int $dias = 7
    ): array {

        return $this->gerarHorarios(
            $itens,
            $agora,
            $dias,
            false
        );
    }


    /*
    =================================
    HORÁRIOS DE ENTREGA
    =================================
    */

    public function gerarHorariosEntrega(
        array $itens,
        ?DateTimeImmutable $agora = null,
        int $dias = 7
    ): array {

        return $this->gerarHorarios(
            $itens,
            $agora,
            $dias,
            true
        );
    }


    /*
    =================================
    GERA HORÁRIOS
    =================================
    */

    private function gerarHorarios(
        array $itens,
        ?DateTimeImmutable $agora,
        int $dias,
        bool $entrega
    ): array {

        $agora =
            $agora
            ?? $this->agora();


        $primeiro =
            $this->calcularPrimeiroHorario(
                $itens,
                $agora
            );


        $primeiroHorario =
            $primeiro[
                'primeiro_horario'
            ];


        $resultado =
            [];


        /*
        =================================
        DATA INICIAL
        =================================
        */

        $dataInicial =
            $agora->setTime(
                self::HORA_ABERTURA,
                0,
                0
            );


        /*
        =================================
        DIAS
        =================================
        */

        for (
            $dia = 0;
            $dia < $dias;
            $dia++
        ) {

            $data =
                $dataInicial
                ->modify(
                    '+'
                    . $dia
                    . ' day'
                );


            /*
            =================================
            HORÁRIO INICIAL
            =================================
            */

            $inicioMinutos =
                self::HORA_ABERTURA
                * 60;


            /*
            =================================
            HORÁRIO FINAL
            =================================
            */

            $fimMinutos =
                self::HORA_FECHAMENTO
                * 60;


            /*
            =================================
            ENTREGA
            =================================

            Última janela:

            16:30 → 17:00
            */

            if (
                $entrega
            ) {

                $fimMinutos -=
                    self::INTERVALO_MINUTOS;
            }


            for (
                $minuto = $inicioMinutos;
                $minuto <= $fimMinutos;
                $minuto +=
                    self::INTERVALO_MINUTOS
            ) {

                $hora =
                    intdiv(
                        $minuto,
                        60
                    );


                $minutos =
                    $minuto % 60;


                $horario =
                    $data->setTime(
                        $hora,
                        $minutos,
                        0
                    );


                /*
                =================================
                IGNORA HORÁRIO ANTERIOR
                =================================
                */

                if (
                    $horario <
                    $primeiroHorario
                ) {

                    continue;
                }


                /*
                =================================
                VALIDA FIM DE SEMANA
                =================================
                */

                if (
                    !$this->horarioPermitidoNoFimDeSemana(
                        $agora,
                        $horario
                    )
                ) {

                    continue;
                }


                /*
                =================================
                JANELA DE ENTREGA
                =================================
                */

                $fimJanela =
                    null;


                if (
                    $entrega
                ) {

                    $fimJanela =
                        $horario->add(
                            new DateInterval(
                                'PT30M'
                            )
                        );
                }


                $resultado[] = [

                    'data' =>
                        $horario->format(
                            'Y-m-d'
                        ),

                    'data_formatada' =>
                        $this->formatarData(
                            $horario
                        ),

                    'hora' =>
                        $horario->format(
                            'H:i'
                        ),

                    'hora_fim' =>
                        $fimJanela !== null
                            ? $fimJanela->format(
                                'H:i'
                            )
                            : null,

                    'datetime' =>
                        $horario->format(
                            'Y-m-d H:i:s'
                        ),

                    'datetime_fim' =>
                        $fimJanela !== null
                            ? $fimJanela->format(
                                'Y-m-d H:i:s'
                            )
                            : null,
                ];
            }
        }


        return $resultado;
    }


    /*
    =================================
    CENTOS
    =================================
    */

    private function calcularCentos(
        int $quantidade,
        int $partesPorCento
    ): int {

        if (
            $quantidade <= 0
            ||
            $partesPorCento <= 0
        ) {

            return 0;
        }


        return
            (int)
            ceil(
                $quantidade
                /
                $partesPorCento
            );
    }


    /*
    =================================
    TEMPO DOS TRADICIONAIS FRITOS
    =================================

    Até 3 centos:
    1h30

    4 centos:
    2h

    5 centos:
    2h30

    etc.
    */

    private function calcularTempoTradicionaisFritos(
        int $centos
    ): int {

        if (
            $centos <= 0
        ) {

            return 0;
        }


        if (
            $centos <= 3
        ) {

            return 90;
        }


        return
            90
            +
            (
                (
                    $centos - 3
                )
                * 30
            );
    }


    /*
    =================================
    CALCULA FIM DA PRODUÇÃO
    =================================

    Consome somente minutos dentro
    do expediente da loja.

    Exemplo:

    16:30 + 90min

    → 17:00
    → próximo dia 08:00
    → 08:30
    → 09:00

    Resultado:
    09:00
    */

    private function calcularFimDaProducao(
        DateTimeImmutable $inicio,
        int $duracaoMinutos
    ): DateTimeImmutable {

        if (
            $duracaoMinutos <= 0
        ) {

            return
                $this->ajustarParaExpediente(
                    $inicio
                );
        }


        $momento =
            $this->ajustarParaExpediente(
                $inicio
            );


        $restantes =
            $duracaoMinutos;


        while (
            $restantes > 0
        ) {

            $hora =
                (int)
                $momento->format(
                    'H'
                );


            $minuto =
                (int)
                $momento->format(
                    'i'
                );


            $minutosAtuais =
                (
                    $hora * 60
                )
                +
                $minuto;


            $fimExpediente =
                self::HORA_FECHAMENTO
                * 60;


            $disponiveisHoje =
                $fimExpediente
                -
                $minutosAtuais;


            if (
                $disponiveisHoje <= 0
            ) {

                $momento =
                    $momento
                    ->modify(
                        '+1 day'
                    )
                    ->setTime(
                        self::HORA_ABERTURA,
                        0,
                        0
                    );

                continue;
            }


            if (
                $restantes <=
                $disponiveisHoje
            ) {

                return
                    $momento->add(
                        new DateInterval(
                            'PT'
                            . $restantes
                            . 'M'
                        )
                    );
            }


            $restantes -=
                $disponiveisHoje;


            $momento =
                $momento
                ->modify(
                    '+1 day'
                )
                ->setTime(
                    self::HORA_ABERTURA,
                    0,
                    0
                );
        }


        return $momento;
    }


    /*
    =================================
    AJUSTA EXPEDIENTE
    =================================
    */

    private function ajustarParaExpediente(
        DateTimeImmutable $momento
    ): DateTimeImmutable {

        $hora =
            (int)
            $momento->format(
                'H'
            );


        $minuto =
            (int)
            $momento->format(
                'i'
            );


        /*
        =================================
        ANTES DAS 08:00
        =================================
        */

        if (
            $hora <
            self::HORA_ABERTURA
        ) {

            return
                $momento->setTime(
                    self::HORA_ABERTURA,
                    0,
                    0
                );
        }


        /*
        =================================
        A PARTIR DAS 17:00
        =================================
        */

        if (
            $hora >=
            self::HORA_FECHAMENTO
        ) {

            return
                $momento
                ->modify(
                    '+1 day'
                )
                ->setTime(
                    self::HORA_ABERTURA,
                    0,
                    0
                );
        }


        /*
        =================================
        ARREDONDA PARA 30 MINUTOS
        =================================
        */

        if (
            $minuto === 0
            ||
            $minuto === 30
        ) {

            return
                $momento->setTime(
                    $hora,
                    $minuto,
                    0
                );
        }


        if (
            $minuto < 30
        ) {

            return
                $momento->setTime(
                    $hora,
                    30,
                    0
                );
        }


        /*
        Exemplo:

        14:45
        ↓
        15:00
        */

        $proximaHora =
            $hora + 1;


        if (
            $proximaHora >=
            self::HORA_FECHAMENTO
        ) {

            return
                $momento
                ->modify(
                    '+1 day'
                )
                ->setTime(
                    self::HORA_ABERTURA,
                    0,
                    0
                );
        }


        return
            $momento->setTime(
                $proximaHora,
                0,
                0
            );
    }


    /*
    =================================
    REGRA DE FIM DE SEMANA
    =================================
    */

    private function ajustarRegraFimDeSemana(
        DateTimeImmutable $agora,
        DateTimeImmutable $horario
    ): DateTimeImmutable {

        /*
        =================================
        SÁBADO
        =================================

        Para receber sábado, o pedido
        precisa respeitar 24h.
        */

        if (
            (int)
            $horario->format('N')
            === 6
        ) {

            $limiteSabado =
                $agora->add(
                    new DateInterval(
                        'PT1440M'
                    )
                );


            if (
                $horario <
                $limiteSabado
            ) {

                return
                    $this->ajustarParaExpediente(
                        $limiteSabado
                    );
            }
        }


        /*
        =================================
        DOMINGO
        =================================

        Pedidos para domingo precisam
        ter sido feitos até sábado às 17h.

        Se o momento atual já passou esse
        limite, domingo deixa de ser
        uma opção.
        */

        return $horario;
    }


    /*
    =================================
    VALIDA SLOT DE FIM DE SEMANA
    =================================
    */

    private function horarioPermitidoNoFimDeSemana(
        DateTimeImmutable $agora,
        DateTimeImmutable $horario
    ): bool {

        $dia =
            (int)
            $horario->format(
                'N'
            );


        /*
        =================================
        SÁBADO
        =================================
        */

        if (
            $dia === 6
        ) {

            $limite =
                $agora->add(
                    new DateInterval(
                        'PT1440M'
                    )
                );


            return
                $horario >=
                $limite;
        }


        /*
        =================================
        DOMINGO
        =================================
        */

        if (
            $dia === 7
        ) {

            /*
            O limite comercial para
            pedidos destinados ao domingo
            é sábado às 17:00.

            Se o pedido atual já passou
            desse momento, domingo não
            pode mais ser escolhido.
            */

            $sabado =
                $horario
                ->modify(
                    '-1 day'
                )
                ->setTime(
                    17,
                    0,
                    0
                );


            return
                $agora <=
                $sabado;
        }


        /*
        =================================
        SEGUNDA A SEXTA
        =================================
        */

        return true;
    }


    /*
    =================================
    MAIOR DATA
    =================================
    */

    private function maiorData(
        DateTimeImmutable $a,
        DateTimeImmutable $b
    ): DateTimeImmutable {

        return
            $a >= $b
                ? $a
                : $b;
    }


    /*
    =================================
    AGORA
    =================================
    */

    private function agora(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            'now',
            new DateTimeZone(
                self::TIMEZONE
            )
        );
    }


    /*
    =================================
    FORMATA DATA
    =================================
    */

    private function formatarData(
        DateTimeInterface $data
    ): string {

        $dias = [

            1 =>
                'segunda-feira',

            2 =>
                'terça-feira',

            3 =>
                'quarta-feira',

            4 =>
                'quinta-feira',

            5 =>
                'sexta-feira',

            6 =>
                'sábado',

            7 =>
                'domingo',
        ];


        return
            $dias[
                (int)
                $data->format('N')
            ]
            . ', '
            . $data->format('d/m/Y');
    }


    /*
    =================================
    FORMATA DATA E HORA
    =================================
    */

    private function formatarDataHora(
        DateTimeInterface $data
    ): string {

        return
            $this->formatarData(
                $data
            )
            . ' às '
            . $data->format('H:i');
    }


    /*
    =================================
    FORMATA DURAÇÃO
    =================================
    */

    private function formatarDuracao(
        int $minutos
    ): string {

        if (
            $minutos <= 0
        ) {

            return
                'sem prazo adicional';
        }


        $horas =
            intdiv(
                $minutos,
                60
            );


        $restantes =
            $minutos % 60;


        $partes =
            [];


        if (
            $horas > 0
        ) {

            $partes[] =
                $horas
                . (
                    $horas === 1
                        ? ' hora'
                        : ' horas'
                );
        }


        if (
            $restantes > 0
        ) {

            $partes[] =
                $restantes
                . ' minutos';
        }


        return implode(
            ' e ',
            $partes
        );
    }
}