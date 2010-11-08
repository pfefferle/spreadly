<?php
require_once('inc/config.inc.php');

session_set_cookie_params(time()+17776000, "/", LikeSettings::COOKIE_DOMAIN);
session_start();
header('P3P: CP="DSP LAW"');
header('Pragma: no-cache');
header('Cache-Control: private, no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

require_once('inc/WidgetUtils.php');
require_once('inc/i18n.php');
if (isset($_GET['url']) && !empty($_GET['url'])) {
  $pUrl = urldecode($_GET['url']);
} else {
  $pUrl = urldecode($_SERVER['HTTP_REFERER']);
}

if (isset($_GET['type']) && !empty($_GET['type'])) {
  $pType = $_GET['type'];
} else {
  $pType = "like";
}
if (isset($_GET['color']) && !empty($_GET['color'])) {
  $lFontcolor = urldecode($_GET['color']);
} else {
  $lFontcolor = "#000000";
}
if (isset($_GET['short']) && !empty($_GET['short'])) {
  $pFullShortVersion = true;
} else {
  $pFullShortVersion = false;
}
if (isset($_GET['social']) && !empty($_GET['social'])) {
  $pSocialFeatures = true;
} else {
  $pSocialFeatures = false;
}

$pTitle = urldecode($_GET['title']);
$pPhoto = urldecode($_GET['photo']);
$pDescription = urldecode($_GET['description']);

$lClickback = ClickBackHelper::extractClickback($pUrl, $_SERVER['HTTP_REFERER']);

$pUserId = MongoSessionPeer::extractUserIdFromSession(LikeSettings::SF_SESSION_COOKIE);
$lSocialObjectArray = SocialObjectPeer::getDataForUrl($pUrl);
$lIsUsed = YiidActivityObjectPeer::actionOnObjectByUser($lSocialObjectArray['_id'], $pUserId);
$lSocialObjectArray = SocialObjectPeer::recalculateCountsRespectingUser($lSocialObjectArray, $lIsUsed);

$lPopupUrl = LikeSettings::JS_POPUP_PATH."?ei_kcuf=".time();

$lShowFriends = false;
$lLimit = 5;
if($pUserId && $pSocialFeatures && $lSocialObjectArray['_id']) {
  $lShowFriends = true;
  $lLimit = $pFullShortVersion?5:7;
}

StatsHelper::trackPageImpression($pUrl, $lClickback, $pUserId);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Yiid it! Button</title>
<script type="text/javascript" src="/js/100_main/include/Like-20101029.min.js"></script>
<script type="text/javascript">
  <?php echo printI18nJSObject($pType); ?>

  YiidWidget.init("<?php echo urlencode($pUrl); ?>", "<?php echo $pType; ?>", "<?php echo urlencode($pTitle); ?>", "<?php echo urlencode($pDescription); ?>", "<?php echo urlencode($pPhoto); ?>");
  YiidWidget.aPopupPath = "<?php echo $lPopupUrl;  ?>";
  YiidRequest.aLikeAction = "<?php echo LikeSettings::JS_LIKE_PATH; ?>";
  YiidRequest.aDislikeAction = "<?php echo LikeSettings::JS_DISLIKE_PATH; ?>";
  YiidCookie.aDomain = "<?php echo LikeSettings::COOKIE_DOMAIN; ?>";
  <?php if ($lShowFriends) { ?>
    YiidFriends.aGetAction = "<?php echo LikeSettings::JS_GETFRIENDS_PATH; ?>";
    YiidFriends.init("<?php echo $lSocialObjectArray['_id'.""] ?>", "<?php echo $pUserId; ?>", "<?php echo $lLimit; ?>")
  <?php } ?>

</script>
<link rel="stylesheet" href="/css/widget/Button.css" type="text/css" media="screen, projection" />
</head>
<body>

  <div id="container">

    <div class="clearfix">

		  <?php if($lIsUsed === false) { ?>
		  <div id="container_like" class="left" <?php if($pUserId) { ?>onmouseover="YiidSlider.showClickElement();" onmouseout="YiidSlider.hideClickElement(event);"<?php } ?>>

	      <div class="light_bg_118 button_full_outer clearfix" id="normal_button">
	        <div id="service_area" class="left">
	          <div id="service_twitter_small_enabled" class="service_icon_small left"></div>
	          <div id="service_facebook_small_enabled" class="service_icon_small right"></div>
	          <div id="service_linkedin_small_enabled" class="service_icon_small left"></div>
	          <div id="service_google_small_enabled" class="service_icon_small right"></div>
	        </div>

	        <div class="hover_bg left" id="like_area">
	          <?php if($pFullShortVersion) { ?>
	            <a class="like_icon" title="<?php echo __("POS_BUTTON_TITLE", $pType); ?>" <?php if($pUserId) { ?>onclick="YiidWidget.doLike(1);return false;"<?php } else { ?>target="popup" onclick="return YiidUtils.openPopup('<?php echo $lPopupUrl; ?>', 1);return false;"<?php } ?>>&nbsp;</a>
	          <?php } else { ?>
	            <a class="like_text" title="<?php echo __("POS_BUTTON_TITLE", $pType); ?>" <?php if($pUserId) { ?>onclick="YiidWidget.doLike(1);return false;"<?php } else { ?>target="popup" onclick="return YiidUtils.openPopup('<?php echo $lPopupUrl; ?>', 1);return false;"<?php } ?>>
	              <?php echo __("POS_BUTTON_VALUE", $pType); ?><span class="like_icon">&nbsp;</span>
	            </a>
	          <?php } ?>
	        </div>

	        <div class="left <?php if($pUserId) { ?>hover_bg<?php } ?>" id="open_settings_icon_area" <?php if($pUserId) { ?>onclick="YiidSlider.slideIn(event); return false;"<?php } ?> style="display: none;">
	          <a id="slide_arrow_closed" class="open_settings_icon" title="<?php echo __("SETTINGS_TITLE"); ?>">&nbsp;</a>
	        </div>
	      </div>

	      <div id="settings_button" class="normal_button_area" style="display:none;">
          <div id="service_area" class="left">
            <div id="service_twitter_small_enabled" class="service_icon_small left"></div>
            <div id="service_facebook_small_enabled" class="service_icon_small right"></div>
            <div id="service_linkedin_small_enabled" class="service_icon_small left"></div>
            <div id="service_google_small_enabled" class="service_icon_small right"></div>
          </div>
	        <p class="left <?php echo (!$pFullShortVersion ? 'normal_space' : 'small_space') ?>" onclick="YiidSlider.slideOut(event);" title="<?php echo __("SETTINGS_TITLE"); ?>">
	          <?php if(!$pFullShortVersion) { ?><?php echo __("SETTINGS_VALUE"); ?><?php } ?>
	        </p>
	      </div>

	      <!-- Area to be slided -->
	      <div id="sliding_area" style="display:none;">
	        <div id="slide-box">
	          <table id="slide_table" cellpadding="0" cellspacing="0" class="left">
	            <tr id="slide-row">
	              <td id="settings-area" target="popup" title="Einstellungen" onclick="return YiidUtils.openPopup('<?php echo $lPopupUrl; ?>');"><a href="#100" class="settings_icon" id="settings">&nbsp;</a></td>
	            </tr>
	          </table>
	          <div class="hover_bg clearfix right" id="close_settings_icon_area" onclick="YiidSlider.slideOut(event); return false;">
	            <a id="slide_arrow_opened" class="close_settings_icon" title="<?php echo __("SETTINGS_TITLE"); ?>">&nbsp;</a>
	          </div>
	        </div>
	      </div>
	      <!-- Area to be slided -->

      </div>

	  <?php } ?>

	  <div id="container_like_used" class="left" <?php if($lIsUsed === false) { ?>style="display: none;"<?php } ?>>
	    <div id="used_button" class="light_bg_118 normal_button_area <?php echo (!$pFullShortVersion ? 'normal_space' : 'small_space') ?>_used" target="popup" onclick="return YiidUtils.openPopup('<?php echo $lPopupUrl; ?>');">

        <div id="service_area" class="left">
          <div id="service_twitter_small_enabled" class="service_icon_small left"></div>
          <div id="service_facebook_small_enabled" class="service_icon_small right"></div>
          <div id="service_linkedin_small_enabled" class="service_icon_small left"></div>
          <div id="service_google_small_enabled" class="service_icon_small right"></div>
        </div>

	      <?php if(is_numeric($lIsUsed)) { ?>

	        <?php if($pFullShortVersion) { ?>
	          <p class="like_icon left" id="liked-text" title="<?php echo __('POS_BUTTON_ACTION_VALUE', $pType); ?>">&nbsp;</p>
	        <?php } else { ?>
	          <p id="liked-text" class="left"><?php echo __('POS_BUTTON_ACTION_VALUE', $pType); ?></p>
	        <?php } ?>

	      <?php } else { ?>

	        <?php if($pFullShortVersion) { ?>
	          <p class="like_icon left" id="liked-text" title="<?php echo __('POS_BUTTON_ACTION_VALUE', $pType); ?>" style="display:none;">&nbsp;</p>
	        <?php } else { ?>
	          <p id="liked-text" class="left" style="display:none;"><?php echo __('POS_BUTTON_ACTION_VALUE', $pType); ?></p>
	        <?php } ?>

	      <?php } ?>
	    </div>
	  </div>

	  <!-- Text information -->
	  <div id="additional_text_area_like" class="left big_space_to_left" style="color: <?php echo $lFontcolor; ?>">
	    <?php if($lSocialObjectArray['urlerror']) { ?>
	      <p id="error-area" style="color: red; font-weight: bold; font-size: 11px;">INVALID URL: URL param must be valid or empty</p>
	    <?php } else { ?>
	      <p id="info-liked">
	        <span id="you-like" <?php if($lIsUsed != 1) { ?>style="display: none;" <?php } ?>><?php echo __('POS_TEXT_VALUE_1', $pType); ?></span>
	        <span class="counter"><?php echo $lSocialObjectArray['l_cnt']; ?></span><?php echo __('POS_TEXT_VALUE_2', $pType); ?>
	      </p>
	    <?php } ?>
	  </div>
	  <!-- /Text information -->
	</div>

  <?php if($lShowFriends) { ?>
    <div id="friends" class="clearfix"></div>
  <?php } else { ?>
    <?php if($pSocialFeatures) { ?>
      <div id="friends_description">
        <p title="<?php echo __('FRIENDS_DESCRIPTION_TITLE'); ?>">
          <?php echo __('FRIENDS_DESCRIPTION_VALUE'); ?>
        </p>
      </div>
    <?php } ?>
  <?php } ?>

</div>

</body>
</html>
