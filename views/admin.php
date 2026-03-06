<?php $pageTitle = 'Administração'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>

<div class="topbar">
    <div>
        <h4 class="mb-0 fw-bold"><i class="fas fa-shield-alt me-2 text-danger"></i>Painel Administrativo</h4>
        <small class="text-muted">Gerenciamento da plataforma</small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/index.php?page=admin_report&type=summary" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-chart-bar me-1"></i> Relatório
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=admin_backup" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-database me-1"></i> Backup
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<!-- Estatísticas Globais -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="h3 fw-bold text-primary"><?= count($users) ?></div>
            <div class="text-muted small">Usuários</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="h3 fw-bold text-warning"><?= count($tasks) ?></div>
            <div class="text-muted small">Tarefas</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="h3 fw-bold text-success"><?= $taskStats['done'] ?? 0 ?></div>
            <div class="text-muted small">Concluídas</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="h3 fw-bold text-danger"><?= $taskStats['open'] ?? 0 ?></div>
            <div class="text-muted small">Em Aberto</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Usuários -->
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between py-3">
                <span><i class="fas fa-users me-2 text-primary"></i>Usuários</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th><th>Nome</th><th>Email</th><th>Função</th><th>Status</th><th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="text-muted"><?= $u['id'] ?></td>
                            <td class="fw-semibold">
                                <a href="<?= BASE_URL ?>/index.php?page=profile&id=<?= $u['id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($u['name']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $u['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $u['is_active'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/index.php?page=admin_user&action=toggle_role&id=<?= $u['id'] ?>" 
                                   class="btn btn-xs btn-outline-warning btn-sm" title="Alternar função">
                                    <i class="fas fa-user-shield"></i>
                                </a>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <a href="<?= BASE_URL ?>/index.php?page=admin_user&action=delete&id=<?= $u['id'] ?>" 
                                   class="btn btn-xs btn-outline-danger btn-sm"
                                   onclick="return confirm('Desativar usuário?')">
                                    <i class="fas fa-ban"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tarefas de todos os usuários -->
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-tasks me-2 text-primary"></i>Todas as Tarefas</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr><th>Título</th><th>Usuário</th><th>Status</th><th>Data</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach (array_slice($tasks, 0, 10) as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= htmlspecialchars($task['owner_name']) ?></td>
                            <td><span class="badge bg-primary"><?= $task['status'] ?></span></td>
                            <td class="text-muted"><?= date('d/m/Y', $task['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Audit Log -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between py-3">
                <span><i class="fas fa-history me-2 text-primary"></i>Audit Log</span>
                <a href="<?= BASE_URL ?>/index.php?page=admin_logs" class="btn btn-xs btn-outline-secondary btn-sm">Ver todos</a>
            </div>
            <div class="card-body p-0" style="max-height:400px;overflow-y:auto;">
                <table class="table table-hover mb-0 small">
                    <tbody>
                    <?php foreach ($auditLog as $log): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($log['action']) ?></div>
                                <div class="text-muted"><?= htmlspecialchars($log['user_name'] ?? 'Sistema') ?> · <?= htmlspecialchars($log['ip']) ?></div>
                                <div class="text-muted" style="font-size:0.7rem;"><?= date('d/m/Y H:i', $log['created_at']) ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ferramentas de Sistema -->
        <div class="card mt-3">
            <div class="card-header py-3"><i class="fas fa-tools me-2 text-warning"></i>Ferramentas do Sistema</div>
            <div class="card-body d-grid gap-2">
                <a href="<?= BASE_URL ?>/index.php?page=admin_logs&file=app.log" class="btn btn-sm btn-outline-secondary text-start">
                    <i class="fas fa-file-alt me-2"></i> Ver Logs da Aplicação
                </a>
                <a href="<?= BASE_URL ?>/index.php?page=diagnostics" class="btn btn-sm btn-outline-secondary text-start">
                    <i class="fas fa-network-wired me-2"></i> Diagnóstico de Rede
                </a>
                <a href="<?= BASE_URL ?>/index.php?page=admin_report&type=security" class="btn btn-sm btn-outline-secondary text-start">
                    <i class="fas fa-shield-alt me-2"></i> Relatório de Segurança
                </a>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layout_footer.php'; ?>
