<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require __DIR__.'/../../vendor/autoload.php';


header('Content-Type: application/json');

include '../connect.php';



$response = [
    'status' => 'error',
    'message' => 'Invalid request method'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // FIRST CONTROL TOKEN EXIST OR NOT
    $jsonFile = __DIR__ . '/../../settings.json';
    $jsonData = file_get_contents($jsonFile);
    $data = json_decode($jsonData, true);
    $key = isset($data['key']) ? $data['key'] : '';

    
    if (isset($_COOKIE['auth_token'])) {
        try {
            //* TOKEN BOS GELEBILIR
            $token = $_COOKIE['auth_token'];
            if(!empty($token)){
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                $username = $decoded->sub;
        
                $query = "SELECT id,username FROM users WHERE username=:username";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $result = $stmt->fetch();
        
                $username1 = $result['username'];
            }
        } catch (\Exception $e) {
            $response['message'] = "Naughty thing <3";
        }
    }
    else{
        $response['message'] = "Something strangly went wrong <3";
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // JSON verisini ayrıştırın
    if (json_last_error() === JSON_ERROR_NONE) {
        $username = isset($data['username']) ? filter_var($data['username'], FILTER_SANITIZE_STRING) : null;
        $showSlug = isset($data['showSlug']) ? filter_var($data['showSlug'], FILTER_SANITIZE_STRING) : null;
        $comment = isset($data['comment']) ? filter_var($data['comment'], FILTER_SANITIZE_STRING) : null;

        if(empty($showSlug) || empty($comment) || empty($username)){
            $response['message'] = 'Please fill in all fields';
        }
        else if(strlen($comment) > 600){
            $response['message'] = 'Comment can only contains at most 600 chars';
        }
        else if(!isset($token) || empty($token) ){
            $response['message'] = 'Got you homie *_<';
        }
        else if(empty($username1)){
            $response['message'] = 'Something went wrong *_<';
        }
        else if($username != $username1){
            $response['message'] = 'You are not the owner of this comment *_<';
        }
        else{
            $query = "SELECT ub.until as until
            FROM UserBans ub
            JOIN users u ON u.id=ub.userId
            WHERE u.username=:username
            ORDER BY until DESC
            LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $uu = $stmt->fetch();

            $istanbulTimeZone = new DateTimeZone('Europe/Istanbul');
            $now = new DateTime('now', $istanbulTimeZone);
            if (isset($uu['until']) && !empty($uu['until'])) {
                $until = new DateTime($uu['until'], $istanbulTimeZone);
                $interval = $now->diff($until);
                if ($until > $now) {
                    $days = $interval->days;
                    $hours = $interval->h;
                    $minutes = $interval->i;

                    $response['message'] = "User banned . Remains : ".($days > 0 ? $days . " days " : "").($hours > 0 ? $hours . " hours " : "").($minutes > 0 ? $minutes . " minutes" : "");

                    $check_exist = 0;
                } else {
                    $check_exist = 1;
                }
            } else {
                $check_exist = 1;
            }

            if ($check_exist == 1) {
                //* get user id
                $sql = "SELECT id FROM users WHERE username=:username";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $result = $stmt->fetch();
                $userId = $result['id'];

                if(empty($result['id'])){
                    throw new Exception("Something is wrong !");
                }

                //* get show id
                $sql = "SELECT id FROM Shows WHERE slug=:slug";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':slug', $showSlug);
                $stmt->execute();
                $result = $stmt->fetch();
                $showId = $result['id'];

                if(empty($result['id'])){
                    throw new Exception("Something is wrong !");
                }

                $sql = "INSERT INTO Comments (userId, showId,comment) VALUES (:userId, :showId,:comment)";
                $stmt = $db->prepare($sql);

                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':showId', $showId);
                $stmt->bindParam(':comment', $comment);

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

?>
