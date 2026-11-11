<?php


declare(strict_types=1);


use App\Controllers\Admin\TemaController;

use App\Services\TemaCalendarioService;


/**

 * Variáveis fornecidas por TemaController::tela().

 *

 * @var string $secao

 * @var array $temas

 * @var array|null $tema

 * @var array $paleta

 * @var array $assets

 * @var array $componentes

 * @var array $config

 * @var array $agendas

 * @var array $biblioteca

 * @var array $historico

 * @var array $auditoria

 * @var array $atual

 */


$tituloPagina = 'Aparência da loja';

$usarBootstrap = false;

$cssAdmin = ['modulos.css'];

$jsAdmin = ['modulos.js'];


$e = static fn ($v): string =>

    htmlspecialchars(

        (string) $v,

        ENT_QUOTES,

        'UTF-8'

    );


$token = \App\Helpers\Csrf::gerar();


$start = static function (

    string $acao,

    int $id = 0,

    bool $upload = false

) use ($e, $token): void {

    echo '<form method="post" class="mod-form" action="'

        . BASE_URL

        . '/admin/temas/acao"'

        . ($upload ? ' enctype="multipart/form-data"' : '')

        . '>';


    echo '<input type="hidden" name="_token" value="'

        . $e($token)

        . '">';


    echo '<input type="hidden" name="acao" value="'

        . $e($acao)

        . '">';


    echo '<input type="hidden" name="tema_id" value="'

        . (int) $id

        . '">';

};


$tipos = [

    'logo_desktop' => 'Logo desktop',

    'logo_mobile' => 'Logo mobile',

    'mascote' => 'Mascote',

    'favicon' => 'Favicon',

    'header_overlay' => 'Decoração do header',

    'header_background' => 'Fundo do header',

    'banner_home' => 'Banner da home',

    'background_site' => 'Fundo das páginas',
    'home_pendente' => 'Enfeite abaixo do header',
    'home_moldura' => 'Moldura do banner principal',
    'home_divisor' => 'Enfeite acima dos Destaques',
    'home_vantagens' => 'Enfeite do título Vantagens',
    'home_atendimento' => 'Enfeite do canto Atendimento',


    'decoracao_esquerda' => 'Decoração esquerda',

    'decoracao_direita' => 'Decoração direita',

    'decoracao_superior' => 'Decoração superior',

    'outro' => 'Ilustração complementar',

];

$tiposCadastro = [
    'logo_desktop' => 'Logo desktop',
    'logo_mobile' => 'Logo mobile',
    'mascote' => 'Mascote',
    'favicon' => 'Favicon',
    'header_overlay' => 'Decoração do header',
    'header_background' => 'Fundo do header',
    'banner_home' => 'Banner da home',
    'background_site' => 'Fundo das páginas',
    'home_pendente' => 'Enfeite abaixo do header',
    'home_moldura' => 'Moldura do banner principal',
    'home_divisor' => 'Enfeite acima dos Destaques',
    'home_vantagens' => 'Enfeite do título Vantagens',
    'home_atendimento' => 'Enfeite do canto Atendimento',

];

$efeitos = ['efeito_neve'=>'Neve', 'efeito_confete'=>'Confete', 'efeito_fogos'=>'Fogos de artifício', 'efeito_baloes'=>'Balões coloridos'];



require APP_ROOT . '/views/layouts/admin/header.php';


?>


<main class="admin-container modulos">


    <header class="mod-heading">


        <div><span class="mod-eyebrow">Identidade e campanhas</span>


            <h1>Aparência da loja</h1>


            <p>Prepare, teste e publique cada temporada preservando a identidade original.</p>


        </div><a class="mod-button" href="<?= BASE_URL ?>/admin/temas/editar/0">Criar tema</a>


    </header>


    <nav class="mod-nav" aria-label="Aparência">

    <?php

    $navegacao = [

        'temas' => ['Tema atual e temas', ''],

        'calendario' => ['Calendário sazonal', '/calendario'],

        'biblioteca' => ['Biblioteca visual', '/biblioteca'],

        'historico' => ['Histórico', '/historico'],

    ];

    ?>


    <?php foreach ($navegacao as $s => [$l, $path]): ?>

        <a

            <?= $s === $secao ? 'aria-current="page"' : '' ?>

            href="<?= BASE_URL ?>/admin/temas<?= $path ?>"

        >

            <?= $l ?>

        </a>

    <?php endforeach; ?>


    <a href="<?= BASE_URL ?>/admin/banners-home">

        Banners da loja

    </a>

</nav>


    <?php if (isset($_SESSION['modulos_aviso'])): ?>

    <p role="status" class="mod-notice">

        <?= $e($_SESSION['modulos_aviso']) ?>

    </p>


    <?php unset($_SESSION['modulos_aviso']); ?>

<?php endif; ?>


    <?php if ($secao === 'temas'): ?>

        <p class="mod-links"><a href="<?= BASE_URL ?>/admin/temas">Meus temas</a><a href="<?= BASE_URL ?>/admin/temas?excluidos=1">Temas excluídos</a></p>


        <section class="mod-panel mod-current">


            <div><span class="mod-eyebrow">Na loja agora</span>


                <h2><?= $e($atual['tema']['nome']) ?></h2>


                <p>Prioridade: ativação manual → agendamento vigente → tema padrão. Em empate: prioridade do agendamento, prioridade do tema e menor ID.</p>


            </div>


            <div class="mod-actions"><?php $start('padrao'); ?><button>Voltar ao padrão</button></form><?php if ($atual['tema']['ativo_manual'] ?? false): $start('desativar', $atual['tema']['id']); ?><button class="mod-secondary">Retomar agendamentos</button></form><?php endif; ?></div>


        </section>


        <div class="mod-theme-grid"><?php foreach ($temas as $t): ?><article class="mod-panel"><span class="mod-badge"><?= $e($t['status']) ?></span>


                    <h2><?= $e($t['nome']) ?></h2>


                    <p><?= $e($t['descricao'] ?: 'Identidade sazonal com os recursos padrão como base.') ?></p>


                    <p class="mod-muted">Prioridade <?= $t['prioridade'] ?> · <?= $e($t['tipo']) ?></p>


                    <div class="mod-actions">

                        <a class="mod-button mod-secondary" href="<?= BASE_URL ?>/admin/temas/editar/<?= $t['id'] ?>"><?= $t['status'] === 'rascunho' ? 'Editar' : 'Abrir' ?></a>

                        <?php if ($t['status'] !== 'arquivado'): ?>

                            <a href="<?= BASE_URL ?>/admin/temas/preview/<?= $t['id'] ?>" target="_blank" rel="noopener">Pré-visualizar</a>

                            <?php if ($t['tipo'] !== 'padrao'): $start('excluir', $t['id']); ?>

                                <button class="mod-secondary" data-confirm="Excluir este tema do painel e cancelar seus agendamentos?">Excluir tema</button>

                            </form><?php endif; ?>

                        <?php else: $start('reabrir', $t['id']); ?>

                            <button class="mod-secondary">Restaurar para editar</button>

                        </form><?php endif; ?>

                    </div>


                </article><?php endforeach; ?></div>


    <?php elseif ($secao === 'editar'): ?>

    <?php

    $id = (int) ($tema['id'] ?? 0);

    $bloqueado = $tema && $tema['status'] !== 'rascunho';

    ?>


        <section class="mod-panel">


            <h2><?= $e($tema['nome'] ?? 'Novo tema') ?></h2>


            <div class="mod-actions">

                <?php if ($id): ?>

                    <?php if ($tema['status'] !== 'arquivado'): ?>

                        <a class="mod-button mod-secondary" target="_blank" rel="noopener" href="<?= BASE_URL ?>/admin/temas/preview/<?= $id ?>">Ver versão salva</a>

                    <?php endif; ?>

                    <?php if ($tema['tipo'] !== 'padrao' && $bloqueado): $start('reabrir', $id); ?>

                        <button data-confirm="Reabrir este mesmo tema para edição? Ele ficará fora da loja até ser publicado novamente."><?= $tema['status'] === 'arquivado' ? 'Restaurar para editar' : 'Editar novamente' ?></button>

                    </form><?php endif; ?>

                    <?php if ($tema['status'] === 'rascunho'): $start('publicar_ativar', $id); ?>

                        <button data-confirm="Aplicar a versão salva para todos os visitantes?">Aplicar na loja</button>

                    </form>
                    <?php $start('publicar', $id); ?><button class="mod-secondary" data-confirm="Disponibilizar a versão salva para agendamentos? Um agendamento vigente poderá exibi-la.">Publicar para agendamento</button></form>
                    <?php elseif ($tema['status'] === 'publicado' && $tema['tipo'] !== 'padrao'): $start('ativar', $id); ?>

                        <button data-confirm="Ativar este tema para todos os visitantes?">Ativar agora</button>

                    </form><?php $start('desativar', $id); ?><button class="mod-secondary">Desativar manual</button></form><?php endif; ?>

                    <?php $start('duplicar', $id); ?><button class="mod-secondary">Criar outra cópia</button></form>

                    <?php if ($tema['tipo'] !== 'padrao' && $tema['status'] !== 'arquivado'): $start('excluir', $id); ?>

                        <button class="mod-secondary" data-confirm="Excluir este tema do painel? Ele sairá da loja e seus agendamentos serão cancelados.">Excluir tema</button>

                    </form><?php endif; ?>

                <?php endif; ?>

            </div>

            <?php if ($bloqueado): ?>

                <p class="mod-notice"><?= $tema['tipo'] === 'padrao'

                    ? 'O tema padrão é permanente. Crie uma cópia para personalizar.'

                    : 'Use Editar novamente para alterar este mesmo tema sem criar outra cópia. Durante a edição, a loja usa outro tema vigente ou o padrão. Depois, use Salvar e aplicar.' ?></p>

            <?php endif; ?>


            <?php $start('salvar', $id); ?><fieldset <?= $bloqueado ? 'disabled' : '' ?>>


                <div class="mod-fields"><label>Nome<input name="nome" maxlength="120" value="<?= $e($tema['nome'] ?? '') ?>" required></label><label>Identificador (letras, números e hífens)<input name="slug" pattern="[a-z0-9]+(-[a-z0-9]+)*" maxlength="140" value="<?= $e($tema['slug'] ?? '') ?>" required></label><label>Tipo<select name="tipo">


                            <option value="sazonal">Sazonal</option>


                            <option value="campanha" <?= ($tema['tipo'] ?? '') === 'campanha' ? 'selected' : '' ?>>Campanha</option>


                        </select></label><label>Prioridade<input name="prioridade" type="number" min="0" max="65535" value="<?= $e($tema['prioridade'] ?? 100) ?>"></label><label>Permitir agendamento<input type="checkbox" name="permitir_agendamento" <?= ($tema['permitir_agendamento'] ?? 1) ? 'checked' : '' ?>></label></div><label>Descrição<textarea name="descricao" maxlength="500"><?= $e($tema['descricao'] ?? '') ?></textarea></label>


                <h3>Paleta de cores</h3>


                <p class="mod-muted">Deixe vazio para herdar do tema padrão. Use cores no formato #RRGGBB.</p>


                <div class="mod-fields"><?php foreach (TemaController::CORES as $k): ?><label><?= $e(ucfirst(str_replace('_', ' ', substr($k, 4)))) ?><input name="<?= $k ?>" value="<?= $e($paleta[$k] ?? '') ?>" placeholder="#5d4037" pattern="#[0-9a-fA-F]{6}" maxlength="7"></label><?php endforeach; ?></div>


                <h3>Enfeites próprios de cada tema</h3>
                <p class="mod-muted">Na área de imagens abaixo, use “Usar como” para adicionar pingentes abaixo do header, moldura do banner, divisor de Destaques, detalhe de Vantagens e enfeite de Atendimento. Envie PNG ou WebP com fundo transparente. Cada imagem acompanha apenas este tema: aparece na prévia ou quando ele está ativo, inclusive por agendamento. Sem imagem cadastrada, o espaço fica sem enfeite.</p>

                <h3>Efeitos adicionais e acabamento</h3>

                <div class="mod-fields"><?php foreach ($efeitos as $k => $l): ?><label><?= $l ?><input name="efeitos[<?= $k ?>]" type="checkbox" <?= ($componentes[$k]['ativo'] ?? 0) ? 'checked' : '' ?>></label><?php endforeach; ?><label>Selo sazonal<input name="selo" maxlength="80" value="<?= $e($config['selo'] ?? '') ?>" placeholder="São João no Cantim"></label><label>Borda do header<select name="borda">


                            <option value="">Padrão</option>


                            <option value="dourada" <?= ($config['borda'] ?? '') === 'dourada' ? 'selected' : '' ?>>Dourada</option>


                        </select></label><label>Sombra<select name="sombra">


                            <option value="">Padrão</option>


                            <option value="suave" <?= ($config['sombra'] ?? '') === 'suave' ? 'selected' : '' ?>>Suave</option>


                        </select></label></div><div class="mod-actions">

<button class="mod-secondary" name="apos_salvar" value="rascunho">Salvar rascunho</button>

<button class="mod-secondary" name="apos_salvar" value="preview">Salvar e pré-visualizar</button>

<button name="apos_salvar" value="aplicar" data-confirm="Salvar e aplicar este tema para todos os visitantes?">Salvar e aplicar</button>

</div>


            </fieldset>


            </form>


        </section>


        <?php if ($id): ?>


            <section class="mod-panel">


                <h2>Imagens do tema</h2>

                <p>Logos, mascotes, fundos e decorações são independentes dos arquivos originais. Para “Decoração do header”, prefira PNG ou WebP com fundo transparente; imagens estáticas são convertidas para WebP. Para o mascote, WebP animado é preservado. Tamanho recomendado para novos arquivos: <strong>1920 × 60 px</strong> (proporção 32:1). Arquivos antigos, como 1920 × 96 px, continuam compatíveis.</p>


                <div class="mod-theme-grid"><?php foreach ($assets as $a): if (!$a['ativo']) continue; ?><article><?php if (str_ends_with($a['url_arquivo'], '.webm')): ?><video class="mod-asset" src="<?= BASE_URL . $e($a['url_arquivo']) ?>" muted loop autoplay playsinline preload="metadata"></video><?php else: ?><img class="mod-asset" src="<?= BASE_URL . $e($a['url_arquivo']) ?>" alt="<?= $e($a['texto_alternativo']) ?>" loading="lazy"><?php endif; ?>


                            <h3><?= $e($tipos[$a['tipo']]) ?></h3>
                            <?php if (!$bloqueado): ?>
                                <div class="mod-asset-actions">
                                    <button type="button" data-theme-edit="<?= $e(json_encode([
                                        'id' => (int)$a['id'], 'tipo' => $a['tipo'],
                                        'rotulo' => $tipos[$a['tipo']] ?? $a['tipo'],
                                        'url' => BASE_URL . $a['url_arquivo'],
                                        'escala' => $a['escala'], 'posicao_x' => $a['posicao_x'],
                                        'posicao_y' => $a['posicao_y'], 'opacidade' => $a['opacidade'],
                                        'texto_alternativo' => $a['texto_alternativo'],
                                    ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>">Editar</button>
                            <?php $start('remover_asset', $id); ?><input type="hidden" name="asset_id" value="<?= $a['id'] ?>"><button class="mod-secondary">Retirar do tema</button></form>
                                </div><?php endif; ?>


                        </article><?php endforeach; ?></div>


                <?php if (!$bloqueado): $start('asset', $id, true); ?>
                    <h3 data-theme-editor-title tabindex="-1">Adicionar imagem ao tema</h3>
                    <p class="mod-muted" data-theme-mascot-help hidden>Mascote: aceita vídeo WebM de até 30 MB ou WebP animado de até 5 MB. Para aparecer sem fundo, o arquivo precisa ter transparência. WebM será reproduzido sem som e em repetição; MP4 não é aceito.</p>
                    <input type="hidden" name="asset_id" value="" data-theme-edit-id>
                    <p data-theme-edit-notice hidden>Ajuste a imagem já vinculada ao tema. Para cadastrar outra imagem, cancele a edição.</p>
                    <div class="mod-fields"><label>Usar como<select name="tipo_asset" data-theme-asset-type><?php foreach ($tiposCadastro as $k => $l): ?><option value="<?= $k ?>"><?= $l ?></option><?php endforeach; ?></select></label><label>Arquivo da biblioteca<select name="biblioteca_id">


                                <option value="">Enviar nova imagem</option><?php foreach ($biblioteca as $b): ?><option value="<?= $b['id'] ?>" data-url="<?= $e(BASE_URL . $b['url_arquivo']) ?>"><?= $e($b['nome']) ?></option><?php endforeach; ?>


                            </select></label><label><span data-theme-upload-label>Nova imagem (até 5 MB)</span><input name="arquivo" type="file" accept="image/png,image/jpeg,image/webp,image/avif" data-theme-upload></label><label>Nome na biblioteca<input name="nome_arquivo" maxlength="150"></label><label>Descrição acessível<input name="texto_alternativo" maxlength="255"></label></div>


                    <div class="mod-image-editor">


                        <div class="mod-image-preview" data-preview-type="logo_desktop"><img data-theme-preview alt="Prévia da imagem selecionada" hidden><video data-theme-video-preview autoplay loop muted playsinline hidden></video></div>


                        <div><label>Tamanho <output data-scale-output>100%</output><input name="escala" type="range" min="0.1" max="2" step="0.01" value="1" data-theme-scale></label><label>Posição horizontal<input name="posicao_x" type="range" min="0" max="100" step="0.01" value="50" data-theme-x></label><label>Posição vertical<input name="posicao_y" type="range" min="0" max="100" step="0.01" value="50" data-theme-y></label><label>Opacidade<input name="opacidade" type="range" min="0" max="1" step="0.01" value="1" data-theme-opacity></label></div>


                    </div><div class="mod-actions"><button data-theme-save>Adicionar imagem ao tema</button><button type="reset" class="mod-secondary" data-theme-edit-cancel hidden>Cancelar edição</button></div></form><?php endif; ?>


            </section><?php endif; ?>


    <?php elseif ($secao === 'calendario'): ?>


        <section class="mod-panel">


            <h2>Programar temporada</h2>


            <p>Intervalos anuais podem atravessar dezembro e janeiro. Regras móveis são calculadas a cada ano, no horário de Fortaleza.</p><?php $start('agendar'); ?><div class="mod-fields"><label>Tema<select name="tema_id" required><?php foreach ($temas as $t): if ($t['status'] !== 'publicado' || $t['tipo'] === 'padrao') continue; ?><option value="<?= $t['id'] ?>"><?= $e($t['nome']) ?></option><?php endforeach; ?></select></label><label>Regra<select name="tipo_regra" data-schedule-type>


                        <option value="intervalo_fixo">Intervalo com data e hora</option>


                        <option value="anual_fixo">Todo ano (dia e mês)</option>


                        <option value="regra_calendario">Data móvel</option>


                    </select></label><label>Prioridade<input name="prioridade" type="number" min="0" max="65535" value="100"></label></div>


            <div data-schedule="intervalo_fixo" class="mod-fields"><label>Começa<input name="inicio_datetime" type="datetime-local"></label><label>Termina<input name="fim_datetime" type="datetime-local"></label></div>


            <div data-schedule="anual_fixo" class="mod-fields" hidden><?php foreach (['inicio' => 'Início', 'fim' => 'Fim'] as $k => $l): ?><label><?= $l ?>: dia<input name="dia_<?= $k ?>" type="number" min="1" max="31"></label><label><?= $l ?>: mês<input name="mes_<?= $k ?>" type="number" min="1" max="12"></label><?php endforeach; ?></div>


            <div data-schedule="regra_calendario" class="mod-fields" hidden><label>Data<select name="regra_codigo"><?php foreach (['easter' => 'Páscoa', 'carnival' => 'Carnaval', 'mothers_day_br' => 'Dia das Mães', 'fathers_day_br' => 'Dia dos Pais', 'black_friday' => 'Black Friday', 'cyber_monday' => 'Cyber Monday'] as $k => $l): ?><option value="<?= $k ?>"><?= $l ?> · <?= TemaCalendarioService::data($k, (int)date('Y'))->format('d/m/Y') ?></option><?php endforeach; ?></select></label><label>Dias antes<input name="dias_antes" type="number" min="0" max="60" value="14"></label><label>Dias depois<input name="dias_depois" type="number" min="0" max="60" value="0"></label></div><button>Salvar agendamento</button></form>


        </section>


        <section class="mod-panel">


            <h2>Agendamentos</h2><?php foreach ($agendas as $a): ?><article class="mod-record">


                    <h3><?= $e($a['nome']) ?> · <?= $a['ativo'] ? 'Ativo' : 'Cancelado' ?></h3>


                    <p><?= $e(match ($a['tipo_regra']) {


                                            'intervalo_fixo' => $a['inicio_datetime'] . ' até ' . $a['fim_datetime'],


                                            'anual_fixo' => sprintf('%02d/%02d até %02d/%02d, todo ano', $a['dia_inicio'], $a['mes_inicio'], $a['dia_fim'], $a['mes_fim']),


                                            default => $a['regra_codigo'] . ' · ' . $a['dias_antes'] . ' dias antes / ' . $a['dias_depois'] . ' depois'


                                        }) ?> · prioridade <?= $a['prioridade'] ?></p><?php if ($a['ativo']): $start('cancelar_agenda', $a['tema_id']); ?><input type="hidden" name="agenda_id" value="<?= $a['id'] ?>"><button class="mod-secondary">Cancelar agendamento</button></form><?php endif; ?>


                </article><?php endforeach; ?>


        </section>


    <?php elseif ($secao === 'biblioteca'): ?>


        <section class="mod-panel">


            <h2>Biblioteca visual</h2><?php $start('upload', 0, true); ?><div class="mod-fields"><label>Nome<input name="nome_arquivo" maxlength="150" required></label><label>Imagem (PNG, JPG, WebP ou AVIF; até 5 MB)<input name="arquivo" type="file" accept="image/png,image/jpeg,image/webp,image/avif" required></label></div><button>Adicionar à biblioteca</button></form>


        </section>

        <?php $start('excluir_biblioteca_lote', 0); ?>
            <div class="mod-library-toolbar">
                <label class="mod-library-select-all"><input type="checkbox" data-library-select-all> Selecionar todas</label>
                <span class="mod-muted" data-library-selection-count>0 selecionadas</span>
                <button type="submit" class="mod-secondary" data-library-bulk-delete disabled>Excluir selecionadas</button>
            </div>
            <div data-library-bulk-inputs></div>
        </form>

        <div class="mod-theme-grid"><?php foreach ($biblioteca as $b): ?><article class="mod-panel mod-library-card">
                    <label class="mod-library-check"><input type="checkbox" value="<?= (int)$b['id'] ?>" data-library-select aria-label="Selecionar <?= $e($b['nome']) ?>"></label>
                    <?php if (str_ends_with($b['url_arquivo'], '.webm')): ?><video class="mod-asset" src="<?= BASE_URL . $e($b['url_arquivo']) ?>" muted loop autoplay playsinline preload="metadata"></video><?php else: ?><img class="mod-asset" src="<?= BASE_URL . $e($b['url_arquivo']) ?>" alt="<?= $e($b['nome']) ?>" loading="lazy"><?php endif; ?>


                    <h3><?= $e($b['nome']) ?></h3>


                    <p><?= $b['largura'] ?> × <?= $b['altura'] ?> · <?= number_format($b['tamanho_bytes'] / 1024, 1) ?> KB</p>
                    <?php if ($b['em_uso']): ?>
                        <span class="mod-badge mod-library-used">Em uso</span>
                        <div class="mod-library-usages">
                            <?php foreach ($b['usos'] as $uso): ?>
                                <div><strong><?= $e($uso['tema']) ?></strong><span><?= $e($uso['tipo']) ?></span></div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <span class="mod-badge mod-library-free">Não utilizada</span>
                    <?php endif; ?>

                    <?php $start('excluir_biblioteca', 0); ?>
                        <input type="hidden" name="biblioteca_id" value="<?= (int)$b['id'] ?>">
                        <button
                            type="submit"
                            class="mod-secondary"
                            data-library-delete
                        >Excluir</button>
                    </form>

                </article><?php endforeach; ?></div>

        <dialog class="mod-confirm-dialog" data-library-confirm>
            <form method="dialog">
                <h2>Excluir imagem</h2>
                <p data-library-confirm-question>Excluir esta imagem da biblioteca?</p>
                <p class="mod-muted">Esta ação não poderá ser desfeita.</p>
                <div class="mod-actions">
                    <button type="submit" class="mod-secondary">Cancelar</button>
                    <button type="button" data-library-confirm-submit>Excluir imagem</button>
                </div>
            </form>
        </dialog>

    <?php elseif ($secao === 'historico'): ?>


        <section class="mod-panel">


            <h2>Publicações e ativações</h2>


            <div class="mod-table-wrap">


                <table>


                    <thead>


                        <tr>


                            <th>Data</th>


                            <th>Tema</th>


                            <th>Ação</th>


                            <th>Origem</th>


                            <th>Administrador</th>


                            <th>Detalhes</th>


                        </tr>


                    </thead>


                    <tbody><?php foreach ($historico as $h): ?><tr>


                                <td><?= $e($h['criado_em']) ?></td>


                                <td><?= $e($h['nome']) ?></td>


                                <td><?= $e($h['acao']) ?></td>


                                <td><?= $e($h['origem']) ?></td>


                                <td><?= $e($h['admin_id'] ?? 'Sistema') ?></td>


                                <td><?= $e($h['detalhes']) ?></td>


                            </tr><?php endforeach; ?></tbody>


                </table>


            </div>


        </section>


        <section class="mod-panel">


            <h2>Auditoria de alterações</h2><?php foreach ($auditoria as $a): ?><details class="mod-record">


                    <summary><?= $e($a['criado_em'] . ' · ' . $a['entidade'] . ' #' . $a['entidade_id'] . ' · ' . $a['acao']) ?></summary>


                    <p>Administrador #<?= $e($a['admin_id']) ?></p>


                    <pre><?= $e($a['dados_antes_json']) ?></pre>


                    <pre><?= $e($a['dados_depois_json']) ?></pre>


                </details><?php endforeach; ?>


        </section>


    <?php endif; ?>


</main>


<?php require APP_ROOT . '/views/layouts/admin/footer.php'; ?>
