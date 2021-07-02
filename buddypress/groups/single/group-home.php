<?php
/**
 * Group "home" template.
 *
 * Called from home.php, this is the template that powers the main Group page.
 *
 * @since 1.0.0
 */
?>

<?php
$group_type = cboxol_get_group_group_type( bp_get_current_group_id() );
if ( is_wp_error( $group_type ) ) {
	return;
}

$group_slug = bp_get_group_slug();

$group_id          = bp_get_current_group_id();
$group_name        = groups_get_current_group()->name;
$group_description = groups_get_current_group()->description;

// Remove items that have been deleted, or have incomplete values.
$clone_history = openlab_get_group_clone_history_data( $group_id );
$clone_history = array_filter(
	$clone_history,
	function( $item ) {
		return ! empty( $item['group_creator_id'] );
	}
);

// Remove items that exactly match the credits of the current group.
$this_item_clone_data = openlab_get_group_data_for_clone_history( $group_id );

$clone_history = array_filter(
	$clone_history,
	function( $item ) use ( $this_item_clone_data ) {
		return $item['group_admins'] !== $this_item_clone_data['group_admins'];
	}
);

$has_non_member_creator  = false;
$has_non_contact_creator = false;

$additional_text = openlab_get_group_creators_additional_text( $group_id );

$all_group_contacts = cboxol_get_all_group_contact_ids( $group_id );

$group_creators = openlab_get_group_creators( $group_id );
foreach ( $group_creators as $group_creator ) {
	if ( 'member' === $group_creator['type'] ) {
		$user = get_user_by( 'slug', $group_creator['member-login'] );

		if ( ! $user || ! in_array( $user->ID, $all_group_contacts, true ) ) {
			$has_non_contact_creator = true;
			break;
		}
	} elseif ( 'non-member' === $group_creator['type'] ) {
		$has_non_member_creator = true;
		break;
	}
}

$credits_chunks = [];

/*
 * Non-clones show Acknowledgements only if Creators differ from Contacts,
 * or if there is Additional Text to show.
 */
$show_acknowledgements = false;
if ( ! $clone_history || $has_non_member_creator || $has_non_contact_creator ) {
	$credits_markup = '';

	$additional_text = openlab_get_group_creators_additional_text( $group_id );

	if ( $has_non_member_creator || $has_non_contact_creator ) {
		$creator_items = array_map(
			function( $creator ) {
				switch ( $creator['type'] ) {
					case 'member' :
						$user = get_user_by( 'slug', $creator['member-login'] );

						if ( ! $user ) {
							return null;
						}

						return sprintf(
							'<a href="%s">%s</a>',
							esc_attr( bp_core_get_user_domain( $user->ID ) ),
							esc_html( bp_core_get_user_displayname( $user->ID ) )
						);

					case 'non-member' :
						return esc_html( $creator['non-member-name'] );
				}
			},
			$group_creators
		);

		$creator_items = array_filter( $creator_items );

		if ( $creator_items ) {
			$show_acknowledgements = true;

			$credits_intro_text = sprintf(
				// translators: Names/links of group 'Creators'
				esc_html__( 'Acknowledgements: Created by: %s', 'commons-in-a-box' ),
				implode( ', ', $creator_items )
			);

			$credits_chunks[] = [
				'intro' => $credits_intro_text,
				'items' => '',
			];
		}

		if ( $clone_history ) {
			$clone_intro_text = esc_html__( 'It is based on the following:', 'commons-in-a-box' );

			$credits_chunks[] = [
				'intro' => $clone_intro_text,
				'items' => openlab_format_group_clone_history_data_list( $clone_history ),
			];
		}

		if ( $additional_text ) {
			$post_credits_markup = '<p>' . wp_kses( $additional_text, openlab_creators_additional_text_allowed_tags() ) . '</p>';
		}
	} elseif ( $additional_text ) {
		$show_acknowledgements = true;
		$credits_intro_text    = sprintf(
			// translators: Acknowledgement text as provided by group administrator
			esc_html__( 'Acknowledgements: %s', 'commons-in-a-box' ),
			wp_kses( $additional_text, openlab_creators_additional_text_allowed_tags() )
		);

		$credits_chunks[] = [
			'intro' => $credits_intro_text,
			'items' => '',
		];
	}
} else {
	$credits_markup     = openlab_format_group_clone_history_data_list( $clone_history );
	$credits_intro_text = esc_html__( 'Acknowledgements: Based on the following:', 'commons-in-a-box' );

	$credits_chunks[] = [
		'intro' => $credits_intro_text,
		'items' => $credits_markup,
	];

	$show_acknowledgements = true;
}

$academic_unit_data = cboxol_get_object_academic_unit_data_for_display(
	array(
		'object_id'   => $group_id,
		'object_type' => 'group',
	)
);
?>

<div class="wrapper-block visible-xs sidebar mobile-group-site-links">
	<?php openlab_bp_group_site_pages( true ); ?>
</div>

<?php if ( bp_is_group_home() ) : ?>
	<div id="<?php echo esc_attr( $group_type->get_slug() ); ?>-header" class="group-header row">

		<div id="<?php echo esc_attr( $group_type->get_slug() ); ?>-header-avatar" class="alignleft group-header-avatar col-sm-8">
			<div class="padded-img darker">
				<?php
				$group_avatar = bp_core_fetch_avatar(
					array(
						'item_id' => $group_id,
						'object'  => 'group',
						'type'    => 'full',
						'html'    => false,
					)
				);
				?>
				<img class="img-responsive" src="<?php echo esc_attr( $group_avatar ); ?>" alt="<?php echo esc_attr( $group_name ); ?>"/>

				<?php openlab_group_single_badges(); ?>
			</div>

			<?php if ( is_user_logged_in() ) : ?>
				<div id="group-action-wrapper">
					<?php do_action( 'bp_group_header_actions' ); ?>
				</div>
			<?php endif; ?>
			<?php openlab_render_message(); ?>
		</div><!-- #<?php echo esc_html( $group_type->get_slug() ); ?>-header-avatar -->

		<div id="<?php echo esc_attr( $group_type->get_slug() ); ?>-header-content" class="col-sm-16 col-xs-24 alignleft group-header-content group-<?php echo esc_attr( $group_id ); ?>">

			<?php do_action( 'bp_before_group_header_meta' ); ?>

			<?php $status_message = openlab_group_status_message(); ?>

			<?php if ( $group_type->get_is_course() ) : ?>
				<div class="info-panel panel panel-default no-margin no-margin-top">
					<?php
					$course_code  = groups_get_groupmeta( $group_id, 'cboxol_course_code' );
					$section_code = groups_get_groupmeta( $group_id, 'cboxol_section_code' );
					$group_term   = openlab_get_group_term( $group_id );
					?>
					<div class="table-div">
						<?php
						if ( bp_is_group_home() && '' !== $status_message ) {

							do_action( 'bp_before_group_status_message' )
							?>

							<div class="table-row row">
								<div class="col-xs-24 status-message italics"><?php echo esc_html( $status_message ); ?></div>
							</div>

							<?php
							do_action( 'bp_after_group_status_message' );
						}
						?>

						<?php foreach ( $academic_unit_data as $type_data ) : ?>
							<div class="table-row row">
								<div class="bold col-sm-7">
									<?php echo esc_html( $type_data['label'] ); ?>
								</div>

								<div class="col-sm-17">
									<?php echo esc_html( $type_data['value'] ); ?>
								</div>
							</div>
						<?php endforeach; ?>

						<?php

						$group_contacts = groups_get_groupmeta( $group_id, 'group_contact', false );
						// Backward compatibility.
						if ( ! $group_contacts ) {
							$group_contacts = [
								groups_get_current_group()->admins[0]->user_id,
							];

							$additional_faculty = groups_get_groupmeta( $group_id, 'additional_faculty', false );
							if ( $additional_faculty ) {
								$group_contacts = array_merge( $group_contacts, $additional_faculty );
							}
						}

						$group_contact_label = $group_type->get_label( 'group_contact' );
						?>

						<?php if ( $group_contacts ) : ?>
							<div class="table-row row">
								<div class="bold col-sm-7"><?php echo esc_html( $group_contact_label ); ?></div>
								<?php // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<div class="col-sm-17 row-content"><?php echo implode( ', ', array_map( 'bp_core_get_userlink', $group_contacts ) ); ?></div>
							</div>
						<?php endif; ?>

						<?php if ( $course_code ) : ?>
							<div class="table-row row">
								<div class="bold col-sm-7"><?php echo esc_html( $group_type->get_label( 'course_code' ) ); ?></div>
								<div class="col-sm-17 row-content"><?php echo esc_html( $course_code ); ?></div>
							</div>
						<?php endif; ?>

						<?php if ( $section_code ) : ?>
							<div class="table-row row">
								<div class="bold col-sm-7"><?php echo esc_html( $group_type->get_label( 'section_code' ) ); ?></div>
								<div class="col-sm-17 row-content"><?php echo esc_html( $section_code ); ?></div>
							</div>
						<?php endif; ?>

						<?php if ( $group_term ) : ?>
							<div class="table-row row">
								<div class="bold col-sm-7"><?php esc_html_e( 'Term', 'commons-in-a-box' ); ?></div>
								<div class="col-sm-17 row-content"><?php echo esc_html( $group_term ); ?></div>
							</div>
						<?php endif; ?>
						<?php if ( function_exists( 'bpcgc_get_group_selected_terms' ) ) : ?>
							<?php $group_terms = bpcgc_get_group_selected_terms( $group_id, true ); ?>
							<?php if ( $group_terms ) : ?>
								<div class="table-row row">
									<div class="bold col-sm-7"><?php esc_html_e( 'Category', 'commons-in-a-box' ); ?></div>
									<div class="col-sm-17 row-content"><?php echo esc_html( implode( ', ', wp_list_pluck( $group_terms, 'name' ) ) ); ?></div>
								</div>
							<?php endif; ?>
						<?php endif; ?>

						<div class="table-row row">
							<div class="bold col-sm-7"><?php esc_html_e( 'Course Description', 'commons-in-a-box' ); ?></div>
							<?php // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<div class="col-sm-17 row-content"><?php echo apply_filters( 'the_content', $group_description ); ?></div>
						</div>

						<?php if ( openlab_group_can_be_cloned( bp_get_current_group_id() ) ) : ?>
							<div class="table-row row">
								<div class="col-xs-24 status-message italics">
									<?php esc_html_e( 'May be cloned by logged-in community members.', 'commons-in-a-box' ); ?>

									<?php
									$exclude_hidden   = ! current_user_can( 'bp_moderate' );
									$descendant_count = openlab_get_clone_descendant_count_of_group( $group_id, $exclude_hidden );
									?>

									<?php if ( $descendant_count > 0 ) : ?>
										<?php
										$view_clones_link = trailingslashit( bp_get_group_type_directory_permalink( $group_type->get_slug() ) );
										$view_clones_link = add_query_arg( 'descendant-of', $group_id, $view_clones_link );

										// translators: Number of times that the group has been cloned.
										$count_message = _n( 'It has been cloned or re-cloned %s time', 'It has been cloned or re-cloned %s times', $descendant_count, 'commons-in-a-box' );
										?>
										<?php echo esc_html( sprintf( $count_message, number_format_i18n( $descendant_count ) ) ); ?>; <a href="<?php echo esc_attr( $view_clones_link ); ?>"><?php esc_html_e( 'view clones', 'commons-in-a-box' ); ?></a>.
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $show_acknowledgements ) : ?>
							<div class="table-row row">
								<div class="col-xs-24 status-message clone-acknowledgements">
									<?php foreach ( $credits_chunks as $credits_chunk ) : ?>
										<?php if ( ! empty( $credits_chunk['intro'] ) ) : ?>
											<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											<p><?php echo $credits_chunk['intro']; ?></p>
										<?php endif; ?>

										<?php if ( ! empty( $credits_chunk['items'] ) ) : ?>
											<ul class="group-credits">
												<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
												<?php echo $credits_chunk['items']; ?>
											</ul>
										<?php endif; ?>
									<?php endforeach; ?>

									<?php if ( ! empty( $post_credits_markup ) ) : ?>
										<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php echo $post_credits_markup; ?>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

					</div>

				</div>

				<?php do_action( 'bp_group_header_meta' ); ?>

			<?php else : ?>

				<div class="info-panel panel panel-default no-margin no-margin-top">
					<div class="table-div">
						<div class="table-row row">
							<div class="col-xs-24 status-message italics"><?php echo esc_html( $status_message ); ?></div>
						</div>

						<?php foreach ( $academic_unit_data as $type_data ) : ?>
							<div class="table-row row">
								<div class="bold col-sm-7">
									<?php echo esc_html( $type_data['label'] ); ?>
								</div>

								<div class="col-sm-17">
									<?php echo esc_html( $type_data['value'] ); ?>
								</div>
							</div>
						<?php endforeach; ?>

						<?php
						$group_contacts      = groups_get_groupmeta( $group_id, 'group_contact', false );
						$group_contact_label = $group_type->get_label( 'group_contact' );
						?>

						<?php if ( $group_contacts ) : ?>
							<div class="table-row row">
								<div class="bold col-sm-7"><?php echo esc_html( $group_contact_label ); ?></div>
								<?php // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<div class="col-sm-17 row-content"><?php echo implode( ', ', array_map( 'bp_core_get_userlink', $group_contacts ) ); ?></div>
							</div>
						<?php endif; ?>

						<?php if ( function_exists( 'bpcgc_get_group_selected_terms' ) ) : ?>
							<?php $group_terms = bpcgc_get_group_selected_terms( $group_id, true ); ?>
							<?php if ( $group_terms ) : ?>
								<div class="table-row row">
									<div class="bold col-sm-7"><?php esc_html_e( 'Category', 'commons-in-a-box' ); ?></div>
									<div class="col-sm-17 row-content"><?php echo esc_html( implode( ', ', wp_list_pluck( $group_terms, 'name' ) ) ); ?></div>
								</div>
							<?php endif; ?>
						<?php endif; ?>

						<div class="table-row row">
							<div class="bold col-sm-7"><?php esc_html_e( 'Description', 'commons-in-a-box' ); ?></div>
							<div class="col-sm-17 row-content"><?php bp_group_description(); ?></div>
						</div>

						<?php if ( ! empty( $group_history ) ) : ?>
							<div class="table-row row">
								<div class="bold col-sm-7"><?php esc_html_e( 'Credits', 'commons-in-a-box' ); ?></div>
								<?php // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<div class="col-sm-17 row-content"><?php echo $group_history; ?></div>
							</div>
						<?php endif; ?>

						<?php if ( $group_type->get_is_portfolio() ) : ?>

							<div class="table-row row">
								<div class="bold col-sm-7"><?php esc_html_e( 'Member Profile', 'commons-in-a-box' ); ?></div>
								<?php // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<div class="col-sm-17 row-content"><?php echo bp_core_get_userlink( openlab_get_user_id_from_portfolio_group_id( bp_get_group_id() ) ); ?></div>
							</div>

						<?php endif; ?>

						<?php if ( openlab_group_can_be_cloned( bp_get_current_group_id() ) ) : ?>
							<div class="table-row row">
								<div class="col-xs-24 status-message italics">

									<?php esc_html_e( 'May be cloned by logged-in community members.', 'commons-in-a-box' ); ?>

									<?php
									$exclude_hidden   = ! current_user_can( 'bp_moderate' );
									$descendant_count = openlab_get_clone_descendant_count_of_group( $group_id, $exclude_hidden );
									?>

									<?php if ( $descendant_count > 0 ) : ?>
										<?php
										$view_clones_link = trailingslashit( bp_get_group_type_directory_permalink( $group_type->get_slug() ) );
										$view_clones_link = add_query_arg( 'descendant-of', $group_id, $view_clones_link );

										// translators: Number of times that the group has been cloned.
										$count_message = _n( 'It has been cloned or re-cloned %s time', 'It has been cloned or re-cloned %s times', $descendant_count, 'commons-in-a-box' );
										?>
										<?php echo esc_html( sprintf( $count_message, number_format_i18n( $descendant_count ) ) ); ?>; <a href="<?php echo esc_attr( $view_clones_link ); ?>"><?php esc_html_e( 'view clones', 'commons-in-a-box' ); ?></a>.
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>


					</div>
				</div>

			<?php endif; ?>
		</div><!-- .header-content -->

		<?php do_action( 'bp_after_group_header' ); ?>

	</div><!--<?php echo esc_html( $group_type->get_slug() ); ?>-header -->

<?php endif; ?>

<?php
openlab_group_profile_activity_list();
