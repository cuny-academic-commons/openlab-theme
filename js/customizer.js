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

	} );

	$(window).load(function(){
			var editorIds = [ 'openlab_footer_left_content', 'openlab_footer_middle_content' ];
			var setChange, textarea;
			for ( var i in editorIds ) {
				var editorId = editorIds[ i ];
				wp.editor.initialize( editorId );

				textarea = document.getElementById( editorId );
				var editor = tinyMCE.get( editorId );

				// Catch changes to Visual.
				if ( editor ) {
					switchEditors.go( editorId, 'tmce' );

					editor.onChange.add( function(ed,e) {
						console.log(ed.id);
						ed.save();
						var newContent = ed.getContent();

						updateTextarea( ed.id, newContent );
					} );
				}

				// Catch changes to Text.
				$('#' + editorId).on( 'input propertychange', function( e ) {
					// Have to let the customizer catch up.
					var setChange;
					clearTimeout(setChange);
					setChange = setTimeout(function(){
						var newContent = $(e.target).val();
						updateTextarea( e.target.id, newContent );
					}, 1000);
				} );
			}
	});

	var updateTextarea = function( textareaId, content ) {
		var setChange;
		clearTimeout(setChange);
		setChange = setTimeout(function(){
			var $textareaInput = $( 'input[data-customize-setting-link="' + textareaId + '"]' );
			$textareaInput.val(content).trigger('change');
		}, 500);
	}
} )( wp, jQuery );
