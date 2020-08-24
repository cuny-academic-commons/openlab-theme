( function ( exports, $ ) {
	"use strict";

	wp.customize.bind(
		'ready',
		function() {
			wp.customize(
				'openlab_color_scheme',
				function( setting ) {
					$( '#customize-control-openlab_color_scheme label' ).each(
						function( index, el ) {
							var $el         = $( el );
							var colorScheme = $el.find( 'input' ).val();
							$el.addClass( 'color-scheme-' + colorScheme );
						}
					);
				}
			);

		}
	);

	$( window ).load(
		function(){
			var editorIds = [ 'openlab_footer_left_content', 'openlab_footer_middle_content' ];
			var setChange, textarea;
			for ( var i in editorIds ) {
				var editorId = editorIds[ i ];
				wp.editor.initialize( editorId, {
					tinymce: {
						wpautop: true
					},
					quicktags: true
				} );
			}
		}
	);

	$(document).on( 'tinymce-editor-init', function( event, editor ) {
		editor.on('change', function(e){
			tinyMCE.triggerSave();
			$('#'+editor.id).trigger('change');
		});
	});
} )( wp, jQuery );
