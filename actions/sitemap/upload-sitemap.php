<?php
//* Authenticate
include "../admin/auth/authenticate.php";

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Invalid request method'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['sitemap']) && $_FILES['sitemap']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../sitemap/';
        $uploadedFile = $_FILES['sitemap'];
        $fileName = basename($uploadedFile['name']);
        $uploadFile = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $files = glob($uploadDir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        if (move_uploaded_file($uploadedFile['tmp_name'], $uploadFile)) {
            $response = [
                'status' => 'success',
                'message' => 'File uploaded successfully'
            ];
        } else {
            $response['message'] = 'Failed to move uploaded file';
        }
    } else {
        $response['message'] = 'No file uploaded or upload error';
    }
}

echo json_encode($response);
exit();
?>
