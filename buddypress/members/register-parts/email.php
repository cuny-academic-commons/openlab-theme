<?php
/**
 * Email field for the signup form.
 *
 * @since 1.7.0
 */

$limited_email_domains_message = '';
$limited_email_domains         = get_site_option( 'limited_email_domains' );
if ( $limited_email_domains ) {
	$led = array();
	foreach ( $limited_email_domains as $d ) {
		$led[] = sprintf( '<span class="limited-email-domain">' . esc_html( $d ) . '</span>' );
	}
	$limited_email_domains_message = sprintf(
		// translators: list of allowed email domains
		esc_html__( 'Allowed email domains: %s', 'commons-in-a-box' ),
		implode( ', ', $led )
	);
}

?>

<div class="form-group">
	<label class="control-label" for="signup_email"><?php esc_html_e( 'Email Address (required)', 'commons-in-a-box' ); ?> <?php
	if ( $limited_email_domains_message ) :
		?>
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="email-requirements"><?php echo $limited_email_domains_message; ?></div><?php endif; ?></label>
	<?php do_action( 'bp_signup_email_errors' ); ?>
	<input
		class="form-control"
		type="text"
		name="signup_email"
		id="signup_email"
		value="<?php echo esc_attr( openlab_post_value( 'signup_email' ) ); ?>"
		data-parsley-trigger="blur"
		data-parsley-required
		data-parsley-type="email"
		data-parsley-group="email"
		data-parsley-iff="#signup_email_confirm"
		data-parsley-iff-message=""
	/>

	<label class="control-label" for="signup_email_confirm"><?php esc_html_e( 'Confirm Email Address (required)', 'commons-in-a-box' ); ?></label>
	<input
	class="form-control"
	type="text"
	name="signup_email_confirm"
	id="signup_email_confirm"
	value="<?php echo esc_attr( openlab_post_value( 'signup_email_confirm' ) ); ?>"
	data-parsley-trigger="blur"
	data-parsley-required
	data-parsley-type="email"
	data-parsley-iff="#signup_email"
	data-parsley-iff-message="<?php esc_attr_e( 'Email addresses must match.', 'commons-in-a-box' ); ?>"
	data-parsley-group="email"
	/>
</div>
