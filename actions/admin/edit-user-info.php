<?php
//* authenticate
include "../admin/auth/authenticate.php";

header('Content-Type: application/json');

include '../connect.php';

$response = [
    'status' => 'error',
    'message' => 'Invalid request method'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // JSON verisini ayrıştırın
    if (json_last_error() === JSON_ERROR_NONE) {
        $userId = isset($data['id']) ? filter_var($data['id'], FILTER_SANITIZE_STRING) : null;
        $username = isset($data['username']) ? filter_var($data['username'], FILTER_SANITIZE_STRING) : null;
        $action = isset($data['action']) ? filter_var($data['action'], FILTER_SANITIZE_STRING) : null;
        $name = isset($data['name']) ? filter_var($data['name'], FILTER_SANITIZE_STRING) : null;
        $description = isset($data['description']) ? filter_var($data['description'], FILTER_SANITIZE_STRING) : null;

        
        if (!empty($action)) {
            // Güncelleme işlemi
            try {
                $db->beginTransaction();

                if($action == 'remove-banner'){
                    $sql = "UPDATE users SET bannerImage=:bid WHERE id=:usrId";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(':bid', null, PDO::PARAM_NULL);
                    $stmt->bindParam(':usrId', $userId);
                }
                else if ($action == "remove-image"){
                    $sql = "UPDATE users SET image=:img WHERE id=:usrId";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(':img', null, PDO::PARAM_NULL);
                    $stmt->bindParam(':usrId', $userId);
                }
                else if($action == "submit"){
                    $sql = "SELECT id FROM users WHERE username=:username";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':username', $username);
                    $stmt->execute();
                    $result = $stmt->fetch();

                    if(empty($result['id']) || $userId == $result['id']){
                        if(!empty($username)){
                            $sql = "UPDATE users SET username=:username,name=:name,description=:description WHERE id=:usrId";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':username', $username);
                            $stmt->bindParam(':description', $description);
                            $stmt->bindParam(':name', $name);
                        
                            $stmt->bindParam(':usrId', $userId);
                        }
                        else{
                            $flag = 2;

                            $response = [
                                'status' => 'error',
                                'message' => 'WTF !'
                            ];
                        }
                    }
                    else{
                        $flag = 1;
                        $response = [
                            'status' => 'error',
                            'message' => 'This username already exists'
                        ];
                    }
                }
                else {
                    $response = [
                        'status' => 'error',
                        'message' => 'Something went wrong'
                    ];
                }

                if(!isset($flag)){
                    $stmt->execute();

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
            }
        } 
        else{
            $response = [
                'status' => 'error',
                'message' => 'You can\'t do that sir !'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid JSON: ' . json_last_error_msg()
        ];
    }

    echo json_encode($response);
    exit();
}


?>