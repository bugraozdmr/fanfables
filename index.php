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
//TODO veritabanı boşşsa kontrolu

//* Random GET
$query = "SELECT s.name as name,s.image as image,s.slug as slug,s.imdb as imdb,t.name as type
FROM SHOWS s
JOIN Types t ON t.id=s.typeId
ORDER BY RAND()
LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$randomShows = $stmt->fetchAll(PDO::FETCH_ASSOC);

//* RECENTLY ADDED
$query = "SELECT s.name as name,s.image as image,s.slug as slug,s.imdb as imdb,t.name as type
FROM SHOWS s
JOIN Types t ON t.id=s.typeId
ORDER BY s.created_at DESC
LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$recentShows = $stmt->fetchAll(PDO::FETCH_ASSOC);

//* MOST IMDB
$query = "SELECT s.name as name,s.image as image,s.slug as slug,s.imdb as imdb,t.name as type
FROM SHOWS s
JOIN Types t ON t.id=s.typeId
ORDER BY s.imdb DESC
LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$mostRated = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                                <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <?php foreach ($randomShows as $rands) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg" data-setbg="<?php echo $index_path . "/" . $rands['image'] ?>">
                                        <?php if (!empty($rands['imdb'])) : ?>
                                            <div class="ep"><?php echo $rands['imdb'] ?></div>
                                        <?php endif; ?>
                                        <div class="comment"><i class="fa fa-comments"></i> 11</div>
                                        <div class="view"><i class="fa fa-eye"></i> Character count TODO</div>
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
                                <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($mostRated as $mr) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg" data-setbg="<?php echo $index_path . "/" . $mr['image'] ?>">
                                        <?php if (!empty($mr['imdb'])) : ?>
                                            <div class="ep"><?php echo $mr['imdb'] ?></div>
                                        <?php endif; ?>
                                        <div class="comment"><i class="fa fa-comments"></i> 11</div>
                                        <div class="view"><i class="fa fa-eye"></i> Character count TODO</div>
                                    </div>
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
                                <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($recentShows as $rs) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg" data-setbg="<?php echo $index_path . "/" . $rs['image'] ?>">
                                        <?php if (!empty($rs['imdb'])) : ?>
                                            <div class="ep"><?php echo $rs['imdb'] ?></div>
                                        <?php endif; ?>
                                        <div class="comment"><i class="fa fa-comments"></i> 11</div>
                                        <div class="view"><i class="fa fa-eye"></i> Character count TODO</div>
                                    </div>
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
                            <h5>Top Views</h5>
                        </div>
                        <ul class="filter__controls">
                            <li class="active" data-filter="*">Day</li>
                            <li data-filter=".week">Week</li>
                            <li data-filter=".month">Month</li>
                            <li data-filter=".years">Years</li>
                        </ul>
                        <div class="filter__gallery">
                            <div class="product__sidebar__view__item set-bg mix day years"
                                data-setbg="img/sidebar/tv-1.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">Boruto: Naruto next generations</a></h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg mix month week"
                                data-setbg="img/sidebar/tv-2.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">The Seven Deadly Sins: Wrath of the Gods</a></h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg mix week years"
                                data-setbg="img/sidebar/tv-3.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">Sword art online alicization war of underworld</a></h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg mix years month"
                                data-setbg="img/sidebar/tv-4.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">Fate/stay night: Heaven's Feel I. presage flower</a></h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg mix day"
                                data-setbg="img/sidebar/tv-5.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">Fate stay night unlimited blade works</a></h5>
                            </div>
                        </div>
                    </div>
                    <div class="product__sidebar__comment">
                        <div class="section-title">
                            <h5>New Comment</h5>
                        </div>
                        <div class="product__sidebar__comment__item">
                            <div class="product__sidebar__comment__item__pic">
                                <img src="img/sidebar/comment-1.jpg" alt="">
                            </div>
                            <div class="product__sidebar__comment__item__text">
                                <ul>
                                    <li>Active</li>
                                    <li>Movie</li>
                                </ul>
                                <h5><a href="#">The Seven Deadly Sins: Wrath of the Gods</a></h5>
                                <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                            </div>
                        </div>
                        <div class="product__sidebar__comment__item">
                            <div class="product__sidebar__comment__item__pic">
                                <img src="img/sidebar/comment-2.jpg" alt="">
                            </div>
                            <div class="product__sidebar__comment__item__text">
                                <ul>
                                    <li>Active</li>
                                    <li>Movie</li>
                                </ul>
                                <h5><a href="#">Shirogane Tamashii hen Kouhan sen</a></h5>
                                <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                            </div>
                        </div>
                        <div class="product__sidebar__comment__item">
                            <div class="product__sidebar__comment__item__pic">
                                <img src="img/sidebar/comment-3.jpg" alt="">
                            </div>
                            <div class="product__sidebar__comment__item__text">
                                <ul>
                                    <li>Active</li>
                                    <li>Movie</li>
                                </ul>
                                <h5><a href="#">Kizumonogatari III: Reiket su-hen</a></h5>
                                <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                            </div>
                        </div>
                        <div class="product__sidebar__comment__item">
                            <div class="product__sidebar__comment__item__pic">
                                <img src="img/sidebar/comment-4.jpg" alt="">
                            </div>
                            <div class="product__sidebar__comment__item__text">
                                <ul>
                                    <li>Active</li>
                                    <li>Movie</li>
                                </ul>
                                <h5><a href="#">Monogatari Series: Second Season</a></h5>
                                <span><i class="fa fa-eye"></i> 19.141 Viewes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . "/components/down-all.php" ?>