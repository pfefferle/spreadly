<?php use_helper('Avatar', 'Text'); ?>
<div id="content-outer" role="main">
<header>
  <h1 class="success"><?php echo __("Thanks for sharing!"); ?></h1>
 <span id="deal-marker"><?php echo __("Your deal"); ?></span>
</header>

<!-- weisser Content -->
<div id="content-inner" class="clearfix deal-content-inner">
<!-- weisser Content -->
  <div id="deal_url">
    <h3><?php echo $pDeal->getCouponTitle(); ?></h3>
    <a class="link B" href="<?php echo $pDeal->getCouponUrl(); ?>"><?php echo __("Go to the reward website"); ?></a>

    <p><?php echo $pDeal->getCouponText(); ?></p>
  </div>
</div>
</div>


