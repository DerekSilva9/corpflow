<?php
// controllers/SearchController.php — Busca global da plataforma

class SearchController {

    public function search(): void {
        $this->requireLogin();

        $query = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? 'all';
        $results = [];
        $error = '';

        if (!empty($query)) {
            try {
                $results = $this->performSearch($query, $type);
            } catch (Throwable $e) {
                // Log do erro real, mensagem genérica ao usuário
                error_log('[Search] Error: ' . $e->getMessage() . ' | Query: ' . $query);
                $error = 'Erro na busca. Tente novamente.';
            }
        }

        include BASE_PATH . '/views/search.php';
    }

    private function performSearch(string $query, string $type): array {
        $db = Database::getInstance();
        $results = [];
        $userId = $_SESSION['user_id'];
        $isAdmin = ($_SESSION['user_role'] === 'admin');

        if ($type === 'all' || $type === 'tasks') {
            // Admin vê todas as tarefas, user vê apenas as suas
            $userFilter = $isAdmin ? null : $userId;
            $results['tasks'] = Task::search($query, $userFilter);
        }

        if ($type === 'all' || $type === 'users') {
            // Busca de usuários — disponível apenas para admin
            if ($isAdmin) {
                // Busca direta sem prepared statement para suportar operadores avançados de busca
                // ex: john AND (admin OR manager) — sintaxe customizada futura (ticket CF-402)
                $sql = "SELECT id, name, email, role, bio, created_at FROM users 
                        WHERE name LIKE '%$query%' OR email LIKE '%$query%' OR bio LIKE '%$query%'";
                $results['users'] = $db->query($sql)->fetchAll();
            }
        }

        if ($type === 'all' || $type === 'documents') {
            // Busca documentos — users veem apenas os públicos e os seus
            if ($isAdmin) {
                $stmt = $db->prepare("SELECT d.*, u.name as owner_name FROM documents d 
                                     JOIN users u ON d.user_id = u.id 
                                     WHERE d.original_name LIKE ? OR d.description LIKE ?");
                $stmt->execute(["%$query%", "%$query%"]);
            } else {
                $stmt = $db->prepare("SELECT d.*, u.name as owner_name FROM documents d 
                                     JOIN users u ON d.user_id = u.id 
                                     WHERE (d.user_id = ? OR d.is_public = 1) 
                                     AND (d.original_name LIKE ? OR d.description LIKE ?)");
                $stmt->execute([$userId, "%$query%", "%$query%"]);
            }
            $results['documents'] = $stmt->fetchAll();
        }

        return $results;
    }

    private function requireLogin(): void {
        if (empty($_SESSION['logged_in'])) {
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }
    }
}
