<?php
$title = "All Categories";
$pageDescription = "Whatever you want from A to Z";
$all_cat_path = "/anime";

include __DIR__.'/actions/connect.php';

$query = "SELECT * FROM Categories";
$stmt = $db->prepare($query);
$stmt->execute();
$cattegories = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($cattegories as &$ccs){
    $cattId = $ccs['id'];

    $query = "SELECT COUNT(*) FROM ShowCategories WHERE CategoryId=:ccid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':ccid', $cattId);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    $ccs['showCount'] = $count;
}

include __DIR__ . "/components/up-all.php";
?>
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="<?php echo $all_cat_path ?>/index.php"><i class="fa fa-home"></i> Home</a>
                    <a href="<?php echo $all_cat_path ?>/all-categories.php">All Categories</a>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="product-page spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="product__page__content">
                    <div class="product__page__title">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="section-title">
                                    <h4>All Categories</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach($cattegories as $cats) : ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__sidebar__view__item set-bg mix month week"
                                    data-setbg="<?php echo $all_cat_path.$cats['categoryImage'] ?>">
                                    <div class="ep"><?php echo $cats['showCount']." SHOWS" ?></div>
                                    <div class="view"><i class="fa fa-eye"></i> Chracter count</div>
                                    <h5><a href="<?php echo $all_cat_path."/".$cats['slug'] ?>"><?php echo $cats['name'] ?></a></h5>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . "/components/down-all.php"; ?>