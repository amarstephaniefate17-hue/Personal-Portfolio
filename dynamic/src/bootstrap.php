<?php
declare(strict_types=1);

const APP_ROOT = __DIR__ . '/..';

function env_value(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false || $value === '' ? $default : $value;
}

function app_config(): array
{
    static $config;
    if ($config !== null) return $config;
    $private = [];
    $privateFile = __DIR__ . '/local-config.php';
    if (is_file($privateFile) && is_readable($privateFile)) {
        $loaded = require $privateFile;
        if (is_array($loaded)) $private = $loaded;
    }
    $config = [
        'db_host' => env_value('PORTFOLIO_DB_HOST', (string)($private['db_host'] ?? '127.0.0.1')),
        'db_port' => env_value('PORTFOLIO_DB_PORT', (string)($private['db_port'] ?? '3306')),
        'db_name' => env_value('PORTFOLIO_DB_NAME', (string)($private['db_name'] ?? '')),
        'db_user' => env_value('PORTFOLIO_DB_USER', (string)($private['db_user'] ?? '')),
        'db_password' => env_value('PORTFOLIO_DB_PASSWORD', (string)($private['db_password'] ?? '')),
        'login_salt' => env_value('PORTFOLIO_LOGIN_SALT', (string)($private['login_salt'] ?? __FILE__)),
        'base_url' => rtrim(env_value('PORTFOLIO_BASE_URL', (string)($private['base_url'] ?? '')), '/'),
        'secure_cookies' => env_value('PORTFOLIO_SECURE_COOKIES', !empty($private['secure_cookies']) ? '1' : '0') === '1',
        'debug' => env_value('PORTFOLIO_DEBUG', !empty($private['debug']) ? '1' : '0') === '1',
    ];
    return $config;
}

function db(): PDO
{
    static $pdo;
    if ($pdo instanceof PDO) return $pdo;
    $c = app_config();
    if (!$c['db_name'] || !$c['db_user']) throw new RuntimeException('Database configuration is incomplete. Set PORTFOLIO_DB_NAME and PORTFOLIO_DB_USER.');
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $c['db_host'], $c['db_port'], $c['db_name']);
    $pdo = new PDO($dsn, $c['db_user'], (string)$c['db_password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $pdo->exec("SET time_zone = '+00:00'");
    return $pdo;
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $https = app_config()['secure_cookies'] || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('portfolio_session');
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$https,'httponly'=>true,'samesite'=>'Lax']);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
    $now = time();
    if (isset($_SESSION['last_activity']) && $now - (int)$_SESSION['last_activity'] > 1800) {
        $_SESSION = [];
        session_regenerate_id(true);
    } elseif (isset($_SESSION['regenerated_at']) && $now - (int)$_SESSION['regenerated_at'] > 900) {
        session_regenerate_id(true);
        $_SESSION['regenerated_at'] = $now;
    }
    $_SESSION['last_activity'] = $now;
    $_SESSION['regenerated_at'] = $_SESSION['regenerated_at'] ?? $now;
}

start_secure_session();
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: DENY');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || app_config()['secure_cookies']) header('Strict-Transport-Security: max-age=31536000');

function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function text_length(string $value): int { return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value); }
function url(string $path = ''): string { $base = app_config()['base_url']; return $base . '/' . ltrim($path, '/'); }
function redirect(string $path) { header('Location: ' . url($path), true, 303); exit; }
function csrf_token(): string { if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32)); return $_SESSION['_csrf']; }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">'; }
function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? '';
    if (!is_string($token) || !hash_equals((string)($_SESSION['_csrf'] ?? ''), $token)) { http_response_code(403); exit('Invalid form token. Reload the page and try again.'); }
}
function is_admin(): bool { return isset($_SESSION['admin_id']) && is_int($_SESSION['admin_id']); }
function require_admin(): void { if (!is_admin()) redirect('admin/login.php'); }
function flash(string $type, string $message): void { $_SESSION['_flash'] = ['type'=>$type,'message'=>$message]; }
function take_flash(): ?array { $f = $_SESSION['_flash'] ?? null; unset($_SESSION['_flash']); return is_array($f) ? $f : null; }
function setting(string $key, string $fallback = ''): string
{
    static $settings = null;
    if ($settings === null) {
        try { $settings = db()->query('SELECT setting_key, setting_value FROM website_settings')->fetchAll(PDO::FETCH_KEY_PAIR); }
        catch (Throwable $e) { $settings = []; }
    }
    return (string)($settings[$key] ?? $fallback);
}
function records(string $table, string $order = 'sort_order ASC, id ASC'): array
{
    $allowed = ['skills','projects','education','experiences','achievements'];
    if (!in_array($table, $allowed, true)) throw new InvalidArgumentException('Unknown portfolio collection.');
    return db()->query("SELECT * FROM `$table` WHERE is_published = 1 ORDER BY $order")->fetchAll();
}
function upload_image(array $file, string $bucket): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 5 * 1024 * 1024) throw new RuntimeException('Choose an image smaller than 5 MB.');
    if (!in_array($bucket, ['profile','projects','achievements'], true)) throw new RuntimeException('Invalid image category.');
    $tmp = (string)($file['tmp_name'] ?? '');
    $info = @getimagesize($tmp);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $formats = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    if (!$info || !isset($formats[$mime]) || $info['mime'] !== $mime) throw new RuntimeException('Only valid JPEG, PNG, or WebP images are accepted.');
    $dir = APP_ROOT . '/public/uploads/' . $bucket;
    if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) throw new RuntimeException('Image storage is unavailable.');
    $filename = bin2hex(random_bytes(20)) . '.' . $formats[$mime];
    if (!move_uploaded_file($tmp, $dir . '/' . $filename)) throw new RuntimeException('The image could not be stored.');
    return 'uploads/' . $bucket . '/' . $filename;
}
