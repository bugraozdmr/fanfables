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
        $scategoryId = isset($data['id']) ? filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT) : null;
        $scategoryName = isset($data['name']) ? filter_var($data['name'], FILTER_SANITIZE_STRING) : null;
        
        if ($scategoryId) {
            // Güncelleme işlemi
            if ($scategoryName) {
                $sql = "UPDATE Types SET ";
                $params = [];

                if ($scategoryName) {
                    $sql .= "name = :scategoryName";
                    $params[':scategoryName'] = $scategoryName;
                }

                $sql .= " WHERE id = :scategoryId";
                $params[':scategoryId'] = $scategoryId;

                $stmt = $db->prepare($sql);

                try {
                    $stmt->execute($params);
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
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'At least one field (name or hex) is required for update'
                ];
            }
        } else {
            // Ekleme işlemi
            if ($scategoryName) {
                $sql = "INSERT INTO Types (name) VALUES (:scategoryName)";
                $stmt = $db->prepare($sql);

                $stmt->bindParam(':scategoryName', $scategoryName);

                try {
                    $stmt->execute();
                    $response = [
                        'status' => 'success',
                        'message' => 'New record created successfully'
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
                    'message' => 'Name and HEX are required for insertion'
                ];
            }
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
        $scategoryId = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT) ?? null;

        if ($scategoryId) {
            $checkQuery = "SELECT COUNT(*) FROM Types WHERE id = :scategoryId";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(':scategoryId', $scategoryId, PDO::PARAM_INT);
            $checkStmt->execute();
            $recordExists = $checkStmt->fetchColumn();

            if ($recordExists) {
                $sql = "DELETE FROM Types WHERE id = :scategoryId";
                $stmt = $db->prepare($sql);

                $stmt->bindParam(':scategoryId', $scategoryId, PDO::PARAM_INT);

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
