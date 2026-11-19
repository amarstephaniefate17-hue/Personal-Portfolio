<?php
// Copy to local-config.php OUTSIDE the web document root and fill in panel values.
// Never commit local-config.php or place it in a public directory.
return [
    'db_host' => 'database-server-from-hosting-panel',
    'db_port' => '3306',
    'db_name' => 'database-name-from-control-panel',
    'db_user' => 'database-username-from-client-panel',
    'db_password' => 'replace-with-private-database-password',
    'login_salt' => 'replace-with-at-least-32-random-characters',
    'base_url' => '',
    'secure_cookies' => true,
    'debug' => false,
];
