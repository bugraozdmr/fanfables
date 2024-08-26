<?php
include "../admin/auth/authenticate.php";

header('Content-Type: application/json');

include '../connect.php';


if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $id = filter_var($data['id'], FILTER_SANITIZE_STRING) ?? null;

        if ($id) {
            $checkQuery = "SELECT COUNT(*) FROM Comments WHERE id = :reviewId";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(':reviewId', $id);
            $checkStmt->execute();
            $recordExists = $checkStmt->fetchColumn();

            if ($recordExists) {
                $sql = "DELETE FROM Comments WHERE id = :reviewId";
                $stmt = $db->prepare($sql);

                $stmt->bindParam(':reviewId', $id);

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