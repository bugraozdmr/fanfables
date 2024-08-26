<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require __DIR__.'/vendor/autoload.php';

function timeAgo($dateTime)
{
    $timezone = new DateTimeZone('Europe/Istanbul');
    $now = new DateTime('now', $timezone);
    $date = new DateTime($dateTime, $timezone);
    $interval = $now->diff($date);

    if ($date > $now) {
        return $date->format('d.m.Y H:i:s');
    }

    if ($interval->y > 0) {
        return $interval->y . ' years ago';
    } elseif ($interval->m > 0) {
        return $interval->m . ' months ago';
    } elseif ($interval->d > 0) {
        return $interval->d . ' days ago';
    } elseif ($interval->h > 0) {
        return $interval->h . ' hours ago';
    } elseif ($interval->i > 0) {
        return $interval->i . ' minutes ago';
    } else {
        return 'Now';
    }
}


if (isset($_GET['username'])) {
    $jsonFile = __DIR__ . '/settings.json';
    $jsonData = file_get_contents($jsonFile);
    $data = json_decode($jsonData, true);
    $dynamicUrl = isset($data['dynamicUrl']) ? $data['dynamicUrl'] : '';
    $key = isset($data['key']) ? $data['key'] : '';

    $username = $_GET['username'];
    include './actions/connect.php';
    $query = "SELECT id,name,image,bannerImage,description,username,email,createdAt FROM users WHERE username=:username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $useer = $stmt->fetch();

    if (isset($useer) && !empty($useer) && !empty($useer['username'])) {
        $title = 'User '.$useer['username'];

        $query = "SELECT COUNT(*) AS count FROM Comments WHERE userId=:userId";
        $stmt = $db->prepare($query);
        $userId = $useer['id'];
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        $cc = $stmt->fetch();

        $rcount = $cc['count'];

        $query = "SELECT COUNT(*) AS count FROM WatchLater WHERE userId=:userId";
        $stmt = $db->prepare($query);
        $userId = $useer['id'];
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        $cc = $stmt->fetch();

        $qcount = $cc['count'];
    } else {
        header('Location: /anime/404.php');
        exit();
    }

    if (isset($_COOKIE['auth_token'])) {
        try {
            // TOKEN BOS GELEBILIR
            $token = $_COOKIE['auth_token'];
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $TokenUsername = $decoded->sub;
    
            $query = "SELECT id FROM users WHERE username=:username";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $TokenUsername);
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
        } catch (\Exception $e) {
            header('Location: /anime/404.php');
            exit();
        }
    }

} else {
    header('Location: /anime/404.php');
    exit();
}
include './components/up-all.php'
?>

<style>
    @import url(https://fonts.googleapis.com/css2?family=Poppins:weight@100;200;300;400;500;600;700;800&display=swap);.card,.upper,.user{position:relative}body{font-family:Poppins,sans-serif;font-weight:300}.container-user{height:80vh}.card{width:380px;border:none;border-radius:15px;padding:8px;background-color:#fff;height:400px}.upper{height:100px;overflow:hidden}.upper img{width:100%;height:100%;object-fit:cover;border-top-left-radius:10px;border-top-right-radius:10px;position:absolute;top:0;left:0}.profile img{height:80px;width:80px;margin-top:2px}.profile{position:absolute;top:-50px;left:38%;height:90px;width:90px;border:3px solid #fff;border-radius:50%}.follow{border-radius:15px;padding-left:20px;padding-right:20px;height:35px}.stats span{font-size:29px}
</style>

<div class="container-user d-flex justify-content-center align-items-center">
    <div class="card">
        <div class="upper">
            <img src="<?php echo $dynamicUrl . ($useer['bannerImage'] ?? '/img/default_banner.jpg') ?>" class="img-fluid">
        </div>
        <div class="user text-center">
            <div class="profile">
                <img src="<?php echo $dynamicUrl . ($useer['image'] ?? '/img/defuser.png') ?>" class="rounded-circle" width="80">
            </div>
        </div>
        <div class="mt-5 text-center">
            <h4 class="mb-2"><?php echo $useer['username'] ?></h4>
            <h5>Joined <?php echo timeAgo($useer['createdAt']) ?></h5>
            <h6 class="mt-3" style="
                padding: 10px;
                border: 4px solid;
                border-image: linear-gradient(45deg, #ff6b6b, #feca57, #48dbfb, #1dd1a1);
                border-image-slice: 1;
                border-radius: 8px;
                display: inline-block;
            ">
                <?php echo (isset($useer['description']) && !empty($useer['description'])) ? $useer['description'] : 'No info given' ?>
            </h6>
            <?php if(isset($result) && !empty($result) && in_array('ADMIN', $rolee)) : ?>
                <span class="d-block" style="color:cadetblue">- User Contact Info -</span>
                <?php if(isset($useer['name']) && !empty($useer['name'])) : ?>
                    <span class="text-muted d-block"><span style="color:orangered">Name :</span><?php echo $useer['name'] ?></span>
                <?php endif ; ?>
                <?php if(isset($useer['email']) && !empty($useer['email'])) : ?>
                    <span class="text-muted d-block mb"><span style="color:orangered">Mail :</span><?php echo $useer['email'] ?></span>
                <?php endif ; ?>
            <?php endif ; ?>
            <div class="d-flex justify-content-between align-items-center mt-4 px-4">
                <div class="stats">
                    <h6 class="mb-0">Comments</h6>
                    <span><?php echo $rcount ?></span>
                </div>
                <div class="stats">
                    <h6 class="mb-0">Blog Entrys TODO</h6>
                    <span><?php echo $qcount ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './components/down-all.php' ?>