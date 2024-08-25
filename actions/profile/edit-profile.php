<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require __DIR__ . '/../../vendor/autoload.php';

header('Content-Type: application/json');

include '../connect.php';

$response = [
    'status' => 'error',
    'message' => 'Invalid request method'
];

$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

function getFileExtension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}
function generateRandomHash($length = 4)
{
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? filter_var($_POST['username'], FILTER_SANITIZE_STRING) : null;
    $description = isset($_POST['description']) ? filter_var($_POST['description'], FILTER_SANITIZE_STRING) : null;
    $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_STRING) : null;
    $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_SANITIZE_STRING) : null;

    if (empty($username) || empty($id)) {
        $response['message'] = 'Something went wrong ! Check username maybe';
    }
    else if(strlen($description) > 400){
        $response['message'] = 'Up to 400 chars';
    } else {
        $jsonFile = __DIR__ . '/../../settings.json';
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);
        $key = isset($data['key']) ? $data['key'] : '';

        if (isset($_COOKIE['auth_token'])) {
            try {
                //* TOKEN BOS GELEBILIR
                $token = $_COOKIE['auth_token'];
                if (!empty($token)) {
                    $decoded = JWT::decode($token, new Key($key, 'HS256'));
                    $username_d = $decoded->sub;

                    $query = "SELECT id,image,bannerImage FROM users WHERE username=:username";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':username', $username_d);
                    $stmt->execute();
                    $result = $stmt->fetch();

                    //* USERID -- TOKEN ID KARSILASTIR
                    $userrId = $result['id'];
                    $img = $result['image'];
                    $bimg = $result['bannerImage'];
                }
            } catch (\Exception $e) {
                $response['message'] = "Naughty thing <3";
            }
        } else {
            $response['message'] = "Something strangly went wrong <3";
        }

        // checks
        if (!isset($userrId) || $userrId != $id) {
            $response['message'] = "What is going on here !";
        } else if (isset($userrId) && !empty($userrId)) {
            $sql = "SELECT username,id FROM users WHERE username=:username";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $result = $stmt->fetch();

            if(isset($result['username']) && !empty($result['username']) && ($result['id'] != $id)){
                $response['message'] = "Username already exists !";
            }
            else{
                try {
                    $db->beginTransaction();

                    $sql = "UPDATE users SET name=:name,username=:username,image=:image,bannerImage=:bannerImg,description=:description WHERE id=:id";
                    $stmt = $db->prepare($sql);
        
                    $stmt->bindParam(':id', $userrId);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':description', $description);

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
                        $targetFilePath = "../../uploads/users/" . basename($uniqueFileName);

                        // Move the file to the upload directory
                        if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                            //coded file name
                            $codedFile = "/uploads/users/" . $uniqueFileName;
                            $stmt->bindParam(':image', $codedFile);

                        } else {
                            throw new Exception('Failed to move uploaded file.');
                        }
                    }
                    else{
                        $stmt->bindParam(':image', $img);
                    }

                    if (!empty($_FILES['bannerImage']['name'])) {
                        $maxFileSize = 1 * 1024 * 1024;


                        $fileName = $_FILES['bannerImage']['name'];
                        $fileTmpName = $_FILES['bannerImage']['tmp_name'];
                        $fileType = $_FILES['bannerImage']['type'];
                        $fileError = $_FILES['bannerImage']['error'];
                        $fileSize = $_FILES['bannerImage']['size'];

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
                        $targetFilePath = "../../uploads/userBannerImages/" . basename($uniqueFileName);

                        // Move the file to the upload directory
                        if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                            //coded file name
                            $codedFile = "/uploads/userBannerImages/" . $uniqueFileName;
                            $stmt->bindParam(':bannerImg', $codedFile);

                            $stmt->execute();
                        } else {
                            throw new Exception('Failed to move uploaded file.');
                        }
                    }
                    else{
                        $stmt->bindParam(':image', $bimg);
                        $stmt->execute();
                    }



                    //! execute altta ona gore
                    $db->commit();
                    $response = [
                        'status' => 'success',
                        'message' => 'User info updated'
                    ];
                } catch (\PDOException $e) {
                    $response = [
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }
        } else {
            $response['message'] = "Something went wrong sir *_*";
        }
    }
}

// TODO HATA SIKILDIMAMK

echo json_encode($response);
exit();
?>