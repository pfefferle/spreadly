<div class="whtboxtopwide" id="signin-head">
	<div class="rcor">
	  <?php if ($has_credentials == true) { ?>
		  <h1 class="signin-title"><?php echo __('Please choose your favorite service for sharing'); ?><br/><em><?php echo __('You can add additional services anytime later'); ?></em></h1>
		<?php } else { ?>
		  <h1 class="signin-title"><?php echo __('Please connect with your facebook-profile'); ?><br/><em><?php echo __('You can add additional services anytime later'); ?></em></h1>
    <?php } ?>
  </div>
</div>
<div class="wht-contentbox clearfix">
	<div class="whtboxpad clearfix">
		  <ul id="logos_big" class="normal_list small_size important clearfix">
		    <li id="facebook_area"><a id="facebook_logo" class="service_icon" href="<?php echo url_for("@signinto?service=facebook", true); ?>">Facebook</a></li>
		    <?php if ($has_credentials == true) { ?>
		    <li id="twitter_area"><a id="twitter_logo" class="service_icon" href="<?php echo url_for("@signinto?service=twitter", true); ?>">Twitter</a></li>
		    <li id="linkedin_area"><a id="linkedin_logo" class="service_icon" href="<?php echo url_for("@signinto?service=linkedin", true); ?>">Linkedin</a></li>
		    <li id="google_area"><a id="google_logo" class="service_icon" href="<?php echo url_for("@signinto?service=google", true); ?>">Google Buzz</a></li>
		    <?php } ?>
		  </ul>
  </div>
</div>
<div class="whtboxbot"><span></span></div>