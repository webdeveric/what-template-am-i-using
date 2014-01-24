(function($){
	'use strict';

	var cookies = {
		/**
			@return string "; expires {GMT_Date}" or "";
			@param days - int days to keep cookie
		*/
		getExpires:function(days){
			var expires="";
			if(days){
				var d=new Date();
				d.setTime(d.getTime()+(days*86400000));
				expires="; expires="+d.toGMTString();
			}
			return expires;
		},

		/**
			@return boolean - true if set and false if not.
			@param name - string name of cookie
			@param value - string value of cookie
			@param days - int days to keep cookie
		*/
		set:function(name,value,days){
			document.cookie = name+"="+ value + cookies.getExpires(days) +"; path=/";
			return (document.cookie.indexOf(name)>-1);
		},
		
		/**
			@return string cookie data or null if cookie not found.
			@param name - string name of cookie
		*/
		get:function( name ){
			var cookie = document.cookie.split(/;\s+/), data = {}, i = 0, limit = cookie.length;
			for( ; i < limit ; ++i ){
				var pair = cookie[ i ].split('=');
				data[ pair[ 0 ] ] = pair[ 1 ];
			}
			return ("undefined"!=typeof data[name])?data[name]:null;
		},
		
		/**
			@return boolean - true if cookie deleted and false if not.
		*/
		remove:function(name){
			if(cookies.get(name)!=null){
				cookies.set(name,'',-1);
				return true;
			} else {
				return false;
			}
		},
		
		/**
			@return string - all cookies.
		*/
		toString:function(){
			return document.cookie.toString();
		}

	}

	var wtaiu = null;

	function open_wtaiu_panel(){
		$('body').removeClass('wtaiu-closed').addClass('wtaiu-open');
		wtaiu.addClass('open');
		cookies.set('wtaiu', 'open');
	}

	function close_wtaiu_panel(){
		$('body').removeClass('wtaiu-open').addClass('wtaiu-closed');
		wtaiu.removeClass('open');
		cookies.set('wtaiu', 'closed');
	}

	function add_wtaiu_transitions(){
		wtaiu.addClass('transition-right');
		$('#wpadminbar').addClass('transition-right');
		$('body').addClass('transition-margin');
	}

	$( function(){

		wtaiu = $('#wtaiu');

		$('#wtaiu-close').click( function(){
			close_wtaiu_panel();
			wtaiu.remove();
			cookies.remove('wtaiu');
		} );

		$('#wtaiu-handle').click( function(){
			wtaiu.hasClass('open') ? close_wtaiu_panel() : open_wtaiu_panel();
		} );

		cookies.get('wtaiu') == 'open' ? open_wtaiu_panel() : close_wtaiu_panel();

		setTimeout( add_wtaiu_transitions, 500 );

	} );

})(jQuery);