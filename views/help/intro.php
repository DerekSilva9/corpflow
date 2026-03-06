<div class="container-fluid">
    <h5><i class="fas fa-book me-2 text-primary"></i>Central de Ajuda</h5>
    <p class="text-muted">Bem-vindo à Central de Ajuda do CorpFlow.</p>

    <div class="list-group mt-3">
        <a href="<?= BASE_URL ?>/index.php?page=help&section=intro" class="list-group-item list-group-item-action">
            Introdução ao CorpFlow
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=help&section=tasks" class="list-group-item list-group-item-action">
            Como gerenciar tarefas
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=help&section=documents" class="list-group-item list-group-item-action">
            Upload de documentos
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=help&section=api" class="list-group-item list-group-item-action">
            Uso da API
        </a>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h6>Sobre o CorpFlow</h6>
            <p class="small text-muted">
                CorpFlow é uma plataforma de gerenciamento interno desenvolvida para times de médio e grande porte.
                Versão <?= APP_VERSION ?> — <?= date('Y') ?>.
            </p>
        </div>
    </div>
</div>
