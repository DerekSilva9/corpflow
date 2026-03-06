<?php $pageTitle = 'Logs'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>

<div class="topbar">
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Visualizador de Logs</h4>
        <small class="text-muted">Monitoramento da aplicação</small>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/index.php" class="d-flex gap-2 align-items-end">
            <input type="hidden" name="page" value="admin_logs">
            <div>
                <label class="form-label small fw-semibold text-muted">ARQUIVO DE LOG</label>
                <select name="file" class="form-select form-select-sm">
                    <?php foreach ($availableLogs as $logFile): ?>
                        <option value="<?= htmlspecialchars($logFile) ?>" <?= ($file ?? '') === $logFile ? 'selected' : '' ?>>
                            <?= htmlspecialchars($logFile) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Carregar</button>
        </form>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between py-3">
        <span><i class="fas fa-file-alt me-2"></i><?= htmlspecialchars($file ?? '') ?></span>
        <small class="text-muted">Últimas 500 linhas</small>
    </div>
    <div class="card-body p-0">
        <pre class="bg-dark text-light p-3 mb-0 small" style="max-height:500px;overflow-y:auto;border-radius:0 0 12px 12px;"><?= htmlspecialchars($content ?? '') ?></pre>
    </div>
</div>

<?php include BASE_PATH . '/views/layout_footer.php'; ?>
