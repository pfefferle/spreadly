<div class="widget widget-single widget-one-section">
	<header><section><?php echo $title ?></section></header>
	<section class="widget-data-section chart-section">
		<div id="pie-chart-content">
							<?php include_partial('dashboard/chart_pie_'.$type, array('pChartsettings' =>
								'{
									"width": 130,
									"height": 130,
									"margin": [-10, 0, 30, 0],
									"plotsize": "80%",
									"bgcolor" : "#1e2021",
									"renderto" : "pie-chart-content"
									}',
									'data' => $data)); ?>
		</div>
	</section>
	<footer>
		Powered by @Spreadly
	</footer>
</div>
