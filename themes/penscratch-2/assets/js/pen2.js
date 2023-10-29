/* Detect-zoom
 * -----------
 * Cross Browser Zoom and Pixel Ratio Detector
 * Version 1.0.4 | Apr 1 2013
 * dual-licensed under the WTFPL and MIT license
 * Maintained by https://github/tombigel
 * Original developer https://github.com/yonran
 */

//AMD and CommonJS initialization copied from https://github.com/zohararad/audio5js
(function (root, ns, factory) {
    "use strict";

    if (typeof (module) !== 'undefined' && module.exports) { // CommonJS
        module.exports = factory(ns, root);
    } else if (typeof (define) === 'function' && define.amd) { // AMD
        define("factory", function () {
            return factory(ns, root);
        });
    } else {
        root[ns] = factory(ns, root);
    }

}(window, 'detectZoom', function () {

    /**
     * Use devicePixelRatio if supported by the browser
     * @return {Number}
     * @private
     */
    var devicePixelRatio = function () {
        return window.devicePixelRatio || 1;
    };

    /**
     * Fallback function to set default values
     * @return {Object}
     * @private
     */
    var fallback = function () {
        return {
            zoom: 1,
            devicePxPerCssPx: 1
        };
    };
    /**
     * IE 8 and 9: no trick needed!
     * TODO: Test on IE10 and Windows 8 RT
     * @return {Object}
     * @private
     **/
    var ie8 = function () {
        var zoom = Math.round((screen.deviceXDPI / screen.logicalXDPI) * 100) / 100;
        return {
            zoom: zoom,
            devicePxPerCssPx: zoom * devicePixelRatio()
        };
    };

    /**
     * For IE10 we need to change our technique again...
     * thanks https://github.com/stefanvanburen
     * @return {Object}
     * @private
     */
    var ie10 = function () {
        var zoom = Math.round((document.documentElement.offsetHeight / window.innerHeight) * 100) / 100;
        return {
            zoom: zoom,
            devicePxPerCssPx: zoom * devicePixelRatio()
        };
    };

    /**
     * Mobile WebKit
     * the trick: window.innerWIdth is in CSS pixels, while
     * screen.width and screen.height are in system pixels.
     * And there are no scrollbars to mess up the measurement.
     * @return {Object}
     * @private
     */
    var webkitMobile = function () {
        var deviceWidth = (Math.abs(window.orientation) == 90) ? screen.height : screen.width;
        var zoom = deviceWidth / window.innerWidth;
        return {
            zoom: zoom,
            devicePxPerCssPx: zoom * devicePixelRatio()
        };
    };

    /**
     * Desktop Webkit
     * the trick: an element's clientHeight is in CSS pixels, while you can
     * set its line-height in system pixels using font-size and
     * -webkit-text-size-adjust:none.
     * device-pixel-ratio: http://www.webkit.org/blog/55/high-dpi-web-sites/
     *
     * Previous trick (used before http://trac.webkit.org/changeset/100847):
     * documentElement.scrollWidth is in CSS pixels, while
     * document.width was in system pixels. Note that this is the
     * layout width of the document, which is slightly different from viewport
     * because document width does not include scrollbars and might be wider
     * due to big elements.
     * @return {Object}
     * @private
     */
    var webkit = function () {
        var important = function (str) {
            return str.replace(/;/g, " !important;");
        };

        var div = document.createElement('div');
        div.innerHTML = "1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9<br>0";
        div.setAttribute('style', important('font: 100px/1em sans-serif; -webkit-text-size-adjust: none; text-size-adjust: none; height: auto; width: 1em; padding: 0; overflow: visible;'));

        // The container exists so that the div will be laid out in its own flow
        // while not impacting the layout, viewport size, or display of the
        // webpage as a whole.
        // Add !important and relevant CSS rule resets
        // so that other rules cannot affect the results.
        var container = document.createElement('div');
        container.setAttribute('style', important('width:0; height:0; overflow:hidden; visibility:hidden; position: absolute;'));
        container.appendChild(div);

        document.body.appendChild(container);
        var zoom = 1000 / div.clientHeight;
        zoom = Math.round(zoom * 100) / 100;
        document.body.removeChild(container);

        return{
            zoom: zoom,
            devicePxPerCssPx: zoom * devicePixelRatio()
        };
    };

    /**
     * no real trick; device-pixel-ratio is the ratio of device dpi / css dpi.
     * (Note that this is a different interpretation than Webkit's device
     * pixel ratio, which is the ratio device dpi / system dpi).
     *
     * Also, for Mozilla, there is no difference between the zoom factor and the device ratio.
     *
     * @return {Object}
     * @private
     */
    var firefox4 = function () {
        var zoom = mediaQueryBinarySearch('min--moz-device-pixel-ratio', '', 0, 10, 20, 0.0001);
        zoom = Math.round(zoom * 100) / 100;
        return {
            zoom: zoom,
            devicePxPerCssPx: zoom
        };
    };

    /**
     * Firefox 18.x
     * Mozilla added support for devicePixelRatio to Firefox 18,
     * but it is affected by the zoom level, so, like in older
     * Firefox we can't tell if we are in zoom mode or in a device
     * with a different pixel ratio
     * @return {Object}
     * @private
     */
    var firefox18 = function () {
        return {
            zoom: firefox4().zoom,
            devicePxPerCssPx: devicePixelRatio()
        };
    };

    /**
     * works starting Opera 11.11
     * the trick: outerWidth is the viewport width including scrollbars in
     * system px, while innerWidth is the viewport width including scrollbars
     * in CSS px
     * @return {Object}
     * @private
     */
    var opera11 = function () {
        var zoom = window.top.outerWidth / window.top.innerWidth;
        zoom = Math.round(zoom * 100) / 100;
        return {
            zoom: zoom,
            devicePxPerCssPx: zoom * devicePixelRatio()
        };
    };

    /**
     * Use a binary search through media queries to find zoom level in Firefox
     * @param property
     * @param unit
     * @param a
     * @param b
     * @param maxIter
     * @param epsilon
     * @return {Number}
     */
    var mediaQueryBinarySearch = function (property, unit, a, b, maxIter, epsilon) {
        var matchMedia;
        var head, style, div;
        if (window.matchMedia) {
            matchMedia = window.matchMedia;
        } else {
            head = document.getElementsByTagName('head')[0];
            style = document.createElement('style');
            head.appendChild(style);

            div = document.createElement('div');
            div.className = 'mediaQueryBinarySearch';
            div.style.display = 'none';
            document.body.appendChild(div);

            matchMedia = function (query) {
                style.sheet.insertRule('@media ' + query + '{.mediaQueryBinarySearch ' + '{text-decoration: underline} }', 0);
                var matched = getComputedStyle(div, null).textDecoration == 'underline';
                style.sheet.deleteRule(0);
                return {matches: matched};
            };
        }
        var ratio = binarySearch(a, b, maxIter);
        if (div) {
            head.removeChild(style);
            document.body.removeChild(div);
        }
        return ratio;

        function binarySearch(a, b, maxIter) {
            var mid = (a + b) / 2;
            if (maxIter <= 0 || b - a < epsilon) {
                return mid;
            }
            var query = "(" + property + ":" + mid + unit + ")";
            if (matchMedia(query).matches) {
                return binarySearch(mid, b, maxIter - 1);
            } else {
                return binarySearch(a, mid, maxIter - 1);
            }
        }
    };

    /**
     * Generate detection function
     * @private
     */
    var detectFunction = (function () {
        var func = fallback;
        //IE8+
        if (!isNaN(screen.logicalXDPI) && !isNaN(screen.systemXDPI)) {
            func = ie8;
        }
        // IE10+ / Touch
        else if (window.navigator.msMaxTouchPoints) {
            func = ie10;
        }
        //Mobile Webkit
        else if ('orientation' in window && typeof document.body.style.webkitMarquee === 'string') {
            func = webkitMobile;
        }
        //WebKit
        else if (typeof document.body.style.webkitMarquee === 'string') {
            func = webkit;
        }
        //Opera
        else if (navigator.userAgent.indexOf('Opera') >= 0) {
            func = opera11;
        }
        //Last one is Firefox
        //FF 18.x
        else if (window.devicePixelRatio) {
            func = firefox18;
        }
        //FF 4.0 - 17.x
        else if (firefox4().zoom > 0.001) {
            func = firefox4;
        }

        return func;
    }());


    return ({

        /**
         * Ratios.zoom shorthand
         * @return {Number} Zoom level
         */
        zoom: function () {
            return detectFunction().zoom;
        },

        /**
         * Ratios.devicePxPerCssPx shorthand
         * @return {Number} devicePxPerCssPx level
         */
        device: function () {
            return detectFunction().devicePxPerCssPx;
        }
    });
}));

var wpcom_img_zoomer = {
        clientHintSupport: {
                gravatar: false,
                files: false,
                photon: false,
                mshots: false,
                staticAssets: false,
                latex: false,
                imgpress: false,
        },
	useHints: false,
	zoomed: false,
	timer: null,
	interval: 1000, // zoom polling interval in millisecond

	// Should we apply width/height attributes to control the image size?
	imgNeedsSizeAtts: function( img ) {
		// Do not overwrite existing width/height attributes.
		if ( img.getAttribute('width') !== null || img.getAttribute('height') !== null )
			return false;
		// Do not apply the attributes if the image is already constrained by a parent element.
		if ( img.width < img.naturalWidth || img.height < img.naturalHeight )
			return false;
		return true;
	},

        hintsFor: function( service ) {
                if ( this.useHints === false ) {
                        return false;
                }
                if ( this.hints() === false ) {
                        return false;
                }
                if ( typeof this.clientHintSupport[service] === "undefined" ) {
                        return false;
                }
                if ( this.clientHintSupport[service] === true ) {
                        return true;
                }
                return false;
        },

	hints: function() {
		try {
			var chrome = window.navigator.userAgent.match(/\sChrome\/([0-9]+)\.[.0-9]+\s/)
			if (chrome !== null) {
				var version = parseInt(chrome[1], 10)
				if (isNaN(version) === false && version >= 46) {
					return true
				}
			}
		} catch (e) {
			return false
		}
		return false
	},

	init: function() {
		var t = this;
		try{
			t.zoomImages();
			t.timer = setInterval( function() { t.zoomImages(); }, t.interval );
		}
		catch(e){
		}
	},

	stop: function() {
		if ( this.timer )
			clearInterval( this.timer );
	},

	getScale: function() {
		var scale = detectZoom.device();
		// Round up to 1.5 or the next integer below the cap.
		if      ( scale <= 1.0 ) scale = 1.0;
		else if ( scale <= 1.5 ) scale = 1.5;
		else if ( scale <= 2.0 ) scale = 2.0;
		else if ( scale <= 3.0 ) scale = 3.0;
		else if ( scale <= 4.0 ) scale = 4.0;
		else                     scale = 5.0;
		return scale;
	},

	shouldZoom: function( scale ) {
		var t = this;
		// Do not operate on hidden frames.
		if ( "innerWidth" in window && !window.innerWidth )
			return false;
		// Don't do anything until scale > 1
		if ( scale == 1.0 && t.zoomed == false )
			return false;
		return true;
	},

	zoomImages: function() {
		var t = this;
		var scale = t.getScale();
		if ( ! t.shouldZoom( scale ) ){
			return;
		}
		t.zoomed = true;
		// Loop through all the <img> elements on the page.
		var imgs = document.getElementsByTagName("img");

		for ( var i = 0; i < imgs.length; i++ ) {
			// Wait for original images to load
			if ( "complete" in imgs[i] && ! imgs[i].complete )
				continue;

			// Skip images that have srcset attributes.
			if ( imgs[i].hasAttribute('srcset') ) {
				continue;
			}

			// Skip images that don't need processing.
			var imgScale = imgs[i].getAttribute("scale");
			if ( imgScale == scale || imgScale == "0" )
				continue;

			// Skip images that have already failed at this scale
			var scaleFail = imgs[i].getAttribute("scale-fail");
			if ( scaleFail && scaleFail <= scale )
				continue;

			// Skip images that have no dimensions yet.
			if ( ! ( imgs[i].width && imgs[i].height ) )
				continue;

			// Skip images from Lazy Load plugins
			if ( ! imgScale && imgs[i].getAttribute("data-lazy-src") && (imgs[i].getAttribute("data-lazy-src") !== imgs[i].getAttribute("src")))
				continue;

			if ( t.scaleImage( imgs[i], scale ) ) {
				// Mark the img as having been processed at this scale.
				imgs[i].setAttribute("scale", scale);
			}
			else {
				// Set the flag to skip this image.
				imgs[i].setAttribute("scale", "0");
			}
		}
	},

	scaleImage: function( img, scale ) {
		var t = this;
		var newSrc = img.src;

                var isFiles = false;
                var isLatex = false;
                var isPhoton = false;

		// Skip slideshow images
		if ( img.parentNode.className.match(/slideshow-slide/) )
			return false;

		// Skip CoBlocks Lightbox images
		if ( img.parentNode.className.match(/coblocks-lightbox__image/) )
			return false;

		// Scale gravatars that have ?s= or ?size=
		if ( img.src.match( /^https?:\/\/([^\/]*\.)?gravatar\.com\/.+[?&](s|size)=/ ) ) {
                        if ( this.hintsFor( "gravatar" ) === true ) {
                                return false;
                        }
			newSrc = img.src.replace( /([?&](s|size)=)(\d+)/, function( $0, $1, $2, $3 ) {
				// Stash the original size
				var originalAtt = "originals",
				originalSize = img.getAttribute(originalAtt);
				if ( originalSize === null ) {
					originalSize = $3;
					img.setAttribute(originalAtt, originalSize);
					if ( t.imgNeedsSizeAtts( img ) ) {
						// Fix width and height attributes to rendered dimensions.
						img.width = img.width;
						img.height = img.height;
					}
				}
				// Get the width/height of the image in CSS pixels
				var size = img.clientWidth;
				// Convert CSS pixels to device pixels
				var targetSize = Math.ceil(img.clientWidth * scale);
				// Don't go smaller than the original size
				targetSize = Math.max( targetSize, originalSize );
				// Don't go larger than the service supports
				targetSize = Math.min( targetSize, 512 );
				return $1 + targetSize;
			});
		}

		// Scale mshots that have width
		else if ( img.src.match(/^https?:\/\/([^\/]+\.)*(wordpress|wp)\.com\/mshots\/.+[?&]w=\d+/) ) {
                        if ( this.hintsFor( "mshots" ) === true ) {
                                return false;
                        }
			newSrc = img.src.replace( /([?&]w=)(\d+)/, function($0, $1, $2) {
				// Stash the original size
				var originalAtt = 'originalw', originalSize = img.getAttribute(originalAtt);
				if ( originalSize === null ) {
					originalSize = $2;
					img.setAttribute(originalAtt, originalSize);
					if ( t.imgNeedsSizeAtts( img ) ) {
						// Fix width and height attributes to rendered dimensions.
						img.width = img.width;
						img.height = img.height;
					}
				}
				// Get the width of the image in CSS pixels
				var size = img.clientWidth;
				// Convert CSS pixels to device pixels
				var targetSize = Math.ceil(size * scale);
				// Don't go smaller than the original size
				targetSize = Math.max( targetSize, originalSize );
				// Don't go bigger unless the current one is actually lacking
				if ( scale > img.getAttribute("scale") && targetSize <= img.naturalWidth )
					targetSize = $2;
				if ( $2 != targetSize )
					return $1 + targetSize;
				return $0;
			});

			// Update height attribute to match width
			newSrc = newSrc.replace( /([?&]h=)(\d+)/, function($0, $1, $2) {
				if ( newSrc == img.src ) {
					return $0;
				}
				// Stash the original size
				var originalAtt = 'originalh', originalSize = img.getAttribute(originalAtt);
				if ( originalSize === null ) {
					originalSize = $2;
					img.setAttribute(originalAtt, originalSize);
				}
				// Get the height of the image in CSS pixels
				var size = img.clientHeight;
				// Convert CSS pixels to device pixels
				var targetSize = Math.ceil(size * scale);
				// Don't go smaller than the original size
				targetSize = Math.max( targetSize, originalSize );
				// Don't go bigger unless the current one is actually lacking
				if ( scale > img.getAttribute("scale") && targetSize <= img.naturalHeight )
					targetSize = $2;
				if ( $2 != targetSize )
					return $1 + targetSize;
				return $0;
			});
		}

		// Scale simple imgpress queries (s0.wp.com) that only specify w/h/fit
		else if ( img.src.match(/^https?:\/\/([^\/.]+\.)*(wp|wordpress)\.com\/imgpress\?(.+)/) ) {
                        if ( this.hintsFor( "imgpress" ) === true ) {
                                return false; 
                        }
			var imgpressSafeFunctions = ["zoom", "url", "h", "w", "fit", "filter", "brightness", "contrast", "colorize", "smooth", "unsharpmask"];
			// Search the query string for unsupported functions.
			var qs = RegExp.$3.split('&');
			for ( var q in qs ) {
				q = qs[q].split('=')[0];
				if ( imgpressSafeFunctions.indexOf(q) == -1 ) {
					return false;
				}
			}
			// Fix width and height attributes to rendered dimensions.
			img.width = img.width;
			img.height = img.height;
			// Compute new src
			if ( scale == 1 )
				newSrc = img.src.replace(/\?(zoom=[^&]+&)?/, '?');
			else
				newSrc = img.src.replace(/\?(zoom=[^&]+&)?/, '?zoom=' + scale + '&');
		}

		// Scale files.wordpress.com, LaTeX, or Photon images (i#.wp.com)
		else if (
			( isFiles = img.src.match(/^https?:\/\/([^\/]+)\.files\.wordpress\.com\/.+[?&][wh]=/) ) ||
			( isLatex = img.src.match(/^https?:\/\/([^\/.]+\.)*(wp|wordpress)\.com\/latex\.php\?(latex|zoom)=(.+)/) ) ||
			( isPhoton = img.src.match(/^https?:\/\/i[\d]{1}\.wp\.com\/(.+)/) )
		) {
                        if ( false !== isFiles && this.hintsFor( "files" ) === true ) {
                                return false
                        }
                        if ( false !== isLatex && this.hintsFor( "latex" ) === true ) {
                                return false
                        }
                        if ( false !== isPhoton && this.hintsFor( "photon" ) === true ) {
                                return false
                        }
			// Fix width and height attributes to rendered dimensions.
			img.width = img.width;
			img.height = img.height;
			// Compute new src
			if ( scale == 1 ) {
				newSrc = img.src.replace(/\?(zoom=[^&]+&)?/, '?');
			} else {
				newSrc = img.src;

				var url_var = newSrc.match( /([?&]w=)(\d+)/ );
				if ( url_var !== null && url_var[2] ) {
					newSrc = newSrc.replace( url_var[0], url_var[1] + img.width );
				}

				url_var = newSrc.match( /([?&]h=)(\d+)/ );
				if ( url_var !== null && url_var[2] ) {
					newSrc = newSrc.replace( url_var[0], url_var[1] + img.height );
				}

				var zoom_arg = '&zoom=2';
				if ( !newSrc.match( /\?/ ) ) {
					zoom_arg = '?zoom=2';
				}
				img.setAttribute( 'srcset', newSrc + zoom_arg + ' ' + scale + 'x' );
			}
		}

		// Scale static assets that have a name matching *-1x.png or *@1x.png
		else if ( img.src.match(/^https?:\/\/[^\/]+\/.*[-@]([12])x\.(gif|jpeg|jpg|png)(\?|$)/) ) {
                        if ( this.hintsFor( "staticAssets" ) === true ) {
                                return false; 
                        }
			// Fix width and height attributes to rendered dimensions.
			img.width = img.width;
			img.height = img.height;
			var currentSize = RegExp.$1, newSize = currentSize;
			if ( scale <= 1 )
				newSize = 1;
			else
				newSize = 2;
			if ( currentSize != newSize )
				newSrc = img.src.replace(/([-@])[12]x\.(gif|jpeg|jpg|png)(\?|$)/, '$1'+newSize+'x.$2$3');
		}

		else {
			return false;
		}

		// Don't set img.src unless it has changed. This avoids unnecessary reloads.
		if ( newSrc != img.src ) {
			// Store the original img.src
			var prevSrc, origSrc = img.getAttribute("src-orig");
			if ( !origSrc ) {
				origSrc = img.src;
				img.setAttribute("src-orig", origSrc);
			}
			// In case of error, revert img.src
			prevSrc = img.src;
			img.onerror = function(){
				img.src = prevSrc;
				if ( img.getAttribute("scale-fail") < scale )
					img.setAttribute("scale-fail", scale);
				img.onerror = null;
			};
			// Finally load the new image
			img.src = newSrc;
		}

		return true;
	}
};

wpcom_img_zoomer.init();
;
/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	var container, button, menu, links, i, len;

	container = document.getElementById( 'site-navigation' );
	if ( ! container ) {
		return;
	}

	button = container.getElementsByTagName( 'button' )[0];
	if ( 'undefined' === typeof button ) {
		return;
	}

	menu = container.getElementsByTagName( 'ul' )[0];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	menu.setAttribute( 'aria-expanded', 'false' );
	if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
		menu.className += ' nav-menu';
	}

	button.onclick = function() {
		if ( -1 !== container.className.indexOf( 'toggled' ) ) {
			container.className = container.className.replace( ' toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			menu.setAttribute( 'aria-expanded', 'false' );
		} else {
			container.className += ' toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			menu.setAttribute( 'aria-expanded', 'true' );
		}
	};

	// Get all the link elements within the menu.
	links    = menu.getElementsByTagName( 'a' );

	// Each time a menu link is focused or blurred, toggle focus.
	for ( i = 0, len = links.length; i < len; i++ ) {
		links[i].addEventListener( 'focus', toggleFocus, true );
		links[i].addEventListener( 'blur', toggleFocus, true );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		var self = this;

		// Move up through the ancestors of the current link until we hit .nav-menu.
		while ( -1 === self.className.indexOf( 'nav-menu' ) ) {

			// On li elements toggle the class .focus.
			if ( 'li' === self.tagName.toLowerCase() ) {
				if ( -1 !== self.className.indexOf( 'focus' ) ) {
					self.className = self.className.replace( ' focus', '' );
				} else {
					self.className += ' focus';
				}
			}

			self = self.parentElement;
		}
	}

	/**
	 * Toggles `focus` class to allow submenu access on tablets.
	 */
	( function( container ) {
		var touchStartFn, i,
			parentLink = container.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

		if ( 'ontouchstart' in window ) {
			touchStartFn = function( e ) {
				var menuItem = this.parentNode, i;

				if ( ! menuItem.classList.contains( 'focus' ) ) {
					e.preventDefault();
					for ( i = 0; i < menuItem.parentNode.children.length; ++i ) {
						if ( menuItem === menuItem.parentNode.children[i] ) {
							continue;
						}
						menuItem.parentNode.children[i].classList.remove( 'focus' );
					}
					menuItem.classList.add( 'focus' );
				} else {
					menuItem.classList.remove( 'focus' );
				}
			};

			for ( i = 0; i < parentLink.length; ++i ) {
				parentLink[i].addEventListener( 'touchstart', touchStartFn, false );
			}
		}
	}( container ) );
} )();
;
/*
 * Triggers resize event to make sure video widgets in the footer maintain the correct aspect ratio
 */
( function( $ ) {

	$( window ).on( 'load', function() {
		setTimeout( function(){
			if ( typeof( Event ) === 'function' ) {
				window.dispatchEvent( new Event( 'resize' ) );
			} else {
				var event = window.document.createEvent( 'UIEvents' );
				event.initUIEvent( 'resize', true, false, window, 0 );
				window.dispatchEvent( event );
			}
		} );
	} );

} )( jQuery );
;
/**
 * File skip-link-focus-fix.js.
 *
 * Helps with accessibility for keyboard only users.
 *
 * Learn more: https://git.io/vWdr2
 */
(function() {
	var isIe = /(trident|msie)/i.test( navigator.userAgent );

	if ( isIe && document.getElementById && window.addEventListener ) {
		window.addEventListener( 'hashchange', function() {
			var id = location.hash.substring( 1 ),
				element;

			if ( ! ( /^[A-z0-9_-]+$/.test( id ) ) ) {
				return;
			}

			element = document.getElementById( id );

			if ( element ) {
				if ( ! ( /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) ) {
					element.tabIndex = -1;
				}

				element.focus();
			}
		}, false );
	}
})();
;
/* globals JSON */
( function () {
	var eventName = 'wpcom_masterbar_click';

	var linksTracksEvents = {
		// top level items
		'wp-admin-bar-blog'                        : 'my_sites',
		'wp-admin-bar-newdash'                     : 'reader',
		'wp-admin-bar-ab-new-post'                 : 'write_button',
		'wp-admin-bar-my-account'                  : 'my_account',
		'wp-admin-bar-notes'                       : 'notifications',
		// my sites - top items
		'wp-admin-bar-switch-site'                 : 'my_sites_switch_site',
		'wp-admin-bar-blog-info'                   : 'my_sites_site_info',
		'wp-admin-bar-site-view'                   : 'my_sites_view_site',
		'wp-admin-bar-blog-stats'                  : 'my_sites_site_stats',
		'wp-admin-bar-plan'                        : 'my_sites_plan',
		'wp-admin-bar-plan-badge'                  : 'my_sites_plan_badge',
		// my sites - manage
		'wp-admin-bar-edit-page'                   : 'my_sites_manage_site_pages',
		'wp-admin-bar-new-page-badge'              : 'my_sites_manage_add_page',
		'wp-admin-bar-edit-post'                   : 'my_sites_manage_blog_posts',
		'wp-admin-bar-new-post-badge'              : 'my_sites_manage_add_post',
		'wp-admin-bar-edit-attachment'             : 'my_sites_manage_media',
		'wp-admin-bar-new-attachment-badge'        : 'my_sites_manage_add_media',
		'wp-admin-bar-comments'                    : 'my_sites_manage_comments',
		'wp-admin-bar-edit-jetpack-testimonial'    : 'my_sites_manage_testimonials',
		'wp-admin-bar-new-jetpack-testimonial'     : 'my_sites_manage_add_testimonial',
		'wp-admin-bar-edit-jetpack-portfolio'      : 'my_sites_manage_portfolio',
		'wp-admin-bar-new-jetpack-portfolio'       : 'my_sites_manage_add_portfolio',
		// my sites - personalize
		'wp-admin-bar-themes'                      : 'my_sites_personalize_themes',
		'wp-admin-bar-cmz'                         : 'my_sites_personalize_themes_customize',
		// my sites - configure
		'wp-admin-bar-sharing'                     : 'my_sites_configure_sharing',
		'wp-admin-bar-people'                      : 'my_sites_configure_people',
		'wp-admin-bar-people-add'                  : 'my_sites_configure_people_add_button',
		'wp-admin-bar-plugins'                     : 'my_sites_configure_plugins',
		'wp-admin-bar-domains'                     : 'my_sites_configure_domains',
		'wp-admin-bar-domains-add'                 : 'my_sites_configure_add_domain',
		'wp-admin-bar-blog-settings'               : 'my_sites_configure_settings',
		'wp-admin-bar-legacy-dashboard'            : 'my_sites_configure_wp_admin',
		// reader
		'wp-admin-bar-followed-sites'              : 'reader_followed_sites',
		'wp-admin-bar-reader-followed-sites-manage': 'reader_manage_followed_sites',
		'wp-admin-bar-discover-discover'           : 'reader_discover',
		'wp-admin-bar-discover-search'             : 'reader_search',
		'wp-admin-bar-my-activity-my-likes'        : 'reader_my_likes',
		// account
		'wp-admin-bar-user-info'                   : 'my_account_user_name',
		// account - profile
		'wp-admin-bar-my-profile'                  : 'my_account_profile_my_profile',
		'wp-admin-bar-account-settings'            : 'my_account_profile_account_settings',
		'wp-admin-bar-billing'                     : 'my_account_profile_manage_purchases',
		'wp-admin-bar-security'                    : 'my_account_profile_security',
		'wp-admin-bar-notifications'               : 'my_account_profile_notifications',
		// account - special
		'wp-admin-bar-get-apps'                    : 'my_account_special_get_apps',
		'wp-admin-bar-next-steps'                  : 'my_account_special_next_steps',
		'wp-admin-bar-help'                        : 'my_account_special_help',
	};

	var notesTracksEvents = {
		openSite: function ( data ) {
			return {
				clicked: 'masterbar_notifications_panel_site',
				site_id: data.siteId
			};
		},
		openPost: function ( data ) {
			return {
				clicked: 'masterbar_notifications_panel_post',
				site_id: data.siteId,
				post_id: data.postId
			};
		},
		openComment: function ( data ) {
			return {
				clicked: 'masterbar_notifications_panel_comment',
				site_id: data.siteId,
				post_id: data.postId,
				comment_id: data.commentId
			};
		}
	};

	// Element.prototype.matches as a standalone function, with old browser fallback
	function matches( node, selector ) {
		if ( ! node ) {
			return undefined;
		}

		if ( ! Element.prototype.matches && ! Element.prototype.msMatchesSelector ) {
			throw new Error( 'Unsupported browser' );
		}

		return Element.prototype.matches ? node.matches( selector ) : node.msMatchesSelector( selector );
	}

	// Element.prototype.closest as a standalone function, with old browser fallback
	function closest( node, selector ) {
		if ( ! node ) {
			return undefined;
		}

		if ( Element.prototype.closest ) {
			return node.closest( selector );
		}

		do {
			if ( matches( node, selector ) ) {
				return node;
			}

			node = node.parentElement || node.parentNode;
		} while ( node !== null && node.nodeType === 1 );

		return null;
	}

	function recordTracksEvent( eventProps ) {
		eventProps = eventProps || {};
		window._tkq = window._tkq || [];
		window._tkq.push( [ 'recordEvent', eventName, eventProps ] );
	}

	function parseJson( s, defaultValue ) {
		try {
			return JSON.parse( s );
		} catch ( e ) {
			return defaultValue;
		}
	}

	function createTrackableLinkEventHandler( link ) {
		return function () {
			var parent = closest( link, 'li' );

			if ( ! parent ) {
				return;
			}

			var trackingId = link.getAttribute( 'ID' ) || parent.getAttribute( 'ID' );

			if ( ! linksTracksEvents.hasOwnProperty( trackingId ) ) {
				return;
			}

			var eventProps = { 'clicked': linksTracksEvents[ trackingId ] };
			recordTracksEvent( eventProps );
		}
	}

	function init() {
		var trackableLinkSelector = '.mb-trackable .ab-item:not(div),' +
			'#wp-admin-bar-notes .ab-item,' +
			'#wp-admin-bar-user-info .ab-item,' +
			'.mb-trackable .ab-secondary';

		var trackableLinks = document.querySelectorAll( trackableLinkSelector );

		for ( var i = 0; i < trackableLinks.length; i++ ) {
			var link = trackableLinks[ i ];
			var handler = createTrackableLinkEventHandler( link );

			link.addEventListener( 'click', handler );
			link.addEventListener( 'touchstart', handler );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

	// listen for postMessage events from the notifications iframe
	window.addEventListener( 'message', function ( event ) {
		if ( event.origin !== 'https://widgets.wp.com' ) {
			return;
		}

		var data = ( typeof event.data === 'string' ) ? parseJson( event.data, {} ) : event.data;
		if ( data.type !== 'notesIframeMessage' ) {
			return;
		}

		var eventData = notesTracksEvents[ data.action ];
		if ( ! eventData ) {
			return;
		}

		recordTracksEvent( eventData( data ) );
	}, false );

} )();
;
/* global wpcom, jetpackCarouselStrings, DocumentTouch */

( function () {
	'use strict';
	var swiper;
	/////////////////////////////////////
	// Utility functions
	/////////////////////////////////////
	var util = ( function () {
		var noop = function () {};

		function texturize( text ) {
			// Ensure we get a string.
			text = text + '';
			text = text.replace( /'/g, '&#8217;' ).replace( /&#039;/g, '&#8217;' );
			text = text
				.replace( /"/g, '&#8221;' )
				.replace( /&#034;/g, '&#8221;' )
				.replace( /&quot;/g, '&#8221;' )
				.replace( /[\u201D]/g, '&#8221;' );
			// Untexturize allowed HTML tags params double-quotes.
			text = text.replace( /([\w]+)=&#[\d]+;(.+?)&#[\d]+;/g, '$1="$2"' );
			return text.trim();
		}

		function applyReplacements( text, replacements ) {
			if ( ! text ) {
				return;
			}
			if ( ! replacements ) {
				return text;
			}
			return text.replace( /{(\d+)}/g, function ( match, number ) {
				return typeof replacements[ number ] !== 'undefined' ? replacements[ number ] : match;
			} );
		}

		function getBackgroundImage( imgEl ) {
			var canvas = document.createElement( 'canvas' ),
				context = canvas.getContext && canvas.getContext( '2d' );

			if ( ! imgEl ) {
				return;
			}

			context.filter = 'blur(20px) ';
			context.drawImage( imgEl, 0, 0 );
			var url = canvas.toDataURL( 'image/png' );
			canvas = null;

			return url;
		}

		return {
			noop: noop,
			texturize: texturize,
			applyReplacements: applyReplacements,
			getBackgroundImage: getBackgroundImage,
		};
	} )();

	/////////////////////////////////////
	// DOM-related utility functions
	/////////////////////////////////////
	var domUtil = ( function () {
		// Helper matches function (not a polyfill), compatible with IE 11.
		function matches( el, sel ) {
			if ( Element.prototype.matches ) {
				return el.matches( sel );
			}

			if ( Element.prototype.msMatchesSelector ) {
				return el.msMatchesSelector( sel );
			}
		}

		// Helper closest parent node function (not a polyfill) based on
		// https://developer.mozilla.org/en-US/docs/Web/API/Element/closest#Polyfill
		function closest( el, sel ) {
			if ( el.closest ) {
				return el.closest( sel );
			}

			var current = el;

			do {
				if ( matches( current, sel ) ) {
					return current;
				}
				current = current.parentElement || current.parentNode;
			} while ( current !== null && current.nodeType === 1 );

			return null;
		}

		function hide( el ) {
			if ( el ) {
				el.style.display = 'none';
			}
		}

		function show( el ) {
			if ( el ) {
				// Everything we show and hide in Carousel is currently a block,
				// so we can make this really straightforward.
				el.style.display = 'block';
			}
		}

		function fade( el, start, end, callback ) {
			if ( ! el ) {
				return callback();
			}

			// Prepare for transition.
			// Ensure the item is in the render tree, in its initial state.
			el.style.removeProperty( 'display' );
			el.style.opacity = start;
			el.style.transition = 'opacity 0.2s';
			el.style.pointerEvents = 'none';

			var finished = function ( e ) {
				if ( e.target === el && e.propertyName === 'opacity' ) {
					el.style.removeProperty( 'transition' );
					el.style.removeProperty( 'opacity' );
					el.style.removeProperty( 'pointer-events' );
					el.removeEventListener( 'transitionend', finished );
					el.removeEventListener( 'transitioncancel', finished );
					callback();
				}
			};

			requestAnimationFrame( function () {
				// Double rAF for browser compatibility.
				requestAnimationFrame( function () {
					el.addEventListener( 'transitionend', finished );
					el.addEventListener( 'transitioncancel', finished );
					// Trigger transition.
					el.style.opacity = end;
				} );
			} );
		}

		function fadeIn( el, callback ) {
			callback = callback || util.noop;
			fade( el, '0', '1', callback );
		}

		function fadeOut( el, callback ) {
			callback = callback || util.noop;
			fade( el, '1', '0', function () {
				if ( el ) {
					el.style.display = 'none';
				}
				callback();
			} );
		}

		function emitEvent( el, type, detail ) {
			var e;
			try {
				e = new CustomEvent( type, {
					bubbles: true,
					cancelable: true,
					detail: detail || null,
				} );
			} catch ( err ) {
				e = document.createEvent( 'CustomEvent' );
				e.initCustomEvent( type, true, true, detail || null );
			}
			el.dispatchEvent( e );
		}

		// From: https://easings.net/#easeInOutQuad
		function easeInOutQuad( num ) {
			return num < 0.5 ? 2 * num * num : 1 - Math.pow( -2 * num + 2, 2 ) / 2;
		}

		function getFooterClearance( container ) {
			var footer = container.querySelector( '.jp-carousel-info-footer' );
			var infoArea = container.querySelector( '.jp-carousel-info-extra' );
			var contentArea = container.querySelector( '.jp-carousel-info-content-wrapper' );

			if ( footer && infoArea && contentArea ) {
				var styles = window.getComputedStyle( infoArea );
				var padding = parseInt( styles.paddingTop, 10 ) + parseInt( styles.paddingBottom, 10 );
				padding = isNaN( padding ) ? 0 : padding;
				return contentArea.offsetHeight + footer.offsetHeight + padding;
			}
			return 0;
		}

		function isTouch() {
			return (
				'ontouchstart' in window || ( window.DocumentTouch && document instanceof DocumentTouch )
			);
		}

		function scrollToElement( el, container, callback ) {
			if ( ! el || ! container ) {
				if ( callback ) {
					return callback();
				}
				return;
			}

			// For iOS Safari compatibility, use JS to set the minimum height.
			var infoArea = container.querySelector( '.jp-carousel-info-extra' );
			if ( infoArea ) {
				// 64px is the same height as `.jp-carousel-info-footer` in the CSS.
				infoArea.style.minHeight = window.innerHeight - 64 + 'px';
			}

			var isScrolling = true;
			var startTime = Date.now();
			var duration = 300;
			var originalPosition = container.scrollTop;
			var targetPosition = Math.max(
				0,
				el.offsetTop - Math.max( 0, window.innerHeight - getFooterClearance( container ) )
			);
			var distance = targetPosition - container.scrollTop;
			distance = Math.min( distance, container.scrollHeight - window.innerHeight );

			function stopScroll() {
				isScrolling = false;
			}

			function runScroll() {
				var now = Date.now();
				var progress = easeInOutQuad( ( now - startTime ) / duration );

				progress = progress > 1 ? 1 : progress;
				var newVal = progress * distance;
				container.scrollTop = originalPosition + newVal;

				if ( now <= startTime + duration && isScrolling ) {
					return requestAnimationFrame( runScroll );
				}
				if ( callback ) {
					callback();
				}
				if ( infoArea ) {
					infoArea.style.minHeight = '';
				}
				isScrolling = false;
				container.removeEventListener( 'wheel', stopScroll );
			}

			// Allow scroll to be cancelled by user interaction.
			container.addEventListener( 'wheel', stopScroll );
			runScroll();
		}

		function getJSONAttribute( el, attr ) {
			if ( ! el || ! el.hasAttribute( attr ) ) {
				return undefined;
			}

			try {
				return JSON.parse( el.getAttribute( attr ) );
			} catch ( e ) {
				return undefined;
			}
		}

		function convertToPlainText( html ) {
			var dummy = document.createElement( 'div' );
			dummy.textContent = html;
			return dummy.innerHTML;
		}

		function stripHTML( text ) {
			return text.replace( /<[^>]*>?/gm, '' );
		}

		return {
			closest: closest,
			matches: matches,
			hide: hide,
			show: show,
			fadeIn: fadeIn,
			fadeOut: fadeOut,
			scrollToElement: scrollToElement,
			getJSONAttribute: getJSONAttribute,
			convertToPlainText: convertToPlainText,
			stripHTML: stripHTML,
			emitEvent: emitEvent,
			isTouch: isTouch,
		};
	} )();

	/////////////////////////////////////
	// Carousel implementation
	/////////////////////////////////////
	function init() {
		var commentInterval;
		var screenPadding;
		var originalOverflow;
		var originalHOverflow;
		var scrollPos;

		var lastKnownLocationHash = '';
		var isUserTyping = false;

		var gallerySelector =
			'div.gallery, div.tiled-gallery, ul.wp-block-gallery, ul.blocks-gallery-grid, ' +
			'figure.wp-block-gallery.has-nested-images, div.wp-block-jetpack-tiled-gallery, a.single-image-gallery';

		// Selector for items within a gallery or tiled gallery.
		var galleryItemSelector =
			'.gallery-item, .tiled-gallery-item, .blocks-gallery-item, ' + ' .tiled-gallery__item';

		// Selector for all items including single images.
		var itemSelector = galleryItemSelector + ', .wp-block-image';

		var carousel = {};

		var stat =
			typeof wpcom !== 'undefined' && wpcom.carousel && wpcom.carousel.stat
				? wpcom.carousel.stat
				: util.noop;

		var pageview =
			typeof wpcom !== 'undefined' && wpcom.carousel && wpcom.carousel.pageview
				? wpcom.carousel.pageview
				: util.noop;

		function handleKeyboardEvent( e ) {
			if ( ! isUserTyping ) {
				switch ( e.which ) {
					case 38: // up
						e.preventDefault();
						carousel.overlay.scrollTop -= 100;
						break;
					case 40: // down
						e.preventDefault();
						carousel.overlay.scrollTop += 100;
						break;
					case 39: // right
						e.preventDefault();
						swiper.slideNext();
						break;
					case 37: // left
					case 8: // backspace
						e.preventDefault();
						swiper.slidePrev();
						break;
					case 27: // escape
						e.preventDefault();
						closeCarousel();
						break;
					default:
						break;
				}
			}
		}

		function disableKeyboardNavigation() {
			isUserTyping = true;
		}

		function enableKeyboardNavigation() {
			isUserTyping = false;
		}

		function calculatePadding() {
			var baseScreenPadding = 110;
			screenPadding = baseScreenPadding;

			if ( window.innerWidth <= 760 ) {
				screenPadding = Math.round( ( window.innerWidth / 760 ) * baseScreenPadding );

				if ( screenPadding < 40 && domUtil.isTouch() ) {
					screenPadding = 0;
				}
			}
		}

		function initializeCarousel() {
			if ( ! carousel.overlay ) {
				carousel.overlay = document.querySelector( '.jp-carousel-overlay' );
				carousel.container = carousel.overlay.querySelector( '.jp-carousel-wrap' );
				carousel.gallery = carousel.container.querySelector( '.jp-carousel' );
				carousel.info = carousel.overlay.querySelector( '.jp-carousel-info' );
				carousel.caption = carousel.info.querySelector( '.jp-carousel-caption' );
				carousel.commentField = carousel.overlay.querySelector(
					'#jp-carousel-comment-form-comment-field'
				);
				carousel.emailField = carousel.overlay.querySelector(
					'#jp-carousel-comment-form-email-field'
				);
				carousel.authorField = carousel.overlay.querySelector(
					'#jp-carousel-comment-form-author-field'
				);
				carousel.urlField = carousel.overlay.querySelector( '#jp-carousel-comment-form-url-field' );

				calculatePadding();

				[
					carousel.commentField,
					carousel.emailField,
					carousel.authorField,
					carousel.urlField,
				].forEach( function ( field ) {
					if ( field ) {
						field.addEventListener( 'focus', disableKeyboardNavigation );
						field.addEventListener( 'blur', enableKeyboardNavigation );
					}
				} );

				carousel.overlay.addEventListener( 'click', function ( e ) {
					var target = e.target;
					var isTargetCloseHint = !! domUtil.closest( target, '.jp-carousel-close-hint' );
					var isSmallScreen = !! window.matchMedia( '(max-device-width: 760px)' ).matches;
					if ( target === carousel.overlay ) {
						if ( isSmallScreen ) {
							return;
						} else {
							closeCarousel();
						}
					} else if ( isTargetCloseHint ) {
						closeCarousel();
					} else if ( target.classList.contains( 'jp-carousel-image-download' ) ) {
						stat( 'download_original_click' );
					} else if ( target.classList.contains( 'jp-carousel-comment-login' ) ) {
						handleCommentLoginClick( e );
					} else if ( domUtil.closest( target, '#jp-carousel-comment-form-container' ) ) {
						handleCommentFormClick( e );
					} else if (
						domUtil.closest( target, '.jp-carousel-photo-icons-container' ) ||
						target.classList.contains( 'jp-carousel-photo-title' )
					) {
						handleFooterElementClick( e );
					} else if ( ! domUtil.closest( target, '.jp-carousel-info' ) ) {
						return;
					}
				} );

				window.addEventListener( 'keydown', handleKeyboardEvent );

				carousel.overlay.addEventListener( 'jp_carousel.afterOpen', function () {
					enableKeyboardNavigation();

					// Don't show navigation if there's only one image.
					if ( carousel.slides.length <= 1 ) {
						return;
					}
					// Show dot pagination if slide count is <= 5, otherwise show n/total.
					if ( carousel.slides.length <= 5 ) {
						domUtil.show( carousel.info.querySelector( '.jp-swiper-pagination' ) );
					} else {
						domUtil.show( carousel.info.querySelector( '.jp-carousel-pagination' ) );
					}
				} );

				carousel.overlay.addEventListener( 'jp_carousel.beforeClose', function () {
					disableKeyboardNavigation();

					// Fixes some themes where closing carousel brings view back to top.
					document.documentElement.style.removeProperty( 'height' );

					// If we disable the swiper (because there's only one image)
					// we have to re-enable it here again as Swiper doesn't, for some reason,
					// show the navigation buttons again after reinitialization.
					if ( swiper ) {
						swiper.enable();
					}

					// Hide pagination.
					domUtil.hide( carousel.info.querySelector( '.jp-swiper-pagination' ) );
					domUtil.hide( carousel.info.querySelector( '.jp-carousel-pagination' ) );
				} );

				carousel.overlay.addEventListener( 'jp_carousel.afterClose', function () {
					// don't force the browser back when the carousel closes.
					if ( window.history.pushState ) {
						history.pushState(
							'',
							document.title,
							window.location.pathname + window.location.search
						);
					} else {
						window.location.href = '';
					}
					lastKnownLocationHash = '';
					carousel.isOpen = false;
				} );

				// Prevent native browser zooming
				carousel.overlay.addEventListener( 'touchstart', function ( e ) {
					if ( e.touches.length > 1 ) {
						e.preventDefault();
					}
				} );
			}
		}

		function handleCommentLoginClick() {
			var slide = carousel.currentSlide;
			var attachmentId = slide ? slide.attrs.attachmentId : '0';

			window.location.href = jetpackCarouselStrings.login_url + '%23jp-carousel-' + attachmentId;
		}

		function updatePostResults( msg, isSuccess ) {
			var results = carousel.overlay.querySelector( '#jp-carousel-comment-post-results' );
			var elClass = 'jp-carousel-comment-post-' + ( isSuccess ? 'success' : 'error' );
			results.innerHTML = '<span class="' + elClass + '">' + msg + '</span>';
			domUtil.hide( carousel.overlay.querySelector( '#jp-carousel-comment-form-spinner' ) );
			carousel.overlay
				.querySelector( '#jp-carousel-comment-form' )
				.classList.remove( 'jp-carousel-is-disabled' );
			domUtil.show( results );
		}

		function handleCommentFormClick( e ) {
			var target = e.target;
			var data = domUtil.getJSONAttribute( carousel.container, 'data-carousel-extra' ) || {};
			var attachmentId = carousel.currentSlide.attrs.attachmentId;

			var wrapper = document.querySelector( '#jp-carousel-comment-form-submit-and-info-wrapper' );
			var spinner = document.querySelector( '#jp-carousel-comment-form-spinner' );
			var submit = document.querySelector( '#jp-carousel-comment-form-button-submit' );
			var form = document.querySelector( '#jp-carousel-comment-form' );

			if (
				carousel.commentField &&
				carousel.commentField.getAttribute( 'id' ) === target.getAttribute( 'id' )
			) {
				// For first page load
				disableKeyboardNavigation();
				domUtil.show( wrapper );
			} else if ( domUtil.matches( target, 'input[type="submit"]' ) ) {
				e.preventDefault();
				e.stopPropagation();

				domUtil.show( spinner );
				form.classList.add( 'jp-carousel-is-disabled' );

				var ajaxData = {
					action: 'post_attachment_comment',
					nonce: jetpackCarouselStrings.nonce,
					blog_id: data.blog_id,
					id: attachmentId,
					comment: carousel.commentField.value,
				};

				if ( ! ajaxData.comment.length ) {
					updatePostResults( jetpackCarouselStrings.no_comment_text, false );
					return;
				}

				if ( Number( jetpackCarouselStrings.is_logged_in ) !== 1 ) {
					ajaxData.email = carousel.emailField.value;
					ajaxData.author = carousel.authorField.value;
					ajaxData.url = carousel.urlField.value;

					if ( Number( jetpackCarouselStrings.require_name_email ) === 1 ) {
						if ( ! ajaxData.email.length || ! ajaxData.email.match( '@' ) ) {
							updatePostResults( jetpackCarouselStrings.no_comment_email, false );
							return;
						} else if ( ! ajaxData.author.length ) {
							updatePostResults( jetpackCarouselStrings.no_comment_author, false );
							return;
						}
					}
				}

				var xhr = new XMLHttpRequest();
				xhr.open( 'POST', jetpackCarouselStrings.ajaxurl, true );
				xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
				xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );

				xhr.onreadystatechange = function () {
					if (
						this.readyState === XMLHttpRequest.DONE &&
						this.status >= 200 &&
						this.status < 300
					) {
						var response;
						try {
							response = JSON.parse( this.response );
						} catch ( error ) {
							updatePostResults( jetpackCarouselStrings.comment_post_error, false );
							return;
						}
						if ( response.comment_status === 'approved' ) {
							updatePostResults( jetpackCarouselStrings.comment_approved, true );
						} else if ( response.comment_status === 'unapproved' ) {
							updatePostResults( jetpackCarouselStrings.comment_unapproved, true );
						} else {
							// 'deleted', 'spam', false
							updatePostResults( jetpackCarouselStrings.comment_post_error, false );
						}
						clearCommentTextAreaValue();
						fetchComments( attachmentId );
						submit.value = jetpackCarouselStrings.post_comment;
						domUtil.hide( spinner );
						form.classList.remove( 'jp-carousel-is-disabled' );
					} else {
						// TODO: Add error handling and display here
						updatePostResults( jetpackCarouselStrings.comment_post_error, false );
					}
				};

				var params = [];
				for ( var item in ajaxData ) {
					if ( item ) {
						// Encode each form element into a URI-compatible string.
						var encoded = encodeURIComponent( item ) + '=' + encodeURIComponent( ajaxData[ item ] );
						// In x-www-form-urlencoded, spaces should be `+`, not `%20`.
						params.push( encoded.replace( /%20/g, '+' ) );
					}
				}
				var encodedData = params.join( '&' );

				xhr.send( encodedData );
			}
		}

		/**
		 * Handles clicks to icons and other action elements in the icon container.
		 * @param {MouseEvent|TouchEvent|KeyBoardEvent} Event object.
		 */
		function handleFooterElementClick( e ) {
			e.preventDefault();

			var target = e.target;
			var extraInfoContainer = carousel.info.querySelector( '.jp-carousel-info-extra' );
			var photoMetaContainer = carousel.info.querySelector( '.jp-carousel-image-meta' );
			var commentsContainer = carousel.info.querySelector( '.jp-carousel-comments-wrapper' );
			var infoIcon = carousel.info.querySelector( '.jp-carousel-icon-info' );
			var commentsIcon = carousel.info.querySelector( '.jp-carousel-icon-comments' );

			function handleInfoToggle() {
				if ( commentsIcon ) {
					commentsIcon.classList.remove( 'jp-carousel-selected' );
				}
				infoIcon.classList.toggle( 'jp-carousel-selected' );

				if ( commentsContainer ) {
					commentsContainer.classList.remove( 'jp-carousel-show' );
				}
				if ( photoMetaContainer ) {
					photoMetaContainer.classList.toggle( 'jp-carousel-show' );
					if ( photoMetaContainer.classList.contains( 'jp-carousel-show' ) ) {
						extraInfoContainer.classList.add( 'jp-carousel-show' );
					} else {
						extraInfoContainer.classList.remove( 'jp-carousel-show' );
					}
				}
			}

			function handleCommentToggle() {
				if ( infoIcon ) {
					infoIcon.classList.remove( 'jp-carousel-selected' );
				}
				commentsIcon.classList.toggle( 'jp-carousel-selected' );

				if ( photoMetaContainer ) {
					photoMetaContainer.classList.remove( 'jp-carousel-show' );
				}
				if ( commentsContainer ) {
					commentsContainer.classList.toggle( 'jp-carousel-show' );
					if ( commentsContainer.classList.contains( 'jp-carousel-show' ) ) {
						extraInfoContainer.classList.add( 'jp-carousel-show' );
					} else {
						extraInfoContainer.classList.remove( 'jp-carousel-show' );
					}
				}
			}

			if (
				domUtil.closest( target, '.jp-carousel-icon-info' ) ||
				target.classList.contains( 'jp-carousel-photo-title' )
			) {
				if ( photoMetaContainer && photoMetaContainer.classList.contains( 'jp-carousel-show' ) ) {
					domUtil.scrollToElement( carousel.overlay, carousel.overlay, handleInfoToggle );
				} else {
					handleInfoToggle();
					domUtil.scrollToElement( carousel.info, carousel.overlay );
				}
			}

			if ( domUtil.closest( target, '.jp-carousel-icon-comments' ) ) {
				if ( commentsContainer && commentsContainer.classList.contains( 'jp-carousel-show' ) ) {
					domUtil.scrollToElement( carousel.overlay, carousel.overlay, handleCommentToggle );
				} else {
					handleCommentToggle();
					domUtil.scrollToElement( carousel.info, carousel.overlay );
				}
			}
		}

		function processSingleImageGallery() {
			var images = document.querySelectorAll( 'a img[data-attachment-id]' );
			Array.prototype.forEach.call( images, function ( image ) {
				var link = image.parentElement;
				var container = link.parentElement;

				// Skip if image was already added to gallery by shortcode.
				if ( container.classList.contains( 'gallery-icon' ) ) {
					return;
				}

				// Skip if image is part of a gallery.
				if ( domUtil.closest( container, galleryItemSelector ) ) {
					return;
				}

				// Skip if the parent is not actually a link.
				if ( ! link.hasAttribute( 'href' ) ) {
					return;
				}

				var valid = false;

				// If link points to 'Media File' (ignoring GET parameters) and flag is set, allow it.
				if (
					link.getAttribute( 'href' ).split( '?' )[ 0 ] ===
						image.getAttribute( 'data-orig-file' ).split( '?' )[ 0 ] &&
					Number( jetpackCarouselStrings.single_image_gallery_media_file ) === 1
				) {
					valid = true;
				}

				// If link points to 'Attachment Page', allow it.
				if ( link.getAttribute( 'href' ) === image.getAttribute( 'data-permalink' ) ) {
					valid = true;
				}

				// Links to 'Custom URL' or 'Media File' when flag is not set are not valid.
				if ( ! valid ) {
					return;
				}

				// Make this node a gallery recognizable by event listener above.
				link.classList.add( 'single-image-gallery' );
				// blog_id is needed to allow posting comments to correct blog.
				link.setAttribute(
					'data-carousel-extra',
					JSON.stringify( {
						blog_id: Number( jetpackCarouselStrings.blog_id ),
					} )
				);
			} );
		}

		function testForData( el ) {
			return !! ( el && el.getAttribute( 'data-carousel-extra' ) );
		}

		function openOrSelectSlide( gal, index ) {
			if ( ! carousel.isOpen ) {
				// The `open` method selects the correct slide during the initialization.
				loadSwiper( gal, { startIndex: index } );
			} else {
				selectSlideAtIndex( index );
				// We have to force swiper to slide to the index onHasChange.
				swiper.slideTo( index + 1 );
			}
		}

		function selectSlideAtIndex( index ) {
			if ( ! index || index < 0 || index > carousel.slides.length ) {
				index = 0;
			}
			carousel.currentSlide = carousel.slides[ index ];

			var current = carousel.currentSlide;
			var attachmentId = current.attrs.attachmentId;
			var infoIcon = carousel.info.querySelector( '.jp-carousel-icon-info' );
			var commentsIcon = carousel.info.querySelector( '.jp-carousel-icon-comments' );

			// If the comment/info section is toggled open, it's kept open, but scroll to top of the next slide.
			if (
				( infoIcon && infoIcon.classList.contains( 'jp-carousel-selected' ) ) ||
				( commentsIcon && commentsIcon.classList.contains( 'jp-carousel-selected' ) )
			) {
				if ( carousel.overlay.scrollTop !== 0 ) {
					domUtil.scrollToElement( carousel.overlay, carousel.overlay );
				}
			}

			loadFullImage( carousel.slides[ index ] );

			if (
				Number( jetpackCarouselStrings.display_background_image ) === 1 &&
				! carousel.slides[ index ].backgroundImage
			) {
				loadBackgroundImage( carousel.slides[ index ] );
			}

			domUtil.hide( carousel.caption );
			updateTitleCaptionAndDesc( {
				caption: current.attrs.caption,
				title: current.attrs.title,
				desc: current.attrs.desc,
			} );

			var imageMeta = carousel.slides[ index ].attrs.imageMeta;
			updateExif( imageMeta );
			updateFullSizeLink( current );

			if ( Number( jetpackCarouselStrings.display_comments ) === 1 ) {
				testCommentsOpened( carousel.slides[ index ].attrs.commentsOpened );
				fetchComments( attachmentId );
				domUtil.hide( carousel.info.querySelector( '#jp-carousel-comment-post-results' ) );
			}

			// Update pagination in footer.
			var pagination = carousel.info.querySelector( '.jp-carousel-pagination' );
			if ( pagination && carousel.slides.length > 5 ) {
				var currentPage = index + 1;
				pagination.innerHTML = '<span>' + currentPage + ' / ' + carousel.slides.length + '</span>';
			}

			// Record pageview in WP Stats, for each new image loaded full-screen.
			if ( jetpackCarouselStrings.stats ) {
				new Image().src =
					document.location.protocol +
					'//pixel.wp.com/g.gif?' +
					jetpackCarouselStrings.stats +
					'&post=' +
					encodeURIComponent( attachmentId ) +
					'&rand=' +
					Math.random();
			}

			pageview( attachmentId );

			window.location.hash = lastKnownLocationHash = '#jp-carousel-' + attachmentId;
		}

		function restoreScroll() {
			window.scrollTo( window.scrollX || window.pageXOffset || 0, scrollPos || 0 );
		}

		function closeCarousel() {
			// Make sure to let the page scroll again.
			document.body.style.overflow = originalOverflow;
			document.documentElement.style.overflow = originalHOverflow;
			clearCommentTextAreaValue();

			disableKeyboardNavigation();

			domUtil.emitEvent( carousel.overlay, 'jp_carousel.beforeClose' );
			restoreScroll();
			swiper.destroy();
			carousel.isOpen = false;
			// Clear slide data for DOM garbage collection.
			carousel.slides = [];
			carousel.currentSlide = undefined;
			carousel.gallery.innerHTML = '';

			domUtil.fadeOut( carousel.overlay, function () {
				domUtil.emitEvent( carousel.overlay, 'jp_carousel.afterClose' );
			} );
		}

		function calculateMaxSlideDimensions() {
			return {
				width: window.innerWidth,
				height: window.innerHeight - 64, //subtract height of bottom info bar,
			};
		}

		function selectBestImageUrl( args ) {
			if ( typeof args !== 'object' ) {
				args = {};
			}

			if ( typeof args.origFile === 'undefined' ) {
				return '';
			}

			if ( typeof args.origWidth === 'undefined' || typeof args.maxWidth === 'undefined' ) {
				return args.origFile;
			}

			if ( typeof args.mediumFile === 'undefined' || typeof args.largeFile === 'undefined' ) {
				return args.origFile;
			}

			// Check if the image is being served by Photon (using a regular expression on the hostname).

			var imageLinkParser = document.createElement( 'a' );
			imageLinkParser.href = args.largeFile;

			var isPhotonUrl = /^i[0-2]\.wp\.com$/i.test( imageLinkParser.hostname );

			var mediumSizeParts = getImageSizeParts( args.mediumFile, args.origWidth, isPhotonUrl );
			var largeSizeParts = getImageSizeParts( args.largeFile, args.origWidth, isPhotonUrl );

			var largeWidth = parseInt( largeSizeParts[ 0 ], 10 );
			var largeHeight = parseInt( largeSizeParts[ 1 ], 10 );
			var mediumWidth = parseInt( mediumSizeParts[ 0 ], 10 );
			var mediumHeight = parseInt( mediumSizeParts[ 1 ], 10 );

			args.origMaxWidth = args.maxWidth;
			args.origMaxHeight = args.maxHeight;

			// Give devices with a higher devicePixelRatio higher-res images (Retina display = 2, Android phones = 1.5, etc)
			if ( typeof window.devicePixelRatio !== 'undefined' && window.devicePixelRatio > 1 ) {
				args.maxWidth = args.maxWidth * window.devicePixelRatio;
				args.maxHeight = args.maxHeight * window.devicePixelRatio;
			}

			if ( largeWidth >= args.maxWidth || largeHeight >= args.maxHeight ) {
				return args.largeFile;
			}

			if ( mediumWidth >= args.maxWidth || mediumHeight >= args.maxHeight ) {
				return args.mediumFile;
			}

			if ( isPhotonUrl ) {
				// args.origFile doesn't point to a Photon url, so in this case we use args.largeFile
				// to return the photon url of the original image.
				var largeFileIndex = args.largeFile.lastIndexOf( '?' );
				var origPhotonUrl = args.largeFile;
				if ( largeFileIndex !== -1 ) {
					origPhotonUrl = args.largeFile.substring( 0, largeFileIndex );
					// If we have a really large image load a smaller version
					// that is closer to the viewable size
					if ( args.origWidth > args.maxWidth || args.origHeight > args.maxHeight ) {
						// @2x the max sizes so we get a high enough resolution for zooming.
						args.origMaxWidth = args.maxWidth * 2;
						args.origMaxHeight = args.maxHeight * 2;
						origPhotonUrl += '?fit=' + args.origMaxWidth + '%2C' + args.origMaxHeight;
					}
				}
				return origPhotonUrl;
			}

			return args.origFile;
		}

		function getImageSizeParts( file, origWidth, isPhotonUrl ) {
			var size = isPhotonUrl
				? file.replace( /.*=([\d]+%2C[\d]+).*$/, '$1' )
				: file.replace( /.*-([\d]+x[\d]+)\..+$/, '$1' );

			var sizeParts =
				size !== file
					? isPhotonUrl
						? size.split( '%2C' )
						: size.split( 'x' )
					: [ origWidth, 0 ];

			// If one of the dimensions is set to 9999, then the actual value of that dimension can't be retrieved from the url.
			// In that case, we set the value to 0.
			if ( sizeParts[ 0 ] === '9999' ) {
				sizeParts[ 0 ] = '0';
			}

			if ( sizeParts[ 1 ] === '9999' ) {
				sizeParts[ 1 ] = '0';
			}

			return sizeParts;
		}

		/**
		 * Returns a number in a fraction format that represents the shutter speed.
		 * @param Number speed
		 * @return String
		 */
		function formatShutterSpeed( speed ) {
			var denominator;

			// round to one decimal if value > 1s by multiplying it by 10, rounding, then dividing by 10 again
			if ( speed >= 1 ) {
				return Math.round( speed * 10 ) / 10 + 's';
			}

			// If the speed is less than one, we find the denominator by inverting
			// the number. Since cameras usually use rational numbers as shutter
			// speeds, we should get a nice round number. Or close to one in cases
			// like 1/30. So we round it.
			denominator = Math.round( 1 / speed );

			return '1/' + denominator + 's';
		}

		function parseTitleOrDesc( value ) {
			if ( ! value.match( ' ' ) && value.match( '_' ) ) {
				return '';
			}

			return value;
		}

		function updateTitleCaptionAndDesc( data ) {
			var caption = '';
			var title = '';
			var desc = '';
			var captionMainElement;
			var captionInfoExtraElement;
			var titleElement;
			var descriptionElement;

			captionMainElement = carousel.overlay.querySelector( '.jp-carousel-photo-caption' );
			captionInfoExtraElement = carousel.overlay.querySelector( '.jp-carousel-caption' );

			titleElement = carousel.overlay.querySelector( '.jp-carousel-photo-title' );
			descriptionElement = carousel.overlay.querySelector( '.jp-carousel-photo-description' );

			domUtil.hide( captionMainElement );
			domUtil.hide( captionInfoExtraElement );
			domUtil.hide( titleElement );
			domUtil.hide( descriptionElement );

			caption = parseTitleOrDesc( data.caption ) || '';
			title = parseTitleOrDesc( data.title ) || '';
			desc = parseTitleOrDesc( data.desc ) || '';

			if ( caption || title || desc ) {
				if ( caption ) {
					captionMainElement.innerHTML = caption;
					captionInfoExtraElement.innerHTML = caption;

					domUtil.show( captionMainElement );
					domUtil.show( captionInfoExtraElement );
				}

				if ( domUtil.stripHTML( caption ) === domUtil.stripHTML( title ) ) {
					title = '';
				}

				if ( domUtil.stripHTML( caption ) === domUtil.stripHTML( desc ) ) {
					desc = '';
				}

				if ( domUtil.stripHTML( title ) === domUtil.stripHTML( desc ) ) {
					desc = '';
				}

				if ( desc ) {
					descriptionElement.innerHTML = desc;
					domUtil.show( descriptionElement );

					if ( ! title && ! caption ) {
						captionMainElement.innerHTML = domUtil.stripHTML( desc );
						domUtil.show( captionMainElement );
					}
				}

				if ( title ) {
					var plainTitle = domUtil.stripHTML( title );
					titleElement.innerHTML = plainTitle;

					if ( ! caption ) {
						captionMainElement.innerHTML = plainTitle;
						captionInfoExtraElement.innerHTML = plainTitle;

						domUtil.show( captionMainElement );
					}

					domUtil.show( titleElement );
				}
			}
		}

		// updateExif updates the contents of the exif UL (.jp-carousel-image-exif)
		function updateExif( meta ) {
			if ( ! meta || Number( jetpackCarouselStrings.display_exif ) !== 1 ) {
				return false;
			}

			var ul = carousel.info.querySelector( '.jp-carousel-image-meta ul.jp-carousel-image-exif' );
			var html = '';

			for ( var key in meta ) {
				var val = meta[ key ];
				var metaKeys = jetpackCarouselStrings.meta_data || [];

				if ( parseFloat( val ) === 0 || ! val.length || metaKeys.indexOf( key ) === -1 ) {
					continue;
				}

				switch ( key ) {
					case 'focal_length':
						val = val + 'mm';
						break;
					case 'shutter_speed':
						val = formatShutterSpeed( val );
						break;
					case 'aperture':
						val = 'f/' + val;
						break;
				}

				html += '<li><h5>' + jetpackCarouselStrings[ key ] + '</h5>' + val + '</li>';
			}

			ul.innerHTML = html;
			ul.style.removeProperty( 'display' );
		}

		// Update the contents of the jp-carousel-image-download link
		function updateFullSizeLink( currentSlide ) {
			if ( ! currentSlide ) {
				return false;
			}
			var original;
			var origSize = [ currentSlide.attrs.origWidth, currentSlide.attrs.origHeight ];
			var imageLinkParser = document.createElement( 'a' );

			imageLinkParser.href = currentSlide.attrs.src.replace( /\?.+$/, '' );

			// Is this a Photon URL?
			if ( imageLinkParser.hostname.match( /^i[\d]{1}\.wp\.com$/i ) !== null ) {
				original = imageLinkParser.href;
			} else {
				original = currentSlide.attrs.origFile.replace( /\?.+$/, '' );
			}

			var downloadText = carousel.info.querySelector( '.jp-carousel-download-text' );
			var permalink = carousel.info.querySelector( '.jp-carousel-image-download' );

			downloadText.innerHTML = util.applyReplacements(
				jetpackCarouselStrings.download_original,
				origSize
			);
			permalink.setAttribute( 'href', original );
			permalink.style.removeProperty( 'display' );
		}

		function testCommentsOpened( opened ) {
			var commentForm = carousel.container.querySelector( '.jp-carousel-comment-form-container' );
			var isOpened = parseInt( opened, 10 ) === 1;

			if ( isOpened ) {
				domUtil.fadeIn( commentForm );
			} else {
				domUtil.fadeOut( commentForm );
			}
		}

		function fetchComments( attachmentId, offset ) {
			var shouldClear = offset === undefined;
			var commentsIndicator = carousel.info.querySelector(
				'.jp-carousel-icon-comments .jp-carousel-has-comments-indicator'
			);

			commentsIndicator.classList.remove( 'jp-carousel-show' );

			clearInterval( commentInterval );

			if ( ! attachmentId ) {
				return;
			}

			if ( ! offset || offset < 1 ) {
				offset = 0;
			}

			var comments = carousel.info.querySelector( '.jp-carousel-comments' );
			var commentsLoading = carousel.info.querySelector( '#jp-carousel-comments-loading' );
			domUtil.show( commentsLoading );

			if ( shouldClear ) {
				domUtil.hide( comments );
				comments.innerHTML = '';
			}

			var xhr = new XMLHttpRequest();
			var url =
				jetpackCarouselStrings.ajaxurl +
				'?action=get_attachment_comments' +
				'&nonce=' +
				jetpackCarouselStrings.nonce +
				'&id=' +
				attachmentId +
				'&offset=' +
				offset;
			xhr.open( 'GET', url );
			xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );

			var onError = function () {
				domUtil.fadeIn( comments );
				domUtil.fadeOut( commentsLoading );
			};

			xhr.onload = function () {
				// Ignore the results if they arrive late and we're now on a different slide.
				if (
					! carousel.currentSlide ||
					carousel.currentSlide.attrs.attachmentId !== attachmentId
				) {
					return;
				}

				var isSuccess = xhr.status >= 200 && xhr.status < 300;
				var data;
				try {
					data = JSON.parse( xhr.responseText );
				} catch ( e ) {
					// Do nothing.
				}

				if ( ! isSuccess || ! data || ! Array.isArray( data ) ) {
					return onError();
				}

				if ( shouldClear ) {
					comments.innerHTML = '';
				}

				for ( var i = 0; i < data.length; i++ ) {
					var entry = data[ i ];
					var comment = document.createElement( 'div' );
					comment.classList.add( 'jp-carousel-comment' );
					comment.setAttribute( 'id', 'jp-carousel-comment-' + entry.id );
					comment.innerHTML =
						'<div class="comment-gravatar">' +
						entry.gravatar_markup +
						'</div>' +
						'<div class="comment-content">' +
						'<div class="comment-author">' +
						entry.author_markup +
						'</div>' +
						'<div class="comment-date">' +
						entry.date_gmt +
						'</div>' +
						entry.content +
						'</div>';
					comments.appendChild( comment );

					// Set the interval to check for a new page of comments.
					clearInterval( commentInterval );
					commentInterval = setInterval( function () {
						if ( carousel.container.scrollTop + 150 > window.innerHeight ) {
							fetchComments( attachmentId, offset + 10 );
							clearInterval( commentInterval );
						}
					}, 300 );
				}

				if ( data.length > 0 ) {
					domUtil.show( comments );
					commentsIndicator.innerText = data.length;
					commentsIndicator.classList.add( 'jp-carousel-show' );
				}

				domUtil.hide( commentsLoading );
			};

			xhr.onerror = onError;

			xhr.send();
		}

		function loadFullImage( slide ) {
			var el = slide.el;
			var attrs = slide.attrs;
			var image = el.querySelector( 'img' );

			if ( ! image.hasAttribute( 'data-loaded' ) ) {
				var hasPreview = !! attrs.previewImage;
				var thumbSize = attrs.thumbSize;

				if ( ! hasPreview || ( thumbSize && el.offsetWidth > thumbSize.width ) ) {
					image.src = attrs.src;
				} else {
					image.src = attrs.previewImage;
				}

				image.setAttribute( 'itemprop', 'image' );
				image.setAttribute( 'data-loaded', 1 );
			}
		}

		function loadBackgroundImage( slide ) {
			var currentSlide = slide.el;

			if ( swiper && swiper.slides ) {
				currentSlide = swiper.slides[ swiper.activeIndex ];
			}

			var image = slide.attrs.originalElement;
			var isLoaded = image.complete && image.naturalHeight !== 0;

			if ( isLoaded ) {
				applyBackgroundImage( slide, currentSlide, image );
				return;
			}

			image.onload = function () {
				applyBackgroundImage( slide, currentSlide, image );
			};
		}

		function applyBackgroundImage( slide, currentSlide, image ) {
			var url = util.getBackgroundImage( image );
			slide.backgroundImage = url;
			currentSlide.style.backgroundImage = 'url(' + url + ')';
			currentSlide.style.backgroundSize = 'cover';
		}

		function clearCommentTextAreaValue() {
			if ( carousel.commentField ) {
				carousel.commentField.value = '';
			}
		}

		function getOriginalDimensions( el ) {
			var size = el.getAttribute( 'data-orig-size' ) || '';

			if ( size ) {
				var parts = size.split( ',' );
				return { width: parseInt( parts[ 0 ], 10 ), height: parseInt( parts[ 1 ], 10 ) };
			} else {
				return {
					width:
						el.getAttribute( 'data-original-width' ) || el.getAttribute( 'width' ) || undefined,
					height:
						el.getAttribute( 'data-original-height' ) || el.getAttribute( 'height' ) || undefined,
				};
			}
		}

		function initCarouselSlides( items, startIndex ) {
			carousel.slides = [];

			var max = calculateMaxSlideDimensions();

			// If the startIndex is not 0 then preload the clicked image first.
			if ( startIndex !== 0 ) {
				var img = new Image();
				img.src = items[ startIndex ].getAttribute( 'data-gallery-src' );
			}

			var useInPageThumbnails = !! domUtil.closest( items[ 0 ], '.tiled-gallery.type-rectangular' );

			// create the 'slide'
			Array.prototype.forEach.call( items, function ( item, i ) {
				var permalinkEl = domUtil.closest( item, 'a' );
				var origFile = item.getAttribute( 'data-orig-file' ) || item.getAttribute( 'src-orig' );
				var attrID =
					item.getAttribute( 'data-attachment-id' ) || item.getAttribute( 'data-id' ) || '0';
				var caption = document.querySelector(
					'img[data-attachment-id="' + attrID + '"] + figcaption'
				);

				if ( caption ) {
					caption = caption.innerHTML;
				} else {
					caption = item.getAttribute( 'data-image-caption' );
				}

				var attrs = {
					originalElement: item,
					attachmentId: attrID,
					commentsOpened: item.getAttribute( 'data-comments-opened' ) || '0',
					imageMeta: domUtil.getJSONAttribute( item, 'data-image-meta' ) || {},
					title: item.getAttribute( 'data-image-title' ) || '',
					desc: item.getAttribute( 'data-image-description' ) || '',
					mediumFile: item.getAttribute( 'data-medium-file' ) || '',
					largeFile: item.getAttribute( 'data-large-file' ) || '',
					origFile: origFile || '',
					thumbSize: { width: item.naturalWidth, height: item.naturalHeight },
					caption: caption || '',
					permalink: permalinkEl && permalinkEl.getAttribute( 'href' ),
					src: origFile || item.getAttribute( 'src' ) || '',
				};

				var tiledGalleryItem = domUtil.closest( item, '.tiled-gallery-item' );
				var tiledCaptionEl =
					tiledGalleryItem && tiledGalleryItem.querySelector( '.tiled-gallery-caption' );
				var tiledCaption = tiledCaptionEl && tiledCaptionEl.innerHTML;
				if ( tiledCaption ) {
					attrs.caption = tiledCaption;
				}

				var origDimensions = getOriginalDimensions( item );

				attrs.origWidth = origDimensions.width || attrs.thumbSize.width;
				attrs.origHeight = origDimensions.height || attrs.thumbSize.height;

				if ( typeof wpcom !== 'undefined' && wpcom.carousel && wpcom.carousel.generateImgSrc ) {
					attrs.src = wpcom.carousel.generateImgSrc( item, max );
				} else {
					attrs.src = selectBestImageUrl( {
						origFile: attrs.src,
						origWidth: attrs.origWidth,
						origHeight: attrs.origHeight,
						maxWidth: max.width,
						maxHeight: max.height,
						mediumFile: attrs.mediumFile,
						largeFile: attrs.largeFile,
					} );
				}

				// Set the final src.
				item.setAttribute( 'data-gallery-src', attrs.src );

				if ( attrs.attachmentId !== '0' ) {
					attrs.title = util.texturize( attrs.title );
					attrs.desc = util.texturize( attrs.desc );
					attrs.caption = util.texturize( attrs.caption );

					// Initially, the image is a 1x1 transparent gif.
					// The preview is shown as a background image on the slide itself.
					var image = new Image();
					image.src = attrs.src;

					var slideEl = document.createElement( 'div' );
					slideEl.classList.add( 'swiper-slide' );
					slideEl.setAttribute( 'itemprop', 'associatedMedia' );
					slideEl.setAttribute( 'itemscope', '' );
					slideEl.setAttribute( 'itemtype', 'https://schema.org/ImageObject' );
					var zoomEl = document.createElement( 'div' );
					zoomEl.classList.add( 'swiper-zoom-container' );

					carousel.gallery.appendChild( slideEl );

					slideEl.appendChild( zoomEl );
					zoomEl.appendChild( image );
					slideEl.setAttribute( 'data-attachment-id', attrs.attachmentId );
					slideEl.setAttribute( 'data-permalink', attrs.permalink );
					slideEl.setAttribute( 'data-orig-file', attrs.origFile );

					if ( useInPageThumbnails ) {
						// Use the image already loaded in the gallery as a preview.
						attrs.previewImage = attrs.src;
					}

					var slide = { el: slideEl, attrs: attrs, index: i };
					carousel.slides.push( slide );
				}
			} );
		}

		function loadSwiper( gallery, options ) {
			if ( ! window.Swiper670 ) {
				var loader = document.querySelector( '#jp-carousel-loading-overlay' );
				domUtil.show( loader );
				var jsScript = document.createElement( 'script' );
				jsScript.id = 'jetpack-carousel-swiper-js';
				jsScript.src = window.jetpackSwiperLibraryPath.url;
				jsScript.async = true;
				jsScript.onload = function () {
					domUtil.hide( loader );
					openCarousel( gallery, options );
				};
				jsScript.onerror = function () {
					domUtil.hide( loader );
				};
				document.head.appendChild( jsScript );
				return;
			}
			openCarousel( gallery, options );
		}

		function openCarousel( gallery, options ) {
			var settings = {
				imgSelector:
					'.gallery-item [data-attachment-id], .tiled-gallery-item [data-attachment-id], img[data-attachment-id], img[data-id]',
				startIndex: 0,
			};

			var data = domUtil.getJSONAttribute( gallery, 'data-carousel-extra' );
			var tapTimeout;

			if ( ! data ) {
				return; // don't run if the default gallery functions weren't used
			}

			initializeCarousel();

			if ( carousel.isOpen ) {
				return; // don't open if already opened
			}
			carousel.isOpen = true;

			// make sure to stop the page from scrolling behind the carousel overlay, so we don't trigger
			// infiniscroll for it when enabled (Reader, theme infiniscroll, etc).
			originalOverflow = getComputedStyle( document.body ).overflow;
			document.body.style.overflow = 'hidden';
			// prevent html from overflowing on some of the new themes.
			originalHOverflow = getComputedStyle( document.documentElement ).overflow;
			document.documentElement.style.overflow = 'hidden';
			scrollPos = window.scrollY || window.pageYOffset || 0;

			carousel.container.setAttribute( 'data-carousel-extra', JSON.stringify( data ) );
			stat( [ 'open', 'view_image' ] );

			// If options exist, lets merge them
			// with our default settings
			for ( var option in options || {} ) {
				settings[ option ] = options[ option ];
			}

			if ( settings.startIndex === -1 ) {
				settings.startIndex = 0; // -1 returned if can't find index, so start from beginning
			}

			domUtil.emitEvent( carousel.overlay, 'jp_carousel.beforeOpen' );
			carousel.gallery.innerHTML = '';

			// Need to set the overlay manually to block or swiper does't initialise properly.
			carousel.overlay.style.opacity = 1;
			carousel.overlay.style.display = 'block';

			initCarouselSlides( gallery.querySelectorAll( settings.imgSelector ), settings.startIndex );

			swiper = new window.Swiper670( '.jp-carousel-swiper-container', {
				centeredSlides: true,
				zoom: true,
				loop: carousel.slides.length > 1,
				// Turn off interactions and hide navigation arrows if there is only one slide.
				enabled: carousel.slides.length > 1,
				pagination: {
					el: '.jp-swiper-pagination',
					clickable: true,
				},
				navigation: {
					nextEl: '.jp-swiper-button-next',
					prevEl: '.jp-swiper-button-prev',
				},
				initialSlide: settings.startIndex,
				on: {
					init: function () {
						selectSlideAtIndex( settings.startIndex );
					},
				},
				preventClicks: false,
				preventClicksPropagation: false,
				preventInteractionOnTransition: ! domUtil.isTouch(),
				threshold: 5,
			} );

			swiper.on( 'slideChange', function ( swiper ) {
				var index;
				// Swiper indexes slides from 1, plus when looping to left last slide ends up
				// as 0 and looping to right first slide as total slides + 1. These are adjusted
				// here to match index of carousel.slides.
				if ( swiper.activeIndex === 0 ) {
					index = carousel.slides.length - 1;
				} else if ( swiper.activeIndex === carousel.slides.length + 1 ) {
					index = 0;
				} else {
					index = swiper.activeIndex - 1;
				}
				selectSlideAtIndex( index );

				carousel.overlay.classList.remove( 'jp-carousel-hide-controls' );
			} );

			swiper.on( 'zoomChange', function ( swiper, scale ) {
				if ( scale > 1 ) {
					carousel.overlay.classList.add( 'jp-carousel-hide-controls' );
				}

				if ( scale === 1 ) {
					carousel.overlay.classList.remove( 'jp-carousel-hide-controls' );
				}
			} );

			swiper.on( 'doubleTap', function ( swiper ) {
				clearTimeout( tapTimeout );
				if ( swiper.zoom.scale === 1 ) {
					var zoomTimeout = setTimeout( function () {
						carousel.overlay.classList.remove( 'jp-carousel-hide-controls' );
						clearTimeout( zoomTimeout );
					}, 150 );
				}
			} );

			swiper.on( 'tap', function () {
				if ( swiper.zoom.scale > 1 ) {
					tapTimeout = setTimeout( function () {
						carousel.overlay.classList.toggle( 'jp-carousel-hide-controls' );
					}, 150 );
				}
			} );

			domUtil.fadeIn( carousel.overlay, function () {
				domUtil.emitEvent( carousel.overlay, 'jp_carousel.afterOpen' );
			} );
		}

		// Register the event listener for starting the gallery
		document.body.addEventListener( 'click', function ( e ) {
			var isCompatible =
				window.CSS && window.CSS.supports && window.CSS.supports( 'display', 'grid' );

			// IE11 support is being dropped in August 2021. The new swiper.js libray is not IE11 compat
			// so just default to opening individual image attachment/media pages for IE.
			if ( ! isCompatible ) {
				return;
			}

			var target = e.target;
			var gallery = domUtil.closest( target, gallerySelector );

			if ( gallery ) {
				if ( ! testForData( gallery ) ) {
					return;
				}

				var parent = target.parentElement;
				var grandparent = parent.parentElement;

				// If Gallery is made up of individual Image blocks check for custom link before
				// loading carousel.
				if ( grandparent && grandparent.classList.contains( 'wp-block-image' ) ) {
					var parentHref = parent.getAttribute( 'href' );

					// If the link does not point to the attachment or media file then assume Image has
					// a custom link so don't load the carousel.
					if (
						parentHref.split( '?' )[ 0 ] !==
							target.getAttribute( 'data-orig-file' ).split( '?' )[ 0 ] &&
						parentHref !== target.getAttribute( 'data-permalink' )
					) {
						return;
					}
				}

				// Do not open the modal if we are looking at a gallery caption from before WP5, which may contain a link.
				if ( parent.classList.contains( 'gallery-caption' ) ) {
					return;
				}

				// Do not open the modal if we are looking at a caption of a gallery block, which may contain a link.
				if ( domUtil.matches( parent, 'figcaption' ) ) {
					return;
				}

				// Set height to auto.
				// Fix some themes where closing carousel brings view back to top.
				document.documentElement.style.height = 'auto';

				e.preventDefault();

				// Stopping propagation in case there are parent elements
				// with .gallery or .tiled-gallery class
				e.stopPropagation();

				var item = domUtil.closest( target, itemSelector );
				var index = Array.prototype.indexOf.call( gallery.querySelectorAll( itemSelector ), item );
				loadSwiper( gallery, { startIndex: index } );
			}
		} );

		// Handle lightbox (single image gallery) for images linking to 'Attachment Page'.
		if ( Number( jetpackCarouselStrings.single_image_gallery ) === 1 ) {
			processSingleImageGallery();
			document.body.addEventListener( 'is.post-load', function () {
				processSingleImageGallery();
			} );
		}

		// Makes carousel work on page load and when back button leads to same URL with carousel hash
		// (i.e. no actual document.ready trigger).
		window.addEventListener( 'hashchange', function () {
			var hashRegExp = /jp-carousel-(\d+)/;

			if ( ! window.location.hash || ! hashRegExp.test( window.location.hash ) ) {
				if ( carousel.isOpen ) {
					closeCarousel();
				}

				return;
			}

			if ( window.location.hash === lastKnownLocationHash && carousel.isOpen ) {
				return;
			}

			if ( window.location.hash && carousel.gallery && ! carousel.isOpen && history.back ) {
				history.back();
				return;
			}

			lastKnownLocationHash = window.location.hash;
			var matchList = window.location.hash.match( hashRegExp );
			var attachmentId = parseInt( matchList[ 1 ], 10 );
			var galleries = document.querySelectorAll( gallerySelector );

			// Find the first thumbnail that matches the attachment ID in the location
			// hash, then open the gallery that contains it.
			for ( var i = 0; i < galleries.length; i++ ) {
				var gallery = galleries[ i ];
				var selected;

				var images = gallery.querySelectorAll( 'img' );
				for ( var j = 0; j < images.length; j++ ) {
					if (
						parseInt( images[ j ].getAttribute( 'data-attachment-id' ), 10 ) === attachmentId ||
						parseInt( images[ j ].getAttribute( 'data-id' ), 10 ) === attachmentId
					) {
						selected = j;
						break;
					}
				}

				if ( selected !== undefined ) {
					openOrSelectSlide( gallery, selected );
					break;
				}
			}
		} );

		if ( window.location.hash ) {
			domUtil.emitEvent( window, 'hashchange' );
		}
	}

	if ( document.readyState !== 'loading' ) {
		init();
	} else {
		document.addEventListener( 'DOMContentLoaded', init );
	}
} )();
;
( function () {
	function TiledGalleryCollection() {
		this.galleries = [];
		this.findAndSetupNewGalleries();
	}

	TiledGalleryCollection.prototype.findAndSetupNewGalleries = function () {
		var self = this;
		var unresizedGalleries = document.querySelectorAll( '.tiled-gallery.tiled-gallery-unresized' );

		Array.prototype.forEach.call( unresizedGalleries, function ( el ) {
			self.galleries.push( new TiledGallery( el ) );
		} );
	};

	TiledGalleryCollection.prototype.resizeAll = function () {
		Array.prototype.forEach.call( this.galleries, function ( gallery ) {
			gallery.resize();
		} );
	};

	function TiledGallery( galleryElem ) {
		this.gallery = galleryElem;

		this.addCaptionEvents();

		// Resize when initialized to fit the gallery to window dimensions
		this.resize();

		// Displays the gallery and prevents it from being initialized again
		this.gallery.classList.remove( 'tiled-gallery-unresized' );
	}

	/**
	 * Selector for all resizeable elements inside a Tiled Gallery
	 */

	TiledGallery.prototype.resizeableElementsSelector =
		'.gallery-row, .gallery-group, .tiled-gallery-item img';

	/**
	 * Story
	 */

	TiledGallery.prototype.addCaptionEvents = function () {
		// Hide captions
		var galleryCaptions = this.gallery.querySelectorAll( '.tiled-gallery-caption' );
		Array.prototype.forEach.call( galleryCaptions, function ( el ) {
			el.style.display = 'none';
		} );

		var mouseHoverHandler = function ( e ) {
			var itemEl = e.target.closest( '.tiled-gallery-item' );
			var displayValue = 'mouseover' === e.type ? 'block' : 'none';

			if ( itemEl ) {
				var itemCaption = itemEl.querySelector( '.tiled-gallery-caption' );
				if ( itemCaption ) {
					itemCaption.style.display = displayValue;
				}
			}
		};

		// Add hover effects to bring the caption up and down for each item
		this.gallery.addEventListener( 'mouseover', mouseHoverHandler );
		this.gallery.addEventListener( 'mouseout', mouseHoverHandler );
	};

	TiledGallery.prototype.getExtraDimension = function ( el, attribute, mode ) {
		if ( mode === 'horizontal' ) {
			var left = attribute === 'border' ? 'borderLeftWidth' : attribute + 'Left';
			var right = attribute === 'border' ? 'borderRightWidth' : attribute + 'Right';
			return ( parseInt( el.style[ left ], 10 ) || 0 ) + ( parseInt( el.style[ right ], 10 ) || 0 );
		} else if ( mode === 'vertical' ) {
			var top = attribute === 'border' ? 'borderTopWidth' : attribute + 'Top';
			var bottom = attribute === 'border' ? 'borderBottomWidth' : attribute + 'Bottom';
			return ( parseInt( el.style[ top ], 10 ) || 0 ) + ( parseInt( el.style[ bottom ], 10 ) || 0 );
		} else {
			return 0;
		}
	};

	TiledGallery.prototype.resize = function () {
		// Resize everything in the gallery based on the ratio of the current content width
		// to the original content width;
		var originalWidth = parseInt( this.gallery.dataset.originalWidth, 10 );
		var currentWidth = parseFloat(
			getComputedStyle( this.gallery.parentNode, null ).width.replace( 'px', '' )
		);
		var resizeRatio = Math.min( 1, currentWidth / originalWidth );

		var self = this;
		var resizableElements = this.gallery.querySelectorAll( this.resizeableElementsSelector );
		Array.prototype.forEach.call( resizableElements, function ( el ) {
			var marginWidth = self.getExtraDimension( el, 'margin', 'horizontal' );
			var marginHeight = self.getExtraDimension( el, 'margin', 'vertical' );

			var paddingWidth = self.getExtraDimension( el, 'padding', 'horizontal' );
			var paddingHeight = self.getExtraDimension( el, 'padding', 'vertical' );

			var borderWidth = self.getExtraDimension( el, 'border', 'horizontal' );
			var borderHeight = self.getExtraDimension( el, 'border', 'vertical' );

			// Take all outer dimensions into account when resizing so that images
			// scale with constant empty space between them
			var outerWidth =
				parseInt( el.dataset.originalWidth, 10 ) + paddingWidth + borderWidth + marginWidth;
			var outerHeight =
				parseInt( el.dataset.originalHeight, 10 ) + paddingHeight + borderHeight + marginHeight;

			// Subtract margins so that images don't overflow on small browser windows
			el.style.width = Math.floor( resizeRatio * outerWidth ) - marginWidth + 'px';
			el.style.height = Math.floor( resizeRatio * outerHeight ) - marginHeight + 'px';
		} );
	};

	/**
	 * Resizing logic
	 */

	var requestAnimationFrame =
		window.requestAnimationFrame ||
		window.mozRequestAnimationFrame ||
		window.webkitRequestAnimationFrame ||
		window.msRequestAnimationFrame;

	function attachResizeInAnimationFrames( tiledGalleries ) {
		var resizing = false;
		var resizeTimeout = null;

		function handleFrame() {
			tiledGalleries.resizeAll();
			if ( resizing ) {
				requestAnimationFrame( handleFrame );
			}
		}

		window.addEventListener( 'resize', function () {
			clearTimeout( resizeTimeout );

			if ( ! resizing ) {
				requestAnimationFrame( handleFrame );
			}
			resizing = true;
			resizeTimeout = setTimeout( function () {
				resizing = false;
			}, 15 );
		} );
	}

	function attachPlainResize( tiledGalleries ) {
		window.addEventListener( 'resize', function () {
			tiledGalleries.resizeAll();
		} );
	}

	/**
	 * Ready, set...
	 */
	function ready( fn ) {
		if ( document.readyState !== 'loading' ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}
	ready( function () {
		var tiledGalleries = new TiledGalleryCollection();

		document.body.addEventListener( 'is.post-load', function () {
			tiledGalleries.findAndSetupNewGalleries();
		} );

		if ( typeof jQuery === 'function' ) {
			jQuery( document ).on( 'page-rendered.wpcom-newdash', function () {
				tiledGalleries.findAndSetupNewGalleries();
			} );
		}

		// Chrome is a unique snow flake and will start lagging on occasion
		// It helps if we only resize on animation frames
		//
		// For other browsers it seems like there is no lag even if we resize every
		// time there is an event
		if ( window.chrome && requestAnimationFrame ) {
			attachResizeInAnimationFrames( tiledGalleries );
		} else {
			attachPlainResize( tiledGalleries );
		}

		if ( 'undefined' !== typeof wp && wp.customize && wp.customize.selectiveRefresh ) {
			wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function ( placement ) {
				if ( wp.isJetpackWidgetPlaced( placement, 'gallery' ) ) {
					tiledGalleries.findAndSetupNewGalleries();
				}
			} );
		}
	} );
} )();
;
( function () {
	'use strict';

	if ( typeof window.wpcom === 'undefined' ) {
		window.wpcom = {};
	}

	if ( window.wpcom.carousel ) {
		return;
	}

	var prebuilt_widths = jetpackCarouselStrings.widths;
	var pageviews_stats_args = jetpackCarouselStrings.stats_query_args;

	var findFirstLargeEnoughWidth = function ( original_w, original_h, dest_w, dest_h ) {
		var inverse_ratio = original_h / original_w;

		for ( var i = 0; i < prebuilt_widths.length; ++i ) {
			if ( prebuilt_widths[ i ] >= dest_w || prebuilt_widths[ i ] * inverse_ratio >= dest_h ) {
				return prebuilt_widths[ i ];
			}
		}

		return original_w;
	};

	var removeResizeFromImageURL = function ( url ) {
		return removeArgFromURL( url, 'resize' );
	};

	var removeArgFromURL = function ( url, arg ) {
		var re = new RegExp( '[\\?&]' + arg + '(=[^?&]+)?' );
		if ( url.match( re ) ) {
			return url.replace( re, '' );
		}
		return url;
	};

	var addWidthToImageURL = function ( url, width ) {
		width = parseInt( width, 10 );
		// Give devices with a higher devicePixelRatio higher-res images (Retina display = 2, Android phones = 1.5, etc)
		if ( 'undefined' !== typeof window.devicePixelRatio && window.devicePixelRatio > 1 ) {
			width = Math.round( width * window.devicePixelRatio );
		}
		url = addArgToURL( url, 'w', width );
		url = addArgToURL( url, 'h', '' );
		return url;
	};

	var addArgToURL = function ( url, arg, value ) {
		var re = new RegExp( arg + '=[^?&]+' );
		if ( url.match( re ) ) {
			return url.replace( re, arg + '=' + value );
		} else {
			var divider = url.indexOf( '?' ) !== -1 ? '&' : '?';
			return url + divider + arg + '=' + value;
		}
	};

	var stat = function ( names ) {
		if ( typeof names !== 'string' ) {
			names = names.join( ',' );
		}

		new Image().src = window.location.protocol +
			'//pixel.wp.com/g.gif?v=wpcom-no-pv' +
			'&x_carousel=' + names +
			'&baba=' + Math.random();
	};

	var pageview = function ( post_id ) {
		new Image().src = window.location.protocol +
			'//pixel.wp.com/g.gif?host=' + encodeURIComponent( window.location.host ) +
			'&ref=' + encodeURIComponent( document.referrer ) +
			'&rand=' + Math.random() +
			'&' + pageviews_stats_args +
			'&post=' + encodeURIComponent( post_id );
	};

	var generateImgSrc = function ( srcItem, max ) {
		var origSize = srcItem.getAttribute( 'data-orig-size' ) || '';

		var src = srcItem.getAttribute( 'src' ) || srcItem.getAttribute( 'original' ) || srcItem.getAttribute( 'data-original' ) || srcItem.getAttribute( 'data-lazy-src' );
		if ( src.indexOf( 'imgpress' ) !== -1 ) {
			src = srcItem.getAttribute( 'data-orig-file' );
		}
		// Square/Circle galleries use a resize param that needs to be removed.
		src = removeResizeFromImageURL( src );
		src = addWidthToImageURL(
			src,
			findFirstLargeEnoughWidth( origSize.width, origSize.height, max.width, max.height )
		);

		return src;
	};

	window.wpcom.carousel = {
		findFirstLargeEnoughWidth: findFirstLargeEnoughWidth,
		removeResizeFromImageURL: removeResizeFromImageURL,
		addWidthToImageURL: addWidthToImageURL,
		stat: stat,
		pageview: pageview,
		generateImgSrc: generateImgSrc
	};

} )();
;
