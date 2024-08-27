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
        
        if (!empty($userId)) {
            // UPDATE
            try {
                $db->beginTransaction();

                $sql = "DELETE FROM UserBans WHERE userId=:userId";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':userId', $userId);
                $stmt->execute();

                $db->commit();
                $response = [
                    'status' => 'success',
                    'message' => 'Record created successfully'
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
