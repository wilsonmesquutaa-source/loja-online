<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Repositories\ProdutoRepository;
use RuntimeException;

final class CarrinhoController extends Controller
{
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

        if (!$categoriaId) {
            $this->redirecionar('produtos');
        }

        if (!is_array($quantidades)) {
            $quantidades = [];
        }

        $repository = new ProdutoRepository(
            $this->pdo
        );

        $categoria = $repository->buscarCategoriaPorId(
            $categoriaId
        );

        if ($categoria === null) {
            throw new RuntimeException(
                'Categoria não encontrada.'
            );
        }

        $produtos =
            $repository->buscarProdutosPorCategoria(
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
                $quantidade === false
                ||
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
        }

        /*
        =================================
        CALCULA O ITEM
        =================================
        */

        $quantidadeTotal = 0;

        foreach ($selecionados as $selecionado) {
            $quantidadeTotal +=
                $selecionado['quantidade'];
        }

        $precoUnitario = 0.0;
        $subtotal = 0.0;

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

            if (
                $quantidadeTotal >=
                $quantidadeMinima
            ) {
                $precoUnitario =
                    $precoRevenda;
            } else {
                $precoUnitario =
                    $precoNormal;
            }

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

            /*
            Tradicionais e Folhados:

            A categoria representa um cento.
            O preço não é multiplicado pelas
            quantidades dos sabores.

            As quantidades representam apenas
            a composição do cento.
            */

            $precoUnitario =
                (float) $categoria['preco'];

            $subtotal =
                $precoUnitario;
        }

        /*
        =================================
        ITEM DO CARRINHO
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
        INICIALIZA CARRINHO
        =================================
        */

        if (
            !isset(
                $_SESSION['carrinho']
            )
            ||
            !is_array(
                $_SESSION['carrinho']
            )
        ) {
            $_SESSION['carrinho'] = [];
        }

        /*
        =================================
        ADICIONA ITEM
        =================================
        */

        $_SESSION['carrinho'][] = $item;

        /*
        =================================
        VOLTA PARA O CARRINHO
        =================================
        */

        $this->redirecionar('carrinho');
    }


    public function index(): void
    {
        if (
            session_status()
            !== PHP_SESSION_ACTIVE
        ) {
            session_start();
        }

        $carrinho =
            $_SESSION['carrinho'] ?? [];

        $this->view(
            'site/carrinho',
            [
                'tituloPagina' =>
                'Carrinho',

                'rotaAtual' =>
                'carrinho',

                'carrinho' =>
                $carrinho,
            ]
        );
    }
}
