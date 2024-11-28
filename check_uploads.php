<?php
// Place this file in your project root to check upload settings
error_reporting(E_ALL);
ini_set('display_errors', 1);

$upload_dir = __DIR__ . '/uploads/documents';

echo "<h2>Upload Directory Check</h2>";
echo "<pre>";

// Check if directory exists
if (!file_exists($upload_dir)) {
    echo "Creating upload directory...\n";
    mkdir($upload_dir, 0777, true);
}

// Check permissions
echo "Upload directory: " . $upload_dir . "\n";
echo "Exists: " . (file_exists($upload_dir) ? 'Yes' : 'No') . "\n";
echo "Writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "\n";
echo "Permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "\n";

// Check PHP upload settings
echo "\nPHP Upload Settings:\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";

// Test file creation
$test_file = $upload_dir . '/test.txt';
$result = @file_put_contents($test_file, 'test');
echo "\nTest file creation: " . ($result !== false ? 'Success' : 'Failed') . "\n";
if ($result !== false) {
    unlink($test_file);
}

echo "</pre>"; 