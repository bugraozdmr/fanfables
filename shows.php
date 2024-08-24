<?php 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


if (isset($_GET['slug'])) {
    //* show path
    $show_path = "/anime";

    $slug = $_GET['slug'];

    $jsonFile = __DIR__ . '/settings.json';
    $jsonData = file_get_contents($jsonFile);
    $data = json_decode($jsonData, true);
    $dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';

    $key = isset($data['key']) ? $data['key'] : '';

    require __DIR__ . '/vendor/autoload.php';

    //* CONNECT
    include __DIR__ . "/actions/connect.php";

    $loggedIn = true;
    if (isset($_COOKIE['auth_token'])) {
        $token = $_COOKIE['auth_token'];
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $username = $decoded->sub;

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
        $result = $stmt->fetch();

    } else {
        $loggedIn = false;
    }


    try {
        $query = "SELECT s.id as id,s.name as name,s.watchLink as watchLink,s.imdb as imdb, s.director as director,s.image as image,s.status as status,s.studio as studio,s.date_aired as date_aired, s.episode_count as episode_count,s.duration as duration,s.description as description,s.card_desc as card_desc,s.image as image,t.name as type
        FROM Shows s
        JOIN Types t ON t.id=s.typeId
        WHERE slug = :slug";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        $show = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($show) {
            //*set title
            $title = $show['name'];
            $pageDescription = $show['card_desc'];

            //? SHOW CATEGORIES
            $query = "SELECT c.name as name 
            FROM ShowCategories sc
            JOIN Categories c ON sc.CategoryId = c.id
            WHERE showId=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $show['id']);
            $stmt->execute();
            $showCategories = $stmt->fetchAll();

            $namesArray = array_column($showCategories, 'name');
            $cnamesString = implode(',', $namesArray);

        } else {
            // TODO CHANGE 404
            header('Location: /anime/404.php');
            exit();
        }
    } catch (PDOException $e) {
        echo 'Query failed: ' . $e->getMessage();
        exit();
    }
} else {
    header('Location: /anime/404.php');
    exit();
}

include "./components/up-all.php";
?>

    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="./index.html"><i class="fa fa-home"></i> Home</a>
                        <a href="./categories.html">Shows</a>
                        <span><?php echo $show['name'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="anime-details spad">
        <div class="container">
            <div class="anime__details__content">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="anime__details__pic set-bg" data-setbg="<?php echo $show_path.$show['image'] ?>">
                            <div class="comment"><i class="fa fa-comments"></i> 11</div>
                            <div class="view"><i class="fa fa-eye"></i> 9141</div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="anime__details__text">
                            <div class="anime__details__title">
                                <h3><?php echo $show['name'] ?></h3>
                                <?php if($show['director']) : ?>
                                    <span><?php echo $show['director'] ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="anime__details__rating">
                                <div class="rating">
                                    <span><img width="60" height="22" src="<?php echo $show_path."/img/imdb.webp" ?>" alt="imdb logo"></span>
                                </div>
                                <span><?php echo $show['imdb'] ?></span>
                            </div>
                            <p><?php echo $show['description'] ?></p>
                            <div class="anime__details__widget">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Type:</span> <?php echo $show['type'] ?></li>
                                            <li><span>Rating:</span> <?php echo $show['imdb'] ?></li>
                                            <li><span>Date :</span> <?php echo $show['date_aired'] ?></li>
                                            <?php if(!empty($show['status'])) : ?>
                                                <li><span>Status:</span> <?php echo $show['status'] ?></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Genre:</span> <?php echo $cnamesString ?></li>
                                            <li><span>Studios:</span>  <?php echo $show['studio'] ?? "" ?></li>
                                            <li><span>Duration:</span> <?php echo $show['duration'] ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="anime__details__btn">
                                <a href="#" class="follow-btn"><i class="fa fa-heart-o"></i> Watch Later</a>
                                <?php if(!empty($show['watchLink'])) : ?>
                                <a target="_blank" href="<?php echo $show['watchLink'] ?>" class="watch-btn"><span>Watch Now</span> <i
                                    class="fa fa-angle-right"></i></a>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-8">
                        <div class="anime__details__review">
                            <div class="section-title">
                                <h5>Reviews</h5>
                            </div>
                            <div class="anime__review__item">
                                <div class="anime__review__item__pic">
                                    <img src="img/anime/review-1.jpg" alt="">
                                </div>
                                <div class="anime__review__item__text">
                                    <h6>Chris Curry - <span>1 Hour ago</span></h6>
                                    <p>whachikan Just noticed that someone categorized this as belonging to the genre
                                    "demons" LOL</p>
                                </div>
                            </div>
                            <div class="anime__review__item">
                                <div class="anime__review__item__pic">
                                    <img src="img/anime/review-2.jpg" alt="">
                                </div>
                                <div class="anime__review__item__text">
                                    <h6>Lewis Mann - <span>5 Hour ago</span></h6>
                                    <p>Finally it came out ages ago</p>
                                </div>
                            </div>
                            <div class="anime__review__item">
                                <div class="anime__review__item__pic">
                                    <img src="img/anime/review-3.jpg" alt="">
                                </div>
                                <div class="anime__review__item__text">
                                    <h6>Louis Tyler - <span>20 Hour ago</span></h6>
                                    <p>Where is the episode 15 ? Slow update! Tch</p>
                                </div>
                            </div>
                            <div class="anime__review__item">
                                <div class="anime__review__item__pic">
                                    <img src="img/anime/review-4.jpg" alt="">
                                </div>
                                <div class="anime__review__item__text">
                                    <h6>Chris Curry - <span>1 Hour ago</span></h6>
                                    <p>whachikan Just noticed that someone categorized this as belonging to the genre
                                    "demons" LOL</p>
                                </div>
                            </div>
                            <div class="anime__review__item">
                                <div class="anime__review__item__pic">
                                    <img src="img/anime/review-5.jpg" alt="">
                                </div>
                                <div class="anime__review__item__text">
                                    <h6>Lewis Mann - <span>5 Hour ago</span></h6>
                                    <p>Finally it came out ages ago</p>
                                </div>
                            </div>
                            <div class="anime__review__item">
                                <div class="anime__review__item__pic">
                                    <img src="img/anime/review-6.jpg" alt="">
                                </div>
                                <div class="anime__review__item__text">
                                    <h6>Louis Tyler - <span>20 Hour ago</span></h6>
                                    <p>Where is the episode 15 ? Slow update! Tch</p>
                                </div>
                            </div>
                        </div>
                        <div class="anime__details__form">
                            <div class="section-title">
                                <h5>Your Comment</h5>
                            </div>
                            <form action="#">
                                <textarea placeholder="Your Comment"></textarea>
                                <button type="submit"><i class="fa fa-location-arrow"></i> Review</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="anime__details__sidebar">
                            <div class="section-title">
                                <h5>you might like...</h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-1.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">Boruto: Naruto next generations</a></h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-2.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">The Seven Deadly Sins: Wrath of the Gods</a></h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-3.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">Sword art online alicization war of underworld</a></h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-4.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">Fate/stay night: Heaven's Feel I. presage flower</a></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

<?php include "./components/down-all.php" ?>