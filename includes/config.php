<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Change this
define('DB_PASS', '');      // Change this
define('DB_NAME', 'st_rita_wedding');

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'your-email@gmail.com');  // Change this to your Gmail address
define('SMTP_PASSWORD', 'your-app-password');     // Change this to your Gmail app password
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_FROM_NAME', 'St. Rita Mission Station');

// System configuration
define('SITE_NAME', 'St. Rita Mission Station');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Site URL Configuration
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$baseDir = dirname($_SERVER['PHP_SELF']);
$baseDir = $baseDir === '\\' ? '/' : $baseDir . '/';
define('SITE_URL', $protocol . $host . $baseDir);
