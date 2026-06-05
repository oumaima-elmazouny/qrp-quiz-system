</div> <footer class="mt-auto py-4 text-center" style="background: var(--card-bg); border-top: 1px solid var(--border); color: var(--muted); font-size: 0.85rem; font-weight: 600;">
        <div class="container">
            &copy; <?= date('Y') ?> 
            <span style="color: var(--primary); font-weight: 800;"><?= defined('SITE_NAME') ? SITE_NAME : "Quiz Révision" ?></span> 
            — Tous droits réservés
            <br>
            <small class="opacity-50 fw-normal">Mini Projet Web - QRP</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php 
        $js_path = file_exists('../js/script.js') ? '../js/script.js' : 'js/script.js';
    ?>
    <script src="<?= $js_path ?>"></script>

</body>
</html>