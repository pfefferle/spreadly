WidgetLikeForm.send();
LikeImage.init(<?php echo $pImgCount; ?>, "<?php echo $pUrl; ?>");

jQuery('#area-like-comment').charcounter();

i18n.init({
	"like_success_message": "<?php echo __("Like successfully sent!");?>",
	"like_error_message": "<?php echo __("Something went wrong! Check your selected services and try again!");?>",
	"close_popup": "<?php echo __("(Close window)");?>"
});

LikeIdentity.init();
