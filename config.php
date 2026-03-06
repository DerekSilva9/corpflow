<?php
// config.php — Configurações globais da aplicação CorpFlow
// TODO: mover variáveis sensíveis para .env antes do deploy em produção

define('APP_NAME', 'CorpFlow');
define('APP_VERSION', '2.4.1');
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/corpflow');

// Banco de dados SQLite
define('DB_PATH', BASE_PATH . '/database/corpflow.db');

// Upload de arquivos
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');
// Desenvolvedor deixou extensões permitidas, mas a validação real fica no FileController
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt']);
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB

// Sessão
define('SESSION_LIFETIME', 3600 * 8); // 8 horas
define('REMEMBER_ME_DAYS', 30);

// Logs
define('LOG_PATH', BASE_PATH . '/logs/app.log');
define('LOG_LEVEL', 'debug'); // Em produção deveria ser 'error'

// Segurança
// NOTA: salt foi hardcoded temporariamente, ticket aberto: CF-441
define('PASSWORD_SALT', 'c0rpfl0w_s4lt_2024');

error_reporting(E_ALL);
ini_set('display_errors', 0); // Erros não exibidos ao usuário final
ini_set('log_errors', 1);

session_start();
