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
        $roles = isset($data['roles']) ? $data['roles'] : null;
        
        if (!empty($roles) && !empty($userId)) {
            try {
                $db->beginTransaction();
                // Güncelleme işlemi

                //? FIRST DELETE
                $sql = "DELETE FROM UserRoles WHERE userId=:usrId";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':usrId', $userId);

                $stmt->execute();

                //? THEN UPDATE
                foreach($roles as $role){
                    $sql = "SELECT id FROM roles WHERE normalized_name=:nname";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':nname', $role);
                    $stmt->execute();
                    $resse = $stmt->fetchColumn();

                    $sql = "INSERT INTO UserRoles (userId,roleId) VALUES (:userId,:roleId)";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':userId', $userId);
                    $stmt->bindParam(':roleId', $resse);
                    $stmt->execute();
                }

                $db->commit();
                $response = [
                    'status' => 'success',
                    'message' => 'Record updated successfully'
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
                'message' => 'All fields are required'
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


if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $userId = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT) ?? null;

        if ($userId) {
            $checkQuery = "SELECT COUNT(*) FROM Types WHERE id = :userId";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $checkStmt->execute();
            $recordExists = $checkStmt->fetchColumn();

            if ($recordExists) {
                $sql = "DELETE FROM Types WHERE id = :userId";
                $stmt = $db->prepare($sql);

                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

                try {
                    $stmt->execute();
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
