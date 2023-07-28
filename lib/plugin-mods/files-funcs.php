<?php
/**
 * Buddypress Group Documents functions
 * These functions are clones of those found in the BuddyPress Group Documents plugin
 * They are duplicated here so that Bootstrap markup can be injected for uniform styling
 */

/**
 * Dequeue inherit styling from plugin
 */
function openlab_dequeue_bp_files_styles() {
	wp_dequeue_style( 'bp-group-documents' );
}
add_action( 'wp_print_styles', 'openlab_dequeue_bp_files_styles', 999 );

/**
 * Custom file pagination
 * Pulled from BP_Group_Documents_Template->do_paging_logic()
 *
 * @global type $wpdb
 * @global type $bp
 */
function openlab_get_files_count() {
	global $wpdb, $bp;

	$start_record = 1;
	$page         = 1;

	$group_id = bp_get_group_id();
	$table    = BP_GROUP_DOCUMENTS_TABLE;

	$sql = "SELECT COUNT(*) FROM {$table} WHERE group_id = %d ";

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$total_records = $wpdb->get_var( $wpdb->prepare( $sql, $group_id ) );

	$items_per_page = get_option( 'bp_group_documents_items_per_page' );
	$total_pages    = ceil( $total_records / $items_per_page );

	// phpcs:disable WordPress.Security.NonceVerification
	if ( isset( $_GET['page'] ) && ctype_digit( $_GET['page'] ) ) {
		$page         = $_GET['page'];
		$start_record = ( ( $page - 1 ) * $items_per_page ) + 1;
	}
	// phpcs:enable WordPress.Security.NonceVerification

	$last_possible = $items_per_page * $page;
	$end_record    = ( $total_records < $last_possible ) ? $total_records : $last_possible;

	// translators: 1. pagination start number, 2. pagination end number, 3. total item count
	echo esc_html( sprintf( __( 'Viewing item %1$s to %1$s (of %1$s items)', 'commons-in-a-box' ), $start_record, $end_record, $total_records ) );
}

/**
 * Buddypress Group Documents is very secretive about it's pagination, so we'll
 * have to do this with some str_replace fun
 *
 * @param type $template
 */
function openlab_bp_group_documents_custom_pagination_links( $template ) {

	// dump the echoed legacy pagination into a string
	ob_start();
	$template->pagination_links();
	$legacy_pag = ob_get_clean();

	// redesign
	$legacy_pag = str_replace( array( '<span' ), '<li><span', $legacy_pag );
	$legacy_pag = str_replace( array( '</span>' ), '</li></span>', $legacy_pag );
	$legacy_pag = str_replace( array( '<a' ), '<li><a', $legacy_pag );
	$legacy_pag = str_replace( array( '</a>' ), '</li></a>', $legacy_pag );

	$legacy_pag = str_replace( 'page-numbers', 'page-numbers pagination', $legacy_pag );

	$legacy_pag = str_replace( '&raquo;', '<i class="fa fa-angle-right"></i>', $legacy_pag );
	$legacy_pag = str_replace( '&laquo;', '<i class="fa fa-angle-left"></i>', $legacy_pag );

	return $legacy_pag;
}

/**
 * Disables the bp-group-documents step in the group creation process.
 *
 * This is a hack based on the fact that bp-group-documents doesn't show the
 * step if mods are not allowed to configure this setting.
 */
function openlab_disable_bp_group_documents_group_creation_step( $value ) {
	if ( ! bp_is_group_create() ) {
		return $value;
	}

	return '';
}
add_filter( 'option_bp_group_documents_upload_permission', 'openlab_disable_bp_group_documents_group_creation_step' );

/**
 * Disable BP Group Documents category feature for the time being.
 */
add_filter( 'option_bp_group_documents_use_categories', '__return_false' );

/**
 * Filter the success language.
 */
add_filter(
	'bp_core_render_message_content',
	function( $message ) {
		$old = __( 'Document successfully uploaded', 'commons-in-a-box' );
		$new = __( 'File successfully uploaded', 'commons-in-a-box' );
		return str_replace( $old, $new, $message );
	}
);

// Don't force Files to be active on all groups.
add_filter( 'pre_option_bp_group_documents_enable_all_groups', '__return_zero' );

/**
 * Checks whether Files tab is enabled for a group.
 *
 * @param int $group_id Group id.
 * @return bool
 */
function openlab_is_files_enabled_for_group( $group_id = null ) {
	if ( null === $group_id ) {
		$group_id = bp_get_current_group_id();
	}

	// Default to true in case no value is found.
	if ( ! $group_id ) {
		return true;
	}

	$is_disabled = groups_get_groupmeta( $group_id, 'group_documents_documents_disabled' );

	return empty( $is_disabled );
}

/**
 * Disable bp-group-documents email notifications.
 *
 * We use the ones triggered by BPGES.
 *
 * @since 1.3.0
 */
remove_action( 'groups_screen_notification_settings', 'bp_group_documents_screen_notification_settings' );
remove_action( 'bp_group_documents_add_success', 'bp_group_documents_email_notification', 10 );

/**
 * Email notification management.
 *
 * @since 1.3.0
 *
 * @param bool   $send_it  Whether the notification should be sent.
 * @param object $activity Activity object.
 * @param int    $user_id  ID of the user.
 * @param string $sub      Subscription level of the user.
 * @return bool
 */
// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed
function openlab_files_activity_notification_control( $send_it, $activity, $user_id, $sub ) {
	if ( ! $send_it ) {
		return $send_it;
	}

	switch ( $activity->type ) {
		case 'deleted_group_document':
			return false;

		case 'added_group_document':
		case 'edited_group_document':
			return openlab_notify_group_members_of_this_action() && 'no' !== $sub;

		default:
			return $send_it;
	}
}
add_action( 'bp_ass_send_activity_notification_for_user', 'openlab_files_activity_notification_control', 100, 4 );
add_action( 'bp_ges_add_to_digest_queue_for_user', 'openlab_files_activity_notification_control', 100, 4 );

/**
 * Delete bp-group-documents category cookie, which messes with our navigation.
 */
function openlab_delete_bpgd_category_cookie_on_load() {
	unset( $_COOKIE['bp-group-documents-category'] );
}
add_action( 'bp_actions', 'openlab_delete_bpgd_category_cookie_on_load' );

/**
 * Catch POST request for link create/edit.
 */
add_action(
	'bp_group_documents_template_do_post_action',
	function() {
		$request_type = ! empty( $_POST['bp_group_documents_file_type'] ) ? wp_unslash( $_POST['bp_group_documents_file_type'] ) : 'upload';

		// If request is not of type 'link', let buddypress-group-documents handle it.
		if ( 'link' !== $request_type ) {
			return;
		}

		// For 'link' requests, we do not want buddypress-group-documents to process the
		// form. So we unset the 'bp_group_documents_operation' flag, which short-circuits
		// BP_Group_Documents_Template::do_post_logic().
		if ( ! empty( $_POST['bp_group_documents_operation'] ) && 'edit' === $_POST['bp_group_documents_operation'] ) {
			$operation_type = 'edit';
		} else {
			$operation_type = 'add';
		}

		check_admin_referer( 'bp_group_document_save_' . $operation_type, 'bp_group_document_save' );

		unset( $_POST['bp_group_documents_operation'] );

		switch ( $operation_type ) {
			case 'add' :
				$document              = new BP_Group_Documents();
				$document->user_id     = get_current_user_id();
				$document->group_id    = bp_get_current_group_id();
				$document->name        = wp_unslash( $_POST['bp_group_documents_link_name'] );
				$document->description = $_POST['bp_group_documents_link_description'];
				$document->file        = $_POST['bp_group_documents_link_url'];

				// false means "don't check for a file upload".
				if ( $document->save( false ) ) {
					openlab_update_external_link_category( $document );
					do_action( 'bp_group_documents_add_success', $document );
					bp_core_add_message( __( 'External link successfully added.', 'bp-group-documents' ) );
				}
				break;
			case 'edit' :
				$document              = new BP_Group_Documents( $_POST['bp_group_documents_id'] );
				$document->name        = wp_unslash( $_POST['bp_group_documents_link_name'] );
				$document->description = $_POST['bp_group_documents_link_description'];

				if ( $document->save( false ) ) {
					openlab_update_external_link_category( $document );
					do_action( 'bp_group_documents_edit_success', $document );
					bp_core_add_message( __( 'External link successfully edited', 'bp-group-documents' ) );
				}
				break;
		}
	}
);

/**
 * Update `file` column for the external links saved
 * in the documents table.
 *
 * BP_Group_Documents::save()
 */
add_action(
	'bp_group_documents_data_after_save',
	function( $document ) {
		// Get the operation type, used to build the nonce.
		$operation_type = ! empty( $_POST['bp_group_documents_operation'] ) && 'edit' === $_POST['bp_group_documents_operation'] ? 'edit' : 'add';

		check_admin_referer( 'bp_group_document_save_' . $operation_type, 'bp_group_document_save' );

		$request_type = ! empty( $_POST['bp_group_documents_file_type'] ) ? wp_unslash( $_POST['bp_group_documents_file_type'] ) : 'upload';

		// If request is not of type 'link', let buddypress-group-documents handle it.
		if ( 'link' !== $request_type ) {
			return;
		}

		$result = null;
		if ( $document->id ) {
			global $wpdb, $bp;

			$result = $wpdb->query(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"UPDATE {$bp->group_documents->table_name}
				SET
					file = %s
				WHERE id = %d",
					$_POST['bp_group_documents_link_url'],
					$document->id
				)
			);
		}

		if ( ! $result ) {
			return false;
		}

		return $result;
	}
);

/**
 * Set categories for the external link submitted from the
 * group documents form.
 *
 * @param BP_Group_Documents $document Document object.
 */
function openlab_update_external_link_category( $document ) {
	// Get the operation type, used to build the nonce.
	$operation_type = ! empty( $_POST['bp_group_documents_operation'] ) && 'edit' === $_POST['bp_group_documents_operation'] ? 'edit' : 'add';

	check_admin_referer( 'bp_group_document_save_' . $operation_type, 'bp_group_document_save' );

	// Update categories from checkbox list.
	if ( isset( $_POST['bp_group_documents_link_categories'] ) ) {
		$category_ids = apply_filters( 'bp_group_documents_category_ids_in', $_POST['bp_group_documents_link_categories'] );
	}

	if ( isset( $category_ids ) ) {
		wp_set_object_terms( $document->id, $category_ids, 'group-documents-category' );
	}

	// Check if new category was added, if so, append to current list.
	if ( isset( $_POST['bp_group_documents_link_new_category'] ) && $_POST['bp_group_documents_link_new_category'] ) {

		$parent_id = \BP_Group_Documents_Template::get_parent_category_id();

		if ( ! term_exists( $_POST['bp_group_documents_link_new_category'], 'group-documents-category', $parent_id ) ) {
			$term_info = wp_insert_term( $_POST['bp_group_documents_link_new_category'], 'group-documents-category', array( 'parent' => $parent_id ) );
			wp_set_object_terms( $document->id, $term_info['term_id'], 'group-documents-category', true );
		}
	}
}

/**
 * Catch folder delete request.
 */
add_action(
	'bp_actions',
	function() {
		if ( ! bp_is_group() || ! bp_is_current_action( BP_GROUP_DOCUMENTS_SLUG ) ) {
			return;
		}

		if ( ! bp_is_action_variable( 'delete-folder' ) ) {
			return;
		}

		$folder_id = (int) bp_action_variable( 1 );
		if ( ! $folder_id ) {
			return;
		}

		check_admin_referer( 'group-documents-delete-folder-link' );

		if ( ! bp_is_item_admin() ) {
			return;
		}

		wp_delete_term( $folder_id, 'group-documents-category' );

		bp_core_add_message( __( 'Folder successfully deleted.', 'commons-in-a-box' ) );

		bp_core_redirect( trailingslashit( bp_get_group_permalink( groups_get_current_group() ) . BP_GROUP_DOCUMENTS_SLUG ) );
		die;
	}
);


/**
 * Get BP document type by the filename url.
 *
 * @param string $file_name File name.
 * @return string 'link' or 'upload'.
 */
function openlab_get_document_type( $file_name ) {
	return filter_var( $file_name, FILTER_VALIDATE_URL ) ? 'link' : 'upload';
}

/**
 * Filters the URL of group documents to account for links.
 */
add_filter(
	'bp_group_documents_file_url',
	function( $document_url, $group_id, $file ) {
		if ( 'upload' === openlab_get_document_type( $file ) ) {
			return $document_url;
		}

		return $file;
	},
	10,
	3
);

/**
 * Render external link icon.
 *
 * @since 1.5.0
 *
 * @param string $url URL of the external link.
 * @return void
 */
function openlab_external_link_icon( $url ) {
	$url_parts = wp_parse_url( $url );

	if ( ! isset( $url_parts['host'] ) ) {
		return;
	}

	?>
	<a role="presentation" class="group-documents-icon" href="<?php echo esc_url( $url ); ?>" target="_blank">
		<img class="bp-group-documents-icon" src="<?php echo esc_url( get_template_directory_uri() . '/images/doc-icons/' . openlab_get_service_from_url( $url_parts['host'] ) ); ?>.png" alt="">
		<span class="sr-only"><?php esc_html_e( 'View document', 'commons-in-a-box' ); ?></span>
	</a>
	<?php
}

/**
 * Get external link service name from host.
 *
 * @param string $host Host name from parse_url().
 * @return string
 */
function openlab_get_service_from_url( $host ) {
	switch ( $host ) {
		case 'dropbox.com':
			return 'dropbox';

		case 'docs.google.com':
		case 'drive.google.com':
			return 'drive';

		case 'zoom.com':
		case 'zoom.us':
			return 'zoom';

		case '1drv.ms':
		case 'onedrive.live.com':
			return 'onedrive';

		default:
			return 'external';
	}
}
