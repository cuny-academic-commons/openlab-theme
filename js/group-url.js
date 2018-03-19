(function($){
	var $errorFormat, $errorTaken, $nameField, $urlField, $urlFieldParent;

	$(document).ready(function(){
		$ajaxStatus = $('#group-url-status');
		$errorFormat = $('#url-error-format');
		$errorTaken = $('#url-error-taken');
		$nameField = $('#group-name');
		$urlField = $('#group-url');
		$urlFieldParent = $urlField.parent();

		// Suggest based on name.
		$nameField.on( 'blur', function() {
			// Don't bother with AJAX request, which is likely to be too slow for next field.
			if ( 0 === $urlField.val().length ) {
				var suggestedUrl = $nameField.val()
					.toLowerCase()										// lower case
					.replace( /\s+/g, '-' )						// spaces to hyphens
					.replace( /[^a-z0-9\-_]+/g, '' )	// remove other illegal chars
					.replace( /\-{2,}/g, '-' );				// resulting double-hyphens

				$urlField.val(suggestedUrl);
				$urlField.trigger('blur');
			}
		} );

		// Format validation on keyup.
		$urlField.on( 'keyup', function() {
			var url = $urlField.val();

			if ( url.match( /[^a-z0-9\-_]/ ) ) {
				$urlFieldParent.addClass('has-error');
				$errorFormat.removeAttr( 'aria-hidden' );
			} else {
				$urlFieldParent.removeClass('has-error');
				$errorFormat.attr( 'aria-hidden', 'true' );
			}
		});

		// Only run the AJAX request on blur.
		$urlField.on( 'blur', function() {
			// No value? Nothing to check.
			if ( 0 === $urlField.val().length ) {
				return;
			}

			$urlFieldParent.addClass('did-ajax').addClass('ajax-in-progress');
			$ajaxStatus
				.show()
				.removeClass( 'fa-exclamation-circle' )
				.removeClass( 'fa-check' )
				.addClass('fa-spinner fa-pulse');

			$.ajax( ajaxurl, {
				data: {
					action: 'openlab_group_url_validate',
					name: $('group-name').val(),
					url: $urlField.val()
				},
				success: function( response ) {
					$urlFieldParent.removeClass('ajax-in-progress');
					$ajaxStatus.removeClass('fa-spinner fa-pulse');
					if ( response.success ) {
						$urlFieldParent.removeClass('has-error').addClass('ajax-success');
						$ajaxStatus.removeClass('fa-exclamation-circle').addClass('fa-check');
						$errorTaken.attr( 'aria-hidden', 'true' );
					} else {
						$urlFieldParent.addClass('has-error').addClass('ajax-error');
						$ajaxStatus.removeClass('fa-check').addClass('fa-exclamation-circle');
						$errorTaken.removeAttr( 'aria-hidden' );
					}
				}
			} );
			var url = $urlField.val();
			console.log('blurred: ' + url);

		});
	});
	console.log('ok');
}(jQuery));
