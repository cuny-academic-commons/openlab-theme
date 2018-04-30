(function($){
	var $errorFormat, $errorTaken, $nameField, $urlField, $urlFieldParent;
	var isValid, isTaken;

	$(document).ready(function(){
		isValid = isAvailable = false;
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
				$urlField.trigger('blur').trigger('keyup');
			}
		} );

		// Format validation on keyup.
		$urlField.on( 'keyup', function() {
			var url = $urlField.val();

			// Force URL to lowercase.
			url = url.toLowerCase();
			$urlField.val( url );

			var hasIllegalCharacters = url.match( /[^a-z0-9\-_]/ );
			var hasLegalStart = url.match( /^[a-z0-9]/ );
			var hasLegalEnd = url.match( /[a-z0-9]$/ );
			var hasEnoughCharacters = url.length >= 3;

			if ( ! hasLegalStart || ! hasLegalEnd || hasIllegalCharacters || ! hasEnoughCharacters ) {
				isValid = false;
				$errorFormat.removeAttr( 'aria-hidden' );
			} else {
				isValid = true;
				$errorFormat.attr( 'aria-hidden', 'true' );
			}
			toggleError();
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
					groupId: $('#group_id').val(),
					name: $('group-name').val(),
					url: $urlField.val()
				},
				success: function( response ) {
					$urlFieldParent.removeClass('ajax-in-progress');
					$ajaxStatus.removeClass('fa-spinner fa-pulse');
					if ( response.success ) {
						isAvailable = true;
						$errorTaken.attr( 'aria-hidden', 'true' );
					} else {
						isAvailable = false;
						$errorTaken.removeAttr( 'aria-hidden' );
					}
					toggleError();
				}
			} );
		});

		var toggleError = function() {
			if ( isAvailable && isValid ) {
				$urlFieldParent.removeClass('has-error').removeClass('ajax-error');
				$ajaxStatus.addClass('fa-check').removeClass('fa-exclamation-circle');
			} else {
				$urlFieldParent.addClass('has-error').addClass('ajax-error');
				$ajaxStatus.removeClass('fa-check').addClass('fa-exclamation-circle');
			}
		}
	});
}(jQuery));
