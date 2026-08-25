<?php

declare(strict_types=1);

use App\Helpers\View;
use App\Helpers\Csrf;

require APP_ROOT
    . '/views/layouts/site/header.php';


View::componente(
    'site/navbar',
    [
        'rotaAtual' =>
        $rotaAtual,
    ]
);


/*
=================================
DADOS
=================================
*/

$erro =
    $erro
    ?? null;


$nome =
    $nome
    ?? '';


$email =
    $email
    ?? '';


$retorno =
    $retorno
    ?? '';


$identificacao =
    $identificacao
    ?? 'Minha casa';


$destinatario =
    $destinatario
    ?? '';


$cep =
    $cep
    ?? '';


$logradouro =
    $logradouro
    ?? '';


$numero =
    $numero
    ?? '';


$complemento =
    $complemento
    ?? '';


$bairro =
    $bairro
    ?? '';


$cidade =
    $cidade
    ?? 'Fortaleza';


$estado =
    $estado
    ?? 'CE';

?>



<main class="cliente-cadastro">

    <div class="cliente-cadastro-card">


        <!-- =================================
             CABEÇALHO
        ================================== -->

        <div class="cliente-cadastro-cabecalho">

            <div class="cliente-cadastro-icone">

                <i class="bi bi-person-plus"></i>

            </div>


            <h1>
                Crie sua conta
            </h1>


            <p>
                Cadastre seus dados e seu endereço
                para fazer seus pedidos com mais facilidade.
            </p>

        </div>


        <!-- =================================
             ERRO
        ================================== -->

        <?php if (
            $erro
        ): ?>

            <div
                class="cliente-cadastro-erro">

                <?= htmlspecialchars(
                    $erro,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =================================
             FORMULÁRIO
        ================================== -->

        <form
            method="POST"
            action="<?= BASE_URL ?>/cadastro"
            class="cliente-cadastro-formulario">


            <!-- =================================
                 CSRF
            ================================== -->

            <input
                type="hidden"
                name="_csrf"
                value="<?= htmlspecialchars(
                            Csrf::gerarCliente(),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">


            <!-- =================================
                 RETORNO
            ================================== -->

            <input
                type="hidden"
                name="retorno"
                value="<?= htmlspecialchars(
                            $retorno,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>">


            <!-- =================================
                 DADOS DA CONTA
            ================================== -->

            <div
                class="cliente-cadastro-secao">

                <div
                    class="cliente-cadastro-secao-cabecalho">

                    <h2>
                        Seus dados
                    </h2>


                    <p>
                        Informe os dados que serão usados
                        para acessar sua conta.
                    </p>

                </div>


                <!-- NOME -->

                <div class="cliente-cadastro-campo">

                    <label for="nome">

                        Nome completo

                    </label>


                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?= htmlspecialchars(
                                    $nome,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="Digite seu nome completo"
                        maxlength="150"
                        autocomplete="name"
                        required>

                </div>


                <!-- E-MAIL -->

                <div class="cliente-cadastro-campo">

                    <label for="email">

                        E-mail

                    </label>


                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars(
                                    $email,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="Digite seu e-mail"
                        maxlength="180"
                        autocomplete="email"
                        required>

                </div>


                <!-- SENHA -->

                <div class="cliente-cadastro-campo">

                    <label for="senha">

                        Senha

                    </label>


                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Crie uma senha"
                        autocomplete="new-password"
                        minlength="8"
                        required>


                    <span
                        class="cliente-cadastro-senha-info">

                        Use uma senha com pelo menos
                        8 caracteres.

                    </span>

                </div>


                <!-- CONFIRMAÇÃO -->

                <div class="cliente-cadastro-campo">

                    <label for="senha_confirmacao">

                        Confirmar senha

                    </label>


                    <input
                        type="password"
                        id="senha_confirmacao"
                        name="senha_confirmacao"
                        placeholder="Digite a senha novamente"
                        autocomplete="new-password"
                        minlength="8"
                        required>

                </div>

            </div>


            <!-- =================================
                 ENDEREÇO
            ================================== -->

            <div
                class="cliente-cadastro-secao">

                <div
                    class="cliente-cadastro-secao-cabecalho">

                    <h2>
                        Seu endereço
                    </h2>


                    <p>
                        Cadastre seu endereço principal
                        para agilizar seus próximos pedidos.
                    </p>

                </div>


                <!-- IDENTIFICAÇÃO -->

                <div class="cliente-cadastro-campo">

                    <label for="identificacao">

                        Identificação do endereço

                    </label>


                    <input
                        type="text"
                        id="identificacao"
                        name="identificacao"
                        value="<?= htmlspecialchars(
                                    $identificacao,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="Ex.: Minha casa"
                        maxlength="100"
                        required>

                </div>


                <!-- DESTINATÁRIO -->

                <div class="cliente-cadastro-campo">

                    <label for="destinatario">

                        Quem receberá o pedido?

                    </label>


                    <input
                        type="text"
                        id="destinatario"
                        name="destinatario"
                        value="<?= htmlspecialchars(
                                    $destinatario,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="Nome de quem receberá"
                        maxlength="150"
                        autocomplete="name"
                        required>

                </div>


                <!-- CEP -->

                <div class="cliente-cadastro-campo">

                    <label for="cep">

                        CEP

                    </label>


                    <input
                        type="text"
                        id="cep"
                        name="cep"
                        value="<?= htmlspecialchars(
                                    $cep,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="00000-000"
                        maxlength="9"
                        inputmode="numeric"
                        autocomplete="postal-code"
                        required>

                </div>


                <!-- LOGRADOURO -->

                <div class="cliente-cadastro-campo">

                    <label for="logradouro">

                        Logradouro

                    </label>


                    <input
                        type="text"
                        id="logradouro"
                        name="logradouro"
                        value="<?= htmlspecialchars(
                                    $logradouro,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="Rua, avenida, travessa..."
                        maxlength="180"
                        autocomplete="street-address"
                        required>

                </div>


                <!-- NÚMERO -->

                <div class="cliente-cadastro-campo">

                    <label for="numero">

                        Número

                    </label>


                    <input
                        type="text"
                        id="numero"
                        name="numero"
                        value="<?= htmlspecialchars(
                                    $numero,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="Ex.: 250"
                        maxlength="20"
                        required>

                </div>


                <!-- COMPLEMENTO -->

                <div class="cliente-cadastro-campo">

                    <label for="complemento">

                        Complemento

                    </label>


                    <input
                        type="text"
                        id="complemento"
                        name="complemento"
                        value="<?= htmlspecialchars(
                                    $complemento,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="Apartamento, bloco, referência..."
                        maxlength="150"
                        autocomplete="address-line2">

                </div>


                <!-- BAIRRO -->

                <div class="cliente-cadastro-campo">

                    <label for="bairro">

                        Bairro

                    </label>


                    <input
                        type="text"
                        id="bairro"
                        name="bairro"
                        value="<?= htmlspecialchars(
                                    $bairro,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="Digite o bairro"
                        maxlength="100"
                        required>

                </div>


                <!-- CIDADE -->

                <div class="cliente-cadastro-campo">

                    <label for="cidade">

                        Cidade

                    </label>


                    <input
                        type="text"
                        id="cidade"
                        name="cidade"
                        value="<?= htmlspecialchars(
                                    $cidade,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="Digite a cidade"
                        maxlength="100"
                        autocomplete="address-level2"
                        required>

                </div>


                <!-- ESTADO -->

                <div class="cliente-cadastro-campo">

                    <label for="estado">

                        Estado

                    </label>


                    <input
                        type="text"
                        id="estado"
                        name="estado"
                        value="<?= htmlspecialchars(
                                    $estado,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                        placeholder="CE"
                        maxlength="2"
                        minlength="2"
                        style="text-transform: uppercase;"
                        autocomplete="address-level1"
                        required>

                </div>

            </div>


            <!-- =================================
                 BOTÃO
            ================================== -->

            <button
                type="submit"
                class="cliente-cadastro-botao">

                <i class="bi bi-person-plus"></i>

                Criar minha conta

            </button>


            <!-- =================================
                 GOOGLE
            ================================== -->

            <div
                class="cliente-cadastro-divisor">

                <span>
                    ou
                </span>

            </div>


            <a
                href="<?= BASE_URL ?>/cadastro/google"
                class="cliente-cadastro-google">

                <i class="bi bi-google"></i>

                Cadastrar com Google

            </a>


        </form>


        <!-- =================================
             LOGIN
        ================================== -->

        <p class="cliente-cadastro-login">

            Já possui uma conta?

            <a
                href="<?= BASE_URL ?>/login">
                Entrar
            </a>

        </p>


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
                    'cliente-cadastro-cep-carregando'
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
                    =================================
                    PREENCHE AUTOMATICAMENTE
                    =================================
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
                    Dispara eventos para
                    outras lógicas do formulário.
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

                } catch (
                    erro
                ) {

                    cep.dataset.cepConsultado =
                        '';

                    alert(
                        'Não foi possível consultar o CEP. Verifique sua conexão e tente novamente.'
                    );

                } finally {

                    cep.classList.remove(
                        'cliente-cadastro-cep-carregando'
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


                    const atual =
                        this.value.replace(
                            /\D/g,
                            ''
                        );


                    /*
                    Permite nova consulta
                    se o CEP foi alterado.
                    */

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

?>