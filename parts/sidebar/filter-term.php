<?php
$current_term = openlab_get_current_filter( 'term' );
?>

<div class="custom-select">
	<label for="course-term-select" class="sr-only"><?php echo esc_html_e( 'Select: Term', 'openlab-theme' ); ?></label>
	<select name="term" class="last-select" id="course-term-select">
		<option value='' <?php selected( '', $current_term ) ?>><?php esc_html_e( 'Term', 'openlab-theme' ); ?></option>
		<option value='term_all' <?php selected( 'term_all', $current_term ) ?>><?php esc_html_e( 'All', 'openlab-theme' ); ?></option>
		<?php foreach ( openlab_get_active_terms() as $term ) : ?>
			<option value="<?php echo esc_attr( $term ) ?>" <?php selected( $current_term, $term ) ?>><?php echo esc_attr( $term ) ?></option>
		<?php endforeach; ?>
	</select>
</div>
