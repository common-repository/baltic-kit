/* global BalticKitl10n, ActiveXObject */
( function( $ ) {

	var balticKit = balticKit || {};

	balticKit.init = function() {

		// Cache dom
		this.isRtl 			= ( document.documentElement.dir === 'rtl' ) ? true : false ;

		this.recentlyViewed();
	};

	balticKit.ajax = function ( url, success ) {
	    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	    xhr.open('GET', url);
	    xhr.onreadystatechange = function() {
	        if (xhr.readyState > 3 && xhr.status === 200) {
	        	success(xhr.responseText);
	        }
	    };
	    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	    xhr.send();
	    return xhr;
	};

	balticKit.recentlyViewed = function() {

		var widgets, i;

		widgets = document.getElementsByClassName('baltic-kit__recently-viewed');

		if ( widgets ) {

			var ajaxCall = function( el, count ) {
				balticKit.ajax( BalticKitl10n.ajax_url + '?action=baltic_kit_recently_viewed_products&count=' + count, function( data ) {
					if( data ) {
						el.parentElement.style.display = 'block';
						el.innerHTML = data;
					} else {
						el.parentElement.style.display = 'none';
					}
				});
			};

			var loadView = function() {

				for ( i = 0; i < widgets.length; i++ ) {

					var widget 	= widgets[i];
					var count 	= widgets[i].getAttribute('data-count');

					ajaxCall( widget, count );

				}

			};

			window.addEventListener( 'load', loadView, false );

		}

	};

	/** Initialize balticKit.init() */
	$( function() {

		balticKit.init();

	    if ( 'undefined' === typeof wp || ! wp.customize || ! wp.customize.selectiveRefresh ) {
	        return;
	    }

	});

} )( jQuery );
