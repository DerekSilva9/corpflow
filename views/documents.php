<?php $pageTitle = 'Documentos'; ?>
<?php include BASE_PATH . '/views/layout_header.php'; ?>

<div class="topbar">
    <div>
        <h4 class="mb-0 fw-bold">Meus Documentos</h4>
        <small class="text-muted">Arquivos e anexos</small>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="fas fa-upload me-1"></i> Upload
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($documents)): ?>
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted opacity-25 mb-3 d-block"></i>
                <h6 class="text-muted">Nenhum documento ainda</h6>
                <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    Fazer primeiro upload
                </button>
            </div>
        <?php else: ?>
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="small text-muted fw-semibold">ARQUIVO</th>
                    <th class="small text-muted fw-semibold">TIPO</th>
                    <th class="small text-muted fw-semibold">TAMANHO</th>
                    <th class="small text-muted fw-semibold">DESCRIÇÃO</th>
                    <th class="small text-muted fw-semibold">VISIBILIDADE</th>
                    <th class="small text-muted fw-semibold">DATA</th>
                    <th class="small text-muted fw-semibold">AÇÕES</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($documents as $doc): ?>
                <tr>
                    <td>
                        <i class="fas fa-file me-2 text-muted"></i>
                        <?= htmlspecialchars($doc['original_name']) ?>
                    </td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars(strtoupper($doc['file_type'])) ?></span></td>
                    <td class="text-muted small"><?= round($doc['file_size'] / 1024, 1) ?> KB</td>
                    <td class="text-muted small"><?= htmlspecialchars($doc['description']) ?></td>
                    <td>
                        <span class="badge bg-<?= $doc['is_public'] ? 'success' : 'secondary' ?>">
                            <?= $doc['is_public'] ? 'Público' : 'Privado' ?>
                        </span>
                    </td>
                    <td class="text-muted small"><?= date('d/m/Y', $doc['created_at']) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/index.php?page=document_view&id=<?= $doc['id'] ?>" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload de Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=documents" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">ARQUIVO</label>
                        <input type="file" name="document" class="form-control" required>
                        <div class="form-text">
                            Formatos: JPG, PNG, PDF, DOC, DOCX, TXT. Máx 10MB.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">DESCRIÇÃO (opcional)</label>
                        <input type="text" name="description" class="form-control" placeholder="Breve descrição do arquivo">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_public" class="form-check-input" id="isPublic">
                        <label class="form-check-label small" for="isPublic">
                            Tornar público (visível para toda equipe)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/views/layout_footer.php'; ?>
