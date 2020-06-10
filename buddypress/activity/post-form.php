<form action="<?php bp_activity_post_form_action(); ?>" method="post" id="whats-new-form" name="whats-new-form">

	<?php do_action( 'bp_before_activity_post_form' ); ?>

	<?php // phpcs:disable WordPress.Security.NonceVerification.Recommended ?>
	<?php if ( isset( $_GET['r'] ) ) : ?>
		<div id="message" class="info">
			<?php /* translators: mentioned user name */ ?>
			<p><?php echo esc_html( sprintf( __( 'You are mentioning %s in a new update, this user will be sent a notification of your message.', 'commons-in-a-box' ), bp_get_mentioned_user_display_name( $_GET['r'] ) ) ); ?></p>
		</div>
	<?php endif; ?>
	<?php // phpcs:enable WordPress.Security.NonceVerification.Recommended ?>

	<div id="whats-new-avatar">
		<a href="<?php echo esc_attr( bp_loggedin_user_domain() ); ?>">
			<?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ); ?>
		</a>
	</div>

	<h5>
		<?php if ( bp_is_group() ) : ?>
			<?php /* translators: 1. current group name, 2. logged-in user name */ ?>
			<?php echo esc_html( sprintf( __( 'What\'s new in %1$s, %2$s?', 'commons-in-a-box' ), bp_get_group_name(), bp_get_user_firstname() ) ); ?>
		<?php else : ?>
			<?php /* translators: logged-in user name */ ?>
			<?php echo esc_html( sprintf( __( "What's new %s?", 'commons-in-a-box' ), bp_get_user_firstname() ) ); ?>
		<?php endif; ?>
	</h5>

	<div id="whats-new-content">
		<div id="whats-new-textarea">
			<?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<?php $textarea_content = isset( $_GET['r'] ) ? '@' . wp_unslash( $_GET['r'] ) : ''; ?>
			<textarea name="whats-new" id="whats-new" cols="50" rows="10"><?php echo esc_textarea( $textarea_content ); ?></textarea>
		</div>

		<div id="whats-new-options">
			<div id="whats-new-submit">
				<span class="ajax-loader"></span> &nbsp;
				<input type="submit" name="aw-whats-new-submit" id="aw-whats-new-submit" value="<?php esc_html_e( 'Post Update', 'commons-in-a-box' ); ?>" />
			</div>
			<?php if ( function_exists( 'bp_has_groups' ) && ! bp_is_my_profile() && ! bp_is_group() ) : ?>
				<div id="whats-new-post-in-box">
					<?php esc_html_e( 'Post in', 'commons-in-a-box' ); ?>:

					<select id="whats-new-post-in" name="whats-new-post-in">
						<option selected="selected" value="0"><?php esc_html_e( 'My Profile', 'commons-in-a-box' ); ?></option>

						<?php
						if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0' ) ) :
							while ( bp_groups() ) :
								bp_the_group();
								?>
							<option value="<?php bp_group_id(); ?>"><?php bp_group_name(); ?></option>
								<?php
						endwhile;
endif;
						?>
					</select>
				</div>
				<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
			<?php elseif ( bp_is_group_home() ) : ?>
				<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
				<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id(); ?>" />
			<?php endif; ?>

			<?php do_action( 'bp_activity_post_form_options' ); ?>

		</div><!-- #whats-new-options -->
	</div><!-- #whats-new-content -->

	<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
	<?php do_action( 'bp_after_activity_post_form' ); ?>

</form><!-- #whats-new-form -->
