<?php use_helper('YiidUrl');?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <link rel="shortcut icon" href="/img/favicon.ico" />
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>

		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link rel="stylesheet" media="all" href="" />
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<!-- Adding "maximum-scale=1" fixes the Mobile Safari auto-zoom bug: http://filamentgroup.com/examples/iosScaleBug/ -->

		<link rel="stylesheet" type="text/css" media="screen" href="/css/engineroom/admin-dash.css" />
		<script type="text/javascript" src="/js/100_main/include/backend-<?php echo sfConfig::get('app_release_name') ?>.min.js"></script>
    <?php include_javascripts() ?>
  </head>
  <body>
      <div id="header" class="clearfix">
        <?php echo image_tag('/img/dashboard_logo') ?>
        <ul class="clearfix">
          <li class="<?php echo ($sf_request->getParameter('range')=='today' || !$sf_request->getParameter('range')) ? 'active' : '' ?>"><?php echo link_to('Today', 'dashboard/index?range=today') ?></li>
          <li class="<?php echo $sf_params->get('range')=='yesterday' ? 'active' : '' ?>"><?php echo link_to('Yesterday', 'dashboard/index?range=yesterday') ?></li>
          <li class="<?php echo $sf_params->get('range')=='last7d' ? 'active' : '' ?>"><?php echo link_to('Last 7d', 'dashboard/index?range=last7d') ?></li>
          <li class="<?php echo $sf_params->get('range')=='last30d' ? 'active' : '' ?>"><?php echo link_to('Last 30d', 'dashboard/index?range=last30d') ?></li>
          <li><?php echo link_to('<span style="font-size: 18px;float:left; line-height: 11px; padding-right: 5px;font-weight: normal;">&#8634;</span>Back', '/backend.php') ?></li>
        </ul>
      </div>
    	<?php echo $sf_content; ?>
		<script type="text/javascript">
			// Custom Checkbox Function
	    jQuery(document).ready( function() {

	    });
	  </script>
  </body>
</html>