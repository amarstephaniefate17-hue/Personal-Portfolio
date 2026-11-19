# Deployment Verification — Personal-Portfolio

**Checked:** 20 November 2026  
**Repository:** `amarstephaniefate17-hue/Personal-Portfolio`  
**Dynamic implementation branch:** `feature/php-mysql-portfolio`  
**Latest implementation commit:** `d722317` (pushed to `origin/feature/php-mysql-portfolio`)

## Status summary

The PHP/MySQL implementation is committed and pushed to its own feature branch. The static GitHub Pages site remains on `main`; the original snapshot is preserved at `backup/pre-dynamic-portfolio-20261120`. No PHP files were merged to `main`, and GitHub Pages has not been repointed. The current static portfolio address remains:

`https://amarstephaniefate17-hue.github.io/Personal-Portfolio/`

**TinkerHost deployment was not performed.** No account was created, no account-specific PHP/database settings were available, and no live dynamic or administrator URL exists yet. Do not use example paths as a deployment claim.

## Local verification performed

The project was checked locally with PHP 8.3.6 and MariaDB 10.11 on a disposable test database. The final pass reported:

- No PHP syntax errors in the application and CLI PHP files.
- JavaScript syntax validation passed for `dynamic/public/assets/app.js`.
- `git diff --check` passed.
- `dynamic/bin/verify-install.php` passed checks for the PHP version, `pdo_mysql`, Fileinfo, MySQL connection, all expected schema tables, seeded profile/content rows, initial test administrator, and writable upload directories.
- Prior HTTP integration checks passed for public pages; login and access controls; CSRF protection; create/edit/delete operations across profile, skills, projects, education, experience and achievements; contact submission and persistence; XSS output escaping; profile and settings updates; and valid/invalid image upload handling.
- The disposable `portfolio_test` database, its local test user, and the generated QA upload were removed after testing. No administrator credentials or database secrets were committed.

These checks are local QA only; they do not establish that a third-party host has the required configuration or that a public deployment works.

## TinkerHost verification and limits

Public TinkerHost materials reviewed for this project state that its free hosting advertises PHP, MySQL/phpMyAdmin, FTP/file-manager access, SSL, and no credit card at signup. The public PHP upgrade notice says free hosting moved to PHP 8.3 in April 2025. Its database guide documents PDO and says the website and MySQL database must be on the same account; external MySQL connections are not supported. The SQL import guide describes importing through phpMyAdmin. A June 2025 community response only estimated MariaDB 10.4 or slightly higher and explicitly said the responder was not certain.

Those general statements do not verify the configuration of a newly created account. Specifically, the account-specific database hostname/version, enabled PHP extensions, limits, environment-variable facilities, document-root controls, and actual import/upload behavior remain unverified. The user's TinkerHost account and its private setup information are needed before deployment can be attempted. Do not put passwords in GitHub or this report.

Sources:

- Free hosting overview: https://tinkerhost.net/free
- PHP 8.3 upgrade notice: https://community.tinkerhost.net/forum/d/338-php-83-upgrade
- MySQL/PDO connection guide: https://community.tinkerhost.net/forum/d/35-how-to-connect-to-your-mysql-database
- MariaDB version discussion (uncertain estimate): https://community.tinkerhost.net/forum/d/348-mariadb-1011
- SQL import guide: https://community.tinkerhost.net/forum/d/36-how-to-import-a-sql-file-database-backup-file

## Next action required for live deployment

The user must create or access their own TinkerHost account and database, then provide the account-specific hosting details through a private channel (not a GitHub commit). Once those user-only steps are complete, verify the account's runtime and database settings, configure private credentials, import the seed SQL into a new database, create the initial administrator through the CLI setup tool, deploy the public directory, and run the documented live checks. Request administrator credentials privately only when the real setup is ready; never place them in repository files.
