<?php
/**
 * Invite only functions
 * These functions are clones of those found in the Invite Anyone plugin
 * They are duplicated here so that Bootstrap markup can be injected for uniform styling
 * See also: openlab/buddypress/members/single/invite-anyone.php for template overrides
 */

/**
 * Dequeue inherit styling from plugin
 */
function openlab_dequeue_invite_anyone_styles() {
	wp_dequeue_style( 'invite-anyone-by-email-style' );
}

add_action( 'wp_print_styles', 'openlab_dequeue_invite_anyone_styles', 999 );

/**
 * Invite new members custom
 *
 * @global type $bp
 * @return type
 */
function openlab_invite_anyone_screen_one_content() {

	global $bp;

	$iaoptions = invite_anyone_options();

	// Hack - catch already=accepted
	if ( ! empty( $_GET['already'] ) && 'accepted' === $_GET['already'] && bp_is_my_profile() ) {
		esc_html_e( 'It looks like you&#8217;ve already accepted your invitation to join the site.', 'commons-in-a-box' );
		return;
	}

	// If the user has maxed out his invites, no need to go on
	if ( ! empty( $iaoptions['email_limit_invites_toggle'] ) && 'yes' === $iaoptions['email_limit_invites_toggle'] && ! current_user_can( 'delete_others_pages' ) ) {
		$sent_invites       = invite_anyone_get_invitations_by_inviter_id( bp_displayed_user_id() );
		$sent_invites_count = $sent_invites->post_count;
		if ( $sent_invites_count >= $iaoptions['limit_invites_per_user'] ) :
			?>

			<h4><?php esc_html_e( 'Invite New Members', 'commons-in-a-box' ); ?></h4>

			<p id="welcome-message"><?php esc_html_e( 'You have sent the maximum allowed number of invitations.', 'commons-in-a-box' ); ?></em></p>

			<?php
			return;
		endif;
	}

	$max_invites = $iaoptions['max_invites'];
	if ( ! $max_invites ) {
		$max_invites = 5;
	}

	$from_group = false;
	if ( ! empty( $bp->action_variables ) ) {
		if ( bp_is_action_variable( 'group-invites', 0 ) ) {
			$from_group = $bp->action_variables[1];
		}
	}

	$returned_data = ! empty( $bp->invite_anyone->returned_data ) ? $bp->invite_anyone->returned_data : false;

	/* If the user is coming from the widget, $returned_emails is populated with those email addresses */
	if ( isset( $_POST['invite_anyone_widget'] ) ) {
		check_admin_referer( 'invite-anyone-widget_' . $bp->loggedin_user->id );

		if ( ! empty( $_POST['invite_anyone_email_addresses'] ) ) {
			$returned_data['error_emails'] = invite_anyone_parse_addresses( $_POST['invite_anyone_email_addresses'] );
		}

		/* If the widget appeared on a group page, the group ID should come along with it too */
		if ( isset( $_POST['invite_anyone_widget_group'] ) ) {
			$returned_data['groups'] = $_POST['invite_anyone_widget_group'];
		}
	}

	// $returned_groups is padded so that array_search (below) returns true for first group */
	$counter         = 0;
	$returned_groups = array( 0 );
	if ( ! empty( $returned_data['groups'] ) ) {
		foreach ( $returned_data['groups'] as $group_id ) {
			$returned_groups[] = (int) $group_id;
		}
	}

	// Get the returned email subject, if there is one
	$returned_subject = ! empty( $returned_data['subject'] ) ? stripslashes( $returned_data['subject'] ) : false;

	// Get the returned email message, if there is one
	$returned_message = ! empty( $returned_data['message'] ) ? stripslashes( $returned_data['message'] ) : false;

	$blogname = get_bloginfo( 'name' );

	// translators: blog name
	$welcome_message = sprintf( __( 'Invite friends to join %s by following these steps:', 'commons-in-a-box' ), $blogname );
	?>
	<form id="invite-anyone-by-email" class="form-panel" action="<?php echo esc_attr( $bp->displayed_user->domain . $bp->invite_anyone->slug . '/sent-invites/send/' ); ?>" method="post">

		<div class="panel panel-default">
			<div class="panel-heading"><?php esc_html_e( 'Invite New Members', 'commons-in-a-box' ); ?></div>
			<div class="panel-body">

				<?php
				if ( ! empty( $returned_data['error_message'] ) ) {
					?>
					<div class="invite-anyone-error bp-template-notice error">
						<p><?php esc_html_e( 'Some of your invitations were not sent. Please see the errors below and resubmit the failed invitations.', 'commons-in-a-box' ); ?></p>
					</div>
					<?php
				}
				if ( ! empty( $returned_data['error_message'] ) ) :
					?>
					<div class="invite-anyone-error bp-template-notice error">
						<p><?php echo esc_html( $returned_data['error_message'] ); ?></p>
					</div>
				<?php endif ?>

				<?php
				if ( isset( $iaoptions['email_limit_invites_toggle'] ) && 'yes' === $iaoptions['email_limit_invites_toggle'] && ! current_user_can( 'delete_others_pages' ) ) {
					if ( ! isset( $sent_invites ) ) {
						$sent_invites       = invite_anyone_get_invitations_by_inviter_id( bp_loggedin_user_id() );
						$sent_invites_count = $sent_invites->post_count;
					}

					$limit_invite_count = (int) $iaoptions['limit_invites_per_user'] - (int) $sent_invites_count;

					if ( $limit_invite_count < 0 ) {
						$limit_invite_count = 0;
					}
					?>

					<?php // translators: 1. max invite count per user, 2. remaining invite count for user ?>
					<p class="description"><?php printf( esc_html__( 'The site administrator has limited each user to %1$d invitations. You have %2$d invitations remaining.', 'commons-in-a-box' ), (int) $iaoptions['limit_invites_per_user'], (int) $limit_invite_count ); ?></p>

					<?php
				}
				?>

				<p id="welcome-message"><?php echo esc_html( $welcome_message ); ?></p>

				<ol id="invite-anyone-steps" class="inline-element-list">

					<li>
						<div class="manual-email">
							<p>
								<?php esc_html_e( 'Enter email addresses below, one per line.', 'commons-in-a-box' ); ?>
								<?php if ( invite_anyone_allowed_domains() ) : ?>
									<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php esc_html_e( 'You can only invite people whose email addresses end in one of the following domains:', 'commons-in-a-box' ); ?> <?php echo invite_anyone_allowed_domains(); ?>
								<?php endif; ?>
							</p>

							<?php $max_no_invites = invite_anyone_max_invites(); ?>
							<?php if ( false !== $max_no_invites ) : ?>
								<?php // translators: max number of invites ?>
								<p class="description"><?php echo esc_html( sprintf( __( 'You can invite a maximum of %s people at a time.', 'commons-in-a-box' ), $max_no_invites ) ); ?></p>
							<?php endif ?>
							<?php $error_emails = isset( $returned_data['error_emails'] ) ? $returned_data['error_emails'] : []; ?>
							<?php openlab_invite_anyone_email_fields( $error_emails ); ?>
						</div>

						<?php /* invite_anyone_after_addresses gets $iaoptions so that Cloudsponge etc can tell whether certain components are activated, without an additional lookup */ ?>
						<?php do_action( 'invite_anyone_after_addresses', $iaoptions ); ?>

					</li>

					<li>
						<?php if ( 'yes' === $iaoptions['subject_is_customizable'] ) : ?>
							<label for="invite-anyone-custom-subject"><?php esc_html_e( '(optional) Customize the subject line of the invitation email.', 'commons-in-a-box' ); ?></label>
							<textarea name="invite_anyone_custom_subject" id="invite-anyone-custom-subject" class="form-control" rows="3" cols="10" ><?php echo esc_textarea( invite_anyone_invitation_subject( $returned_subject ) ); ?></textarea>
						<?php else : ?>
							<label for="invite-anyone-custom-subject"><h4><?php esc_html_e( 'Subject:', 'commons-in-a-box' ); ?> <span class="disabled-subject"><?php esc_html_e( 'Subject line is fixed', 'commons-in-a-box' ); ?></span></h4></label>
							<textarea name="invite_anyone_custom_subject" id="invite-anyone-custom-subject" class="form-control" rows="3" cols="10" disabled="disabled"><?php echo esc_textarea( invite_anyone_invitation_subject( $returned_subject ) ); ?> </textarea>

							<input type="hidden" id="invite-anyone-customised-subject" name="invite_anyone_custom_subject" value="<?php echo esc_attr( invite_anyone_invitation_subject() ); ?>" />
						<?php endif; ?>
					</li>

					<li>
						<?php if ( 'yes' === $iaoptions['message_is_customizable'] ) : ?>
							<label for="invite-anyone-custom-message"><h4><?php esc_html_e( '(optional) Customize the text of the invitation.', 'commons-in-a-box' ); ?></h4></label>
							<p class="description"><?php esc_html_e( 'The message will also contain a custom footer containing links to accept the invitation or opt out of further email invitations from this site.', 'commons-in-a-box' ); ?></p>
							<textarea name="invite_anyone_custom_message" id="invite-anyone-custom-message" class="form-control" cols="40" rows="7"><?php echo esc_textarea( invite_anyone_invitation_message( $returned_message ) ); ?></textarea>
						<?php else : ?>
							<label for="invite-anyone-custom-message"><?php esc_html_e( 'Message:', 'commons-in-a-box' ); ?></label>
							<textarea name="invite_anyone_custom_message" id="invite-anyone-custom-message" class="form-control" disabled="disabled" ><?php echo esc_textarea( invite_anyone_invitation_message( $returned_message ) ); ?></textarea>

							<input type="hidden" name="invite_anyone_custom_message" value="<?php echo esc_attr( invite_anyone_invitation_message() ); ?>" />
						<?php endif; ?>

					</li>

					<?php if ( invite_anyone_are_groups_running() ) : ?>
						<?php if ( 'yes' === $iaoptions['can_send_group_invites_email'] && bp_has_groups( 'per_page=10000&type=alphabetical&user_id=' . bp_loggedin_user_id() ) ) : ?>
							<li>
								<p><?php esc_html_e( '(optional) Select some groups. Invitees will receive invitations to these groups when they join the site.', 'commons-in-a-box' ); ?></p>
								<ul id="invite-anyone-group-list" class="inline-element-list row group-list">
									<?php
									while ( bp_groups() ) :
										bp_the_group();

										$group_avatar = bp_core_fetch_avatar(
											array(
												'item_id' => bp_get_group_id(),
												'object'  => 'group',
												'type'    => 'full',
												'html'    => false,
											)
										);
										?>
										<?php
										// Enforce per-group invitation settings
										if ( ! bp_groups_user_can_send_invites( bp_get_group_id() ) || 'anyone' !== invite_anyone_group_invite_access_test( bp_get_group_id() ) ) {
											continue;
										}
										?>
										<li class="col-md-8 col-sm-12">
											<div class="group-item-wrapper pointer">
												<label for="invite_anyone_groups-<?php bp_group_id(); ?>" class="invite-anyone-group-name">
													<div class="row">
														<div class="col-xs-2"><input type="checkbox" class="no-margin no-margin-top" name="invite_anyone_groups[]" id="invite_anyone_groups-<?php bp_group_id(); ?>" value="<?php bp_group_id(); ?>" <?php checked( bp_get_group_id() === (int) $from_group || array_search( bp_get_group_id(), $returned_groups, true ) ); ?> /></div>
														<div class="col-xs-8"><img class="img-responsive" src="<?php echo esc_attr( $group_avatar ); ?>" alt="<?php echo esc_attr( bp_get_group_name() ); ?>"/></div>
														<div class="col-xs-14"><?php bp_group_name(); ?></div>
													</div>
												</label>
											</div>

										</li>
									<?php endwhile; ?>

								</ul>
							</li>
						<?php endif; ?>

					<?php endif; ?>

					<?php do_action( 'invite_anyone_addl_fields' ); ?>

				</ol>
			</div>
		</div>

		<div class="submit">
			<input type="submit" name="invite-anyone-submit" id="invite-anyone-submit" class="btn btn-primary btn-margin btn-margin-top" value="<?php esc_attr_e( 'Send Invites', 'commons-in-a-box' ); ?> " />
		</div>

		<?php wp_nonce_field( 'invite_anyone_send_by_email', 'ia-send-by-email-nonce' ); ?>
	</form>
	<?php
}

/**
 * Custom invite anyone email textarea
 *
 * @param type $returned_emails
 */
function openlab_invite_anyone_email_fields( $returned_emails = false ) {
	if ( is_array( $returned_emails ) ) {
		$returned_emails = implode( "\n", $returned_emails );
	}
	?>
	<textarea name="invite_anyone_email_addresses" class="invite-anyone-email-addresses form-control" id="invite-anyone-email-addresses" rows="7"><?php echo esc_textarea( $returned_emails ); ?></textarea>
	<?php
}

function openlab_invite_anyone_screen_two_content() {
	global $bp;

	// Load the pagination helper
	if ( ! class_exists( 'BBG_CPT_Pag' ) ) {
		require_once BP_INVITE_ANYONE_DIR . 'lib/bbg-cpt-pag.php';
	}
	$pagination = new BBG_CPT_Pag();

	$inviter_id = bp_loggedin_user_id();

	// phpcs:disable WordPress.Security.NonceVerification
	if ( isset( $_GET['sort_by'] ) ) {
		$sort_by = $_GET['sort_by'];
	} else {
		$sort_by = 'date_invited';
	}

	if ( isset( $_GET['order'] ) ) {
		$order = $_GET['order'];
	} else {
		$order = 'DESC';
	}
	// phpcs:enable WordPress.Security.NonceVerification

	$base_url = $bp->displayed_user->domain . $bp->invite_anyone->slug . '/sent-invites/';
	?>

	<?php $invites = invite_anyone_get_invitations_by_inviter_id( bp_loggedin_user_id(), $sort_by, $order, $pagination->get_per_page, $pagination->get_paged ); ?>

	<?php $pagination->setup_query( $invites ); ?>

	<?php if ( $invites->have_posts() ) : ?>
		<div class="form-panel sent-invites-panel">
			<div class="panel panel-default">
				<div class="panel-heading">
					<span class="bold">
						<?php esc_html_e( 'Sent Invites', 'commons-in-a-box' ); ?>
					</span>

					<div class="pull-right pagination-viewing">
						<?php $pagination->currently_viewing_text(); ?>
					</div>
				</div>

				<div class="panel-body">

					<p id="sent-invites-intro"><?php esc_html_e( 'You have sent invitations to the following people.', 'commons-in-a-box' ); ?></p>

					<thead>
						<tr>
							<th scope="col" class="col-delete-invite"></th>

							<?php
							$th_class   = 'email' === $sort_by ? 'sort-by-me' : '';
							$link_order = 'email' === $sort_by && 'ASC' === $order ? 'DESC' : 'ASC';
							?>
							<th scope="col" class="col-email <?php echo esc_attr( $th_class ); ?>">
								<a class="<?php echo esc_attr( $order ); ?>" href="<?php echo esc_attr( $base_url . '?sort_by=email&amp;order=' . $link_order ); ?>">
									<?php esc_html_e( 'Invited email address', 'commons-in-a-box' ); ?>
								</a>
							</th>

							<th scope="col" class="col-group-invitations"><?php esc_html_e( 'Group invitations', 'commons-in-a-box' ); ?></th>

							<?php
							$th_class   = 'date_invited' === $sort_by ? 'sort-by-me' : '';
							$link_order = 'date_invited' === $sort_by && 'ASC' === $order ? 'DESC' : 'ASC';
							?>
							<th scope="col" class="col-date-invited <?php echo esc_attr( $th_class ); ?>">
								<a class="<?php echo esc_attr( $order ); ?>" href="<?php echo esc_attr( $base_url . '?sort_by=date_invited&amp;order=' . $link_order ); ?>">
									<?php echo esc_html( _x( 'Sent', 'Invitation management column header', 'commons-in-a-box' ) ); ?>
								</a>
							</th>

							<?php
							$th_class   = 'date_joined' === $sort_by ? 'sort-by-me' : '';
							$link_order = 'date_joined' === $sort_by && 'ASC' === $order ? 'DESC' : 'ASC';
							?>
							<th scope="col" class="col-date-joined <?php echo esc_attr( $th_class ); ?>">
								<a class="<?php echo esc_attr( $order ); ?>" href="<?php echo esc_attr( $base_url . '?sort_by=date_joined&amp;order=' . $link_order ); ?>">
									<?php echo esc_html( _x( 'Accepted', 'Invitation management column header', 'commons-in-a-box' ) ); ?>
								</a>
							</th>
						</tr>
					</thead>

					<tfoot>
						<tr id="batch-clear">
							<td colspan="5" >
								<div id="invite-anyone-clear-links" class="inline-element-list">
									<a class="confirm btn btn-primary link-btn" href="<?php echo esc_attr( wp_nonce_url( $base_url . '?clear=accepted', 'invite_anyone_clear' ) ); ?>"><?php esc_html_e( 'Clear all accepted invitations', 'commons-in-a-box' ); ?></a>
									<a class="confirm btn btn-primary link-btn" href="<?php echo esc_attr( wp_nonce_url( $base_url . '?clear=all', 'invite_anyone_clear' ) ); ?>"><?php esc_html_e( 'Clear all invitations', 'commons-in-a-box' ); ?></a>
								</div>
							</td>
						</tr>
					</tfoot>

					<tbody>
						<?php
						while ( $invites->have_posts() ) :
							$invites->the_post();
							?>

							<?php
							$emails = wp_get_post_terms( get_the_ID(), invite_anyone_get_invitee_tax_name() );

							// Should never happen, but was messing up my test env
							if ( empty( $emails ) ) {
								continue;
							}

							// Before storing taxonomy terms in the db, we replaced "+" with ".PLUSSIGN.", so we need to reverse that before displaying the email address.
							$email = str_replace( '.PLUSSIGN.', '+', $emails[0]->name );

							$post_id = get_the_ID();

							$query_string = preg_replace( '|clear=[0-9]+|', '', $_SERVER['QUERY_STRING'] );

							$clear_url  = ( $query_string ) ? $base_url . '?' . $query_string . '&clear=' . $post_id : $base_url . '?clear=' . $post_id;
							$clear_url  = wp_nonce_url( $clear_url, 'invite_anyone_clear' );
							$clear_link = '<a class="clear-entry confirm" title="' . esc_attr__( 'Clear this invitation', 'commons-in-a-box' ) . '" href="' . esc_attr( $clear_url ) . '">x<span></span></a>';

							$groups = wp_get_post_terms( get_the_ID(), invite_anyone_get_invited_groups_tax_name() );
							if ( ! empty( $groups ) ) {
								$group_names = '<ul class="inline-element-list">';
								foreach ( $groups as $group_term ) {
									$group        = new BP_Groups_Group( $group_term->name );
									$group_names .= '<li>' . esc_html( bp_get_group_name( $group ) ) . '</li>';
								}
								$group_names .= '</ul>';
							} else {
								$group_names = '-';
							}

							global $post;

							$date_invited = invite_anyone_format_date( $post->post_date );

							$accepted = get_post_meta( get_the_ID(), 'bp_ia_accepted', true );

							if ( $accepted ) :
								$date_joined = invite_anyone_format_date( $accepted );
								$accepted    = true;
							else :
								$date_joined = '-';
								$accepted    = false;
							endif;
							?>

							<?php $tr_class = $accepted ? 'accepted' : ''; ?>

							<tr class="<?php echo esc_attr( $tr_class ); ?>">
								<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<td class="col-delete-invite"><?php echo $clear_link; ?></td>
								<td class="col-email"><?php echo esc_html( $email ); ?></td>
								<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<td class="col-group-invitations"><?php echo $group_names; ?></td>
								<td class="col-date-invited"><?php echo esc_html( $date_invited ); ?></td>
								<td class="date-joined hidden-xs col-date-joined"><span></span><?php echo esc_html( $date_joined ); ?></td>
							</tr>
						<?php endwhile ?>
					</tbody>
				</table>

				<div class="ia-pagination">
					<div class="pag-links">
						<?php $pagination->paginate_links(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>


	<?php else : ?>

		<div class="info group-list row" id="message">
			<div class="col-md-24">
				<p class="bold"><?php esc_html_e( "You haven't sent any email invitations yet.", 'commons-in-a-box' ); ?></p>
			</div>
	</div>

	<?php endif; ?>
	<?php
}
