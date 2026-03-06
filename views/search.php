<?php $pageTitle = 'Busca'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>

<div class="topbar">
    <div>
        <h4 class="mb-0 fw-bold">Busca Global</h4>
        <small class="text-muted">Pesquise por tarefas, usuários e documentos</small>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/index.php" class="d-flex gap-2">
            <input type="hidden" name="page" value="search">
            <input type="text" name="q" class="form-control" 
                   value="<?= htmlspecialchars($query ?? '') ?>" 
                   placeholder="Pesquisar..." autofocus>
            <select name="type" class="form-select" style="max-width:160px;">
                <option value="all" <?= ($type ?? '') === 'all' ? 'selected' : '' ?>>Tudo</option>
                <option value="tasks" <?= ($type ?? '') === 'tasks' ? 'selected' : '' ?>>Tarefas</option>
                <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                <option value="users" <?= ($type ?? '') === 'users' ? 'selected' : '' ?>>Usuários</option>
                <?php endif; ?>
                <option value="documents" <?= ($type ?? '') === 'documents' ? 'selected' : '' ?>>Documentos</option>
            </select>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (!empty($query) && empty($error)): ?>

<!-- Resultado da busca por: -->
<div class="mb-3">
    <small class="text-muted">
        Resultados para: <strong><?= htmlspecialchars($query) ?></strong>
        <?php
        $total = 0;
        foreach ($results as $r) $total += count($r);
        echo "($total resultado(s))";
        ?>
    </small>
</div>

<!-- Tarefas -->
<?php if (!empty($results['tasks'])): ?>
<div class="card mb-3">
    <div class="card-header"><i class="fas fa-tasks me-2 text-primary"></i>Tarefas (<?= count($results['tasks']) ?>)</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 small">
            <thead class="table-light">
                <tr><th>Título</th><th>Responsável</th><th>Status</th><th>Prioridade</th></tr>
            </thead>
            <tbody>
            <?php foreach ($results['tasks'] as $task): ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars($task['owner_name']) ?></td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($task['status']) ?></span></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($task['priority']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Usuários (somente admin) -->
<?php if (!empty($results['users'])): ?>
<div class="card mb-3">
    <div class="card-header"><i class="fas fa-users me-2 text-primary"></i>Usuários (<?= count($results['users']) ?>)</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 small">
            <thead class="table-light">
                <tr><th>Nome</th><th>Email</th><th>Função</th><th>Bio</th><th>Ações</th></tr>
            </thead>
            <tbody>
            <?php foreach ($results['users'] as $u): ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : 'primary' ?>"><?= $u['role'] ?></span></td>
                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?= htmlspecialchars(strip_tags($u['bio'])) ?>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/index.php?page=profile&id=<?= $u['id'] ?>" class="btn btn-xs btn-outline-primary btn-sm">Ver</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Documentos -->
<?php if (!empty($results['documents'])): ?>
<div class="card mb-3">
    <div class="card-header"><i class="fas fa-file me-2 text-primary"></i>Documentos (<?= count($results['documents']) ?>)</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 small">
            <thead class="table-light">
                <tr><th>Arquivo</th><th>Dono</th><th>Tipo</th><th>Tamanho</th><th>Ações</th></tr>
            </thead>
            <tbody>
            <?php foreach ($results['documents'] as $doc): ?>
                <tr>
                    <td><?= htmlspecialchars($doc['original_name']) ?></td>
                    <td><?= htmlspecialchars($doc['owner_name']) ?></td>
                    <td><span class="badge bg-secondary"><?= strtoupper($doc['file_type']) ?></span></td>
                    <td><?= round($doc['file_size']/1024, 1) ?> KB</td>
                    <td>
                        <a href="<?= BASE_URL ?>/index.php?page=document_view&id=<?= $doc['id'] ?>" class="btn btn-xs btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if ($total === 0): ?>
<div class="text-center py-4 text-muted">
    <i class="fas fa-search fa-2x mb-2 d-block opacity-25"></i>
    Nenhum resultado encontrado para "<?= htmlspecialchars($query) ?>"
</div>
<?php endif; ?>

<?php endif; ?>

<?php include BASE_PATH . '/views/layout_footer.php'; ?>
