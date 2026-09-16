<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\Csrf;
use PDO;

final class DestaquesHomeController extends Controller
{
    /*
    =================================
    LISTAGEM
    =================================
    */

    public function index(): void
    {
        $mensagemSucesso = isset($_SESSION['admin_destaque_home_sucesso'])
            ? (string) $_SESSION['admin_destaque_home_sucesso']
            : null;

        unset($_SESSION['admin_destaque_home_sucesso']);

        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $sql = "
            SELECT
                destaques_home.id,
                destaques_home.tipo,
                destaques_home.item_id,
                destaques_home.ativo,
                destaques_home.ordem,

                categorias.id AS categoria_id,
                categorias.nome AS categoria_nome,
                categorias.slug AS categoria_slug,

                produtos.id AS produto_id,
                produtos.nome AS produto_nome,
                produtos.descricao AS produto_descricao,

                produto_categorias.id AS produto_categoria_id,
                produto_categorias.nome AS produto_categoria_nome,

                CASE
                    WHEN destaques_home.tipo = 'categoria'
                    THEN (
                        SELECT
                            categoria_imagens.url_imagem

                        FROM categoria_imagens

                        WHERE
                            categoria_imagens.categoria_id =
                                categorias.id

                        AND categoria_imagens.principal = 1

                        ORDER BY
                            categoria_imagens.ordem ASC,
                            categoria_imagens.id ASC

                        LIMIT 1
                    )

                    WHEN destaques_home.tipo = 'produto'
                    THEN (
                        SELECT
                            produto_imagens.url_imagem

                        FROM produto_imagens

                        WHERE
                            produto_imagens.produto_id =
                                produtos.id

                        AND produto_imagens.principal = 1

                        ORDER BY
                            produto_imagens.ordem ASC,
                            produto_imagens.id ASC

                        LIMIT 1
                    )

                    ELSE NULL

                END AS imagem_url

            FROM destaques_home

            LEFT JOIN categorias
                ON destaques_home.tipo = 'categoria'

                AND categorias.id =
                    destaques_home.item_id

            LEFT JOIN produtos
                ON destaques_home.tipo = 'produto'

                AND produtos.id =
                    destaques_home.item_id

            LEFT JOIN categorias AS produto_categorias
                ON produtos.categoria_id =
                    produto_categorias.id

            ORDER BY
                destaques_home.ordem ASC,
                destaques_home.id ASC
        ";


        $destaques =
            $pdo
                ->query($sql)
                ->fetchAll();


        $this->view(
            'admin/destaques-home',
            [

                'tituloPagina' =>
                    'Destaques da Home',

                'destaques' =>
                    $destaques,

                'csrfToken' =>
                    Csrf::gerar(),

                'mensagemSucesso' =>
                    $mensagemSucesso,

            ]
        );
    }

    public function editar(int $id): void
    {
        $pdo = require APP_ROOT . '/database/conexao.php';

        $stmt = $pdo->prepare('SELECT id, tipo, item_id, ativo, ordem FROM destaques_home WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $destaqueHome = $stmt->fetch();

        if ($destaqueHome === false) {
            $this->redirecionar('/admin/destaques-home');
        }

        $categorias = $pdo->query('SELECT id, nome, slug FROM categorias WHERE ativo = 1 ORDER BY nome ASC')->fetchAll();
        $produtos = $pdo->query("SELECT produtos.id, produtos.nome, produtos.categoria_id, categorias.nome AS categoria_nome FROM produtos INNER JOIN categorias ON categorias.id = produtos.categoria_id WHERE produtos.status = 'ativo' AND categorias.ativo = 1 ORDER BY categorias.nome ASC, produtos.nome ASC")->fetchAll();

        $this->view('admin/destaque-home-form', [
            'tituloPagina' => 'Editar Destaque da Home',
            'categorias' => $categorias,
            'produtos' => $produtos,
            'destaqueHome' => $destaqueHome,
            'csrfToken' => Csrf::gerar(),
        ]);
    }

    public function atualizar(int $id): void
    {
        $token = isset($_POST['_token'])
            ? (string) $_POST['_token']
            : (isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null);

        if (!Csrf::validar($token)) {
            http_response_code(403);
            exit('Formulário expirado.');
        }

        $tipo = trim((string) ($_POST['tipo'] ?? ''));
        $itemId = $tipo === 'categoria' ? (int) ($_POST['categoria_id'] ?? 0) : (int) ($_POST['produto_id'] ?? 0);
        $ordem = max(0, (int) ($_POST['ordem'] ?? 0));

        if (($tipo !== 'categoria' && $tipo !== 'produto') || $itemId <= 0) {
            exit('Selecione um item válido.');
        }

        $pdo = require APP_ROOT . '/database/conexao.php';
        $stmtItem = $tipo === 'categoria'
            ? $pdo->prepare('SELECT id FROM categorias WHERE id = :id AND ativo = 1 LIMIT 1')
            : $pdo->prepare("SELECT produtos.id FROM produtos INNER JOIN categorias ON categorias.id = produtos.categoria_id WHERE produtos.id = :id AND produtos.status = 'ativo' AND categorias.ativo = 1 LIMIT 1");
        $stmtItem->execute([':id' => $itemId]);

        if ($stmtItem->fetch() === false) {
            exit('O item selecionado não está disponível.');
        }

        $duplicado = $pdo->prepare('SELECT id FROM destaques_home WHERE tipo = :tipo AND item_id = :item_id AND id <> :id LIMIT 1');
        $duplicado->execute([':tipo' => $tipo, ':item_id' => $itemId, ':id' => $id]);

        if ($duplicado->fetch() !== false) {
            exit('Este item já está nos destaques da Home.');
        }

        $stmt = $pdo->prepare('UPDATE destaques_home SET tipo = :tipo, item_id = :item_id, ordem = :ordem WHERE id = :id');
        $stmt->execute([':tipo' => $tipo, ':item_id' => $itemId, ':ordem' => $ordem, ':id' => $id]);

        $this->definirMensagemSucesso('Destaque atualizado com sucesso.');
        $this->redirecionar('/admin/destaques-home');
    }


    /*
    =================================
    NOVO
    =================================
    */

    public function novo(): void
    {
        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        /*
        =================================
        CATEGORIAS
        =================================
        */

        $stmtCategorias =
            $pdo->prepare("
                SELECT
                    categorias.id,
                    categorias.nome,
                    categorias.slug

                FROM categorias

                WHERE categorias.ativo = 1

                ORDER BY
                    categorias.nome ASC
            ");


        $stmtCategorias->execute();


        $categorias =
            $stmtCategorias->fetchAll();


        /*
        =================================
        PRODUTOS
        =================================
        */

        $stmtProdutos =
            $pdo->prepare("
                SELECT
                    produtos.id,
                    produtos.nome,
                    produtos.categoria_id,

                    categorias.nome
                        AS categoria_nome

                FROM produtos

                INNER JOIN categorias
                    ON categorias.id =
                        produtos.categoria_id

                WHERE
                    produtos.status = 'ativo'

                    AND categorias.ativo = 1

                ORDER BY
                    categorias.nome ASC,
                    produtos.nome ASC
            ");


        $stmtProdutos->execute();


        $produtos =
            $stmtProdutos->fetchAll();


        $this->view(
            'admin/destaque-home-form',
            [

                'tituloPagina' =>
                    'Novo Destaque da Home',

                'categorias' =>
                    $categorias,

                'produtos' =>
                    $produtos,

                'csrfToken' =>
                    Csrf::gerar(),

            ]
        );
    }


    /*
    =================================
    SALVAR
    =================================
    */

    public function salvar(): void
    {
        /*
        =================================
        CSRF
        =================================
        */

        $token = isset($_POST['_token'])
            ? (string) $_POST['_token']
            : (isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null);


        if (
            !Csrf::validar($token)
        ) {

            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        /*
        =================================
        CONEXÃO
        =================================
        */

        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        /*
        =================================
        DADOS
        =================================
        */

        $tipo =
            trim(
                (string) (
                    $_POST['tipo']
                    ?? ''
                )
            );


        $ordem =
            isset($_POST['ordem'])
                ? (int) $_POST['ordem']
                : 0;


        /*
        =================================
        VALIDA TIPO
        =================================
        */

        if (
            $tipo !== 'categoria'

            &&

            $tipo !== 'produto'
        ) {

            exit(
                'Tipo de destaque inválido.'
            );
        }


        /*
        =================================
        DEFINE ITEM
        =================================
        */

        $itemId = 0;


        if (
            $tipo === 'categoria'
        ) {

            $itemId =
                isset($_POST['categoria_id'])
                    ? (int) $_POST['categoria_id']
                    : 0;

        }


        if (
            $tipo === 'produto'
        ) {

            $itemId =
                isset($_POST['produto_id'])
                    ? (int) $_POST['produto_id']
                    : 0;

        }


        /*
        =================================
        VALIDA ITEM
        =================================
        */

        if (
            $itemId <= 0
        ) {

            exit(
                'Selecione um item.'
            );
        }


        /*
        =================================
        VERIFICA ITEM
        =================================
        */

        if (
            $tipo === 'categoria'
        ) {

            $stmtItem =
                $pdo->prepare("
                    SELECT
                        id

                    FROM categorias

                    WHERE
                        id = :id

                        AND ativo = 1

                    LIMIT 1
                ");

        } else {

            $stmtItem =
                $pdo->prepare("
                    SELECT
                        produtos.id

                    FROM produtos

                    INNER JOIN categorias
                        ON categorias.id =
                            produtos.categoria_id

                    WHERE
                        produtos.id = :id

                        AND produtos.status = 'ativo'

                        AND categorias.ativo = 1

                    LIMIT 1
                ");

        }


        $stmtItem->execute(
            [

                ':id' =>
                    $itemId,

            ]
        );


        if (
            $stmtItem->fetch()
            === false
        ) {

            exit(
                'O item selecionado não está disponível.'
            );
        }


        /*
        =================================
        EVITA DUPLICIDADE
        =================================
        */

        $stmtDuplicado =
            $pdo->prepare("
                SELECT
                    id

                FROM destaques_home

                WHERE
                    tipo = :tipo

                    AND item_id = :item_id

                LIMIT 1
            ");


        $stmtDuplicado->execute(
            [

                ':tipo' =>
                    $tipo,

                ':item_id' =>
                    $itemId,

            ]
        );


        if (
            $stmtDuplicado->fetch()
            !== false
        ) {

            exit(
                'Este item já está nos destaques da Home.'
            );
        }


        /*
        =================================
        DEFINE ORDEM AUTOMÁTICA
        =================================
        */

        if (
            $ordem <= 0
        ) {

            $stmtOrdem =
                $pdo->query("
                    SELECT

                        COALESCE(
                            MAX(ordem),
                            0
                        ) + 1

                        AS proxima_ordem

                    FROM destaques_home
                ");


            $resultadoOrdem =
                $stmtOrdem->fetch();


            $ordem =
                (int) (
                    $resultadoOrdem[
                        'proxima_ordem'
                    ]
                    ?? 1
                );

        }


        /*
        =================================
        INSERE DESTAQUE
        =================================
        */

        $stmt =
            $pdo->prepare("
                INSERT INTO destaques_home
                (
                    tipo,
                    item_id,
                    ativo,
                    ordem
                )

                VALUES
                (
                    :tipo,
                    :item_id,
                    1,
                    :ordem
                )
            ");


        $stmt->execute(
            [

                ':tipo' =>
                    $tipo,

                ':item_id' =>
                    $itemId,

                ':ordem' =>
                    $ordem,

            ]
        );

        $this->definirMensagemSucesso('Destaque adicionado à Home com sucesso.');


        /*
        =================================
        REDIRECIONA
        =================================
        */

        $this->redirecionar(
            '/admin/destaques-home'
        );
    }


    /*
    =================================
    ATIVA / DESATIVA
    =================================
    */

    public function alternarAtivo(
        int $id
    ): void {

        $token = isset($_POST['_token'])
            ? (string) $_POST['_token']
            : (isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null);


        if (
            !Csrf::validar($token)
        ) {

            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $stmt =
            $pdo->prepare("
                UPDATE destaques_home

                SET

                    ativo =
                        CASE

                            WHEN ativo = 1

                            THEN 0

                            ELSE 1

                        END

                WHERE id = :id
            ");


        $stmt->execute(
            [

                ':id' =>
                    $id,

            ]
        );

        $this->definirMensagemSucesso('Visibilidade do destaque atualizada.');


        $this->redirecionar(
            '/admin/destaques-home'
        );
    }


    /*
    =================================
    EXCLUIR
    =================================
    */

    public function excluir(
        int $id
    ): void {

        $token = isset($_POST['_token'])
            ? (string) $_POST['_token']
            : (isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null);


        if (
            !Csrf::validar($token)
        ) {

            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $stmt =
            $pdo->prepare("
                DELETE FROM destaques_home

                WHERE id = :id
            ");


        $stmt->execute(
            [

                ':id' =>
                    $id,

            ]
        );

        $this->definirMensagemSucesso('Destaque removido da Home.');


        $this->redirecionar(
            '/admin/destaques-home'
        );
    }


    /*
    =================================
    ATUALIZA ORDEM
    =================================
    */

    public function atualizarOrdem(): void
    {
        $token = isset($_POST['_token'])
            ? (string) $_POST['_token']
            : (isset($_POST['csrf_token']) ? (string) $_POST['csrf_token'] : null);


        if (
            !Csrf::validar($token)
        ) {

            http_response_code(403);

            exit(
                'Formulário expirado.'
            );
        }


        $ordens =
            $_POST['ordem']
            ?? [];


        if (
            !is_array($ordens)
        ) {

            $this->redirecionar(
                '/admin/destaques-home'
            );

            return;
        }


        $pdo =
            require APP_ROOT
            . '/database/conexao.php';


        $stmt =
            $pdo->prepare("
                UPDATE destaques_home

                SET
                    ordem = :ordem

                WHERE id = :id
            ");


        foreach (
            $ordens as $id => $ordem
        ) {

            $id =
                (int) $id;


            $ordem =
                (int) $ordem;


            if (
                $id <= 0
            ) {

                continue;

            }


            if (
                $ordem < 0
            ) {

                $ordem = 0;

            }


            $stmt->execute(
                [

                    ':ordem' =>
                        $ordem,

                    ':id' =>
                        $id,

                ]
            );
        }

        $this->definirMensagemSucesso('Ordem dos destaques salva com sucesso.');


        $this->redirecionar(
            '/admin/destaques-home'
        );
    }

    private function definirMensagemSucesso(string $mensagem): void
    {
        $_SESSION['admin_destaque_home_sucesso'] = $mensagem;
    }
}
