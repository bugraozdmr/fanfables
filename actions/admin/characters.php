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


// CONNECT TO DATABASE
include '../connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = [
        'status' => 'error',
        'message' => 'Something went wrong.'
    ];

    try {

        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $starring = isset($_POST['starring']) ? $_POST['starring'] : '';
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $showId = isset($_POST['showId']) ? $_POST['showId'] : '';

        $flag_iu = 1;
        if (empty($name) || empty($starring) || empty($description) || empty($showId)) {
            throw new Exception('All fields are required.');
        }
        if (empty($id)) {
            //* INSERT
            $flag_iu = 0;

            $sql = "INSERT INTO Characters (name, starring, description,showId,image) VALUES (:name, :starring, :description,:showId,:image)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':starring', $starring);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':showId', $showId);
        } else {
            // Update existing product

            $query = "SELECT image FROM Characters WHERE id=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch();

            
            //* Be carefull -- If any error happens in query BOOM
            $sql = "UPDATE Characters SET name = :name,starring=:starring,description=:description,showId=:showId,image=:image WHERE id =:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':starring', $starring);
            $stmt->bindParam(':showId', $showId);
        }

        //! resimlere bagli olmaktan kurtardim :D if'den cikardim endi
        $db->beginTransaction();


        // Handle file uploads
        if (!empty($_FILES['image']['name'])) {
            $maxFileSize = 1 * 1024 * 1024;

            $fileName = $_FILES['image']['name'];
            $fileTmpName = $_FILES['image']['tmp_name'];
            $fileType = $_FILES['image']['type'];
            $fileError = $_FILES['image']['error'];
            $fileSize = $_FILES['image']['size'];


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
            $targetFilePath = "../../uploads/characters/" . basename($uniqueFileName);

            // Move the file to the upload directory
            if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                $flag_iu = 0;
                //coded file name
                $codedFile = "/uploads/characters/" . $uniqueFileName;
                $stmt->bindParam(':image', $codedFile);
                $stmt->execute();

                // eger resim yuklenmisse flag yine degissin
            } else {
                throw new Exception('Failed to move uploaded file.');
            }
        }

        // eger update'ti ve dosya yuklenmedi ise
        if($flag_iu == 1){
            $stmt->bindParam(':image', $row['image']);
            $stmt->execute();
        }

        //! resimlere bagli olmaktan kurtardim :D
        $db->commit();


        // Update response on success
        $response['status'] = 'success';
        $response['message'] = 'Record processed successfully';
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            $response['message'] = 'Product Code has to be unique';
        } elseif (strpos($e->getMessage(), 'Syntax error') !== false) {
            // bu varsa kesin sorgu hatalıdır onu izle
            $response['message'] = 'Syntax error !';
        } elseif (strpos($e->getMessage(), 'datetime format') !== false) {
            $response['message'] = 'Category Id wrong !';
        } elseif (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
            $response['message'] = 'Wrong id usage check again !';
        } else {
            $response['message'] = $e->getMessage() . $id;
        }
    }


    // Return JSON response
    echo json_encode($response);
}

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        //! STRING id
        $showId = filter_var($data['id'], FILTER_SANITIZE_STRING) ?? null;
        $showId = trim($showId);


        if ($showId) {
            $checkQuery = "SELECT COUNT(*) FROM Shows WHERE id =:showId";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(':showId', $showId, PDO::PARAM_STR);
            $checkStmt->execute();
            $recordExists = $checkStmt->fetchColumn();

            if ($recordExists) {
                $db->beginTransaction();

                //* deleting product categories
                $sql3 = "DELETE FROM ShowCategories WHERE showId =:showId";
                $stmt3 = $db->prepare($sql3);
                $stmt3->bindParam(':showId', $showId, PDO::PARAM_STR);

                $sql = "DELETE FROM Shows WHERE id = :showId";
                $stmt = $db->prepare($sql);

                $stmt->bindParam(':showId', $showId, PDO::PARAM_STR);

                try {
                    //! SIRA ONEMLI
                    $stmt3->execute();
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
