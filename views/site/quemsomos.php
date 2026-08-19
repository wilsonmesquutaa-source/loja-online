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

?>

<main>

    <!-- ============================
    QUEM SOMOS
    ============================= -->

    <section class="quem-somos">

        <div class="quem-somos-container">

            <!-- ============================
            CABEÇALHO
            ============================= -->

            <header class="quem-somos-header">

                <span class="quem-somos-subtitulo">
                    Nossa história
                </span>

                <h1>
                    Quem Somos
                </h1>

                <p>
                    Um sonho de pai que virou uma história de família.
                </p>

            </header>


            <!-- ============================
            INTRODUÇÃO
            ============================= -->

            <section class="quem-somos-historia">

                <div class="quem-somos-texto">

                    <span class="quem-somos-etapa">
                        O começo
                    </span>

                    <h2>
                        Um sonho que virou uma história de família
                    </h2>

                    <p>
                        O Cantim do Lanche nasceu de um sonho simples:
                        o sonho do meu pai, Paulo Sérgio, de deixar a CLT
                        e construir algo que fosse nosso.
                    </p>

                    <p>
                        Em 2015, ele decidiu transformar esse sonho em realidade.
                        Padeiro de profissão e com muitos anos de experiência,
                        encontrou nos salgados uma nova forma de colocar em prática
                        tudo aquilo que já sabia fazer.
                    </p>

                    <p>
                        E foi assim que começou a história do Cantim do Lanche.
                    </p>

                </div>


                <div class="quem-somos-foto">

                    <div class="quem-somos-foto-placeholder">

                        <span>
                            Foto do início
                        </span>

                    </div>

                </div>

            </section>


            <!-- ============================
            NOME
            ============================= -->

            <section class="quem-somos-historia historia-invertida">

                <div class="quem-somos-texto">

                    <span class="quem-somos-etapa">
                        Nossa identidade
                    </span>

                    <h2>
                        De Cantinho para Cantim
                    </h2>

                    <p>
                        O nosso nome também carrega um pedacinho dessa história.
                    </p>

                    <p>
                        Antes dos salgados, já tivemos uma lanchonete chamada
                        Cantinho do Lanche. Quando começamos essa nova fase,
                        decidimos manter a essência daquele nome, mas transformá-lo
                        em algo mais íntimo e mais nosso: Cantim do Lanche.
                    </p>

                    <p>
                        No começo, como acontece com todo sonho que está começando,
                        não foi fácil.
                    </p>

                    <p>
                        Precisamos conquistar a confiança das pessoas, apresentar
                        nosso trabalho e fazer com que quem nunca tivesse
                        experimentado nossos salgados tivesse vontade de pedir
                        novamente.
                    </p>

                </div>


                <div class="quem-somos-foto">

                    <div class="quem-somos-foto-placeholder">

                        <span>
                            Foto / antigo Cantinho
                        </span>

                    </div>

                </div>

            </section>


            <!-- ============================
            PRIMEIRO PEDIDO
            ============================= -->

            <section class="quem-somos-momento">

                <div class="quem-somos-momento-conteudo">

                    <span class="quem-somos-etapa">
                        Um momento especial
                    </span>

                    <h2>
                        O primeiro pedido
                    </h2>

                    <p>
                        Um dos momentos que marcou o nosso começo aconteceu
                        com o meu primeiro pedido de salgadinhos para uma festa.
                    </p>

                    <p>
                        Um amigo fez uma encomenda para comemorar o início
                        do namoro e disse que pagaria depois.
                    </p>

                    <p class="quem-somos-destaque">
                        Foi o nosso primeiro pedido “fiado”.
                    </p>

                    <p>
                        Aproveitei a oportunidade, criei um Instagram,
                        publiquei uma foto dos salgados e meus amigos
                        começaram a compartilhar.
                    </p>

                    <p>
                        O que começou de uma maneira tão simples foi ganhando forma.
                        E, pouco a pouco, aquele pequeno negócio foi se transformando
                        no Cantim do Lanche que temos hoje.
                    </p>

                </div>

                <div class="quem-somos-momento-foto">

                    <div class="quem-somos-foto-placeholder">

                        <span>
                            Foto dos primeiros salgados
                        </span>

                    </div>

                </div>

            </section>


            <!-- ============================
            LINHA DO TEMPO
            ============================= -->

            <section class="quem-somos-timeline">

                <header class="quem-somos-section-header">

                    <span class="quem-somos-etapa">
                        Nossa caminhada
                    </span>

                    <h2>
                        Uma história construída juntos
                    </h2>

                </header>


                <div class="timeline-item">

                    <div class="timeline-ano">
                        2015
                    </div>

                    <div class="timeline-conteudo">

                        <h3>
                            O início
                        </h3>

                        <p>
                            Paulo Sérgio decide deixar a CLT e transformar
                            sua experiência como padeiro em um novo negócio.
                        </p>

                    </div>

                </div>


                <div class="timeline-item">

                    <div class="timeline-ano">
                        2016
                    </div>

                    <div class="timeline-conteudo">

                        <h3>
                            Pai e filho
                        </h3>

                        <p>
                            Paulo Henrique deixa seu trabalho e entra
                            de vez no negócio do pai.
                        </p>

                    </div>

                </div>


                <div class="timeline-item">

                    <div class="timeline-ano">
                        Hoje
                    </div>

                    <div class="timeline-conteudo">

                        <h3>
                            O Cantim continua crescendo
                        </h3>

                        <p>
                            Pai e filho seguem trabalhando juntos,
                            levando salgados para revenda, festas e eventos.
                        </p>

                    </div>

                </div>

            </section>


            <!-- ============================
            PAI E FILHO
            ============================= -->

            <section class="quem-somos-historia">

                <div class="quem-somos-texto">

                    <span class="quem-somos-etapa">
                        Pai e filho
                    </span>

                    <h2>
                        Trabalhando juntos todos os dias
                    </h2>

                    <p>
                        Em 2016, eu, Paulo Henrique, deixei meu trabalho CLT
                        em uma autorizada da Motorola e decidi entrar de vez
                        no negócio do meu pai.
                    </p>

                    <p>
                        Foi ele quem me ensinou tudo o que sei:
                        a fazer salgados, empadas, empadões e tantas outras
                        coisas que fazem parte do nosso trabalho até hoje.
                    </p>

                    <p>
                        Desde então, somos nós dois.
                    </p>

                    <p>
                        Pai e filho, trabalhando juntos todos os dias,
                        de segunda a segunda.
                    </p>

                    <p>
                        Produção, compras, vendas, financeiro, administração...
                        fazemos um pouco de tudo.
                    </p>

                    <p class="quem-somos-destaque">
                        Mais do que dividir tarefas, dividimos ideias,
                        aprendizados, dificuldades e conquistas.
                    </p>

                </div>


                <div class="quem-somos-foto">

                    <div class="quem-somos-foto-placeholder">

                        <span>
                            Foto de pai e filho
                        </span>

                    </div>

                </div>

            </section>


            <!-- ============================
            FEITO À MÃO
            ============================= -->

            <section class="quem-somos-artesanal">

                <div class="quem-somos-artesanal-foto">

                    <div class="quem-somos-foto-placeholder">

                        <span>
                            Foto da produção
                        </span>

                    </div>

                </div>


                <div class="quem-somos-artesanal-texto">

                    <span class="quem-somos-etapa">
                        Nosso jeito de fazer
                    </span>

                    <h2>
                        Feito à mão
                    </h2>

                    <p>
                        Nossos salgados são produzidos manualmente,
                        à mão, por nós.
                    </p>

                    <p>
                        A massa é feita com leite, os recheios são bem
                        temperados e cada pedido recebe a nossa atenção.
                    </p>

                    <p>
                        Não fazemos apenas porque precisamos fazer.
                    </p>

                    <p class="quem-somos-destaque">
                        Fazemos porque gostamos do que fazemos.
                    </p>

                    <blockquote>
                        “Meu Deus, que salgado gostoso!”
                    </blockquote>

                </div>

            </section>


            <!-- ============================
            PANDEMIA
            ============================= -->

            <section class="quem-somos-historia historia-invertida">

                <div class="quem-somos-texto">

                    <span class="quem-somos-etapa">
                        Um período que marcou nossa história
                    </span>

                    <h2>
                        Quando o delivery ganhou força
                    </h2>

                    <p>
                        Foi assim que fomos conquistando nossa cartela
                        de clientes, uma indicação de cada vez.
                    </p>

                    <p>
                        Durante a pandemia, quando tudo parou e o delivery
                        se tornou ainda mais importante, a procura pelos
                        nossos salgados cresceu muito.
                    </p>

                    <p>
                        Trabalhamos todos os dias, sem parar.
                    </p>

                    <p>
                        Foi naquele momento que muitos dos clientes
                        que continuam conosco até hoje chegaram.
                    </p>

                </div>


                <div class="quem-somos-foto">

                    <div class="quem-somos-foto-placeholder">

                        <span>
                            Foto do Cantim na pandemia
                        </span>

                    </div>

                </div>

            </section>


            <!-- ============================
            HOJE
            ============================= -->

            <section class="quem-somos-hoje">

                <header class="quem-somos-section-header">

                    <span class="quem-somos-etapa">
                        Hoje
                    </span>

                    <h2>
                        O que fazemos
                    </h2>

                    <p>
                        Dois mundos que amamos e uma mesma dedicação.
                    </p>

                </header>


                <div class="quem-somos-cards">

                    <article class="quem-somos-card">

                        <div class="quem-somos-card-foto">

                            <div class="quem-somos-foto-placeholder">

                                <span>
                                    Foto de revenda
                                </span>

                            </div>

                        </div>

                        <div class="quem-somos-card-conteudo">

                            <h3>
                                Salgados para revenda
                            </h3>

                            <p>
                                Salgados produzidos para quem também
                                quer levar o sabor do Cantim do Lanche
                                para seus clientes.
                            </p>

                        </div>

                    </article>


                    <article class="quem-somos-card">

                        <div class="quem-somos-card-foto">

                            <div class="quem-somos-foto-placeholder">

                                <span>
                                    Foto de festa
                                </span>

                            </div>

                        </div>

                        <div class="quem-somos-card-conteudo">

                            <h3>
                                Festas e eventos
                            </h3>

                            <p>
                                Salgadinhos preparados para deixar
                                momentos especiais ainda mais gostosos.
                            </p>

                        </div>

                    </article>

                </div>

            </section>


            <!-- ============================
            VALORES
            ============================= -->

            <section class="quem-somos-valores">

                <header class="quem-somos-section-header">

                    <span class="quem-somos-etapa">
                        O que nos guia
                    </span>

                    <h2>
                        Nossos valores
                    </h2>

                </header>


                <div class="quem-somos-valores-grid">

                    <div class="quem-somos-valor">

                        <span class="quem-somos-valor-icone">
                            ❤️
                        </span>

                        <h3>
                            Amor
                        </h3>

                        <p>
                            Gostar do que fazemos e colocar carinho
                            em cada pedido.
                        </p>

                    </div>


                    <div class="quem-somos-valor">

                        <span class="quem-somos-valor-icone">
                            👨‍👦
                        </span>

                        <h3>
                            Família
                        </h3>

                        <p>
                            Continuar sendo uma empresa construída
                            e cuidada em família.
                        </p>

                    </div>


                    <div class="quem-somos-valor">

                        <span class="quem-somos-valor-icone">
                            🤝
                        </span>

                        <h3>
                            União
                        </h3>

                        <p>
                            Trabalhar juntos e compartilhar cada etapa
                            dessa caminhada.
                        </p>

                    </div>


                    <div class="quem-somos-valor">

                        <span class="quem-somos-valor-icone">
                            ✋
                        </span>

                        <h3>
                            Dedicação
                        </h3>

                        <p>
                            Fazer cada pedido com atenção,
                            cuidado e responsabilidade.
                        </p>

                    </div>

                </div>

            </section>

           <!-- ============================
ENCERRAMENTO
============================= -->

<section class="quem-somos-final">

    <span class="quem-somos-final-subtitulo">
        Cantim do Lanche
    </span>

    <p>
        O Cantim do Lanche pode crescer, chegar a cada vez mais
        pessoas e conquistar muitos outros clientes.
    </p>

    <p>
        Mas, no fim das contas, queremos que continue sendo
        aquilo que sempre foi:
    </p>

    <strong>
        Um sonho de pai que virou uma história de família.
    </strong>

</section>
</main>


<?php

require APP_ROOT
    . '/views/layouts/site/footer.php';