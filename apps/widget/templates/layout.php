<?php use_helper('YiidUrl', 'Avatar', 'Text') ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php echo __('Spread.ly - first choice for social sharing'); ?></title>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<link rel="stylesheet" type="text/css" href="/css/styles.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="/css/yiid-styles.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="/css/print.css" media="print" />
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
      window.resizeTo(580,600);
    </script>
    <?php } ?>
    <script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
    <script type="text/javascript" src="/js/100_main/include/widget-<?php echo sfConfig::get('app_release_name') ?>.js"></script>

    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body class="nobg">
		<div class="popupblock">
			<div class="sharepic">
        <?php echo image_tag("/img/share-pic.png", array("alt"=>"Share", "title"=>"Share", "width"=>"118", "height"=>"135")); ?>
			</div>
    	<div class="pop-header clearfix">
    		<a href="#" class="alignleft spreadlogo">
    		  <?php echo image_tag("/img/spread-logo.png", array("alt"=>"Spread.ly", "title"=>"Spread.ly", "width"=>"211", "height"=>"88")); ?>
    		</a>
    		<div class="popup-tagline"><?php echo __("first choice for social sharing"); ?></div>
  		</div>

      <!--Pop Navigation start -->
      <?php if ($sf_user->isAuthenticated() ) { ?>
      <div class="popnav">
      	<div class="popnavbg clearfix">
          <ul class="popnavigation alignleft">
          	<!-- li <?php if($sf_context->getModuleName()=='profile') { echo 'class="active"';} ?>><?php echo link_to(__('Profile'), '@widget_profile'); ?> <span></span></li -->
            <li <?php if ($sf_context->getModuleName()=='likes') { echo 'class="active"'; } ?>><?php echo link_to(__('Likes'), '@widget_likes'); ?><span></span></li>
            <li <?php if ($sf_context->getModuleName()=='deals') { echo 'class="active"'; } ?>><?php echo link_to(__('Deals'), '@widget_deals'); ?><span></span></li>
            <li class="last <?php if ($sf_context->getModuleName()=='settings') { echo 'active'; } ?>"><?php echo link_to(__('Settings'), '@widget_settings'); ?><span></span></li>
          </ul>
          <div class="profile-info spread_profile alignright" >
            <div class="alignleft spread <?php if ($sf_context->getModuleName()=='like') { echo 'active'; } ?>"><?php echo link_to(image_tag("/img/spread-it-label.png"), '@widget_like'); ?><?php if ($sf_context->getModuleName()=='like') { echo '<span></span>'; } ?></div>
            <span class="alignleft"><img src="<?php echo avatar_path($sf_user->getUser()->getAvatar(), '25'); ?>" width="25" height="25" alt="<?php echo $sf_user->getUser()->getUsername(); ?>" title="<?php echo $sf_user->getUser()->getUsername(); ?>" class="mic-pic" />Hi <?php echo truncate_text($sf_user->getUser()->getUsername(), 10); ?>,</span>
            <span class="alignleft likebtn"><?php echo image_tag("/img/like-icon.png", array("alt"=>"Like", "title"=>"Like")) ?></span>
            <span class="comment_label alignleft"><?php echo $sf_user->getUser()->getLikeCount(); ?></span><?php echo link_to(__('Logout'), '@signout');?>
          </div>
        </div>
      </div>
      <?php } ?>
      <!-- Pop Navigation end -->
      <div>
      	<div class="grboxtop">
      		<span></span>
      	</div>
        <div class="grboxmid">
        	<div class="grboxmid-content">
						<!-- start content -->
								<div class="graybox clearfix">
	          <?php echo $sf_content; ?>
                <!-- footer -->
                <div class="poweredrow">
                	<?php echo link_to(__("Imprint"), "http://spreadly.com/imprint", array("target" => "_blank")); ?> | <?php echo link_to("Powered by ".image_tag("/img/spread-logosmall.png", array("alt" => "spread.ly icon")), sfConfig::get("app_settings_url"), array("title" => "spread.ly", "target" => "_blank")) ?></div>
                </div>
              </div>
            </div>
          <div class="grboxbot"><span></span></div>
        </div>
    	</div>
    <!--popup block end -->
		<img id="general-ajax-loader" style="display:none;" src="/img/global/ajax-loader-bar-circle.gif" />
	  <script  type="text/javascript">
	    jQuery(document).ready( function() {
	      <?php
	        if (has_slot('js_document_ready')) {
	          include_slot('js_document_ready');
	        }
	      ?>
	    });

      var _sf_async_config={uid:23222,domain:"spread.ly"};
      (function(){
        function loadChartbeat() {
          window._sf_endpt=(new Date()).getTime();
          var e = document.createElement('script');
          e.setAttribute('language', 'javascript');
          e.setAttribute('type', 'text/javascript');
          e.setAttribute('src',
             (("https:" == document.location.protocol) ? "https://a248.e.akamai.net/chartbeat.download.akamai.com/102508/" : "http://static.chartbeat.com/") +
             "js/chartbeat.js");
          document.body.appendChild(e);
        }
        var oldonload = window.onload;
        window.onload = (typeof window.onload != 'function') ?
           loadChartbeat : function() { oldonload(); loadChartbeat(); };
      })();
	  </script>
  </body>
</html>
