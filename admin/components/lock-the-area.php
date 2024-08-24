<?php
// HEADERDAN OLAN KUNUMUNU BUL
$jsonFile = __DIR__ . '/../../settings.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);
$dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';

$key = isset($data['key']) ? $data['key'] : '';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require __DIR__.'/../../vendor/autoload.php';

if (isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $username = $decoded->sub;

    include __DIR__."/../../actions/connect.php";
    $query = "SELECT id FROM users WHERE username=:username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $result = $stmt->fetch();

    //* USERID
    $userId = $result['id'];

    $query = "SELECT r.normalized_name AS role
          FROM UserRoles ur
          INNER JOIN roles r ON ur.RoleId = r.id
          WHERE ur.UserId = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $rolee = [];
    foreach ($result as $row) {
        $rolee[] = $row['role'];
    }

    if(empty($rolee) || !in_array('ADMIN', $rolee)){
        // TODO CHANGE THIS LINK -- UNFORTUNE
        header("Location: /anime/404.php");
        exit();
    }
}
else{
    // TODO CHANGE THIS LINK -- UNFORTUNE
    header("Location: /anime/404.php");
    exit();
}
?>