<?php
include('inc/JavaScriptUtils.php');

$jsu = new JavaScriptUtils();
header("Content-type: text/javascript");
include("./../../js/v1/button.js");
?>
<?php if ($jsu->displayAd()) { ?>
var spreadly_ad_height = <?php echo $jsu->getHeight(); ?>;var spreadly_ad_width = <?php echo $jsu->getWidth(); ?>; var spreadly_ad_mute = <?php echo $jsu->getMute(); ?>;
<?php include("./../../js/v1/advertisement.js"); ?>
<?php } ?>