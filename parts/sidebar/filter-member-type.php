<?php
$member_type_slug = urldecode( openlab_get_current_filter( 'member_type' ) );
?>

<div class="custom-select">
	<label for="portfolio-user-member-type-select" class="sr-only"><?php echo esc_html_e( 'Select: User Type', 'commons-in-a-box' ); ?></label>
	<select name="member_type" class="last-select" id="portfolio-user-member-type-select">
		<option value='' <?php selected( '', $member_type_slug ); ?>><?php esc_html_e( 'User Type', 'commons-in-a-box' ); ?></option>
		<?php foreach ( cboxol_get_member_types() as $member_type ) : ?>
			<option value='<?php echo esc_attr( $member_type->get_slug() ); ?>' <?php selected( $member_type->get_slug(), $member_type_slug ); ?>><?php echo esc_html( $member_type->get_label( 'singular' ) ); ?></option>
		<?php endforeach; ?>
		<option value='all' <?php selected( 'all', $member_type_slug ); ?>><?php esc_html_e( 'All', 'commons-in-a-box' ); ?></option>
	</select>
</div>
