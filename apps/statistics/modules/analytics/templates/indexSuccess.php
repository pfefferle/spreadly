<?php use_helper("Text"); ?>

<?php if(count($pVerifiedDomains) > 0) { ?>
<?php slot('content') ?>
  <div class="data-tablebox">
  	<table border="0" cellspacing="0" cellpadding="0" id="dash-website-table" class="tablesorter scrolltable" style="width: 930px;">
	  	<thead>
	    	<tr>
	  			<th align="center" valign="middle" class="first"><div class="sortlink no-sort"><?php echo __('Websites last 30 days'); ?></div></th>
	  	    <th align="center" valign="middle"><div class="sortlink no-sort"><?php echo __('Likes');?></div></th>
	  	    <th align="center" valign="middle"><div class="sortlink no-sort"><?php echo __('Shares');?></div></th>
	  			<th align="center" valign="middle" class="last" style="white-space: nowrap;"><div class="sortlink no-sort"><?php echo __('Media Penetration'); ?></div></th>
	    	</tr>
	    </thead>
	    <tbody class="scrollbody">
	  		<?php $i = 1;?>
	  		<?php foreach($pVerifiedDomains as $lDomain) { ?>
	  			<tr class="scrollrow">
	  				<td align="left" class="first"><div class="padleft"><?php echo link_to($lDomain->getUrl(), 'analytics/domain_statistics?domainid='.$lDomain->getId()); ?></div></td>
	  		    <td align="center" valign="middle"><div><?php echo array_key_exists($lDomain->getUrl(), $last30ByHost) ? $last30ByHost[$lDomain->getUrl()]['value']['l'] : 0; ?></div></td>
	  		    <td align="center" valign="middle" ><div><?php echo array_key_exists($lDomain->getUrl(), $last30ByHost) ? $last30ByHost[$lDomain->getUrl()]['value']['sh'] : 0; ?></div></td>
	  				<td align="center" class="last"><div><?php echo array_key_exists($lDomain->getUrl(), $last30ByHost) ? $last30ByHost[$lDomain->getUrl()]['value']['mp'] : 0; ?></div></td>
	  			</tr>
	      <?php $i++; } ?>
	    </tbody>
    </table>
  </div>
<?php end_slot(); ?>
<?php include_partial('global/graybox'); ?>
<?php } ?>

<?php if(count($pDeals) > 0) { ?>
<?php slot('content') ?>
  <div class="data-tablebox">
  	<table border="0" cellspacing="0" cellpadding="0" id="dash-deal-table" class="tablesorter scrolltable" style="width: 930px;">
  	<thead>
    	<tr>
  			<th align="center" valign="middle" class="first"><div class="sortlink no-sort"><?php echo __('Deals last 30 days'); ?></div></th>
  			<th align="center" valign="middle"><div class="sortlink no-sort"><?php echo __('Deals left');?></div></th>
  			<th align="center" valign="middle"><div class="sortlink no-sort"><?php echo __('Days left');?></div></th>
  	    <th align="center" valign="middle"><div class="sortlink no-sort"><?php echo __('Likes');?></div></th>
  	    <th align="center" valign="middle"><div class="sortlink no-sort"><?php echo __('Shares');?></div></th>
  			<th align="center" valign="middle" class="last" style="white-space: nowrap;"><div class="sortlink no-sort"><?php echo __('Media Penetration'); ?></div></th>
    	</tr>
    </thead>
    <tbody>
  		<?php $i = 1;?>
  		<?php foreach($pDeals as $lDeal) { ?>
  			<tr>
  				<td align="left" class="first"><div class="padleft"><?php echo $lDeal->getSummary(); ?></div></td>
  				<td align="center" valign="middle"><div><?php echo $lDeal->getRemainingCouponQuantity(); ?></div></td>
  				<td align="center" valign="middle"><div><?php echo $lDeal->getRemainingDays() > 0 ? $lDeal->getRemainingDays() : __('expired'); ?></div></td>
  				<td align="center" valign="middle"><div><?php echo array_key_exists($lDeal->getId(), $last30ByDeal) ? $last30ByDeal[$lDeal->getId()]['value']['l'] : 0; ?></div></td>
  				<td align="center" valign="middle"><div><?php echo array_key_exists($lDeal->getId(), $last30ByDeal) ? $last30ByDeal[$lDeal->getId()]['value']['sh'] : 0; ?></div></td>
  				<td align="center" class="last"><div><?php echo array_key_exists($lDeal->getId(), $last30ByDeal) ? $last30ByDeal[$lDeal->getId()]['value']['mp'] : 0; ?></div></td>
  			</tr>
      <?php $i++; } ?>
    	</tbody>
  	</table>
</div>
<?php end_slot(); ?>
<?php include_partial('global/graybox'); ?>
<?php } ?>


<?php slot('content') ?>
  <div class="data-tablebox">
  	<table border="0" cellspacing="0" cellpadding="0" id="dash-url-table" class="tablesorter scrolltable" style="width: 930px;">
	  	<thead>
	    	<tr>
	  			<th align="center" valign="middle" class="first"><div class="sortlink no-sort"><?php echo __('Top-Pages last 30 days'); ?></div></th>
	  	    <th align="center" valign="middle"><div class="sortlink no-sort"><?php echo __('Likes');?></div></th>
	  	    <th align="center" valign="middle"><div class="sortlink no-sort"><?php echo __('Shares');?></div></th>
	  			<th align="center" valign="middle" class="last" style="white-space: nowrap;"><div class="sortlink no-sort"><?php echo __('Media Penetration'); ?></div></th>
	    	</tr>
	    </thead>
	    <tbody class="scrollbody">
	    	<?php if(count($last30ByUrl) > 0) { ?>
	  		<?php $i = 1;?>
	  		<?php foreach($last30ByUrl as $lUrl) { ?>
	  		  <?php $lUrlValue = $lUrl['value'] ?>
	  			<tr class="scrollrow">
	  				<td align="left" class="first"><div class="padleft"><?php echo truncate_text($lUrl['_id'], 60); ?></div></td>
	  		    <td align="center" valign="middle"><div><?php echo $lUrlValue['l'] ?></div></td>
	  		    <td align="center" valign="middle" ><div><?php echo $lUrlValue['sh'] ?></div></td>
	  				<td align="center" class="last"><div><?php echo $lUrlValue['mp'] ?></div></td>
	  			</tr>
	      <?php $i++; } ?>
	      <?php } else { ?>
	  			<tr class="scrollrow">
	  				<td align="center" colspan="4"><?php echo __('No websites claimed');?></td>
	  			</tr>
	      <?php } ?>
	    </tbody>
    </table>
  </div>
<?php end_slot(); ?>
<?php include_partial('global/graybox'); ?>
