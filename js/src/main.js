(function($){
	'use strict';

	window.wtaiu_sidebar = {

		root:null,
		sidebar:null,
		handle:null,
		closebutton:null,
		panelcontainer:null,
		timer:null,
		data:{},

		init:function(){
			this.root			= $('html');
			this.sidebar		= $('#wtaiu');
			this.handle			= $('#wtaiu-handle');
			this.closebutton	= $('#wtaiu-close');
			this.panelcontainer	= $('#wtaiu-data');

			if( wtaiu.data )
				this.data = wtaiu.data;

			this.setupContextMenu();
			this.setupSortable();
			this.setupOpenToggle();

			this.handle.click( function(){
				if( wtaiu_sidebar.isOpen() )
					wtaiu_sidebar.close();
				else
					wtaiu_sidebar.open();

				wtaiu_sidebar.saveData();
			} );

			this.closebutton.click( function(){
				wtaiu_sidebar.killSidebar();
			} );

			// $(window).on('beforeunload', function(){wtaiu_sidebar.sendAjax( false );} );

			if( this.data.open )
				this.open();
			else
				this.close();

			setTimeout( function(){
				wtaiu_sidebar.addTransitions();
			}, 500 );

		},

		setupSortable:function(){
			this.panelcontainer.sortable( {
				handle: '.label',
				helper: 'clone',
				items: '> .panel',
				// opacity: .66,
				containment: 'parent',
				placeholder: 'panel-placeholder',
				update: function( event, ui ){
					wtaiu_sidebar.saveData();
				}
			} );
		},

		setupOpenToggle:function(){
			this.panelcontainer.find('> .panel').openToggle( {
				button: '.open-toggle-button',
				handle: '.panel-header',
				callback: function(){
					wtaiu_sidebar.saveData();
				}
			} );

		},

		setupContextMenu:function(){

			this.panelcontainer.attr( 'contextmenu', 'wtaiu-context-menu' );

			$('#wtaiu-context-menu .open-all').click( function(){
				wtaiu_sidebar.panelcontainer.find('> .panel').each( function(){
					$(this).removeClass('closed').addClass('open');
				} );
				wtaiu_sidebar.saveData();
			} );

			$('#wtaiu-context-menu .close-all').click( function(){
				wtaiu_sidebar.panelcontainer.find('> .panel').each( function(){
					$(this).removeClass('open').addClass('closed');
				} );
				wtaiu_sidebar.saveData();
			} );

		},

		getData:function(){
			return this.data;
		},

		saveData:function(){

			clearTimeout( this.timer );
			this.timer = setTimeout( this.sendAjax.bind(this), 1000 );

			var panel_status = {};
			this.panelcontainer.find('>.panel').each( function(){
				var panel = $(this);
				var id = panel.attr('id');
				var is_open = panel.hasClass('open');
				panel_status[ id ] = is_open ? 1 : 0;
			} );
			this.data.panels = panel_status;
		},

		sendAjax:function( use_async ){

			if( typeof use_async == 'undefined' )
				use_async = false;

			var data = {
				action: 'wtaiu_save_data',
				open: this.data.open ? 1 : 0,
				panels : this.data.panels
			};

			$.ajax( {
				type: "POST",
				async: use_async,
				cache: false,
				url: wtaiu.ajaxurl,
				data: data,
				dataType: 'json',
				success: function( data, textstatus, jqxhr ){},
			} );
		},

		open:function(){
			this.root.removeClass('wtaiu-closed').addClass('wtaiu-open');
			this.sidebar.addClass('open');
			this.data.open = true;
		},

		close:function(){
			this.root.removeClass('wtaiu-open').addClass('wtaiu-closed');
			this.sidebar.removeClass('open');
			this.data.open = false;
		},

		killSidebar:function(){

			if( ! confirm("Are you sure you want to remove the sidebar?\n\nThe sidebar can be enabled again from your user profile page.") )
				return;

			var data = {
				action: 'wtaiu_save_close_sidebar',
			};

			$.post(
				wtaiu.ajaxurl,
				data,
				function( data, textstatus, jqxhr ){
					wtaiu_sidebar.close();
					setTimeout( function(){
						// Clean up after X button clicked.
						wtaiu_sidebar.sidebar.remove();
						wtaiu_sidebar.root.removeClass('transition-padding wtaiu-closed');
						$('#wpadminbar').removeClass('transition-right');
						wtaiu_sidebar = null;
					}, 250 );
				},
				'json'
			);

		},

		isOpen:function(){
			return this.sidebar.hasClass('open');
		},

		addTransitions:function(){
			this.sidebar.addClass('transition-right');
			this.handle.addClass('transition-all');
			$('#wpadminbar').addClass('transition-right');
			$('html').addClass('transition-padding');
		}

	};

	$( function(){
		wtaiu_sidebar.init();
	} );

})(jQuery);