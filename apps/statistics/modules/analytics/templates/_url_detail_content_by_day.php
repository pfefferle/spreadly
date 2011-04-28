<?php use_helper('Text'); ?>
<?php
if ($pUrlSummary) {
  slot('content')
?>
<?php if (isset($showdate)) {?>
	<h2 class="sub_title"><?php echo __('Likes from %date%', array('%date%' => $showdate)); ?></h2>
<?php } ?>
<div id="line-chart-example">
<?php include_partial('analytics/chart_line_activities_by_hours', array("pData" => $pUrlSummary)); ?>
</div>
<?php
  end_slot();
  include_partial('global/graybox');
}
?>

<?php slot('content') ?>
<h2 class="sub_title"><?php echo __('Like details for %url%', array('%url%' => $pUrl)); ?></h2>
<div class="data-tablebox two-line-table">
  <table width="930px;" border="0" cellspacing="0" cellpadding="0" id="top-like-table" class="tablesorter">
  <thead>
    <tr>
      <th align="center" valign="middle" class="first"><div class="sortlink no-sort"><?php echo __('Hour'); ?></div></th>
      <th align="center" valign="middle">
      	<div class="sortlink">
					<span class="myqtip" title="<?php echo __('Überblick Altersgruppen der Empfehler'); ?>">
      			<?php echo __('Age');?>
      		</span>
	  	  </div>
      </th>
      <th align="center" valign="middle">
      	<div class="sortlink">
      		<span class="myqtip" title="<?php echo __('Überblick Geschlecht der Empfehler'); ?>">
      			<?php echo __('Gender');?>
      		</span>
	  	  </div>
      </th>
      <th align="center" valign="middle">
      	<div class="sortlink">
      		<span class="myqtip" title="<?php echo __('Überblick Beziehungsstatus der Empfehler'); ?>">
      			<?php echo __('Relationship');?>
      		</span>
	  	  </div>
      </th>
      <th align="center" valign="middle">
      	<div class="sortlink">
					<span class="myqtip" title="<?php echo __('Total number of likes published in the social networks listed.'); ?>">
      			<?php echo __('Spreads');?>
      		</span>
	  	  </div>
      </th>
      <th align="center" valign="middle">
      	<div class="sortlink">
      		<span class="myqtip" title="<?php echo __('Maximale Reichweite der Empfehlung'); ?>">
      			<?php echo __('Reach');?>
      		</span>
	  	  </div>
      </th>
      <th align="center" valign="middle">
      	<div class="sortlink">
					<span class="myqtip" title="<?php echo __('Anzahl der Besucher die auf eine Empfehlung gekommen sind'); ?>">
      			<?php echo __('Clickbacks');?>
      		</span>
	  	  </div>
      </th>
      <th align="center" valign="middle" class="last">
      	<div class="sortlink">
      		<span class="myqtip" title="<?php echo __('Anzahl der Besucher die auf eine Empfehlung gekommen sind und dann weiterempfohlen haben'); ?>">
      			<?php echo __('Clickback-Likes');?>
      		</span>
	  	  </div>
      </th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($pUrls->hasNext()) {
      foreach($pUrls as $url){
    ?>
      <tr>
        <td align="center" valign="middle" class="first"><div><?php echo __("%1:00", array("%1" => $url->getHourOfDay())); ?></div></td>
        <td align="center" valign="middle"><div><?php echo $url->getAge() ?></div></td>
        <td align="center" valign="middle"><div><?php echo __($url->getGender()) ?></div></td>
        <td align="center" valign="middle"><div><?php echo __($url->getRelationship()) ?></div></td>
        <td align="center" valign="middle"><div><?php echo $url->getShares() ?></div></td>
        <td align="center" valign="middle"><div><?php echo $url->getMediaPenetration() ?></div></td>
        <td align="center" valign="middle"><div><?php echo $url->getClickbacks() ? $url->getClickbacks() : 0 ?></div></td>
        <td align="center" valign="middle" class="last"><div><?php echo $url->getClickbackLikes() ? $url->getClickbackLikes() : 0 ?></div></td>
      </tr>
    <?php
      }
    } else {
    ?>
      <tr><td align="center" colspan="8"><?php echo __("No likes yet"); ?></td></tr>
    <?php } ?>
    </tbody>
  </table>
</div>
<?php end_slot(); ?>
<?php include_partial('global/graybox'); ?>

<?php
if ($pUrlSummary) {
  slot('content');
?>
<h2 class="sub_title"><?php echo __('Demographics for %url%', array('%url%' => $pUrlSummary->getUrl())); ?></h2>
<div id="pie-charts" class="clearfix">
  <div class="alignleft" id="gender-chart">
    <?php include_partial('analytics/chart_pie_gender', array('pChartsettings' =>
          '{
                "width": 305,
                "height": 180,
                "margin": [ 40, 0, 10, 10],
                "plotsize": "50%",
                "bgcolor" : "#e1e1e1",
                "renderto":"gender-chart"
            }', 'pData' => $pUrlSummary->getDemographics()
    )); ?>
  </div>
  <div class="alignleft" id="age-chart">
    <?php include_partial('analytics/chart_pie_age', array('pChartsettings' =>
          '{
                "width": 305,
                "height": 180,
                "margin": [ 40, 20, 10, 20],
                "plotsize": "50%",
                "bgcolor" : "#e1e1e1",
                "renderto":"age-chart"
            }', 'pData' => $pUrlSummary->getDemographics()
    )); ?>
  </div>
  <div class="alignleft" id="relation-chart">
    <?php include_partial('analytics/chart_pie_relationship', array('pChartsettings' =>
          '{
                "width": 305,
                "height": 180,
                "margin": [ 40, 0, 10, 10],
                "plotsize": "50%",
                "bgcolor" : "#e1e1e1",
                "renderto":"relation-chart"
            }', 'pData' => $pUrlSummary->getDemographics()
    )); ?>
  </div>
</div>
<?php
  end_slot();
  include_partial('global/graybox');
}
?>