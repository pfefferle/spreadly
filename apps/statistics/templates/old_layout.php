<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#">
  <head>
    <title><?php echo __('Spreadly - We monetize Social Sharing'); ?></title>
    <?php include_metas() ?>
    <?php include_http_metas() ?>

    <meta property="og:site_name" content="Spreadly" />
    <meta property="og:image" content="<?php echo image_path("/img/spreadlyicon.jpg", true); ?>" />
    <meta property="og:url" content="http://spreadly.com" />
    <meta property="og:type" content="website" />
    <meta property="og:email" content="info@spreadly.com" />
    <meta property="og:phone_number" content="+49-6201-845200" />
    <meta name="google-site-verification" content="0nH-7N_-Vix-3dGyWtqvJUn9IESoCtdi9wLN_Wp5MQs" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="/css/styles.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="/css/yiid-styles.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="/css/print.css" media="print" />
		<link rel="stylesheet" type="text/css" href="/css/colorbox/colorbox.css" media="screen" />
		<?php if($sf_context->getModuleName() == 'deals' && $sf_context->getActionName() == 'step_verify') { ?>
			<link rel="stylesheet" type="text/css" href="/css/coupon.css" media="screen" />
		<?php } ?>

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
    <script type="text/javascript" src="/js/100_main/include/statistics-<?php echo sfConfig::get('app_release_name') ?>.min.js"></script>
    <script type="text/javascript" src="/js/statistics/configurator/SchemePicker.js"></script>


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
        <span class="alignleft easy-tagline"><?php echo __("We monetize Social Sharing"); ?></span></div>
			</div>
			<!--Content start here -->
			<div id="content" class="clearfix">
      	<!--Top Navigation box start -->
        <div class="topnavblock">
        	<?php include_partial('global/navigation'); ?>
				</div>
        <!--Top Navigation box end -->
        <?php echo $sf_content; ?>

        <div class="selectionnone">
        <div class="grboxtop"><span></span></div>
        <div class="grboxmid">
          <div class="grboxmid-content">
            <div class="panel_width">
              <div class="graybox gallery_list">
                <h3 class="sub_title"><?php echo __("References"); ?></h3>
                <ul id="mycarousel" class="jcarousel-skin-tango">
                  <li><a href="http://www.mailingwork.de/" title="mailingwork" target="_blank"><img src="/img/refrance-img20.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.kerstin-hoffmann.de/pr-doktor/" title="PR-Doktor" target="_blank"><img src="/img/refrance-img19.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.pr-blogger.de/" title="PR-Blogger" target="_blank"><img src="/img/refrance-img18.jpg" alt="Image"/></a></li>
                  <li><a href="http://erdfisch.de/" title="erdfisch" target="_blank"><img src="/img/refrance-img17.jpg" alt="Image"/></a></li>
                  <li><a href="http://learning-factory.de" title="learning-factory" target="_blank"><img src="/img/refrance-img16.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.ibusiness.de/" title="iBusiness" target="_blank"><img src="/img/refrance-img15.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.price-look.de/" title="price-look.de" target="_blank"><img src="/img/refrance-img14.jpg" alt="Image"/></a></li>
                  <li><a href="http://lustich.de/" title="lustich.de" target="_blank"><img src="/img/refrance-img13.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.deutsche-startups.de/" title="deutsche-startups.de" target="_blank"><img src="/img/refrance-img12.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.frankentipps.de/" title="Frankentipps" target="_blank"><img src="/img/refrance-img11.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.pressehof.de/" title="Pressehof" target="_blank"><img src="/img/refrance-img10.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.abacho.de/" title="Abacho" target="_blank"><img src="/img/refrance-img9.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.business-on.de/" title="Business-on.de" target="_blank"><img src="/img/refrance-img6.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.yasni.de/" title="Yasni.de" target="_blank"><img src="/img/refrance-img7.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.ednetz.de/" title="ednetz.de" target="_blank"><img src="/img/refrance-img8.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.familienfreund.de/" title="familienfreund.de" target="_blank"><img src="/img/refrance-img1.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.deraktionaer.de/" title="Der Aktionär" target="_blank"><img src="/img/refrance-img2.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.marketing-boerse.de/" title="Marketing Börse" target="_blank"><img src="/img/refrance-img3.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.daf.fm/" title="Deutsches Anleger Fernsehen" target="_blank"><img src="/img/refrance-img4.jpg" alt="Image"/></a></li>
                  <li><a href="http://www.brille24.de/" title="Brille24" target="_blank"><img src="/img/refrance-img5.jpg" alt="Image"/></a></li>
                </ul>
              </div>
            </div>
            <div class="alignnone"></div>
          </div>
        </div>
        <div class="grboxbot"><span></span></div>
  		</div>

      <!--Content end here -->
      <?php include_partial('global/footer'); ?>

		<script type="text/javascript">
			// Custom Checkbox Function
	    jQuery(document).ready( function() {
	    	//jQuery(".tooltip").tooltip();
	      <?php if(has_component_slot('no_domain_user')) {?>
    		<?php include_component_slot('no_domain_user'); ?>
    	<?php } ?>
	      <?php include_partial('global/js_init_general.js'); ?>
	      <?php if (has_slot('js_document_ready')) { ?>
	        <?php include_slot('js_document_ready'); ?>
	      <?php } ?>
	    });

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-24814204-1']);
      _gaq.push(['_setDomainName', 'spreadly.com']);
      _gaq.push(['_setAllowHash', 'false']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
		<img id="general-ajax-loader" style="display:none;" src="/img/global/ajax-loader-bar-circle.gif" />
		</div>
	</body>
</html>