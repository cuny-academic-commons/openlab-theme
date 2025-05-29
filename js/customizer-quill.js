wp.customize.bind( 'ready', function() {
	jQuery( '.quill-editor' ).each( function() {
		const $editor = jQuery( this );
		const editorId = $editor.attr( 'id' ); // _customize-input-sitewide_notice_text-quill

		// Strip out known prefixes/suffixes
		const settingId = editorId
			.replace( /^_customize-input-/, '' )
			.replace( /-quill$/, '' );

		const Keyboard = Quill.import('modules/keyboard');

		const quill = new Quill( this, {
			theme: 'snow',
			modules: {
				toolbar: [
					['bold', 'italic', 'underline', 'link']
				],
				keyboard: {
					bindings: {
						// Override default Enter behavior
						handleEnter: {
							key: 'Enter',
							handler: function() {
								// Do nothing on Enter
								return false;
							}
						}
					}
				}
			}
		} );

		// Set initial content
		const initialValue = wp.customize( settingId ).get();
		if ( initialValue ) {
			quill.clipboard.dangerouslyPasteHTML( initialValue );
		}

		// Sync Quill to setting
		quill.on( 'text-change', function() {
			const html = quill.root.innerHTML;
			wp.customize( settingId ).set( html );
		} );

		// Optional: respond to setting changes (external updates)
		wp.customize( settingId ).bind( function( value ) {
			if ( value !== quill.root.innerHTML ) {
				quill.clipboard.dangerouslyPasteHTML( value );
			}
		} );
	} );
} );
