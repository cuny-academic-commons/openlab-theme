<?php /* Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter() */ ?>

<?php do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

	<?php do_action( 'bp_before_directory_members_list' ); ?>

	<div id="friend-list" class="item-list group-list row">
	<?php while ( bp_members() ) : ?>
		<?php bp_the_member(); ?>

		<div class="group-item col-md-8 col-xs-12">
			<div class="group-item-wrapper">
				<div class="row info-row">
					<div class="item-avatar col-sm-9 col-xs-7">
						<?php
						$user_avatar = bp_core_fetch_avatar(
							array(
								'item_id' => bp_get_member_user_id(),
								'object'  => 'user',
								'type'    => 'full',
								'html'    => false,
							)
						);
						?>
						<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src ="<?php echo esc_attr( $user_avatar ); ?>" alt="<?php esc_attr( bp_get_member_name() ); ?>"/></a>
					</div>

					<div class="item col-sm-15 col-xs-17">
						<h5 class="item-title"><a class="no-deco" href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a></h5>

						<?php if ( bp_get_member_latest_update() ) : ?>
							<span class="update"> - <?php bp_member_latest_update( 'length=10' ); ?></span>
						<?php endif; ?>

						<?php if ( bp_current_action() !== 'my-friends' ) : ?>
							<div class="timestamp"><span class="fa fa-undo"></span> <?php bp_member_last_active(); ?></div>
						<?php endif; ?>

						<?php do_action( 'bp_directory_members_actions' ); ?>

						<?php do_action( 'bp_directory_members_item' ); ?>

						<?php
						/**
						 * If you want to show specific profile fields here you can,
						 * but it'll add an extra query for each member in the loop
						 * (only one regardless of the number of fields you show):
						 *
						 * bp_member_profile_data( 'field=the field name' );
						 */
						?>
					</div>
				</div><!-- .row -->
			</div><!-- .group-item-wrapper -->
		</div><!-- .group-item -->

	<?php endwhile; ?>
	</div>

	<?php do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pagination-links" id="member-dir-pag-bottom">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo openlab_members_pagination_links(); ?>
		</div>

	</div>

<?php else : ?>

	<div id="message" class="info">
		<p><?php esc_html_e( 'Sorry, no members were found.', 'commons-in-a-box' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>
