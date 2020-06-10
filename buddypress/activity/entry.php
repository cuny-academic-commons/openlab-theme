<?php /* This template is used by activity-loop.php and AJAX functions to show each activity */ ?>

<?php do_action( 'bp_before_activity_entry' ); ?>

<li class="<?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>">
	<div class="activity-avatar">
		<a href="<?php bp_activity_user_link(); ?>">
			<?php bp_activity_avatar( 'type=full&width=100&height=100' ); ?>
		</a>
	</div>

	<div class="activity-content">

		<div class="activity-header">
			<?php bp_activity_action(); ?>
		</div>

		<?php if ( bp_activity_has_content() ) : ?>
			<div class="activity-inner">
				<?php bp_activity_content_body(); ?>
			</div>
		<?php endif; ?>

		<?php do_action( 'bp_activity_entry_content' ); ?>

		<div class="activity-meta">
			<?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>
				<a href="<?php bp_activity_comment_link(); ?>" class="acomment-reply" id="acomment-comment-<?php bp_activity_id(); ?>"><?php esc_html_e( 'Reply', 'commons-in-a-box' ); ?> (<span><?php bp_activity_comment_count(); ?></span>)</a>
			<?php endif; ?>

			<?php if ( is_user_logged_in() ) : ?>
				<?php if ( ! bp_get_activity_is_favorite() ) : ?>
					<a href="<?php bp_activity_favorite_link(); ?>" class="fav" title="<?php esc_attr_e( 'Mark as Favorite', 'commons-in-a-box' ); ?>"><?php esc_html_e( 'Favorite', 'commons-in-a-box' ); ?></a>
				<?php else : ?>
					<a href="<?php bp_activity_unfavorite_link(); ?>" class="unfav" title="<?php esc_attr_e( 'Remove Favorite', 'commons-in-a-box' ); ?>"><?php esc_html_e( 'Remove Favorite', 'commons-in-a-box' ); ?></a>
				<?php endif; ?>
			<?php endif; ?>

			<?php do_action( 'bp_activity_entry_meta' ); ?>
		</div>
	</div>

	<?php if ( 'activity_comment' === bp_get_activity_type() ) : ?>
		<div class="activity-inreplyto">
			<strong><?php esc_html_e( 'In reply to', 'commons-in-a-box' ); ?></strong> - <?php bp_activity_parent_content(); ?> &middot;
			<a href="<?php bp_activity_thread_permalink(); ?>" class="view" title="<?php esc_attr_e( 'View Thread / Permalink', 'commons-in-a-box' ); ?>"><?php esc_html_e( 'View', 'commons-in-a-box' ); ?></a>
		</div>
	<?php endif; ?>

	<?php do_action( 'bp_before_activity_entry_comments' ); ?>

	<?php if ( bp_activity_can_comment() ) : ?>
		<div class="activity-comments">
			<?php bp_activity_comments(); ?>

			<?php if ( is_user_logged_in() ) : ?>
			<form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php bp_activity_id(); ?>" class="ac-form"<?php bp_activity_comment_form_nojs_display(); ?>>
				<div class="ac-reply-avatar"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ); ?></div>
				<div class="ac-reply-content">
					<div class="ac-textarea">
						<textarea id="ac-input-<?php bp_activity_id(); ?>" class="ac-input" name="ac_input_<?php bp_activity_id(); ?>"></textarea>
					</div>
					<input type="submit" name="ac_form_submit" value="<?php esc_attr_e( 'Post', 'commons-in-a-box' ); ?> &rarr;" /> &nbsp; <?php esc_html_e( 'or press esc to cancel.', 'commons-in-a-box' ); ?>
					<input type="hidden" name="comment_form_id" value="<?php bp_activity_id(); ?>" />
				</div>
				<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ); ?>
			</form>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'bp_after_activity_entry_comments' ); ?>
</li>

<?php do_action( 'bp_after_activity_entry' ); ?>
