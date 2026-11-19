<?php
declare(strict_types=1);
require_once dirname(__DIR__,2).'/src/bootstrap.php';
require_admin();
require_once dirname(__DIR__,2).'/src/admin_layout.php';
try {
    $counts=[];
    foreach(['projects'=>'Projects','skills'=>'Skills','messages'=>'Messages','education'=>'Education','experiences'=>'Experience','achievements'=>'Achievements'] as $table=>$label) { $counts[$label]=(int)db()->query("SELECT COUNT(*) FROM `$table`")->fetchColumn(); }
    $recent=db()->query('SELECT id,name,email,subject,is_read,created_at FROM messages ORDER BY created_at DESC LIMIT 8')->fetchAll();
} catch(Throwable $ex) { http_response_code(503); error_log('Portfolio dashboard error: '.$ex->getMessage()); exit('The dashboard cannot connect to the database. Check the installation instructions.'); }
admin_header('index.php','Dashboard overview');
?><div class="admin-grid"><?php foreach($counts as $label=>$count): ?><div class="admin-card admin-stat"><span class="muted"><?= e($label) ?></span><strong><?= $count ?></strong></div><?php endforeach; ?></div><section class="admin-card"><div class="admin-top"><h2>Recent messages</h2><a class="admin-link" href="messages.php">Manage messages</a></div><?php if(!$recent): ?><p class="muted">No messages received yet.</p><?php else: ?><table class="admin-table"><thead><tr><th>From</th><th>Subject</th><th>Received</th><th>Status</th></tr></thead><tbody><?php foreach($recent as $m): ?><tr class="<?= $m['is_read']?'':'unread' ?>"><td><?= e($m['name']) ?><br><small><?= e($m['email']) ?></small></td><td><a class="admin-link" href="messages.php?id=<?= (int)$m['id'] ?>"><?= e($m['subject']) ?></a></td><td><?= e($m['created_at']) ?></td><td><?= $m['is_read']?'Read':'Unread' ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></section><?php admin_footer(); ?>
