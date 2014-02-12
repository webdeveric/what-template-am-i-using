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