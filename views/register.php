<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> — Criar Conta</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 100vh; display: flex; align-items: center; }
        .card { border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); padding: 2.5rem; width: 100%; max-width: 440px; border: none; }
        .brand h2 { font-weight: 800; color: #1e293b; }
        .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
        .btn-primary { background: #2563eb; border: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="card">
        <div class="brand text-center mb-4">
            <h2>⬡ CorpFlow</h2>
            <p class="text-muted small">Criar nova conta</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/index.php?page=register">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">NOME COMPLETO</label>
                <input type="text" name="name" class="form-control" placeholder="João Silva" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">EMAIL</label>
                <input type="email" name="email" class="form-control" placeholder="joao@empresa.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">SENHA</label>
                <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold text-muted">CONFIRMAR SENHA</label>
                <input type="password" name="password_confirm" class="form-control" placeholder="Repita a senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Criar Conta</button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/index.php?page=login" class="text-decoration-none small text-muted">← Voltar ao login</a>
        </div>
    </div>
</div>
</body>
</html>
