<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate a logged-in user if needed
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Replace with a valid user ID
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Document Upload Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h2>Document Upload Test</h2>
    
    <div>
        <h3>Upload Marriage License</h3>
        <input type="file" id="marriage_license_file" accept=".pdf">
        <button onclick="uploadDocument('marriage_license')">Upload Marriage License</button>
    </div>
    
    <div>
        <h3>Upload CENOMAR</h3>
        <input type="file" id="cenomar_file" accept=".pdf">
        <button onclick="uploadDocument('cenomar')">Upload CENOMAR</button>
    </div>

    <div id="result"></div>

    <script>
    function uploadDocument(docType) {
        const fileInput = document.getElementById(docType + '_file');
        const file = fileInput.files[0];
        
        if (!file) {
            alert('Please select a file first');
            return;
        }

        const formData = new FormData();
        formData.append('document', file);
        formData.append('document_type', docType);

        $.ajax({
            url: 'api/upload-document.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#result').html('<pre>' + JSON.stringify(response, null, 2) + '</pre>');
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Document uploaded successfully'
                });
            },
            error: function(xhr) {
                $('#result').html('<pre>Error: ' + xhr.responseText + '</pre>');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to upload document'
                });
            }
        });
    }
    </script>
</body>
</html> 