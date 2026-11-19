# Stephanie Fate R. Amar — Portfolio

The current `main` branch remains the original static portfolio and GitHub Pages deployment. The PHP/MySQL upgrade is developed on `feature/php-mysql-portfolio` in `dynamic/`; this keeps the working static site live while GitHub Pages cannot execute PHP. A clean snapshot of the original repository is on `backup/pre-dynamic-portfolio-20261120`.

## Dynamic app requirements

Use PHP 8.3 or newer with PDO, `pdo_mysql`, and Fileinfo enabled, plus MySQL 5.7+ or compatible MariaDB. TinkerHost's latest official version notice I could verify says its free hosting upgraded to PHP 8.3 in April 2025; check the actual account in case it has since changed. The app uses prepared PDO statements, database-backed public portfolio data, secure admin sessions, CSRF tokens, output escaping, upload content checks, and environment-provided database configuration.

## Local installation

1. Create a new MySQL database and a dedicated database user with access only to that database.
2. Import `dynamic/database/portfolio.sql` into the empty database with MySQL or phpMyAdmin. It creates the tables and seeds the identity and content currently in the static portfolio. The seed export is intended for an empty database; do not repeatedly import it into a live database.
3. Set these private environment variables in the PHP process or host settings: `PORTFOLIO_DB_HOST`, `PORTFOLIO_DB_PORT`, `PORTFOLIO_DB_NAME`, `PORTFOLIO_DB_USER`, and `PORTFOLIO_DB_PASSWORD`. See `dynamic/.env.example` for names only. If the host has no private variable feature, copy `dynamic/src/local-config.example.php` to `dynamic/src/local-config.php`, fill it out and keep that private file outside the document root; Git ignores the real config. Do not upload real credentials to GitHub.
4. Set `PORTFOLIO_SECURE_COOKIES=1` when HTTPS is active, `PORTFOLIO_DEBUG=0` in production, and `PORTFOLIO_LOGIN_SALT` to a long random value.
5. Run `php dynamic/bin/create-admin.php` from a trusted terminal after the environment variables are available. The script prompts for the initial username and password, hides password input, rejects weak passwords, and stores only a `password_hash()` result. It is CLI-only and refuses to create another administrator once one exists.
6. Run `php dynamic/bin/verify-install.php` to check the runtime, modules, database connection, seeded records, admin setup and upload directories.
7. Serve `dynamic/public/` as the web document root. Open `/` for the portfolio and `/admin/login.php` for the private dashboard. Ensure Apache honors the included `.htaccess` files. Configure uploads as writable by PHP but not executable.

## TinkerHost deployment

TinkerHost's [free-hosting page](https://tinkerhost.net/free) advertises PHP and MySQL/phpMyAdmin, FTP and an online file manager, SSL certificates, 5 GiB disk space, and signup without a credit card. Its [PHP upgrade notice](https://community.tinkerhost.net/forum/d/338-php-83-upgrade) says free hosting moved to PHP 8.3 in April 2025; verify the runtime in a newly created account in case service versions have since changed. TinkerHost's [database guide](https://community.tinkerhost.net/forum/d/35-how-to-connect-to-your-mysql-database) supports PDO and says the database server, username and password are shown in the client panel, while the database name is in the control panel. It requires the website and database on the same account and does not support external MySQL connections. A June 2025 [support reply](https://community.tinkerhost.net/forum/d/348-mariadb-1011) estimated free-hosting MariaDB at 10.4 or slightly higher, but explicitly said the admin was not certain, so check the actual account's database version. The official [SQL import guide](https://community.tinkerhost.net/forum/d/36-how-to-import-a-sql-file-database-backup-file) says create the database first, then import through phpMyAdmin; large imports may time out. Public pages do not confirm account-specific PHP extensions, per-database limits, SSH, custom document-root controls, or a private environment-variable panel. Confirm those details before uploading.

The safest layout is to keep `dynamic/src/`, `dynamic/database/`, and `dynamic/bin/` outside the web document root, and point the web root at `dynamic/public/`. If the host only provides a fixed `public_html` directory, place the contents of `dynamic/public/` in `public_html` and place `dynamic/src/` alongside the web root so the relative include still resolves. Keep `dynamic/src/local-config.php` outside the document root if using the private-file method. Do not place SQL exports or private config files in a publicly served directory. Add database values through the hosting control panel if it supports private environment variables. Set `PORTFOLIO_DB_HOST` (or `db_host` in the private config) to the exact SQL server shown in the TinkerHost panel; do not assume `localhost` or `127.0.0.1`. Do not deploy real passwords inside a tracked PHP file.

Create the database in the TinkerHost control panel, import `dynamic/database/portfolio.sql` with phpMyAdmin, then set private credentials and upload the app over the offered file manager or FTP. Enable HTTPS and force secure session cookies. TinkerHost's documentation warns that phpMyAdmin may time out on large SQL imports; this seed export is small. A TinkerHost account and its private database credentials are required to test the actual deployment. No TinkerHost account was created and no paid service was selected as part of this repository implementation.

## Existing and dynamic addresses

The original static site is `https://amarstephaniefate17-hue.github.io/Personal-Portfolio/`. It is kept on the repository's `main` branch and Pages workflow. A dynamic PHP address and its admin path do not exist until a compatible PHP/MySQL host account is configured and the live app is uploaded and tested. Do not submit the example paths as live URLs before verification.

## Admin modules

The dashboard includes profile information, skills, projects, education, work/activities, achievements and certificates, contact messages, website settings, and administrator account settings. Public contact submissions are stored in MySQL and can be read, marked read/unread, or deleted in the dashboard. Project and achievement images and the profile picture are limited to verified JPEG, PNG, or WebP files under 5 MiB.

## Deployment verification checklist

- [ ] Confirm PHP version and `pdo_mysql`/Fileinfo availability.
- [ ] Run `php dynamic/bin/verify-install.php` on the configured host.
- [ ] Import schema and verify all tables and seeded records.
- [ ] Create the first admin using the private setup environment and terminal.
- [ ] Verify public pages read current DB values.
- [ ] Verify admin login, logout, account settings and access protection.
- [ ] Exercise create/edit/delete for every content collection.
- [ ] Submit a contact form and manage its message.
- [ ] Test valid and invalid image uploads; verify uploaded scripts cannot execute.
- [ ] Test mobile navigation and theme toggle.
- [ ] Verify HTTPS, direct protected-page behavior and database persistence.
- [ ] Recheck the unchanged static URL and record the actual dynamic/admin URLs.
