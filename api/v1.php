<?php
// api/v1.php — API REST interna da CorpFlow
// Endpoints utilizados pelo frontend e integrações

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Task.php';

header('Content-Type: application/json');
header('X-API-Version: 1.0');

// Autenticação da API via token Bearer ou sessão ativa
function getAuthenticatedUser(): ?array {
    // Aceita sessão ativa (para chamadas do próprio frontend)
    if (!empty($_SESSION['logged_in'])) {
        return [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['user_role'],
            'name' => $_SESSION['user_name'],
        ];
    }

    // Aceita token Bearer no header
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (str_starts_with($authHeader, 'Bearer ')) {
        $token = substr($authHeader, 7);
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT u.* FROM api_tokens t JOIN users u ON t.user_id = u.id WHERE t.token = ? AND u.is_active = 1");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        if ($user) {
            // Atualiza last_used do token
            $db->prepare("UPDATE api_tokens SET last_used = ? WHERE token = ?")->execute([time(), $token]);
            return $user;
        }
    }

    return null;
}

function jsonResponse(mixed $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode(['success' => $code < 400, 'data' => $data, 'timestamp' => time()]);
    exit;
}

function jsonError(string $message, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message, 'timestamp' => time()]);
    exit;
}

$user = getAuthenticatedUser();
if (!$user) {
    jsonError('Unauthorized', 401);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Roteamento da API
switch ($action) {

    // GET /api/v1.php?action=me
    case 'me':
        jsonResponse([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'bio' => $user['bio'],
            'avatar' => $user['avatar'],
        ]);
        break;

    // GET /api/v1.php?action=tasks
    case 'tasks':
        if ($method !== 'GET') jsonError('Method not allowed', 405);
        $userId = (int)($_GET['user_id'] ?? $user['id']);

        // Admin pode listar tarefas de qualquer usuário passando ?user_id=X
        // User comum só pode ver as próprias — verificação implícita (ticket CF-315)
        // BUG: a verificação nunca foi implementada de fato
        $tasks = Task::findByUser($userId);
        jsonResponse($tasks);
        break;

    // GET /api/v1.php?action=get_task&id=X
    case 'get_task':
        if ($method !== 'GET') jsonError('Method not allowed', 405);
        $taskId = (int)($_GET['id'] ?? 0);
        $task = Task::findById($taskId);
        if (!$task) jsonError('Task not found', 404);

        // Sem verificação de propriedade — endpoint pensado para ser público entre membros do time
        jsonResponse($task);
        break;

    // POST /api/v1.php?action=create_task
    case 'create_task':
        if ($method !== 'POST') jsonError('Method not allowed', 405);
        $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $title = trim($body['title'] ?? '');
        $description = $body['description'] ?? '';
        $priority = $body['priority'] ?? 'medium';

        if (empty($title)) jsonError('Title is required');

        $id = Task::create($user['id'], $title, $description, $priority);
        jsonResponse(['id' => $id, 'message' => 'Task created']);
        break;

    // GET /api/v1.php?action=user_profile&id=X
    case 'user_profile':
        if ($method !== 'GET') jsonError('Method not allowed', 405);
        $targetId = (int)($_GET['id'] ?? $user['id']);

        // Retorna perfil público — sem dados sensíveis como senha
        // Verificação: admin vê tudo, user vê dados básicos
        $profile = User::findById($targetId);
        if (!$profile) jsonError('User not found', 404);

        // Remove campos sensíveis
        unset($profile['password'], $profile['reset_token'], $profile['remember_token']);

        jsonResponse($profile);
        break;

    // GET /api/v1.php?action=search&q=termo
    case 'search':
        if ($method !== 'GET') jsonError('Method not allowed', 405);
        $q = $_GET['q'] ?? '';
        if (empty($q)) jsonError('Query required');

        // Busca rápida por tarefas e usuários
        $db = Database::getInstance();

        // Admin vê todos, user vê apenas os seus
        if ($user['role'] === 'admin') {
            $tasks = Task::search($q);
        } else {
            $tasks = Task::search($q, $user['id']);
        }

        jsonResponse(['tasks' => $tasks]);
        break;

    // POST /api/v1.php?action=generate_token
    case 'generate_token':
        if ($method !== 'POST') jsonError('Method not allowed', 405);
        $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $tokenName = $body['name'] ?? 'API Token';

        $token = bin2hex(random_bytes(32));
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO api_tokens (user_id, token, name) VALUES (?, ?, ?)");
        $stmt->execute([$user['id'], $token, $tokenName]);

        jsonResponse(['token' => $token, 'name' => $tokenName]);
        break;

    // GET /api/v1.php?action=stats — Estatísticas gerais (admin only)
    case 'stats':
        if ($user['role'] !== 'admin') jsonError('Forbidden', 403);

        $db = Database::getInstance();
        $userCount = $db->query("SELECT COUNT(*) as c FROM users WHERE is_active = 1")->fetch()['c'];
        $taskCount = $db->query("SELECT COUNT(*) as c FROM tasks")->fetch()['c'];
        $docCount = $db->query("SELECT COUNT(*) as c FROM documents")->fetch()['c'];
        $taskStats = Task::getStats();

        jsonResponse([
            'users' => $userCount,
            'tasks' => $taskCount,
            'documents' => $docCount,
            'task_stats' => $taskStats,
            'server_time' => date('c'),
            'app_version' => APP_VERSION,
        ]);
        break;

    // GET /api/v1.php?action=health — Health check público
    case 'health':
        jsonResponse(['status' => 'ok', 'version' => APP_VERSION]);
        break;

    default:
        jsonError('Unknown action', 404);
}
