<?php
/**
 * Merge Topic
 *
 * @package bbPress
 * @subpackage Theme
 */
?>

<div id="bbpress-forums">

	<?php bbp_breadcrumb(); ?>

	<?php if ( is_user_logged_in() && current_user_can( 'edit_topic', bbp_get_topic_id() ) ) : ?>

		<div id="merge-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-merge">

			<div class="panel-body">

				<form id="merge_topic" class="form form-panel" name="merge_topic" method="post" action="<?php the_permalink(); ?>">

					<div class="bbp-form panel panel-default">

						<?php // translators: topic title ?>
						<div class="panel-heading"><?php echo esc_html( sprintf( __( 'Merge topic "%s"', 'bbpress' ), bbp_get_topic_title() ) ); ?></div>

						<div class="panel-body">

							<div class="bbp-template-notice info">
								<p><?php esc_html_e( 'Select the topic to merge this one into. The destination topic will remain the lead topic, and this one will change into a reply.', 'bbpress' ); ?></p>
								<p><?php esc_html_e( 'To keep this topic as the lead, go to the other topic and use the merge tool from there instead.', 'bbpress' ); ?></p>
							</div>

							<div class="bbp-template-notice">
								<p><?php esc_html_e( 'All replies within both topics will be merged chronologically. The order of the merged replies is based on the time and date they were posted. If the destination topic was created after this one, its post date will be updated to second earlier than this one.', 'bbpress' ); ?></p>
							</div>

							<fieldset class="bbp-form">
								<legend><?php esc_html_e( 'Destination', 'bbpress' ); ?></legend>
								<div>
									<?php
									if ( bbp_has_topics(
										array(
											'show_stickies' => false,
											'post_parent'  => bbp_get_topic_forum_id( bbp_get_topic_id() ),
											'post__not_in' => array( bbp_get_topic_id() ),
										)
									) ) :
										?>

										<label for="bbp_destination_topic"><?php esc_html_e( 'Merge with this topic:', 'bbpress' ); ?></label>

											<?php
											bbp_dropdown(
												array(
													'post_type' => bbp_get_topic_post_type(),
													'post_parent' => bbp_get_topic_forum_id( bbp_get_topic_id() ),
													'selected' => -1,
													'exclude'  => bbp_get_topic_id(),
													'select_id' => 'bbp_destination_topic',
												)
											);
											?>

									<?php else : ?>

										<label><?php esc_html_e( 'There are no other topics in this forum to merge with.', 'bbpress' ); ?></label>

									<?php endif; ?>

								</div>
							</fieldset>

							<fieldset class="bbp-form">
								<legend><?php esc_html_e( 'Topic Extras', 'bbpress' ); ?></legend>

								<div>

									<?php if ( bbp_is_subscriptions_active() ) : ?>

										<input name="bbp_topic_subscribers" id="bbp_topic_subscribers" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
										<label for="bbp_topic_subscribers"><?php esc_html_e( 'Merge topic subscribers', 'bbpress' ); ?></label><br />

									<?php endif; ?>

									<input name="bbp_topic_favoriters" id="bbp_topic_favoriters" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
									<label for="bbp_topic_favoriters"><?php esc_html_e( 'Merge topic favoriters', 'bbpress' ); ?></label><br />

									<?php if ( bbp_allow_topic_tags() ) : ?>

										<input name="bbp_topic_tags" id="bbp_topic_tags" type="checkbox" value="1" checked="checked" tabindex="<?php bbp_tab_index(); ?>" />
										<label for="bbp_topic_tags"><?php esc_html_e( 'Merge topic tags', 'bbpress' ); ?></label><br />

									<?php endif; ?>

								</div>
							</fieldset>

						</div>
					</div>

					<div class="bbp-template-notice error">
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction ?>
						<p><?php _e( '<strong>WARNING:</strong> This process cannot be undone.', 'bbpress' ); ?></p>
					</div>

					<div class="bbp-submit-wrapper">
						<button type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_merge_topic_submit" name="bbp_merge_topic_submit" class="btn btn-primary submit"><?php esc_html_e( 'Submit', 'bbpress' ); ?></button>
					</div>
			</div>

			<?php bbp_merge_topic_form_fields(); ?>

			</form>

		</div>

	<?php else : ?>

		<div id="no-topic-<?php bbp_topic_id(); ?>" class="bbp-no-topic">
			<div class="entry-content"><?php is_user_logged_in() ? esc_html_e( 'You do not have the permissions to edit this topic!', 'bbpress' ) : esc_html_e( 'You cannot edit this topic.', 'bbpress' ); ?></div>
		</div>

	<?php endif; ?>

</div>
