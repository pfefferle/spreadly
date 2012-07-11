	      <div id="like-response" class="error">
				<?php if(isset($pError)) {?>
	        	<?php echo __($pError); ?>
        <?php } ?>
        </div>
        <?php if (!$sf_user->isAuthenticated() ) { ?>
          <h4><?php echo __('Please choose your favorite service for sharing.'); ?><br/><?php echo __('You can add additional services anytime later.'); ?></h4>
        <?php } ?>
        <?php if ($sf_user->isAuthenticated() ) { ?>
        <ul class="clearfix" id="like-oi-list">
          <?php foreach($pIdentities as $lIdentity) {?>
            <?php include_partial("like/identity_selector", array("identity" => $lIdentity, "domain_profile" => $domain_profile)); ?>
          <?php } ?>
            <li class="B">
              <a href="#">+add</a>
              <ul class="services">
                <li><a href="#" onclick="window.open('<?php echo url_for("@signinto?service=twitter"); ?>', 'auth_popup', 'width=580,height=450,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;"><?php echo __("another twitter-account"); ?></a></li>
                <li><a href="#" onclick="window.open('<?php echo url_for("@signinto?service=facebook"); ?>', 'auth_popup', 'width=580,height=450,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;"><?php echo __("another facebook-account"); ?></a></li>
                <li><a href="#" onclick="window.open('<?php echo url_for("@signinto?service=linkedin"); ?>', 'auth_popup', 'width=580,height=450,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;"><?php echo __("another linkedin-account"); ?></a></li>
                <li><a href="#" onclick="window.open('<?php echo url_for("@signinto?service=tumblr"); ?>', 'auth_popup', 'width=580,height=450,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;"><?php echo __("another tumblr-account"); ?></a></li>
                <li><a href="#" onclick="window.open('<?php echo url_for("@signinto?service=xing"); ?>', 'auth_popup', 'width=580,height=550,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;"><?php echo __("another xing-account"); ?></a></li>
                <li><a href="#" onclick="window.open('<?php echo url_for("@signinto?service=flattr"); ?>', 'auth_popup', 'width=580,height=550,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;"><?php echo __("another flattr-account"); ?></a></li>
              </ul>
            </li>
        </ul>
        <?php } else { ?>
        <ul class="clearfix" id="like-oi-list">
          <li class="B" id="o1" onclick="WidgetLikeForm.beforeSend(); window.open('<?php echo url_for("@signinto?service=twitter"); ?>', 'auth_popup', 'width=580,height=450,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;">
            <input class="add-service-checkbox" type="checkbox" name="twitter" value="twitter" />
            <label for="o1"><?php echo link_to(image_tag("/img/twitter-favicon.gif", array("alt" => 'Twitter', "title" => 'Twitter')), "@signinto?service=twitter"); ?></label>
          </li>
          <li class="B" id="o2" onclick="WidgetLikeForm.beforeSend();window.open('<?php echo url_for("@signinto?service=facebook"); ?>', 'auth_popup', 'width=580,height=450,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;">
            <input class="add-service-checkbox" type="checkbox" name="facebook" value="facebook" />
            <label for="o2"><?php echo link_to(image_tag("/img/facebook-favicon.gif", array("alt" => 'facebook', "title" => 'facebook')), "@signinto?service=facebook"); ?></label>
          </li>
          <li class="B" id="o3" onclick="WidgetLikeForm.beforeSend();window.open('<?php echo url_for("@signinto?service=linkedin"); ?>', 'auth_popup', 'width=580,height=450,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;">
            <input class="add-service-checkbox" type="checkbox" name="linkedin" value="linkedin" />
            <label for="o3"><?php echo link_to(image_tag("/img/linkedin-favicon.gif", array("alt" => 'Linkedin', "title" => 'Linkedin')), "@signinto?service=linkedin"); ?></label>
          </li>
          <li class="B" id="o3" onclick="WidgetLikeForm.beforeSend();window.open('<?php echo url_for("@signinto?service=xing"); ?>', 'auth_popup', 'width=580,height=550,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;">
            <input class="add-service-checkbox" type="checkbox" name="linkedin" value="xing" />
            <label for="o3"><?php echo link_to(image_tag("/img/xing-favicon.gif", array("alt" => 'Xing', "title" => 'Xing')), "@signinto?service=xing"); ?></label>
          </li>
          <li class="B" id="o3" onclick="WidgetLikeForm.beforeSend();window.open('<?php echo url_for("@signinto?service=flattr"); ?>', 'auth_popup', 'width=580,height=550,scrollbars=no,toolbar=no,status=no,resizable=no,menubar=no,location=0,directories=no,top=150,left=150'); return false;">
            <input class="add-service-checkbox" type="checkbox" name="linkedin" value="flattr" />
            <label for="o3"><?php echo link_to(image_tag("/img/flattr-favicon.gif", array("alt" => 'flattr', "title" => 'flattr')), "@signinto?service=flattr"); ?></label>
          </li>
        </ul>
        <?php } ?>
        <a class="send B" id="popup-like-button" href="#"><?php echo __('Continue and Share'); ?></a>