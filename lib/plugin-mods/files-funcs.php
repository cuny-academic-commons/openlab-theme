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
	global $bp;
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
	$page = 1;

	$group_id = bp_get_group_id();
	$table = BP_GROUP_DOCUMENTS_TABLE;

	$sql = "SELECT COUNT(*) FROM {$table} WHERE group_id = %d ";

	$total_records = $wpdb->get_var( $wpdb->prepare( $sql, $group_id ) );

	$items_per_page = get_option( 'bp_group_documents_items_per_page' );
	$total_pages = ceil( $total_records / $items_per_page );

	if ( isset( $_GET['page'] ) && ctype_digit( $_GET['page'] ) ) {
		$page = $_GET['page'];
		$start_record = (($page - 1) * $items_per_page) + 1;
	}

	$last_possible = $items_per_page * $page;
	$end_record = ($total_records < $last_possible) ? $total_records : $last_possible;

	printf( __( 'Viewing item %1$s to %1$s (of %1$s items)', 'bp-group-documents' ), $start_record, $end_record, $total_records );
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
add_filter( 'bp_core_render_message_content', function( $message ) {
	$old = __( 'Document successfully uploaded', 'bp-group-documents' );
	$new = __( 'File successfully uploaded', 'openlab-theme' );
	return str_replace( $old, $new, $message );
} );
