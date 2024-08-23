<?php
header('Content-Type: application/json');

use Firebase\JWT\JWT;

require '../../vendor/autoload.php';

$response = [
    'status' => 'error',
    'message' => 'Something went wrong'
];

//* CREATE TOKEN
function createToken($userId)
{
    //* GETTING KEY
    $jsonFile = '../../settings.json';
    $jsonData = file_get_contents($jsonFile);
    $data = json_decode($jsonData, true);
    $key = isset($data['key']) ? $data['key'] : '';


    $issuedAt = time();
    $expiration = $issuedAt + 86400; // 1 day

    $payload = [
        'iss' => 'fanfables.com', // Token'ı veren
        'iat' => $issuedAt, // Oluşturulma zamanı
        'exp' => $expiration, // Son kullanma tarihi
        'sub' => $userId
    ];
    return JWT::encode($payload, $key, 'HS256');
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $username = isset($data['username']) ? trim(filter_var($data['username'], FILTER_SANITIZE_STRING)) : null;
        $password = isset($data['password']) ? trim(filter_var($data['password'], FILTER_SANITIZE_STRING)) : null;

        if (empty($username) || empty($password)) {
            $response['message'] = "Please fill all the fields";
        } else {
            $allowedCharacters = '/^[a-zA-Z0-9._+-@]+$/';
            if (preg_match('/\s/', $username)) {
                $response['message'] = "Username cannot contain spaces.";
            } elseif (!preg_match($allowedCharacters, $username)) {
                $response['message'] = "Username contains invalid characters.";
            } else {
                //? connect to the db
                include '../connect.php';
                if (strpos($username, '@') !== false && strpos($username, '.com') !== false) {
                    $checkUsrParam = "email";
                } else {
                    $checkUsrParam = "username";
                }

                try {
                    $query = "SELECT password FROM users where " . "$checkUsrParam" . "=:username";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }

                if ($result) {
                    $hashedPassword = $result['password'];


                    if (password_verify($password, $hashedPassword)) {
                        //composer require firebase/php-jwt
                        $token = createToken($username);

                        //* CREATE TOKEN
                        $cookieName = 'auth_token';
                        $cookieValue = $token;
                        $cookieExpire = time() + 86400; // 1 gün
                        $cookiePath = '/';

                        // * CANLIDA domain techarsiv.com // ve secure true
                        //? domain koyunca hata aldı
                        setcookie($cookieName, $cookieValue, [
                            'expires' => $cookieExpire,
                            'path' => $cookiePath,
                            'domain' => '',
                            'secure' => false, // HTTPS kullanıyorsanız true yapın
                            'httponly' => true, // JavaScript üzerinden erişilemez
                            'samesite' => 'Lax'
                        ]);

                        $response['status'] = "success";
                        $response['message'] = 'Login successful';
                    } else {
                        $response['message'] = 'Invalid credentials';
                    }
                } else {
                    $response['message'] = 'Invalid credentials';
                }
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
?>