<?php
declare(strict_types=1);
require_once dirname(__DIR__,2).'/src/bootstrap.php';require_admin();require_once dirname(__DIR__,2).'/src/admin_layout.php';
$fields=['site_title'=>'Website title','site_tagline'=>'Tagline','accent_color'=>'Accent color (hex)','contact_email'=>'Public contact email','facebook_name'=>'Facebook label','logo_text'=>'Logo text','footer_text'=>'Footer text'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$values=[];foreach($fields as $key=>$label)$values[$key]=trim((string)($_POST[$key]??''));
 if($values['site_title']===''||$values['logo_text']===''||!preg_match('/^#[0-9a-fA-F]{6}$/',$values['accent_color'])||($values['contact_email']!==''&&!filter_var($values['contact_email'],FILTER_VALIDATE_EMAIL))){flash('error','Website title, logo text, a six-digit hex color, and valid email are required.');redirect('admin/settings.php');}
 $s=db()->prepare('INSERT INTO website_settings(setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');foreach($values as $key=>$value)$s->execute([$key,$value]);flash('success','Website settings saved.');redirect('admin/settings.php');
}
$values=db()->query('SELECT setting_key,setting_value FROM website_settings')->fetchAll(PDO::FETCH_KEY_PAIR);
admin_header('settings.php','Website settings');
?><section class="admin-card"><p class="muted">These settings affect the public website. Use a six-digit HEX value for the accent color.</p><form method="post" class="admin-form" style="margin-top:1rem"><?= csrf_field() ?><?php foreach($fields as $key=>$label): ?><label for="<?= e($key) ?>"><?= e($label) ?></label><input id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e($values[$key]??'') ?>" <?= $key==='contact_email'?'type="email"':'' ?> <?= $key==='accent_color'?'pattern="#[0-9a-fA-F]{6}"':'' ?>><?php endforeach; ?><button class="btn btn-primary" type="submit">Save settings</button></form></section><?php admin_footer(); ?>
