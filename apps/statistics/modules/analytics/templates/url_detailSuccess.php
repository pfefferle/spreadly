<?php slot('content') ?>
<div id="analytics-bread">
	<?php echo link_to(__('Dashboard'), 'analytics/index'); ?>&nbsp;&raquo;&nbsp;
	<?php echo link_to('Overview for '.$pDomainProfile->getUrl(), 'analytics/domain_statistics?domainid='.$pDomainProfile->getId()); ?>&nbsp;&raquo;&nbsp;
	<?php echo link_to('Details for '.$pDomainProfile->getUrl(), 'analytics/domain_detail?domainid='.$pDomainProfile->getId());?>&nbsp;&raquo;&nbsp;
	<strong><?php echo link_to('Details for '.$pUrl, 'analytics/url_detail', array('query_string' => 'url='.$pUrl.'&domainid='.$pDomainProfile->getId())); ?></strong>
</div>

<form action="/analytics/get_url_detail" name="analytics-filter-form" id="analytics-filter-form">
  <input type="hidden" name="domainid" value="<?php echo $pDomainProfile->getId(); ?>" />
  <input type="hidden" name="url" value="<?php echo $pUrl; ?>" />
<div class="clearfix">
  <label for="host_id" id="filter-period-label" class="alignleft">
    <select class="custom-select" id="filter_period" name="date-selector">
      <option value="now"><?php echo __('Today'); ?></option>
      <option value="yesterday" selected="selected"><?php echo __('Yesterday'); ?></option>
      <option value="7"><?php echo __('Last week'); ?></option>
      <option value="30"><?php echo __('Last month'); ?></option>
    </select>
  </label>
    <span class="alignleft" id="filter-period-or"><?php echo __('or'); ?></span>
    <?php echo link_to('<span>'.__('Other period').'</span>', 'analytics/select_period?url='.$pUrl.'&domainid='.$pDomainProfile->getId(), array('id' => 'select-period-link', 'class' => 'colorbox button')); ?>
</div>
</form>
<?php end_slot(); ?>
<?php include_partial('global/graybox'); ?>

<div id="domain-detail-content">
<?php include_partial('analytics/url_detail_content_by_day', array('pUrl' => $pUrl, 'pUrls' => $pUrls, 'pUrlSummary' => $pUrlSummary, 'pDomainProfile' => $pDomainProfile, 'date-selector' => 'yesterday', 'showdate' => date("Y-m-d", strtotime("yesterday")))); ?>
</div>