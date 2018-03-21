(function( $ ) {
	$(document).ready( function() {
		var fieldMap = {
			openlab_footer_left_heading: 'footer-left-heading',
			openlab_footer_left_content: 'footer-left-content',
		};

		for ( var settingId in fieldMap ) {
			var elementId = fieldMap[ settingId ];
			wp.customize( settingId, function( value ) {
				var thisElId = elementId;
				value.bind( function( newval ) {
					document.getElementById( thisElId ).innerHTML = newval;
				} );
			} );
		}
	} );
})(jQuery);
