<?php $pageTitle = 'Dashboard'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>

<div class="topbar">
    <div>
        <h4 class="mb-0 fw-bold">Olá, <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?> 👋</h4>
        <small class="text-muted">Visão geral do seu workspace</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/index.php?page=tasks" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Nova Tarefa
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-primary small fw-semibold text-uppercase">Abertas</div>
                    <div class="h2 fw-bold text-primary mb-0"><?= $taskStats['open'] ?? 0 ?></div>
                </div>
                <i class="fas fa-clipboard-list fa-2x text-primary opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#fef9c3,#fef08a);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-warning small fw-semibold text-uppercase">Em andamento</div>
                    <div class="h2 fw-bold text-warning mb-0"><?= $taskStats['in_progress'] ?? 0 ?></div>
                </div>
                <i class="fas fa-spinner fa-2x text-warning opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-success small fw-semibold text-uppercase">Concluídas</div>
                    <div class="h2 fw-bold text-success mb-0"><?= $taskStats['done'] ?? 0 ?></div>
                </div>
                <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#f3e8ff,#e9d5ff);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-purple small fw-semibold text-uppercase" style="color:#7c3aed">Total</div>
                    <div class="h2 fw-bold mb-0" style="color:#7c3aed"><?= count($tasks) ?></div>
                </div>
                <i class="fas fa-tasks fa-2x opacity-50" style="color:#7c3aed"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tarefas Recentes -->
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span><i class="fas fa-check-square me-2 text-primary"></i>Tarefas Recentes</span>
                <a href="<?= BASE_URL ?>/index.php?page=tasks" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($tasks)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                        Nenhuma tarefa ainda. <a href="<?= BASE_URL ?>/index.php?page=tasks">Crie uma!</a>
                    </div>
                <?php else: ?>
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-muted fw-semibold border-0">TÍTULO</th>
                            <th class="small text-muted fw-semibold border-0">PRIORIDADE</th>
                            <th class="small text-muted fw-semibold border-0">STATUS</th>
                            <th class="small text-muted fw-semibold border-0">DATA</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach (array_slice($tasks, 0, 5) as $task): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($task['title']) ?></td>
                            <td>
                                <?php $pColors = ['high'=>'danger','medium'=>'warning','low'=>'secondary']; ?>
                                <span class="badge bg-<?= $pColors[$task['priority']] ?? 'secondary' ?>">
                                    <?= htmlspecialchars($task['priority']) ?>
                                </span>
                            </td>
                            <td>
                                <?php $sColors = ['open'=>'primary','in_progress'=>'warning','done'=>'success']; ?>
                                <span class="badge bg-<?= $sColors[$task['status']] ?? 'secondary' ?>">
                                    <?= htmlspecialchars($task['status']) ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= date('d/m/Y', $task['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header py-3"><i class="fas fa-user me-2 text-primary"></i>Minha Conta</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:50px;height:50px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.1rem;">
                        <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <div class="fw-semibold"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></div>
                    </div>
                </div>
                <span class="badge bg-<?= ($_SESSION['user_role'] ?? '') === 'admin' ? 'danger' : 'primary' ?>">
                    <?= htmlspecialchars($_SESSION['user_role'] ?? '') ?>
                </span>
                <div class="mt-3">
                    <a href="<?= BASE_URL ?>/index.php?page=profile" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-user-edit me-1"></i> Editar Perfil
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3"><i class="fas fa-bolt me-2 text-warning"></i>Ações Rápidas</div>
            <div class="card-body d-grid gap-2">
                <a href="<?= BASE_URL ?>/index.php?page=documents" class="btn btn-sm btn-outline-secondary text-start">
                    <i class="fas fa-upload me-2"></i> Upload de Documento
                </a>
                <a href="<?= BASE_URL ?>/index.php?page=search" class="btn btn-sm btn-outline-secondary text-start">
                    <i class="fas fa-search me-2"></i> Buscar
                </a>
                <a href="<?= BASE_URL ?>/api/v1.php?action=health" class="btn btn-sm btn-outline-secondary text-start" target="_blank">
                    <i class="fas fa-heartbeat me-2"></i> Status da API
                </a>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layout_footer.php'; ?>
