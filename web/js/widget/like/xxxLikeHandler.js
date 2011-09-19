/**
 * @nocombine widget
 */

/**
 * Handles the sending of the like
 * @author KM
 * @deprecated not in use anymore
 */


var WidgetDealForm = {
    /**
     * inits the form-functions
     */
    init: function() {
      debug.log('[WidgetDealForm][init]');
      // reset the form after side-reload (fix for ff)
      if (typeof (document.popupdealform) != "undefined") {
        document.popupdealform.reset();
      }

      WidgetDealForm.doSend();
    },

    /**
     * sends the like to the backend
     * @author KM
     */
    doSend: function() {
      debug.log('[WidgetDealForm][doSend]');

      jQuery('#popup-send-deal-button').bind('click', function() {
        var lAction = jQuery('#popupdealform').attr('action');
        OnLoadGrafic.showGrafic();
        WidgetDealForm.hideButton();
        var options = {
          url : lAction,
          data : {
            ei_kcuf : new Date().getTime()
          },
          type : 'POST',
          dataType : 'json',
          success : function(pResponse) {
            if(pResponse.success == true) {
              jQuery('#coupon-unused-container').empty();
              jQuery('#coupon-unused-container').append(pResponse.html);
              jQuery('#content-outer header h2').empty().append(i18n.get('deal_success_headline'));
            } else {
              WidgetDealForm.showButton();
              WidgetDealForm.showErrorMsg(pResponse.message);
            }
            OnLoadGrafic.hideGrafic();
          }
        };

         jQuery('#popupdealform').ajaxSubmit(options);
         return false;
      });
    },

    hideButton: function() {
      jQuery('#popup-send-deal-box').hide();
    },

    showButton: function(){
      jQuery('#popup-send-deal-box').show();
    },

    showErrorMsg: function(pMsg) {
      debug.log('[showErrorMsg]');
      jQuery('#like-response').empty();
      if(pMsg == undefined){
        pMsg = i18n.get('deal_error_message');
      }

      jQuery('#like-response').append('<span class="error">'+pMsg+"</span>");

      var lTimeout;
        lTimeout = setTimeout(function() {
          jQuery('.error').hide('slow');
          jQuery('.error').remove();
        }, 3000);
    }
};


/**
 * @deprecated not in use anymore 
 */
var WidgetLikeForm = {

    aComment: " ",

    /**
     * inits the form-functions
     */
    init: function() {
      debug.log('[WidgetLikeForm][init]');
      WidgetLikeForm.doSend();
      //jQuery('#area-like-comment').toggleValue();
      jQuery('.mirror-value').mirrorValue();
      WidgetLikeForm.aComment = jQuery('#area-like-comment').val();

      /* reset the form after side-reload (fix for ff)
      if (typeof (document.popup-like-form) != "undefined") {
        document.popup-like-form.reset();
      } */

    },


    /**
     * sends the like to the backend
     * @author KM
     */
    doSend: function() {
      debug.log('[WidgetLikeForm][doSend]');

      jQuery('#popup-send-like-button').bind('click', function() {
        var lAction = jQuery('#popup-like-form').attr('action');
        OnLoadGrafic.showGrafic();
        WidgetLikeForm.hideButton();
        //WidgetLikeForm.hideTextarea();
        
        var options = {
          beforeSerialize : WidgetLikeForm.checkComment,
          url : lAction,
          data : {
            ei_kcuf : new Date().getTime()
          },
          type : 'POST',
          dataType : 'json',
          success : function(pResponse) {
            if(pResponse.success == true) {
              WidgetLikeForm.removeButton();
              //WidgetLikeForm.removeTextarea();
              WidgetLikeForm.removeShares();
              WidgetLikeForm.showSuccessMsg();
            } else {
              WidgetLikeForm.showButton();
              //WidgetLikeForm.showTextarea();
              WidgetLikeForm.showErrorMsg();
            }

            OnLoadGrafic.hideGrafic();
          }
        };

         jQuery('#popup-like-form').ajaxSubmit(options);
         return false;
      });
    },

    checkComment: function(form, options) {
      var lComment = jQuery(form[0]["like[comment]"]).val();
      if(Utils.trim(lComment) == Utils.trim(WidgetLikeForm.aComment)){
        jQuery(form[0]["like[comment]"]).val(" ");
        //debug.log(form[0]["like[comment]"]);
        //form[0]["like[comment]"] = " ";
        form[0]["like[comment]"].defaultValue = " ";
      }
      return true;
    },

    /**
     * sets the hidden img-value
     * @author KM
     * @param string pPath
     */
    setImageValue: function(pPath){
      jQuery('#like-img-value').val(pPath);
    },

    hideButton: function() {
      jQuery('#popup-send-like-button').hide();
    },

    removeButton: function() {
      jQuery('#popup-send-like-button').remove();
    },

    showButton: function(){
      jQuery('#popup-send-like-button').show();
    },

    removeShares: function(){
      jQuery('#like-oi-list').remove();
    },

    /*
    hideTextarea: function() {
      jQuery('#comment-area').hide();
    },

    removeTextarea: function() {
      jQuery('#comment-area').remove();
    },

    showTextarea: function() {
      jQuery('#comment-area').show();
    },*/

    showErrorMsg: function() {
      debug.log('[showErrorMsg]');
      jQuery('#like-response').empty();
      jQuery('#like-response').append('<span class="error">'+i18n.get('like_error_message')+"</span>");

      var lTimeout;
        lTimeout = setTimeout(function() {
          jQuery('.error').hide('slow');
          jQuery('.error').remove();
        }, 5000);
    },

    showSuccessMsg: function() {
      debug.log('[showSuccessMsg]');

      jQuery('#comment-area').empty();
      jQuery('#comment-area').append('<span class="success">'+i18n.get('like_success_message')+"</span><a href='/' id='close-popup-link'>"+i18n.get('close_popup')+"</a>");
      WidgetLikeForm.closePopup();
      /*
      var lTimeout;
        lTimeout = setTimeout(function() {
          jQuery('#comment-area').hide('slow');
          jQuery('#comment-area').remove();
        }, 5000);*/
    },

    closePopup: function(){
      jQuery('#close-popup-link').live('click', function() {
        window.close();
      });
    }
};


/**
 * Handles the behaviour/settings of the preview image for like-messages in services
 * @author KM
 */
var LikeImage = {

    init: function(pImgCount, pUrl) {
     debug.log("[LikeImage][init]");
     debug.log(pImgCount);
     if(pImgCount == 0) {
        LikeImage.get(pUrl);
     } else if (pImgCount == 1) {
       WidgetLikeForm.setImageValue(LikeImage.getImgPath(0));
       LikeImageCounter.hide();
     } else {
       LikeImageScroller.init(true);
       LikeImageCounter.init(pImgCount);
       LikeImageCounter.show();
       LikeImageScroller.onScroll();
     }
    },

    /**
     * if there is no image given by the meta-tag-parser, try to get some from the html-content from the given url
     * @author KM
     * @param string pUrl
     */
    get: function(pUrl) {
      debug.log("[LikeImage][get]");
      OnLoadGrafic.insertGraficToElem(jQuery('#myscroll'));
      var lAction = '/like/get_images';
      var lData = {
        ei_kcuf : new Date().getTime(),
        url: pUrl
      };

      jQuery.ajax({
        //beforeSubmit : OnLoadGrafic.showGrafic,
        type : "GET",
        url : lAction,
        dataType : "json",
        data : lData,
        success : function(pResponse) {
          LikeImage.handleResponse(pResponse);
        }
      });
    },

    /**
     * handles the response after the get-images
     * @author KM
     * @param object pResponse
     */
    handleResponse: function(pResponse) {
      debug.log("[LikeImage][handleResponse]");      
      //insert the image into slider-container
      LikeImage.insert(pResponse.html);
      //if there is no or 1 image, hide the slide-arrows and the counter
      debug.log(pResponse.count);
      if(pResponse.count === 0 || pResponse.count === 1){
        //LikeImageCounter.hide();
        LikeImageScroller.hideContainer();
      } else {
        // if there are more than 1 images:
        // init the scroller
        LikeImageScroller.init(true);
        //init the counter
        //LikeImageCounter.init(pResponse.count);
        //show the slide-arrows and the counter
        //LikeImageCounter.show();
        //and init the onscroll-functionalities (e.g. update counter & update hidden-img-value onscroll)
        LikeImageScroller.onScroll();
      }

      //fill the hidden-img-value with the path of the first image
      WidgetLikeForm.setImageValue(LikeImage.getImgPath(0));
      OnLoadGrafic.removeGraficFromElem(jQuery('#myscroll'));
    },

    /**
     * inserts the images into the scroll-area
     * @author KM
     * @param pHtml
     */
    insert: function(pHtml) {
      jQuery('#scroll-meta-images').empty();
      jQuery('#scroll-meta-images').append(pHtml);
    },


    /**
     * returns the image-path of a given index(position of the image into the slider)
     * @author KM
     * @param number pIndex
     * @returns string
     */
    getImgPath: function(pIndex) {
      return jQuery('#meta-img-'+pIndex).attr('src');
    }
};


/**
 * Handles the scroll/slide functionalities
 * @author KM
 */
var LikeImageScroller = {
  aApiObj: {},

  /**
   * init the scrollable-plugin and set global vars
   * @author KM
   * @param boolean pCircular (should the slider slide "endless"?)
   */
  init: function(pCircular){
    
    LikeImageScroller.showContainer();
    
    //check, if we would an endless slide-show
    var lCircular = false;
    if(pCircular !== undefined) {
      lCircular = true;
    }

    //init the plugin
    jQuery("#myscroll").scrollable({
      //circular: true
    });

    //init the global apiobject with the plugins-api-object
    LikeImageScroller.aApiObj = jQuery('#myscroll').data("scrollable");
  },

  /**
   * calls the scrollable api on scrolling thru the images
   * http://flowplayer.org/tools/documentation/scripting.html
   * @author KM
   */
  onScroll: function(){
    //on seek means on changing the showed image (like onscroll)
    LikeImageScroller.aApiObj.onSeek(function() {
      //update the counter
      LikeImageCounter.update(this.getIndex());
      //update the hidden image value into the form with the path of the current selected image
      WidgetLikeForm.setImageValue(LikeImage.getImgPath(this.getIndex()));
    });
  },
  
  showContainer: function() {
    jQuery('#scroll-button-area').show();    
    
  },

  hideContainer: function() {
    jQuery('#scroll-button-area').hide();
  }


};


/**
 * Handles the behaviour of the image counter
 * @author KM
 * @depricated not in use anymore
 */
var LikeImageCounter = {

  /**
   * sets the total number
   * @author KM
   * @param number pCount
   */
  init: function(pCount) {
    jQuery('#img-number').empty();
    jQuery('#img-number').append(pCount);
  },

  /**
   * updates the counter, e.g. after scrolling
   * @author KM
   * @param number pCount
   */
  update: function(pCount) {
    jQuery('#img-counter').empty();
    jQuery('#img-counter').append(pCount+1);
  },

  /**
   * shows the counter-area (including the slide-arrows)
   * @author KM
   */
  show: function() {
    jQuery('#scroll-button-area').show();
  },

  /**
   * hides the counter-area (including the slide-arrows)
   * @author KM
   */
  hide: function() {
    jQuery('#scroll-button-area').hide();
  }
};

var WidgetLikeContent = {

  aIsContent: false,

  get: function(){
    debug.log('[WidgetLikeContent][get]');
    OnLoadGrafic.showGrafic();
    jQuery('#man-url-content').empty();
    jQuery.ajax({
      //beforeSubmit : OnLoadGrafic.showGrafic,
      type :     "GET",
      url:       '/like/get_like_content',
      dataType : "json",
      data: {
        ei_kcuf: new Date().getTime(),
        url: jQuery('#man-url-input').val()
      },
      success : function(pResponse) {
        if(pResponse.success == true) {
          WidgetLikeContent.show(pResponse.html);
          //WidgetLikeContent.aIsContent = true;
          LikeImage.init(pResponse.imgcount, pResponse.url);
          WidgetLikeForm.init();
        } else {
          //if(WidgetLikeContent.aIsContent === false) {
            WidgetLikeContent.showError(pResponse.msg);
          //}
          //OnLoadGrafic.hideGrafic();
        }
      }
    });
  },

  show: function(pHtml) {
    debug.log('[WidgetLikeContent][show]');
    //WidgetLikeContent.aIsContent = false;
    jQuery('#man-url-content').empty();
    jQuery('#man-url-content').append(pHtml);
    OnLoadGrafic.hideGrafic();
  },

  showError: function(pMsg){
    debug.log('[WidgetLikeContent][showError]');
    jQuery('#man-url-content').empty();
    jQuery('#man-url-content').prepend("<div class='error'>"+pMsg+"</div>");
    OnLoadGrafic.hideGrafic();
  }
};

var WidgetAddService = {
  init:function(){
  	debug.log('[WidgetAddService][init]');
    WidgetAddService.bindClick();
  },

  bindClick: function() {
    jQuery('#like-oi-list .add-service-checkbox').live('click', function() {
      OnLoadGrafic.showGrafic();
      jQuery("body").css("cursor", "progress");
      var lService = jQuery(this).val();
      WidgetAddService.redirect(lService);
    });
  },

  redirect: function(pService){
    window.location = '/auth/signinto?service='+pService;
  }

};