<?php $layout = $layout ?? 'front'; ?>
      </main>
      <footer class="bg-white border-top py-3 mt-auto">
        <div class="<?= $layout === 'back' ? 'container-fluid px-4' : 'container' ?> text-muted small d-flex justify-content-between align-items-center">
          <span>Green Tunisia Association - Module Gestion des bénévoles</span>
          <span>© <?= date('Y') ?> Green Tunisia</span>
        </div>
      </footer>
<?php if ($layout === 'back'): ?>
    </div>
  </div>
<?php endif; ?>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>