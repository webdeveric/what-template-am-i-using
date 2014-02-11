/*!	Open Toggle
	@author WebDevEric
	@date 2014-02-04 */
jQuery.fn.openToggle = function( settings ){

	settings = jQuery.extend( {
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

	return this.each( function(){

		var self = this;
		var item = jQuery(this);
		var button = item.find( settings.button );		
		var handle = item.find( settings.handle );

		if( button.length > 0 ){
			button.click( function(e){
				_toggle_open( item );
				settings.callback.call( self, item );
			} );
		}

		if( handle.length > 0 ){
			handle.dblclick( function(e){
				_toggle_open( item );
				settings.callback.call( self, item );
			} );
		}

	} );

};;(function($){
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
			if(cookies.get(name)!==null){
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

	};

	var wtaiu = null;
	var wtaiu_data = null;

	function open_wtaiu_panel(){
		$('html').removeClass('wtaiu-closed').addClass('wtaiu-open');
		wtaiu.addClass('open');
		cookies.set('wtaiu', 'open');
	}

	function close_wtaiu_panel(){
		$('html').removeClass('wtaiu-open').addClass('wtaiu-closed');
		wtaiu.removeClass('open');
		cookies.set('wtaiu', 'closed');
	}

	function add_wtaiu_transitions(){
		wtaiu.addClass('transition-right');
		$('#wpadminbar').addClass('transition-right');
		$('#wtaiu-handle').addClass('transition-all');
		$('html').addClass('transition-padding');
	}

	function save_sort_order( sort_order ){

		var data = {
			action: 'wtaiu_save_sort_order',
			order: sort_order
		};

		$.post(
			wtaiu_ajaxurl,
			data,
			function( data, textstatus, jqxhr ){},
			'json'
		);

	}

	function save_open_status( open_statuses ){

		var data = {
			action: 'wtaiu_save_panel_open_status',
			panel_statuses: open_statuses
		};

		$.post(
			wtaiu_ajaxurl,
			data,
			function( data, textstatus, jqxhr ){},
			'json'
		);

	}

	function wtaiu_save_close_sidebar(){

		if( ! confirm("Are you sure you want to remove the sidebar?\n\nThe sidebar can be enabled again from your user profile page.") )
			return;

		var data = {
			action: 'wtaiu_save_close_sidebar',
		};

		$.post(
			wtaiu_ajaxurl,
			data,
			function( data, textstatus, jqxhr ){
				close_wtaiu_panel();
				setTimeout( function(){
					// Clean up after X button clicked.
					wtaiu.remove();
					wtaiu = null;
					cookies.remove('wtaiu');
					$('#wpadminbar').removeClass('transition-right');
					$('html').removeClass('transition-padding wtaiu-closed');
				}, 250 );
			},
			'json'
		);

	}

	$( function(){

		wtaiu = $('#wtaiu');
		wtaiu_data = $('#wtaiu-data');

		$('#wtaiu-close').click( wtaiu_save_close_sidebar );

		$('#wtaiu-handle').click( function(){
			if( wtaiu.hasClass('open') )
				close_wtaiu_panel();
			else
				open_wtaiu_panel();
		} );

		if( cookies.get('wtaiu') == 'open' )
			open_wtaiu_panel();
		else
			close_wtaiu_panel();

		wtaiu_data.attr( 'contextmenu', 'wtaiu-context-menu' );

		$('#wtaiu-context-menu .open-all').click( function(){
			var status = {};
			$('#wtaiu-data > .panel').each( function(){
				$(this).removeClass('closed').addClass('open');
				status[ this.id ] = true;
			} );
			save_open_status( status );
		} );

		$('#wtaiu-context-menu .close-all').click( function(){
			var status = {};
			$('#wtaiu-data > .panel').each( function(){
				$(this).removeClass('open').addClass('closed');
				status[ this.id ] = false;
			} );
			save_open_status( status );
		} );

		wtaiu_data.sortable( {
			handle: '.label',
			helper: 'clone',
			items: '> .panel',
			// opacity: .66,
			containment: 'parent',
			placeholder: 'panel-placeholder',
			update: function( event, ui ){
				var order = wtaiu_data.sortable("toArray");
				save_sort_order( order );
			}
		});

		setTimeout( add_wtaiu_transitions, 500 );


		$('#wtaiu-data > .panel').openToggle( {
			button: '.open-toggle-button',
			handle: '.panel-header',
			callback: function( $item ){// "this" refers to the .panel html element; "$item" is the jQuery object that represents .panel
				var status = {};
				status[ this.id ] = ! $item.hasClass('closed');
				save_open_status( status );
			}
		} );


	} );

})(jQuery);;/*! Modernizr 2.7.1 (Custom Build) | MIT & BSD
 * Build: http://modernizr.com/download/#-touch-cssclasses-teststyles-testprop-testallprops-prefixes-domprefixes
 */
window.Modernizr = (function( window, document, undefined ) {

    var version = '2.7.1',

    Modernizr = {},

    enableClasses = true,

    docElement = document.documentElement,

    mod = 'modernizr',
    modElem = document.createElement(mod),
    mStyle = modElem.style,

    inputElem  ,


    toString = {}.toString,

    prefixes = ' -webkit- -moz- -o- -ms- '.split(' '),



    omPrefixes = 'Webkit Moz O ms',

    cssomPrefixes = omPrefixes.split(' '),

    domPrefixes = omPrefixes.toLowerCase().split(' '),


    tests = {},
    inputs = {},
    attrs = {},

    classes = [],

    slice = classes.slice,

    featureName, 


    injectElementWithStyles = function( rule, callback, nodes, testnames ) {

      var style, ret, node, docOverflow,
          div = document.createElement('div'),
                body = document.body,
                fakeBody = body || document.createElement('body');

      if ( parseInt(nodes, 10) ) {
                      while ( nodes-- ) {
              node = document.createElement('div');
              node.id = testnames ? testnames[nodes] : mod + (nodes + 1);
              div.appendChild(node);
          }
      }

                style = ['&#173;','<style id="s', mod, '">', rule, '</style>'].join('');
      div.id = mod;
          (body ? div : fakeBody).innerHTML += style;
      fakeBody.appendChild(div);
      if ( !body ) {
                fakeBody.style.background = '';
                fakeBody.style.overflow = 'hidden';
          docOverflow = docElement.style.overflow;
          docElement.style.overflow = 'hidden';
          docElement.appendChild(fakeBody);
      }

      ret = callback(div, rule);
        if ( !body ) {
          fakeBody.parentNode.removeChild(fakeBody);
          docElement.style.overflow = docOverflow;
      } else {
          div.parentNode.removeChild(div);
      }

      return !!ret;

    },
    _hasOwnProperty = ({}).hasOwnProperty, hasOwnProp;

    if ( !is(_hasOwnProperty, 'undefined') && !is(_hasOwnProperty.call, 'undefined') ) {
      hasOwnProp = function (object, property) {
        return _hasOwnProperty.call(object, property);
      };
    }
    else {
      hasOwnProp = function (object, property) { 
        return ((property in object) && is(object.constructor.prototype[property], 'undefined'));
      };
    }


    if (!Function.prototype.bind) {
      Function.prototype.bind = function bind(that) {

        var target = this;

        if (typeof target != "function") {
            throw new TypeError();
        }

        var args = slice.call(arguments, 1),
            bound = function () {

            if (this instanceof bound) {

              var F = function(){};
              F.prototype = target.prototype;
              var self = new F();

              var result = target.apply(
                  self,
                  args.concat(slice.call(arguments))
              );
              if (Object(result) === result) {
                  return result;
              }
              return self;

            } else {

              return target.apply(
                  that,
                  args.concat(slice.call(arguments))
              );

            }

        };

        return bound;
      };
    }

    function setCss( str ) {
        mStyle.cssText = str;
    }

    function setCssAll( str1, str2 ) {
        return setCss(prefixes.join(str1 + ';') + ( str2 || '' ));
    }

    function is( obj, type ) {
        return typeof obj === type;
    }

    function contains( str, substr ) {
        return !!~('' + str).indexOf(substr);
    }

    function testProps( props, prefixed ) {
        for ( var i in props ) {
            var prop = props[i];
            if ( !contains(prop, "-") && mStyle[prop] !== undefined ) {
                return prefixed == 'pfx' ? prop : true;
            }
        }
        return false;
    }

    function testDOMProps( props, obj, elem ) {
        for ( var i in props ) {
            var item = obj[props[i]];
            if ( item !== undefined) {

                            if (elem === false) return props[i];

                            if (is(item, 'function')){
                                return item.bind(elem || obj);
                }

                            return item;
            }
        }
        return false;
    }

    function testPropsAll( prop, prefixed, elem ) {

        var ucProp  = prop.charAt(0).toUpperCase() + prop.slice(1),
            props   = (prop + ' ' + cssomPrefixes.join(ucProp + ' ') + ucProp).split(' ');

            if(is(prefixed, "string") || is(prefixed, "undefined")) {
          return testProps(props, prefixed);

            } else {
          props = (prop + ' ' + (domPrefixes).join(ucProp + ' ') + ucProp).split(' ');
          return testDOMProps(props, prefixed, elem);
        }
    }
      tests.touch = function() {
        var bool;

        if(('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
          bool = true;
        } else {
          injectElementWithStyles(['@media (',prefixes.join('touch-enabled),('),mod,')','{#modernizr{top:9px;position:absolute}}'].join(''), function( node ) {
            bool = node.offsetTop === 9;
          });
        }

        return bool;
    };
    for ( var feature in tests ) {
        if ( hasOwnProp(tests, feature) ) {
                                    featureName  = feature.toLowerCase();
            Modernizr[featureName] = tests[feature]();

            classes.push((Modernizr[featureName] ? '' : 'no-') + featureName);
        }
    }



     Modernizr.addTest = function ( feature, test ) {
       if ( typeof feature == 'object' ) {
         for ( var key in feature ) {
           if ( hasOwnProp( feature, key ) ) {
             Modernizr.addTest( key, feature[ key ] );
           }
         }
       } else {

         feature = feature.toLowerCase();

         if ( Modernizr[feature] !== undefined ) {
                                              return Modernizr;
         }

         test = typeof test == 'function' ? test() : test;

         if (typeof enableClasses !== "undefined" && enableClasses) {
           docElement.className += ' ' + (test ? '' : 'no-') + feature;
         }
         Modernizr[feature] = test;

       }

       return Modernizr; 
     };


    setCss('');
    modElem = inputElem = null;


    Modernizr._version      = version;

    Modernizr._prefixes     = prefixes;
    Modernizr._domPrefixes  = domPrefixes;
    Modernizr._cssomPrefixes  = cssomPrefixes;



    Modernizr.testProp      = function(prop){
        return testProps([prop]);
    };

    Modernizr.testAllProps  = testPropsAll;


    Modernizr.testStyles    = injectElementWithStyles;    docElement.className = docElement.className.replace(/(^|\s)no-js(\s|$)/, '$1$2') +

                                                    (enableClasses ? ' js ' + classes.join(' ') : '');

    return Modernizr;

})(this, this.document);