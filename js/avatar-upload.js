window.bp = window.bp || {};

(function($){
	$(document).ready(function(){
		$('.avatar-upload-form').hide();

		$('#signup_submit').on('click', function(){
			window.onbeforeunload = null;
		});

		bp.Avatar.Attachment.on( 'change', function( data ) {
			$('#avatar-wrapper img').attr('src', data.attributes.url);
		} );

		bp.Uploader.filesQueue.on( 'reset', function() {
			$('.avatar-crop-submit').addClass('btn').addClass('btn-primary');
		} );
	});
}(jQuery));
