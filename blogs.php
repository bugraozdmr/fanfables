<?php
$title = "Blogs";
$pageDescription = "If you want to learn about the latest news about the shows and what to do, you are in a good place!";
$blog_path = "/anime";
$url_to = "http://localhost/anime/blogs.php";

include __DIR__ . '/actions/connect.php';

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $sanitized_query = filter_var($_GET['query'], FILTER_SANITIZE_STRING);
}


//* BLOG COUNT
$query = "SELECT COUNT(*) FROM Blog" . (isset($sanitized_query) && !empty($sanitized_query) ? " WHERE title LIKE '%" . $sanitized_query . "%'" : '');
$stmt = $db->prepare($query);
$stmt->execute();
$blog_count = $stmt->fetchColumn();

$total_blogs = $blog_count;

//! degisme !
$divide = 6;


$bpage_count = (int)ceil($total_blogs / $divide);
$blogPage = 1;
if (isset($_GET['bpage']) && $_GET['bpage'] > 0 && $bpage_count >= $_GET['bpage']) {
    $blogPage = (int)$_GET['bpage'];
}
$take = $divide;
$skipBlog = (int)(($blogPage - 1) * $take);

//* BLOGS
if(isset($sanitized_query)){
    $query = "SELECT slug,title,createdAt,image 
    FROM Blog " . " WHERE title LIKE '%" . $sanitized_query . "%'" . "
    LIMIT :take OFFSET :skip";
}
else{
    $query = "SELECT slug,title,createdAt,image 
    FROM Blog
    LIMIT :take OFFSET :skip";
}
$stmt = $db->prepare($query);
$stmt->bindParam(':take', $take, PDO::PARAM_INT);
$stmt->bindParam(':skip', $skipBlog, PDO::PARAM_INT);
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$showCount = count($blogs);

include __DIR__ . "/components/up-all.php";
?>

<section class="normal-breadcrumb set-bg" data-setbg="<?php echo $blog_path . "/img/breadcrump.jpg" ?>">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="normal__breadcrumb__text">
                    <h2>Blogs</h2>
                    <p>Welcome The World Of Watching</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="blog spad">
    <div class="container">
        <div class="col-lg-12">
            <form onsubmit="return redirectToSearchh()">
                <div class="input-group mb-4">
                    <input id="search-blog-input" type="text" class="form-control border-danger" placeholder="Search" aria-label="Search" <?php echo (isset($sanitized_query) && (!empty($sanitized_query)) ? "value=" . $sanitized_query : '') ?>>
                    <span class="input-group-text bg-danger text-white border-danger">
                        <i class="fa fa-search"></i>
                    </span>
                </div>
            </form>
        </div>

        <div class="row">
            <?php if ($showCount == 0) : ?>
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="section-title">
                        <div class="text-center">
                            <h2 class="mb-2" style="color: white !important;">No Blog Found</h2>
                            <img src="<?php echo $blog_path . "/img/nothing-found.png" ?>" alt="nothing found">
                        </div>
                    </div>
                </div>
            <?php elseif ($showCount == 1) : ?>
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="blog__item set-bg" data-setbg="<?php echo $blog_path . $blogs[0]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[0]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[0]['slug'] ?>">
                                            <?php echo $blogs[0]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($showCount == 2) : ?>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="blog__item set-bg" data-setbg="<?php echo $blog_path . $blogs[0]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[0]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[0]['slug'] ?>">
                                            <?php echo $blogs[0]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="blog__item set-bg" data-setbg="<?php echo $blog_path . $blogs[1]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[1]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[1]['slug'] ?>">
                                            <?php echo $blogs[1]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($showCount == 3) : ?>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="blog__item set-bg" data-setbg="<?php echo $blog_path . $blogs[0]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[0]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[0]['slug'] ?>">
                                            <?php echo $blogs[0]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[1]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[1]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[1]['slug'] ?>">
                                            <?php echo $blogs[1]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[2]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[2]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[2]['slug'] ?>">
                                            <?php echo $blogs[2]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($showCount == 4) : ?>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="blog__item set-bg" data-setbg="<?php echo $blog_path . $blogs[0]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[0]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[0]['slug'] ?>">
                                            <?php echo $blogs[0]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[1]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[1]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[1]['slug'] ?>">
                                            <?php echo $blogs[1]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[2]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[2]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[2]['slug'] ?>">
                                            <?php echo $blogs[2]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="blog__item set-bg" data-setbg="<?php echo $blog_path . $blogs[3]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[3]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[3]['slug'] ?>">
                                            <?php echo $blogs[3]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($showCount == 5) : ?>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="blog__item set-bg" data-setbg="<?php echo $blog_path . $blogs[0]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[0]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[0]['slug'] ?>">
                                            <?php echo $blogs[0]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[1]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[1]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[1]['slug'] ?>">
                                            <?php echo $blogs[1]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[2]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[2]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[2]['slug'] ?>">
                                            <?php echo $blogs[2]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[3]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[3]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[3]['slug'] ?>">
                                            <?php echo $blogs[3]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="blog__item set-bg" data-setbg="<?php echo $blog_path . $blogs[4]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[4]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[4]['slug'] ?>">
                                            <?php echo $blogs[4]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($showCount == 6) : ?>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="blog__item set-bg" data-setbg="<?php echo $blog_path . $blogs[0]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[0]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[0]['slug'] ?>">
                                            <?php echo $blogs[0]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[1]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[1]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[1]['slug'] ?>">
                                            <?php echo $blogs[1]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[2]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[2]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[2]['slug'] ?>">
                                            <?php echo $blogs[2]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[3]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[3]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[3]['slug'] ?>">
                                            <?php echo $blogs[3]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog_path . $blogs[4]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[4]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[4]['slug'] ?>">
                                            <?php echo $blogs[4]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="blog__item set-bg" data-setbg="<?php echo $blog_path . $blogs[5]['image'] ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php
                                                                            $date = new DateTime($blogs[5]['createdAt']);
                                                                            $formattedDate = $date->format('d F Y');
                                                                            echo $formattedDate;
                                                                            ?></p>
                                    <h4>
                                        <a href="<?php echo $blog_path . "/b/" . $blogs[5]['slug'] ?>">
                                            <?php echo $blogs[5]['title'] ?>
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="pagination__area text-center">
        <?php
        if ($total_blogs != 0) {
            $max_pages_to_show = 5;
            $total_pages = $bpage_count;
            $current_page = $blogPage;

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
        <?php if ($total_blogs != 0 && floor($total_blogs / $divide) != 0 && !(floor($total_blogs / $divide) == 1 && $total_blogs % $divide == 0)) : ?>
            <div class="product__pagination">
                <?php if ($start > 1) : ?>
                    <a href="<?php echo $dynamicUrl . "/blogs.php" . "?bpage=1" . (isset($sanitized_query) && !empty($sanitized_query) ? '&query=' . $sanitized_query : '') ?>">1</a>
                    <?php if ($start > 2) : ?>
                        <a href="#"><span>...</span></a>
                    <?php endif; ?>
                <?php endif; ?>
                <!-- Middle pages -->
                <?php for ($i = $start; $i <= $end; $i++) : ?>
                    <a class="<?php echo ($i == $current_page) ? 'current-page' : '' ?>" href="<?php echo $dynamicUrl . "/blogs.php"  . "?bpage=" . $i  . (isset($sanitized_query) && !empty($sanitized_query) ? '&query=' . $sanitized_query : '') ?>">
                        <?php echo $i ?>
                    </a>
                <?php endfor; ?>
                <!-- Last page -->
                <?php if ($end < $total_pages) : ?>
                    <?php if ($end < $total_pages - 1) : ?>
                        <a href="#"><span>...</span></a>
                    <?php endif; ?>
                    <a href="<?php echo $dynamicUrl . "/blogs.php" . "?bpage=" . $total_pages  . (isset($sanitized_query) && !empty($sanitized_query) ? '&query=' . $sanitized_query : '') ?>">
                        <?php echo $total_pages ?>
                    </a>
                <?php endif; ?>
                <?php if ($current_page != $total_pages) : ?>
                    <a href="<?php echo $dynamicUrl . "/blogs.php" . "?bpage=" . ($current_page + 1)  . (isset($sanitized_query) && !empty($sanitized_query) ? '&query=' . $sanitized_query : '') ?>"><i class="fa fa-angle-double-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    </div>
</section>

<script>
    function redirectToSearchh() {
        const e = document.getElementById("search-blog-input").value;
        if (e) {
            const n = `http://localhost/anime/blogs.php?query=${encodeURIComponent(e)}`;
            window.location.href = n
        }
        return !1
    }
</script>

<?php include __DIR__ . "/components/down-all.php" ?>