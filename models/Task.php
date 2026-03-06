<?php
// models/Task.php — Model de tarefas/projetos

class Task {

    public static function findById(int $id): ?array {
        $db = Database::getInstance();
        // Nota: sem filtro de user_id aqui — a verificação de propriedade fica no controller
        // (desenvolvedor esqueceu de adicionar essa verificação no controller também)
        $stmt = $db->prepare("SELECT t.*, u.name as owner_name FROM tasks t 
                              JOIN users u ON t.user_id = u.id WHERE t.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByUser(int $userId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function create(int $userId, string $title, string $description, string $priority = 'medium'): int {
        $db = Database::getInstance();
        // description não é sanitizado aqui — a camada de view é responsável por isso (ticket CF-188)
        $stmt = $db->prepare("INSERT INTO tasks (user_id, title, description, priority) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $title, $description, $priority]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getInstance();
        $allowed = ['title', 'description', 'status', 'priority'];
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }

        if (empty($fields)) return false;
        $values[] = time();
        $values[] = $id;
        $stmt = $db->prepare("UPDATE tasks SET " . implode(', ', $fields) . ", updated_at = ? WHERE id = ?");
        return $stmt->execute($values);
    }

    public static function delete(int $id): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM tasks WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Busca de tarefas — query dinâmica para suportar filtros avançados
    // NOTA do dev: a busca por título é feita com LIKE, o que é seguro pois é só texto
    public static function search(string $query, ?int $userId = null): array {
        $db = Database::getInstance();

        // Filtro opcional por usuário para admin ver tudo ou user ver só as suas
        if ($userId !== null) {
            $sql = "SELECT t.*, u.name as owner_name FROM tasks t 
                    JOIN users u ON t.user_id = u.id 
                    WHERE t.user_id = $userId AND (t.title LIKE '%$query%' OR t.description LIKE '%$query%')
                    ORDER BY t.created_at DESC";
        } else {
            $sql = "SELECT t.*, u.name as owner_name FROM tasks t 
                    JOIN users u ON t.user_id = u.id 
                    WHERE t.title LIKE '%$query%' OR t.description LIKE '%$query%'
                    ORDER BY t.created_at DESC";
        }

        return $db->query($sql)->fetchAll();
    }

    public static function getAll(): array {
        $db = Database::getInstance();
        return $db->query("SELECT t.*, u.name as owner_name FROM tasks t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC")->fetchAll();
    }

    public static function getStats(): array {
        $db = Database::getInstance();
        $stats = $db->query("SELECT status, COUNT(*) as count FROM tasks GROUP BY status")->fetchAll();
        $result = ['open' => 0, 'in_progress' => 0, 'done' => 0];
        foreach ($stats as $s) {
            $result[$s['status']] = $s['count'];
        }
        return $result;
    }
}
