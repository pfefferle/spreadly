<?php use_helper('Text'); ?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo __('Spreadly - We monetize Social Sharing'); ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="Spreadly consists of sharing button widgets that enable users to share content into a wide range of social networks simultanously. Besides that, Spreadly offers a sophisticated functionality to reward users for their likes." />
    <meta name="keywords" content="sharing,sharebutton,like,likebutton,deal,dealbutton,facebook,linkedin,twitter,buzz" />
  	<link rel="shortcut icon" href="https://s3.amazonaws.com/spread.ly/img/favicon.ico" type="image/x-icon">
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="/js/100_main/include/widget-<?php echo sfConfig::get('app_release_name') ?>.js"></script>
    <script type="text/javascript">var _sf_startpt=(new Date()).getTime();</script>
    
    <script type="text/javascript" src="/js/main.js"></script>
    
    <link rel="stylesheet" type="text/css" href="/css/popup.css" />
    <!--[if lte IE 8]>
        <link rel="stylesheet" type="text/css" href="/css/popup_ie.css" />
    <![endif]-->
    <!--[if lte IE 9]>
      <script type="text/javascript" src="/js/labelify.ie.js"></script>
      <script type="text/javascript" src="/js/custom.ie.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="sharebg">
      <?php echo $sf_content; ?>
      <?php include_partial('global/footer'); ?>
    </div>
    
		<img id="general-ajax-loader" style="display: none;" src="/img/global/ajax-loader-bar-circle.gif" />
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
