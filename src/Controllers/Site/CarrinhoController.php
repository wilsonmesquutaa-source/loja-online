<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Repositories\CarrinhoRepository;
use App\Repositories\CardapioRepository;
use RuntimeException;

final class CarrinhoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TOKEN DA SESSÃO
    |--------------------------------------------------------------------------
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
            empty(
                $_SESSION['carrinho_token']
            )
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


    /*
    |--------------------------------------------------------------------------
    | IDENTIFICA TIPO DA CATEGORIA
    |--------------------------------------------------------------------------
    */

    private function identificarTipoCategoria(
        string $nomeCategoria
    ): string {
        $nomeCategoria =
            mb_strtolower(
                trim(
                    $nomeCategoria
                ),
                'UTF-8'
            );


        if (
            str_contains(
                $nomeCategoria,
                'tradicionais'
            )
        ) {
            return 'cento_tradicionais';
        }


        if (
            str_contains(
                $nomeCategoria,
                'folhados'
            )
        ) {
            return 'cento_folhados';
        }


        if (
            str_contains(
                $nomeCategoria,
                'grandes'
            )
        ) {
            return 'salgados_grandes';
        }


        if (
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
            return 'empadao';
        }


        return 'unica';
    }


    /*
    |--------------------------------------------------------------------------
    | QUANTIDADE DE SABORES POR CENTO
    |--------------------------------------------------------------------------
    */

    private function partesPorCento(
        string $tipoCategoria
    ): int {
        if (
            $tipoCategoria ===
            'cento_tradicionais'
        ) {
            return 4;
        }


        if (
            $tipoCategoria ===
            'cento_folhados'
        ) {
            return 2;
        }


        return 0;
    }


    /*
    |--------------------------------------------------------------------------
    | MONTA CARRINHO VISUAL
    |--------------------------------------------------------------------------
    */

    private function montarCarrinhoVisual(
        array $itens
    ): array {
        $grupos = [];


        foreach (
            $itens as $item
        ) {

            $categoriaId =
                (int)
                $item['categoria_id'];


            $tipoCategoria =
                $this->identificarTipoCategoria(
                    $item['categoria_nome']
                );


            if (
                !isset(
                    $grupos[$categoriaId]
                )
            ) {

                $grupos[$categoriaId] = [
                    'categoria_id' =>
                        $categoriaId,

                    'categoria_nome' =>
                        $item['categoria_nome'],

                    'tipo_categoria' =>
                        $tipoCategoria,

                    'produtos' =>
                        [],

                    'quantidade_total' =>
                        0,

                    'subtotal' =>
                        0.0,

                    'preco_unitario' =>
                        0.0,

                    'quantidade_centos' =>
                        0,
                ];
            }


            $grupos[$categoriaId]['produtos'][] = [
                'produto_id' =>
                    (int)
                    $item['produto_id'],

                'nome' =>
                    $item['nome'],

                'quantidade' =>
                    (int)
                    $item['quantidade'],

                'preco_unitario' =>
                    (float)
                    $item['preco_unitario'],
            ];


            $grupos[$categoriaId]['quantidade_total'] +=
                (int)
                $item['quantidade'];
        }


        foreach (
            $grupos as &$grupo
        ) {

            $tipoCategoria =
                $grupo['tipo_categoria'];


            /*
            |--------------------------------------------------------------------------
            | CENTOS
            |--------------------------------------------------------------------------
            */

            if (
                $tipoCategoria ===
                'cento_tradicionais'
                ||
                $tipoCategoria ===
                'cento_folhados'
            ) {

                $partes =
                    $this->partesPorCento(
                        $tipoCategoria
                    );


                if (
                    $partes > 0
                ) {

                    $grupo['quantidade_centos'] =
                        (int)
                        ceil(
                            $grupo['quantidade_total']
                            /
                            $partes
                        );
                }


                if (
                    isset(
                        $grupo['produtos'][0]
                        ['preco_unitario']
                    )
                ) {

                    $precoPorParte =
                        (float)
                        $grupo['produtos'][0]
                        ['preco_unitario'];


                    $grupo['preco_unitario'] =
                        $precoPorParte
                        *
                        $partes;


                    $grupo['subtotal'] =
                        $grupo['quantidade_centos']
                        *
                        $grupo['preco_unitario'];
                }


            } else {

                /*
                |--------------------------------------------------------------------------
                | PRODUTOS UNITÁRIOS
                |--------------------------------------------------------------------------
                */

                $subtotal =
                    0.0;


                foreach (
                    $grupo['produtos']
                    as $produto
                ) {

                    $subtotal +=
                        (
                            (int)
                            $produto['quantidade']
                        )
                        *
                        (
                            (float)
                            $produto['preco_unitario']
                        );
                }


                $grupo['subtotal'] =
                    $subtotal;


                if (
                    isset(
                        $grupo['produtos'][0]
                        ['preco_unitario']
                    )
                ) {

                    $grupo['preco_unitario'] =
                        (float)
                        $grupo['produtos'][0]
                        ['preco_unitario'];
                }
            }
        }


        unset($grupo);


        return array_values(
            $grupos
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CARRINHO
    |--------------------------------------------------------------------------
    */

    public function index(): void
    {
        $tokenSessao =
            $this->obterTokenSessao();


        $repository =
            new CarrinhoRepository(
                $this->pdo
            );


        $carrinho =
            $repository
            ->buscarAbertoPorToken(
                $tokenSessao
            );


        $itens = [];


        if (
            $carrinho !== null
        ) {

            $itens =
                $repository
                ->buscarItens(
                    (int)
                    $carrinho['id']
                );
        }


        $carrinhoVisual =
            $this->montarCarrinhoVisual(
                $itens
            );


        $this->view(
            'site/carrinho',
            [
                'tituloPagina' =>
                    'Carrinho',

                'rotaAtual' =>
                    'carrinho',

                'carrinho' =>
                    $carrinhoVisual,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADICIONAR AO CARRINHO
    |--------------------------------------------------------------------------
    */

    public function adicionar(): void
    {
        $categoriaId =
            filter_input(
                INPUT_POST,
                'categoria_id',
                FILTER_VALIDATE_INT
            );


        $quantidades =
            $_POST['quantidades']
            ?? [];


        $editarCategoriaId =
            filter_input(
                INPUT_POST,
                'editar_categoria_id',
                FILTER_VALIDATE_INT
            );


        if (
            !$categoriaId
        ) {

            $this->redirecionar(
                'cardapio'
            );
        }


        if (
            !is_array(
                $quantidades
            )
        ) {

            $quantidades = [];
        }


        /*
        |--------------------------------------------------------------------------
        | REPOSITORIES
        |--------------------------------------------------------------------------
        */

        $cardapioRepository =
            new CardapioRepository(
                $this->pdo
            );


        $carrinhoRepository =
            new CarrinhoRepository(
                $this->pdo
            );


        /*
        |--------------------------------------------------------------------------
        | CATEGORIA
        |--------------------------------------------------------------------------
        */

        $categoria =
            $cardapioRepository
            ->buscarCategoriaPorId(
                (int)
                $categoriaId
            );


        if (
            $categoria === null
        ) {

            throw new RuntimeException(
                'Categoria não encontrada.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PRODUTOS DA CATEGORIA
        |--------------------------------------------------------------------------
        */

        $produtos =
            $cardapioRepository
            ->buscarProdutosPorCategoria(
                (int)
                $categoriaId
            );


        $produtosPorId = [];


        foreach (
            $produtos as $produto
        ) {

            $produtosPorId[
                (int)
                $produto['id']
            ] =
                $produto;
        }


        /*
        |--------------------------------------------------------------------------
        | PRODUTOS SELECIONADOS
        |--------------------------------------------------------------------------
        */

        $selecionados = [];


        foreach (
            $quantidades as
            $produtoId => $quantidade
        ) {

            $produtoId =
                (int)
                $produtoId;


            $quantidade =
                filter_var(
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


            if (
                !isset(
                    $produtosPorId[
                        $produtoId
                    ]
                )
            ) {
                continue;
            }


            $selecionados[] = [
                'produto_id' =>
                    $produtoId,

                'quantidade' =>
                    $quantidade,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | NENHUM PRODUTO SELECIONADO
        |--------------------------------------------------------------------------
        */

        if (
            $selecionados === []
        ) {

            $this->redirecionar(
                'cardapio/categoria/'
                . $categoriaId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TIPO DA CATEGORIA
        |--------------------------------------------------------------------------
        */

        $tipoCategoria =
            $this->identificarTipoCategoria(
                $categoria['nome']
            );


        $totalSelecionado =
            0;


        foreach (
            $selecionados as
            $selecionado
        ) {

            $totalSelecionado +=
                $selecionado['quantidade'];
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDA CENTO TRADICIONAL
        |--------------------------------------------------------------------------
        */

        if (
            $tipoCategoria ===
            'cento_tradicionais'
        ) {

            if (
                $totalSelecionado !== 4
            ) {

                $this->redirecionar(
                    'cardapio/categoria/'
                    . $categoriaId
                    . '?erro=cento_incompleto'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDA CENTO FOLHADO
        |--------------------------------------------------------------------------
        */

        if (
            $tipoCategoria ===
            'cento_folhados'
        ) {

            if (
                $totalSelecionado !== 2
            ) {

                $this->redirecionar(
                    'cardapio/categoria/'
                    . $categoriaId
                    . '?erro=cento_incompleto'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CARRINHO
        |--------------------------------------------------------------------------
        */

        $tokenSessao =
            $this->obterTokenSessao();


        $carrinho =
            $carrinhoRepository
            ->obterOuCriar(
                $tokenSessao
            );


        $carrinhoId =
            (int)
            $carrinho['id'];


        $this->pdo
            ->beginTransaction();


        try {

            /*
            |--------------------------------------------------------------------------
            | EDIÇÃO
            |--------------------------------------------------------------------------
            */

            if (
                $editarCategoriaId !== false
                &&
                $editarCategoriaId !== null
            ) {

                $carrinhoRepository
                    ->removerItensPorCategoria(
                        $carrinhoId,
                        (int)
                        $editarCategoriaId
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | SALGADOS GRANDES
            |--------------------------------------------------------------------------
            */

            if (
                $tipoCategoria ===
                'salgados_grandes'
            ) {

                $precoNormal =
                    (float)
                    $categoria['preco'];


                $precoRevenda =
                    (float)
                    $categoria['preco_revenda'];


                $quantidadeMinima =
                    (int)
                    $categoria[
                        'quantidade_minima_revenda'
                    ];


                $itensExistentes =
                    $carrinhoRepository
                    ->buscarItens(
                        $carrinhoId
                    );


                $quantidadeExistente =
                    0;


                foreach (
                    $itensExistentes
                    as $itemExistente
                ) {

                    if (
                        (int)
                        $itemExistente['categoria_id']
                        ===
                        (int)
                        $categoriaId
                    ) {

                        $quantidadeExistente +=
                            (int)
                            $itemExistente[
                                'quantidade'
                            ];
                    }
                }


                if (
                    $editarCategoriaId !== false
                    &&
                    $editarCategoriaId !== null
                ) {

                    $quantidadeExistente =
                        0;
                }


                $quantidadeFinal =
                    $quantidadeExistente
                    +
                    $totalSelecionado;


                $precoFinal =
                    $quantidadeFinal >=
                    $quantidadeMinima
                    ? $precoRevenda
                    : $precoNormal;


                foreach (
                    $selecionados
                    as $selecionado
                ) {

                    $carrinhoRepository
                        ->adicionarItem(
                            $carrinhoId,
                            $selecionado[
                                'produto_id'
                            ],
                            $selecionado[
                                'quantidade'
                            ],
                            $precoFinal
                        );
                }


                $carrinhoRepository
                    ->atualizarPrecoPorCategoria(
                        $carrinhoId,
                        (int)
                        $categoriaId,
                        $precoFinal
                    );


            } else {

                /*
                |--------------------------------------------------------------------------
                | CENTOS / EMPADÃO / UNITÁRIOS
                |--------------------------------------------------------------------------
                */

                $precoPorBloco =
                    0.0;


                if (
                    $tipoCategoria ===
                    'cento_tradicionais'
                ) {

                    $precoPorBloco =
                        (
                            (float)
                            $categoria['preco']
                        )
                        /
                        4;


                } elseif (
                    $tipoCategoria ===
                    'cento_folhados'
                ) {

                    $precoPorBloco =
                        (
                            (float)
                            $categoria['preco']
                        )
                        /
                        2;


                } else {

                    $precoPorBloco =
                        (float)
                        $categoria['preco'];
                }


                foreach (
                    $selecionados
                    as $selecionado
                ) {

                    $carrinhoRepository
                        ->adicionarItem(
                            $carrinhoId,
                            $selecionado[
                                'produto_id'
                            ],
                            $selecionado[
                                'quantidade'
                            ],
                            $precoPorBloco
                        );
                }
            }


            $this->pdo
                ->commit();


        } catch (
            \Throwable $erro
        ) {

            if (
                $this->pdo
                ->inTransaction()
            ) {

                $this->pdo
                    ->rollBack();
            }


            throw $erro;
        }


        /*
        |--------------------------------------------------------------------------
        | REDIRECIONAMENTO
        |--------------------------------------------------------------------------
        */

        $mensagem =
            (
                $editarCategoriaId !== false
                &&
                $editarCategoriaId !== null
            )
            ? 'atualizado'
            : 'adicionado';


        $this->redirecionar(
            'cardapio/categoria/'
            . $categoriaId
            . '?sucesso='
            . $mensagem
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR CATEGORIA DO CARRINHO
    |--------------------------------------------------------------------------
    */

    public function editar(
        int $categoriaId
    ): void {

        $tokenSessao =
            $this->obterTokenSessao();


        $repository =
            new CarrinhoRepository(
                $this->pdo
            );


        $carrinho =
            $repository
            ->buscarAbertoPorToken(
                $tokenSessao
            );


        if (
            $carrinho === null
        ) {

            $this->redirecionar(
                'carrinho'
            );
        }


        $itens =
            $repository
            ->buscarItens(
                (int)
                $carrinho['id']
            );


        $encontrou =
            false;


        foreach (
            $itens as $item
        ) {

            if (
                (int)
                $item['categoria_id']
                ===
                $categoriaId
            ) {

                $encontrou =
                    true;

                break;
            }
        }


        if (
            !$encontrou
        ) {

            $this->redirecionar(
                'carrinho'
            );
        }


        $this->redirecionar(
            'cardapio/categoria/'
            . $categoriaId
            . '?editar=1'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REMOVER CATEGORIA DO CARRINHO
    |--------------------------------------------------------------------------
    */

    public function remover(): void
    {
        $tokenSessao =
            $this->obterTokenSessao();


        $categoriaId =
            filter_input(
                INPUT_POST,
                'categoria_id',
                FILTER_VALIDATE_INT
            );


        if (
            !$categoriaId
        ) {

            $this->redirecionar(
                'carrinho'
            );
        }


        $repository =
            new CarrinhoRepository(
                $this->pdo
            );


        $carrinho =
            $repository
            ->buscarAbertoPorToken(
                $tokenSessao
            );


        if (
            $carrinho === null
        ) {

            $this->redirecionar(
                'carrinho'
            );
        }


        $repository
            ->removerItensPorCategoria(
                (int)
                $carrinho['id'],
                (int)
                $categoriaId
            );


        $repository
            ->removerSeVazio(
                (int)
                $carrinho['id']
            );


        $this->redirecionar(
            'carrinho'
        );
    }
}