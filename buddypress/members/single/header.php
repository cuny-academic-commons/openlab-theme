<?php
/**
 * Member header template.
 *
 * @since 1.0.0
 */
?>

<?php
$this_user_id = bp_displayed_user_id();

//
// whenever profile is viewed, update user meta for first name and last name so this shows up
// in the back end on users display so teachers see the students full name
//
$name_member_id    = bp_displayed_user_id();
$first_name        = xprofile_get_field_data( 'First Name', $name_member_id );
$last_name         = xprofile_get_field_data( 'Last Name', $name_member_id );
$update_user_first = update_user_meta( $name_member_id, 'first_name', $first_name );
$update_user_last  = update_user_meta( $name_member_id, 'last_name', $last_name );

$academic_unit_data = cboxol_get_object_academic_unit_data_for_display(
	array(
		'object_id'   => bp_displayed_user_id(),
		'object_type' => 'user',
	)
);
?>

<?php
// Get the displayed user's base domain
// This is required because the my-* pages aren't really displayed user pages from BP's
// point of view
$dud = bp_displayed_user_domain();
if ( ! $dud ) {
	$dud = bp_loggedin_user_domain(); // will always be the logged in user on my-*
}
?>

<div id="member-header" class="member-header row">
<?php
do_action( 'bp_before_member_header' );

do_action( 'bp_before_member_home_content' );
?>
	<div id="member-header-avatar" class="alignleft group-header-avatar col-sm-8">
		<div id="avatar-wrapper">
			<div class="padded-img darker">
				<?php
				$avatar_src = bp_core_fetch_avatar(
					array(
						'item_id' => $this_user_id,
						'object'  => 'member',
						'type'    => 'full',
						'html'    => false,
					)
				);
				?>
				<img class="img-responsive padded" src="<?php echo esc_attr( $avatar_src ); ?>" alt="<?php echo bp_core_get_user_displayname( $this_user_id ); ?>" />
			</div>
		</div><!--memeber-header-avatar-->

		<div id="profile-action-wrapper">
			<?php if ( is_user_logged_in() && openlab_is_my_profile() ) : ?>
				<div id="group-action-wrapper">
					<a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo $dud . 'profile/edit/'; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> <?php esc_html_e( 'Edit Profile', 'openlab-theme' ); ?></a>
					<a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo $dud . 'profile/change-avatar/'; ?>"><i class="fa fa-camera" aria-hidden="true"></i> <?php esc_html_e( 'Change Avatar', 'openlab-theme' ); ?></a>
				</div>
			<?php elseif ( is_user_logged_in() && ! openlab_is_my_profile() ) : ?>
				<?php bp_add_friend_button( openlab_fallback_user(), bp_loggedin_user_id() ); ?>

				<?php
				echo bp_get_button(
					array(
						'id'                => 'private_message',
						'component'         => 'messages',
						'must_be_logged_in' => true,
						'block_self'        => true,
						'wrapper_id'        => 'send-private-message',
						'link_href'         => bp_get_send_private_message_link(),
						'link_title'        => __( 'Send a private message to this user.', 'buddypress' ),
						'link_text'         => __( '<i class="fa fa-envelope" aria-hidden="true"></i> Send Message', 'buddypress' ),
						'link_class'        => 'send-message btn btn-default btn-block btn-primary link-btn',
					)
				)
				?>

			<?php endif ?>
		</div><!--profile-action-wrapper-->
	</div><!-- #item-header-avatar -->

	<div id="member-header-content" class="col-sm-16 col-xs-24">

		<?php do_action( 'bp_before_member_header_meta' ); ?>

		<div id="item-meta">

			<?php do_action( 'bp_profile_header_meta' ); ?>

		</div><!-- #item-meta -->

		<div class="profile-fields">
			<?php if ( bp_has_profile() ) : ?>
				<div class="info-panel panel panel-default no-margin no-margin-top">
					<div class="profile-fields table-div">
						<?php
						while ( bp_profile_groups() ) :
							bp_the_profile_group();
							if ( bp_profile_group_has_fields() ) :
								while ( bp_profile_fields() ) :
									bp_the_profile_field();
									if ( bp_field_has_data() ) :
										// @todo This should not be hardcoded like this.
										if ( bp_get_the_profile_field_name() !== 'Name'
											&& bp_get_the_profile_field_name() !== 'Account Type'
											&& bp_get_the_profile_field_name() !== 'First Name'
											&& bp_get_the_profile_field_name() !== 'Last Name'
											) :
											?>

											<div class="table-row row">
												<div class="bold col-sm-7">
													<?php bp_the_profile_field_name(); ?>
												</div>

												<div class="col-sm-17">
													<?php
													if ( bp_get_the_profile_field_name() == 'Academic interests' || bp_get_the_profile_field_name() == 'Bio' ) {
														echo bp_get_the_profile_field_value();
													} else {
														$field_value = str_replace( '<p>', '', bp_get_the_profile_field_value() );
														$field_value = str_replace( '</p>', '', $field_value );
														echo $field_value;
													}
													?>
												</div>
											</div>
										<?php endif; ?>

									<?php endif; // bp_field_has_data() ?>

								<?php endwhile; // bp_profile_fields() ?>

							<?php endif; // bp_profile_group_has_fields() ?>

						<?php endwhile; // bp_profile_groups() ?>

						<?php /* @todo this should go somewhere else */ ?>
						<?php foreach ( $academic_unit_data as $type => $type_data ) : ?>
							<div class="table-row row">
								<div class="bold col-sm-7">
									<?php echo esc_html( $type_data['label'] ); ?>
								</div>

								<div class="col-sm-17">
									<?php echo esc_html( $type_data['value'] ); ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; // bp_has_profile() ?>
		</div>

	</div><!-- #item-header-content -->

<?php do_action( 'bp_after_member_header' ); ?>

</div><!-- #item-header -->
<?php
