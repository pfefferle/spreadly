<?php
	$lIsEdit = false;
	$lDeal = null;
	if($pForm->getEmbeddedForm('deal')->getObject()->getId()){
		$lIsEdit = true;
		$lDeal = $pForm->getEmbeddedForm('deal')->getObject();
	}

	if($pForm->isBound()){
		$lDealValues = $pForm->getTaintedValues();
		$lDefaultDeal = $lDealValues['deal'];
	} else {
		$lDefaultDeal =  $pForm->getEmbeddedForm('deal')->getDefaults();
	}
?>

<?php if($lIsEdit) { ?>
	<div class="content-header-box">
		<div class="content-box-head">
			<h3><?php echo __('+ Editing an Ad')?></h3>
	  </div>
	  <div class="content-box-body">
	  	<?php echo __('Button wording, deal description, scheduling and quantity fields are pre-populated with your existing deal settings. Edits you make here will replace your existing deal. Once you submit your changes, your deal will stop running until it has been approved by our team.'); ?>
	  </div>
	</div>
<?php } ?>

<form action="<?php echo url_for('deals/save'); ?>" method="post" id="deal_form" name="deal_form">
	<?php echo $pForm['deal']['id']->render();?>
	<?php echo $pForm['_csrf_token']->render(); ?>
  <div id="create-deal-content">

<!-- ********** Form head ********** -->
		<div class="content-header-box" id="creat-deal-box">
		  <div class="content-box-head">
		  	<?php if($lIsEdit) { ?>
		    	<h3><?php echo __('+ Edit Deal')?></h3>
		    <?php } else { ?>
		    	<h3><?php echo __('+ Create New Deal')?></h3>
		    <?php } ?>
		  </div>
		  <div class="content-box-body" id="claiming-profile-content">
  			<?php echo $pForm->renderGlobalErrors();?>

<!-- ********** Select domain area ********** -->
  			<div class="left" id="deal-teaser-img">
    			<?php echo image_tag('/img/global/42x42/promotion.png'); ?>
  			</div>
  			<div class="clearfix" id="deal-domain-select-box">
					<?php echo $pForm['id']->render();?><br />
					<?php echo __('Push your content into social networks through recommendations. Only one deal per domain at a time. Please allow 24 hours for reviewing new or changed deals. '); ?>
  			</div>

<!-- ********** Create button area ********** -->
  			<div class="content-box bg-white">
	  			<h2><?php echo __('Step 1: Create your Button')?></h2>
	  			<div class="left form-row">
	  				<div class="label-box">
		    			<?php echo $pForm['deal']['button_wording']->renderLabel();?>
		    			<div class="meta-label"><?php echo __('<span id="button_wording_counter">35</span> characters left'); ?></div>
	    				<?php echo $pForm['deal']['button_wording']->renderError();?>
		  			</div>
	    			<?php echo $pForm['deal']['button_wording']->render(array('class' => 'mirror-value'));?>
	  			</div>
	  			<div class="clearfix preview-deal-box" id="preview-deal-button">
      			<img src="/img/global/yiid-btn-like-en.png" class="left"/>
      			<div class="deal_button_wording-mirror">
      				dat wat im button zu stehn hat
      			</div>
	  			</div>
  			</div>

<!-- ********** Configure coupon area ********** -->
  			<div class="content-box bg-white">
    			<h2><?php echo __('Step 2: Configure your Coupon')?></h2>
    			<div class="left">
    				<div class="form-row">
	  					<div class="label-box">
								<?php echo $pForm['deal']['summary']->renderLabel();?>
								<div class="meta-label"><?php echo __('<span id="summary_counter">40</span> characters left'); ?></div>
								<?php echo $pForm['deal']['summary']->renderError();?>
							</div>
							<?php echo $pForm['deal']['summary']->render(array('class' => 'mirror-value'));?>
						</div>
						<div class="form-row">
							<div class="label-box">
								<?php echo $pForm['deal']['description']->renderLabel();?>
								<div class="meta-label"><?php echo __('<span id="description_counter">80</span> characters left'); ?></div>
								<?php echo $pForm['deal']['description']->renderError();?>
							</div>
							<?php echo $pForm['deal']['description']->render(array('class' => 'mirror-value'));?>
						</div>
						<div class="form-row">
							<div class="label-box">
								<?php echo $pForm['deal']['start_date']->renderLabel();?>
								<?php echo $pForm['deal']['end_date']->renderError();?>
								<?php echo $pForm['deal']['start_date']->renderError();?>
							</div>
							<?php echo $pForm['deal']['start_date']->render();?> -
							<?php echo $pForm['deal']['end_date']->render();?>
						</div>
						<div class="form-row">
							<div class="label-box">
      					<?php echo $pForm['deal']['terms_of_deal']->renderLabel();?>
      					<?php echo $pForm['deal']['terms_of_deal']->renderError();?>
      				</div>
      				<?php echo $pForm['deal']['terms_of_deal']->render();?>
      			</div>
						<div class="form-row">
							<div class="label-box">
      					<?php echo $pForm['imprint_url']->renderLabel();?>
      					<?php echo $pForm['imprint_url']->renderError();?>
							</div>
      				<?php echo $pForm['imprint_url']->render();?><br />
      			</div>
    			</div>

					<!-- ********** Preview Coupon ********** -->
    			<div class="clearfix preview-deal-box">
        		<div class="content-box">
        			<div class="content-box yellow">
          			<div class="coupon-summary deal_summary-mirror"><?php echo $lDefaultDeal['summary']; ?></div>
          			<div class="coupon-description deal_description-mirror"><?php echo $lDefaultDeal['description']; ?></div>
          			<div class="coupon-foot"><?php echo __('Expires at'); ?> <span id="deal_end_date-mirror"><?php echo $lDefaultDeal['end_date']; ?></span></div>
          		</div>
          		<div class="meta-label" id="accept-tod">
          			<input type="checkbox" name="coupon-accept-tod" /><?php echo __('I accept the %terms%', array('%terms%' => link_to(__('Terms of Deal'), '/'))); ?>
          		</div>
          		<img src="/img/global/yiid-btn-like-en.png"/>
          		<div style="text-align: right;"><?php echo __('Imprint'); ?></div>
        		</div>
    			</div>
  			</div>

<!-- ********** Configure success coupon area ********** -->
	  		<div class="content-box bg-white">
	    		<h2><?php echo __('Step 3: Configure your Coupon after a Like')?></h2>
	    		<div class="left">
	    			<div class="form-row">
	    				<?php if($lIsEdit) { ?>
	    					<div class="label-box">
		    					<?php echo __('Coupon-Codes');?>
								</div>
								<?php echo __('You have chosen %codetype% for all deals:', array('%codetype%' => $pCouponType)); ?> <strong><?php echo $pDefaultCode; ?></strong>
								<input type="hidden" name="deal[coupon_type]" value="<?php echo $pCouponType;?>" />
							<?php } else {?>
		    				<div class="label-box">
		    					<?php echo $pForm['deal']['coupon_type']->renderLabel();?>
									<?php echo $pForm['deal']['coupon_type']->renderError();?>
								</div>
								<?php echo $pForm['deal']['coupon_type']->render();?>
							<?php } ?>
						</div>

						<!-- ********** Configure single code ********** -->
						<div id="single-code-row" <?php echo ($pCouponType == 'multiple')? 'style="display:none;"': ''; ?>>
							<?php if($lIsEdit) { ?>
								<input type="hidden" name="deal[coupon][single_code]" value="<?php echo $pDefaultCode; ?>" />
				    		<div class="form-row clearfix">
				    			<div class="label-box">
				    				<?php echo $pForm['deal']['coupon_quantity']->renderLabel();?>
				    				<?php if(!$lDeal->isUnlimited()) {?>
		          				<div class="meta-label">
		          					<?php echo __('Empty or 0 is same as unlimeted');?>
		          				</div>
		          			<?php } ?>
										<?php echo $pForm['deal']['coupon_quantity']->renderError();?>
									</div>
									<?php if($lDeal->isUnlimited()) {?>
										<div class="inline-row" id="edit-quantity">
											<?php echo __('Your coupon quantity is unlimited'); ?>
										</div>
									<?php } else { ?>
										<div class="inline-row" id="edit-quantity">
											<div class="label-box">
												<?php echo __('Will end after');?>
											</div>
											<?php echo $pForm['deal']['coupon_quantity']->render();?>
											<?php echo __('likes'); ?>
										</div>
									<?php } ?>
								</div>
							<?php } else {?>
				    		<div class="form-row" id="single-code-row">
				    			<div class="label-box">
				    				<?php echo $pForm['deal']['coupon']['single_code']->renderLabel();?>
										<?php echo $pForm['deal']['coupon']['single_code']->renderError();?>
									</div>
									<?php echo $pForm['deal']['coupon']['single_code']->render(array('class' => 'mirror-value'));?>
								</div>
				    		<div class="form-row clearfix">
				    			<div class="label-box">
				    				<?php echo $pForm['deal']['coupon_quantity']->renderLabel();?>
	          				<div class="meta-label">
	          					<?php echo __('Empty or 0 is same as unlimeted');?>
	          				</div>
										<?php echo $pForm['deal']['coupon_quantity']->renderError();?>
									</div>
									<div class="inline-row" id="edit-quantity">
										<div class="label-box">
											<input type="radio" name="single-quantity" id="radio-single-quantity" <?php echo ($pCouponQuantity > 0)? 'checked="checked"':''; ?> />
											<?php echo __('Will end after');?>
										</div>
										<?php echo $pForm['deal']['coupon_quantity']->render();?>
										<?php echo __('likes'); ?>
									</div>
									<div class="inline-row" id="single-quantity-unlimited">
										<input type="radio" name="single-quantity" id="radio-single-quantity-unltd" <?php echo ($pCouponQuantity == 0)? 'checked="checked"':''; ?> />
										<?php echo __('unlimited'); ?>
									</div>
								</div>
							<?php } ?>
						</div>

						<!-- ********** Configure multiple codes ********** -->
						<div id="multiple-code-row" <?php echo ($pCouponType=='single')? 'style="display:none;"': ''; ?>>
		    			<div class="form-row">
		    				<div class="label-box">
		    					<?php echo $pForm['deal']['coupon']['multiple_codes']->renderLabel();?>
		    					<div class="meta-label"><?php echo __('Paste codes coma-separated or one code per line'); ?></div>
									<?php echo $pForm['deal']['coupon']['multiple_codes']->renderError();?>
								</div>
								<?php echo $pForm['deal']['coupon']['multiple_codes']->render();?>
							</div>
		    			<div class="form-row">
		    				<div class="label-box">
		    					<?php echo $pForm['deal']['coupon_quantity']->renderLabel();?>
								</div>
								<?php if($lIsEdit) { ?>
									<?php echo __('Will end after <span id="code-counter">%codecounter%</span> likes.', array('%codecounter%' => $lDeal->getCouponQuantity())); ?>
									<?php echo __('%oldcodes% remaining old coupon codes', array('%oldcodes%' => $lDeal->getCouponQuantity()-$lDeal->getClaimedQuantity())); ?>
								<?php } else { ?>
									<?php echo __('Will end after <span id="code-counter">%codecounter%</span> likes.', array('%codecounter%' => '0')); ?>
								<?php } ?>
							</div>
			 			</div>

						<!-- ********** Redeem url ********** -->
						<div class="form-row">
	    				<div class="label-box">
								<?php echo $pForm['deal']['redeem_url']->renderLabel();?>
								<?php echo $pForm['deal']['redeem_url']->renderError();?>
							</div>
							<?php echo $pForm['deal']['redeem_url']->render(array('class' => 'mirror-value'));?>
						</div>
	    		</div>

	    		<!-- ********** Preview success coupon ********** -->
	    		<div class="clearfix preview-deal-box">
	        	<div class="content-box">
	        		<div class="content-box yellow">
	          		<div class="coupon-summary deal_summary-mirror"><?php echo $lDefaultDeal['summary']; ?></div>
	          		<div class="coupon-description">
	          			<p><?php echo __('Ihr Gutschein-Code:'); ?></p>
	          			<p><strong class="deal_coupon_single_code-mirror"><?php echo $pDefaultCode; ?></strong></p>
	          		</div>
	          		<div class="coupon-foot">Expires at <?php echo $lDefaultDeal['end_date']; ?></div>
	          		<a href="/" class="deal_redeem_url-mirror"><?php echo $lDefaultDeal['redeem_url']; ?></a>
	          	</div>
	        	</div>
	    		</div>
	  		</div>

<!-- ********** form-foot: accept terms of use ********** -->
	  		<div class="form-row" id="terms-of-use">
					<?php echo $pForm['deal']['tos_accepted']->render(array('class' => 'left'));?>
					<?php echo __($pForm['deal']['tos_accepted']->renderLabel(), array('%terms%' => link_to('Terms of Deals', '/', array('target' => '_blank'))));?>
					<?php echo $pForm['deal']['tos_accepted']->renderError();?>
				</div>
				<input type="submit" class="button positive" id="proceed-deal-button" value="<?php echo __('Submit Deal >>', null, 'widget');?>" />
				<?php echo __('or'); ?>
				<?php echo link_to('Cancel', 'deals/get_create_index', array('class' => 'link-deal-content')); ?>
			</div>
		</div>
	</div>
</form>