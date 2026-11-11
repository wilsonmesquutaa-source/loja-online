<?php

/*
|--------------------------------------------------------------------------
| AVISO DE PRÉ-VISUALIZAÇÃO
|--------------------------------------------------------------------------
*/

if ($temaLoja['preview']):
?>
    <aside
        class="tema-preview-aviso"
        role="status"
    >
        Pré-visualização:
        <?= $temaEsc($temaLoja['tema']['nome']) ?>
        · Somente você vê este tema.

        <a
            href="<?= BASE_URL ?>/admin/temas/editar/<?= (int) $temaLoja['tema']['id'] ?>"
        >
            Voltar ao editor
        </a>
    </aside>
<?php
endif;
?>


<?php

/*
|--------------------------------------------------------------------------
| SELO DO TEMA
|--------------------------------------------------------------------------
*/

if (trim($temaLoja['config']['selo'] ?? '') !== ''):
?>
    <div class="tema-selo">
        <?= $temaEsc($temaLoja['config']['selo']) ?>
    </div>
<?php
endif;
?>


<?php

/*
|--------------------------------------------------------------------------
| EFEITOS DO HEADER
|--------------------------------------------------------------------------
|
| Estes efeitos pertencem ao topo da página e não à viewport.
| Assim, acompanham o header e desaparecem naturalmente ao rolar a página.
|
*/

$efeitosHeaderAtivos = [];

foreach ($temaLoja['componentes'] as $k => $c) {
    if (
        !empty($c['ativo'])
        && in_array(
            $k,
            \App\Controllers\Admin\TemaController::EFEITOS_HEADER,
            true
        )
    ) {
        $efeitosHeaderAtivos[$k] = $c;
    }
}

?>


<?php if ($efeitosHeaderAtivos): ?>

    <div
        class="tema-decoracoes-header"
        aria-hidden="true"
    >

        <?php foreach ($efeitosHeaderAtivos as $k => $c): ?>

            <div
                class="tema-efeito tema-<?= $temaEsc($k) ?>"
                data-tema-efeito="<?= $temaEsc($k) ?>"
            >

                <?php if ($k === 'header_luzes'): ?>

                    <div class="tema-fio-natal"></div>

                    <?php for ($i = 0; $i < 24; $i++): ?>
                        <span style="--n:<?= $i ?>"></span>
                    <?php endfor; ?>

                <?php elseif ($k === 'header_guirlanda'): ?>

                    <div class="tema-guirlanda-folhagem">

                        <?php for ($i = 0; $i < 28; $i++): ?>
                            <span
                                class="tema-guirlanda-ramo"
                                style="--n:<?= $i ?>"
                            ></span>
                        <?php endfor; ?>

                        <i class="tema-guirlanda-bola bola-1"></i>
                        <i class="tema-guirlanda-bola bola-2"></i>
                        <i class="tema-guirlanda-bola bola-3"></i>
                        <i class="tema-guirlanda-bola bola-4"></i>
                        <i class="tema-guirlanda-bola bola-5"></i>

                        <div class="tema-guirlanda-laco">
                            <i></i>
                            <i></i>
                            <b></b>
                        </div>

                    </div>

                <?php elseif ($k === 'header_bandeirinhas'): ?>

                    <div class="tema-fio-bandeirinhas"></div>

                    <?php for ($i = 0; $i < 18; $i++): ?>
                        <span style="--n:<?= $i ?>"></span>
                    <?php endfor; ?>

                <?php elseif ($k === 'header_coracoes'): ?>

                    <div class="tema-fio-coracoes"></div>

                    <?php for ($i = 0; $i < 16; $i++): ?>
                        <span style="--n:<?= $i ?>">♥</span>
                    <?php endfor; ?>

                <?php elseif ($k === 'header_flores'): ?>

                    <div class="tema-folhagem-flores"></div>

                    <?php for ($i = 0; $i < 18; $i++): ?>
                        <span style="--n:<?= $i ?>">✿</span>
                    <?php endfor; ?>

                <?php elseif ($k === 'header_serpentina'): ?>

                    <?php for ($i = 0; $i < 14; $i++): ?>
                        <span style="--n:<?= $i ?>"></span>
                    <?php endfor; ?>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>


<?php

/*
|--------------------------------------------------------------------------
| CAMADA GLOBAL DE DECORAÇÕES
|--------------------------------------------------------------------------
|
| Apenas os efeitos globais permanecem fixos na viewport.
|
*/

?>

<div
    class="tema-decoracoes"
    aria-hidden="true"
>

    <?php

    /*
    |--------------------------------------------------------------------------
    | IMAGENS DECORATIVAS
    |--------------------------------------------------------------------------
    */

    foreach (
        [
            'decoracao_esquerda',
            'decoracao_direita',
            'decoracao_superior',
            'mascote',
            'outro'
        ] as $tipo
    ):

        if (!isset($temaLoja['assets'][$tipo])) {
            continue;
        }

        $a = $temaLoja['assets'][$tipo];

    ?>

        <div class="tema-decor tema-<?= $tipo ?>">

            <?php if ($tipo === 'mascote' && str_ends_with($a['url_arquivo'], '.webm')): ?>
                <video src="<?= BASE_URL . $temaEsc($a['url_arquivo']) ?>" style="<?= $temaEsc($temaAssetStyle($a)) ?>" autoplay loop muted playsinline preload="metadata" data-tema-decor></video>
            <?php else: ?>
            <img
                alt=""
                src="<?= BASE_URL . $temaEsc($a['url_arquivo']) ?>"
                style="<?= $temaEsc($temaAssetStyle($a)) ?>"
                loading="lazy"
                data-tema-decor
            >
            <?php endif; ?>

        </div>

    <?php
    endforeach;
    ?>


    <?php

    /*
    |--------------------------------------------------------------------------
    | EFEITOS GLOBAIS
    |--------------------------------------------------------------------------
    */

    foreach ($temaLoja['componentes'] as $k => $c):

        if (
            empty($c['ativo'])
            || !in_array(
                $k,
                \App\Controllers\Admin\TemaController::EFEITOS_GLOBAIS,
                true
            )
        ) {
            continue;
        }

    ?>

        <div
            class="tema-efeito tema-<?= $temaEsc($k) ?>"
            data-tema-efeito="<?= $temaEsc($k) ?>"
        >

            <?php
            $neve = $k === 'efeito_neve'
                ? [
                    ['x' => 3, 'size' => 11, 'duration' => 12, 'delay' => -2, 'opacity' => .45, 'drift' => '18px', 'rotation' => '120deg', 'symbol' => '❄'],
                    ['x' => 8, 'size' => 16, 'duration' => 17, 'delay' => -11, 'opacity' => .7, 'drift' => '-24px', 'rotation' => '220deg', 'symbol' => '❅'],
                    ['x' => 14, 'size' => 9, 'duration' => 10, 'delay' => -5, 'opacity' => .5, 'drift' => '12px', 'rotation' => '80deg', 'symbol' => '❆'],
                    ['x' => 21, 'size' => 14, 'duration' => 15, 'delay' => -8, 'opacity' => .6, 'drift' => '-16px', 'rotation' => '180deg', 'symbol' => '❄'],
                    ['x' => 27, 'size' => 12, 'duration' => 19, 'delay' => -14, 'opacity' => .55, 'drift' => '26px', 'rotation' => '300deg', 'symbol' => '❅'],
                    ['x' => 34, 'size' => 18, 'duration' => 14, 'delay' => -4, 'opacity' => .75, 'drift' => '-20px', 'rotation' => '160deg', 'symbol' => '❆'],
                    ['x' => 40, 'size' => 10, 'duration' => 11, 'delay' => -9, 'opacity' => .48, 'drift' => '15px', 'rotation' => '260deg', 'symbol' => '❄'],
                    ['x' => 47, 'size' => 15, 'duration' => 18, 'delay' => -16, 'opacity' => .65, 'drift' => '-28px', 'rotation' => '200deg', 'symbol' => '❅'],
                    ['x' => 54, 'size' => 11, 'duration' => 13, 'delay' => -6, 'opacity' => .5, 'drift' => '22px', 'rotation' => '340deg', 'symbol' => '❆'],
                    ['x' => 61, 'size' => 17, 'duration' => 16, 'delay' => -12, 'opacity' => .72, 'drift' => '-14px', 'rotation' => '140deg', 'symbol' => '❄'],
                    ['x' => 68, 'size' => 9, 'duration' => 20, 'delay' => -18, 'opacity' => .45, 'drift' => '30px', 'rotation' => '240deg', 'symbol' => '❅'],
                    ['x' => 75, 'size' => 13, 'duration' => 12, 'delay' => -3, 'opacity' => .58, 'drift' => '-18px', 'rotation' => '100deg', 'symbol' => '❆'],
                    ['x' => 82, 'size' => 16, 'duration' => 15, 'delay' => -10, 'opacity' => .68, 'drift' => '20px', 'rotation' => '280deg', 'symbol' => '❄'],
                    ['x' => 89, 'size' => 10, 'duration' => 18, 'delay' => -15, 'opacity' => .5, 'drift' => '-26px', 'rotation' => '190deg', 'symbol' => '❅'],
                    ['x' => 96, 'size' => 14, 'duration' => 13, 'delay' => -7, 'opacity' => .62, 'drift' => '16px', 'rotation' => '320deg', 'symbol' => '❆'],
                ]
                : [];
            for ($i = count($neve); $i < 28; $i++) {
                $neve[] = [
                    'x' => ($i * 37) % 97 + 1,
                    'size' => 9 + (($i * 5) % 10),
                    'duration' => 11 + (($i * 7) % 10),
                    'delay' => -($i * 3 % 19),
                    'opacity' => .45 + (($i * 3) % 30) / 100,
                    'drift' => (($i % 2 ? -1 : 1) * (12 + (($i * 9) % 20))) . 'px',
                    'rotation' => (($i * 47) % 340 + 20) . 'deg',
                    'symbol' => ['❄', '❅', '❆'][$i % 3],
                ];
            }
            $fogos = [
                ['x'=>10,'y'=>58,'size'=>.72,'duration'=>9.6,'delay'=>-2.4,'palette'=>['#ff3b30','#ff9500','#ffd60a'],'kind'=>'classico','particles'=>16],
                ['x'=>23,'y'=>34,'size'=>1.08,'duration'=>11.8,'delay'=>-8.6,'palette'=>['#0a84ff','#64d2ff','#ffffff'],'kind'=>'longo','particles'=>20],
                ['x'=>37,'y'=>65,'size'=>.62,'duration'=>8.7,'delay'=>-5.1,'palette'=>['#30d158','#ffd60a','#ffffff'],'kind'=>'brilho','particles'=>14],
                ['x'=>51,'y'=>27,'size'=>.9,'duration'=>10.4,'delay'=>-1.7,'palette'=>['#bf5af2','#ff2d92','#ffffff'],'kind'=>'classico','particles'=>18],
                ['x'=>64,'y'=>52,'size'=>.76,'duration'=>12.6,'delay'=>-10.2,'palette'=>['#f8df9a','#fff4cf','#ffffff'],'kind'=>'longo','particles'=>20],
                ['x'=>77,'y'=>39,'size'=>1.16,'duration'=>13.4,'delay'=>-6.8,'palette'=>['#ff9f0a','#f8df9a','#ffffff'],'kind'=>'classico','particles'=>18],
                ['x'=>88,'y'=>69,'size'=>.58,'duration'=>9.1,'delay'=>-3.3,'palette'=>['#5e5ce6','#64d2ff','#bf5af2'],'kind'=>'brilho','particles'=>14],
                ['x'=>18,'y'=>22,'size'=>.67,'duration'=>11.1,'delay'=>-11.4,'palette'=>['#ff375f','#ff9f0a','#ffffff'],'kind'=>'longo','particles'=>18],
            ];
            $baloes = [
                ['x'=>5,'size'=>.78,'duration'=>23,'delay'=>-4,'sway'=>18,'rotate'=>-4,'color'=>'#ef4444','group'=>''],
                ['x'=>13,'size'=>1.08,'duration'=>29,'delay'=>-17,'sway'=>24,'rotate'=>5,'color'=>'#3b82f6','group'=>'dupla'],
                ['x'=>20,'size'=>.9,'duration'=>25,'delay'=>-11,'sway'=>15,'rotate'=>-7,'color'=>'#facc15','group'=>'dupla'],
                ['x'=>29,'size'=>1.3,'duration'=>34,'delay'=>-25,'sway'=>27,'rotate'=>4,'color'=>'#22c55e','group'=>''],
                ['x'=>38,'size'=>.72,'duration'=>21,'delay'=>-7,'sway'=>13,'rotate'=>-5,'color'=>'#f97316','group'=>''],
                ['x'=>45,'size'=>1.02,'duration'=>27,'delay'=>-20,'sway'=>22,'rotate'=>6,'color'=>'#ec4899','group'=>'trio'],
                ['x'=>51,'size'=>.86,'duration'=>24,'delay'=>-15,'sway'=>17,'rotate'=>-3,'color'=>'#a855f7','group'=>'trio'],
                ['x'=>57,'size'=>1.16,'duration'=>31,'delay'=>-28,'sway'=>25,'rotate'=>5,'color'=>'#60a5fa','group'=>'trio'],
                ['x'=>65,'size'=>.68,'duration'=>20,'delay'=>-2,'sway'=>12,'rotate'=>-6,'color'=>'#f43f5e','group'=>''],
                ['x'=>71,'size'=>1.24,'duration'=>33,'delay'=>-22,'sway'=>29,'rotate'=>7,'color'=>'#84cc16','group'=>''],
                ['x'=>78,'size'=>.82,'duration'=>26,'delay'=>-13,'sway'=>19,'rotate'=>-4,'color'=>'#fb923c','group'=>''],
                ['x'=>86,'size'=>1.04,'duration'=>30,'delay'=>-8,'sway'=>23,'rotate'=>5,'color'=>'#c084fc','group'=>''],
                ['x'=>93,'size'=>.74,'duration'=>22,'delay'=>-18,'sway'=>14,'rotate'=>-7,'color'=>'#38bdf8','group'=>''],
                ['x'=>26,'size'=>.96,'duration'=>28,'delay'=>-31,'sway'=>21,'rotate'=>4,'color'=>'#f472b6','group'=>''],
                ['x'=>62,'size'=>.76,'duration'=>24,'delay'=>-35,'sway'=>16,'rotate'=>-5,'color'=>'#facc15','group'=>''],
                ['x'=>82,'size'=>.9,'duration'=>27,'delay'=>-39,'sway'=>20,'rotate'=>6,'color'=>'#ef4444','group'=>''],
            ];
            $quantidade = $k === 'efeito_neve' ? count($neve) : ($k === 'efeito_fogos' ? count($fogos) : 12);
            if ($k === 'efeito_fogos'):
                foreach ($fogos as $fogo):
                    $anguloBase = ($fogo['x'] * 13) % 17;
            ?>
                    <div
                        class="tema-fogo tema-fogo-<?= $fogo['kind'] ?>"
                        style="--firework-x:<?= $fogo['x'] ?>%;--firework-y:<?= $fogo['y'] ?>%;--firework-size:<?= $fogo['size'] ?>;--firework-duration:<?= $fogo['duration'] ?>s;--firework-delay:<?= $fogo['delay'] ?>s;--firework-color:<?= $fogo['palette'][0] ?>;--firework-angle:<?= $anguloBase ?>deg"
                    >
                        <i class="tema-fogo-foguete"></i>
                        <i class="tema-fogo-centro"></i>
                        <?php for ($p = 0; $p < $fogo['particles']; $p++):
                            $angulo = ($p * (360 / $fogo['particles'])) + (($p % 3) * 2.5) + $anguloBase;
                            $distancia = 38 + (($p * 17 + $fogo['x']) % 28);
                            $duracaoParticula = .82 + (($p * 13) % 28) / 100;
                            $corParticula = $fogo['palette'][$p % count($fogo['palette'])];
                        ?>
                            <i
                                class="tema-fogo-particula"
                                style="--firework-particle-angle:<?= round($angulo, 2) ?>deg;--firework-particle-distance:<?= $distancia ?>px;--firework-particle-duration:<?= $duracaoParticula ?>s;--firework-particle-color:<?= $corParticula ?>"
                            ></i>
                        <?php endfor; ?>
                    </div>
                <?php endforeach;
            endif;
            if ($k === 'efeito_baloes'):
                foreach ($baloes as $balao):
            ?>
                    <div
                        class="tema-balao tema-balao-<?= $balao['group'] ?: 'individual' ?>"
                        style="--balloon-x:<?= $balao['x'] ?>%;--balloon-size:<?= $balao['size'] ?>;--balloon-duration:<?= $balao['duration'] ?>s;--balloon-delay:<?= $balao['delay'] ?>s;--balloon-sway:<?= $balao['sway'] ?>px;--balloon-rotate:<?= $balao['rotate'] ?>deg;--balloon-color:<?= $balao['color'] ?>"
                    >
                        <span class="tema-balao-corpo"></span>
                        <span class="tema-balao-no"></span>
                        <span class="tema-balao-fio"></span>
                    </div>
                <?php endforeach;
                ?>
                </div>
                <?php
                continue;
            endif;
            for ($i = 0; $i < $quantidade; $i++):
                $floco = $neve[$i] ?? null;
                if ($k === 'efeito_fogos') continue;
            ?>

                <span
                    style="<?= $floco ? sprintf('--snow-x:%d%%;--snow-size:%dpx;--snow-duration:%ds;--snow-delay:%ds;--snow-opacity:%.2f;--snow-drift:%s;--snow-rotation:%s;', $floco['x'], $floco['size'], $floco['duration'], $floco['delay'], $floco['opacity'], $floco['drift'], $floco['rotation']) : '--n:' . $i ?>"
                >
                    <?=
                        match ($k) {
                            'efeito_neve' => $floco['symbol'],
                            default       => ''
                        }
                    ?>
                </span>

            <?php endfor; ?>

        </div>

    <?php
    endforeach;
    ?>

</div>
