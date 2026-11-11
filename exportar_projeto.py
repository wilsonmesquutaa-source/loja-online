from pathlib import Path
import os
import zipfile
from datetime import datetime


# ==========================================================
# CONFIGURAÇÕES
# ==========================================================

PASTA_PROJETO = Path(__file__).resolve().parent

PASTA_SAIDA = PASTA_PROJETO / "_exportacao_projeto"

PASTAS_IGNORADAS = {
    ".git",
    ".idea",
    ".vscode",
    "__pycache__",
    "node_modules",
    "vendor",
    "_exportacao_projeto",
}

ARQUIVOS_IGNORADOS = {
    ".env",
    ".DS_Store",
    "Thumbs.db",
}

EXTENSOES_TEXTO = {
    ".php",
    ".html",
    ".htm",
    ".css",
    ".scss",
    ".sass",
    ".js",
    ".ts",
    ".jsx",
    ".tsx",
    ".json",
    ".xml",
    ".sql",
    ".py",
    ".txt",
    ".md",
    ".yml",
    ".yaml",
    ".ini",
    ".conf",
    ".config",
    ".htaccess",
    ".gitignore",
    ".env.example",
    ".csv",
    ".sh",
    ".bat",
    ".ps1",
}

EXTENSOES_BINARIAS = {
    ".png",
    ".jpg",
    ".jpeg",
    ".gif",
    ".webp",
    ".ico",
    ".bmp",
    ".svgz",

    ".zip",
    ".rar",
    ".7z",
    ".tar",
    ".gz",

    ".pdf",

    ".exe",
    ".dll",
    ".so",

    ".mp3",
    ".wav",
    ".ogg",

    ".mp4",
    ".avi",
    ".mov",
    ".mkv",

    ".woff",
    ".woff2",
    ".ttf",
    ".otf",

    ".sqlite",
    ".db",
}


# ==========================================================
# FUNÇÕES AUXILIARES
# ==========================================================

def deve_ignorar_pasta(nome):
    return nome in PASTAS_IGNORADAS


def deve_ignorar_arquivo(nome):
    return nome in ARQUIVOS_IGNORADOS


def caminho_relativo(caminho):
    return caminho.relative_to(PASTA_PROJETO)


def tamanho_formatado(bytes_total):
    unidades = ["B", "KB", "MB", "GB"]

    tamanho = float(bytes_total)

    for unidade in unidades:
        if tamanho < 1024:
            return f"{tamanho:.2f} {unidade}"

        tamanho /= 1024

    return f"{tamanho:.2f} TB"


def arquivo_e_texto(caminho):
    nome = caminho.name.lower()
    extensao = caminho.suffix.lower()

    if extensao in EXTENSOES_BINARIAS:
        return False

    if extensao in EXTENSOES_TEXTO:
        return True

    if nome in {
        ".htaccess",
        ".gitignore",
        ".gitattributes",
        "dockerfile",
        "makefile",
        "composer.json",
        "composer.lock",
    }:
        return True

    try:
        with open(caminho, "rb") as arquivo:
            bloco = arquivo.read(4096)

        if b"\x00" in bloco:
            return False

        return True

    except Exception:
        return False


def ler_arquivo(caminho):
    codificacoes = [
        "utf-8",
        "utf-8-sig",
        "latin-1",
        "cp1252",
    ]

    for codificacao in codificacoes:
        try:
            with open(
                caminho,
                "r",
                encoding=codificacao
            ) as arquivo:

                return arquivo.read()

        except UnicodeDecodeError:
            continue

        except Exception as erro:
            return f"[ERRO AO LER ARQUIVO: {erro}]"

    return "[NÃO FOI POSSÍVEL DECODIFICAR O ARQUIVO]"


# ==========================================================
# ÁRVORE DO PROJETO
# ==========================================================

def gerar_arvore():
    linhas = []

    linhas.append(f"{PASTA_PROJETO.name}/")

    def percorrer(pasta, prefixo=""):

        itens = []

        try:
            for item in pasta.iterdir():

                if item.is_dir():

                    if deve_ignorar_pasta(item.name):
                        continue

                else:

                    if deve_ignorar_arquivo(item.name):
                        continue

                itens.append(item)

        except PermissionError:
            linhas.append(
                prefixo
                + "└── [SEM PERMISSÃO]"
            )

            return

        itens.sort(
            key=lambda x: (
                not x.is_dir(),
                x.name.lower()
            )
        )

        total = len(itens)

        for indice, item in enumerate(itens):

            ultimo = indice == total - 1

            conector = (
                "└── "
                if ultimo
                else "├── "
            )

            linhas.append(
                prefixo
                + conector
                + item.name
                + ("/" if item.is_dir() else "")
            )

            if item.is_dir():

                novo_prefixo = (
                    prefixo
                    + (
                        "    "
                        if ultimo
                        else "│   "
                    )
                )

                percorrer(
                    item,
                    novo_prefixo
                )

    percorrer(PASTA_PROJETO)

    return "\n".join(linhas)


# ==========================================================
# LISTAR ARQUIVOS
# ==========================================================

def listar_arquivos():

    arquivos = []

    for raiz, pastas, nomes in os.walk(PASTA_PROJETO):

        raiz_path = Path(raiz)

        pastas[:] = [
            pasta
            for pasta in pastas
            if not deve_ignorar_pasta(pasta)
        ]

        for nome in nomes:

            if deve_ignorar_arquivo(nome):
                continue

            caminho = raiz_path / nome

            if PASTA_SAIDA in caminho.parents:
                continue

            arquivos.append(caminho)

    arquivos.sort(
        key=lambda x: str(
            caminho_relativo(x)
        ).lower()
    )

    return arquivos


# ==========================================================
# RELATÓRIO
# ==========================================================

def gerar_relatorio(arvore, arquivos):

    agora = datetime.now()

    nome_relatorio = (
        "projeto_"
        + agora.strftime("%Y-%m-%d_%H-%M-%S")
        + ".txt"
    )

    caminho_relatorio = (
        PASTA_SAIDA
        / nome_relatorio
    )

    with open(
        caminho_relatorio,
        "w",
        encoding="utf-8"
    ) as relatorio:

        relatorio.write(
            "=" * 80
            + "\n"
        )

        relatorio.write(
            f"PROJETO: {PASTA_PROJETO.name}\n"
        )

        relatorio.write(
            f"CAMINHO: {PASTA_PROJETO}\n"
        )

        relatorio.write(
            "GERADO EM: "
            + agora.strftime(
                "%d/%m/%Y %H:%M:%S"
            )
            + "\n"
        )

        relatorio.write(
            "=" * 80
            + "\n\n"
        )

        # ------------------------------------------
        # ÁRVORE
        # ------------------------------------------

        relatorio.write(
            "ÁRVORE DO PROJETO\n"
        )

        relatorio.write(
            "=" * 80
            + "\n\n"
        )

        relatorio.write(arvore)

        relatorio.write(
            "\n\n"
            + "=" * 80
            + "\n"
        )

        relatorio.write(
            "CONTEÚDO DOS ARQUIVOS\n"
        )

        relatorio.write(
            "=" * 80
            + "\n\n"
        )

        # ------------------------------------------
        # CONTEÚDO
        # ------------------------------------------

        for caminho in arquivos:

            relativo = caminho_relativo(
                caminho
            )

            try:
                tamanho = caminho.stat().st_size

            except Exception:
                tamanho = 0

            relatorio.write(
                "\n"
                + "#" * 80
                + "\n"
            )

            relatorio.write(
                f"ARQUIVO: {relativo}\n"
            )

            relatorio.write(
                f"TAMANHO: "
                f"{tamanho_formatado(tamanho)}\n"
            )

            relatorio.write(
                "#" * 80
                + "\n\n"
            )

            if arquivo_e_texto(caminho):

                conteudo = ler_arquivo(
                    caminho
                )

                relatorio.write(conteudo)

            else:

                relatorio.write(
                    "[ARQUIVO BINÁRIO "
                    "OU NÃO TEXTUAL]\n"
                )

            relatorio.write("\n")

    return caminho_relatorio


# ==========================================================
# CRIAR ZIP
# ==========================================================

def criar_zip(arquivos):

    agora = datetime.now()

    nome_zip = (
        PASTA_PROJETO.name
        + "_"
        + agora.strftime(
            "%Y-%m-%d_%H-%M-%S"
        )
        + ".zip"
    )

    caminho_zip = (
        PASTA_SAIDA
        / nome_zip
    )

    with zipfile.ZipFile(
        caminho_zip,
        "w",
        compression=zipfile.ZIP_DEFLATED,
        compresslevel=9,
    ) as zipf:

        for caminho in arquivos:

            relativo = caminho_relativo(
                caminho
            )

            try:

                zipf.write(
                    caminho,
                    arcname=relativo
                )

            except Exception as erro:

                print(
                    f"Erro ao adicionar "
                    f"{relativo}: {erro}"
                )

    return caminho_zip


# ==========================================================
# ESTATÍSTICAS
# ==========================================================

def calcular_estatisticas(arquivos):

    quantidade = len(arquivos)

    tamanho_total = 0

    arquivos_texto = 0
    arquivos_binarios = 0

    for arquivo in arquivos:

        try:
            tamanho_total += (
                arquivo.stat().st_size
            )
        except Exception:
            pass

        if arquivo_e_texto(arquivo):
            arquivos_texto += 1
        else:
            arquivos_binarios += 1

    return {
        "quantidade": quantidade,
        "tamanho": tamanho_total,
        "texto": arquivos_texto,
        "binarios": arquivos_binarios,
    }


# ==========================================================
# PROGRAMA PRINCIPAL
# ==========================================================

def main():

    print()
    print("=" * 70)
    print("EXPORTADOR DE PROJETO")
    print("=" * 70)

    print()
    print(
        f"Projeto:"
        f"\n{PASTA_PROJETO}"
    )

    print()
    print(
        "Lendo arquivos..."
    )

    PASTA_SAIDA.mkdir(
        exist_ok=True
    )

    # ------------------------------------------
    # Árvore
    # ------------------------------------------

    arvore = gerar_arvore()

    print()
    print("=" * 70)
    print("ÁRVORE DO PROJETO")
    print("=" * 70)
    print()
    print(arvore)

    # ------------------------------------------
    # Arquivos
    # ------------------------------------------

    arquivos = listar_arquivos()

    estatisticas = (
        calcular_estatisticas(
            arquivos
        )
    )

    print()
    print("=" * 70)
    print("ESTATÍSTICAS")
    print("=" * 70)

    print(
        f"Arquivos: "
        f"{estatisticas['quantidade']}"
    )

    print(
        f"Arquivos de texto: "
        f"{estatisticas['texto']}"
    )

    print(
        f"Arquivos binários: "
        f"{estatisticas['binarios']}"
    )

    print(
        f"Tamanho total: "
        f"{tamanho_formatado(estatisticas['tamanho'])}"
    )

    # ------------------------------------------
    # Relatório
    # ------------------------------------------

    print()
    print(
        "Gerando relatório..."
    )

    caminho_relatorio = (
        gerar_relatorio(
            arvore,
            arquivos
        )
    )

    # ------------------------------------------
    # ZIP
    # ------------------------------------------

    print(
        "Criando ZIP..."
    )

    caminho_zip = criar_zip(
        arquivos
    )

    # ------------------------------------------
    # Final
    # ------------------------------------------

    print()
    print("=" * 70)
    print("CONCLUÍDO")
    print("=" * 70)

    print()
    print(
        "Relatório:"
    )

    print(
        caminho_relatorio
    )

    print()
    print(
        "ZIP:"
    )

    print(
        caminho_zip
    )

    print()
    print(
        "O arquivo .env foi ignorado "
        "por segurança."
    )

    print()


if __name__ == "__main__":
    main()