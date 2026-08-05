    <footer class="footer-site py-5">

        <div class="container">

            <div
                class="d-flex flex-column flex-md-row
                       justify-content-between
                       align-items-md-center gap-3"
            >

                <div>

                    <p class="fw-bold mb-1">
                        Loja Online
                    </p>

                    <p class="text-secondary mb-0">
                        &copy;
                        <?= date('Y') ?>
                        — Projeto desenvolvido na UC12.
                    </p>

                </div>

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-voltar-topo
                >
                    <i
                        class="bi bi-arrow-up"
                        aria-hidden="true"
                    ></i>

                    Voltar ao topo
                </button>

            </div>

        </div>

    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    ></script>

    <script
        src="<?=
            htmlspecialchars(
                BASE_URL
                    . '/assets/js/site.js',
                ENT_QUOTES,
                'UTF-8'
            )
        ?>"
    ></script>

</body>
</html>
