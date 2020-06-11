<?php
$current_term = openlab_get_current_filter( 'term' );
?>

<div class="custom-select">
	<label for="course-term-select" class="sr-only"><?php echo esc_html_e( 'Select: Term', 'commons-in-a-box' ); ?></label>
	<select name="term" class="last-select" id="course-term-select">
		<option value='' <?php selected( '', $current_term ); ?>><?php esc_html_e( 'Term', 'commons-in-a-box' ); ?></option>
		<option value='term_all' <?php selected( 'term_all', $current_term ); ?>><?php esc_html_e( 'All', 'commons-in-a-box' ); ?></option>
		<?php foreach ( openlab_get_active_terms() as $the_term ) : ?>
			<option value="<?php echo esc_attr( $the_term ); ?>" <?php selected( $current_term, $the_term ); ?>><?php echo esc_attr( $the_term ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
