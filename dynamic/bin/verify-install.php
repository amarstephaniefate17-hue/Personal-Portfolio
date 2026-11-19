<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__).'/src/bootstrap.php';
$failed=false;
function result_line(string $label, bool $ok, string $detail=''): void
{
    global $failed;
    if(!$ok)$failed=true;
    fwrite(STDOUT,($ok?'PASS':'FAIL').' '.$label.($detail!==''?': '.$detail:'')."\n");
}
result_line('PHP version',PHP_VERSION_ID>=80300,PHP_VERSION);
foreach(['pdo_mysql','fileinfo'] as $extension)result_line('PHP extension '.$extension,extension_loaded($extension));
try{
    $pdo=db();
    result_line('MySQL connection',true,'connected using the configured private settings');
    $required=['administrators','personal_information','skills','projects','education','experiences','achievements','messages','website_settings','login_attempts','contact_attempts'];
    $found=$pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach($required as $table)result_line('Database table '.$table,in_array($table,$found,true));
    foreach(['personal_information','skills','projects','education','experiences'] as $table){if(in_array($table,$found,true)){ $count=(int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();result_line('Seed records '.$table,$count>0,(string)$count.' rows');}}
    if(in_array('administrators',$found,true)){$count=(int)$pdo->query('SELECT COUNT(*) FROM administrators')->fetchColumn();result_line('Initial administrator',$count>0,$count>0?'configured':'create one with bin/create-admin.php');}
}catch(Throwable $exception){error_log('Portfolio install verification failed: '.$exception->getMessage());result_line('MySQL connection',false,'check private connection settings; details were logged');}
foreach(['profile','projects','achievements'] as $folder){$path=APP_ROOT.'/public/uploads/'.$folder;result_line('Upload folder '.$folder,is_dir($path)&&is_writable($path));}
exit($failed?1:0);
