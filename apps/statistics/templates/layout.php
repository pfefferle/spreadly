<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#">
  <head>
    <title><?php echo __('Spread.ly - first choice for social sharing'); ?></title>
    <?php include_metas() ?>
    <?php include_http_metas() ?>

    <meta property="og:site_name" content="Spread.ly" />
    <meta property="og:image" content="<?php echo image_path("/img/spreadlyicon.jpg", true); ?>" />
    <meta property="og:url" content="http://spreadly.com" />
    <meta property="og:type" content="website" />
    <meta property="og:email" content="info@spreadly.com" />
    <meta property="og:phone_number" content="+49-6201-845200" />

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="/css/styles.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="/css/yiid-styles.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="/css/print.css" media="print" />
		<link rel="stylesheet" type="text/css" href="/css/colorbox/colorbox.css" media="screen" />
		<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>

		<!--[if IE]>
		<link rel="stylesheet" type="text/css" href="/css/ie.css" />
		<![endif]-->
		<!--[if IE 6]>
		<script src="/js/DD_belatedPNG.js"> </script>
		<script>
		  DD_belatedPNG.fix('*');
		</script>
		<![endif]-->
		<?php if ($sf_params->get('resize') == true) { ?>
		<script>
      window.resizeTo(580,500);
    </script>
    <?php } ?>
    <script type="text/javascript" src="/js/100_main/include/statistics-<?php echo sfConfig::get('app_release_name') ?>.js"></script>

    <?php include_javascripts();?>
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
<body class="innerpage">
	<div id="wrapper">
		<!--feedback box start-->
		<div class="feedback"><a href="http://getsatisfaction.com/spreadly" target="_blank"><img src="/img/feedback.png" alt="Feedback" title="Feedback" width="155" height="137" /></a></div>
    <!--feedback box end-->
    <div id="header">
    	<div class="clearfix">
      	<div id="logo-spread" class="alignleft"><a href="<?php echo url_for("@homepage"); ?>"><img src="/img/spread-logo-domain.png" width="306" height="128" alt="" /></a></div>
        <span class="alignleft easy-tagline"><?php echo __("first choice for social sharing"); ?></span></div>
			</div>
			<!--Content start here -->
			<div id="content" class="clearfix">
      	<!--Top Navigation box start -->
        <div class="topnavblock">
        	<?php include_partial('global/navigation'); ?>
				</div>
        <!--Top Navigation box end -->
        <?php echo $sf_content; ?>
  		</div>

      <!--Content end here -->
			<div id="footer" class="nobg">
   			<span>Copyright Spread.ly 2011</span> | <?php echo link_to(__("Imprint"), "@imprint") ?> |  <?php echo link_to(__("TOS"), "system/tos") ?> | <?php echo link_to(__("Privacy Policy"), "system/privacy") ?> | <?php echo link_to(__("Blog"), "http://blog.spreadly.com/", array("target" => "_blank")) ?> | <a href="mailto:info@spreadly.com">info@spreadly.com</a></div>
			</div>
		<script type="text/javascript">
			// Custom Checkbox Function
	    jQuery(document).ready( function() {
	      <?php if(has_component_slot('no_domain_user')) {?>
    		<?php include_component_slot('no_domain_user'); ?>
    	<?php } ?>
	      <?php include_partial('global/js_init_general.js'); ?>
	      <?php if (has_slot('js_document_ready')) { ?>
	        <?php include_slot('js_document_ready'); ?>
	      <?php } ?>
	    });

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-21966328-1']);
      _gaq.push(['_setDomainName', '.spreadly.com']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
		<img id="general-ajax-loader" style="display:none;" src="/img/global/ajax-loader-bar-circle.gif" />
	</body>
</html>