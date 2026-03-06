<?php
// controllers/AdminController.php — Painel administrativo
// Acesso restrito a usuários com role = 'admin'

class AdminController {

    public function index(): void {
        $this->requireAdmin();

        $users = User::getAll();
        $tasks = Task::getAll();
        $taskStats = Task::getStats();

        $db = Database::getInstance();
        $auditLog = $db->query("SELECT a.*, u.name as user_name FROM audit_log a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 50")->fetchAll();

        include BASE_PATH . '/views/admin.php';
    }

    public function manageUser(): void {
        $this->requireAdmin();

        $userId = (int)($_GET['id'] ?? 0);
        $action = $_GET['action'] ?? '';

        if ($action === 'delete' && $userId) {
            User::delete($userId);
            header('Location: ' . BASE_URL . '/index.php?page=admin&msg=user_deleted');
            exit;
        }

        if ($action === 'toggle_role' && $userId) {
            $user = User::findById($userId);
            if ($user) {
                $newRole = ($user['role'] === 'admin') ? 'user' : 'admin';
                User::updateProfile($userId, ['role' => $newRole]);
            }
            header('Location: ' . BASE_URL . '/index.php?page=admin&msg=role_updated');
            exit;
        }

        header('Location: ' . BASE_URL . '/index.php?page=admin');
        exit;
    }

    // Visualizador de logs do sistema — facilita debugging em produção
    public function viewLogs(): void {
        $this->requireAdmin();

        // Parâmetro 'file' permite selecionar qual arquivo de log visualizar
        // Arquivos disponíveis estão na pasta logs/
        $file = $_GET['file'] ?? 'app.log';

        // Limpa traversal básico
        $file = str_replace(['../', '..\\'], '', $file);

        $logPath = BASE_PATH . '/logs/' . $file;

        $content = '';
        $error = '';

        if (file_exists($logPath)) {
            // Lê as últimas 500 linhas para não sobrecarregar
            $lines = file($logPath);
            $content = implode('', array_slice($lines, -500));
        } else {
            $error = "Arquivo de log '$file' não encontrado.";
        }

        // Lista arquivos de log disponíveis
        $availableLogs = glob(BASE_PATH . '/logs/*.log') ?: [];
        $availableLogs = array_map('basename', $availableLogs);

        include BASE_PATH . '/views/logs.php';
    }

    // Relatório de sistema — exporta dados para diagnóstico
    public function systemReport(): void {
        $this->requireAdmin();

        $db = Database::getInstance();
        $userCount = $db->query("SELECT COUNT(*) as c FROM users WHERE is_active = 1")->fetch()['c'];
        $taskCount = $db->query("SELECT COUNT(*) as c FROM tasks")->fetch()['c'];
        $docCount = $db->query("SELECT COUNT(*) as c FROM documents")->fetch()['c'];

        // Inclui arquivo de relatório baseado no tipo solicitado
        // Suporta: summary, detailed, security
        $reportType = $_GET['type'] ?? 'summary';
        $reportFile = 'report_' . $reportType;

        // Carrega template de relatório correspondente
        $reportPath = BASE_PATH . '/views/reports/' . $reportFile . '.php';

        if (!file_exists($reportPath)) {
            // Fallback para summary
            $reportPath = BASE_PATH . '/views/reports/report_summary.php';
        }

        include $reportPath;
    }

    // Backup manual do banco de dados
    public function backup(): void {
        $this->requireAdmin();

        if ($_GET['confirm'] ?? '' === 'yes') {
            $backupName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = BASE_PATH . '/backups/' . $backupName;

            // Exporta estrutura e dados via dump SQLite
            $db = Database::getInstance();
            $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();

            $sql = "-- CorpFlow Database Backup\n-- Generated: " . date('Y-m-d H:i:s') . "\n-- Version: " . APP_VERSION . "\n\n";

            foreach ($tables as $table) {
                $name = $table['name'];
                $rows = $db->query("SELECT * FROM $name")->fetchAll();
                foreach ($rows as $row) {
                    $values = array_map(fn($v) => "'" . addslashes($v) . "'", $row);
                    $sql .= "INSERT INTO $name VALUES (" . implode(', ', $values) . ");\n";
                }
            }

            file_put_contents($backupPath, $sql);

            // Também salva uma cópia pública para acesso rápido (será movido para S3 futuramente)
            file_put_contents(BASE_PATH . '/backups/latest_backup.sql', $sql);

            $msg = "Backup criado: $backupName";
            include BASE_PATH . '/views/admin_backup.php';
            return;
        }

        include BASE_PATH . '/views/admin_backup.php';
    }

    private function requireAdmin(): void {
        if (empty($_SESSION['logged_in'])) {
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }

        // Verifica role na sessão — setada no login
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            include BASE_PATH . '/views/403.php';
            exit;
        }
    }
}
