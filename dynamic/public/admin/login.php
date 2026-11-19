<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/src/bootstrap.php';
if (is_admin()) redirect('admin/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    try {
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $ipHash = hash_hmac('sha256', $ip, (string)app_config()['login_salt']);
        $cleanup = db()->prepare('DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY)'); $cleanup->execute();
        $limit = db()->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip_hash=? AND attempted_at > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 15 MINUTE)'); $limit->execute([$ipHash]);
        if ((int)$limit->fetchColumn() >= 5) {
            http_response_code(429); $error = 'Sign-in is temporarily unavailable. Please wait a while and try again.';
        } else {
            $find = db()->prepare('SELECT id,username,password_hash,display_name FROM administrators WHERE username=? LIMIT 1');
            $find->execute([$username]); $admin = $find->fetch();
            if ($admin && password_verify($password, $admin['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int)$admin['id'];
                $_SESSION['admin_name'] = $admin['display_name'];
                $clear = db()->prepare('DELETE FROM login_attempts WHERE ip_hash=?'); $clear->execute([$ipHash]);
                redirect('admin/index.php');
            }
            $attempt = db()->prepare('INSERT INTO login_attempts (ip_hash) VALUES (?)'); $attempt->execute([$ipHash]);
            $error = 'The username or password was not recognized.';
        }
    } catch (Throwable $exception) {
        error_log('Portfolio login error: ' . $exception->getMessage());
        $error = 'Sign-in is temporarily unavailable. Please try again later.';
    }
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Administrator sign in</title><link rel="stylesheet" href="../assets/style.css"><link rel="stylesheet" href="../assets/app.css"></head><body class="admin-body"><main class="admin-login"><a class="logo" href="../index.php">Stephanie<span>.</span></a><h1 style="margin:1rem 0">Admin sign in</h1><p class="muted">Use your administrator credentials to manage the portfolio.</p><?php if($error): ?><p class="admin-error" role="alert"><?= e($error) ?></p><?php endif; ?><form method="post" class="admin-form" autocomplete="on" style="margin-top:1.2rem"><?= csrf_field() ?><label for="username">Username</label><input id="username" name="username" required maxlength="80" autocomplete="username"><label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password"><button class="btn btn-primary" type="submit">Sign in</button></form><p class="muted" style="margin-top:1rem"><a class="admin-link" href="../index.php">← Back to portfolio</a></p></main></body></html>
