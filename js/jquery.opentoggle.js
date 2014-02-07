/**
	Open Toggle
	@author WebDevEric
	@date 2014-02-04
*/
jQuery.fn.openToggle = function( settings ){

	settings = jQuery.extend( {
		handle: '.open-toggle-handle',
		callback: function(){}
	}, settings );

	function _toggle_open( item ){
		if( item.hasClass('open') || ( ! item.hasClass('open') && ! item.hasClass('closed') ) )
			item.removeClass('open').addClass('closed');
		else
			item.removeClass('closed').addClass('open');
	}

	return this.each( function(){
		var self = this;
		var item = jQuery(this);
		var handle = item.find( settings.handle );
		if( handle.length > 0 ){
			handle.click( function(e){
				_toggle_open( item );
				settings.callback.call( self, item );
			} );
		}
		item.dblclick( function(e){
			_toggle_open( item );
			settings.callback.call( self, item );
		} );

	} );

};