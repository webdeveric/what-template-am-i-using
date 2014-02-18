/*!	Open Toggle
	@author WebDevEric
	@date 2014-02-18 */
jQuery.fn.openToggle = function( settings ){

	settings = jQuery.extend( {
		target: this,
		handle: '.open-toggle-handle',
		button: '.open-toggle-button',
		callback: function(){}
	}, settings );

	function _toggle_open( item ){
		if( item.hasClass('open') || ( ! item.hasClass('open') && ! item.hasClass('closed') ) )
			item.removeClass('open').addClass('closed');
		else
			item.removeClass('closed').addClass('open');
	}

	function _handle_clicks( e ){
		var item = jQuery(this).closest( settings.target );
		_toggle_open( item );
		settings.callback.call( self, item );
	}

	this.on( "click", settings.button, _handle_clicks ).on( "dblclick", settings.handle, _handle_clicks );
	
	return this;
};