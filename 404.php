<!DOCTYPE html>
<html lang="tr">
    <?php $currPath = "/anime" ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $currPath ?>/vendors/notFound/style.css">
    <title>404</title>
</head>
<?php 
$jsonFile = './settings.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);
$dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';
?>
<body>
    <a href="<?php echo $dynamicUrl ?>">
        <svg height="0.8em" width="0.8em" viewBox="0 0 2 1" preserveAspectRatio="none">
            <polyline fill="none" stroke="#777777" stroke-width="0.1" points="0.9,0.1 0.1,0.5 0.9,0.9" />
        </svg> Home
    </a>
    <div class="background-wrapper">
        <h1 id="visual">404</h1>
    </div>
    <p>The page you’re looking for does not exist</p>
    <script src="<?php echo $currPath ?>/vendors/notFound/index.js"></script>
</body>
</html>