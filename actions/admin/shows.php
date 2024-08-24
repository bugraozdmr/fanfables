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

function generateSlug($string)
{
    // Convert to lowercase
    $slug = strtolower($string);

    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);

    // Remove multiple hyphens
    $slug = preg_replace('/-+/', '-', $slug);

    // Trim hyphens from start and end
    $slug = trim($slug, '-');

    // Generate a 2-character random hash
    $hash = substr(bin2hex(random_bytes(1)), 0, 4);

    // Append the hash to the slug
    $slug .= '-' . $hash;

    return $slug;
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
        $director = isset($_POST['director']) ? $_POST['director'] : '';
        $studio = isset($_POST['studio']) ? $_POST['studio'] : '';
        $duration = isset($_POST['duration']) ? $_POST['duration'] : '';
        $epCount = isset($_POST['epCount']) ? $_POST['epCount'] : '';
        $show_date = isset($_POST['show_date']) ? $_POST['show_date'] : '';
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $imdb = isset($_POST['imdb']) ? $_POST['imdb'] : '';
        $watchLink = isset($_POST['link']) ? $_POST['link'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $card_desc = isset($_POST['card_desc']) ? $_POST['card_desc'] : '';
        $typeid = isset($_POST['typeid']) ? $_POST['typeid'] : '';
        $categories = isset($_POST['categories']) ? $_POST['categories'] : '';

        if (empty($name) || empty($director) || empty($duration) || empty($show_date) || empty($description) || empty($card_desc) || empty($typeid) || empty($categories)) {
            throw new Exception('Some fields seems like empty.');
        }
        if (empty($id)) {
            // Insert new product
            //? generate slug
            $slug = generateSlug($name);

            $sql = "INSERT INTO Shows (name, director, description,card_desc, studio,watchLink,slug,duration,date_aired,status,imdb,episode_count,typeId) VALUES (:name, :director, :description,:card_desc, :studio,:watchLink,:slug, :duration,:show_date,:status,:imdb,:episode_count,:typeId)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':director', $director);
            $stmt->bindParam(':studio', $studio);
            $stmt->bindParam(':watchLink', $watchLink);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':show_date', $show_date);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':imdb', $imdb);
            $stmt->bindParam(':episode_count', $epCount);
            $stmt->bindParam(':card_desc', $card_desc);
            $stmt->bindParam(':typeId', $typeid, PDO::PARAM_INT);
        } else {
            // Update existing product

            $sql = "SELECT name,slug FROM Shows WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $res = $stmt->fetch();

            if ($name != $res['name']) {
                $slug = generateSlug($name);
            } else {
                $slug = $res['slug'];
            }

            //* Be carefull -- If any error happens in query BOOM
            $sql = "UPDATE Shows SET name = :name,director=:director,watchLink=:watchLink,slug=:slug,card_desc=:card_desc, description = :description, duration = :duration, episode_count = :episode_count,typeId=:typeId,imdb=:imdb,status=:status,date_aired=:show_date,studio=:studio WHERE id =:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':director', $director);
            $stmt->bindParam(':watchLink', $watchLink);
            $stmt->bindParam(':studio', $studio);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':show_date', $show_date);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':imdb', $imdb);
            $stmt->bindParam(':episode_count', $epCount);
            $stmt->bindParam(':card_desc', $card_desc);
            $stmt->bindParam(':typeId', $typeid, PDO::PARAM_INT);
        }

        //! resimlere bagli olmaktan kurtardim :D if'den cikardim endi
        $db->beginTransaction();

        // Execute the query
        $stmt->execute();

        //* GETTING ID
        if (empty($id)) {
            $sql = "SELECT id FROM Shows WHERE slug=:slug";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':slug', $slug);
            $stmt->execute();
            $realId = $stmt->fetchColumn();
        }

        //? INSERTING || UPDATING COLORS SIZES
        //* INSERT OR UPDATE DOESNT MATTER I need iD
        $requireID;
        if (empty($id)) {
            $requireID = $realId;
        } else {
            $requireID = $id;
        }
        //! TURNED STRING INTO ARRAY
        $categories = explode(',', $categories);

        $categoryCount = count($categories);
        //UPDATE
        if (isset($categories) && !empty($categories)) {
            $sql = "DELETE FROM ShowCategories WHERE showId =:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $requireID);
            $stmt->execute();

            for ($i = 0; $i < $categoryCount; $i++) {
                $sql = "INSERT INTO ShowCategories (showId, categoryId) VALUES (:showId, :categoryId)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':showId', $requireID);
                $stmt->bindParam(':categoryId', $categories[$i]);
                $stmt->execute();
            }
        }
        //? INSERTING || UPDATING COLORS SIZES END

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
            $targetFilePath = "../../uploads/shows/" . basename($uniqueFileName);

            // Move the file to the upload directory
            if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                // Optionally, save file info to database
                $sql = "UPDATE Shows SET image=:image_url WHERE id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $requireID);
                //coded file name
                $codedFile = "/uploads/shows/" . $uniqueFileName;
                $stmt->bindParam(':image_url', $codedFile);
                $stmt->execute();
            } else {
                throw new Exception('Failed to move uploaded file.');
            }
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
