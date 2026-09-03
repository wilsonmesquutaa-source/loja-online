import threading
from pathlib import Path
import tkinter as tk
from tkinter import ttk, filedialog, messagebox

try:
    from PIL import Image, ImageOps
except ImportError:
    raise SystemExit(
        "A biblioteca Pillow não está instalada.\n\n"
        "Abra o PowerShell e execute:\n"
        "py -m pip install Pillow"
    )


# Extensões aceitas pelo programa
SUPPORTED_INPUT = {
    ".jpg",
    ".jpeg",
    ".png",
    ".webp"
}


class ImageConverterApp:

    def __init__(self, root):

        self.root = root

        self.root.title(
            "Compressor e Conversor de Imagens - V2"
        )

        self.root.geometry(
            "820x690"
        )

        self.root.minsize(
            760,
            620
        )

        # =====================================================
        # VARIÁVEIS
        # =====================================================

        self.input_dir = tk.StringVar()

        self.output_dir = tk.StringVar()

        self.quality = tk.IntVar(
            value=82
        )

        self.max_width = tk.IntVar(
            value=1600
        )

        self.max_height = tk.IntVar(
            value=1600
        )

        self.convert_to_webp = tk.BooleanVar(
            value=True
        )

        self.keep_structure = tk.BooleanVar(
            value=True
        )

        self.build_ui()

    # =========================================================
    # INTERFACE
    # =========================================================

    def build_ui(self):

        main = ttk.Frame(
            self.root,
            padding=14
        )

        main.pack(
            fill="both",
            expand=True
        )

        # -----------------------------------------------------
        # TÍTULO
        # -----------------------------------------------------

        ttk.Label(
            main,
            text="Compressor e Conversor de Imagens - V2",
            font=(
                "Segoe UI",
                18,
                "bold"
            )
        ).pack(
            anchor="w"
        )

        ttk.Label(
            main,
            text=(
                "Comprima imagens em lote e converta "
                "JPG/PNG automaticamente para WebP."
            )
        ).pack(
            anchor="w",
            pady=(2, 14)
        )

        # -----------------------------------------------------
        # PASTAS
        # -----------------------------------------------------

        folders = ttk.LabelFrame(
            main,
            text="Pastas",
            padding=10
        )

        folders.pack(
            fill="x",
            pady=(0, 10)
        )

        ttk.Label(
            folders,
            text="Imagens originais:"
        ).grid(
            row=0,
            column=0,
            sticky="w",
            pady=5
        )

        ttk.Entry(
            folders,
            textvariable=self.input_dir
        ).grid(
            row=0,
            column=1,
            sticky="ew",
            padx=8
        )

        ttk.Button(
            folders,
            text="Escolher...",
            command=self.choose_input
        ).grid(
            row=0,
            column=2
        )

        ttk.Label(
            folders,
            text="Salvar em:"
        ).grid(
            row=1,
            column=0,
            sticky="w",
            pady=5
        )

        ttk.Entry(
            folders,
            textvariable=self.output_dir
        ).grid(
            row=1,
            column=1,
            sticky="ew",
            padx=8
        )

        ttk.Button(
            folders,
            text="Escolher...",
            command=self.choose_output
        ).grid(
            row=1,
            column=2
        )

        folders.columnconfigure(
            1,
            weight=1
        )

        # -----------------------------------------------------
        # CONFIGURAÇÕES
        # -----------------------------------------------------

        options = ttk.LabelFrame(
            main,
            text="Configurações",
            padding=10
        )

        options.pack(
            fill="x",
            pady=(0, 10)
        )

        # Qualidade
        ttk.Label(
            options,
            text="Qualidade WebP:"
        ).grid(
            row=0,
            column=0,
            sticky="w"
        )

        self.quality_scale = ttk.Scale(
            options,
            from_=40,
            to=100,
            orient="horizontal",
            variable=self.quality,
            command=self.update_quality
        )

        self.quality_scale.grid(
            row=0,
            column=1,
            sticky="ew",
            padx=10
        )

        self.quality_label = ttk.Label(
            options,
            text="82"
        )

        self.quality_label.grid(
            row=0,
            column=2,
            sticky="w"
        )

        # Largura
        ttk.Label(
            options,
            text="Largura máxima:"
        ).grid(
            row=1,
            column=0,
            sticky="w",
            pady=7
        )

        ttk.Spinbox(
            options,
            from_=0,
            to=10000,
            textvariable=self.max_width,
            width=10
        ).grid(
            row=1,
            column=1,
            sticky="w",
            padx=10
        )

        ttk.Label(
            options,
            text="px (0 = manter tamanho)"
        ).grid(
            row=1,
            column=2,
            sticky="w"
        )

        # Altura
        ttk.Label(
            options,
            text="Altura máxima:"
        ).grid(
            row=2,
            column=0,
            sticky="w"
        )

        ttk.Spinbox(
            options,
            from_=0,
            to=10000,
            textvariable=self.max_height,
            width=10
        ).grid(
            row=2,
            column=1,
            sticky="w",
            padx=10
        )

        ttk.Label(
            options,
            text="px (0 = manter tamanho)"
        ).grid(
            row=2,
            column=2,
            sticky="w"
        )

        # Converter para WebP
        ttk.Checkbutton(
            options,
            text=(
                "Converter JPG/JPEG/PNG "
                "para WebP automaticamente"
            ),
            variable=self.convert_to_webp
        ).grid(
            row=3,
            column=0,
            columnspan=3,
            sticky="w",
            pady=(8, 4)
        )

        # Manter estrutura
        ttk.Checkbutton(
            options,
            text="Manter a estrutura das subpastas",
            variable=self.keep_structure
        ).grid(
            row=4,
            column=0,
            columnspan=3,
            sticky="w"
        )

        ttk.Label(
            options,
            text=(
                "Exemplo: produtos/coxinha.jpg "
                "→ produtos/coxinha.webp"
            )
        ).grid(
            row=5,
            column=0,
            columnspan=3,
            sticky="w",
            pady=(8, 0)
        )

        options.columnconfigure(
            1,
            weight=1
        )

        # -----------------------------------------------------
        # PROGRESSO
        # -----------------------------------------------------

        progress_frame = ttk.LabelFrame(
            main,
            text="Progresso",
            padding=10
        )

        progress_frame.pack(
            fill="both",
            expand=True,
            pady=(0, 10)
        )

        self.progress = ttk.Progressbar(
            progress_frame,
            mode="determinate",
            maximum=100
        )

        self.progress.pack(
            fill="x",
            pady=(0, 7)
        )

        self.status = ttk.Label(
            progress_frame,
            text="Pronto."
        )

        self.status.pack(
            anchor="w",
            pady=(0, 7)
        )

        self.log = tk.Text(
            progress_frame,
            height=15,
            state="disabled",
            font=(
                "Consolas",
                9
            )
        )

        self.log.pack(
            fill="both",
            expand=True
        )

        # -----------------------------------------------------
        # RODAPÉ
        # -----------------------------------------------------

        footer = ttk.Frame(
            main
        )

        footer.pack(
            fill="x"
        )

        ttk.Label(
            footer,
            text="Os arquivos originais não são alterados."
        ).pack(
            side="left"
        )

        self.start_button = ttk.Button(
            footer,
            text="▶ Processar imagens",
            command=self.start
        )

        self.start_button.pack(
            side="right"
        )

    # =========================================================
    # ESCOLHER PASTA DE ORIGEM
    # =========================================================

    def choose_input(self):

        folder = filedialog.askdirectory(
            title="Selecione a pasta com as imagens"
        )

        if folder:

            self.input_dir.set(
                folder
            )

            # Sugestão automática
            if not self.output_dir.get():

                self.output_dir.set(
                    str(
                        Path(folder) /
                        "webp_comprimidas"
                    )
                )

    # =========================================================
    # ESCOLHER DESTINO
    # =========================================================

    def choose_output(self):

        folder = filedialog.askdirectory(
            title="Selecione a pasta de destino"
        )

        if folder:

            self.output_dir.set(
                folder
            )

    # =========================================================
    # ATUALIZAR QUALIDADE
    # =========================================================

    def update_quality(self, value):

        self.quality_label.config(
            text=str(
                int(
                    float(value)
                )
            )
        )

    # =========================================================
    # LOG
    # =========================================================

    def add_log(self, text):

        self.log.config(
            state="normal"
        )

        self.log.insert(
            "end",
            text + "\n"
        )

        self.log.see(
            "end"
        )

        self.log.config(
            state="disabled"
        )

    # =========================================================
    # INICIAR
    # =========================================================

    def start(self):

        input_dir = Path(
            self.input_dir.get()
        ).expanduser()

        output_text = (
            self.output_dir
            .get()
            .strip()
        )

        # Verificar origem
        if not input_dir.is_dir():

            messagebox.showerror(
                "Erro",
                "Selecione uma pasta de origem válida."
            )

            return

        # Verificar destino
        if not output_text:

            messagebox.showerror(
                "Erro",
                "Selecione uma pasta de destino."
            )

            return

        output_dir = Path(
            output_text
        ).expanduser()

        # Origem e destino precisam ser diferentes
        try:

            if (
                input_dir.resolve()
                ==
                output_dir.resolve()
            ):

                messagebox.showerror(
                    "Erro",
                    (
                        "A pasta de destino deve ser "
                        "diferente da pasta original."
                    )
                )

                return

        except OSError:

            pass

        # Criar destino
        output_dir.mkdir(
            parents=True,
            exist_ok=True
        )

        # Desabilitar botão
        self.start_button.config(
            state="disabled"
        )

        # Resetar progresso
        self.progress["value"] = 0

        self.status.config(
            text="Procurando imagens..."
        )

        # Limpar log
        self.log.config(
            state="normal"
        )

        self.log.delete(
            "1.0",
            "end"
        )

        self.log.config(
            state="disabled"
        )

        # Executar em segundo plano
        threading.Thread(
            target=self.process_images,
            args=(
                input_dir,
                output_dir
            ),
            daemon=True
        ).start()

    # =========================================================
    # PROCESSAMENTO
    # =========================================================

    def process_images(
        self,
        input_dir,
        output_dir
    ):

        # Procurar todas as imagens
        files = [
            p
            for p in input_dir.rglob("*")
            if (
                p.is_file()
                and
                p.suffix.lower()
                in SUPPORTED_INPUT
            )
        ]

        total = len(files)

        # Nenhuma imagem
        if total == 0:

            self.root.after(
                0,
                lambda: self.status.config(
                    text=(
                        "Nenhuma imagem "
                        "JPG, PNG ou WebP encontrada."
                    )
                )
            )

            self.root.after(
                0,
                lambda: self.start_button.config(
                    state="normal"
                )
            )

            return

        # -----------------------------------------------------
        # CONFIGURAÇÕES
        # -----------------------------------------------------

        try:

            quality = max(
                40,
                min(
                    100,
                    int(
                        self.quality.get()
                    )
                )
            )

            max_w = max(
                0,
                int(
                    self.max_width.get()
                )
            )

            max_h = max(
                0,
                int(
                    self.max_height.get()
                )
            )

            convert_to_webp = bool(
                self.convert_to_webp.get()
            )

            keep_structure = bool(
                self.keep_structure.get()
            )

        except (
            ValueError,
            tk.TclError
        ):

            self.root.after(
                0,
                lambda: messagebox.showerror(
                    "Erro",
                    (
                        "Confira os valores de "
                        "qualidade e dimensões."
                    )
                )
            )

            self.root.after(
                0,
                lambda: self.start_button.config(
                    state="normal"
                )
            )

            return

        # -----------------------------------------------------
        # CONTADORES
        # -----------------------------------------------------

        total_before = 0
        total_after = 0

        converted_count = 0
        webp_count = 0
        failed = 0

        # =====================================================
        # PROCESSAR ARQUIVOS
        # =====================================================

        for index, source in enumerate(
            files,
            start=1
        ):

            try:

                # ---------------------------------------------
                # CAMINHO RELATIVO
                # ---------------------------------------------

                relative = source.relative_to(
                    input_dir
                )

                # ---------------------------------------------
                # DEFINIR NOME DO ARQUIVO
                # ---------------------------------------------

                if convert_to_webp:

                    # JPG/PNG/WebP -> WebP
                    destination = (
                        output_dir /
                        relative.with_suffix(
                            ".webp"
                        )
                    )

                else:

                    # Manter formato original
                    destination = (
                        output_dir /
                        relative
                    )

                # Criar diretório
                destination.parent.mkdir(
                    parents=True,
                    exist_ok=True
                )

                # ---------------------------------------------
                # TAMANHO ORIGINAL
                # ---------------------------------------------

                before = source.stat().st_size

                total_before += before

                # ---------------------------------------------
                # ABRIR IMAGEM
                # ---------------------------------------------

                with Image.open(
                    source
                ) as original:

                    # Corrigir orientação de fotos
                    # feitas por celulares
                    img = ImageOps.exif_transpose(
                        original
                    )

                    # -----------------------------------------
                    # REDIMENSIONAR
                    # -----------------------------------------

                    if (
                        max_w > 0
                        or
                        max_h > 0
                    ):

                        limit_w = (
                            max_w
                            if max_w > 0
                            else img.width
                        )

                        limit_h = (
                            max_h
                            if max_h > 0
                            else img.height
                        )

                        if (
                            img.width > limit_w
                            or
                            img.height > limit_h
                        ):

                            img.thumbnail(
                                (
                                    limit_w,
                                    limit_h
                                ),
                                Image.Resampling.LANCZOS
                            )

                    # -----------------------------------------
                    # CONVERTER PARA WEBP
                    # -----------------------------------------

                    if convert_to_webp:

                        # Preservar transparência
                        if (
                            "A"
                            not in
                            img.getbands()
                            and
                            img.mode
                            not in (
                                "RGB",
                                "L"
                            )
                        ):

                            img = img.convert(
                                "RGB"
                            )

                        img.save(
                            destination,
                            "WEBP",
                            quality=quality,
                            method=6
                        )

                        if source.suffix.lower() in {
                            ".jpg",
                            ".jpeg",
                            ".png"
                        }:

                            converted_count += 1

                        else:

                            webp_count += 1

                    # -----------------------------------------
                    # NÃO CONVERTER
                    # -----------------------------------------

                    else:

                        ext = (
                            source
                            .suffix
                            .lower()
                        )

                        # JPG
                        if ext in {
                            ".jpg",
                            ".jpeg"
                        }:

                            if img.mode not in {
                                "RGB",
                                "L"
                            }:

                                if (
                                    "A"
                                    in
                                    img.getbands()
                                ):

                                    background = (
                                        Image.new(
                                            "RGB",
                                            img.size,
                                            "white"
                                        )
                                    )

                                    background.paste(
                                        img,
                                        mask=img.getchannel(
                                            "A"
                                        )
                                    )

                                    img = background

                                else:

                                    img = img.convert(
                                        "RGB"
                                    )

                            img.save(
                                destination,
                                "JPEG",
                                quality=quality,
                                optimize=True,
                                progressive=True
                            )

                        # PNG
                        elif ext == ".png":

                            img.save(
                                destination,
                                "PNG",
                                optimize=True
                            )

                        # WebP
                        elif ext == ".webp":

                            img.save(
                                destination,
                                "WEBP",
                                quality=quality,
                                method=6
                            )

                # ---------------------------------------------
                # TAMANHO FINAL
                # ---------------------------------------------

                after = destination.stat().st_size

                total_after += after

                # ---------------------------------------------
                # CALCULAR REDUÇÃO
                # ---------------------------------------------

                if before:

                    reduction = (
                        1 -
                        after /
                        before
                    ) * 100

                else:

                    reduction = 0

                percent = (
                    index /
                    total
                ) * 100

                # ---------------------------------------------
                # LOG
                # ---------------------------------------------

                line = (
                    f"{index}/{total} | "
                    f"{source.name} → "
                    f"{destination.name} | "
                    f"{format_size(before)} → "
                    f"{format_size(after)} | "
                    f"{reduction:.1f}%"
                )

                self.root.after(
                    0,
                    lambda text=line:
                    self.add_log(text)
                )

                self.root.after(
                    0,
                    lambda p=percent:
                    self.progress.configure(
                        value=p
                    )
                )

                self.root.after(
                    0,
                    lambda i=index, t=total:
                    self.status.config(
                        text=(
                            f"Processando "
                            f"{i} de {t}..."
                        )
                    )
                )

            except Exception as exc:

                failed += 1

                line = (
                    f"ERRO | "
                    f"{source.name} | "
                    f"{exc}"
                )

                self.root.after(
                    0,
                    lambda text=line:
                    self.add_log(text)
                )

                self.root.after(
                    0,
                    lambda p=(index / total) * 100:
                    self.progress.configure(
                        value=p
                    )
                )

        # =====================================================
        # RESULTADO
        # =====================================================

        if total_before:

            reduction_total = (
                1 -
                total_after /
                total_before
            ) * 100

        else:

            reduction_total = 0

        successful = (
            total -
            failed
        )

        summary = (
            f"Processamento concluído!\n\n"

            f"Imagens encontradas: "
            f"{total}\n"

            f"Processadas com sucesso: "
            f"{successful}\n"

            f"Com erro: "
            f"{failed}\n\n"

            f"Convertidas para WebP: "
            f"{converted_count}\n"

            f"WebP processados: "
            f"{webp_count}\n\n"

            f"Tamanho original: "
            f"{format_size(total_before)}\n"

            f"Tamanho final: "
            f"{format_size(total_after)}\n"

            f"Redução total: "
            f"{reduction_total:.1f}%\n\n"

            f"Destino:\n"
            f"{output_dir}"
        )

        self.root.after(
            0,
            lambda: self.status.config(
                text=(
                    "Concluído! "
                    f"Redução total: "
                    f"{reduction_total:.1f}%"
                )
            )
        )

        self.root.after(
            0,
            lambda: self.add_log(
                "\n" +
                summary
            )
        )

        self.root.after(
            0,
            lambda: self.start_button.config(
                state="normal"
            )
        )

        self.root.after(
            0,
            lambda: messagebox.showinfo(
                "Concluído",
                summary
            )
        )


# =============================================================
# FORMATAR TAMANHO
# =============================================================

def format_size(
    value
):

    size = float(
        value
    )

    units = [
        "B",
        "KB",
        "MB",
        "GB",
        "TB"
    ]

    for unit in units:

        if (
            size < 1024
            or
            unit == units[-1]
        ):

            return (
                f"{size:.2f} "
                f"{unit}"
            )

        size /= 1024

    return (
        f"{size:.2f} TB"
    )


# =============================================================
# INICIAR PROGRAMA
# =============================================================

if __name__ == "__main__":

    root = tk.Tk()

    # Melhor escala em telas modernas
    try:

        root.tk.call(
            "tk",
            "scaling",
            1.1
        )

    except tk.TclError:

        pass

    app = ImageConverterApp(
        root
    )

    root.mainloop()