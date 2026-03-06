<?php $pageTitle = 'Relatório'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>

<div class="topbar">
    <div>
        <h4 class="mb-0 fw-bold">Relatório do Sistema</h4>
        <small class="text-muted">Gerado em <?= date('d/m/Y H:i') ?></small>
    </div>
    <div>
        <a href="<?= BASE_URL ?>/index.php?page=admin_report&type=detailed" class="btn btn-sm btn-outline-secondary me-1">Detalhado</a>
        <a href="<?= BASE_URL ?>/index.php?page=admin_report&type=security" class="btn btn-sm btn-outline-secondary">Segurança</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card text-center py-4">
            <div class="h2 fw-bold text-primary"><?= $userCount ?></div>
            <div class="text-muted">Usuários Ativos</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-4">
            <div class="h2 fw-bold text-warning"><?= $taskCount ?></div>
            <div class="text-muted">Tarefas Totais</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-4">
            <div class="h2 fw-bold text-success"><?= $docCount ?></div>
            <div class="text-muted">Documentos</div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">Informações do Sistema</div>
    <div class="card-body">
        <table class="table table-sm small">
            <tr><td class="text-muted">Aplicação</td><td><?= APP_NAME ?> v<?= APP_VERSION ?></td></tr>
            <tr><td class="text-muted">PHP</td><td><?= PHP_VERSION ?></td></tr>
            <tr><td class="text-muted">Servidor</td><td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></td></tr>
            <tr><td class="text-muted">Banco de dados</td><td>SQLite (<?= DB_PATH ?>)</td></tr>
            <tr><td class="text-muted">Pasta de uploads</td><td><?= UPLOAD_PATH ?></td></tr>
            <tr><td class="text-muted">Data/Hora</td><td><?= date('Y-m-d H:i:s') ?></td></tr>
        </table>
    </div>
</div>

<?php include BASE_PATH . '/views/layout_footer.php'; ?>
