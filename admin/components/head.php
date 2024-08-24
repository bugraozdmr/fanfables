<?php
$base_url = '/anime/admin/assets/';
$image_base = "/anime";
?>

<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>ADMIN PANEL</title>
<meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
<link rel="icon" href="<?php echo $image_base; ?>/img/favicon.ico" type="image/x-icon" />

<!-- Fonts Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Fonts and icons -->
<script src="<?php echo $base_url; ?>js/plugin/webfont/webfont.min.js"></script>
<script>
  WebFont.load({
    google: { families: ["Public Sans:300,400,500,600,700"] },
    custom: {
      families: [
        "Font Awesome 5 Solid",
        "Font Awesome 5 Regular",
        "Font Awesome 5 Brands",
        "simple-line-icons",
      ],
      urls: ["<?php echo $base_url; ?>css/fonts.min.css"],
    },
    active: function () {
      sessionStorage.fonts = true;
    },
  });
</script>

<!-- CSS Files -->
<link rel="stylesheet" href="<?php echo $base_url; ?>css/bootstrap.min.css" />
<link rel="stylesheet" href="<?php echo $base_url; ?>css/plugins.min.css" />
<link rel="stylesheet" href="<?php echo $base_url; ?>css/kaiadmin.min.css" />

<!-- CSS Just for demo purpose, don't include it in your project -->
<link rel="stylesheet" href="<?php echo $base_url; ?>css/demo.css" />
