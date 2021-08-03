<?php do_action( 'bp_before_member_messages_loop' ); ?>

<?php if ( bp_has_message_threads() ) : ?>

	<?php do_action( 'bp_before_member_messages_threads' ); ?>

		<?php
		global $messages_template;
		while ( bp_message_threads() ) :
			bp_message_thread();

			$mstatus = true;
			$read    = 'unread';

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['status'] ) && 'unread' === $_GET['status'] ) {
				$mstatus = bp_message_thread_has_unread();
			}
			if ( isset( $_GET['status'] ) && 'read' === $_GET['status'] ) {
				$mstatus = ! bp_message_thread_has_unread();
				$read    = 'read';
			}
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			$user_avatar = bp_core_fetch_avatar(
				array(
					'item_id' => $messages_template->thread->last_sender_id,
					'object'  => 'member',
					'type'    => 'full',
					'html'    => false,
				)
			);

			?>
			<?php if ( $mstatus ) { ?>
			<div id="m-<?php bp_message_thread_id(); ?>" class="message col-xs-12 <?php echo esc_attr( $read ); ?>">
				<div class="group-item-wrapper">
					<div class="item-avatar col-sm-9 col-xs-7">
						<a href="<?php bp_message_thread_view_link(); ?>"><img class="img-responsive" src="<?php echo esc_attr( $user_avatar ); ?>" alt="Message #<?php echo esc_attr( bp_message_thread_id() ); ?>"/></a>
					</div>

					<div class="item col-sm-15 col-xs-17">
						<h2 class="item-title"><a  class="no-deco"href="<?php bp_message_thread_view_link(); ?>" title="<?php esc_attr_e( 'View Message', 'commons-in-a-box' ); ?>"><?php bp_message_thread_subject(); ?></a></h2>

						<div class="info-line">
							<?php if ( 'sentbox' !== bp_current_action() ) : ?>
								<?php esc_html_e( 'From:', 'commons-in-a-box' ); ?> <?php bp_message_thread_from(); ?><br />
							<?php else : ?>
									<?php esc_html_e( 'To:', 'commons-in-a-box' ); ?> <?php bp_message_thread_to(); ?><br />
							<?php endif; ?>
						</div>

						<div class="timestamp">
							<span class="fa fa-undo"></span> <span class="timestamp"><?php bp_message_thread_last_post_date(); ?></span>
						</div>

						<p class="thread-excerpt"><?php bp_message_thread_excerpt(); ?>... <a href="<?php bp_message_thread_view_link(); ?>" class="read-more" title="<?php esc_attr_e( 'View Message', 'commons-in-a-box' ); ?>"><?php esc_html_e( 'See More', 'commons-in-a-box' ); ?></a></p>

						<?php do_action( 'bp_messages_inbox_list_item' ); ?>

					</div>

					<div class="message-actions">
						<?php if ( bp_message_thread_has_unread() ) : ?>
							<span class="message-unread"><?php esc_html_e( 'Unread', 'commons-in-a-box' ); ?></span> <span class="sep">|</span>
						<?php endif; ?>
						<a class="delete-button confirm" href="<?php echo esc_attr( bp_get_message_thread_delete_link() ); ?>" title="<?php esc_attr_e( 'Delete Message', 'commons-in-a-box' ); ?>"><i class="fa fa-minus-circle"></i><?php esc_html_e( 'Delete', 'commons-in-a-box' ); ?></a>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php endwhile; ?>

	<div id="pag-bottom" class="pagination">

		<div class="pagination-links" id="messages-dir-pag">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo openlab_messages_pagination(); ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'bp_after_member_messages_pagination' ); ?>

<?php else : ?>

	<div id="message" class="info">
		<p><?php esc_html_e( 'Sorry, no messages were found.', 'commons-in-a-box' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_member_messages_loop' ); ?>
