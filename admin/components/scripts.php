<?php
$base_url = '/e-trade/admin/assets/js/';
?>

<!-- Core JS Files -->
<script src="<?php echo $base_url; ?>core/jquery-3.7.1.min.js"></script>
<script src="<?php echo $base_url; ?>core/popper.min.js"></script>
<script src="<?php echo $base_url; ?>core/bootstrap.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="<?php echo $base_url; ?>plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

<!-- Chart JS -->
<script src="<?php echo $base_url; ?>plugin/chart.js/chart.min.js"></script>

<!-- jQuery Sparkline -->
<script src="<?php echo $base_url; ?>plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Chart Circle -->
<script src="<?php echo $base_url; ?>plugin/chart-circle/circles.min.js"></script>


<!-- Bootstrap Notify -->
<script src="<?php echo $base_url; ?>plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- jQuery Vector Maps -->
<script src="<?php echo $base_url; ?>plugin/jsvectormap/jsvectormap.min.js"></script>
<script src="<?php echo $base_url; ?>plugin/jsvectormap/world.js"></script>

<!-- Sweet Alert -->
<script src="<?php echo $base_url; ?>plugin/sweetalert/sweetalert.min.js"></script>

<!-- Kaiadmin JS -->
<script src="<?php echo $base_url; ?>kaiadmin.min.js"></script>

<!-- DEMO FILEs TODO REMOVE -->
<script src="<?php echo $base_url; ?>setting-demo.js"></script>
<script src="<?php echo $base_url; ?>demo.js"></script>

<script>
  $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
    type: "line",
    height: "70",
    width: "100%",
    lineWidth: "2",
    lineColor: "#177dff",
    fillColor: "rgba(23, 125, 255, 0.14)",
  });

  $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
    type: "line",
    height: "70",
    width: "100%",
    lineWidth: "2",
    lineColor: "#f3545d",
    fillColor: "rgba(243, 84, 93, .14)",
  });

  $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
    type: "line",
    height: "70",
    width: "100%",
    lineWidth: "2",
    lineColor: "#ffa534",
    fillColor: "rgba(255, 165, 52, .14)",
  });
</script>