<?php
/*
 * Combined because they're on a single line in the interface.
 */

$is_open      = openlab_get_current_filter( 'open' );
$is_cloneable = openlab_get_current_filter( 'cloneable' );

?>

<div class="sidebar-filter sidebar-filter-open-cloneable">
	<div class="form-group">
		<div class="sidebar-filter-checkbox">
			<input type="checkbox" name="is_open" id="checkbox-is-open" <?php checked( $is_open ); ?> value="1" /> <label for="checkbox-is-open"><?php esc_html_e( 'Open', 'openlab-theme' ); ?></label>
		</div>

		<div class="sidebar-filter-checkbox">
			<input type="checkbox" name="is_cloneable" id="checkbox-is-cloneable" <?php checked( $is_cloneable ); ?> value="1" /> <label for="checkbox-is-cloneable"><?php esc_html_e( 'Cloneable', 'openlab-theme' ); ?></label>
		</div>
	</div>
</div>
