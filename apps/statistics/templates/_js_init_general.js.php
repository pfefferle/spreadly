ErrorHandler.catchGlobalError();
jQuery('.colorbox' ).live('click',function(e){
	e.preventDefault();
  jQuery(this).colorbox({open:true});
});


	jQuery(".radio-label").dgStyle();
	jQuery(".checkbox-label").dgStyle();