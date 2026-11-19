<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/bootstrap.php';

try {
    $profile = db()->query('SELECT * FROM personal_information ORDER BY id LIMIT 1')->fetch() ?: [];
    $page = preg_replace('/[^a-z]/', '', (string)($_GET['page'] ?? 'home')) ?: 'home';
    $allowedPages = ['home','about','skills','projects','resume','contact'];
    if (!in_array($page, $allowedPages, true)) { http_response_code(404); $page='home'; }
    $skills = records('skills');
    $projects = records('projects');
    $education = records('education');
    $experiences = records('experiences');
    $achievements = records('achievements');
    $notice = $_SESSION['_contact_notice'] ?? null;
    unset($_SESSION['_contact_notice']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'contact') {
        verify_csrf();
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $subject = trim((string)($_POST['subject'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));
        $honeypot = trim((string)($_POST['website'] ?? ''));
        if ($honeypot !== '') { $_SESSION['_contact_notice'] = ['ok'=>true,'text'=>'Thanks — your message has been received.']; redirect('?page=contact'); }
        elseif (text_length($name) < 2 || text_length($name) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL) || text_length($subject) < 2 || text_length($subject) > 180 || text_length($message) < 10 || text_length($message) > 5000) {
            $notice = ['ok'=>false,'text'=>'Please check the form. Name, subject, valid email and a message of 10–5,000 characters are required.'];
        } else {
            $ipHash = hash_hmac('sha256', (string)($_SERVER['REMOTE_ADDR'] ?? 'unknown'), (string)app_config()['login_salt']);
            $count = db()->prepare('SELECT COUNT(*) FROM contact_attempts WHERE ip_hash=? AND attempted_at > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 HOUR)');
            $count->execute([$ipHash]);
            if ((int)$count->fetchColumn() >= 4) $notice = ['ok'=>false,'text'=>'Please wait before sending another message.'];
            else {
                db()->prepare('INSERT INTO contact_attempts (ip_hash) VALUES (?)')->execute([$ipHash]);
                db()->prepare('DELETE FROM contact_attempts WHERE attempted_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY)')->execute();
                $stmt = db()->prepare('INSERT INTO messages (name,email,subject,message) VALUES (?,?,?,?)');
                $stmt->execute([$name,$email,$subject,$message]);
                $_SESSION['_contact_notice'] = ['ok'=>true,'text'=>'Thanks — your message has been sent.'];
                redirect('?page=contact');
            }
        }
    }
} catch (Throwable $exception) {
    http_response_code(503);
    if (app_config()['debug']) { error_log((string)$exception); }
    exit('The portfolio is temporarily unavailable. Please check the database configuration and try again.');
}
$fullName = $profile['full_name'] ?? 'Stephanie Fate R. Amar';
$tagline = setting('site_tagline','Curious by nature. Practical by design.');
$title = setting('site_title',$fullName . ' | Personal Portfolio');
$accent = setting('accent_color', '#7c4dff');
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $accent)) $accent = '#7c4dff';
$nav = ['home'=>'Home','about'=>'About','skills'=>'Skills','projects'=>'Projects','resume'=>'Resume','contact'=>'Contact'];
$h = static fn($v) => e($v);
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="<?= $h($profile['introduction'] ?? '') ?>"><title><?= $h(ucfirst($page) . ' | ' . $title) ?></title><link rel="stylesheet" href="assets/style.css"><link rel="stylesheet" href="assets/app.css"></head>
<body style="--accent:<?= $h($accent) ?>"><header><div class="container navbar"><a class="logo" href="index.php"><?= $h(setting('logo_text','Stephanie.')) ?></a><nav><ul class="nav-links"><?php foreach($nav as $key=>$label): ?><li><a class="<?= $page===$key?'active':'' ?>" href="?page=<?= $h($key) ?>"><?= $h($label) ?></a></li><?php endforeach; ?></ul></nav><div class="nav-actions"><button class="icon-btn" id="themeBtn" aria-label="Toggle theme">☾</button><button class="menu-btn" aria-label="Open menu" aria-expanded="false">☰</button></div></div></header>
<main>
<?php if ($page === 'home'): ?>
<section class="hero container"><div><div class="eyebrow"><?= $h($profile['headline'] ?? '') ?></div><h1>Hello, I'm <span><?= $h($fullName) ?></span></h1><p class="lead"><?= $h($profile['introduction'] ?? '') ?></p><div class="buttons"><a class="btn btn-primary" href="?page=projects">Explore my work ↗</a><a class="btn btn-outline" href="?page=contact">Get in touch</a></div></div><div class="profile-card"><img src="<?= $h($profile['profile_image'] ?? 'profile.jpg') ?>" alt="<?= $h($fullName) ?>" class="profile-photo"><div class="profile-caption"><span><?= $h($profile['headline'] ?? '') ?></span><span><?= $h($tagline) ?></span></div></div></section>
<section><div class="container"><div class="section-head"><div><div class="eyebrow">01 / Profile</div><h2>Learning by doing.</h2></div><p><?= $h($profile['biography'] ?? '') ?></p></div><a class="btn btn-outline" href="?page=about">Read my background ↗</a></div></section>
<section><div class="container"><div class="section-head"><div><div class="eyebrow">02 / Portfolio</div><h2>Take a closer look.</h2></div><p>Browse my learning areas, project work, education, and ways to connect with me.</p></div><div class="home-link-grid"><a class="home-link" href="?page=skills"><small>01</small><strong>Skills ↗</strong></a><a class="home-link" href="?page=projects"><small>02</small><strong>Projects ↗</strong></a><a class="home-link" href="?page=resume"><small>03</small><strong>Resume & Education ↗</strong></a><a class="home-link" href="?page=contact"><small>04</small><strong>Contact ↗</strong></a></div></div></section>
<?php elseif ($page === 'about'): ?>
<section class="page-hero container"><div class="eyebrow">About me</div><h1>A little about <span><?= $h($fullName) ?></span></h1><p class="lead"><?= $h($profile['introduction'] ?? '') ?></p></section><section><div class="container grid grid-2"><div class="card"><h2>Background</h2><p class="muted"><?= nl2br($h($profile['biography'] ?? '')) ?></p></div><div class="card"><h2>Goals</h2><p class="muted"><?= nl2br($h($profile['goals'] ?? '')) ?></p></div><div class="card"><h2>Interests</h2><p class="muted"><?= nl2br($h($profile['interests'] ?? '')) ?></p></div><div class="card"><h2>Contact</h2><p class="muted"><?= $h($profile['email'] ?? '') ?><br><?= $h($profile['location'] ?? '') ?></p></div></div></section>
<?php elseif ($page === 'skills'): ?>
<section class="page-hero container"><div class="eyebrow">What I can do</div><h1>My <span>skills</span></h1><p class="lead">A growing set of technical and creative skills developed through school and practice.</p></section><section><div class="container"><div class="grid grid-4"><?php foreach($skills as $skill): ?><div class="card skill-card"><div class="skill-top"><span><?= $h($skill['name']) ?></span><span><?= $h($skill['label']) ?></span></div><small class="muted"><?= $h($skill['category']) ?></small><div class="bar"><span style="width:<?= (int)$skill['proficiency'] ?>%"></span></div></div><?php endforeach; ?></div></div></section>
<?php elseif ($page === 'projects'): ?>
<section class="page-hero container"><div class="eyebrow">My work</div><h1>Selected <span>projects</span></h1><p class="lead">Academic and personal projects that represent my growing technical skills.</p></section><section><div class="container"><div class="filters"><button class="filter active" data-filter="all">All</button><button class="filter" data-filter="web">Web</button><button class="filter" data-filter="programming">Programming</button><button class="filter" data-filter="school">School</button></div><div class="grid grid-2"><?php foreach($projects as $i=>$project): $categories=preg_split('/\s+/',strtolower($project['category'])); ?><article class="card project" data-category="<?= $h(implode(' ', $categories)) ?>"><?php if($project['image_path']): ?><img class="project-img uploaded-project" src="<?= $h($project['image_path']) ?>" alt="<?= $h($project['title']) ?>"><?php else: ?><div class="project-img" aria-hidden="true"><?= sprintf('%02d',$i+1) ?></div><?php endif; ?><h3><?= $h($project['title']) ?></h3><p class="muted"><?= nl2br($h($project['description'])) ?></p><div class="tags"><?php foreach(explode(',',$project['technologies']) as $tag): ?><span class="tag"><?= $h(trim($tag)) ?></span><?php endforeach; ?></div><?php if($project['project_url']): ?><a class="text-link" href="<?= $h($project['project_url']) ?>" rel="noopener noreferrer" target="_blank">View project ↗</a><?php endif; ?></article><?php endforeach; ?></div></div></section>
<?php if($projects): ?><div class="modal" id="projectModal" aria-hidden="true"><div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalDescription"><button class="modal-close" type="button" aria-label="Close project preview">×</button><div class="modal-img" aria-hidden="true">✦</div><h2 id="modalTitle">Project preview</h2><p id="modalDescription" class="muted"></p></div></div><?php endif; ?>
<?php elseif ($page === 'resume'): ?>
<section class="page-hero container"><div class="eyebrow">My journey</div><h1>Resume & <span>experience</span></h1><p class="lead">An editable overview of my education, activities, training, and developing experience.</p></section><section><div class="container grid grid-2"><div class="card"><h2>Education</h2><div class="timeline"><?php foreach($education as $item): ?><div class="timeline-item"><h3><?= $h($item['qualification']) ?></h3><p class="muted"><strong><?= $h($item['institution']) ?></strong> · <?= $h(trim($item['start_year'].'–'.$item['end_year'],'–')) ?></p><p class="muted"><?= $h($item['description']) ?></p></div><?php endforeach; ?></div></div><div class="card"><h2>Experience & Activities</h2><div class="timeline"><?php foreach($experiences as $item): ?><div class="timeline-item"><h3><?= $h($item['role']) ?></h3><p class="muted"><strong><?= $h($item['organization']) ?></strong> · <?= $h(trim($item['start_date'].'–'.$item['end_date'],'–')) ?></p><p class="muted"><?= $h($item['description']) ?></p></div><?php endforeach; ?></div></div><div class="card achievements"><h2>Achievements & Certificates</h2><?php if(!$achievements): ?><p class="muted">Achievements and certificates will appear here when added.</p><?php else: ?><?php foreach($achievements as $item): ?><div class="timeline-item"><h3><?= $h($item['title']) ?></h3><p class="muted"><?= $h($item['issuer']) ?> · <?= $h($item['award_date']) ?></p><p class="muted"><?= $h($item['description']) ?></p></div><?php endforeach; ?><?php endif; ?></div></div></section>
<?php else: ?>
<section class="page-hero container"><div class="eyebrow">Let's connect</div><h1>Get in <span>touch</span></h1><p class="lead">Have a question, project idea, or opportunity? Send me a message.</p></section><section><div class="container contact-grid"><div class="card"><h2>Contact information</h2><div class="contact-list" style="margin-top:1.2rem"><div class="contact-item"><div class="contact-icon">@</div><div><strong>Email</strong><p class="muted"><?= $h($profile['email'] ?? '') ?></p></div></div><?php if(!empty($profile['facebook_url'])): ?><div class="contact-item"><div class="contact-icon">f</div><div><strong>Facebook</strong><p class="muted"><a href="<?= $h($profile['facebook_url']) ?>" rel="noopener noreferrer"><?= $h(setting('facebook_name','Facebook')) ?></a></p></div></div><?php endif; ?></div></div><div class="card"><h2>Send a message</h2><?php if($notice): ?><p class="notice <?= $notice['ok']?'success':'error' ?>" role="status"><?= $h($notice['text']) ?></p><?php endif; ?><form method="post" action="?page=contact" style="margin-top:1rem" novalidate><?= csrf_field() ?><div class="honeypot" aria-hidden="true"><label>Leave this empty<input name="website" tabindex="-1" autocomplete="off"></label></div><div class="field"><label for="name">Name</label><input required maxlength="120" id="name" name="name" value="<?= $h($_POST['name'] ?? '') ?>" autocomplete="name"></div><div class="field"><label for="email">Email</label><input required maxlength="254" id="email" name="email" type="email" value="<?= $h($_POST['email'] ?? '') ?>" autocomplete="email"></div><div class="field"><label for="subject">Subject</label><input required maxlength="180" id="subject" name="subject" value="<?= $h($_POST['subject'] ?? '') ?>"></div><div class="field"><label for="message">Message</label><textarea required minlength="10" maxlength="5000" id="message" name="message"><?= $h($_POST['message'] ?? '') ?></textarea></div><button class="btn btn-primary" type="submit">Send Message</button></form></div></div></section>
<?php endif; ?>
</main><footer><div class="container"><?= $h(setting('footer_text','© 2026 Stephanie Fate R. Amar · Personal Portfolio')) ?></div></footer><button id="topBtn" class="icon-btn" aria-label="Scroll to top">↑</button><script src="assets/app.js" defer></script></body></html>
