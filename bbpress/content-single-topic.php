<?php
/**
 * Single Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */
?>

<?php
$group_type = cboxol_get_group_group_type( bp_get_current_group_id() );
?>

<div id="bbpress-forums">

	<?php bbp_breadcrumb(); ?>

	<?php do_action( 'bbp_template_before_single_topic' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

		<?php bbp_topic_tag_list(); ?>

		<?php bbp_single_topic_description(); ?>

		<?php if ( bbp_show_lead_topic() ) : ?>

			<?php bbp_get_template_part( 'content', 'single-topic-lead' ); ?>

		<?php endif; ?>

		<?php if ( bbp_current_user_can_access_create_reply_form() ) : ?>
		<?php elseif ( bbp_is_topic_closed() ) : ?>

			<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
				<div class="bbp-template-notice">
					<?php // translators: topic name ?>
					<p><?php echo esc_html( sprintf( __( 'The topic &#8216;%s&#8217; is closed to new replies.', 'bbpress' ), bbp_get_topic_title() ) ); ?></p>
				</div>
			</div>

		<?php elseif ( bbp_is_forum_closed( bbp_get_topic_forum_id() ) ) : ?>

			<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
				<div class="bbp-template-notice">
					<?php // translators: forum name ?>
					<p><?php echo esc_html( sprintf( __( 'The forum &#8216;%s&#8217; is closed to new topics and replies.', 'bbpress' ), bbp_get_forum_title( bbp_get_topic_forum_id() ) ) ); ?></p>
				</div>
			</div>

		<?php else : ?>

			<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
				<div class="bbp-template-notice">
					<p><?php is_user_logged_in() ? esc_html_e( 'You cannot reply to this topic.', 'bbpress' ) : esc_html_e( 'You must be logged in to reply to this topic.', 'bbpress' ); ?></p>
				</div>
			</div>

		<?php endif; ?>

		<?php if ( bbp_has_replies() ) : ?>

			<div class="bbp-back-to-course-discussion">
				<?php /* Trick: use the buddypress string so it gets translated */ ?>
				<p><a class="btn btn-primary link-btn" href="<?php bp_group_permalink(); ?>forum/"><span class="fa fa-chevron-circle-left"></span> <?php echo esc_html( $group_type->get_label( 'group_discussion' ) ); ?></a></p>
			</div>

			<div class="panel panel-default">

				<?php bbp_get_template_part( 'loop', 'replies' ); ?>

				<?php bbp_get_template_part( 'pagination', 'replies' ); ?>
			</div>

		<?php endif; ?>

		<?php /* Prev/next - this is not beautiful */ ?>
		<?php
		$group_topics = new WP_Query(
			array(
				'post_type'              => bbp_get_topic_post_type(),
				'post_parent'            => bbp_get_forum_id(),
				'meta_key'               => '_bbp_last_active_time',
				'orderby'                => 'meta_value',
				'order'                  => 'DESC',
				'posts_per_page'         => -1,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
			)
		);

		$this_topic_index = array_search( bbp_get_topic_id(), $group_topics->posts, true );
		$last_topic_index = end( $group_topics->posts );

		$prev_url  = '';
		$next_url  = '';
		$prev_link = '';
		$next_link = '';

		// Previous is +1.
		if ( $this_topic_index < $last_topic_index ) {
			if ( isset( $group_topics->posts[ $this_topic_index + 1 ] ) ) {
				$prev_topic_id = $group_topics->posts[ $this_topic_index + 1 ];
				$prev_url      = get_permalink( $prev_topic_id );
				$prev_link     = '<a class="btn btn-primary link-btn" href="' . esc_attr( $prev_url ) . '"><span class="fa fa-chevron-circle-left"></span> ' . esc_html__( 'Previous Topic', 'commons-in-a-box' ) . '</a>';
			}
		}

		// Next is -1.
		if ( $this_topic_index > 0 ) {
			$next_topic_id = $group_topics->posts[ $this_topic_index - 1 ];
			$next_url      = get_permalink( $next_topic_id );
			$next_link     = '<a class="btn btn-primary link-btn"  href="' . esc_attr( $next_url ) . '">' . esc_html__( 'Next Topic', 'commons-in-a-box' ) . ' <span class="fa fa-chevron-circle-right"></span></a>';
		}
		?>

		<div class="bbp-prev-next">
			<p>
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo implode( '&nbsp;&nbsp;&nbsp;', array( $prev_link, $next_link ) ); ?>
			</p>
		</div>

		<?php bbp_get_template_part( 'form', 'reply' ); ?>

	<?php endif; ?>

	<?php do_action( 'bbp_template_after_single_topic' ); ?>

</div>
