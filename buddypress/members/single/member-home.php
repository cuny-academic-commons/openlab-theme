<?php
/**
 * Main member profile template.
 *
 * Called from home.php, this powers the primary profile.
 *
 * @since 1.0.0
 */
?>

<?php
$group_types = cboxol_get_group_types(
	array(
		'exclude_portfolio' => true,
	)
);

do_action( 'bp_before_member_home_content' );
?>

<?php if ( bp_is_user_activity() || 'public' == bp_current_action() ) { ?>
	<?php bp_get_template_part( 'members/single/header' ); ?>
	<div id="portfolio-sidebar-inline-widget" class="visible-xs sidebar sidebar-inline"><?php openlab_members_sidebar_blocks(); ?></div>
<?php } ?>

<div id="member-item-body" class="row">

	<?php foreach ( $group_types as $group_type ) : ?>
		<?php echo openlab_profile_group_type_activity_block( $group_type ); ?>
	<?php endforeach; ?>

	<script type='text/javascript'>(function ($) {
			$('.activity-list').css('visibility', 'hidden');
		})(jQuery);</script>

	<?php
	if ( bp_is_active( 'friends' ) ) :
		if ( ! $friend_ids = wp_cache_get( 'friends_friend_ids_' . bp_displayed_user_id(), 'bp' ) ) {
			$friend_ids = BP_Friends_Friendship::get_random_friends( bp_displayed_user_id(), 20 );
			wp_cache_set( 'friends_friend_ids_' . bp_displayed_user_id(), $friend_ids, 'bp' );
		}
		?>

		<div id="members-list" class="info-group col-xs-24">

		<?php if ( $friend_ids ) : ?>

			<h2 class="title activity-title"><a class="no-deco" href="<?php echo bp_displayed_user_domain() . bp_get_friends_slug(); ?>"><?php bp_word_or_name( __( 'My Friends', 'buddypress' ), __( "%s's Friends", 'buddypress' ) ); ?><span class="fa fa-chevron-circle-right font-size font-18" aria-hidden="true"></span></a></h2>

			<ul id="member-list" class="inline-element-list">
				<?php foreach ( $friend_ids as $friend_id ) : ?>
					<li class="inline-element">
						<a href="<?php echo bp_core_get_user_domain( $friend_id ); ?>">
							<img class="img-responsive" src ="
							<?php
							echo bp_core_fetch_avatar(
								array(
									'item_id' => $friend_id,
									'object' => 'member',
									'type' => 'full',
									'html' => false,
								)
							);
?>
" alt="<?php echo bp_core_get_user_displayname( $friend_id ); ?>"/>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php else : ?>
			<h2 class="title activity-title"><?php bp_word_or_name( __( 'My Friends', 'buddypress' ), __( "%s's Friends", 'buddypress' ) ); ?></h2>

			<div id="message" class="info">
				<p><?php bp_word_or_name( __( "You haven't added any friend connections yet.", 'buddypress' ), __( "%s hasn't created any friend connections yet.", 'buddypress' ) ); ?></p>
			</div>

		<?php endif; ?>
	<?php endif; /* bp_is_active( 'friends' ) */ ?>
	</div>
<?php do_action( 'bp_after_member_body' ); ?>

</div><!-- #item-body -->

<?php do_action( 'bp_after_member_home_content' ); ?>
