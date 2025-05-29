(function ( $, api ) {
	function waitForElement( id, callback, maxWait = 5000 ) {
		const start = Date.now();

		(function check() {
			const el = document.getElementById( id );
			if ( el ) {
				callback( el );
			} else if ( Date.now() - start < maxWait ) {
				requestAnimationFrame( check );
			}
		})();
	}

	$( document ).ready(
		function () {
			var fieldMap = {
				openlab_footer_left_heading: 'footer-left-heading',
				openlab_footer_left_content: 'footer-left-content',
				openlab_footer_middle_heading: 'footer-middle-heading',
				openlab_footer_middle_content: 'footer-middle-content'
			};

			for ( var settingId in fieldMap ) {
				var elementId = fieldMap[ settingId ];
				api(
					settingId,
					function ( value ) {
						var thisElId = elementId;
						value.bind(
							function ( newval ) {
								document.getElementById( thisElId ).innerHTML = newval;
							}
						);
					}
				);
			}
		}
	);

	if ( $( '.openlab-sitewide-notice' ).length === 0 ) {
		waitForElement( 'wpadminbar', function( wpAdminBar ) {
			const $noticeEl = $( '<div class="openlab-sitewide-notice"><span class="notice-text"></span><button class="notice-dismiss fa fa-times"><span class="screen-reader-text">Dismiss</span></button></div>' );

			const { colors } = window.openlabSitewideNotice
			const { background, text } = colors
			if ( background ) $noticeEl.css( 'background-color', background );
			if ( text ) $noticeEl.css( 'color', text );

			const noticeEl = $noticeEl[0]
			wpAdminBar.parentNode.insertBefore( noticeEl, wpAdminBar.nextSibling );

			// The top position should be the same as the admin bar plus the height of the admin bar.
			const adminBarHeight = wpAdminBar.offsetHeight;
			const adminBarTop = wpAdminBar.offsetTop;
			noticeEl.style.top = ( adminBarTop + adminBarHeight ) + 'px';
			noticeEl.style.position = 'absolute';
		} );
	}

	api( 'sitewide_notice_text', function( value ) {
		$( '.openlab-sitewide-notice .notice-text' ).html( value.get() );

		value.bind( function( newVal ) {
			$( '.openlab-sitewide-notice .notice-text' ).html( newVal );
		} );
	} );

	api( 'sitewide_notice_toggle', function( value ) {
		// Initial visibility
		if ( parseInt( value.get(), 10 ) === 1 ) {
			$( '.openlab-sitewide-notice' ).show();
			document.body.classList.add( 'has-sitewide-notice' );
		} else {
			$( '.openlab-sitewide-notice' ).hide();
			document.body.classList.remove( 'has-sitewide-notice' );
		}

		value.bind( function( newVal ) {
			if ( !! newVal ) {
				$( '.openlab-sitewide-notice' ).show();
				document.body.classList.add( 'has-sitewide-notice' );
			} else {
				$( '.openlab-sitewide-notice' ).hide();
				document.body.classList.remove( 'has-sitewide-notice' );
			}
		} );
	} );

	api( 'sitewide_notice_dismissable_toggle', function( value ) {
		if ( parseInt( value.get(), 10 ) === 1 ) {
			$( '.openlab-sitewide-notice .notice-dismiss' ).show();
		} else {
			$( '.openlab-sitewide-notice .notice-dismiss' ).hide();
		}

		value.bind( function( newVal ) {
			if ( !! newVal ) {
				$( '.openlab-sitewide-notice .notice-dismiss' ).show();
			} else {
				$( '.openlab-sitewide-notice .notice-dismiss' ).hide();
			}
		} );
	} );
})( jQuery, wp.customize );
