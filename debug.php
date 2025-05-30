<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>System Check</h2>";
echo "<pre>";

// Check PHP Version
echo "PHP Version: " . phpversion() . "\n";

// Check upload settings 
echo "\nUpload Settings:\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";

// Check directories
$directories = [
    'uploads' => __DIR__ . '/uploads',
    'documents' => __DIR__ . '/uploads/documents'
];

echo "\nDirectory Permissions:\n";
foreach ($directories as $name => $path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        echo "$name directory created: $path\n";
    }
    echo "$name directory (" . $path . "):\n";
    echo "- Exists: " . (file_exists($path) ? 'Yes' : 'No') . "\n";
    echo "- Writable: " . (is_writable($path) ? 'Yes' : 'No') . "\n";
    echo "- Permissions: " . substr(sprintf('%o', fileperms($path)), -4) . "\n";
}

// Check database connection
require_once 'includes/config.php';
require_once 'includes/db_connection.php';

echo "\nDatabase Connection:\n";
try {
    $db = Database::getInstance()->getConnection();
    echo "- Connected successfully\n";
    
    // Check tables
    $tables = ['users', 'bookings', 'documents', 'document_requirements'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        echo "- Table '$table': " . ($stmt->rowCount() > 0 ? 'Exists' : 'Missing') . "\n";
        
        if ($stmt->rowCount() > 0) {
            $cols = $db->query("SHOW COLUMNS FROM $table");
            echo "  Columns:\n";
            while ($col = $cols->fetch(PDO::FETCH_ASSOC)) {
                echo "    - {$col['Field']} ({$col['Type']})\n";
            }
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}

echo "</pre>"; 
