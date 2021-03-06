		<div class="contentbox">
			<div class="grboxtop"><span></span></div>
			<div class="grboxmid midcontent">
				<div class="grboxmid-content">
					<div class="graybox clearfix">
						<div>
							<div class="alignright help_content step2_content">
								<h3 class="title"><?php echo __("Hilfe"); ?></h3>
								<p><?php echo __("Bitte geben Sie die genaue Webadresse an, auf der der Button laufen soll und wählen Sie das gewünschte Erscheinungsbild des Buttons aus. Der Button erscheint dann je nach Wahl mit oder ohne Photo."); ?></p><br />
                <p><?php echo __('Ihr individueller Code erscheint nun im linken grauen Feld. Klicken Sie auf "Code kopieren", um den Code für Ihren Spread.ly-Button direkt kopieren und an beliebiger Stelle einfügen zu können.'); ?></p><br/>
                <p><?php echo __("For more information read our %doku%", array('%doku%' => link_to('developer documentation', 'http://dev.spreadly.com'))); ?></p>
							</div>
							<div class="alignleft stylestatus_box">
								<h3 class="sub_title"><?php echo __('Step 2: Choose Site & Style'); ?></h3>
								<div class="innerloginbox">
									<form action="" name="likebutton-form" id="likebutton-form">
      						<?php if($pService) {?>
        						<input type="hidden" name="likebutton[service]" value="<?php echo $pService->getSlug(); ?>"/>
        					<?php } ?>

										<fieldset class="group">
										<?php if (!$pService || $pService->getSlug() == 'static') {?>
											<div class="clearfix">
												<label class="textfield">
												<span>
													<input type="text" class="wd260" name="likebutton[url]" id="likebutton_url" value="<?php echo __('Url of your site e.g. http://www.domain.com"'); ?>" />
												</span>
												</label>
											</div>
											<div class="meta-text"><?php echo __('URL-Beispiel: <strong>http://</strong>www.example.com'); ?></div>
										<?php } else { ?>
											<div class="clearfix">
												<label class="textfield"><span>
													<input type="text" class="wd260" name="service_no_url" id="service_no_url" value="<?php echo __('Your '.$pService->getName().' Permalink'); ?>" readonly/>
													</span>
												</label>
											</div>
										<?php } ?>
										<div class="stylechoose_box clearfix">
											<div class="group radiobtn_list alignleft">
												<?php if(!$pService || ($pService && !($pService->getSlug() == 'static'))) { ?>
													<label for="radio1">&nbsp;</label><input id="radio1" type="radio" name="likebutton[wt]" value="short" class="widget-radio" <?php if(!isset($pSocial) || $pSocial == 0){ ?>checked="checked"<?php } ?>/>
												<?php } ?>
												<?php if(!$pService || ($pService && ($pService->getSlug() != 'static'))) { ?>
													<label for="radio2">&nbsp;</label><input id="radio2" type="radio" name="likebutton[wt]" value="stand_social" class="widget-radio" <?php if((isset($pSocial) && $pSocial == 1) || ($pService && $pService->getSlug() == 'static')){ ?>checked="checked"<?php } ?>/>
												<?php } ?>
											</div>
											<div class="status_list alignleft" id="preview_widgets">
												<?php //include_partial('configurator/preview_widgets'); ?>
											</div>
										</div>
										</fieldset>
									</form>
								</div>
							</div>
							<div class="alignleft grabcode_box">
								<h3 class="sub_title"><?php echo __('Step 3: Grab Your Code'); ?></h3>
								<div class="textariabox">
									<div class="textaria_top"><span>&nbsp;</span></div>
									<div class="textaria_middle">
										<div class="textaria_right">
											<label class="textareablock">
										    <textarea rows="10" cols="10" id="your_code"><?php //include_partial('configurator/widget_like') ?></textarea>
											</label>
										</div>
									</div>
									<div class="textaria_bot"><span>&nbsp;</span></div>
									<div class="copycodebox clearfix"> <a href="#" title="<?php echo __("Code kopieren"); ?>" class="graybtn alignleft" id="d_clip_container" style="position: relative;"><span id="d_clip_button"><?php echo __("Code kopieren"); ?></span></a><?php if(!$sf_user->isAuthenticated()){?><span class="optionltext"><?php echo link_to(__("Optional: register  to get your statistics, it's free!"), '@sf_guard_signin'); ?></span><?php } ?> </div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="grboxbot"><span></span></div>
		</div>