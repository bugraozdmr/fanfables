<?php
header('Content-Type: application/json');
include '../connect.php';


$response = [
    'status' => 'error',
    'message' => 'Something went wrong'
];

/*
hata varsa düşünmeden göm
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $username = isset($data['username']) ? trim(htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8')) : null;
        $email = isset($data['email']) ? trim(htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8')) : null;
        $password = isset($data['password']) ? trim(htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8')) : null;



        if (empty($username) || empty($password) || empty($email)) {
            $response['message'] = "Please fill all the fields";
        } else {
            //* CONTROL USERNAME
            $turkishCharacters = '/[çÇğĞıİöÖşŞüÜ]/u';
            $specialCharacters = '/[^a-zA-Z0-9]/';

            if (preg_match($turkishCharacters, $username) || preg_match('/\s/', $username) || preg_match($specialCharacters, $username)) {
                $response['message'] = "Username cannot contain Turkish characters, spaces, or special characters.";
            }elseif (strlen($username) < 2) {
                $response['message'] = "Username must be at least 2 characters long.";
            } 
            elseif (strlen($password) < 6) {
                $response['message'] = "Password must be at least 6 characters long.";
            }
            else{
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $verify_token = hash('sha256', uniqid(mt_rand(), true));
    
                // Check if email or username already exists
                $check_email = "SELECT email FROM users WHERE email=:email LIMIT 1";
                $stmt = $db->prepare($check_email);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
    
                $check_username = "SELECT username FROM users WHERE username=:username LIMIT 1";
                $stmt1 = $db->prepare($check_username);
                $stmt1->bindParam(':username', $username);
                $stmt1->execute();
    
                if ($stmt->rowCount() > 0 || $stmt1->rowCount() > 0) {
                    if ($stmt->rowCount() > 0) {
                        $response['message'] = "Email already exists";
                    }
                    else if ($stmt1->rowCount() > 0) {
                        $response['message'] = "Username already exists";
                    }
                } else {
                    try {
                        $db->beginTransaction();
    
                        // Create User
                        $query = "INSERT INTO users (username, email,  password) VALUES (:username, :email, :password)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':email', $email);
                        $stmt->bindParam(':password', $hashedPassword);
                        $stmt->execute();
    
                        // Get User ID
                        $sql = "SELECT id FROM users WHERE username=:username";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(':username', $username);
                        $stmt->execute();
                        $realId = $stmt->fetchColumn();

                        // Get Role Id
                        $sql = "SELECT id FROM roles WHERE normalized_name=:normalized";
                        $stmt = $db->prepare($sql);
                        $normalized='USER';
                        $stmt->bindParam(':normalized', $normalized);
                        $stmt->execute();
                        $defaultUserRole = $stmt->fetchColumn();
    
                        // Create User Role
                        $query = "INSERT INTO UserRoles (UserId, RoleId) VALUES (:userId, :roleId)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':userId', $realId);
                        $stmt->bindParam(':roleId', $defaultUserRole);
                        $stmt->execute();
    
                        $db->commit();
    
    
                        $response = [
                            'status' => 'success',
                            'message' => 'Account created successfully'
                        ];
                    } catch (Exception $e) {
                        $db->rollBack();
                        $response = [
                            'status' => 'error',
                            'message' => $e->getMessage()
                        ];
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
}
