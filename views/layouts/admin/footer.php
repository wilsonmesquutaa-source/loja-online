</div>
<!-- FECHA CONTENT -->

<script src="<?= BASE_URL ?>/assets/js/admin/admin.js?v=<?= filemtime(APP_ROOT . '/public/assets/js/admin/admin.js') ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/admin/banner-zoom.js?v=<?= filemtime(APP_ROOT . '/public/assets/js/admin/banner-zoom.js') ?>"></script>
<?php foreach ($jsAdmin ?? [] as $arquivoJs): ?>
    <script src="<?= htmlspecialchars(BASE_URL . '/assets/js/admin/' . $arquivoJs, ENT_QUOTES, 'UTF-8') ?>?v=<?= filemtime(APP_ROOT . '/public/assets/js/admin/' . $arquivoJs) ?>"></script>
<?php endforeach; ?>

</body>

</html>
