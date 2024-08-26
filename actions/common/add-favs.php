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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // JSON verisini ayrıştırın
    if (json_last_error() === JSON_ERROR_NONE) {
        $slug = isset($data['slug']) ? filter_var($data['slug'], FILTER_SANITIZE_STRING) : null;

        if (empty($slug)) {
            $response['message'] = 'Please fill all fields';
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
                        $username = $decoded->sub;

                        $query = "SELECT id FROM users WHERE username=:username";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':username', $username);
                        $stmt->execute();
                        $result = $stmt->fetch();

                        //* USERID -- TOKEN ID KARSILASTIR
                        $userrId = $result['id'];
                    }
                } catch (\Exception $e) {
                    $response['message'] = "Naughty thing <3";
                }
            } else {
                $response['message'] = "Something strangly went wrong <3";
            }

            // checks
            if (!isset($userrId) || empty($userrId)) {
                $response['message'] = "Something Went Wrong !";
            } else {
                try {
                    //* GET Show ID
                    $query = "SELECT id FROM Shows WHERE slug=:slug";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':slug', $slug);
                    $stmt->execute();
                    $ssshowId = $stmt->fetch();

                    //* CHECK RECORD ALREADY EXIST
                    $query = "SELECT COUNT(*) as count FROM WatchLater WHERE userId=:userId AND showId=:showId";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':userId', $userrId);
                    $stmt->bindParam(':showId', $ssshowId['id']);
                    $stmt->execute();
                    $rslt = $stmt->fetch();

                    if ($rslt && $rslt['count'] != 0) {
                        //* DELETE
                        $query = "DELETE FROM WatchLater WHERE userId=:userId AND showId=:showId";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':userId', $userrId);
                        $stmt->bindParam(':showId', $ssshowId['id']);
                        $stmt->execute();
                        $response['message'] = "Show Removed From Watch Later";
                    } else {
                        //* INSERT
                        $query = "INSERT INTO WatchLater (userId, showId) VALUES (:userId, :showId)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':userId', $userrId);
                        $stmt->bindParam(':showId', $ssshowId['id']);
                        $stmt->execute();
                        $response['message'] = "Show Added To Watch Later";
                    }

                    $response['status'] = "success";
                } catch (\Exception $e) {
                    //"Something Went Wrong !";
                    $response['message'] = $e->getMessage();
                }
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
