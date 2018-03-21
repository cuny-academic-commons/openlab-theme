( function ( exports, $ ) {
	"use strict";

	wp.customize.bind( 'ready', function() {
		wp.customize( 'openlab_color_scheme', function( setting ) {
			$( '#customize-control-openlab_color_scheme label' ).each( function( index, el ) {
				var $el = $(el);
				var colorScheme = $el.find('input').val();
				$el.addClass( 'color-scheme-' + colorScheme );
			} );
		} );

		wp.editor.initialize( 'openlab_footer_left' );
		wp.editor.initialize( 'openlab_footer_right' );
	} );
} )( wp, jQuery );
