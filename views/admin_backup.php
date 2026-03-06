<?php $pageTitle = 'Backup'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>

<div class="topbar">
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-database me-2"></i>Backup do Banco de Dados</h4>
        <small class="text-muted">Gestão de backups do sistema</small>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-plus me-2"></i>Gerar Novo Backup</div>
            <div class="card-body">
                <p class="text-muted small">Gera um dump SQL completo do banco de dados atual.</p>
                <div class="alert alert-warning py-2 small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    O backup inclui todos os dados, incluindo senhas hasheadas e tokens.
                </div>
                <a href="<?= BASE_URL ?>/index.php?page=admin_backup&confirm=yes" 
                   class="btn btn-primary"
                   onclick="return confirm('Iniciar backup agora?')">
                    <i class="fas fa-download me-1"></i> Gerar Backup Agora
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-folder me-2"></i>Backups Disponíveis</div>
            <div class="card-body">
                <?php
                $backupFiles = glob(BASE_PATH . '/backups/*.sql') ?: [];
                if (empty($backupFiles)):
                ?>
                    <p class="text-muted small">Nenhum backup encontrado.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                    <?php foreach (array_reverse($backupFiles) as $bf): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <i class="fas fa-file-code me-2 text-muted"></i>
                                <span class="small"><?= htmlspecialchars(basename($bf)) ?></span>
                                <div class="text-muted" style="font-size:0.7rem;"><?= round(filesize($bf)/1024, 1) ?> KB</div>
                            </div>
                            <a href="<?= BASE_URL ?>/backups/<?= htmlspecialchars(basename($bf)) ?>" class="btn btn-xs btn-outline-primary btn-sm">
                                <i class="fas fa-download"></i>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layout_footer.php'; ?>
