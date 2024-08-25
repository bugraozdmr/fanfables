<?php
$header_path = "/anime";

$jsonFile = __DIR__ . '/../settings.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);
$dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';

$key = isset($data['key']) ? $data['key'] : '';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require 'vendor/autoload.php';


//? connect first
include __DIR__ . "/../actions/connect.php";

if (isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $username = $decoded->sub;



    $query = "SELECT id,username,image FROM users WHERE username=:username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $result = $stmt->fetch();

    $usernamee = $result['username'];
    $img = $result['image'];

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
}
?>
<style>
.dropdown1{position:relative;display:inline-block}.custom-avatar{width:28px;height:28px;border-radius:50%;cursor:pointer}.dropdown1-menu{display:none;position:absolute;top:35px;right:0;background-color:#fff;box-shadow:0 4px 8px rgba(0,0,0,.15);border-radius:4px;z-index:1000;min-width:140px;padding:8px 0}.dropdown1-menu a{color:#333;padding:6px 10px;font-size:13px;text-decoration:none;display:block;text-align:left;white-space:nowrap}.dropdown1-menu a:hover{background-color:#f1f1f1}.dropdown1.show .dropdown1-menu{display:block}
</style>
<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <div class="header__logo">
                    <a href="<?php echo $header_path ?>/index.php">
                        <img width="98" height="23" src="<?php echo $header_path ?>/img/logo.png" alt="logo image">
                    </a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="header__nav">
                    <nav class="header__menu mobile-menu">
                        <ul>
                            <li class="active"><a href="<?php echo $header_path ?>/index.php">Homepage</a></li>
                            <li><a href="<?php echo $header_path ?>/all-categories.php">Categories</a></li>
                            <li><a href="./blog.html">Our Blog</a></li>
                            <?php if(isset($token) && !empty($token)) : ?>
                                <li><a href="<?php echo $header_path ?>/watch-later.php">Watch Later</a></li>
                            <?php endif ; ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="header__right">
                    <a href="#" class="search-switch"><span class="icon_search"></span></a>
                    <?php if (isset($token) && !empty($token)): ?>
                        <a class="dropdown1" href="#" onclick="return false;">
                            <img class="avatar custom-avatar" src="<?php echo $header_path.$img ?? $header_path . '/img/defuser.png' ?>" onclick="toggleDropdown()" />
                            <div id="dropdownMenu" class="dropdown1-menu">
                                <a href="<?php echo $header_path ?>/profile.php">Profile</a>
                                <a href="<?php echo $header_path ?>/logout.php">Logout</a>
                            </div>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo $header_path ?>/login.php"><span class="icon_profile"></span></a>
                    <?php endif; ?>
                    <?php if (isset($rolee) && !empty($rolee) && in_array('ADMIN', $rolee)): ?>
                        <a href="/anime/admin" class="user__menu">
                            <i class="fa fa-lock" style="font-size:large;" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div id="mobile-menu-wrap"></div>
    </div>
</header>

<script>
function toggleDropdown(){var e=document.getElementById("dropdownMenu");"block"===e.style.display?e.style.display="none":e.style.display="block"}window.onclick=function(e){if(!e.target.matches(".custom-avatar"))for(var n=document.getElementsByClassName("dropdown1-menu"),o=0;o<n.length;o++){var t=n[o];"block"===t.style.display&&(t.style.display="none")}};
</script>