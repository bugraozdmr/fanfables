<?php 
$jsonFile = __DIR__.'/../../settings.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);
$dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';

$hero_path = "/anime";

include __DIR__ . '/../../actions/connect.php';

//* RECENTLY ADDED
$query = "SELECT s.name as name,s.image as image,s.description as description,s.slug as slug,t.name as type
FROM SHOWS s
JOIN Types t ON t.id=s.typeId
ORDER BY s.created_at DESC
LIMIT 4";
$stmt = $db->prepare($query);
$stmt->execute();
$hero = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="hero">
    <div class="container">
        <div class="hero__slider owl-carousel">
            <?php foreach($hero as $heroe) : ?>
            <div class="hero__items set-bg" data-setbg="<?php echo $hero_path.$heroe['image'] ?>">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hero__text">
                            <div class="label"><?php echo $heroe['type'] ?></div>
                            <h2><?php echo $heroe['name'] ?></h2>
                            <p><?php echo $heroe['description'] ?></p>
                            <a href="<?php echo $dynamicUrl."/s/".$heroe['slug'] ?>"><span>See More</span> <i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>