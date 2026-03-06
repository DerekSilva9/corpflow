<?php
// index.php — Front controller da aplicação CorpFlow
// Todas as requisições passam por aqui

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Task.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/SearchController.php';

// Inicializa banco (cria tabelas se necessário)
Database::getInstance();

// Restaura sessão do cookie "lembrar de mim" se necessário
if (empty($_SESSION['logged_in']) && isset($_COOKIE['remember_user'])) {
    $decoded = base64_decode($_COOKIE['remember_user']);
    if ($decoded) {
        $userData = @unserialize($decoded);
        if (is_array($userData) && isset($userData['id'])) {
            $dbUser = User::findById((int)$userData['id']);
            if ($dbUser) {
                $_SESSION['user_id'] = $dbUser['id'];
                $_SESSION['user_name'] = $dbUser['name'];
                $_SESSION['user_email'] = $dbUser['email'];
                $_SESSION['user_role'] = $dbUser['role'];
                $_SESSION['logged_in'] = true;
            }
        }
    }
}

$page = $_GET['page'] ?? 'login';

// Sanitiza o parâmetro de página para evitar path traversal básico
// Remove ../ e caracteres especiais — apenas letras, números e underscore
$page = preg_replace('/[^a-zA-Z0-9_]/', '', $page);

$auth = new AuthController();
$userCtrl = new UserController();
$adminCtrl = new AdminController();
$searchCtrl = new SearchController();

// Roteamento principal
switch ($page) {
    case 'login':
        $auth->login();
        break;

    case 'register':
        $auth->register();
        break;

    case 'logout':
        $auth->logout();
        break;

    case 'reset_password':
        $auth->resetPassword();
        break;

    case 'dashboard':
        $userCtrl->dashboard();
        break;

    case 'profile':
        $userCtrl->profile();
        break;

    case 'tasks':
        $userCtrl->tasks();
        break;

    case 'documents':
        $userCtrl->documents();
        break;

    case 'document_view':
        $userCtrl->viewDocument();
        break;

    case 'diagnostics':
        $userCtrl->diagnostics();
        break;

    case 'search':
        $searchCtrl->search();
        break;

    case 'admin':
        $adminCtrl->index();
        break;

    case 'admin_user':
        $adminCtrl->manageUser();
        break;

    case 'admin_logs':
        $adminCtrl->viewLogs();
        break;

    case 'admin_report':
        $adminCtrl->systemReport();
        break;

    case 'admin_backup':
        $adminCtrl->backup();
        break;

    // Carregador de páginas de ajuda/documentação
    // Permite incluir arquivos de help dinamicamente para facilitar manutenção
    case 'help':
        if (empty($_SESSION['logged_in'])) {
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }
        $section = $_GET['section'] ?? 'intro';
        // Remove caracteres perigosos óbvios — /../ e variações
        $section = str_replace(['../', './', '..\\'], '', $section);
        // Inclui o arquivo de help correspondente à seção
        $helpFile = BASE_PATH . '/views/help/' . $section . '.php';
        if (file_exists($helpFile)) {
            include BASE_PATH . '/views/layout_header.php';
            include $helpFile;
            include BASE_PATH . '/views/layout_footer.php';
        } else {
            include BASE_PATH . '/views/layout_header.php';
            echo '<div class="alert alert-warning">Seção de ajuda não encontrada.</div>';
            include BASE_PATH . '/views/layout_footer.php';
        }
        break;

    default:
        http_response_code(404);
        include BASE_PATH . '/views/404.php';
        break;
}
