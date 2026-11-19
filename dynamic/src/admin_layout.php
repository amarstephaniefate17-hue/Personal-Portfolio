<?php
function admin_header(string $active, string $title): void
{
    $links = ['index.php'=>'Overview','profile.php'=>'Personal information','manage.php?type=skills'=>'Skills','manage.php?type=projects'=>'Projects','manage.php?type=education'=>'Education','manage.php?type=experiences'=>'Experience','manage.php?type=achievements'=>'Achievements','messages.php'=>'Messages','settings.php'=>'Website settings','account.php'=>'Account settings'];
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' | Portfolio Admin</title><link rel="stylesheet" href="../assets/style.css"><link rel="stylesheet" href="../assets/app.css"></head><body class="admin-body"><div class="admin-shell"><aside class="admin-side"><div class="admin-brand">Stephanie<span style="color:#a98bff">.</span><small style="display:block;color:#bbb;font-weight:400">Portfolio Admin</small></div><nav>';
    foreach ($links as $href=>$label) { $isActive = ($href===$active || (strpos($active,'manage.php')===0 && strpos($href,'type='.($_GET['type']??''))!==false)); echo '<a class="'.($isActive?'active':'').'" href="'.$href.'">'.e($label).'</a>'; }
    echo '</nav><div class="admin-profile">Signed in as '.e($_SESSION['admin_name'] ?? 'Administrator').'<form method="post" action="logout.php" style="margin-top:.8rem">'.csrf_field().'<button class="btn btn-small" type="submit">Sign out</button></form></div></aside><main class="admin-main"><div class="admin-top"><div><div class="eyebrow">Portfolio management</div><h1>'.e($title).'</h1></div><a class="btn btn-outline" href="../index.php" target="_blank" rel="noopener">View website ↗</a></div>';
    $flash = take_flash(); if ($flash) echo '<p class="'.(($flash['type']??'')==='error'?'admin-error':'admin-flash').'">'.e($flash['message']).'</p>';
}
function admin_footer(): void { echo '</main></div></body></html>'; }
