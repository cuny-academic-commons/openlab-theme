<?php
$current_group_types = openlab_get_current_filter( 'group-types' );

?>

<div class="sidebar-filter sidebar-filter-group-type">
	<div class="form-group">
		<?php foreach ( cboxol_get_group_types() as $group_type ) : ?>
			<div class="sidebar-filter-checkbox">
				<label for="checkbox-group-type-<?php echo esc_attr( $group_type->get_slug() ); ?>">
					<input type="checkbox" name="group-types[]" id="checkbox-group-type-<?php echo esc_attr( $group_type->get_slug() ); ?>" <?php checked( in_array( $group_type->get_slug(), $current_group_types, true ) ); ?> value="<?php echo esc_attr( $group_type->get_slug() ); ?>" /> <?php echo esc_html( $group_type->get_name() ); ?>
				</label>
			</div>
		<?php endforeach; ?>
	</div>
</div>
