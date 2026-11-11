<?php

declare(strict_types=1);

namespace App\Services;

use DomainException;

final class TemaAssetService
{
    public const TIPOS = [
        'logo_desktop',
        'logo_mobile',
        'mascote',
        'favicon',
        'header_overlay',
        'header_background',
        'banner_home',
        'background_site',
        'decoracao_esquerda',
        'decoracao_direita',
        'decoracao_superior',
        'outro',
        'home_pendente',
        'home_moldura',
        'home_divisor',
        'home_vantagens',
        'home_atendimento',

    ];

    private const TAMANHO_MAXIMO = 5 * 1024 * 1024;

    private const TAMANHO_MAXIMO_VIDEO_MASCOTE = 30 * 1024 * 1024;

    private const LARGURA_MAXIMA = 6000;

    private const ALTURA_MAXIMA = 6000;

    private const TOTAL_PIXELS_MAXIMO = 16000000;

    private const QUALIDADE_WEBP = 88;

    private const MIMES_PERMITIDOS = [
        'image/png',
        'image/jpeg',
        'image/webp',
        'image/avif',
    ];


    /*
    =================================
    VERIFICA SE O ASSET EXISTE
    =================================
    */

    public static function existente(
        string $url
    ): bool {

        $padrao = '~^/assets/'
            . '(uploads/temas|images)/'
            . '[a-zA-Z0-9_.-]+'
            . '\.(webp|png|jpg|jpeg|avif|webm)$~D';

        if (
            !preg_match(
                $padrao,
                $url
            )
        ) {
            return false;
        }

        $arquivo =
            APP_ROOT
            . '/public'
            . $url;

        if (str_ends_with($url, '.webm')) return self::webmValido($arquivo);

        return
            is_file($arquivo)
            &&
            @getimagesize($arquivo) !== false;
    }


    /*
    =================================
    UPLOAD DO ASSET
    =================================
    */

    public static function upload(
        array $arquivoUpload,
        string $tipo = ''
    ): array {

        self::validarUpload(
            $arquivoUpload,
            $tipo
        );

        $arquivoTemporario =
            (string) $arquivoUpload['tmp_name'];

        $mime =
            self::obterMime(
                $arquivoTemporario
            );

        if ($mime === 'video/webm') {
            if ($tipo !== 'mascote') throw new DomainException('Vídeo WebM é permitido somente para o mascote.');
            if (!self::webmValido($arquivoTemporario)) throw new DomainException('Arquivo WebM inválido.');
            $diretorio = self::obterDiretorioDestino();
            self::garantirDiretorio($diretorio);
            $nome = bin2hex(random_bytes(16)) . '.webm';
            if (!move_uploaded_file($arquivoTemporario, $diretorio . '/' . $nome)) throw new DomainException('Não foi possível salvar o vídeo.');
            return ['url_arquivo'=>'/assets/uploads/temas/'.$nome, 'mime_type'=>'video/webm',
                'largura'=>null, 'altura'=>null, 'tamanho_bytes'=>(int)filesize($diretorio.'/'.$nome),
                'possui_transparencia'=>0];
        }

        self::validarMime(
            $mime
        );

        $informacoes =
            self::obterInformacoesImagem(
                $arquivoTemporario
            );

        self::validarDimensoes(
            $informacoes
        );

        if ($mime === 'image/webp' && self::webpAnimado($arquivoTemporario)) {
            if ($tipo !== 'mascote') {
                throw new DomainException('WebP animado é permitido somente para o mascote. Selecione Mascote em Usar como.');
            }
            $diretorio = self::obterDiretorioDestino();
            self::garantirDiretorio($diretorio);
            $nome = self::gerarNomeArquivo();
            $destino = $diretorio . '/' . $nome;
            // Não passar pelo GD: a recodificação descartaria os quadros animados.
            if (!move_uploaded_file($arquivoTemporario, $destino)) {
                throw new DomainException('Não foi possível salvar o mascote animado.');
            }
            $cabecalho = file_get_contents($destino, false, null, 0, 30);
            return [
                'url_arquivo' => '/assets/uploads/temas/' . $nome,
                'mime_type' => 'image/webp',
                'largura' => (int)$informacoes[0], 'altura' => (int)$informacoes[1],
                'tamanho_bytes' => (int)filesize($destino),
                'possui_transparencia' => (ord($cabecalho[20]) & 0x10) ? 1 : 0,
            ];
        }

        $imagem =
            self::criarImagem(
                $arquivoTemporario
            );

        self::prepararImagemParaWebp(
            $imagem
        );

        $diretorio =
            self::obterDiretorioDestino();

        self::garantirDiretorio(
            $diretorio
        );

        $nomeArquivo =
            self::gerarNomeArquivo();

        $arquivoDestino =
            $diretorio
            . '/'
            . $nomeArquivo;

        $possuiTransparenciaOrigem =
            self::possuiTransparencia(
                $arquivoTemporario,
                $mime
            );

        self::salvarComoWebp(
            $imagem,
            $arquivoDestino
        );

        imagedestroy(
            $imagem
        );

        if (
            $possuiTransparenciaOrigem
            &&
            !self::possuiTransparencia(
                $arquivoDestino,
                'image/webp'
            )
        ) {
            @unlink($arquivoDestino);

            throw new DomainException(
                'A transparência da imagem não foi preservada na conversão para WebP.'
            );
        }

        return [
            'url_arquivo' =>
                '/assets/uploads/temas/'
                . $nomeArquivo,

            'mime_type' =>
                'image/webp',

            'largura' =>
                (int) $informacoes[0],

            'altura' =>
                (int) $informacoes[1],

            'tamanho_bytes' =>
                (int) filesize(
                    $arquivoDestino
                ),

            'possui_transparencia' => $possuiTransparenciaOrigem ? 1 : 0,
        ];
    }


    /*
    =================================
    VALIDA UPLOAD
    =================================
    */

    /** Detecta animação por chunks RIFF, sem confundir textos de metadados com quadros. */
    public static function webmValido(string $arquivo): bool
    {
        if (!is_file($arquivo) || filesize($arquivo) < 32) return false;
        $cabecalho = file_get_contents($arquivo, false, null, 0, 4096);
        return substr($cabecalho, 0, 4) === "\x1a\x45\xdf\xa3"
            && str_contains($cabecalho, "webm")
            && (new \finfo(FILEINFO_MIME_TYPE))->file($arquivo) === 'video/webm';
    }

    public static function webpAnimado(string $arquivo): bool
    {
        $dados = @file_get_contents($arquivo);
        if ($dados === false || substr($dados, 0, 4) !== 'RIFF' || substr($dados, 8, 4) !== 'WEBP') return false;
        $total = strlen($dados);
        if ($total < 30 || unpack('V', substr($dados, 4, 4))[1] + 8 !== $total) {
            throw new DomainException('Arquivo WebP incompleto ou inválido.');
        }
        $animado = false; $controle = false; $quadros = 0;
        for ($offset = 12; $offset < $total;) {
            if ($offset + 8 > $total) throw new DomainException('Arquivo WebP inválido.');
            $chunk = substr($dados, $offset, 4);
            $tamanho = unpack('V', substr($dados, $offset + 4, 4))[1];
            if ($offset + 8 + $tamanho + ($tamanho % 2) > $total) throw new DomainException('Arquivo WebP incompleto.');
            if ($chunk === 'VP8X') {
                if ($offset !== 12 || $tamanho !== 10) throw new DomainException('Cabeçalho WebP inválido.');
                $animado = (ord($dados[$offset + 8]) & 0x02) !== 0;
            }
            if ($chunk === 'ANIM') {
                if (!$animado || $tamanho !== 6) throw new DomainException('Animação WebP inválida.');
                $controle = true;
            }
            if ($chunk === 'ANMF') {
                if (!$controle || $tamanho < 24) throw new DomainException('Quadro WebP inválido.');
                $quadros++;
            }
            $offset += 8 + $tamanho + ($tamanho % 2);
        }
        if ($animado && (!$controle || !$quadros)) throw new DomainException('Animação WebP sem quadros.');
        return $animado;
    }

    private static function validarUpload(
        array $arquivoUpload,
        string $tipo = ''
    ): void {

        $erro =
            (int) (
                $arquivoUpload['error']
                ?? UPLOAD_ERR_NO_FILE
            );

        $arquivoTemporario =
            (string) (
                $arquivoUpload['tmp_name']
                ?? ''
            );

        if (in_array($erro, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            throw new DomainException('O arquivo excede o limite de envio do servidor. Use WebM de até 30 MB para mascote ou imagem de até 5 MB.');
        }

        if (
            $erro !== UPLOAD_ERR_OK
            ||
            $arquivoTemporario === ''
            ||
            !is_uploaded_file(
                $arquivoTemporario
            )
        ) {
            throw new DomainException(
                'Selecione uma imagem válida.'
            );
        }

        $tamanho = (int) filesize($arquivoTemporario);
        $videoMascote = $tipo === 'mascote' && self::obterMime($arquivoTemporario) === 'video/webm';
        $limite = $videoMascote ? self::TAMANHO_MAXIMO_VIDEO_MASCOTE : self::TAMANHO_MAXIMO;
        if ($tamanho <= 0 || $tamanho > $limite) {
            throw new DomainException($videoMascote
                ? 'O vídeo do mascote deve ter no máximo 30 MB.'
                : 'A imagem deve ter no máximo 5 MB.');
        }

    }


    /*
    =================================
    MIME
    =================================
    */

    private static function obterMime(
        string $arquivoTemporario
    ): string {

        $finfo =
            new \finfo(
                FILEINFO_MIME_TYPE
            );

        $mime =
            $finfo->file(
                $arquivoTemporario
            );

        if (
            !is_string($mime)
            ||
            $mime === ''
        ) {
            throw new DomainException(
                'Não foi possível identificar o tipo da imagem.'
            );
        }

        return $mime;
    }


    private static function validarMime(
        string $mime
    ): void {

        if (
            !in_array(
                $mime,
                self::MIMES_PERMITIDOS,
                true
            )
        ) {
            throw new DomainException(
                'Use PNG, JPG, WebP ou AVIF.'
            );
        }
    }


    /*
    =================================
    DIMENSÕES
    =================================
    */

    private static function obterInformacoesImagem(
        string $arquivoTemporario
    ): array {

        $informacoes =
            @getimagesize(
                $arquivoTemporario
            );

        if (
            $informacoes === false
        ) {
            throw new DomainException(
                'O arquivo enviado não é uma imagem válida.'
            );
        }

        return $informacoes;
    }


    private static function validarDimensoes(
        array $informacoes
    ): void {

        $largura =
            (int) $informacoes[0];

        $altura =
            (int) $informacoes[1];

        $totalPixels =
            $largura
            * $altura;

        if (
            $largura > self::LARGURA_MAXIMA
            ||
            $altura > self::ALTURA_MAXIMA
            ||
            $totalPixels >
                self::TOTAL_PIXELS_MAXIMO
        ) {
            throw new DomainException(
                'Imagem muito grande. '
                . 'Limite de 16 milhões de pixels '
                . 'e 6000 px por lado.'
            );
        }
    }


    /*
    =================================
    CRIA RECURSO GD
    =================================
    */

    private static function criarImagem(
        string $arquivoTemporario
    ): \GdImage {

        $conteudo =
            @file_get_contents(
                $arquivoTemporario
            );

        if (
            $conteudo === false
        ) {
            throw new DomainException(
                'Não foi possível ler a imagem.'
            );
        }

        $imagem =
            @imagecreatefromstring(
                $conteudo
            );

        if (
            $imagem === false
        ) {
            throw new DomainException(
                'Formato não suportado pelo servidor.'
            );
        }

        return $imagem;
    }


    /*
    =================================
    PREPARA IMAGEM PARA WEBP
    =================================
    */

    private static function prepararImagemParaWebp(
        \GdImage $imagem
    ): void {

        /*
        Imagens PNG de paleta precisam ser
        convertidas para true color antes
        de serem gravadas como WebP.
        */

        if (
            !imageistruecolor(
                $imagem
            )
        ) {
            imagepalettetotruecolor(
                $imagem
            );
        }

        /*
        Preserva canal alpha quando existir.
        */

        imagealphablending(
            $imagem,
            false
        );

        imagesavealpha(
            $imagem,
            true
        );
    }


    /*
    =================================
    DIRETÓRIO
    =================================
    */

    private static function obterDiretorioDestino(): string
    {
        return APP_ROOT
            . '/public/assets/uploads/temas';
    }


    private static function garantirDiretorio(
        string $diretorio
    ): void {

        if (
            is_dir(
                $diretorio
            )
        ) {
            return;
        }

        if (
            !mkdir(
                $diretorio,
                0755,
                true
            )
            &&
            !is_dir(
                $diretorio
            )
        ) {
            throw new DomainException(
                'Não foi possível preparar o diretório.'
            );
        }
    }


    /*
    =================================
    NOME DO ARQUIVO
    =================================
    */

    private static function gerarNomeArquivo(): string
    {
        return bin2hex(
            random_bytes(16)
        )
            . '.webp';
    }


    /*
    =================================
    SALVA WEBP
    =================================
    */

    private static function salvarComoWebp(
        \GdImage $imagem,
        string $arquivoDestino
    ): void {

        if (
            !function_exists(
                'imagewebp'
            )
        ) {
            imagedestroy(
                $imagem
            );

            throw new DomainException(
                'O servidor não possui suporte à conversão WebP.'
            );
        }

        $salvou =
            @imagewebp(
                $imagem,
                $arquivoDestino,
                self::QUALIDADE_WEBP
            );

        if (
            !$salvou
        ) {
            imagedestroy(
                $imagem
            );

            throw new DomainException(
                'Falha ao converter a imagem para WebP.'
            );
        }
    }


    /*
    =================================
    VERIFICA TRANSPARÊNCIA
    =================================
    */

    private static function possuiTransparencia(
        string $arquivoTemporario,
        string $mime
    ): bool {

        if (
            $mime === 'image/jpeg'
        ) {
            return false;
        }

        $conteudo =
            @file_get_contents(
                $arquivoTemporario
            );

        if (
            $conteudo === false
        ) {
            return false;
        }

        $imagem =
            @imagecreatefromstring(
                $conteudo
            );

        if (
            $imagem === false
        ) {
            return false;
        }

        if (
            !imageistruecolor(
                $imagem
            )
        ) {
            $transparente =
                imagecolortransparent(
                    $imagem
                )
                >= 0;

            imagedestroy(
                $imagem
            );

            return $transparente;
        }

        $largura =
            imagesx(
                $imagem
            );

        $altura =
            imagesy(
                $imagem
            );

        /*
        Faz amostragem da imagem em vez
        de testar obrigatoriamente milhões
        de pixels.
        */

        $passoX =
            max(
                1,
                (int) floor(
                    $largura / 100
                )
            );

        $passoY =
            max(
                1,
                (int) floor(
                    $altura / 100
                )
            );

        for (
            $y = 0;
            $y < $altura;
            $y += $passoY
        ) {

            for (
                $x = 0;
                $x < $largura;
                $x += $passoX
            ) {

                $cor =
                    imagecolorat(
                        $imagem,
                        $x,
                        $y
                    );

                $alpha =
                    ($cor >> 24)
                    & 0x7F;

                if (
                    $alpha > 0
                ) {
                    imagedestroy(
                        $imagem
                    );

                    return true;
                }
            }
        }

        imagedestroy(
            $imagem
        );

        return false;
    }
}