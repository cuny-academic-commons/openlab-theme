/* global jQuery */
(function($){
	$(document).ready(function(){
		if ( ! window.hasOwnProperty( 'openlabSitewideNotice' ) ) {
			return
		}

		// Bail if this is the customizer.
		const { wp } = window;
		if ( wp && wp.customize ) {
			return;
		}

		const {
			colors,
			dismissUrl,
			isDismissedForUser,
			noticeDismissable,
			noticeText,
			strings
		} = window.openlabSitewideNotice;

		if ( isDismissedForUser ) {
			return;
		}

		const { background, text } = colors;

		const noticeEl = document.createElement( 'div' );
		noticeEl.classList.add( 'openlab-sitewide-notice' );

		noticeEl.style.backgroundColor = background || '';
		noticeEl.style.color = text || '';

		const noticeElContent = document.createElement( 'span' );
		noticeElContent.classList.add( 'notice-text' );
		noticeElContent.innerHTML = noticeText;
		noticeEl.appendChild( noticeElContent );

		if ( noticeDismissable ) {
			const dismissButton = document.createElement( 'button' );
			dismissButton.classList.add( 'notice-dismiss', 'fa', 'fa-times' );

			const dismissButtonText = document.createElement( 'span' );
			dismissButtonText.classList.add( 'screen-reader-text' );
			dismissButtonText.innerHTML = strings.dismiss;

			dismissButton.appendChild( dismissButtonText );
			noticeEl.appendChild( dismissButton );

			noticeEl.classList.add( 'is-dismissable' );
		}

		// insert after #wpadminbar
		const wpAdminBar = document.getElementById( 'wpadminbar' );
		wpAdminBar.parentNode.insertBefore( noticeEl, wpAdminBar.nextSibling );

		// The top position should be the same as the admin bar plus the height of the admin bar.
		const adminBarHeight = wpAdminBar.offsetHeight;
		const adminBarTop = wpAdminBar.offsetTop;
		noticeEl.style.top = ( adminBarTop + adminBarHeight ) + 'px';
		noticeEl.style.position = 'absolute';

		document.body.classList.add( 'has-sitewide-notice' );

		$( noticeEl ).on( 'click', '.notice-dismiss', function() {
			$( noticeEl ).remove();
			document.body.classList.remove( 'has-sitewide-notice' );

			$.ajax( {
				url: dismissUrl.replace( /&amp;/g, '&' ), // Ensure the URL is properly formatted
				type: 'GET'
			} );
		} );
	});
})(jQuery);
