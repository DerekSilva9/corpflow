<?php $pageTitle = 'Tarefas'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>

<div class="topbar">
    <div>
        <h4 class="mb-0 fw-bold">Minhas Tarefas</h4>
        <small class="text-muted"><?= count($tasks) ?> tarefa(s) no total</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTaskModal">
        <i class="fas fa-plus me-1"></i> Nova Tarefa
    </button>
</div>

<?php if (empty($tasks)): ?>
<div class="card text-center py-5">
    <i class="fas fa-clipboard-list fa-3x text-muted opacity-25 mb-3"></i>
    <h5 class="text-muted">Nenhuma tarefa criada ainda</h5>
    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createTaskModal">
        Criar primeira tarefa
    </button>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="small text-muted fw-semibold">#</th>
                    <th class="small text-muted fw-semibold">TÍTULO</th>
                    <th class="small text-muted fw-semibold">DESCRIÇÃO</th>
                    <th class="small text-muted fw-semibold">PRIORIDADE</th>
                    <th class="small text-muted fw-semibold">STATUS</th>
                    <th class="small text-muted fw-semibold">DATA</th>
                    <th class="small text-muted fw-semibold">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td class="text-muted small">#<?= $task['id'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($task['title']) ?></td>
                    <td class="text-muted small" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <?= htmlspecialchars(strip_tags($task['description'])) ?>
                    </td>
                    <td>
                        <?php $pBadge = ['high'=>'danger','medium'=>'warning','low'=>'secondary']; ?>
                        <span class="badge bg-<?= $pBadge[$task['priority']] ?? 'secondary' ?>">
                            <?= htmlspecialchars($task['priority']) ?>
                        </span>
                    </td>
                    <td>
                        <?php $sBadge = ['open'=>'primary','in_progress'=>'warning','done'=>'success']; ?>
                        <span class="badge bg-<?= $sBadge[$task['status']] ?? 'secondary' ?>">
                            <?= htmlspecialchars($task['status']) ?>
                        </span>
                    </td>
                    <td class="text-muted small"><?= date('d/m/Y', $task['created_at']) ?></td>
                    <td>
                        <button class="btn btn-xs btn-outline-primary btn-sm" 
                                onclick="editTask(<?= $task['id'] ?>, '<?= addslashes($task['title']) ?>', '<?= addslashes($task['status']) ?>', '<?= addslashes($task['priority']) ?>')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Excluir tarefa?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            <button class="btn btn-xs btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Modal: Criar Tarefa -->
<div class="modal fade" id="createTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=tasks">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">TÍTULO</label>
                        <input type="text" name="title" class="form-control" required placeholder="Ex: Revisar relatório mensal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">DESCRIÇÃO</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Detalhes da tarefa..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">PRIORIDADE</label>
                        <select name="priority" class="form-select">
                            <option value="low">Baixa</option>
                            <option value="medium" selected>Média</option>
                            <option value="high">Alta</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Tarefa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Editar Tarefa -->
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=tasks">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="task_id" id="editTaskId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">TÍTULO</label>
                        <input type="text" name="title" id="editTaskTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">STATUS</label>
                        <select name="status" id="editTaskStatus" class="form-select">
                            <option value="open">Aberta</option>
                            <option value="in_progress">Em andamento</option>
                            <option value="done">Concluída</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">PRIORIDADE</label>
                        <select name="priority" id="editTaskPriority" class="form-select">
                            <option value="low">Baixa</option>
                            <option value="medium">Média</option>
                            <option value="high">Alta</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editTask(id, title, status, priority) {
    document.getElementById('editTaskId').value = id;
    document.getElementById('editTaskTitle').value = title;
    document.getElementById('editTaskStatus').value = status;
    document.getElementById('editTaskPriority').value = priority;
    new bootstrap.Modal(document.getElementById('editTaskModal')).show();
}
</script>

<?php include BASE_PATH . '/views/layout_footer.php'; ?>
