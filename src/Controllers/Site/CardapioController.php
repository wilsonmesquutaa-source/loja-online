<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Repositories\CarrinhoRepository;
use App\Repositories\CardapioRepository;

final class CardapioController extends Controller
{
    public function index(): void
    {
        $cardapioRepository =
            new CardapioRepository(
                $this->pdo
            );

        $categorias =
            $cardapioRepository
            ->buscarCategorias();

        $this->view(
            'site/cardapio',
            [
                'tituloPagina' =>
                'Cardápio',

                'rotaAtual' =>
                'cardapio',

                'categorias' =>
                $categorias,

                'etiquetaProdutos' =>
                'Cardápio',
            ]
        );
    }

    public function categoria(
        int $id
    ): void {
        $cardapioRepository =
            new CardapioRepository(
                $this->pdo
            );

        /*
        =================================
        BUSCA A CATEGORIA
        =================================
        */

        $categoria =
            $cardapioRepository
            ->buscarCategoriaPorId(
                $id
            );

        /*
        =================================
        CATEGORIA NÃO ENCONTRADA
        =================================
        */

        if (
            $categoria === null
        ) {
            http_response_code(404);

            require
                APP_ROOT
                . '/views/erros/404.php';

            return;
        }

        /*
        =================================
        BUSCA OS PRODUTOS
        =================================
        */

        $produtos =
            $cardapioRepository
            ->buscarProdutosPorCategoria(
                $id
            );

        /*
        =================================
        BANNER DA CATEGORIA
        =================================
        */

        $bannerCategoria =
            $cardapioRepository
            ->buscarBannerCategoriaPorId(
                $id
            );

        /*
        =================================
        IDENTIFICA A CATEGORIA
        =================================
        */

        $nomeCategoria =
            mb_strtolower(
                trim(
                    $categoria['nome']
                ),
                'UTF-8'
            );

        /*
        =================================
        VALORES PADRÃO
        =================================
        */

        $tipoCategoria =
            'unica';

        $limiteOpcoes =
            1;

        /*
        =================================
        TRADICIONAIS
        =================================
        */

        if (
            str_contains(
                $nomeCategoria,
                'tradicionais'
            )
        ) {
            $tipoCategoria =
                'cento_tradicionais';

            $limiteOpcoes =
                4;
        }

        /*
        =================================
        FOLHADOS
        =================================
        */ elseif (
            str_contains(
                $nomeCategoria,
                'folhados'
            )
        ) {
            $tipoCategoria =
                'cento_folhados';

            $limiteOpcoes =
                2;
        }

        /*
        =================================
        SALGADOS GRANDES
        =================================
        */ elseif (
            str_contains(
                $nomeCategoria,
                'grandes'
            )
        ) {
            $tipoCategoria =
                'salgados_grandes';

            $limiteOpcoes =
                null;
        }

        /*
        =================================
        EMPADÃO
        =================================
        */ elseif (
            str_contains(
                $nomeCategoria,
                'empadão'
            )
            ||
            str_contains(
                $nomeCategoria,
                'empadões'
            )
            ||
            str_contains(
                $nomeCategoria,
                'empadao'
            )
            ||
            str_contains(
                $nomeCategoria,
                'empadoes'
            )
        ) {
            $tipoCategoria =
                'empadao';

            $limiteOpcoes =
                null;
        }

        /*
        =================================
        ESTADO PADRÃO
        =================================
        */

        $editarCategoriaId =
            null;

        $quantidadesIniciais =
            [];

        /*
        =================================
        VERIFICA MODO DE EDIÇÃO
        =================================
        */

        $estaEditando =
            isset(
                $_GET['editar']
            )
            &&
            $_GET['editar'] === '1';

        if (
            $estaEditando
        ) {
            /*
            ==============================
            TOKEN DA SESSÃO
            ==============================
            */

            $tokenSessao =
                $this->obterTokenSessao();

            /*
            ==============================
            CARRINHO
            ==============================
            */

            $carrinhoRepository =
                new CarrinhoRepository(
                    $this->pdo
                );

            $carrinho =
                $carrinhoRepository
                ->buscarAbertoPorToken(
                    $tokenSessao
                );

            /*
            ==============================
            SEM CARRINHO
            ==============================
            */

            if (
                $carrinho === null
            ) {
                $this->redirecionar(
                    'cardapio/categoria/'
                        . $id
                );

                return;
            }

            /*
            ==============================
            ITENS DO CARRINHO
            ==============================
            */

            $itens =
                $carrinhoRepository
                ->buscarItens(
                    (int)
                    $carrinho['id']
                );

            /*
            ==============================
            LOCALIZA A CATEGORIA
            ==============================
            */

            $encontrouCategoria =
                false;

            foreach (
                $itens as $item
            ) {
                if (
                    (int)
                    $item['categoria_id']
                    !== $id
                ) {
                    continue;
                }

                /*
                ------------------------------
                ENCONTROU ITEM
                ------------------------------
                */

                $encontrouCategoria =
                    true;

                $editarCategoriaId =
                    $id;

                $quantidadesIniciais[(int)
                    $item['produto_id']] =
                    (int)
                    $item['quantidade'];
            }

            /*
            ==============================
            CATEGORIA NÃO ESTÁ NO CARRINHO
            ==============================
            */

            if (
                !$encontrouCategoria
            ) {
                $this->redirecionar(
                    'cardapio/categoria/'
                        . $id
                );

                return;
            }
        }

        /*
        =================================
        CARREGA A VIEW
        =================================
        */

        $this->view(
            'site/categoria',
            [
                'tituloPagina' =>
                $categoria['nome'],

                'rotaAtual' =>
                'cardapio',

                'categoria' =>
                $categoria,

                'nomeCategoria' =>
                $categoria['nome'],

                'produtos' =>
                $produtos,

                'limiteOpcoes' =>
                $limiteOpcoes,

                'tipoCategoria' =>
                $tipoCategoria,

                'quantidadesIniciais' =>
                $quantidadesIniciais,

                'editarCategoriaId' =>
                $editarCategoriaId,
                'bannerCategoria' =>
                $bannerCategoria,
            ]
        );
    }

    /*
    =================================
    TOKEN DA SESSÃO
    =================================
    */

    private function obterTokenSessao(): string
    {
        if (
            session_status() !==
            PHP_SESSION_ACTIVE
        ) {
            session_start();
        }

        if (
            empty($_SESSION['carrinho_token'])
        ) {
            $_SESSION['carrinho_token'] =
                bin2hex(
                    random_bytes(32)
                );
        }

        return
            (string)
            $_SESSION['carrinho_token'];
    }
}
