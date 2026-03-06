<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> — Recuperar Senha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 100vh; display: flex; align-items: center; }
        .card { border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); border: none; max-width: 420px; width: 100%; }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="card p-4">
        <h4 class="fw-bold mb-1">Recuperar Senha</h4>
        <p class="text-muted small mb-4">CorpFlow — Redefinição de acesso</p>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-info py-2 small"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <?php if (!$showForm): ?>
        <!-- Etapa 1: Solicitar reset -->
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=reset_password">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">SEU EMAIL</label>
                <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar Instruções</button>
        </form>
        <?php else: ?>
        <!-- Etapa 2: Nova senha -->
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=reset_password">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">NOVA SENHA</label>
                <input type="password" name="new_password" class="form-control" placeholder="Mínimo 6 caracteres" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Redefinir Senha</button>
        </form>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/index.php?page=login" class="text-decoration-none small text-muted">← Voltar ao login</a>
        </div>
    </div>
</div>
</body>
</html>
