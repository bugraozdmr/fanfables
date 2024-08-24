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

if (isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];
    $decoded = JWT::decode($token, new Key($key, 'HS256'));
    $username = $decoded->sub;


    //? connect first
    include __DIR__ . "/../actions/connect.php";

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
.dropdown{position:relative;display:inline-flex;align-items:center}.custom-avatar{width:28px;height:28px;border-radius:50%;cursor:pointer;margin-left:0}.dropdown-menu{display:none;position:absolute;top:60px;right:0;background-color:#fff;box-shadow:0 8px 16px rgba(0,0,0,.2);border-radius:5px;z-index:1000;min-width:120px}.dropdown-menu a{color:#000;padding:8px 12px;font-size:14px;text-decoration:none;display:block}.dropdown-menu a:hover{background-color:#f1f1f1}.dropdown.show .dropdown-menu{display:block}
</style>
<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <div class="header__logo">
                    <a href="<?php echo $header_path ?>/index.php">
                        <img width="98" height="23" src="img/logo.png" alt="">
                    </a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="header__nav">
                    <nav class="header__menu mobile-menu">
                        <ul>
                            <li class="active"><a href="<?php echo $header_path ?>/index.php">Homepage</a></li>
                            <li><a href="./categories.html">Categories <span class="arrow_carrot-down"></span></a>
                                <ul class="dropdown">
                                    <li><a href="./categories.html">Categories</a></li>
                                    <li><a href="./anime-details.html">Anime Details</a></li>
                                    <li><a href="./anime-watching.html">Anime Watching</a></li>
                                    <li><a href="./blog-details.html">Blog Details</a></li>
                                    <li><a href="<?php echo $header_path ?>/signup.php">Sign Up</a></li>
                                    <li><a href="<?php echo $header_path ?>/login.php">Login</a></li>
                                </ul>
                            </li>
                            <li><a href="./blog.html">Our Blog</a></li>
                            <li><a href="#">Contacts</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="header__right">
                    <a href="#" class="search-switch"><span class="icon_search"></span></a>
                    <?php if (isset($token) && !empty($token)): ?>
                        <a class="dropdown" href="#" onclick="return false;">
                            <img class="avatar custom-avatar" src="<?php echo $header_path.$img ?? $header_path . '/img/defuser.png' ?>" onclick="toggleDropdown()" />
                            <div id="dropdownMenu" class="dropdown-menu">
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
function toggleDropdown(){var e=document.getElementById("dropdownMenu");"block"===e.style.display?e.style.display="none":e.style.display="block"}window.onclick=function(e){if(!e.target.matches(".custom-avatar"))for(var n=document.getElementsByClassName("dropdown-menu"),o=0;o<n.length;o++){var t=n[o];"block"===t.style.display&&(t.style.display="none")}};
</script>