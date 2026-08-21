<?php

declare(strict_types=1);

use App\Helpers\View;

require APP_ROOT
    . '/views/layouts/site/header.php';

View::componente(
    'site/navbar',
    [
        'rotaAtual' =>
        $rotaAtual,
    ]
);

$endereco =
    $endereco
    ?? null;

$erro =
    $_GET['erro']
    ?? null;

?>

<main class="cliente-enderecos-page">

    <div class="cliente-enderecos-container cliente-endereco-form-container">

        <header class="cliente-enderecos-header">

            <p class="cliente-enderecos-etiqueta">
                Minha conta
            </p>

            <h1>
                <?= $endereco
                    ? 'Editar endereço'
                    : 'Novo endereço'
                ?>
            </h1>

            <p>
                Informe os dados do local onde deseja receber seus pedidos.
            </p>

        </header>


        <?php if ($erro !== null): ?>

            <div
                class="cliente-enderecos-alert cliente-enderecos-alert-danger">
                <?= htmlspecialchars(
                    (string) $erro,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>

        <?php endif; ?>


        <section class="cliente-endereco-form-card">

            <form
                method="POST"
                action="<?= BASE_URL ?><?= $endereco
                                            ? '/cliente/enderecos/atualizar/'
                                            . (int) $endereco['id']
                                            : '/cliente/enderecos/salvar'
                                        ?>"
                class="cliente-endereco-form">

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= htmlspecialchars(
                                $csrfToken,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>">
                <?php if (
                    !empty($retorno)
                ): ?>

                    <input
                        type="hidden"
                        name="retorno"
                        value="<?= htmlspecialchars(
                                    (string) $retorno,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                <?php endif; ?>

                <div class="cliente-endereco-form-group">

                    <label for="identificacao">
                        Como deseja identificar este endereço?
                    </label>

                    <input
                        type="text"
                        id="identificacao"
                        name="identificacao"
                        maxlength="80"
                        required
                        placeholder="Ex.: Minha casa, Casa da tia, Trabalho"
                        value="<?= htmlspecialchars(
                                    (string)
                                    (
                                        $endereco['identificacao']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </div>


                <div class="cliente-endereco-form-group">

                    <label for="destinatario">
                        Nome do destinatário
                    </label>

                    <input
                        type="text"
                        id="destinatario"
                        name="destinatario"
                        maxlength="150"
                        required
                        value="<?= htmlspecialchars(
                                    (string)
                                    (
                                        $endereco['destinatario']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </div>


                <div class="cliente-endereco-form-grid">

                    <div class="cliente-endereco-form-group">

                        <label for="cep">
                            CEP
                        </label>

                        <input
                            type="text"
                            id="cep"
                            name="cep"
                            maxlength="9"
                            required
                            placeholder="00000-000"
                            value="<?= htmlspecialchars(
                                        (string)
                                        (
                                            $endereco['cep']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                    </div>


                    <div class="cliente-endereco-form-group">

                        <label for="estado">
                            Estado
                        </label>

                        <input
                            type="text"
                            id="estado"
                            name="estado"
                            maxlength="2"
                            required
                            placeholder="CE"
                            value="<?= htmlspecialchars(
                                        (string)
                                        (
                                            $endereco['estado']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                    </div>

                </div>


                <div class="cliente-endereco-form-group">

                    <label for="logradouro">
                        Logradouro
                    </label>

                    <input
                        type="text"
                        id="logradouro"
                        name="logradouro"
                        maxlength="180"
                        required
                        placeholder="Rua, avenida, travessa..."
                        value="<?= htmlspecialchars(
                                    (string)
                                    (
                                        $endereco['logradouro']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </div>


                <div class="cliente-endereco-form-grid">

                    <div class="cliente-endereco-form-group">

                        <label for="numero">
                            Número
                        </label>

                        <input
                            type="text"
                            id="numero"
                            name="numero"
                            maxlength="20"
                            required
                            value="<?= htmlspecialchars(
                                        (string)
                                        (
                                            $endereco['numero']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                    </div>


                    <div class="cliente-endereco-form-group">

                        <label for="complemento">
                            Complemento
                        </label>

                        <input
                            type="text"
                            id="complemento"
                            name="complemento"
                            maxlength="120"
                            placeholder="Apartamento, bloco, casa..."
                            value="<?= htmlspecialchars(
                                        (string)
                                        (
                                            $endereco['complemento']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>">

                    </div>

                </div>


                <div class="cliente-endereco-form-group">

                    <label for="bairro">
                        Bairro
                    </label>

                    <input
                        type="text"
                        id="bairro"
                        name="bairro"
                        maxlength="120"
                        required
                        value="<?= htmlspecialchars(
                                    (string)
                                    (
                                        $endereco['bairro']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </div>


                <div class="cliente-endereco-form-group">

                    <label for="cidade">
                        Cidade
                    </label>

                    <input
                        type="text"
                        id="cidade"
                        name="cidade"
                        maxlength="120"
                        required
                        value="<?= htmlspecialchars(
                                    (string)
                                    (
                                        $endereco['cidade']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </div>


                <?php if (!$endereco): ?>

                    <label
                        class="cliente-endereco-principal-checkbox">

                        <input
                            type="checkbox"
                            name="principal"
                            value="1">

                        <span>
                            Definir como endereço principal
                        </span>

                    </label>

                <?php else: ?>

                    <label
                        class="cliente-endereco-principal-checkbox">

                        <input
                            type="checkbox"
                            name="principal"
                            value="1"
                            <?= (
                                (int)
                                (
                                    $endereco['principal']
                                    ?? 0
                                ) === 1
                            )
                                ? 'checked'
                                : ''
                            ?>>

                        <span>
                            Definir como endereço principal
                        </span>

                    </label>

                <?php endif; ?>


                <div class="cliente-endereco-form-acoes">

                    <button
                        type="submit"
                        class="cliente-endereco-form-btn-salvar">

                        <?= $endereco
                            ? 'Salvar alterações'
                            : 'Cadastrar endereço'
                        ?>

                    </button>


                    <a
                        href="<?= BASE_URL ?><?= !empty($retorno) && $retorno === 'checkout'
                                                    ? '/checkout'
                                                    : '/cliente/enderecos'
                                                ?>"
                        class="cliente-endereco-form-btn-voltar">

                        Voltar

                    </a>

                </div>

            </form>

        </section>

    </div>

</main>


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const cep =
                document.getElementById('cep');

            const logradouro =
                document.getElementById(
                    'logradouro'
                );

            const bairro =
                document.getElementById(
                    'bairro'
                );

            const cidade =
                document.getElementById(
                    'cidade'
                );

            const estado =
                document.getElementById(
                    'estado'
                );


            if (
                !cep ||
                !logradouro ||
                !bairro ||
                !cidade ||
                !estado
            ) {
                return;
            }


            /*
            =================================
            FORMATA CEP
            =================================
            */

            function formatarCep(valor) {

                valor =
                    valor.replace(
                        /\D/g,
                        ''
                    );


                if (
                    valor.length > 8
                ) {
                    valor =
                        valor.substring(
                            0,
                            8
                        );
                }


                if (
                    valor.length > 5
                ) {
                    return (
                        valor.substring(
                            0,
                            5
                        ) +
                        '-' +
                        valor.substring(
                            5
                        )
                    );
                }


                return valor;
            }


            /*
            =================================
            LIMPA CAMPOS AUTOMÁTICOS
            =================================
            */

            function limparEndereco() {

                logradouro.value =
                    '';

                bairro.value =
                    '';

                cidade.value =
                    '';

                estado.value =
                    '';
            }


            /*
            =================================
            CONSULTA CEP
            =================================
            */

            async function consultarCep() {

                const cepNumeros =
                    cep.value.replace(
                        /\D/g,
                        ''
                    );


                if (
                    cepNumeros.length !== 8
                ) {
                    return;
                }


                /*
                Evita consultas repetidas
                para o mesmo CEP.
                */

                if (
                    cep.dataset.cepConsultado ===
                    cepNumeros
                ) {
                    return;
                }


                cep.dataset.cepConsultado =
                    cepNumeros;


                /*
                Indica carregamento.
                */

                cep.classList.add(
                    'cliente-endereco-cep-carregando'
                );


                try {

                    const resposta =
                        await fetch(
                            'https://viacep.com.br/ws/' +
                            cepNumeros +
                            '/json/'
                        );


                    if (
                        !resposta.ok
                    ) {
                        throw new Error(
                            'Falha na consulta do CEP.'
                        );
                    }


                    const dados =
                        await resposta.json();


                    /*
                    CEP inexistente.
                    */

                    if (
                        dados.erro
                    ) {

                        limparEndereco();

                        cep.dataset.cepConsultado =
                            '';

                        alert(
                            'CEP não encontrado.'
                        );

                        return;
                    }


                    /*
                    Preenche automaticamente.
                    */

                    logradouro.value =
                        dados.logradouro ??
                        '';

                    bairro.value =
                        dados.bairro ??
                        '';

                    cidade.value =
                        dados.localidade ??
                        '';

                    estado.value =
                        dados.uf ??
                        '';


                    /*
                    Dispara eventos para qualquer
                    outra lógica do formulário.
                    */

                    logradouro.dispatchEvent(
                        new Event(
                            'change', {
                                bubbles: true
                            }
                        )
                    );

                    bairro.dispatchEvent(
                        new Event(
                            'change', {
                                bubbles: true
                            }
                        )
                    );

                    cidade.dispatchEvent(
                        new Event(
                            'change', {
                                bubbles: true
                            }
                        )
                    );

                    estado.dispatchEvent(
                        new Event(
                            'change', {
                                bubbles: true
                            }
                        )
                    );

                } catch (erro) {

                    cep.dataset.cepConsultado =
                        '';

                    alert(
                        'Não foi possível consultar o CEP. Verifique sua conexão e tente novamente.'
                    );

                } finally {

                    cep.classList.remove(
                        'cliente-endereco-cep-carregando'
                    );
                }
            }


            /*
            =================================
            DIGITAÇÃO DO CEP
            =================================
            */

            cep.addEventListener(
                'input',
                function() {

                    this.value =
                        formatarCep(
                            this.value
                        );


                    /*
                    Permite consultar novamente
                    caso o usuário altere o CEP.
                    */

                    const atual =
                        this.value.replace(
                            /\D/g,
                            ''
                        );


                    if (
                        atual !==
                        this.dataset.cepConsultado
                    ) {
                        this.dataset.cepConsultado =
                            '';
                    }


                    /*
                    Consulta automaticamente
                    ao completar 8 números.
                    */

                    if (
                        atual.length === 8
                    ) {
                        consultarCep();
                    }
                }
            );


            /*
            =================================
            CONSULTA AO SAIR DO CAMPO
            =================================
            */

            cep.addEventListener(
                'blur',
                function() {

                    consultarCep();

                }
            );


            /*
            =================================
            ESTADO
            =================================
            */

            estado.addEventListener(
                'input',
                function() {

                    this.value =
                        this.value
                        .toUpperCase()
                        .replace(
                            /[^A-Z]/g,
                            ''
                        )
                        .substring(
                            0,
                            2
                        );
                }
            );


            /*
            =================================
            CEP JÁ PREENCHIDO
            =================================
            */

            if (
                cep.value.replace(
                    /\D/g,
                    ''
                ).length === 8
            ) {
                consultarCep();
            }

        }
    );
</script>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';
