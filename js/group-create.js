/**
 * This is the JavaScript related to group creation. It's loaded only during the group creation
 * process.
 */

function showHide(id) {
  var elem = document.getElementById(id);
  if ( !elem ){
          return;
  }

  var style = elem.style
   if (style.display == "none")
	style.display = "";
   else
	style.display = "none";
}

jQuery(document).ready(function($){
	var form,
		form_type,
		form_validated = false,
		new_group_type = CBOXOL_Group_Create.new_group_type,
		$body = $( 'body' ),
		$gc_submit = $( '#group-creation-create' ),
		$required_fields;

	if ( $body.hasClass( 'group-admin' ) ) {
		form_type = 'admin';
		form = document.getElementById( 'group-settings-form' );
	} else {
		form_type = 'create';
		form = document.getElementById( 'create-group-form' );
	}

	$form = $( form );

	$required_fields = $form.find( 'input:required' );

	function new_old_switch( noo ) {
		var radioid = '#new_or_old_' + noo;
		$(radioid).prop('checked','checked');

		$('.noo_radio').each(function(i,v) {
			var thisval = $(v).val();
			var thisid = '#noo_' + thisval + '_options';

			if ( noo == thisval) {
				$(thisid).find('input').each(function(index,element){
					$(element).removeClass('disabled-opt');
					$(element).removeProp('disabled').removeClass('disabled');
				});

				$(thisid).find('select').each(function(index,element){
					if ($(element).attr('type') !== 'radio') {
						$(element).removeClass('disabled-opt');
						$(element).removeProp('disabled').removeClass('disabled');
					}
				});

				//for external site note
				if ($(this).attr('id') === 'new_or_old_external'){
					$('#check-note').removeClass('disabled-opt');
					$('#wds-website-external #find-feeds').removeClass('disabled');
				}

			} else {
				$(thisid).find('input').each(function(index,element){
					if ($(element).attr('type') !== 'radio') {
						$(element).addClass('disabled-opt');
						$(element).prop('disabled','disabled').addClass('disabled');
					}
				});

				$(thisid).find('select').each(function(index,element){
					if ($(element).attr('type') !== 'radio') {
						$(element).addClass('disabled-opt');
						$(element).prop('disabled','disabled').addClass('disabled');
					}
				});

				//for external site note
				if ($(this).attr('id') === 'new_or_old_external'){
					$('#check-note').addClass('disabled-opt');
					$('#wds-website-external #find-feeds').addClass('disabled');
				}
			}
		});

		var efr = $('#external-feed-results');
		if ( 'external' == noo ) {
			$(efr).show();
		} else {
			$(efr).hide();
		}
	}

	function disable_gc_form() {
		$gc_submit.attr('disabled', 'disabled');
		$gc_submit.fadeTo( 500, 0.2 );
	}

	function enable_gc_form() {
		$gc_submit.removeAttr('disabled');
		$gc_submit.fadeTo( 500, 1.0 );
	}

	function mark_loading( obj ) {
		$(obj).before('<span class="loading" id="group-create-ajax-loader"></span>');
	}

	function unmark_loading( obj ) {
		var loader = $(obj).siblings('.loading');
		$(loader).remove();
	}

	function showHideAll() {
		showHide('wds-website');
		showHide('wds-website-existing');
		showHide('wds-website-external');
		showHide('wds-website-tooltips');
		showHide('wds-website-clone');
		showHide('check-note-wrapper');
	}

	function showHideAssociatedSitePrivacy() {
		var $associatedSitePrivacyPanel = $('#associated-site-privacy-panel');
		if ( setuptoggle.is(':checked') ) {
			$associatedSitePrivacyPanel.show();
		} else {
			$associatedSitePrivacyPanel.hide();
		}
	}

	function do_external_site_query(e) {
		var euf = $('#external-site-url');
		//var euf = e.target;
		var eu = $(euf).val();

		if ( 0 == eu.length ) {
			enable_gc_form();
			return;
		}

		disable_gc_form();
		mark_loading( $(e.target) );

		$.post( ajaxurl,
			{
				action: 'openlab_detect_feeds',
				'site_url': eu
			},
			function(response) {
				var robj = $.parseJSON(response);

				var efr = $('#external-feed-results');

				if ( 0 != efr.length ) {
					$(efr).empty(); // Clean it out
				} else {
					$('#wds-website-external').after( '<div id="external-feed-results"></div>' );
					efr = $('#external-feed-results');
				}

				if ( "posts" in robj ) {
					$(efr).append( '<p class="feed-url-tip">' + OLGroupCreate.strings.externalFeedsFound + '</p>' );
				} else {
					$(efr).append( '<p class="feed-url-tip">' + OLGroupCreate.strings.externalFeedsNotFound + '</p>' );
				}

				var posts = "posts" in robj ? robj.posts : '';
				var comments = "comments" in robj ? robj.comments : '';
				var type = "type" in robj ? robj.type : '';

				$(efr).append( '<p class="feed-url posts-feed-url"><label for="external-posts-url">' + OLGroupCreate.strings.externalFeedsFieldLabelPosts + '</label> <input name="external-posts-url" id="external-posts-url" type="text" class="form-control" value="' + posts + '" /></p>' );

				$(efr).append( '<p class="feed-url comments-feed-url"><label for="external-comments-url">' + OLGroupCreate.strings.externalFeedsFieldLabelComments + '</label> <input name="external-comments-url" id="external-comments-url" type="text" class="form-control" value="' + comments + '" /></p>' );

				$(efr).append( '<input name="external-site-type" id="external-site-type" type="hidden" value="' + type + '" />' );

				enable_gc_form();
				unmark_loading( $(e.target) );
			}
		);
	}

	function toggle_clone_options( on_or_off ) {
		var $group_to_clone, group_id_to_clone;

		$group_to_clone = $('#group-to-clone');

		if ( 'on' == on_or_off ) {
			// Check "Clone a course" near the top
			$('#create-or-clone-clone').attr('checked', true);

			// Allow a course to be selected from the source dropdown,
			// and un-grey the associated labels/text
			$group_to_clone.removeClass('disabled-opt');
			$group_to_clone.attr('disabled', false);
			$('#ol-clone-description').removeClass('disabled-opt');

			// Set up the site clone information
			group_id_to_clone = $group_to_clone.val();
			if ( ! group_id_to_clone ) {
				group_id_to_clone = $.urlParam( 'clone' );
			}

			// Ensure that the "Set up a site" section is visible
			if ( ! $(setuptoggle).is( ':checked' ) ) {
				$(setuptoggle).trigger('click');
			}
		} else {
			// Check "Create a course" near the top
			$('#create-or-clone-create').attr('checked', true);

			// Grey out options related to selecting a course to clone
			$group_to_clone.addClass('disabled-opt');
			$group_to_clone.attr('disabled', true);
			$('#ol-clone-description').addClass('disabled-opt');

			group_id_to_clone = 0;
		}

		fetch_clone_source_details( group_id_to_clone );
	}

	function fetch_clone_source_details( group_id ) {
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'openlab_group_clone_fetch_details',
				group_id: group_id
			},
			success: function( response ) {
				var r = JSON.parse( response );
				var $typeSelector;

				// Description
				$('#group-desc').val(r.description);
				$('#group-status-' + r.status).prop('checked',true);

				// Schools and Departments
				$.each( r.academic_units, function( unitType, unitsOfType ) {
					$typeSelector = $( '.cboxol-academic-unit-selector-for-type-' + unitType );

					$.each( unitsOfType, function( key, unitValue ) {
						$typeSelector.find( '#academic-unit-' + unitValue ).attr( 'checked', true ).trigger( 'change' );
					} );
				} );

				// Course Code
				$('#course-code').val(r.course_code);

				// Section Code
				$('#section-code').val(r.section_code);

				// Additional Description
				$('#additional-desc-html').val(r.additional_description);

				// Associated site
				if ( r.site_id ) {
					// Un-grey the website clone options
					$('#wds-website-clone .radio').removeClass('disabled-opt');
					$('#wds-website-clone input[name="new_or_old"]').removeAttr('disabled');

					// Auto-select the "Name your cloned site" option,
					// and trigger setup JS
					$('#new_or_old_clone').attr('checked', true);
					$('#new_or_old_clone').trigger('click');

					// Site URL
					$('#cloned-site-url').html( 'Your original address was: ' + r.site_url );
					$('#blog-id-to-clone').val( r.site_id );
				} else {
					// Grey out the website clone options
					$('#wds-website-clone .radio').addClass('disabled-opt');
					$('#wds-website-clone input[name="new_or_old"]').attr('disabled','disabled');

					// Pre-select "Create a new site"
					$('#new_or_old_new').attr('checked', true);
					$('#new_or_old_new').trigger('click');
				}

			}
		});
	}

	$('.noo_radio').click(function(el){
		var whichid = $(el.target).prop('id').split('_').pop();
		new_old_switch(whichid);
	});

	$.urlParam = function(name){
	    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
	    return results === null ? 0 : results[1];
	}

	// setup
	new_old_switch( 'new' );

	if ( CBOXOL_Group_Create.is_course ) {
		var $create_or_clone, create_or_clone, group_id_to_clone, new_create_or_clone;

		$create_or_clone = $('input[name="create-or-clone"]');

		// If not found, then the group already has an associated site - nothing to do.
		if ( $create_or_clone.length ) {
			create_or_clone = $create_or_clone.val();
			group_id_to_clone = $.urlParam( 'clone' );

			if ( group_id_to_clone ) {
				// Clone ID passed to URL
				toggle_clone_options( 'on' );
			} else {
				// No clone ID passed to URL
				toggle_clone_options( 'create' == create_or_clone ? 'off' : 'on' );
			}

			$create_or_clone.on( 'change', function() {
				new_create_or_clone = 'create' == $(this).val() ? 'off' : 'on';
				toggle_clone_options( new_create_or_clone );
			} );
		}
	}

	// Switching between groups to clone
	$('#group-to-clone').on('change', function() {
		fetch_clone_source_details( this.value );
	});

	/* AJAX validation for external RSS feeds */
	$('#find-feeds').on( 'click', function(e) {
		e.preventDefault();
		do_external_site_query(e);
	} );

	/* "Set up a site" toggle */
	var setuptoggle = $('input[name="set-up-site-toggle"]');
	$(setuptoggle).on( 'click', function(){
		showHideAll();
		showHideAssociatedSitePrivacy();
	} );
	if ( $(setuptoggle).is(':checked') ) {
		showHideAll();
	};
	showHideAssociatedSitePrivacy();

	if ( CBOXOL_Group_Create.enable_site_by_default && ! $(setuptoggle).is( ':checked' ) ) {
		$(setuptoggle).trigger('click');
	}

	// Set up Invite Anyone autocomplete
	if ( typeof ia_on_autocomplete_select !== 'undefined' ) {
		$('#send-to-input').autocomplete({
			serviceUrl: ajaxurl,
			width: 300,
			delimiter: /(,|;)\s*/,
			onSelect: ia_on_autocomplete_select,
			deferRequestBy: 300,
			params: { action: 'invite_anyone_autocomplete_ajax_handler' },
			noCache: true
		});
	}

	$( '.domain-validate' ).on( 'change', function() {
		form_validated = false;
	} );

	// Schools/Departments are required fields for Courses.
	// @todo this must be made dynamic
	$gc_submit.on( 'mouseover focus', function() {
		if ( CBOXOL_Group_Create.is_course ) {
			var school_tech = document.getElementById( 'school_tech' );
			var is_school_selected = $( '.school-inputs input:checked' ).length > 0;

			if ( null !== school_tech ) {
				school_tech.setCustomValidity( is_school_selected ? '' : 'You must select a School.' );
			}

			if ( is_school_selected ) {
				var is_department_selected = $( '.departments input:checked' ).length > 0;
				document.getElementsByClassName( 'wds-department' )[0].setCustomValidity( is_department_selected ? '' : 'You must select a Department.' );
			}
		}
	} );

	/**
	 * Form validation.
	 *
	 * - Site URL is validated by AJAX.
	 * - Name and Description use native validation.
	 */
	validate_form = function( event ) {
		event = ( event ? event : window.event );

		if ( form_validated ) {
			return true;
		}

		// Don't allow submission if avatar is not cropped.
		var $cropActions = $('#avatar-crop-actions');
		if ( $cropActions.length > 0 && $cropActions.is( ':visible' ) ) {
			$cropActions.after('<div class="ajax-warning bp-template-notice error">' + OLGroupCreate.strings.incompleteCrop + '</div>');
			$('html,body').animate({
				scrollTop: $('#avatar-panel').offset().top
			}, 1000);
			return false;
		}

		// If "Set up a site" is not checked, there's no validation to do
		if ( $( setuptoggle ).length && ! $( setuptoggle ).is( ':checked' ) ) {
			return true;
		}

		var new_or_old = $( 'input[name=new_or_old]:checked' ).val();
		var domain, $domain_field;

		// Different fields require different validation.
		switch ( new_or_old ) {
			case 'old' :

				// do something
				break;

			case 'clone' :
				$domain_field = $( '#clone-destination-path' );
				break;

			case 'new' :
				$domain_field = $( '#new-site-domain' );
				break;
		}

		if ( 'undefined' === typeof $domain_field ) {
			return true;
		}

		event.preventDefault();

		domain = $domain_field.val();

		var warn = $domain_field.siblings( '.ajax-warning' );
		if ( warn.length > 0 ) {
			warn.remove();
		}

		if ( 0 == domain.length ) {
			$domain_field.after('<div class="ajax-warning bp-template-notice error">' + OLGroupCreate.strings.fieldCannotBeBlank + '</div>');
			return false;
		}

		$.post( ajaxurl,
			{
				action: 'openlab_validate_groupblog_url_handler',
				'path': domain
			},
			function( response ) {
				if ( ! response.success ) {
					$domain_field.after('<div class="ajax-warning bp-template-notice error">' + response.data.error + '</div>');
					return false;
				} else {
					// We're done validating.
					form_validated = true;
					$form.append( '<input name="save" value="1" type="hidden" />' );
					$form.submit();
					return true;
				}
			}
		);
	};

	// Form validation.
	if( form ){
		form.onsubmit = validate_form;
	}
},(jQuery));
