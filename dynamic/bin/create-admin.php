<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__).'/src/bootstrap.php';
function prompt_hidden(string $label): string
{
    fwrite(STDOUT,$label);
    $stty=trim((string)shell_exec('stty -g 2>/dev/null'));
    if($stty!=='')shell_exec('stty -echo');
    $line=fgets(STDIN);
    if($stty!=='')shell_exec('stty '.escapeshellarg($stty));
    fwrite(STDOUT,"\n");
    return trim((string)$line);
}
if((int)db()->query('SELECT COUNT(*) FROM administrators')->fetchColumn()>0){fwrite(STDERR,"An administrator already exists. Use the dashboard's account settings to change it.\n");exit(1);}
$username=trim((string)readline('Administrator username (3–80 letters/numbers/._-): '));
if(!preg_match('/^[A-Za-z0-9_.-]{3,80}$/',$username)){fwrite(STDERR,"Invalid username.\n");exit(1);}
$name=trim((string)readline('Display name: '));if($name==='')$name='Administrator';
$password=prompt_hidden('Password (at least 12 characters): ');$confirm=prompt_hidden('Confirm password: ');
if(strlen($password)<12||!hash_equals($password,$confirm)){fwrite(STDERR,"Passwords must match and be at least 12 characters. Nothing was saved.\n");exit(1);}
$stmt=db()->prepare('INSERT INTO administrators(username,password_hash,display_name) VALUES(?,?,?)');
$stmt->execute([$username,password_hash($password,PASSWORD_DEFAULT),$name]);
unset($password,$confirm);
fwrite(STDOUT,"Administrator account created. Sign in at /admin/login.php.\n");
