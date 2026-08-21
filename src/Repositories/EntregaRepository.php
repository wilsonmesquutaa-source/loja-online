<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class EntregaRepository
{
    private PDO $pdo;


    public function __construct(
        PDO $pdo
    ) {
        $this->pdo = $pdo;
    }


    /*
    =================================
    BUSCA TAXA DE ENTREGA
    =================================
    */

    public function buscarTaxa(
        string $bairro,
        string $cidade,
        string $estado
    ): ?array {

        $sql = "
            SELECT
                id,
                bairro,
                cidade,
                estado,
                valor
            FROM taxas_entrega
            WHERE ativo = 1

            AND LOWER(
                TRIM(bairro)
            ) = LOWER(
                TRIM(:bairro)
            )

            AND LOWER(
                TRIM(cidade)
            ) = LOWER(
                TRIM(:cidade)
            )

            AND estado = :estado

            LIMIT 1
        ";


        $stmt =
            $this->pdo->prepare(
                $sql
            );


        $stmt->execute([
            ':bairro' =>
                $bairro,

            ':cidade' =>
                $cidade,

            ':estado' =>
                strtoupper(
                    trim(
                        $estado
                    )
                ),
        ]);


        $taxa =
            $stmt->fetch(
                PDO::FETCH_ASSOC
            );


        return
            $taxa !== false
                ? $taxa
                : null;
    }
}