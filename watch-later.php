<?php
$title = "Watch Later";
$pageDescription = "Later watch list you can come and check whenever you want ...And share it with your friends !";
$watch_later_path = "/anime/";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require __DIR__ . '/vendor/autoload.php';

$jsonFile = __DIR__ . '/settings.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);
$dynamicUrl = isset($data['dynamicUrl']) ? $data['dynamicUrl'] : '';
$key = isset($data['key']) ? $data['key'] : '';

if (isset($_COOKIE['auth_token'])) {
    try {
        include './actions/connect.php';

        // TOKEN BOS GELEBILIR
        $token = $_COOKIE['auth_token'];
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $TokenUsername = $decoded->sub;

        $query = "SELECT id,username FROM users WHERE username=:username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $TokenUsername);
        $stmt->execute();
        // result altta kullanilmis galiba ondan hata aliyor onu görüyor yukardan assa
        $usrRes = $stmt->fetch();

        if (!isset($usrRes['username']) && empty($usrRes['username'])) {
            header('Location: /anime/404.php');
            exit();
        }
    } catch (\Exception $e) {
        header('Location: /anime/404.php');
        exit();
    }
} else {
    header('Location: /anime/404.php');
    exit();
}

include __DIR__ . "/actions/connect.php";

//* WATCH LATER LIST
$query = "SELECT s.image as image,wl.createdAt as createdAt,s.name as name,t.name as type,s.slug as slug,s.imdb as imdb,s.status as status
FROM WatchLater wl
JOIN users u ON wl.userId = u.id
JOIN Shows s ON s.id = wl.showId
JOIN Types t ON t.id = s.typeId
WHERE userId=:userId";
$stmt = $db->prepare($query);
$stmt->bindParam(':userId', $usrRes['id']);
$stmt->execute();
$watchLaterList = $stmt->fetchAll();

include __DIR__ . "/components/up-all.php"
?>


<section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="normal__breadcrumb__text">
                    <h2>Watch Later</h2>
                    <p>"I'm just a guy who's a hero for fun."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="login spad">
    <div class="container">
        <div class="row">
            <?php foreach ($watchLaterList as $wll) : ?>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="product__item">
                        <div class="product__item__pic set-bg" data-setbg="<?php echo $watch_later_path . "/" . $wll['image'] ?>">
                            <?php if (!empty($wll['imdb'])) : ?>
                                <div class="ep"><?php echo $wll['imdb'] ?></div>
                            <?php endif; ?>
                            <div class="view">
                                <a href="#" class="favorite-button" style="cursor: pointer;text-decoration:none;color:white" data-id="<?php echo $wll['slug']; ?>">
                                    <i class="fa fa-trash"></i> Remove From List
                                </a>
                            </div>
                        </div>
                        <div class="product__item__text">
                            <ul>
                                <li><?php echo $wll['type'] ?></li>
                                <?php if (!empty($wll['status']) && strtolower($wll['status']) == 'active') : ?>
                                    <li>Active</li>
                                <?php endif; ?>
                            </ul>
                            <h5>
                                <a href="<?php echo $dynamicUrl . "/s/" . $wll['slug'] ?>"><?php echo $wll['name'] ?></a>
                            </h5>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded",(function(){document.querySelectorAll(".favorite-button").forEach((function(e){e.addEventListener("click",(function(e){e.preventDefault();var t={slug:this.getAttribute("data-id")};fetch("/anime/actions/common/add-favs.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(t)}).then((e=>e.json())).then((e=>{"success"===e.status?window.location.reload():console.log(e.message)})).catch((e=>{console.error("Error:",e)}))}))}))}));
</script>

<?php include __DIR__ . "/components/down-all.php" ?>