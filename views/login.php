<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> — Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-card { background: #fff; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); padding: 2.5rem; width: 100%; max-width: 420px; }
        .brand { text-align: center; margin-bottom: 2rem; }
        .brand h2 { font-weight: 800; color: #1e293b; letter-spacing: -1px; }
        .brand .tagline { color: #64748b; font-size: 0.9rem; }
        .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
        .btn-primary { background: #2563eb; border: none; padding: 0.65rem; font-weight: 600; }
        .btn-primary:hover { background: #1d4ed8; }
        .divider { text-align: center; color: #94a3b8; font-size: 0.8rem; margin: 1rem 0; }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="login-card">
        <div class="brand">
            <h2>⬡ CorpFlow</h2>
            <p class="tagline">Plataforma de gestão interna</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (($_GET['msg'] ?? '') === 'registered'): ?>
            <div class="alert alert-success py-2">Conta criada! Faça login para continuar.</div>
        <?php elseif (($_GET['msg'] ?? '') === 'password_reset'): ?>
            <div class="alert alert-success py-2">Senha redefinida com sucesso!</div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/index.php?page=login">
            <div class="mb-3">
                <label class="form-label fw-semibold text-muted small">EMAIL</label>
                <input type="email" name="email" class="form-control" placeholder="seu@email.com" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-muted small">SENHA</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember_me" id="remember">
                    <label class="form-check-label text-muted small" for="remember">Lembrar de mim</label>
                </div>
                <a href="<?= BASE_URL ?>/index.php?page=reset_password" class="text-decoration-none small text-primary">Esqueci a senha</a>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>

        <div class="divider">── ou ──</div>

        <div class="text-center">
            <span class="text-muted small">Não tem conta? </span>
            <a href="<?= BASE_URL ?>/index.php?page=register" class="text-decoration-none text-primary small fw-semibold">Criar conta grátis</a>
        </div>

        <hr class="my-3">
        <p class="text-center text-muted" style="font-size:0.75rem;">
            &copy; <?= date('Y') ?> CorpFlow Inc. &mdash; v<?= APP_VERSION ?>
        </p>
    </div>
</div>
</body>
</html>
