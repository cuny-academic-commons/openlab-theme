<div id="single-course-body">
	<?php
	//
	// control the formatting of left and right side by use of variable $first_class.
	// when it is "first" it places it on left side, when it is "" it places it on right side
	//
	// Initialize it to left side to start with
	//
	$first_class = 'first';

	$group_slug = bp_get_group_slug();
	$group_type = cboxol_get_group_group_type( bp_get_current_group_id() );

	$group = groups_get_current_group();

	if ( current_user_can( 'view_private_members_of_group', $group->id ) ) {
		$group_private_members = [];
	} else {
		$group_private_members = openlab_get_private_members_of_group( $group->id );
	}

	?>

	<?php if ( bp_is_group_home() ) { ?>

		<?php if ( 'public' === bp_get_group_status() || ( ( 'hidden' === bp_get_group_status() || 'private' === bp_get_group_status() ) && ( bp_is_item_admin() || bp_group_is_member() ) ) ) : ?>
			<?php
			if ( cboxol_site_can_be_viewed() ) {
				openlab_show_site_posts_and_comments();
			}
			?>

			<?php if ( ! $group_type->get_is_portfolio() ) : ?>
				<div class="row group-activity-overview">
					<?php if ( openlab_is_forum_enabled_for_group( $group->id ) ) : ?>
						<div class="col-sm-12">
							<div class="recent-discussions">
								<div class="recent-posts">
									<h2 class="title activity-title"><a class="no-deco" href="<?php echo esc_attr( bp_get_group_url( $group, bp_groups_get_path_chunks( [ 'forum' ] ) ) ); ?>"><?php esc_html_e( 'Recent Discussions', 'commons-in-a-box' ); ?><span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>
									<?php
									$forum_id  = null;
									$forum_ids = bbp_get_group_forum_ids( bp_get_current_group_id() );

									// Get the first forum ID
									if ( ! empty( $forum_ids ) ) {
										$forum_id = (int) is_array( $forum_ids ) ? $forum_ids[0] : $forum_ids;
									}

									$topic_args = [
										'posts_per_page' => 3,
										'post_parent'    => $forum_id,
										'author__not_in' => $group_private_members,
									];

									?>

									<?php if ( $forum_id && bbp_has_topics( $topic_args ) ) : ?>
										<?php while ( bbp_topics() ) : ?>
											<?php bbp_the_topic(); ?>

											<div class="panel panel-default">
												<div class="panel-body">

													<?php
													$topic_id      = bbp_get_topic_id();
													$last_reply_id = bbp_get_topic_last_reply_id( $topic_id );

													// Oh, bbPress.
													$topic_replies = get_posts(
														[
															'post_type'      => 'reply',
															'post_parent'    => $topic_id,
															'author__not_in' => $group_private_members,
															'posts_per_page' => 1,
															'orderby'        => [ 'post_date' => 'DESC' ],
														]
													);

													if ( $topic_replies ) {
														$last_reply_content = $topic_replies[0]->post_content;
													} else {
														$topic_post         = get_post( $topic_id );
														$last_reply_content = $topic_post->post_content;
													}

													$last_reply_content = bp_create_excerpt( wp_strip_all_tags( $last_reply_content ), 250 );
													?>

													<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
													<?php echo openlab_get_group_activity_content( bbp_get_topic_title(), $last_reply_content, bbp_get_topic_permalink() ); ?>

												</div>
											</div>
										<?php endwhile; ?>
									<?php else : ?>
										<div class="panel panel-default">
											<div class="panel-body">
												<p><?php esc_html_e( 'Sorry, there were no discussion topics found.', 'commons-in-a-box' ); ?></p>
											</div>
										</div>
									<?php endif; ?>
								</div><!-- .recent-post -->
							</div>
						</div>
					<?php endif; // Recent Discussions ?>
					<?php $first_class = ''; ?>
					<?php if ( function_exists( 'bp_docs_get_slug' ) && openlab_is_docs_enabled_for_group( $group->id ) ) : ?>
						<div class="col-sm-12">
							<div id="recent-docs">
								<div class="recent-posts">
									<h2 class="title activity-title"><a class="no-deco" href="<?php echo esc_url( bp_get_group_url( $group, bp_groups_get_path_chunks( [ bp_docs_get_slug() ] ) ) ); ?>"><?php esc_html_e( 'Recent Docs', 'commons-in-a-box' ); ?><span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>
									<?php

									$docs_query = new BP_Docs_Query(
										array(
											'group_id' => bp_get_current_group_id(),
											'orderby'  => 'created',
											'order'    => 'DESC',
											'posts_per_page' => 3,
										)
									);
									$query      = $docs_query->get_wp_query();

									global $post;
									if ( $query->have_posts() ) {
										while ( $query->have_posts() ) :
											$query->the_post();
											$doc_url = bp_docs_get_doc_link( get_the_ID() );
											?>
											<div class="panel panel-default">
												<div class="panel-body">
													<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
													<?php echo openlab_get_group_activity_content( get_the_title(), bp_create_excerpt( wp_strip_all_tags( $post->post_content ), 250, array( 'ending' => '' ) ), $doc_url ); ?>
												</div>
											</div>
											<?php
										endwhile;
									} else {
										echo '<div class="panel panel-default"><div class="panel-body"><p>' . esc_html__( 'No Recent Docs', 'commons-in-a-box' ) . '</p></div></div>';
									}

									$query->reset_postdata();
									?>
								</div>
							</div>
						</div>
					<?php endif; // Recent Docs ?>
				</div>

				<div id="members-list" class="info-group">

					<?php
					if ( bp_is_item_admin() || bp_is_item_mod() ) {
						$href = bp_get_group_url(
							$group,
							bp_groups_get_path_chunks( [ 'admin', 'manage-members' ] )
						);
					} else {
						$href = bp_get_group_url(
							$group,
							bp_groups_get_path_chunks( [ 'members' ] )
						);
					}
					?>

					<h2 class="title activity-title"><a class="no-deco" href="<?php echo esc_attr( $href ); ?>"><?php esc_html_e( 'Members', 'commons-in-a-box' ); ?><span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a></h2>

					<?php
					$group_member_args = [
						'exclude_admins_mods' => false,
					];

					if ( ! current_user_can( 'bp_moderate' ) ) {
						$group_member_args['exclude'] = openlab_get_private_members_of_group( bp_get_current_group_id() );
					}
					?>

					<?php if ( bp_group_has_members( $group_member_args ) ) : ?>

						<ul id="member-list" class="inline-element-list">
							<?php
							while ( bp_group_members() ) :
								bp_group_the_member();
								global $members_template;
								$member = $members_template->member;

								$user_avatar = bp_core_fetch_avatar(
									array(
										'item_id' => $member->ID,
										'object'  => 'user',
										'type'    => 'full',
										'html'    => false,
									)
								);
								?>
								<li class="inline-element">
									<a href="<?php echo esc_attr( bp_group_member_domain() ); ?>">
										<img class="img-responsive" src="<?php echo esc_attr( $user_avatar ); ?>" alt="<?php echo esc_attr( $member->fullname ); ?>"/>
									</a>
								</li>
							<?php endwhile; ?>
						</ul>

						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo openlab_members_pagination_links( 'mlpage' ); ?>
					<?php else : ?>

						<div id="message" class="info">
							<p><?php esc_html_e( 'This group has no members.', 'commons-in-a-box' ); ?></p>
						</div>

					<?php endif; ?>

				</div><!-- .group-activity-overview -->

			<?php endif; // end of if $group != 'portfolio' ?>

		<?php else : ?>
			<?php
			// check if blog (site) is NOT private (option blog_public Not = '_2"), in which
			// case show site posts and comments even though this group is private
			//
			if ( cboxol_site_can_be_viewed() ) {
				openlab_show_site_posts_and_comments();
				echo "<div class='clear'></div>";
			}
			?>
			<?php /* The group is not visible, show the status message */ ?>

		<?php endif; ?>

		<?php
	} else {
		bp_get_template_part( 'groups/single/wds-bp-action-logics.php' );
	}
	?>

</div><!-- #single-course-body -->
