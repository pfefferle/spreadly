<?php use_helper('ChartData') ?>
<div id="chart_bar_publisher" class="area-chart"></div>
<?php //var_dump($pData);die();?>
<script type="text/javascript">
var ActivityChart = {
	init: function(pChartsettings) {

	  Highcharts.theme = { colors: [] };// prevent errors in default theme
	  var lData = <?php echo json_encode($pData); ?>;
	  var lZoomType = (pChartsettings.zoomtype === undefined)?'':pChartsettings.zoomtype;
		var lOptions = {
		    chart: {
		      renderTo: 'chart_bar_publisher',
		      spacingRight: 20,
		      backgroundColor: "#f6f6f6",
		      zoomType: lZoomType
		   },
		    title: {
		      text: false
		   },
		    exporting: {
	        enabled: false
	    	},
		   xAxis: {
		      /*type: 'datetime',*/
		      title: {
		         text: "<?php echo __('Month'); ?>"
		      },
					categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
		      /*tickInterval: 24 * 3600 * 1000,*/
		   },
		   yAxis: {
		      title: {
		         text: "<?php echo __('Money/Euro'); ?>"
		      },
		      min: 0,
		      startOnTick: false,
		      showFirstLabel: false,
		      allowDecimals:false
		   },
		   tooltip: {
		      shared: true
		   },
		   legend: {
		      enabled: false
		   },
		   plotOptions: {
		      area: {
		         lineWidth: 1,
		         marker: {
		            enabled: true,
		            states: {
		               hover: {
		                  enabled: true,
		                  radius: 5
		               }
		            }
		         },
		         shadow: false,
		         states: {
		            hover: {
		               lineWidth: 1
		            }
		         }
		      }
		   },

		   series: [{
		      type: 'column',
		      name: 'Money/Euro',
		      /*pointInterval: 86400000,*/
		      pointStart: 0,
		      data: [5, 7, 9, 12.50],
		      color: '#1231e3',
	        fillOpacity: 0.1,
	        fillColor: {
            linearGradient: [0, 0, 0, 300],
            stops: [
               [0, Highcharts.theme.colors[0]],
               [1, 'rgba(61,160,242,0)']
            ]
         }
		   }]
		};
		var lChart = new Highcharts.Chart(lOptions);
	}//end init
};//end object
ActivityChart.init(<?php echo $pChartsettings; ?>);
</script>