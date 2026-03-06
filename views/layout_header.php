<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> <?= isset($pageTitle) ? '— ' . htmlspecialchars($pageTitle) : '' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --cf-primary: #2563eb;
            --cf-sidebar: #1e293b;
            --cf-accent: #3b82f6;
        }
        body { background: #f1f5f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .sidebar {
            width: 260px; min-height: 100vh; background: var(--cf-sidebar);
            position: fixed; left: 0; top: 0; z-index: 100; padding-top: 0;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem; background: rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand h4 { color: #fff; margin: 0; font-weight: 700; letter-spacing: -0.5px; }
        .sidebar-brand small { color: #94a3b8; font-size: 0.75rem; }
        .sidebar .nav-link {
            color: #cbd5e1; padding: 0.65rem 1.25rem; border-radius: 0; 
            display: flex; align-items: center; gap: 0.65rem; font-size: 0.9rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { 
            color: #fff; background: rgba(255,255,255,0.08); 
        }
        .sidebar .nav-link i { width: 18px; text-align: center; opacity: 0.7; }
        .sidebar .nav-section {
            font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;
            color: #475569; padding: 1rem 1.25rem 0.35rem;
        }
        .main-content { margin-left: 260px; padding: 2rem; min-height: 100vh; }
        .topbar {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;
        }
        .card { border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; font-weight: 600; }
        .badge-role-admin { background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; }
        .badge-role-user { background: #dbeafe; color: #2563eb; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; }
        .stat-card { border-radius: 12px; padding: 1.5rem; }
        .user-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .sidebar-user { padding: 1rem 1.25rem; border-top: 1px solid rgba(255,255,255,0.08); position: absolute; bottom: 0; left: 0; right: 0; }
        code { background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 0.85em; color: #dc2626; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="fas fa-layer-group me-2" style="color:var(--cf-accent)"></i><?= APP_NAME ?></h4>
        <small>v<?= APP_VERSION ?> — Workspace</small>
    </div>

    <div class="nav flex-column mt-2">
        <span class="nav-section">Principal</span>
        <a href="<?= BASE_URL ?>/index.php?page=dashboard" class="nav-link <?= ($page ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=tasks" class="nav-link <?= ($page ?? '') === 'tasks' ? 'active' : '' ?>">
            <i class="fas fa-check-square"></i> Tarefas
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=documents" class="nav-link <?= ($page ?? '') === 'documents' ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i> Documentos
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=search" class="nav-link <?= ($page ?? '') === 'search' ? 'active' : '' ?>">
            <i class="fas fa-search"></i> Busca
        </a>

        <span class="nav-section">Conta</span>
        <a href="<?= BASE_URL ?>/index.php?page=profile" class="nav-link <?= ($page ?? '') === 'profile' ? 'active' : '' ?>">
            <i class="fas fa-user"></i> Meu Perfil
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=help&section=intro" class="nav-link">
            <i class="fas fa-question-circle"></i> Ajuda
        </a>

        <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
        <span class="nav-section">Administração</span>
        <a href="<?= BASE_URL ?>/index.php?page=admin" class="nav-link <?= ($page ?? '') === 'admin' ? 'active' : '' ?>">
            <i class="fas fa-cogs"></i> Painel Admin
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=admin_logs" class="nav-link <?= ($page ?? '') === 'admin_logs' ? 'active' : '' ?>">
            <i class="fas fa-list"></i> Logs
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=diagnostics" class="nav-link <?= ($page ?? '') === 'diagnostics' ? 'active' : '' ?>">
            <i class="fas fa-network-wired"></i> Diagnósticos
        </a>
        <a href="<?= BASE_URL ?>/index.php?page=admin_backup" class="nav-link">
            <i class="fas fa-database"></i> Backup
        </a>
        <?php endif; ?>
    </div>

    <div class="sidebar-user">
        <div class="d-flex align-items-center gap-2">
            <?php if (!empty($_SESSION['user_avatar'] ?? '')): ?>
                <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($_SESSION['user_avatar']) ?>" class="user-avatar" alt="Avatar">
            <?php else: ?>
                <div style="width:36px;height:36px;border-radius:50%;background:#3b82f6;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:0.85rem;">
                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div style="overflow:hidden;">
                <div style="color:#f1f5f9;font-size:0.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
                <div style="color:#64748b;font-size:0.7rem;"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></div>
            </div>
            <a href="<?= BASE_URL ?>/index.php?page=logout" class="ms-auto" style="color:#64748b;" title="Sair"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</div>

<div class="main-content">
