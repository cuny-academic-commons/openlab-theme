<?php
/*
 * Combined because they're on a single line in the interface.
 */

$is_open      = openlab_get_current_filter( 'open' );
$is_cloneable = openlab_get_current_filter( 'cloneable' );

// Cloneable should not appear if the content type is not cloneable.
// @todo search-results
$show_cloneable = false;
if ( bp_is_groups_directory() ) {
	$group_type        = bp_get_current_group_directory_type();
	$group_type_object = cboxol_get_group_type( $group_type );
	if ( ! is_wp_error( $group_type_object ) && $group_type_object->get_can_be_cloned() ) {
		$show_cloneable = true;
	}
}
?>

<div class="sidebar-filter sidebar-filter-open-cloneable">
	<div class="form-group">
		<div class="sidebar-filter-checkbox">
			<input type="checkbox" name="is_open" id="checkbox-is-open" <?php checked( $is_open ); ?> value="1" /> <label for="checkbox-is-open"><?php esc_html_e( 'Open', 'openlab-theme' ); ?></label>
		</div>

		<?php if ( $show_cloneable ) : ?>
			<div class="sidebar-filter-checkbox">
				<input type="checkbox" name="is_cloneable" id="checkbox-is-cloneable" <?php checked( $is_cloneable ); ?> value="1" /> <label for="checkbox-is-cloneable"><?php esc_html_e( 'Cloneable', 'openlab-theme' ); ?></label>
			</div>
		<?php endif; ?>
	</div>
</div>
