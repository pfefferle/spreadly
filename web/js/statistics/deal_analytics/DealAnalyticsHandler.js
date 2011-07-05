/**
 * @nocombine statistics
 */


var DealAnalytics = {
    init: function() {
      debug.log('[DealAnalytics][init]');
      AnalyticsTables.initTablesorter("top-pages-table");  
      AnalyticsTables.initTablesorter("top-influencer-table");          
      jQuery('#top-pages-table').tableScroll({height: 200, flush: true});
      jQuery('#top-influencer-table').tableScroll({height: 200, flush: true});  
      
      jQuery('.myqtip').qtip({
//      style: { name: 'cream' },
      position: {
         corner: {
            target: 'rightTop',
            tooltip: 'leftBottom'
          },
          adjust:{
            x: 10
          }            
      },
      style: {
        border: {
           width: 5,
           radius: 10
        },
        padding: 10, 
        textAlign: 'center',
        tip: true,
        name: 'blue' // Style it according to the preset 'cream' style
     }
    });      
      
    }
};