<?php 
$title = "All Shows";
$pageDescription = "Whatever you are looking for is here. Are you hungry to watch movies and TV series and you don't have any idea what to watch ? Come here.";
?>


<?php include __DIR__ . "/components/up-all.php" ?>

<?php
$jsonFile = './settings.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);
$dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';

$all_shows_path = "/anime";

include __DIR__ . '/actions/connect.php';


//* ALL SHOWS FILTERED
if(isset($_GET['category']) && !empty($_GET['category'])){
    $sanitized = filter_var($_GET['category'],FILTER_SANITIZE_STRING);
    $query = "SELECT id,name,slug FROM Categories WHERE slug=:slug";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':slug', $sanitized,PDO::PARAM_STR);
    $stmt->execute();
    $ressss = $stmt->fetch();
    $category_id = $ressss['id'];
    $category_name = $ressss['name'];
    $category_slug = $ressss['slug'];
}


//? default query
if(isset($category_id) && !empty($category_id)){
    $query = "SELECT COUNT(*) 
    FROM Shows s
    JOIN ShowCategories sc ON sc.showId=s.id
    JOIN Categories c ON sc.categoryId=c.id
    WHERE c.id=:cid
    ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':cid', $category_id,PDO::PARAM_STR);
    $stmt->execute();
    $showCount = $stmt->fetchColumn();
}
else {
    $query = "SELECT COUNT(*) FROM Shows";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $showCount = $stmt->fetchColumn();
}

$total_shows = $showCount;
//* CHANGE
$divide = 15;
if(isset($_GET['c']) && $_GET['c'] > 0 && ($_GET['c']==10 || $_GET['c']==20)){
    $divide = $_GET['c'];
}


$spage_count = (int)ceil($total_shows / $divide);
$showPage = 1;
if (isset($_GET['page']) && $_GET['page'] > 0 && $spage_count >= $_GET['page']) {
    $showPage = (int)$_GET['page'];
}
//* take part
$take = $divide;
if(isset($_GET['c']) && $_GET['c'] > 0 && ($_GET['c']==10 || $_GET['c']==20)){
    $take = $_GET['c'];
}
$skipShow = (int)(($showPage - 1) * $take);

//* sort part
if(isset($_GET['sort']) && ($_GET['sort']=='a-z' || $_GET['sort']=='A-Z')){
    $sortt = $_GET['sort'];
}


if(isset($category_id) && !empty($category_id)){
    $query = "
    SELECT s.id AS id, s.name AS name, s.image AS image, s.slug AS slug, s.imdb AS imdb, t.name AS type
    FROM SHOWS s
    JOIN Types t ON t.id = s.typeId
    JOIN ShowCategories sc ON sc.showId = s.id
    JOIN Categories c ON c.id = sc.categoryId
    WHERE c.id=:cid
    ORDER BY " . (isset($sortt) ? "s.name ASC, s.created_at DESC" : "s.created_at DESC") . "
    LIMIT :take OFFSET :skip
    ";
}
else{
    $query = "
    SELECT s.id AS id, s.name AS name, s.image AS image, s.slug AS slug, s.imdb AS imdb, t.name AS type
    FROM SHOWS s
    JOIN Types t ON t.id = s.typeId
    ORDER BY " . (isset($sortt) ? "s.name ASC, s.created_at DESC" : "s.created_at DESC") . "
    LIMIT :take OFFSET :skip
    ";
}


$stmt = $db->prepare($query);
$stmt->bindParam(':take', $take, PDO::PARAM_INT);
$stmt->bindParam(':skip', $skipShow, PDO::PARAM_INT);
if(isset($category_id) && !empty($category_id)){
    $stmt->bindParam(':cid', $category_id);
}
$stmt->execute();

$allShhows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($allShhows as &$rrs2) {
    $query = "SELECT COUNT(*) FROM Comments WHERE showId=:showId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':showId', $rrs2['id']);
    $stmt->execute();
    $ccount = $stmt->fetchColumn();

    $query = "SELECT COUNT(*) FROM Characters WHERE showId=:showId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':showId', $rrs1['id']);
    $stmt->execute();
    $ccountt = $stmt->fetchColumn();

    $rrs2['commentCount'] = $ccount;
    $rrs2['characterCount'] = $ccountt;
}
?>

<style>
    .product__page__filter{font-family:Arial,sans-serif}.custom-dropdown{position:relative;display:inline-block}.dropdown-button{background-color:#f8f9fa;color:#495057;border:1px solid #ced4da;padding:6px 12px;cursor:pointer;border-radius:4px;font-size:14px;transition:background-color .3s,border-color .3s;width:120px;text-align:center}.custom-dropdown:hover .dropdown-button,.dropdown-button:hover{background-color:#e2e6ea;border-color:#adb5bd}.dropdown-content{display:none;position:absolute;background-color:#fff;min-width:120px;box-shadow:0 4px 8px rgba(0,0,0,.2);z-index:1;border-radius:4px;top:100%;left:0;padding:0}.dropdown-content a{color:#000;padding:8px 12px;text-decoration:none;display:block;font-size:14px;text-align:center}.dropdown-content a:hover{background-color:#f1f1f1}.custom-dropdown:hover .dropdown-content{display:block}
</style>

<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="trending__product">
                    <div class="product__page__title">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-6">
                                <div class="section-title">
                                    <h4><?php echo (isset($category_name) && !empty($category_name) ? $category_name : 'All Shows' ) ?></h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6">
                                <div class="product__page__filter">
                                    <p>Order by:</p>
                                    <div class="custom-dropdown">
                                        <button class="dropdown-button">Select option</button>
                                        <div class="dropdown-content">
                                            <a href="<?php echo $all_shows_path . "/all-shows.php?sort=a-z".(isset($_GET['c']) ? '&c='.$_GET['c'] : '').(isset($category_name) && !empty($category_name) ? '&category='.$category_name : '').(isset($_GET['page']) && !empty($_GET['page']) ? '&page='.$_GET['page'] : '') ?>">A-Z</a>
                                            <a href="<?php echo $all_shows_path . "/all-shows.php?c=10".(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '').(isset($category_name) && !empty($category_name) ? '&category='.$category_name : '').(isset($_GET['page']) && !empty($_GET['page']) ? '&page='.$_GET['page'] : '') ?>">1-10</a>
                                            <a href="<?php echo $all_shows_path . "/all-shows.php?c=20".(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '').(isset($category_name) && !empty($category_name) ? '&category='.$category_name : '').(isset($_GET['page']) && !empty($_GET['page']) ? '&page='.$_GET['page'] : '') ?>">1-20</a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($allShhows as $rands) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg" data-setbg="<?php echo $all_shows_path . "/" . $rands['image'] ?>">
                                        <?php if (!empty($rands['imdb'])) : ?>
                                            <div class="ep"><?php echo $rands['imdb'] ?></div>
                                        <?php endif; ?>
                                        <div class="comment"><i class="fa fa-comments"></i> <?php echo $rands['commentCount'] ?></div>
                                        <div class="view"><i class="fa fa-users"></i> <?php echo $rands['characterCount'] ?> </div>
                                    </div>
                                    <div class="product__item__text">
                                        <ul>
                                            <li><?php echo $rands['type'] ?></li>
                                            <?php if (!empty($rands['status']) && strtolower($rands['status']) == 'active') : ?>
                                                <li>Active</li>
                                            <?php endif; ?>
                                        </ul>
                                        <h5><a href="<?php echo $dynamicUrl . "/s/" . $rands['slug'] ?>"><?php echo $rands['name'] ?></a></h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="pagination__area">
                        <?php
                        if ($total_shows != 0) {
                            $max_pages_to_show = 5;
                            $total_pages = $spage_count;
                            $current_page = $showPage;

                            $start = max(1, $current_page - floor($max_pages_to_show / 2));
                            $end = min($total_pages, $current_page + floor($max_pages_to_show / 2));

                            if ($end - $start + 1 < $max_pages_to_show) {
                                if ($start == 1) {
                                    $end = min($total_pages, $end + ($max_pages_to_show - ($end - $start + 1)));
                                } else {
                                    $start = max(1, $start - ($max_pages_to_show - ($end - $start + 1)));
                                }
                            }
                        }
                        ?>
                        <?php if ($total_shows != 0 && floor($total_shows / $divide) != 0) : ?>
                            <div class="product__pagination">
                                <?php if ($start > 1) : ?>
                                    <a href="<?php echo $dynamicUrl . "/all-shows.php" . "?page=1".(isset($category_slug) && !empty($category_slug) ? '&category='.$category_slug : '').(isset($_GET['c']) ? '&c='.$_GET['c'] : '' ).(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '') ?>">1</a>
                                    <?php if ($start > 2) : ?>
                                        <a href="#"><span>...</span></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <!-- Middle pages -->
                                <?php for ($i = $start; $i <= $end; $i++) : ?>
                                    <a class="<?php echo ($i == $current_page) ? 'current-page' : '' ?>" href="<?php echo $dynamicUrl . "/all-shows.php"  . "?page=" . $i.(isset($category_slug) && !empty($category_slug) ? '&category='.$category_slug : '').(isset($_GET['c']) ? '&c='.$_GET['c'] : '' ).(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '') ?>">
                                    <?php echo $i ?>
                                </a>
                                <?php endfor; ?>
                                <!-- Last page -->
                                <?php if ($end < $total_pages) : ?>
                                    <?php if ($end < $total_pages - 1) : ?>
                                        <a href="#"><span>...</span></a>
                                    <?php endif; ?>
                                    <a href="<?php echo $dynamicUrl . "/all-shows.php" . "?page=" . $total_pages.(isset($category_slug) && !empty($category_slug) ? '&category='.$category_slug : '').(isset($_GET['c']) ? '&c='.$_GET['c'] : '' ).(isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '')  ?>">
                                    <?php echo $total_pages ?>
                                </a>
                                <?php endif; ?>
                                <?php if ($current_page != $total_pages) : ?>
                                    <a href="<?php echo $dynamicUrl . "/all-shows.php" . "?page=" . ($current_page + 1).(isset($category_slug) && !empty($category_slug) ? '&category='.$category_slug : '').(isset($_GET['c']) ? '&c='.$_GET['c'] : '' ).(isset($_GET['sort'] )? '&sort='.$_GET['sort'] : '') ?>"><i class="fa fa-angle-double-right"></i></a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.querySelector(".dropdown-button").addEventListener("click",(function(){var n=document.querySelector(".dropdown-content"),e="block"===n.style.display;n.style.display=e?"none":"block"})),window.addEventListener("click",(function(n){n.target.matches(".dropdown-button")||document.querySelectorAll(".dropdown-content").forEach((function(n){n.style.display="none"}))}));
</script>

<?php include __DIR__ . "/components/down-all.php" ?>