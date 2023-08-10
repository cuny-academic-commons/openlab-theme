<?php
$group_type  = bp_get_current_group_directory_type();
$group_terms = bpcgc_get_terms_by_group_type( $group_type );

$option_value_bpcgc = openlab_get_current_filter( 'cat' );
?>

<?php if ( $group_terms ) : ?>
	<div class="custom-select">
		<label for="bp-group-categories-select" class="sr-only"><?php echo esc_html_e( 'Select: Category', 'commons-in-a-box' ); ?></label>
		<select name="cat" class="last-select" id="bp-group-categories-select">
			<option value="" <?php selected( '', $option_value_bpcgc ); ?>><?php esc_html_e( 'Category', 'commons-in-a-box' ); ?></option>
			<option value='cat_all' <?php selected( 'cat_all', $option_value_bpcgc ); ?>><?php esc_html_e( 'All', 'commons-in-a-box' ); ?></option>
			<?php foreach ( $group_terms as $the_term ) : ?>
				<option value="<?php echo esc_attr( $the_term->slug ); ?>" <?php selected( $option_value_bpcgc, $the_term->slug ); ?>><?php echo esc_html( $the_term->name ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
<?php endif; ?>
