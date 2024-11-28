<?php
// Add this to the top of your upload-document.php to debug
function checkUploadSettings() {
    $settings = [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time')
    ];
    
    // Check upload directory
    $upload_dir = '../uploads/documents';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $settings['upload_dir_writable'] = is_writable($upload_dir);
    $settings['upload_dir_path'] = realpath($upload_dir);
    
    return $settings;
}

// Use this to debug upload issues
if (isset($_GET['check'])) {
    header('Content-Type: application/json');
    echo json_encode(checkUploadSettings(), JSON_PRETTY_PRINT);
    exit;
} 