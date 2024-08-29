<?php $title = "Home" ?>


<?php include __DIR__ . "/components/up-all.php" ?>
<?php include __DIR__ . '/components/main/hero.php' ?>

<?php
$jsonFile = './settings.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);
$dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';

$index_path = "/anime";

include __DIR__ . '/actions/connect.php';

//* Random GET
$query = "SELECT s.id as id,s.name as name,s.image as image,s.slug as slug,s.imdb as imdb,t.name as type
FROM SHOWS s
JOIN Types t ON t.id=s.typeId
ORDER BY RAND()
LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$randomShows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($randomShows as &$rrs){
    $query = "SELECT COUNT(*) FROM Comments WHERE showId=:showId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':showId', $rrs['id']);
    $stmt->execute();
    $ccount = $stmt->fetchColumn();

    $query = "SELECT COUNT(*) FROM Characters WHERE showId=:showId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':showId', $rrs1['id']);
    $stmt->execute();
    $ccounttt = $stmt->fetchColumn();

    $rrs['commentCount'] = $ccount;
    $rrs['characterCount'] = $ccounttt;
}


//* RECENTLY ADDED
$query = "SELECT s.id as id,s.name as name,s.image as image,s.slug as slug,s.imdb as imdb,t.name as type
FROM SHOWS s
JOIN Types t ON t.id=s.typeId
ORDER BY s.created_at DESC
LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$recentShows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($recentShows as &$rrs1){
    $query = "SELECT COUNT(*) FROM Comments WHERE showId=:showId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':showId', $rrs1['id']);
    $stmt->execute();
    $ccount = $stmt->fetchColumn();

    $query = "SELECT COUNT(*) FROM Characters WHERE showId=:showId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':showId', $rrs1['id']);
    $stmt->execute();
    $counttt = $stmt->fetchColumn();

    $rrs1['commentCount'] = $ccount;
    $rrs1['characterCount'] = $counttt;
}

//* MOST IMDB
$query = "SELECT s.id as id,s.name as name,s.image as image,s.slug as slug,s.imdb as imdb,t.name as type
FROM SHOWS s
JOIN Types t ON t.id=s.typeId
ORDER BY s.imdb DESC
LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$mostRated = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($mostRated as &$rrs2){
    $query = "SELECT COUNT(*) FROM Comments WHERE showId=:showId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':showId', $rrs2['id']);
    $stmt->execute();
    $ccount = $stmt->fetchColumn();

    $query = "SELECT COUNT(*) FROM Characters WHERE showId=:showId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':showId', $rrs1['id']);
    $stmt->execute();
    $counttt = $stmt->fetchColumn();

    $rrs2['commentCount'] = $ccount;
    $rrs2['characterCount'] = $counttt;
}

//* MOST ADDED TO WATH LATER
$query = "SELECT 
    s.name AS name,
    s.image AS image,
    s.slug AS slug,
    t.name AS typeName,
    COUNT(wl.showId) AS repetitionCount
FROM 
    WatchLater wl
JOIN 
    Shows s ON s.id = wl.showId
JOIN 
    Types t ON t.id = s.typeId
GROUP BY 
    wl.showId, s.name, s.image, t.name
ORDER BY 
    repetitionCount DESC
LIMIT 4;
";
$stmt = $db->prepare($query);
$stmt->execute();
$watchLaterList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tüm sonuçları tutacak dizi
$mostCommented = [];

// Yıl, ay, hafta, gün sorgularının her birini çalıştırıp sonucu aynı diziye ekleyeceğiz
$queries = [
    'years' => "
        SELECT 
            s.id as id,
            s.name AS name,
            s.slug AS slug,
            s.image AS image,
            s.imdb AS imdb,
            COUNT(c.id) AS commentCount
        FROM 
            Comments c
        JOIN 
            Shows s ON s.id = c.showId
        WHERE 
            YEAR(c.createdAt) = YEAR(CURDATE())
        GROUP BY 
            s.id, s.name, s.image, s.imdb
        ORDER BY 
            commentCount DESC
        LIMIT 3;
    ",
    'month' => "
        SELECT 
            s.id as id,
            s.name AS name,
            s.slug AS slug,
            s.image AS image,
            s.imdb AS imdb,
            COUNT(c.id) AS commentCount
        FROM 
            Comments c
        JOIN 
            Shows s ON s.id = c.showId
        WHERE 
            MONTH(c.createdAt) = MONTH(CURDATE())
            AND YEAR(c.createdAt) = YEAR(CURDATE())
        GROUP BY 
            s.id, s.name, s.image, s.imdb
        ORDER BY 
            commentCount DESC
        LIMIT 3;
    ",
    'week' => "
        SELECT 
            s.id as id,
            s.name AS name,
            s.slug AS slug,
            s.image AS image,
            s.imdb AS imdb,
            COUNT(c.id) AS commentCount
        FROM 
            Comments c
        JOIN 
            Shows s ON s.id = c.showId
        WHERE 
            WEEK(c.createdAt, 1) = WEEK(CURDATE(), 1)
            AND YEAR(c.createdAt) = YEAR(CURDATE())
        GROUP BY 
            s.id, s.name, s.image, s.imdb
        ORDER BY 
            commentCount DESC
        LIMIT 3;
    "
];

foreach ($queries as $dateType => $query) {
    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $result) {
        $id = $result['id'];

        if (isset($mostCommented[$id])) {
            $mostCommented[$id]['date'][] = $dateType;
        } else {
            $mostCommented[$id] = $result;
            $mostCommented[$id]['date'] = [$dateType];
        }
    }
}

// Dizi formatında çıktı
$mostCommented = array_values($mostCommented);

?>


<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="trending__product">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <div class="section-title">
                                <h4>What Should I Watch Today ?</h4>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="btn__all">
                                <a href="<?php echo $index_path."/all-shows.php" ?>" class="primary-btn">View All <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <?php foreach ($randomShows as $rands) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <a href="<?php echo $dynamicUrl . "/s/" . $rands['slug'] ?>" title="goes to the show">
                                        <div class="product__item__pic set-bg" data-setbg="<?php echo $index_path . "/" . $rands['image'] ?>">
                                            <?php if (!empty($rands['imdb'])) : ?>
                                                <div class="ep"><?php echo $rands['imdb'] ?></div>
                                            <?php endif; ?>
                                            <div class="comment"><i class="fa fa-comments"></i> <?php echo $rands['commentCount'] ?></div>
                                            <div class="view"><i class="fa fa-users"></i> <?php echo $rands['characterCount'] ?> </div>
                                        </div>
                                    </a>
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
                </div>
                <div class="popular__product">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <div class="section-title">
                                <h4>IMDB Most Rated</h4>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="btn__all">
                                <a href="<?php echo $index_path."/all-shows.php" ?>" class="primary-btn">View All <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($mostRated as $mr) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <a href="<?php echo $dynamicUrl . "/s/" . $mr['slug'] ?>" title="goes to the show">
                                        <div class="product__item__pic set-bg" data-setbg="<?php echo $index_path . "/" . $mr['image'] ?>">
                                            <?php if (!empty($mr['imdb'])) : ?>
                                                <div class="ep"><?php echo $mr['imdb'] ?></div>
                                            <?php endif; ?>
                                            <div class="comment"><i class="fa fa-comments"></i> <?php echo $mr['commentCount'] ?></div>
                                            <div class="view"><i class="fa fa-users"></i> <?php echo $mr['characterCount'] ?></div>
                                        </div>
                                    </a>
                                    <div class="product__item__text">
                                        <ul>
                                            <li><?php echo $mr['type'] ?></li>
                                            <?php if (!empty($mr['status']) && strtolower($mr['status']) == 'active') : ?>
                                                <li>Active</li>
                                            <?php endif; ?>
                                        </ul>
                                        <h5><a href="<?php echo $dynamicUrl . "/s/" . $mr['slug'] ?>"><?php echo $mr['name'] ?></a></h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="recent__product">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <div class="section-title">
                                <h4>Recently Added Shows</h4>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="btn__all">
                                <a href="<?php echo $index_path."/all-shows.php" ?>" class="primary-btn">View All <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($recentShows as $rs) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <a href="<?php echo $dynamicUrl . "/s/" . $rs['slug'] ?>" title="goes to the show">
                                        <div class="product__item__pic set-bg" data-setbg="<?php echo $index_path . "/" . $rs['image'] ?>">
                                            <?php if (!empty($rs['imdb'])) : ?>
                                                <div class="ep"><?php echo $rs['imdb'] ?></div>
                                            <?php endif; ?>
                                            <div class="comment"><i class="fa fa-comments"></i> <?php echo $rs['commentCount'] ?></div>
                                            <div class="view"><i class="fa fa-users"></i> <?php echo $rs['characterCount'] ?></div>
                                        </div>
                                    </a>
                                    <div class="product__item__text">
                                        <ul>
                                            <li><?php echo $rs['type'] ?></li>
                                            <?php if (!empty($rs['status']) && strtolower($rs['status']) == 'active') : ?>
                                                <li>Active</li>
                                            <?php endif; ?>
                                        </ul>
                                        <h5><a href="<?php echo $dynamicUrl . "/s/" . $rs['slug'] ?>"><?php echo $rs['name'] ?></a></h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-8">
                <div class="product__sidebar">
                    <div class="product__sidebar__view">
                        <div class="section-title">
                            <h5>Top Comments</h5>
                        </div>
                        <ul class="filter__controls">
                            <li class="active" data-filter="*">All</li>
                            <li data-filter=".week">Week</li>
                            <li data-filter=".month">Month</li>
                            <li data-filter=".years">Years</li>
                        </ul>
                        <div class="filter__gallery">
                            <?php foreach($mostCommented as $mc) : ?>
                                <?php $classes = implode(' ', $mc['date']); ?>
                                <div 
                                    class="product__sidebar__view__item set-bg mix <?php echo $classes ?>"
                                    data-setbg="<?php echo $index_path."/".$mc['image'] ?>">
                                    <div class="ep"><?php echo $mc['imdb'] ?></div>
                                    <div class="view"><i class="fa fa-comment"></i> <?php echo $mc['commentCount'] ?></div>
                                    <h5><a href="<?php echo $index_path."/s/".$mc['slug'] ?>"><?php echo $mc['name'] ?></a></h5>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="product__sidebar__comment">
                        <div class="section-title">
                            <h5>Most added to watch later</h5>
                        </div>
                        <?php foreach($watchLaterList as $wlls) : ?>
                            <div class="product__sidebar__comment__item">
                                <a href="<?php echo $dynamicUrl . "/s/" . $wlls['slug'] ?>" title="goes to the show">
                                    <div class="product__sidebar__comment__item__pic">
                                        <img width="100" height="130" src="<?php echo $index_path."/".$wlls['image'] ?>" alt="watch later list image">
                                    </div>
                                </a>
                                <div class="product__sidebar__comment__item__text">
                                    <ul>
                                        <li><?php echo $wlls['typeName'] ?></li>
                                        <?php if (!empty($wlls['status']) && strtolower($wlls['status']) == 'active') : ?>
                                            <li>Active</li>
                                        <?php endif; ?>
                                    </ul>
                                    <h5><a href="<?php echo $index_path."/s/".$wlls['slug'] ?>"><?php echo $wlls['name'] ?></a></h5>
                                    <span><i class="fa fa-heart-o"></i> <?php echo $wlls['repetitionCount'] ?></span>
                                </div>
                            </div>
                        <?php endforeach ; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . "/components/down-all.php" ?>