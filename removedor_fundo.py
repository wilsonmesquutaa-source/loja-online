import cv2
import numpy as np
import subprocess
import threading
import shutil
import os
import tkinter as tk
from tkinter import filedialog, messagebox
from tkinter import ttk


class RemovedorFundoApp:

    def __init__(self, root):
        self.root = root

        self.root.title("Removedor de Fundo Verde")
        self.root.geometry("760x650")
        self.root.minsize(700, 600)

        self.video_path = None
        self.output_path = None
        self.processando = False

        self.criar_interface()

    # =========================================================
    # INTERFACE
    # =========================================================

    def criar_interface(self):

        # TÍTULO
        titulo = tk.Label(
            self.root,
            text="🎬 Removedor de Fundo Verde",
            font=("Segoe UI", 20, "bold")
        )
        titulo.pack(pady=(20, 5))

        subtitulo = tk.Label(
            self.root,
            text="Chroma Key → WebM VP9 com transparência",
            font=("Segoe UI", 10)
        )
        subtitulo.pack(pady=(0, 20))

        # =====================================================
        # VÍDEO
        # =====================================================

        frame_video = ttk.LabelFrame(
            self.root,
            text="1. Vídeo de entrada",
            padding=15
        )
        frame_video.pack(
            fill="x",
            padx=25,
            pady=5
        )

        self.label_video = ttk.Label(
            frame_video,
            text="Nenhum vídeo selecionado"
        )
        self.label_video.pack(
            side="left",
            fill="x",
            expand=True
        )

        ttk.Button(
            frame_video,
            text="📁 Selecionar vídeo",
            command=self.selecionar_video
        ).pack(side="right")

        # =====================================================
        # CHROMA KEY
        # =====================================================

        frame_config = ttk.LabelFrame(
            self.root,
            text="2. Configurações do Chroma Key",
            padding=15
        )
        frame_config.pack(
            fill="x",
            padx=25,
            pady=15
        )

        # -------------------------
        # TOLERÂNCIA
        # -------------------------

        ttk.Label(
            frame_config,
            text="Tolerância do verde:"
        ).grid(
            row=0,
            column=0,
            sticky="w"
        )

        self.tolerancia = tk.IntVar(
            value=60
        )

        slider_tolerancia = ttk.Scale(
            frame_config,
            from_=10,
            to=120,
            variable=self.tolerancia,
            orient="horizontal",
            command=self.atualizar_valores
        )

        slider_tolerancia.grid(
            row=0,
            column=1,
            padx=15,
            sticky="ew"
        )

        self.label_tolerancia = ttk.Label(
            frame_config,
            text="60",
            width=5
        )

        self.label_tolerancia.grid(
            row=0,
            column=2
        )

        # -------------------------
        # SUAVIZAÇÃO
        # -------------------------

        ttk.Label(
            frame_config,
            text="Suavização da borda:"
        ).grid(
            row=1,
            column=0,
            sticky="w",
            pady=15
        )

        self.suavizacao = tk.IntVar(
            value=8
        )

        slider_suavizacao = ttk.Scale(
            frame_config,
            from_=0,
            to=25,
            variable=self.suavizacao,
            orient="horizontal",
            command=self.atualizar_valores
        )

        slider_suavizacao.grid(
            row=1,
            column=1,
            padx=15,
            sticky="ew"
        )

        self.label_suavizacao = ttk.Label(
            frame_config,
            text="8",
            width=5
        )

        self.label_suavizacao.grid(
            row=1,
            column=2
        )

        # -------------------------
        # DESPILL
        # -------------------------

        self.despill = tk.BooleanVar(
            value=True
        )

        ttk.Checkbutton(
            frame_config,
            text="Remover reflexo verde das bordas (Despill)",
            variable=self.despill
        ).grid(
            row=2,
            column=0,
            columnspan=3,
            sticky="w",
            pady=(5, 0)
        )

        frame_config.columnconfigure(
            1,
            weight=1
        )

        # =====================================================
        # EXPLICAÇÃO
        # =====================================================

        frame_info = ttk.LabelFrame(
            self.root,
            text="Ajuste rápido",
            padding=12
        )

        frame_info.pack(
            fill="x",
            padx=25,
            pady=5
        )

        ttk.Label(
            frame_info,
            text=(
                "Comece com tolerância 60. "
                "Se ainda aparecer verde, aumente aos poucos.\n"
                "Se partes do objeto desaparecerem, diminua a tolerância."
            ),
            justify="left"
        ).pack(anchor="w")

        # =====================================================
        # SAÍDA
        # =====================================================

        frame_saida = ttk.LabelFrame(
            self.root,
            text="3. Arquivo de saída",
            padding=15
        )

        frame_saida.pack(
            fill="x",
            padx=25,
            pady=15
        )

        self.label_saida = ttk.Label(
            frame_saida,
            text="O destino será escolhido ao iniciar a conversão."
        )

        self.label_saida.pack(
            anchor="w"
        )

        # =====================================================
        # PROGRESSO
        # =====================================================

        self.progresso = ttk.Progressbar(
            self.root,
            orient="horizontal",
            mode="determinate",
            maximum=100
        )

        self.progresso.pack(
            fill="x",
            padx=25,
            pady=(15, 5)
        )

        self.label_status = ttk.Label(
            self.root,
            text="Aguardando vídeo..."
        )

        self.label_status.pack(
            pady=(0, 10)
        )

        # =====================================================
        # BOTÃO COMEÇAR
        # =====================================================

        self.botao_converter = ttk.Button(
            self.root,
            text="▶ COMEÇAR CONVERSÃO",
            command=self.iniciar_conversao
        )

        self.botao_converter.pack(
            ipadx=35,
            ipady=12,
            pady=15
        )

    # =========================================================
    # ATUALIZAR NÚMEROS DOS SLIDERS
    # =========================================================

    def atualizar_valores(self, _=None):

        self.label_tolerancia.config(
            text=str(
                int(self.tolerancia.get())
            )
        )

        self.label_suavizacao.config(
            text=str(
                int(self.suavizacao.get())
            )
        )

    # =========================================================
    # SELECIONAR VÍDEO
    # =========================================================

    def selecionar_video(self):

        arquivo = filedialog.askopenfilename(
            title="Selecione o vídeo com fundo verde",
            filetypes=[
                (
                    "Arquivos de vídeo",
                    "*.mp4 *.mov *.avi *.mkv *.webm"
                ),
                (
                    "Todos os arquivos",
                    "*.*"
                )
            ]
        )

        if not arquivo:
            return

        self.video_path = arquivo

        nome = os.path.basename(
            arquivo
        )

        self.label_video.config(
            text=nome
        )

        self.label_status.config(
            text="Vídeo carregado. Configure o chroma key e clique em COMEÇAR."
        )

        self.progresso["value"] = 0

    # =========================================================
    # REMOÇÃO DO VERDE
    # =========================================================

    def remover_verde(
        self,
        frame,
        tolerancia,
        suavizacao,
        usar_despill
    ):

        # -----------------------------------------------------
        # Converte BGR para HSV
        # -----------------------------------------------------

        hsv = cv2.cvtColor(
            frame,
            cv2.COLOR_BGR2HSV
        )

        h, s, v = cv2.split(
            hsv
        )

        # -----------------------------------------------------
        # DETECÇÃO DO VERDE
        #
        # No OpenCV:
        # H = 0 até 179
        #
        # Verde puro fica aproximadamente em H = 60
        # -----------------------------------------------------

        amplitude_h = max(
            10,
            int(tolerancia * 0.40)
        )

        h_min = max(
            25,
            60 - amplitude_h
        )

        h_max = min(
            95,
            60 + amplitude_h
        )

        # Quanto maior a tolerância,
        # menor a saturação necessária para ser considerado verde.

        saturacao_minima = max(
            25,
            130 - tolerancia
        )

        valor_minimo = max(
            20,
            100 - int(tolerancia * 0.6)
        )

        lower_green = np.array(
            [
                h_min,
                saturacao_minima,
                valor_minimo
            ],
            dtype=np.uint8
        )

        upper_green = np.array(
            [
                h_max,
                255,
                255
            ],
            dtype=np.uint8
        )

        mask_green = cv2.inRange(
            hsv,
            lower_green,
            upper_green
        )

        # -----------------------------------------------------
        # LIMPEZA DA MÁSCARA
        # -----------------------------------------------------

        kernel_morfologia = np.ones(
            (3, 3),
            np.uint8
        )

        mask_green = cv2.morphologyEx(
            mask_green,
            cv2.MORPH_OPEN,
            kernel_morfologia
        )

        mask_green = cv2.morphologyEx(
            mask_green,
            cv2.MORPH_CLOSE,
            kernel_morfologia
        )

        # -----------------------------------------------------
        # SUAVIZAÇÃO DAS BORDAS
        # -----------------------------------------------------

        if suavizacao > 0:

            tamanho_kernel = (
                suavizacao * 2
            ) + 1

            mask_green = cv2.GaussianBlur(
                mask_green,
                (
                    tamanho_kernel,
                    tamanho_kernel
                ),
                0
            )

        # -----------------------------------------------------
        # ALPHA
        #
        # 255 = visível
        # 0   = transparente
        # -----------------------------------------------------

        alpha = (
            255 - mask_green
        )

        frame_processado = (
            frame.copy()
        )

        # -----------------------------------------------------
        # DESPILL
        # Remove contaminação verde das bordas.
        # -----------------------------------------------------

        if usar_despill:

            b, g, r = cv2.split(
                frame_processado
            )

            b16 = b.astype(
                np.int16
            )

            g16 = g.astype(
                np.int16
            )

            r16 = r.astype(
                np.int16
            )

            media_rb = (
                b16 + r16
            ) // 2

            excesso_verde = (
                g16 - media_rb
            )

            excesso_verde = np.clip(
                excesso_verde,
                0,
                255
            )

            # Só aplica despill onde o pixel
            # ainda possui alguma visibilidade.

            fator_alpha = (
                alpha.astype(np.float32)
                /
                255.0
            )

            reducao = (
                excesso_verde.astype(
                    np.float32
                )
                *
                0.75
                *
                fator_alpha
            )

            g_corrigido = (
                g16.astype(
                    np.float32
                )
                -
                reducao
            )

            g_corrigido = np.clip(
                g_corrigido,
                0,
                255
            ).astype(
                np.uint8
            )

            frame_processado = cv2.merge(
                [
                    b,
                    g_corrigido,
                    r
                ]
            )

        # -----------------------------------------------------
        # BGRA
        # -----------------------------------------------------

        b, g, r = cv2.split(
            frame_processado
        )

        bgra = cv2.merge(
            [
                b,
                g,
                r,
                alpha
            ]
        )

        return bgra

    # =========================================================
    # INICIAR CONVERSÃO
    # =========================================================

    def iniciar_conversao(self):

        if self.processando:
            return

        if not self.video_path:

            messagebox.showwarning(
                "Vídeo não selecionado",
                "Primeiro selecione um vídeo."
            )

            return

        # -----------------------------------------------------
        # VERIFICAR FFMPEG
        # -----------------------------------------------------

        if shutil.which("ffmpeg") is None:

            messagebox.showerror(
                "FFmpeg não encontrado",
                (
                    "O FFmpeg não foi encontrado no Windows.\n\n"
                    "Abra o terminal e execute:\n\n"
                    "ffmpeg -version\n\n"
                    "Se o comando não funcionar, precisamos instalar "
                    "ou configurar o FFmpeg."
                )
            )

            return

        # -----------------------------------------------------
        # ESCOLHER DESTINO
        # -----------------------------------------------------

        nome_original = os.path.splitext(
            os.path.basename(
                self.video_path
            )
        )[0]

        destino = filedialog.asksaveasfilename(
            title="Salvar vídeo transparente",
            defaultextension=".webm",
            initialfile=(
                f"{nome_original}_transparente.webm"
            ),
            filetypes=[
                (
                    "Vídeo WebM",
                    "*.webm"
                )
            ]
        )

        if not destino:
            return

        self.output_path = destino

        self.label_saida.config(
            text=destino
        )

        self.progresso[
            "value"
        ] = 0

        # -----------------------------------------------------
        # Copia configurações antes da thread
        # -----------------------------------------------------

        tolerancia = int(
            self.tolerancia.get()
        )

        suavizacao = int(
            self.suavizacao.get()
        )

        usar_despill = bool(
            self.despill.get()
        )

        # -----------------------------------------------------
        # THREAD
        # -----------------------------------------------------

        thread = threading.Thread(
            target=self.converter_video,
            args=(
                tolerancia,
                suavizacao,
                usar_despill
            ),
            daemon=True
        )

        thread.start()

    # =========================================================
    # CONVERTER
    # =========================================================

    def converter_video(
        self,
        tolerancia,
        suavizacao,
        usar_despill
    ):

        self.processando = True

        self.root.after(
            0,
            lambda: self.botao_converter.config(
                state="disabled"
            )
        )

        self.root.after(
            0,
            lambda: self.label_status.config(
                text="Abrindo vídeo..."
            )
        )

        cap = cv2.VideoCapture(
            self.video_path
        )

        if not cap.isOpened():

            self.finalizar_erro(
                "Não foi possível abrir o vídeo."
            )

            return

        fps = cap.get(
            cv2.CAP_PROP_FPS
        )

        largura = int(
            cap.get(
                cv2.CAP_PROP_FRAME_WIDTH
            )
        )

        altura = int(
            cap.get(
                cv2.CAP_PROP_FRAME_HEIGHT
            )
        )

        total_frames = int(
            cap.get(
                cv2.CAP_PROP_FRAME_COUNT
            )
        )

        if fps <= 0:
            fps = 30

        # =====================================================
        # FFMPEG
        # =====================================================

        comando = [

            "ffmpeg",

            "-y",

            # Vídeo vindo do Python
            "-f",
            "rawvideo",

            "-vcodec",
            "rawvideo",

            "-pix_fmt",
            "bgra",

            "-s",
            f"{largura}x{altura}",

            "-r",
            str(fps),

            "-i",
            "-",

            # Sem áudio nesta versão
            "-an",

            # Codec VP9
            "-c:v",
            "libvpx-vp9",

            # Canal alpha
            "-pix_fmt",
            "yuva420p",

            # Qualidade
            "-crf",
            "25",

            "-b:v",
            "0",

            # Importante para transparência VP9
            "-auto-alt-ref",
            "0",

            self.output_path
        ]

        try:

            processo = subprocess.Popen(
                comando,
                stdin=subprocess.PIPE,
                stdout=subprocess.DEVNULL,
                stderr=subprocess.PIPE
            )

        except Exception as erro:

            cap.release()

            self.finalizar_erro(
                f"Erro ao iniciar FFmpeg:\n\n{erro}"
            )

            return

        frame_atual = 0

        try:

            while True:

                ret, frame = cap.read()

                if not ret:
                    break

                # ---------------------------------------------
                # REMOVER VERDE
                # ---------------------------------------------

                frame_alpha = self.remover_verde(
                    frame,
                    tolerancia,
                    suavizacao,
                    usar_despill
                )

                # ---------------------------------------------
                # ENVIAR PARA FFMPEG
                # ---------------------------------------------

                processo.stdin.write(
                    frame_alpha.tobytes()
                )

                frame_atual += 1

                # ---------------------------------------------
                # PROGRESSO
                # ---------------------------------------------

                if total_frames > 0:

                    percentual = (
                        frame_atual
                        /
                        total_frames
                    ) * 100

                    self.root.after(
                        0,
                        self.atualizar_progresso,
                        percentual,
                        frame_atual,
                        total_frames
                    )

            cap.release()

            processo.stdin.close()

            codigo_saida = processo.wait()

            # =================================================
            # VERIFICAR FFMPEG
            # =================================================

            if codigo_saida != 0:

                erro_ffmpeg = (
                    processo.stderr
                    .read()
                    .decode(
                        "utf-8",
                        errors="ignore"
                    )
                )

                self.finalizar_erro(
                    (
                        "O FFmpeg encontrou um erro:\n\n"
                        +
                        erro_ffmpeg[-2000:]
                    )
                )

                return

            self.root.after(
                0,
                self.conversao_concluida
            )

        except BrokenPipeError:

            cap.release()

            erro_ffmpeg = ""

            try:

                erro_ffmpeg = (
                    processo.stderr
                    .read()
                    .decode(
                        "utf-8",
                        errors="ignore"
                    )
                )

            except Exception:
                pass

            self.finalizar_erro(
                (
                    "O FFmpeg interrompeu a conversão.\n\n"
                    +
                    erro_ffmpeg[-2000:]
                )
            )

        except Exception as erro:

            cap.release()

            try:

                processo.stdin.close()

            except Exception:
                pass

            try:

                processo.kill()

            except Exception:
                pass

            self.finalizar_erro(
                f"Erro durante a conversão:\n\n{erro}"
            )

    # =========================================================
    # PROGRESSO
    # =========================================================

    def atualizar_progresso(
        self,
        percentual,
        atual,
        total
    ):

        self.progresso[
            "value"
        ] = percentual

        self.label_status.config(
            text=(
                f"Processando {atual:,} de {total:,} frames "
                f"• {percentual:.1f}%"
            )
        )

    # =========================================================
    # CONCLUÍDO
    # =========================================================

    def conversao_concluida(self):

        self.processando = False

        self.botao_converter.config(
            state="normal"
        )

        self.progresso[
            "value"
        ] = 100

        self.label_status.config(
            text="✓ Conversão concluída!"
        )

        messagebox.showinfo(
            "Conversão concluída",
            (
                "O vídeo foi convertido.\n\n"
                "Formato: WebM\n"
                "Codec: VP9\n"
                "Transparência: Alpha\n\n"
                f"Arquivo:\n{self.output_path}"
            )
        )

    # =========================================================
    # ERRO
    # =========================================================

    def finalizar_erro(
        self,
        mensagem
    ):

        self.root.after(
            0,
            self._mostrar_erro,
            mensagem
        )

    def _mostrar_erro(
        self,
        mensagem
    ):

        self.processando = False

        self.botao_converter.config(
            state="normal"
        )

        self.label_status.config(
            text="✕ Erro durante a conversão."
        )

        messagebox.showerror(
            "Erro",
            mensagem
        )


# =============================================================
# INICIAR PROGRAMA
# =============================================================

if __name__ == "__main__":

    root = tk.Tk()

    app = RemovedorFundoApp(
        root
    )

    root.mainloop()