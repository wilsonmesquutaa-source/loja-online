<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Repositories\ProdutoRepository;
use RuntimeException;

final class CarrinhoController extends Controller
{
    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $carrinho = $_SESSION['carrinho'] ?? [];

        if (!is_array($carrinho)) {
            $carrinho = [];
        }

        $this->view(
            'site/carrinho',
            [
                'tituloPagina' => 'Carrinho',
                'rotaAtual' => 'carrinho',
                'carrinho' => $carrinho,
            ]
        );
    }


    public function adicionar(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $categoriaId = filter_input(
            INPUT_POST,
            'categoria_id',
            FILTER_VALIDATE_INT
        );

        $quantidades = $_POST['quantidades'] ?? [];

        $editarIndice = filter_input(
            INPUT_POST,
            'editar_indice',
            FILTER_VALIDATE_INT
        );

        if (!$categoriaId) {
            $this->redirecionar('produtos');
        }

        if (!is_array($quantidades)) {
            $quantidades = [];
        }

        $repository = new ProdutoRepository(
            $this->pdo
        );

        $categoria = $repository
            ->buscarCategoriaPorId($categoriaId);

        if ($categoria === null) {
            throw new RuntimeException(
                'Categoria não encontrada.'
            );
        }

        $produtos = $repository
            ->buscarProdutosPorCategoria(
                $categoriaId
            );

        $produtosPorId = [];

        foreach ($produtos as $produto) {
            $produtosPorId[(int) $produto['id']] = $produto;
        }

        $selecionados = [];

        foreach ($quantidades as $produtoId => $quantidade) {
            $produtoId = (int) $produtoId;

            $quantidade = filter_var(
                $quantidade,
                FILTER_VALIDATE_INT
            );

            if (
                $quantidade === false ||
                $quantidade <= 0
            ) {
                continue;
            }

            if (!isset($produtosPorId[$produtoId])) {
                continue;
            }

            $selecionados[] = [
                'produto_id' => $produtoId,
                'nome' =>
                $produtosPorId[$produtoId]['nome'],
                'quantidade' => $quantidade,
            ];
        }

        if ($selecionados === []) {
            $this->redirecionar(
                'produtos/categoria/' . $categoriaId
            );
        }

        $nomeCategoria = mb_strtolower(
            trim($categoria['nome']),
            'UTF-8'
        );

        $tipoCategoria = 'unica';

        if (
            str_contains(
                $nomeCategoria,
                'tradicionais'
            )
        ) {
            $tipoCategoria =
                'cento_tradicionais';
        } elseif (
            str_contains(
                $nomeCategoria,
                'folhados'
            )
        ) {
            $tipoCategoria =
                'cento_folhados';
        } elseif (
            str_contains(
                $nomeCategoria,
                'grandes'
            )
        ) {
            $tipoCategoria =
                'salgados_grandes';
        } elseif (
            str_contains(
                $nomeCategoria,
                'empadão'
            ) ||
            str_contains(
                $nomeCategoria,
                'empadões'
            ) ||
            str_contains(
                $nomeCategoria,
                'empadao'
            ) ||
            str_contains(
                $nomeCategoria,
                'empadoes'
            )
        ) {
            $tipoCategoria =
                'empadao';
        }

        $quantidadeTotal = 0;

        foreach ($selecionados as $selecionado) {
            $quantidadeTotal +=
                $selecionado['quantidade'];
        }


        /*
        =================================
        VALIDA TRADICIONAIS
        =================================
        */

        if (
            $tipoCategoria ===
            'cento_tradicionais'
            &&
            $quantidadeTotal > 4
        ) {
            $this->redirecionar(
                'produtos/categoria/' . $categoriaId
            );
        }


        /*
        =================================
        VALIDA FOLHADOS
        =================================
        */

        if (
            $tipoCategoria ===
            'cento_folhados'
            &&
            $quantidadeTotal > 2
        ) {
            $this->redirecionar(
                'produtos/categoria/' . $categoriaId
            );
        }


        /*
        =================================
        PREÇO
        =================================
        */

        if (
            $tipoCategoria ===
            'salgados_grandes'
        ) {
            $precoNormal =
                (float) $categoria['preco'];

            $precoRevenda =
                (float) $categoria['preco_revenda'];

            $quantidadeMinima =
                (int) $categoria['quantidade_minima_revenda'];

            $precoUnitario =
                $quantidadeTotal >=
                $quantidadeMinima
                ? $precoRevenda
                : $precoNormal;

            $subtotal =
                $quantidadeTotal *
                $precoUnitario;
        } elseif (
            $tipoCategoria ===
            'empadao'
        ) {
            $precoUnitario =
                (float) $categoria['preco'];

            $subtotal =
                $quantidadeTotal *
                $precoUnitario;
        } else {
            $precoUnitario =
                (float) $categoria['preco'];

            $subtotal =
                $precoUnitario;
        }


        /*
        =================================
        ITEM
        =================================
        */

        $item = [
            'categoria_id' =>
            (int) $categoria['id'],

            'categoria_nome' =>
            $categoria['nome'],

            'tipo_categoria' =>
            $tipoCategoria,

            'quantidade_total' =>
            $quantidadeTotal,

            'preco_unitario' =>
            $precoUnitario,

            'subtotal' =>
            $subtotal,

            'produtos' =>
            $selecionados,
        ];


        /*
        =================================
        CARRINHO
        =================================
        */

        if (
            !isset($_SESSION['carrinho'])
            ||
            !is_array(
                $_SESSION['carrinho']
            )
        ) {
            $_SESSION['carrinho'] = [];
        }


        /*
        =================================
        EDITANDO
        =================================
        */

        if (
            $editarIndice !== false
            &&
            $editarIndice !== null
            &&
            isset(
                $_SESSION['carrinho'][$editarIndice]
            )
        ) {
            $_SESSION['carrinho'][$editarIndice] =
                $item;
        } else {
            $_SESSION['carrinho'][] =
                $item;
        }


        $this->redirecionar('carrinho');
    }


    public function editar(int $indice): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (
            !isset(
                $_SESSION['carrinho'][$indice]
            )
        ) {
            $this->redirecionar('carrinho');
        }

        $item =
            $_SESSION['carrinho'][$indice];

        $categoriaId =
            (int) $item['categoria_id'];

        header(
            'Location: '
                . BASE_URL
                . '/produtos/categoria/'
                . $categoriaId
                . '?editar='
                . $indice
        );

        exit;
    }


    public function remover(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $indice = filter_input(
            INPUT_POST,
            'indice',
            FILTER_VALIDATE_INT
        );

        if (
            $indice !== false
            &&
            $indice !== null
            &&
            isset(
                $_SESSION['carrinho'][$indice]
            )
        ) {
            unset(
                $_SESSION['carrinho'][$indice]
            );

            $_SESSION['carrinho'] =
                array_values(
                    $_SESSION['carrinho']
                );
        }

        $this->redirecionar('carrinho');
    }
}
