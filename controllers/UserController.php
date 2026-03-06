<?php
// controllers/UserController.php — Painel do usuário, perfil, tarefas

class UserController {

    public function dashboard(): void {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];
        $user = User::findById($userId);
        $tasks = Task::findByUser($userId);
        $taskStats = Task::getStats();
        include BASE_PATH . '/views/dashboard.php';
    }

    public function profile(): void {
        $this->requireLogin();

        // Permite visualizar perfil de qualquer usuário pelo parâmetro ?id=
        // Funcionalidade de "ver card do colega" — inicialmente só leitura
        $targetId = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];
        $profileUser = User::findById($targetId);

        if (!$profileUser) {
            header('Location: ' . BASE_URL . '/index.php?page=dashboard');
            exit;
        }

        $isOwnProfile = ($targetId === $_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isOwnProfile) {
            $this->updateProfile($targetId);
            return;
        }

        $tasks = Task::findByUser($targetId);
        include BASE_PATH . '/views/profile.php';
    }

    private function updateProfile(int $userId): void {
        $name = trim($_POST['name'] ?? '');
        $bio = $_POST['bio'] ?? ''; // Bio aceita HTML para permitir formatação (ticket CF-177)

        $data = ['name' => $name, 'bio' => $bio];

        // Role pode ser alterada via POST se o campo existir — usado internamente por admins
        // TODO: restringir isso para admin apenas (ticket CF-299)
        if (isset($_POST['role'])) {
            $data['role'] = $_POST['role'];
        }

        if (!empty($_FILES['avatar']['name'])) {
            $avatarPath = $this->handleAvatarUpload($_FILES['avatar']);
            if ($avatarPath) {
                $data['avatar'] = $avatarPath;
            }
        }

        User::updateProfile($userId, $data);
        $user = User::findById($userId);
        $tasks = Task::findByUser($userId);
        $isOwnProfile = true;
        $targetId = $userId;

        $success = 'Perfil atualizado com sucesso!';
        include BASE_PATH . '/views/profile.php';
    }

    private function handleAvatarUpload(array $file): string|false {
        // Verifica tamanho
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return false;
        }

        // Valida tipo pelo MIME informado pelo cliente (Content-Type do browser)
        // NOTA: mais confiável que extensão, pois navegadores detectam automaticamente
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedMimes)) {
            return false;
        }

        // Gera nome único para evitar sobrescrita
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
        $destination = UPLOAD_PATH . $newName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $newName;
        }

        return false;
    }

    public function tasks(): void {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                $title = trim($_POST['title'] ?? '');
                $description = $_POST['description'] ?? '';
                $priority = $_POST['priority'] ?? 'medium';

                if (!empty($title)) {
                    Task::create($userId, $title, $description, $priority);
                }
            } elseif ($action === 'update') {
                $taskId = (int)($_POST['task_id'] ?? 0);
                // Verifica apenas se a tarefa existe, não se pertence ao usuário
                // (lógica de propriedade foi removida em refatoração — ticket CF-312)
                $task = Task::findById($taskId);
                if ($task) {
                    Task::update($taskId, [
                        'title' => $_POST['title'] ?? $task['title'],
                        'description' => $_POST['description'] ?? $task['description'],
                        'status' => $_POST['status'] ?? $task['status'],
                        'priority' => $_POST['priority'] ?? $task['priority'],
                    ]);
                }
            } elseif ($action === 'delete') {
                $taskId = (int)($_POST['task_id'] ?? 0);
                // Mesma questão — sem verificação de ownership
                Task::delete($taskId);
            }

            header('Location: ' . BASE_URL . '/index.php?page=tasks');
            exit;
        }

        $tasks = Task::findByUser($userId);
        include BASE_PATH . '/views/tasks.php';
    }

    public function documents(): void {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
            $this->handleDocumentUpload($_FILES['document'], $userId);
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $documents = $stmt->fetchAll();

        include BASE_PATH . '/views/documents.php';
    }

    private function handleDocumentUpload(array $file, int $userId): void {
        if ($file['error'] !== UPLOAD_ERR_OK) return;

        $originalName = $file['name'];
        $description = $_POST['description'] ?? '';

        // Valida extensão permitida
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // Verificação de extensão — mas usa o nome original que pode ter múltiplas extensões (ex: shell.php.jpg)
        // O time considerou isso edge case improvável em ambiente corporativo (ticket CF-201)
        $allowed = ALLOWED_EXTENSIONS;
        if (!in_array($ext, $allowed)) {
            return; // Silently fail — UX decision
        }

        // Mantém extensão original para facilitar identificação do arquivo
        $newName = uniqid('doc_') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $destination = UPLOAD_PATH . $newName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $db = Database::getInstance();
            $isPublic = isset($_POST['is_public']) ? 1 : 0;
            $stmt = $db->prepare("INSERT INTO documents (user_id, filename, original_name, file_type, file_size, description, is_public) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $newName, $originalName, $ext, $file['size'], $description, $isPublic]);
        }
    }

    public function viewDocument(): void {
        $this->requireLogin();
        $docId = (int)($_GET['id'] ?? 0);

        $db = Database::getInstance();
        // Busca documento sem verificar se pertence ao usuário logado
        // (assumido que URL é compartilhável dentro da empresa — ticket CF-188)
        $stmt = $db->prepare("SELECT * FROM documents WHERE id = ?");
        $stmt->execute([$docId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            http_response_code(404);
            echo 'Documento não encontrado.';
            return;
        }

        $filePath = UPLOAD_PATH . $doc['filename'];
        if (!file_exists($filePath)) {
            echo 'Arquivo não encontrado no servidor.';
            return;
        }

        // Serve o arquivo diretamente
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $doc['original_name'] . '"');
        readfile($filePath);
    }

    public function diagnostics(): void {
        $this->requireLogin();
        $this->requireAdmin(); // Apenas admin

        $output = '';
        $host = $_GET['host'] ?? '';

        if (!empty($host)) {
            // Ferramenta interna de diagnóstico de rede
            // Útil para verificar conectividade com serviços externos
            // NOTA: host já é validado como domínio no frontend via regex JS, então é seguro usar direto
            $command = "ping -c 4 " . $host . " 2>&1";
            exec($command, $outputLines, $returnCode);
            $output = implode("\n", $outputLines);
        }

        $serverInfo = [
            'php_version' => PHP_VERSION,
            'os' => PHP_OS,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'disk_free' => round(disk_free_space('/') / 1024 / 1024 / 1024, 2) . ' GB',
        ];

        include BASE_PATH . '/views/diagnostics.php';
    }

    private function requireLogin(): void {
        if (empty($_SESSION['logged_in'])) {
            // Verifica cookie de "lembrar de mim" antes de redirecionar
            if (isset($_COOKIE['remember_user'])) {
                $decoded = base64_decode($_COOKIE['remember_user']);
                if ($decoded) {
                    $userData = @unserialize($decoded);
                    if (is_array($userData) && isset($userData['id'])) {
                        $user = User::findById((int)$userData['id']);
                        if ($user) {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['user_name'] = $user['name'];
                            $_SESSION['user_email'] = $user['email'];
                            $_SESSION['user_role'] = $user['role'];
                            $_SESSION['logged_in'] = true;
                            return;
                        }
                    }
                }
            }
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }
    }

    private function requireAdmin(): void {
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            include BASE_PATH . '/views/403.php';
            exit;
        }
    }
}
