<?php
/*
 * Combined because they're on a single line in the interface.
 */

$is_open      = openlab_get_current_filter( 'open' );
$is_cloneable = openlab_get_current_filter( 'cloneable' );

// 'Open' is not yet implemented. See https://github.com/cuny-academic-commons/commons-in-a-box/issues/227
$show_open = false;

// Cloneable should not appear if the content type is not cloneable.
$show_cloneable = false;
if ( bp_is_groups_directory() ) {
	$group_type        = bp_get_current_group_directory_type();
	$group_type_object = cboxol_get_group_type( $group_type );
	if ( ! is_wp_error( $group_type_object ) && $group_type_object->get_can_be_cloned() ) {
		$show_cloneable = true;
	}
} elseif ( openlab_is_search_results_page() ) {
	$show_cloneable = true;
}

// Don't render the element if there's nothing to show.
if ( ! $show_cloneable && ! $show_open ) {
	return;
}

?>

<div class="sidebar-filter sidebar-filter-open-cloneable">
	<div class="form-group">
		<?php if ( $show_open ) : ?>
			<div class="sidebar-filter-checkbox">
				<input type="checkbox" name="is_open" id="checkbox-is-open" <?php checked( $is_open ); ?> value="1" /> <label for="checkbox-is-open"><?php esc_html_e( 'Open', 'openlab-theme' ); ?></label>
			</div>
		<?php endif; ?>

		<?php if ( $show_cloneable ) : ?>
			<div class="sidebar-filter-checkbox">
				<input type="checkbox" name="is_cloneable" id="checkbox-is-cloneable" <?php checked( $is_cloneable ); ?> value="1" /> <label for="checkbox-is-cloneable"><?php esc_html_e( 'Cloneable', 'openlab-theme' ); ?></label>
			</div>
		<?php endif; ?>
	</div>
</div>
