<?php get_header(); ?>

	<div id="content">
		<div id="openlab-main-content" class="padder">

			<?php if ( ! is_user_logged_in() ) : ?>
				<h3><?php esc_html_e( 'Site Activity', 'commons-in-a-box' ); ?></h3>
			<?php endif; ?>

			<?php do_action( 'bp_before_directory_activity_content' ); ?>

			<?php if ( is_user_logged_in() ) : ?>
				<?php bp_get_template_part( 'activity/post-form.php' ); ?>
			<?php endif; ?>

			<?php do_action( 'template_notices' ); ?>

			<div class="item-list-tabs activity-type-tabs">
				<ul>
					<?php do_action( 'bp_before_activity_type_tab_all' ); ?>

					<?php /* translators: total member count for site */ ?>
					<li class="selected" id="activity-all"><a href="<?php echo esc_attr( bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/' ); ?>" title="<?php esc_attr_e( 'The public activity for everyone on this site.', 'commons-in-a-box' ); ?>"><?php printf( esc_html__( 'All Members (%s)', 'commons-in-a-box' ), esc_html( bp_get_total_site_member_count() ) ); ?></a></li>

					<?php if ( is_user_logged_in() ) : ?>

						<?php do_action( 'bp_before_activity_type_tab_friends' ); ?>

						<?php if ( function_exists( 'bp_get_total_friend_count' ) ) : ?>
							<?php if ( bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
								<?php /* translators: friend count for user */ ?>
								<li id="activity-friends"><a href="<?php echo esc_attr( bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/' . BP_FRIENDS_SLUG . '/' ); ?>" title="<?php esc_attr_e( 'The activity of my friends only.', 'commons-in-a-box' ); ?>"><?php printf( esc_html__( 'My Friends (%s)', 'commons-in-a-box' ), esc_html( bp_get_total_friend_count( bp_loggedin_user_id() ) ) ); ?></a></li>
							<?php endif; ?>
						<?php endif; ?>

						<?php do_action( 'bp_before_activity_type_tab_groups' ); ?>

						<?php if ( function_exists( 'bp_get_total_group_count_for_user' ) ) : ?>
							<?php if ( bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>
								<?php /* translators: group count for user */ ?>
								<li id="activity-groups"><a href="<?php echo esc_attr( bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/' . BP_GROUPS_SLUG . '/' ); ?>" title="<?php esc_attr_e( 'The activity of groups I am a member of.', 'commons-in-a-box' ); ?>"><?php printf( esc_html__( 'My Groups (%s)', 'commons-in-a-box' ), esc_html( bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) ); ?></a></li>
							<?php endif; ?>
						<?php endif; ?>

						<?php do_action( 'bp_before_activity_type_tab_favorites' ); ?>

						<?php if ( bp_get_total_favorite_count_for_user( bp_loggedin_user_id() ) ) : ?>
							<?php /* translators: favorite activity count */ ?>
							<li id="activity-favorites"><a href="<?php echo esc_attr( bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/favorites/' ); ?>" title="<?php esc_attr_e( "The activity I've marked as a favorite.", 'commons-in-a-box' ); ?>"><?php printf( esc_html_e( 'My Favorites (%s)', 'commons-in-a-box' ), '<span>' . esc_html( bp_get_total_favorite_count_for_user( bp_loggedin_user_id() ) ) . '</span>' ); ?></a></li>
						<?php endif; ?>

						<?php do_action( 'bp_before_activity_type_tab_mentions' ); ?>

						<li id="activity-mentions">
							<a href="<?php echo esc_attr( bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/mentions/' ); ?>" title="<?php esc_attr_e( 'Activity that I have been mentioned in.', 'commons-in-a-box' ); ?>">
								<?php /* translators: username of user whose mentions are being displayed */ ?>
								<?php printf( esc_html__( '@%s Mentions', 'commons-in-a-box' ), esc_html( bp_get_loggedin_user_username() ) ); ?>
								<?php if ( bp_get_total_mention_count_for_user( bp_loggedin_user_id() ) ) : ?>
									<?php /* translators: number of new mentions for the user */ ?>
									<strong><?php printf( esc_html__( '(%s new)', 'commons-in-a-box' ), esc_html( bp_get_total_mention_count_for_user( bp_loggedin_user_id() ) ) ); ?></strong>
								<?php endif; ?>
							</a>
						</li>
					<?php endif; ?>

					<?php do_action( 'bp_activity_type_tabs' ); ?>
				</ul>
			</div><!-- .item-list-tabs -->

			<div class="item-list-tabs no-ajax" id="subnav">
				<ul>
					<li class="feed"><a href="<?php bp_sitewide_activity_feed_link(); ?>" title="<?php esc_html_e( 'RSS Feed', 'commons-in-a-box' ); ?>"><?php esc_html_e( 'RSS', 'commons-in-a-box' ); ?></a></li>

					<?php do_action( 'bp_activity_syndication_options' ); ?>

					<li id="activity-filter-select" class="last">
						<select>
							<option value="-1"><?php esc_html_e( 'No Filter', 'commons-in-a-box' ); ?></option>
							<option value="activity_update"><?php esc_html_e( 'Show Updates', 'commons-in-a-box' ); ?></option>

							<?php if ( bp_is_active( 'blogs' ) ) : ?>
								<option value="new_blog_post"><?php esc_html_e( 'Show Blog Posts', 'commons-in-a-box' ); ?></option>
								<option value="new_blog_comment"><?php esc_html_e( 'Show Blog Comments', 'commons-in-a-box' ); ?></option>
							<?php endif; ?>

							<?php if ( bp_is_active( 'forums' ) ) : ?>
								<option value="new_forum_topic"><?php esc_html_e( 'Show New Forum Topics', 'commons-in-a-box' ); ?></option>
								<option value="new_forum_post"><?php esc_html_e( 'Show Forum Replies', 'commons-in-a-box' ); ?></option>
							<?php endif; ?>

							<?php if ( bp_is_active( 'groups' ) ) : ?>
								<option value="created_group"><?php esc_html_e( 'Show New Groups', 'commons-in-a-box' ); ?></option>
								<option value="joined_group"><?php esc_html_e( 'Show New Group Memberships', 'commons-in-a-box' ); ?></option>
							<?php endif; ?>

							<?php if ( bp_is_active( 'friends' ) ) : ?>
								<option value="friendship_accepted,friendship_created"><?php esc_html_e( 'Show Friendship Connections', 'commons-in-a-box' ); ?></option>
							<?php endif; ?>

							<option value="new_member"><?php esc_html_e( 'Show New Members', 'commons-in-a-box' ); ?></option>

							<?php do_action( 'bp_activity_filter_options' ); ?>
						</select>
					</li>
				</ul>
			</div><!-- .item-list-tabs -->

			<div class="activity">
				<?php bp_get_template_part( 'activity/activity-loop.php' ); ?>
			</div><!-- .activity -->

			<?php do_action( 'bp_after_directory_activity_content' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ); ?>

<?php get_footer(); ?>
