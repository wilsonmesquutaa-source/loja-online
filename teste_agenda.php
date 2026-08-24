<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Services\PedidoAgendaService;


$service =
    new PedidoAgendaService();


$pedido24h = [

    [
        'categoria_id' => 1,

        'categoria_nome' =>
            'Cento de Salgados Tradicionais',

        'tipo_preparo' =>
            'forno',

        'quantidade' =>
            1,
    ],
];


$pedidoFrito = [

    [
        'categoria_id' => 1,

        'categoria_nome' =>
            'Cento de Salgados Tradicionais',

        'tipo_preparo' =>
            'frito',

        'quantidade' =>
            4,
    ],
];


$testes = [

    /*
    =================================
    SEXTA 16:00
    =================================
    */

    'Sexta 16:00 - pedido 24h' => [
        'agora' =>
            new DateTimeImmutable(
                '2026-08-28 16:00:00',
                new DateTimeZone(
                    'America/Fortaleza'
                )
            ),

        'itens' =>
            $pedido24h,
    ],


    /*
    =================================
    SEXTA 18:00
    =================================
    */

    'Sexta 18:00 - pedido 24h' => [
        'agora' =>
            new DateTimeImmutable(
                '2026-08-28 18:00:00',
                new DateTimeZone(
                    'America/Fortaleza'
                )
            ),

        'itens' =>
            $pedido24h,
    ],


    /*
    =================================
    SÁBADO 16:00
    =================================
    */

    'Sábado 16:00 - pedido 24h' => [
        'agora' =>
            new DateTimeImmutable(
                '2026-08-29 16:00:00',
                new DateTimeZone(
                    'America/Fortaleza'
                )
            ),

        'itens' =>
            $pedido24h,
    ],


    /*
    =================================
    SÁBADO 18:00
    =================================
    */

    'Sábado 18:00 - pedido 24h' => [
        'agora' =>
            new DateTimeImmutable(
                '2026-08-29 18:00:00',
                new DateTimeZone(
                    'America/Fortaleza'
                )
            ),

        'itens' =>
            $pedido24h,
    ],


    /*
    =================================
    SEXTA 16:00 - FRITO
    =================================
    */

    'Sexta 16:00 - frito 1h30' => [
        'agora' =>
            new DateTimeImmutable(
                '2026-08-28 16:00:00',
                new DateTimeZone(
                    'America/Fortaleza'
                )
            ),

        'itens' =>
            $pedidoFrito,
    ],

];


foreach (
    $testes as $nome => $teste
) {

    echo PHP_EOL;

    echo '========================================';
    echo PHP_EOL;

    echo $nome;
    echo PHP_EOL;

    echo '========================================';
    echo PHP_EOL;


    $resultado =
        $service->calcularPrimeiroHorario(
            $teste['itens'],
            $teste['agora']
        );


    echo 'Pedido realizado: ';

    echo $teste['agora']
        ->format(
            'd/m/Y H:i'
        );

    echo PHP_EOL;


    echo 'Primeiro horário possível: ';

    echo $resultado[
        'primeiro_horario_formatado'
    ];

    echo PHP_EOL;


    echo 'Tempo de produção: ';

    echo $resultado[
        'tempo_producao'
    ];

    echo PHP_EOL;


    echo 'Exige 24h: ';

    echo $resultado[
        'exige_24h'
    ]
        ? 'SIM'
        : 'NÃO';

    echo PHP_EOL;
}