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
?>