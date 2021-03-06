/**
 * @combine statistics
 */

var Configurator = {
    
  aCulture: "",  
  aClipFlashPath: "",    
  
  init: function(pClipFlashPath) {
    debug.log('[Configurator][init]');
    Configurator.aClipFlashPath = pClipFlashPath;    
    Configurator.chooseApp();
    //DynStyleCode.init();  
    //DynStyleWidgets.init();    
  },
  
  initFormFx: function() {
    jQuery("input[type='radio']").custCheckBox({
      callback: function() {
        DynStyleCode.get();
        DynStyleWidgets.update();
      }
    });      
  },
  
  chooseApp: function() {
    debug.log("[Configurator][chooseApp]");      
    jQuery('.config-app-link').live('click', function() {
      var lAction = jQuery(this).attr('href');
      var lThis = this;
      jQuery.ajax({
        type: "GET",
        url: lAction,
        dataType: "json",
        success: function (response) {
          jQuery('#choose-style-container').empty();
          jQuery('#choose-style-container').html(response.html);
          DynStyleCode.get();
          DynStyleWidgets.init();
          DynStyleForm.init();
          Configurator.initFormFx();  
          DynStyleCode.init();

          if(jQuery(lThis).hasClass('service-img-link')){
            jQuery('.service-img-link').removeClass('active');
            jQuery(lThis).addClass('active');
          }
          
          jQuery('#likebutton_url').toggleValue();
          jQuery('#likebutton_text').toggleValue(); 

          
          if (typeof(document.likebuttonform) !=  "undefined"){
            document.likebuttonform.reset();
          }          
        }
      });
      return false;
    });     
  } 
};


/**
 * class to handle the code in the textarea
 * @author KM
 */
var DynStyleCode = {
  
  /**
   * var to save the path for the copy to clipboard-swf
   */
  aClipFlashPath: "",    
    
  /**
   * inits the special code functionalities
   * @author KM
   */
  init: function() {
    debug.log("[DynStyleCode][init]");     
    DynStyleCode.aClipFlashPath = Configurator.aClipFlashPath;
    //ZeroClipboard.setMoviePath(DynStyleCode.aClipFlashPath);
  },    
    
  /**
   * sends request to get the updated button code after form-editing
   * @author KM
   */
  get: function(){
    debug.log("[DynStyleCode][get]");     
    var options = {
        url:       '/configurator/get_buttoncode',
        data: { ei_kcuf: new Date().getTime() },
        type:      'GET',
        dataType:  'json',
        //resetForm: lReset,
        success:   function(pResponse) { DynStyleCode.show(pResponse); }
    };
    jQuery('#likebutton-form').ajaxSubmit(options);   
  },
  
  /**
   * inserts the updated button code into the textarea
   * @author KM
   * @param json pResponse
   */
  show: function(pResponse) {
    debug.log("[DynStyleCode][show]");
    jQuery('#your_code').empty();
    jQuery('#your_code').val(pResponse.iframe);    
    DynStyleCode.initClipboard();  
  },
  
  /**
   * inits the copy to clipboard functionality
   * @author KM
   */
  initClipboard: function() {
    debug.log("[DynStyleCode][initClipboard]"); 
    jQuery('a#d_clip_container').zclip({
      path: DynStyleCode.aClipFlashPath,
      copy: jQuery('#your_code').val()
    });
  }  
};

/**
 * handles the preview-widgets
 * @author KM
 */
var DynStyleWidgets = {
    
  /**
   * inits special-widget functionalities
   * @author KM
   */
  init: function() {
    debug.log('[DynStyleWidgets][init]');    
    DynStyleWidgets.postload();
  },    
  

  /**
   * show the widget after request
   * @author KM
   */
  show: function(pResponse) {
    debug.log("[DynStyleWidgets][show]");       
    //remove the current widget
    jQuery('#preview_widgets').empty();
    //and append the new one
    jQuery('#preview_widgets').append(pResponse.iframe);  
  },
  
  /**
   * sends the request to get the updated widgets
   * @author KM
   */
  update: function(){
    debug.log("[DynStyleWidgets][update]");         
    //if there are changes in the form or the user clicked the generate-button: send a request to get the changed widget
      var options = {
          url:       '/configurator/get_button',
          data: { ei_kcuf: new Date().getTime() },
          type:      'GET',
          dataType:  'json',
          //resetForm: lReset,
          success:   function(pResponse) {DynStyleWidgets.show(pResponse);}
      };
      jQuery('#likebutton-form').ajaxSubmit(options);
      return false;
  },
  
  /**
   * postload the widgets (used after side-reload...)
   * @author KM
   */
  postload: function() {
    debug.log("[DynStyleWidgets][postload]");         
    //if there are changes in the form or the user clicked the generate-button: send a request to get the changed widget
    var options = {
        url:       '/configurator/get_button',
        data: { ei_kcuf: new Date().getTime() },
        type:      'GET',
        dataType:  'json',
        //resetForm: lReset,
        success:   function(pResponse) {DynStyleWidgets.show(pResponse);}
    };
    jQuery('#likebutton-form').ajaxSubmit(options);    
  }
    
};

var DynStyleForm = {
  init: function() {
    debug.log("[DynStyleForm][init]");       
    DynStyleForm.bindKeyNav();
    DynStyleForm.selectColor();
    
  },
  
  /**
   * updates widgets after typing a url
   * @author KM
   */   
  bindKeyNav: function() {
    debug.log("[DynStyleForm][bindKeyNav]");       
    var lTimeout;
    jQuery('#likebutton_url, #likebutton_text, #likebutton_color').keyup(function(e) {
      clearTimeout(lTimeout);
      lTimeout = setTimeout(function() {
        DynStyleCode.get();
        DynStyleWidgets.update();
      }, 300);
    });    
  },
  
  selectColor: function() {
    debug.log("[DynStyleForm][selectColor]");      
    jQuery('#likebutton_color').ColorPicker({
      color: '973765',
      onBeforeShow: function () {
        jQuery(this).ColorPickerSetColor(this.value);
      },
      
      onSubmit: function(hsb, hex, rgb, el) {
        jQuery(el).val(hex);
        jQuery(el).ColorPickerHide();
        DynStyleCode.get();
        DynStyleWidgets.update();              
      },
      
      onChange: function(hsb, hex, rgb) {
        jQuery('#likebutton_color').val(hex);
      },
      
      onHide: function(){
        DynStyleCode.get();
        DynStyleWidgets.update();                      
      }
    });    
    
  }   
};