<?php
//* authenticate
include "../admin/auth/authenticate.php";

header('Content-Type: application/json');


$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

// Function to get the file extension
function getFileExtension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}
// Function to generate a random 3-character hash
function generateRandomHash($length = 4)
{
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

include '../connect.php';

$response = [
    'status' => 'error',
    'message' => 'Invalid request method'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryId = isset($_POST['categoryId']) ? $_POST['categoryId'] : null;
    $categoryName = isset($_POST['categoryName']) ? $_POST['categoryName'] : null;
    $categorySlug = isset($_POST['categorySlug']) ? $_POST['categorySlug'] : null;
    $categoryImage = isset($_POST['categoryImage']) ? $_POST['categoryImage'] : null;

    if (empty($categoryName) || empty($categorySlug)) {
        $response['message'] = "All fields are required.";
    } else {
        if ($categoryId) {
            // Güncelleme işlemi
            $sql = "UPDATE Categories SET name=:name,slug=:slug WHERE id =:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $categoryName);
            $stmt->bindParam(':slug', $categorySlug);
            $stmt->bindParam(':id', $categoryId);

            try {
                $db->beginTransaction();

                $stmt->execute();

                if ($stmt) {
                    if (!empty($_FILES['categoryImage']['name'])) {
                        $maxFileSize = 1 * 1024 * 1024;


                        $fileName = $_FILES['categoryImage']['name'];
                        $fileTmpName = $_FILES['categoryImage']['tmp_name'];
                        $fileType = $_FILES['categoryImage']['type'];
                        $fileError = $_FILES['categoryImage']['error'];
                        $fileSize = $_FILES['categoryImage']['size'];

                        // Check for file errors
                        if ($fileError !== UPLOAD_ERR_OK) {
                            throw new Exception('File upload error.');
                        }

                        // Validate file extension
                        $fileExtension = getFileExtension($fileName);
                        if (!in_array($fileExtension, $allowedExtensions)) {
                            throw new Exception('Invalid file extension.');
                        }

                        // Validate file MIME type
                        if (!in_array($fileType, $allowedMimeTypes)) {
                            throw new Exception('Invalid file type.');
                        }

                        if ($fileSize > $maxFileSize) {
                            throw new Exception('File size must be less than 1 MB.');
                        }

                        // Generate a unique file name with a random 3-character hash
                        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
                        $randomHash = generateRandomHash();
                        $uniqueFileName = $baseName . '-' . $randomHash . '.' . $fileExtension;
                        // upload direction is different
                        //! DOSYA IZINLERINI FULLEMEN GEREK
                        $targetFilePath = "../../uploads/categories/" . basename($uniqueFileName);

                        // Move the file to the upload directory
                        if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                            // Optionally, save file info to database
                            $sql = "UPDATE  Categories SET categoryImage=:image WHERE id=:id";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':id', $categoryId);
                            //coded file name
                            $codedFile = "/uploads/categories/" . $uniqueFileName;
                            $stmt->bindParam(':image', $codedFile);
                            $stmt->execute();
                        } else {
                            throw new Exception('Failed to move uploaded file.');
                        }
                    }

                    $db->commit();

                    $response = [
                        'status' => 'success',
                        'message' => 'Record updated successfully'
                    ];
                }
            } catch (\PDOException $e) {
                $response = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
                if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                    $response['message'] = 'Database violation error !';
                }
            }
        } else {
            // Ekleme işlemi
            $sql = "INSERT INTO Categories (name, slug) VALUES (:categoryName, :categoryslug)";
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':categoryName', $categoryName);
            $stmt->bindParam(':categoryslug', $categorySlug);

            try {
                $db->beginTransaction();

                $stmt->execute();
                if ($stmt) {
                    //* GETTING ID OF NEW CREATED CREATED
                    $sql = "SELECT * FROM Categories WHERE slug =:categoryslug";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':categoryslug', $categorySlug);
                    $stmt->execute();
                    $getCategory = $stmt->fetch(PDO::FETCH_ASSOC);


                    $realId = $getCategory['id'];

                    if (!empty($_FILES['categoryImage']['name'])) {
                        $maxFileSize = 1 * 1024 * 1024;

                        $fileName = $_FILES['categoryImage']['name'];
                        $fileTmpName = $_FILES['categoryImage']['tmp_name'];
                        $fileType = $_FILES['categoryImage']['type'];
                        $fileError = $_FILES['categoryImage']['error'];
                        $fileSize = $_FILES['categoryImage']['size'];

                        // Check for file errors
                        if ($fileError !== UPLOAD_ERR_OK) {
                            throw new Exception('File upload error.');
                        }

                        // Validate file extension
                        $fileExtension = getFileExtension($fileName);
                        if (!in_array($fileExtension, $allowedExtensions)) {
                            throw new Exception('Invalid file extension.');
                        }

                        // Validate file MIME type
                        if (!in_array($fileType, $allowedMimeTypes)) {
                            throw new Exception('Invalid file type.');
                        }

                        if ($fileSize > $maxFileSize) {
                            throw new Exception('File size must be less than 5 MB.');
                        }

                        // Generate a unique file name with a random 3-character hash
                        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
                        $randomHash = generateRandomHash();
                        $uniqueFileName = $baseName . '-' . $randomHash . '.' . $fileExtension;
                        // upload direction is different
                        //! DOSYA IZINLERINI FULLEMEN GEREK
                        $targetFilePath = "../../uploads/categories/" . basename($uniqueFileName);

                        // Move the file to the upload directory
                        if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                            // Optionally, save file info to database
                            $sql = "UPDATE  Categories SET categoryImage=:image WHERE id=:id";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':id', $realId);
                            //coded file name
                            $codedFile = "/uploads/categories/" . $uniqueFileName;
                            $stmt->bindParam(':image', $codedFile);
                            $stmt->execute();
                        } else {
                            throw new Exception('Failed to move uploaded file.');
                        }
                    }

                    $db->commit();

                    $response = [
                        'status' => 'success',
                        'message' => 'New record created successfully'
                    ];
                }
            } catch (\PDOException $e) {
                $db->rollBack();
                $response = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
    }

    echo json_encode($response);
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $categoryId = filter_var($data['id'], FILTER_SANITIZE_STRING) ?? null;

        if ($categoryId) {
            $checkQuery = "SELECT COUNT(*) FROM Categories WHERE id = :categoryId";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(':categoryId', $categoryId, PDO::PARAM_STR);
            $checkStmt->execute();
            $recordExists = $checkStmt->fetchColumn();

            if ($recordExists) {
                $sql = "DELETE FROM Categories WHERE id = :categoryId";
                $stmt = $db->prepare($sql);

                $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_STR);
                try {
                    $db->beginTransaction();

                    $stmt->execute();

                    $db->commit();

                    $response = [
                        'status' => 'success',
                        'message' => 'Record deleted successfully'
                    ];
                } catch (\PDOException $e) {
                    $response = [
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                    if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                        $response['message'] = 'First delete related things it can be products ...';
                    }
                }
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Record not found'
                ];
            }
        } else {
            $response['message'] = 'ID is required';
        }
    } else {
        $response['message'] = 'Invalid JSON: ' . json_last_error_msg();
    }

    echo json_encode($response);
    exit();
}
