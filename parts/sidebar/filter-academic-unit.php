<?php
$academic_unit_type = cboxol_get_academic_unit_type( get_query_var( 'academic_unit_type' ) );

$url_param     = 'academic-unit-' . $academic_unit_type->get_slug();
$current_unit  = isset( $_GET[ $url_param ] ) ? wp_unslash( $_GET[ $url_param ] ) : null;
$units_of_type = cboxol_get_academic_units( array(
	'type' => $academic_unit_type->get_slug(),
) );
?>

<div class="sidebar-filter custom-select academic-unit-type-select" id="academic-unit-type-select-<?php echo esc_attr( $academic_unit_type->get_slug() ); ?>">
	<label for="<?php echo esc_attr( $academic_unit_type->get_slug() ); ?>-select" class="sr-only"><?php echo esc_html( sprintf( __( 'Select: %s', 'commons-in-a-box' ), $academic_unit_type->get_name() ) ); ?></label>
	<select name="<?php echo esc_attr( $url_param ); ?>" class="last-select" id="<?php echo esc_attr( $academic_unit_type->get_slug() ); ?>-select" data-unittype="<?php echo esc_attr( $academic_unit_type->get_slug() ); ?>">
		<option class="academic-unit" value="" data-parent="" <?php selected( '', $current_unit ) ?>><?php echo esc_html( $academic_unit_type->get_name() ); ?></option>
		<option class="academic-unit" value="all" data-parent="" <?php selected( 'all', $current_unit ) ?>><?php esc_html_e( 'All', 'commons-in-a-box' ); ?></option>

		<?php foreach ( $units_of_type as $unit ) : ?>
			<option class="academic-unit academic-unit-nonempty" data-parent="<?php echo esc_html( $unit->get_parent() ); ?>" value='<?php echo esc_attr( $unit->get_slug() ); ?>' <?php selected( $unit->get_slug(), $current_unit ) ?>><?php echo esc_html( $unit->get_name() ); ?></option>
		<?php endforeach; ?>
	</select>
</div><!-- #academic-unit-type-select-<?php echo esc_html( $academic_unit_type->get_slug() ); ?> -->
