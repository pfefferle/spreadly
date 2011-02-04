            	<div class="whtboxtopwide">
            		<div class="rcor">
              		<ul class="clearfix profileinfo">
                		<li class="prothumb">
                			<img src="/img/macrco-thumb.jpg" width="64" height="64" alt="Marco Ripanti" title="Marco Ripanti" />
                		</li>
                  	<li class="pronameblock">
                  		<h1><?php echo $pUser->getUsername(); ?></h1><?php echo $pUser->getDescription(); ?>
                  		<div class="scicon">
                  		<?php foreach ($pIdentities as $lIdentity) { ?>
                  			<a href="<?php echo url_for($lIdentity->getProfileUri()) ?>" target="_blank">
                  				<img src="/img/<?php echo $lIdentity->getCommunity()->getCommunity(); ?>-favicon.gif" width="17" height="17" alt="<?php echo $lIdentity->getCommunity()->getCommunity(); ?>" title="<?php echo $lIdentity->getCommunity()->getCommunity(); ?>" />
                  			</a>
                  		<?php } ?>
                  		</div>
										</li>
										<li class="friends-box">
											<div class="totalfriend">
												<span>12415</span><br /><?php echo __('friends'); ?>
											</div>
											<?php echo __('Influencer'); ?>
										</li>
                  </ul>
                </div>
              </div>