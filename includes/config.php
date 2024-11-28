<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Change this
define('DB_PASS', '');      // Change this
define('DB_NAME', 'st_rita_wedding');

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your-email@gmail.com');  // Change this
define('SMTP_PASS', 'your-app-password');     // Change this
define('SMTP_PORT', 587);

// System configuration
define('SITE_NAME', 'St. Rita Mission Station');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
