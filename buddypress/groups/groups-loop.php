<?php
/**
 * Group loop
 *
 * @todo All the other group templates (my-*.php as well as *-archive.php) should at some point
 *       be refactored to include this file instead. Filter stuff will probably have to be
 *       abstracted
 */
// Set up the group meta filters

// Set up the bp_has_groups() args: per_page, page, search_terms
$group_sort = openlab_get_current_filter( 'sort' );
$group_args = array(
	'per_page'     => 12,
	'meta_query'   => array(),
	'search_terms' => openlab_get_current_filter( 'search' ),
	'tax_query'    => array( 'relation' => 'AND' ),
	'type'         => $group_sort,
);

if ( openlab_is_search_results_page() ) {
	$current_group_type = openlab_get_current_filter( 'group-types' );
	if ( ! $current_group_type ) {
		$current_group_type = array_map(
			function ( $type ) {
				return $type->get_slug();
			},
			cboxol_get_group_types()
		);
	}
} elseif ( bp_is_user_groups() ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$group_type_raw = isset( $_GET['group_type'] ) ? wp_unslash( $_GET['group_type'] ) : '';
	$group_type_obj = cboxol_get_group_type( $group_type_raw );
	if ( ! is_wp_error( $group_type_obj ) ) {
		$current_group_type = $group_type_obj->get_slug();
	}
} else {
	$current_group_type = bp_get_current_group_directory_type();
}

if ( ! empty( $current_group_type ) ) {
	$group_args['group_type'] = $current_group_type;
}

// @todo 'all' needs special treatment once tax queries work without shim.
$academic_units = array();
// phpcs:disable WordPress.Security.NonceVerification.Recommended
foreach ( $_GET as $get_key => $get_value ) {
	if ( 'academic-unit-' !== substr( $get_key, 0, 14 ) ) {
		continue;
	}

	$academic_units[] = urldecode( wp_unslash( $get_value ) );
}
// phpcs:enable WordPress.Security.NonceVerification.Recommended

$academic_units = array_filter( $academic_units );

if ( ! empty( $academic_units ) ) {
	$academic_units_tax_query = cboxol_get_tax_query_for_academic_units(
		array(
			'units'       => $academic_units,
			'object_type' => 'group',
		)
	);

	if ( $academic_units_tax_query ) {
		$group_args['tax_query']['academic_units'] = $academic_units_tax_query;
	}
}

$category = openlab_get_current_filter( 'cat' );
$the_term = openlab_get_current_filter( 'term' );

// Set up filters
if ( ! empty( $the_term ) && 'term_all' !== strtolower( $the_term ) ) {
	$academic_term_tax_query = cboxol_get_tax_query_for_academic_term( $the_term );

	if ( $academic_term_tax_query ) {
		$group_args['tax_query']['academic_term'] = $academic_term_tax_query;
	}
}

$member_type = openlab_get_current_filter( 'member_type' );
if ( $member_type && 'all' !== $member_type ) {
	$group_args['meta_query'][] = array(
		'key'   => 'portfolio_user_type',
		'value' => $member_type,
	);
}

$is_open = openlab_get_current_filter( 'is_open' );
if ( $is_open ) {
	$group_args['meta_query']['blog_public'] = [
		'key'      => 'blog_public',
		'value'    => [ '1', '0' ],
		'operator' => 'IN',
	];

	$group_args['status'] = 'public';
}

$is_cloneable = openlab_get_current_filter( 'is_cloneable' );
if ( $is_cloneable ) {
	$group_args['meta_query'][] = array(
		'key'     => 'enable_sharing',
		'compare' => 'EXISTS',
	);
}

if ( $category ) {
	if ( 'cat_all' === $category ) {
		$terms    = get_terms( 'bp_group_categories' );
		$term_ids = wp_list_pluck( $terms, 'term_id' );
	} else {
		$term_obj = get_term_by( 'slug', $category, 'bp_group_categories' );
		$term_ids = $term_obj->term_id;
	}

	$group_args['tax_query']['group_categories'] = array(
		'taxonomy' => 'bp_group_categories',
		'terms'    => $term_ids,
		'field'    => 'term_id',
	);
}

$descendant_of = openlab_get_current_filter( 'descendant-of' );
if ( $descendant_of ) {
	$descendant_of_group = groups_get_group( $descendant_of );

	$descendant_of_admin_ids   = cboxol_get_all_group_contact_ids( $descendant_of );
	$descendant_of_admin_links = array_map( 'bp_core_get_userlink', $descendant_of_admin_ids );

	$descendant_of_string = sprintf(
		/* translators: 1. Link to group whose descendants are being viewed; 2. list of links to administrators of that group */
		esc_html__( 'Displaying clones of %1$s by %2$s.', 'commons-in-a-box' ),
		sprintf(
			'<a href="%s">%s</a>',
			esc_attr( bp_get_group_permalink( $descendant_of_group ) ),
			esc_html( $descendant_of_group->name )
		),
		implode( ', ', $descendant_of_admin_links )
	);
}

// Exclude private groups if not current user's profile or don't have moderate access.
$private_groups = [];
if ( bp_is_user() ) {
	$private_groups = openlab_get_user_private_memberships( bp_displayed_user_id() );
	if ( ! bp_is_my_profile() && ! current_user_can( 'bp_moderate' ) ) {
		$group_args['exclude'] = $private_groups;
	}
}

?>

<?php if ( bp_has_groups( $group_args ) ) : ?>
	<div id="openlab-main-content"></div>

	<div class="row group-archive-header-row">
		<?php if ( openlab_is_my_profile() ) : ?>
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo openlab_submenu_markup( 'groups', $group_type_obj, false ); ?>
		<?php elseif ( $descendant_of ) : ?>
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $descendant_of_string; ?>
		<?php elseif ( bp_is_groups_directory() || openlab_is_search_results_page() ) : ?>
			<div class="col-lg-19 col-md-18 col-sm-16">
				<?php esc_html_e( 'Narrow down your results using the search filters.', 'commons-in-a-box' ); ?>
			</div>
		<?php endif; ?>

		<div class="group-count pull-right col-lg-5 col-md-6 col-sm-8"><?php cuny_groups_pagination_count(); ?></div>
	</div>

	<div id="group-list" class="item-list group-list row">
		<?php if ( bp_is_user_groups() ) : ?>
			<div class="my-group-archive-sort form-inline">
				<label for="openlab-sort-my-groups" class="sr-only"><?php esc_html_e( 'Select sort order', 'commons-in-a-box' ); ?></label>
				<select id="openlab-sort-my-groups" class="form-control">
					<option value="alphabetical" <?php selected( $group_sort, 'alphabetical' ); ?>><?php esc_html_e( 'Alphabetical', 'commons-in-a-box' ); ?></option>
					<option value="newest" <?php selected( $group_sort, 'newest' ); ?>><?php esc_html_e( 'Newest', 'commons-in-a-box' ); ?></option>
					<option value="active" <?php selected( $group_sort, 'active' ); ?>><?php esc_html_e( 'Last Active', 'commons-in-a-box' ); ?></option>
				</select>
			</div>
		<?php endif; ?>

		<?php
		while ( bp_groups() ) :
			bp_the_group();
			$group_id       = bp_get_group_id();
			$group_site_url = openlab_get_group_site_url( $group_id );
			$group_type     = cboxol_get_group_group_type( $group_id );

			$classes = 'group-item col-xs-12';
			if ( openlab_group_has_badges( $group_id ) || openlab_group_is_open( $group_id ) || openlab_group_can_be_cloned( $group_id ) ) {
				$classes .= ' group-has-badges';
			}

			$group_avatar = bp_core_fetch_avatar(
				array(
					'item_id' => $group_id,
					'object'  => 'group',
					'type'    => 'full',
					'html'    => false,
				)
			);

			?>

			<div class="<?php echo esc_attr( $classes ); ?>">
				<div class="group-item-wrapper">
					<div class="row">
						<div class="item-avatar alignleft col-xs-6">
							<?php if ( openlab_is_search_results_page() ) : ?>
								<div class="group-type-flag"><?php echo esc_html( $group_type->get_label( 'singular' ) ); ?></div>
							<?php endif; ?>

							<a href="<?php bp_group_permalink(); ?>"><img class="img-responsive" src="<?php echo esc_attr( $group_avatar ); ?>" alt="<?php echo esc_attr( bp_get_group_name() ); ?>"/></a>

							<?php if ( $group_site_url && cboxol_site_can_be_viewed( $group_id ) ) : ?>
								<a class="group-site-link" href="<?php echo esc_url( $group_site_url ); ?>"><?php esc_html_e( 'Visit Site', 'commons-in-a-box' ); ?><span class="fa fa-chevron-circle-right" aria-hidden="true"></span></a>
							<?php endif; ?>
						</div>

						<div class="item col-xs-18">
							<div class="item-content-wrapper">
								<p class="item-title h2">
									<a class="no-deco he-fly hyphenate truncate-on-the-fly" href="<?php bp_group_permalink(); ?>" data-basevalue="50" data-minvalue="20" data-basewidth="290"><?php bp_group_name(); ?></a>
									<span class="original-copy hidden"><?php bp_group_name(); ?></span>
								</p>

								<?php if ( $group_type->get_is_course() ) : ?>
									<div class="info-line uppercase">
										<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php echo openlab_output_group_contact_line( $group_id ); ?>
									</div>
									<div class="info-line uppercase">
										<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php echo openlab_output_course_info_line( $group_id ); ?>
									</div>
								<?php elseif ( ! $group_type->get_is_portfolio() ) : ?>
									<div class="info-line uppercase">
										<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php echo openlab_output_group_contact_line( $group_id ); ?>
									</div>
								<?php endif; ?>

								<div class="description-line">
									<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<p class="hyphenate truncate-on-the-fly" data-basevalue="105" data-basewidth="250"><?php echo bp_get_group_description_excerpt(); ?></p>
								</div>

								<?php if ( current_user_can( 'bp_moderate' ) && in_array( $group_id, $private_groups, true ) ) : ?>
								<p class="private-membership-indicator"><span class="fa fa-eye-slash"></span> <?php esc_html_e( 'Membership hidden', 'commons-in-a-box' ); ?></p>
								<?php endif; ?>
							</div><!-- .item-content-wrapper -->
						</div>
					</div><!-- .row -->

					<?php do_action( 'openlab_theme_after_group_group_directory' ); ?>
				</div><!-- .group-item-wrapper -->
			</div>
		<?php endwhile; ?>
		</div>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo openlab_groups_pagination_links(); ?>
		</div>
	<?php else : ?>
	<div class="row group-archive-header-row">
		<?php if ( openlab_is_my_profile() ) : ?>
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo openlab_submenu_markup( 'groups', $group_type_obj, false ); ?>
		<?php else : ?>
			<div class="current-group-filters current-portfolio-filters col-sm-19">&nbsp;</div>
		<?php endif; ?>
	</div>

	<div class="widget-error">
		<?php esc_html_e( 'There are no items to display', 'commons-in-a-box' ); ?>
	</div>
<?php endif; ?>
