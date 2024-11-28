<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/Authentication.php';

$auth = new Authentication();

// Ensure only admins can access this endpoint
if (!$auth->isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

try {
    // Create backup directory if it doesn't exist
    $backup_dir = '../backups';
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }

    // Create .htaccess to prevent direct access to backup files
    $htaccess_file = $backup_dir . '/.htaccess';
    if (!file_exists($htaccess_file)) {
        file_put_contents($htaccess_file, "Deny from all");
    }

    // Get database credentials from config
    $db = Database::getInstance()->getConnection();
    $db_name = DB_NAME;
    $db_user = DB_USER;
    $db_pass = DB_PASS;
    $db_host = DB_HOST;

    // Generate backup filename with timestamp
    $date = date("Y-m-d-H-i-s");
    $backup_file = $backup_dir . "/backup-" . $date . ".sql";

    // Command for mysqldump
    $command = sprintf(
        'mysqldump --opt -h %s -u %s -p%s %s > %s',
        escapeshellarg($db_host),
        escapeshellarg($db_user),
        escapeshellarg($db_pass),
        escapeshellarg($db_name),
        escapeshellarg($backup_file)
    );

    // Execute backup command
    exec($command, $output, $return_var);

    if ($return_var === 0) {
        // Compress the SQL file
        $zip = new ZipArchive();
        $zip_file = $backup_file . '.zip';
        
        if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($backup_file, basename($backup_file));
            $zip->close();

            // Remove the uncompressed SQL file
            unlink($backup_file);

            // Log the backup
            $stmt = $db->prepare("
                INSERT INTO backup_logs (filename, created_by, created_at) 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([basename($zip_file), $_SESSION['user_id']]);

            // Clean old backups (keep only last 5)
            $files = glob($backup_dir . "/*.zip");
            if (count($files) > 5) {
                array_multisort(array_map('filemtime', $files), SORT_DESC, $files);
                $old_files = array_slice($files, 5);
                foreach ($old_files as $file) {
                    unlink($file);
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Backup created successfully',
                'filename' => basename($zip_file)
            ]);
        } else {
            throw new Exception("Failed to create ZIP archive");
        }
    } else {
        throw new Exception("Database backup failed");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 