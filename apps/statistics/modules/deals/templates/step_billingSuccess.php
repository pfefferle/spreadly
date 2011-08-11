<?php include_partial('deals/breadcrumb', array('pDeal' => $pDeal)); ?>

<?php slot('content') ?>
<form action="<?php echo url_for('deals/step_billing?did='.$pDealId); ?>" id="deal_billing_form" name="create_deal_form" method="POST">
	<div class="createbtnbox alignleft">
		<h2 class="btntitle"><?php echo __('Step 3: Enter your payment method')?></h2>
		<?php //var_dump($pForm['payment_method_id']);die();?>
		<?php echo $pForm['payment_method_id']->render(); ?>
		<input type="radio" class="alignleft" name="existing_pm_id" value="false" checked/>
		<ul class="btnformlist alignleft">
	    <li class="clearfix">
	    	<div class="btnwording alignleft">
	      	<strong><?php echo $pForm['payment_method']['company']->renderLabel(); ?></strong><span><?php echo $pForm['payment_method']['company']->renderError(); ?></span>
	      </div>
	      <label class="textfield-wht">
		      <span>
	      		<?php echo $pForm['payment_method']['company']->render(array('class' => 'wd350')); ?>
	      	</span>
	      </label>
	    </li>
	    <li class="clearfix">
	    	<div class="btnwording alignleft">
	      	<strong><?php echo $pForm['payment_method']['contact_name']->renderLabel(); ?></strong><span><?php echo $pForm['payment_method']['contact_name']->renderError(); ?></span>
	      </div>
	      <label class="textfield-wht">
		      <span>
	      		<?php echo $pForm['payment_method']['contact_name']->render(array('class' => 'wd350')); ?>
	      	</span>
	      </label>
	    </li>
	    <li class="clearfix">
	    	<div class="btnwording alignleft">
	      	<strong><?php echo $pForm['payment_method']['address']->renderLabel(); ?></strong><span><?php echo $pForm['payment_method']['address']->renderError(); ?></span>
	      </div>
	      <label class="textfield-wht">
		      <span>
	      		<?php echo $pForm['payment_method']['address']->render(array('class' => 'wd350')); ?>
	      	</span>
	      </label>
	    </li>
	    <li class="clearfix">
	    	<div class="btnwording alignleft">
	      	<strong><?php echo $pForm['payment_method']['zip']->renderLabel(); ?></strong><span><?php echo $pForm['payment_method']['zip']->renderError(); ?></span>
	      </div>
	      <label class="textfield-wht">
		      <span>
	      		<?php echo $pForm['payment_method']['zip']->render(array('class' => 'wd350')); ?>
	      	</span>
	      </label>
	    </li>
	    <li class="clearfix">
	    	<div class="btnwording alignleft">
	      	<strong><?php echo $pForm['payment_method']['city']->renderLabel(); ?></strong><span><?php echo $pForm['payment_method']['city']->renderError(); ?></span>
	      </div>
	      <label class="textfield-wht">
		      <span>
	      		<?php echo $pForm['payment_method']['city']->render(array('class' => 'wd350')); ?>
	      	</span>
	      </label>
	    </li>
	    <li class="clearfix">
	    	<div class="btnwording alignleft">
	      	<strong><?php echo $pForm['tos_accepted']->renderLabel(); ?></strong><span><?php echo $pForm['tos_accepted']->renderError(); ?></span>
	      </div>
	      <span>
	      	<?php echo $pForm['tos_accepted']->render(); ?>
	      </span>
	    </li>
		</ul>
		<?php foreach($pPaymentMethods as $lPayMethod) { ?>
		<input type="radio" class="alignleft" name="existing_pm_id" value="<?php echo $lPayMethod->getId(); ?>" />
			<ul class="select-address-list alignleft clearfix">
				<li><?php echo $lPayMethod->getCompany(); ?></li>
				<li><?php echo $lPayMethod->getContactName(); ?></li>
				<li><?php echo $lPayMethod->getAddress(); ?></li>
				<li><?php echo $lPayMethod->getZip(); ?> <?php echo $lPayMethod->getCity(); ?></li>
			</ul>
		<?php } ?>
		<input type="submit" id="create_deal_button" class="alignright" />
	</div>
	<div class="alignleft create-deal-helptext">
		<h2 class="btntitle"><?php echo __('Payment and address'); ?></h2>
		<p><?php echo __('Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen Sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien. Es ist ein paradiesmatisches Land, in dem einem gebratene Satzteile in den Mund fliegen.'); ?></p>
	</div>
</form>


<?php end_slot(); ?>
<?php include_partial('global/graybox'); ?>
