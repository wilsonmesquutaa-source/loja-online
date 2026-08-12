<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Repositories\CarrinhoRepository;
use App\Repositories\ProdutoRepository;
use RuntimeException;

final class CarrinhoController extends Controller
{
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
            (string) $_SESSION[
                'carrinho_token'
            ];
    }


    private function identificarTipoCategoria(
        string $nomeCategoria
    ): string {
        $nomeCategoria =
            mb_strtolower(
                trim($nomeCategoria),
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


    private function montarCarrinhoVisual(
        array $itens
    ): array {
        $grupos = array();

        foreach ($itens as $item) {

            $categoriaId =
                (int) $item[
                    'categoria_id'
                ];

            $tipoCategoria =
                $this->identificarTipoCategoria(
                    $item[
                        'categoria_nome'
                    ]
                );


            if (
                !isset(
                    $grupos[$categoriaId]
                )
            ) {
                $grupos[$categoriaId] = array(
                    'categoria_id' =>
                        $categoriaId,

                    'categoria_nome' =>
                        $item[
                            'categoria_nome'
                        ],

                    'tipo_categoria' =>
                        $tipoCategoria,

                    'produtos' =>
                        array(),

                    'quantidade_total' =>
                        0,

                    'subtotal' =>
                        0.0,

                    'preco_unitario' =>
                        0.0,

                    'quantidade_centos' =>
                        0,
                );
            }


            $grupos[$categoriaId][
                'produtos'
            ][] = array(
                'produto_id' =>
                    (int) $item[
                        'produto_id'
                    ],

                'nome' =>
                    $item['nome'],

                'quantidade' =>
                    (int) $item[
                        'quantidade'
                    ],

                'preco_unitario' =>
                    (float) $item[
                        'preco_unitario'
                    ],
            );


            $grupos[$categoriaId][
                'quantidade_total'
            ] +=
                (int) $item[
                    'quantidade'
                ];
        }


        foreach (
            $grupos as
            &$grupo
        ) {

            $tipoCategoria =
                $grupo[
                    'tipo_categoria'
                ];


            /*
            =================================
            CENTOS
            =================================
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


                if ($partes > 0) {

                    $grupo[
                        'quantidade_centos'
                    ] =
                        (int) ceil(
                            $grupo[
                                'quantidade_total'
                            ]
                            / $partes
                        );
                }


                /*
                O preco_unitario do primeiro
                bloco representa o valor de
                uma parte do cento.
                */

                if (
                    isset(
                        $grupo[
                            'produtos'
                        ][0][
                            'preco_unitario'
                        ]
                    )
                ) {

                    $precoPorParte =
                        (float)
                        $grupo[
                            'produtos'
                        ][0][
                            'preco_unitario'
                        ];


                    $grupo[
                        'preco_unitario'
                    ] =
                        $precoPorParte *
                        $partes;


                    $grupo['subtotal'] =
                        $grupo[
                            'quantidade_centos'
                        ]
                        *
                        $grupo[
                            'preco_unitario'
                        ];
                }

            } else {

                /*
                =================================
                PRODUTOS UNITÁRIOS
                =================================
                */

                $subtotal =
                    0.0;


                foreach (
                    $grupo[
                        'produtos'
                    ] as $produto
                ) {

                    $subtotal +=
                        (
                            (int)
                            $produto[
                                'quantidade'
                            ]
                        )
                        *
                        (
                            (float)
                            $produto[
                                'preco_unitario'
                            ]
                        );
                }


                $grupo[
                    'subtotal'
                ] =
                    $subtotal;


                if (
                    isset(
                        $grupo[
                            'produtos'
                        ][0][
                            'preco_unitario'
                        ]
                    )
                ) {
                    $grupo[
                        'preco_unitario'
                    ] =
                        (float)
                        $grupo[
                            'produtos'
                        ][0][
                            'preco_unitario'
                        ];
                }
            }
        }

        unset($grupo);


        return array_values(
            $grupos
        );
    }


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


        $itens =
            array();


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
            array(
                'tituloPagina' =>
                    'Carrinho',

                'rotaAtual' =>
                    'carrinho',

                'carrinho' =>
                    $carrinhoVisual,
            )
        );
    }


    public function adicionar(): void
    {
        $categoriaId =
            filter_input(
                INPUT_POST,
                'categoria_id',
                FILTER_VALIDATE_INT
            );


        $quantidades =
            isset($_POST['quantidades'])
                ? $_POST['quantidades']
                : array();


        $editarCategoriaId =
            filter_input(
                INPUT_POST,
                'editar_categoria_id',
                FILTER_VALIDATE_INT
            );


        if (!$categoriaId) {
            $this->redirecionar(
                'produtos'
            );
        }


        if (!is_array($quantidades)) {
            $quantidades =
                array();
        }


        $produtoRepository =
            new ProdutoRepository(
                $this->pdo
            );


        $carrinhoRepository =
            new CarrinhoRepository(
                $this->pdo
            );


        $categoria =
            $produtoRepository
                ->buscarCategoriaPorId(
                    $categoriaId
                );


        if (
            $categoria === null
        ) {
            throw new RuntimeException(
                'Categoria não encontrada.'
            );
        }


        $produtos =
            $produtoRepository
                ->buscarProdutosPorCategoria(
                    $categoriaId
                );


        $produtosPorId =
            array();


        foreach (
            $produtos as $produto
        ) {

            $produtosPorId[
                (int) $produto['id']
            ] =
                $produto;
        }


        $selecionados =
            array();


        foreach (
            $quantidades as
            $produtoId => $quantidade
        ) {

            $produtoId =
                (int) $produtoId;


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


            $selecionados[] =
                array(
                    'produto_id' =>
                        $produtoId,

                    'quantidade' =>
                        $quantidade,
                );
        }


        if (
            $selecionados === array()
        ) {

            $this->redirecionar(
                'produtos/categoria/'
                . $categoriaId
            );
        }


        $tipoCategoria =
            $this->identificarTipoCategoria(
                $categoria['nome']
            );


        $totalSelecionado =
            0;


        foreach (
            $selecionados
            as $selecionado
        ) {

            $totalSelecionado +=
                $selecionado[
                    'quantidade'
                ];
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
            $totalSelecionado > 4
        ) {
            $this->redirecionar(
                'produtos/categoria/'
                . $categoriaId
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
            $totalSelecionado > 2
        ) {
            $this->redirecionar(
                'produtos/categoria/'
                . $categoriaId
            );
        }


        $tokenSessao =
            $this->obterTokenSessao();


        $carrinho =
            $carrinhoRepository
                ->obterOuCriar(
                    $tokenSessao
                );


        $carrinhoId =
            (int) $carrinho['id'];


        $this->pdo->beginTransaction();


        try {

            /*
            =================================
            EDIÇÃO
            =================================
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
            =================================
            SALGADOS GRANDES
            =================================
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
                    $categoria[
                        'preco_revenda'
                    ];


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
                        $itemExistente[
                            'categoria_id'
                        ]
                        ===
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
                    $quantidadeFinal
                        >=
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
                        $categoriaId,
                        $precoFinal
                    );


            } else {

                /*
                =================================
                CENTOS / EMPADÃO
                =================================
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
                            $categoria[
                                'preco'
                            ]
                        )
                        / 4;

                } elseif (
                    $tipoCategoria ===
                    'cento_folhados'
                ) {

                    $precoPorBloco =
                        (
                            (float)
                            $categoria[
                                'preco'
                            ]
                        )
                        / 2;

                } else {

                    $precoPorBloco =
                        (float)
                        $categoria[
                            'preco'
                        ];
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


            $this->pdo->commit();

        } catch (\Throwable $erro) {

            if (
                $this->pdo->inTransaction()
            ) {
                $this->pdo->rollBack();
            }

            throw $erro;
        }


        $this->redirecionar(
            'carrinho'
        );
    }


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


        if (!$encontrou) {

            $this->redirecionar(
                'carrinho'
            );
        }


        $this->redirecionar(
            'produtos/categoria/'
            . $categoriaId
            . '?editar=1'
        );
    }


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


        if (!$categoriaId) {

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