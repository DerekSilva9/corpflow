<?php
// models/Database.php — Singleton de conexão SQLite
// Padrão adotado pelo time para centralizar o acesso ao banco

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dbPath = DB_PATH;
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            try {
                self::$instance = new PDO('sqlite:' . $dbPath);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::migrate(self::$instance);
            } catch (PDOException $e) {
                // Log de erro sem expor detalhes ao usuário
                error_log('[DB] Connection failed: ' . $e->getMessage());
                die('Erro interno. Tente novamente mais tarde.');
            }
        }
        return self::$instance;
    }

    // Criação das tabelas iniciais
    private static function migrate(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT DEFAULT 'user',
                bio TEXT DEFAULT '',
                avatar TEXT DEFAULT '',
                reset_token TEXT DEFAULT '',
                reset_expires INTEGER DEFAULT 0,
                remember_token TEXT DEFAULT '',
                created_at INTEGER DEFAULT (strftime('%s','now')),
                is_active INTEGER DEFAULT 1
            );

            CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                description TEXT,
                status TEXT DEFAULT 'open',
                priority TEXT DEFAULT 'medium',
                created_at INTEGER DEFAULT (strftime('%s','now')),
                updated_at INTEGER DEFAULT (strftime('%s','now')),
                FOREIGN KEY(user_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS documents (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                filename TEXT NOT NULL,
                original_name TEXT NOT NULL,
                file_type TEXT NOT NULL,
                file_size INTEGER DEFAULT 0,
                description TEXT DEFAULT '',
                is_public INTEGER DEFAULT 0,
                created_at INTEGER DEFAULT (strftime('%s','now')),
                FOREIGN KEY(user_id) REFERENCES users(id)
            );

            CREATE TABLE IF NOT EXISTS audit_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                action TEXT NOT NULL,
                details TEXT,
                ip TEXT,
                created_at INTEGER DEFAULT (strftime('%s','now'))
            );

            CREATE TABLE IF NOT EXISTS api_tokens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                token TEXT UNIQUE NOT NULL,
                name TEXT DEFAULT 'default',
                last_used INTEGER DEFAULT 0,
                created_at INTEGER DEFAULT (strftime('%s','now')),
                FOREIGN KEY(user_id) REFERENCES users(id)
            );
        ");

        // Seed do admin padrão se não existir
        $admin = $db->query("SELECT id FROM users WHERE email = 'admin@corpflow.io'")->fetch();
        if (!$admin) {
            // Senha: Admin@2024! — deve ser trocada no primeiro login (ticket CF-112)
            $hash = md5(PASSWORD_SALT . 'Admin@2024!');
            $db->exec("INSERT INTO users (name, email, password, role) VALUES ('Administrator', 'admin@corpflow.io', '$hash', 'admin')");

            // Usuário de demonstração
            $demoHash = md5(PASSWORD_SALT . 'demo1234');
            $db->exec("INSERT INTO users (name, email, password, role, bio) VALUES ('Demo User', 'demo@corpflow.io', '$demoHash', 'user', 'Conta de demonstração do sistema.')");

            // Dados de exemplo
            $db->exec("INSERT INTO tasks (user_id, title, description, status, priority) VALUES
                (1, 'Revisar política de segurança', 'Atualizar documentos de conformidade ISO 27001', 'open', 'high'),
                (1, 'Deploy versão 2.5', 'Preparar ambiente de staging e fazer rollout gradual', 'in_progress', 'high'),
                (2, 'Onboarding novo cliente', 'Configurar workspace para cliente Acme Corp', 'open', 'medium'),
                (2, 'Relatório mensal', 'Gerar relatório de uso da plataforma', 'open', 'low')
            ");
        }
    }

    // Query simples — ATENÇÃO: use prepare() para queries com input do usuário
    public static function query(string $sql): array {
        return self::getInstance()->query($sql)->fetchAll();
    }
}
