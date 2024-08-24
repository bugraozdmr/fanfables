<?php
//* authenticate
include "../admin/auth/authenticate.php";

header('Content-Type: application/json');


$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

// Function to get the file extension
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}
// Function to generate a random 3-character hash
function generateRandomHash($length = 4) {
    return substr(bin2hex(random_bytes($length)), 0, $length);
}

function generateSlug($string) {
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
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $card_desc = isset($_POST['card_desc']) ? $_POST['card_desc'] : '';
        $typeid = isset($_POST['typeid']) ? $_POST['typeid'] : '';
        $categories = isset($_POST['categories']) ? $_POST['categories'] : '';

        if (empty($name) || empty($director) || empty($studio) || empty($duration) || empty($show_date) || empty($description) || empty($card_desc) || empty($typeid) || empty($categories)) {
            throw new Exception('Some fields seems like empty.');
        }
        if (empty($id)) {
            // Insert new product
            //? generate slug
            $slug = generateSlug($name);

            $sql = "INSERT INTO Products (name, director, description,card_desc, studio,slug,duration,date_aired,status,imdb,episode_count,typeId) VALUES (:name, :director, :description,:card_desc, :studio,:slug, :duration,:show_date,:status,:imdb,:episode_count,:typeId)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':director', $director);
            $stmt->bindParam(':studio', $studio);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':show_date', $show_date);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':imdb', $imdb);
            $stmt->bindParam(':episode_count', $epCount);
            $stmt->bindParam(':card_desc', $card_desc);
            $stmt->bindParam(':typeId', $typeid,PDO :: PARAM_INT);
            

        } else {
            // Update existing product

            $sql = "SELECT name,slug FROM Products WHERE id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $res = $stmt->fetch();

            if($name != $res['name']){
                $slug = generateSlug($name);
            }
            else{
                $slug = $res['slug'];
            }

            //* Be carefull -- If any error happens in query BOOM
            $sql = "UPDATE Products SET name = :name,brand=:brand,slug=:slug,alt_desc=:alt_desc, product_code = :code, description = :description, price = :price,categoryId=:categoryId,subCategoryId=:subCategoryId WHERE id =:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':brand', $brand);
            $stmt->bindParam(':alt_desc', $alt_desc);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':categoryId', $categoryId);
            $stmt->bindParam(':subCategoryId', $subCategoryId,PDO::PARAM_INT);
        }

        //! resimlere bagli olmaktan kurtardim :D if'den cikardim endi
        $db->beginTransaction();

        // Execute the query
        $stmt->execute();

        //* GETTING ID
        if(empty($id)){
            $sql = "SELECT id FROM Products WHERE slug=:slug";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':slug', $slug);
            $stmt->execute();
            $realId = $stmt->fetchColumn();
        }

        //? INSERTING || UPDATING COLORS SIZES
        //* INSERT OR UPDATE DOESNT MATTER I need iD
        $requireID;
        if(empty($id)){
            $requireID = $realId;
        }
        else{
            $requireID = $id;
        }
        //! TURNED STRING INTO ARRAY
        $colors = explode(',', $colors);
        $sizes = explode(',', $sizes);

        $colorCount = count($colors);
        $sizeCount = count($sizes);
        //UPDATE
        if (isset($colors) && !empty($colors)) {
            $sql = "DELETE FROM ProductColors WHERE productId =:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $requireID);
            $stmt->execute();

            for ($i = 0; $i < $colorCount; $i++) {
                $sql = "INSERT INTO ProductColors (productId, colorId) VALUES (:productId, :colorId)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':productId', $requireID);
                $stmt->bindParam(':colorId', $colors[$i]);
                $stmt->execute();
            }
        }
        if (isset($sizes) && !empty($sizes)) {
            $sql = "DELETE FROM ProductSizes WHERE productId =:id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $requireID);
            $stmt->execute();

            for ($i = 0; $i < $sizeCount; $i++) {
                $sql = "INSERT INTO ProductSizes (productId, sizeId) VALUES (:productId, :sizeId)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':productId', $requireID);
                $stmt->bindParam(':sizeId', $sizes[$i]);
                $stmt->execute();
            }
        }
        //? INSERTING || UPDATING COLORS SIZES END

        // Handle file uploads
        if (!empty($_FILES['images']['name'][0])) {
            $fileCount = count($_FILES['images']['name']);
            $maxFileSize = 3 * 1024 * 1024; // 3 MB in bytes

            if(!empty($id)){
                //? eger iceri girerse updatede
                $sql = "DELETE FROM ProductImages WHERE product_id =:id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':id', $id,PDO::PARAM_STR);
                $stmt->execute();
            }

            for ($i = 0; $i < $fileCount; $i++) {
                $fileName = $_FILES['images']['name'][$i];
                $fileTmpName = $_FILES['images']['tmp_name'][$i];
                $fileType = $_FILES['images']['type'][$i];
                $fileError = $_FILES['images']['error'][$i];
                $fileSize = $_FILES['images']['size'][$i];


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
                $targetFilePath = "../../uploads/products/" . basename($uniqueFileName);

                // Move the file to the upload directory
                if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                    // Optionally, save file info to database
                    $sql = "INSERT INTO ProductImages (product_id, image_url) VALUES (:product_id, :image_url)";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':product_id', $id);
                    //coded file name
                    $codedFile ="/uploads/products/".$uniqueFileName;
                    $stmt->bindParam(':image_url', $codedFile);
                    $stmt->execute();
                } else {
                    throw new Exception('Failed to move uploaded file.');
                }
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
        }
        elseif (strpos($e->getMessage(), 'Syntax error') !== false) {
            // bu varsa kesin sorgu hatalıdır onu izle
            $response['message'] = 'Syntax error !';
        }
        elseif(strpos($e->getMessage(), 'datetime format') !== false){
            $response['message'] = 'Category Id wrong !';
        }
        elseif(strpos($e->getMessage(), 'a foreign key constraint fails') !== false){
            $response['message'] = 'Wrong id usage check again !';
        }
        
        else{
            $response['message'] = $e->getMessage().$id;
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
        $productId = filter_var($data['id'], FILTER_SANITIZE_STRING) ?? null;
        $productId = trim($productId);


        if ($productId) {
            $checkQuery = "SELECT COUNT(*) FROM Products WHERE id =:productId";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(':productId', $productId, PDO::PARAM_STR);
            $checkStmt->execute();
            $recordExists = $checkStmt->fetchColumn();

            if ($recordExists) {
                $db->beginTransaction();

                //* deleting product images
                $sql1 = "DELETE FROM ProductImages WHERE product_id =:productId";
                $stmt1 = $db->prepare($sql1);
                $stmt1->bindParam(':productId', $productId, PDO::PARAM_STR);

                //* deleting product sizes
                $sql2 = "DELETE FROM ProductSizes WHERE productId =:productId";
                $stmt2 = $db->prepare($sql2);
                $stmt2->bindParam(':productId', $productId, PDO::PARAM_STR);

                //* deleting product colors
                $sql3 = "DELETE FROM ProductColors WHERE productId =:productId";
                $stmt3 = $db->prepare($sql3);
                $stmt3->bindParam(':productId', $productId, PDO::PARAM_STR);

                $sql = "DELETE FROM Products WHERE id = :productId";
                $stmt = $db->prepare($sql);

                $stmt->bindParam(':productId', $productId, PDO::PARAM_STR);

                try {
                    //! SIRA ONEMLI
                    $stmt1->execute();
                    $stmt2->execute();
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
            }
            else{
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

?>
