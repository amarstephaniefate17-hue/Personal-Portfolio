-- Stephanie Fate R. Amar portfolio schema. Import into a newly created MySQL database.
SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS administrators (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  display_name VARCHAR(120) NOT NULL DEFAULT 'Administrator',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS personal_information (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(160) NOT NULL,
  headline VARCHAR(200) NOT NULL,
  introduction TEXT NOT NULL,
  biography TEXT NOT NULL,
  goals TEXT NOT NULL,
  interests TEXT NOT NULL,
  email VARCHAR(254) NOT NULL DEFAULT '',
  phone VARCHAR(50) NOT NULL DEFAULT '',
  location VARCHAR(160) NOT NULL DEFAULT '',
  facebook_url VARCHAR(500) NOT NULL DEFAULT '',
  profile_image VARCHAR(500) NOT NULL DEFAULT 'profile.jpg',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS skills (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  category VARCHAR(100) NOT NULL DEFAULT 'Technical',
  proficiency TINYINT UNSIGNED NOT NULL DEFAULT 50,
  label VARCHAR(50) NOT NULL DEFAULT 'Learning',
  sort_order INT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_skills_public_order (is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  category VARCHAR(100) NOT NULL DEFAULT 'school',
  description TEXT NOT NULL,
  technologies VARCHAR(500) NOT NULL DEFAULT '',
  image_path VARCHAR(500) NOT NULL DEFAULT '',
  project_url VARCHAR(500) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_projects_public_order (is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS education (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  institution VARCHAR(180) NOT NULL,
  qualification VARCHAR(180) NOT NULL,
  start_year VARCHAR(20) NOT NULL DEFAULT '',
  end_year VARCHAR(20) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_education_public_order (is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS experiences (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  organization VARCHAR(180) NOT NULL,
  role VARCHAR(180) NOT NULL,
  start_date VARCHAR(40) NOT NULL DEFAULT '',
  end_date VARCHAR(40) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_experiences_public_order (is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS achievements (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  issuer VARCHAR(180) NOT NULL DEFAULT '',
  award_date VARCHAR(40) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  image_path VARCHAR(500) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_achievements_public_order (is_published, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(254) NOT NULL,
  subject VARCHAR(180) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_messages_read_created (is_read, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS website_settings (
  setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
  setting_value TEXT NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  ip_hash CHAR(64) NOT NULL,
  attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_login_attempts_ip_time (ip_hash, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_attempts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  ip_hash CHAR(64) NOT NULL,
  attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_contact_attempts_ip_time (ip_hash, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO personal_information (id,full_name,headline,introduction,biography,goals,interests,email,facebook_url,profile_image) VALUES
(1,'Stephanie Fate R. Amar','Information Technology · Student','I\'m an Information Technology student building practical skills in web development, programming, and digital solutions through coursework and projects.','I am Stephanie Fate R. Amar, an Information Technology student developing practical skills through coursework, projects, and self-directed learning. I enjoy discovering how technology can turn ideas into useful digital experiences.','My goal is to strengthen my technical foundation, gain real-world experience, and eventually contribute to meaningful software and web projects.','Web development, programming, technology, UI/UX design, creative projects, learning new tools, and exploring digital ideas.','amarstephanjiefate.20@emal.com','', 'profile.jpg')
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name);

INSERT INTO skills (name,category,proficiency,label,sort_order) VALUES
('HTML','Web Development',90,'Learning',1),('CSS','Web Development',85,'Learning',2),('JavaScript','Programming',78,'Learning',3),('Java','Programming',72,'72%',4),('Python','Programming',40,'Progress',5),('UI/UX Design','Design',75,'75%',6),('Databases','Data',70,'70%',7),('Problem Solving','General',85,'85%',8);

INSERT INTO projects (title,slug,category,description,technologies,sort_order) VALUES
('Personal Portfolio Website','personal-portfolio-website','web school','A responsive multi-page portfolio designed to present my background, skills, projects, and contact information.','HTML, CSS, JavaScript',1),
('Ride IT','ride-it','web school','A local ride-service concept and flowchart focused on a simple ride-booking experience with vehicle and location choices.','UI Planning, Flowchart, UX',2),
('Fortugaleza Medical Record Management System','fortugaleza-medical-record-management-system','programming school','A medical record management system designed to organize patient information and make basic record management more efficient.','Java, Database, CRUD',3),
('Interactive Web Design Project','interactive-web-design-project','web school','A school-based web design project focused on responsive layouts, user-friendly navigation, and interactive page elements.','HTML, CSS, JavaScript',4),
('Crown Care: Curl Crave','crown-care-curl-crave','school','A product advertisement animation created in Adobe Animate, inspired by my personal experience with frizzy hair and turning that experience into a creative solution.','Adobe Animate, Animation, Advertisement',5),
('Squash and Stretch Animation','squash-and-stretch-animation','school','An Adobe Animate project demonstrating the squash and stretch animation principle through the movement and deformation of a character.','Adobe Animate, Animation, Squash & Stretch',6);

INSERT INTO education (institution,qualification,start_year,end_year,description,sort_order) VALUES
('Negros Oriental State University','Information Technology','2026','2027 (Current)','Currently pursuing studies in Information Technology, with a focus on programming, web development, databases, and digital problem-solving.',1),
('Kalumboyan National High School','High School','2018','2024','High school education.',2);

INSERT INTO experiences (organization,role,start_date,end_date,description,sort_order) VALUES
('Academic Projects','Student Project Experience','','Current','Participated in programming, web development, design, and technology-related school activities.',1),
('Technology Training and Workshops','Web Development','','','HTML, CSS, JavaScript, responsive web design, and interactive web development.',2),
('Academic Projects','Java Programming','','','Object-oriented programming and programming logic.',3);

INSERT INTO website_settings (setting_key,setting_value) VALUES
('site_title','Stephanie Fate R. Amar | Personal Portfolio'),
('site_tagline','Curious by nature. Practical by design.'),
('accent_color','#7c4dff'),
('contact_email','amarstephanjiefate.20@emal.com'),
('facebook_name','Stephanie Amar'),
('logo_text','Stephanie.'),
('footer_text','© 2026 Stephanie Fate R. Amar · Personal Portfolio')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
