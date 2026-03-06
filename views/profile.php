<?php $pageTitle = 'Perfil'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>

<div class="topbar">
    <div>
        <h4 class="mb-0 fw-bold">
            <?= $isOwnProfile ? 'Meu Perfil' : 'Perfil de ' . htmlspecialchars($profileUser['name']) ?>
        </h4>
        <small class="text-muted">Informações da conta</small>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <?php if (!empty($profileUser['avatar'])): ?>
                    <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($profileUser['avatar']) ?>" 
                         class="rounded-circle mb-3" style="width:90px;height:90px;object-fit:cover;" alt="Avatar">
                <?php else: ?>
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width:90px;height:90px;font-size:2rem;color:#fff;font-weight:700;">
                        <?= strtoupper(substr($profileUser['name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($profileUser['name']) ?></h5>
                <div class="text-muted small mb-2"><?= htmlspecialchars($profileUser['email']) ?></div>
                <span class="badge bg-<?= $profileUser['role'] === 'admin' ? 'danger' : 'primary' ?>">
                    <?= htmlspecialchars($profileUser['role']) ?>
                </span>
                <div class="mt-3 text-muted small">
                    Membro desde <?= date('d/m/Y', $profileUser['created_at']) ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-info-circle me-2 text-primary"></i>Sobre</div>
            <div class="card-body">
                <?php if (!empty($profileUser['bio'])): ?>
                    <!-- Bio pode conter formatação HTML para uso interno (ticket CF-177) -->
                    <div class="bio-content"><?= $profileUser['bio'] ?></div>
                <?php else: ?>
                    <span class="text-muted small">Nenhuma bio adicionada.</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <?php if ($isOwnProfile): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-user-edit me-2 text-primary"></i>Editar Perfil</div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/index.php?page=profile" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">NOME</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($profileUser['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">BIO
                            <span class="text-muted fw-normal">(suporta formatação HTML)</span>
                        </label>
                        <textarea name="bio" class="form-control" rows="4" placeholder="Fale um pouco sobre você..."><?= htmlspecialchars($profileUser['bio']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">FOTO DE PERFIL</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                        <div class="form-text">JPG, PNG ou GIF. Máx 10MB.</div>
                    </div>
                    <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">FUNÇÃO</label>
                        <select name="role" class="form-select">
                            <option value="user" <?= $profileUser['role'] === 'user' ? 'selected' : '' ?>>Usuário</option>
                            <option value="admin" <?= $profileUser['role'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Salvar Alterações
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header"><i class="fas fa-tasks me-2 text-primary"></i>Tarefas Recentes</div>
            <div class="card-body p-0">
                <?php if (empty($tasks)): ?>
                    <div class="text-muted text-center py-3 small">Nenhuma tarefa.</div>
                <?php else: ?>
                <table class="table table-hover mb-0 small">
                    <thead class="table-light"><tr><th>Título</th><th>Status</th><th>Data</th></tr></thead>
                    <tbody>
                    <?php foreach (array_slice($tasks, 0, 5) as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><span class="badge bg-primary"><?= htmlspecialchars($task['status']) ?></span></td>
                            <td class="text-muted"><?= date('d/m/Y', $task['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Links para perfis de outros usuários (funcionalidade de diretório de equipe) -->
        <div class="mt-3">
            <small class="text-muted">Ver perfil de outro usuário: 
                <code><?= BASE_URL ?>/index.php?page=profile&id={user_id}</code>
            </small>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layout_footer.php'; ?>
