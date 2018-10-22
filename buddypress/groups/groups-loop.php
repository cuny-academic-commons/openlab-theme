<?php
/**
 * Group loop
 *
 * @todo All the other group templates (my-*.php as well as *-archive.php) should at some point
 *       be refactored to include this file instead. Filter stuff will probably have to be
 *       abstracted
 */
// Set up the group meta filters
global $bp;

// Set up the bp_has_groups() args: per_page, page, search_terms
$group_args = array(
	'per_page' => 12,
	'meta_query' => array(),
	'tax_query' => array(),
);

$group_type_slug = bp_get_current_group_directory_type();
if ( ! $group_type_slug && ! empty( $_GET['group_type'] ) ) {
	$group_type_slug = wp_unslash( urldecode( $_GET['group_type'] ) );
}

$group_type = cboxol_get_group_type( $group_type_slug );

// @todo
$search_terms = $search_terms_raw = '';

if ( ! empty( $_POST['group_search'] ) ) {
	$search_terms_raw = $_POST['group_search'];
	$search_terms = 'search_terms=' . $search_terms_raw . '&';
}
if ( ! empty( $_GET['search'] ) ) {
	$search_terms_raw = $_GET['search'];
	$search_terms = 'search_terms=' . $search_terms_raw . '&';
}

$group_args['search_terms'] = $search_terms_raw;

// @todo 'all' needs special treatment once tax queries work without shim.
$academic_units = array();
foreach ( $_GET as $get_key => $get_value ) {
	if ( 'academic-unit-' !== substr( $get_key, 0, 14 ) ) {
		continue;
	}

	$academic_units[] = urldecode( wp_unslash( $get_value ) );
}

$academic_units = array_filter( $academic_units );

if ( ! empty( $academic_units ) ) {
	$academic_units_tax_query = cboxol_get_tax_query_for_academic_units( array(
		'units' => $academic_units,
		'object_type' => 'group',
	) );

	if ( $academic_units_tax_query ) {
		$group_args['tax_query']['academic_units'] = $academic_units_tax_query;
	}
}

if ( ! empty( $_GET['cat'] ) ) {
	$categories = $_GET['cat'];
}

$term = isset( $_GET['term'] ) ? wp_unslash( urldecode( $_GET['term'] ) ) : '';

// Set up filters
if ( ! empty( $term ) && 'term_all' != strtolower( $term ) ) {
	$group_args['meta_query'][] = array(
		'key' => 'openlab_term',
		'value' => $term,
	);
}

if ( ! empty( $_GET['member_type'] ) && 'all' !== $_GET['member_type'] ) {
	$member_type = wp_unslash( $_GET['member_type'] );
	$group_args['meta_query'][] = array(
		'key' => 'portfolio_user_type',
		'value' => $member_type,
	);
}

if ( ! empty( $categories ) ) {

	if ( 'cat_all' === strtolower( $categories ) ) {

		$terms = get_terms( 'bp_group_categories' );
		$term_ids = wp_list_pluck( $terms, 'term_id' );
	} else {
		$term_obj = get_term_by( 'slug', $categories, 'bp_group_categories' );
		$term_ids = $term_obj->term_id;
	}

	$group_args['tax_query']['group_categories'] = array(
		'taxonomy' => 'bp_group_categories',
		'terms' => $term_ids,
		'field' => 'term_id',
	);
}

if ( ! empty( $_GET['group_sequence'] ) ) {
	$group_args['type'] = $_GET['group_sequence'];
}

?>

<?php if ( bp_has_groups( $group_args ) ) : ?>
	<div id="openlab-main-content"></div>

	<div class="row group-archive-header-row">
		<?php if ( bp_is_groups_directory() ) : ?>
			<div class="current-group-filters current-portfolio-filters col-md-18 col-sm-16">
				<?php openlab_current_directory_filters(); ?>
			</div>
		<?php elseif ( openlab_is_my_profile() ) : ?>
			<?php echo openlab_submenu_markup( 'groups', $group_type, false ); ?>
		<?php endif; ?>

		<div class="group-count pull-right col-lg-5 col-md-6 col-sm-8"><?php cuny_groups_pagination_count(); ?></div>
	</div>

	<div id="group-list" class="item-list group-list row">
		<?php
		while ( bp_groups() ) : bp_the_group();
			$group_id = bp_get_group_id();
			$group_site_url = openlab_get_group_site_url( $group_id );
			$group_type = cboxol_get_group_group_type( $group_id );
			?>

			<div class="group-item col-xs-12">
				<div class="group-item-wrapper">
					<div class="row">
						<div class="item-avatar alignleft col-xs-6">
							<a href="<?php bp_group_permalink() ?>"><img class="img-responsive" src ="<?php echo esc_url( bp_core_fetch_avatar( array( 'item_id' => $group_id, 'object' => 'group', 'type' => 'full', 'html' => false ) ) ); ?>" alt="<?php echo esc_attr( bp_get_group_name() ); ?>"/></a>
							<?php if ( $group_site_url && cboxol_site_can_be_viewed( $group_id ) ) : ?>
								<a class="group-site-link" href="<?php echo esc_url( $group_site_url ); ?>"><?php echo esc_html( $group_type->get_label( 'group_site' ) ); ?></a>
							<?php endif; ?>
						</div>

						<div class="item col-xs-18">
							<div class="item-content-wrapper">
								<p class="item-title h2">
									<a class="no-deco he-fly hyphenate" href="<?php bp_group_permalink() ?>" data-basevalue="<?php echo ( $group_type->get_is_course() ? 50 : 65 ) ?>" data-minvalue="20" data-basewidth="290"><?php bp_group_name() ?></a>
									<span class="original-copy hidden"><?php bp_group_name() ?></span>
								</p>

								<?php if ( $group_type->get_is_course() ) : ?>
									<div class="info-line uppercase">
										<?php echo openlab_output_course_info_line( $group_id ); ?>
									</div>
								<?php elseif ( $group_type->get_is_portfolio() ) : ?>
									<div class="info-line">
										<?php echo bp_core_get_userlink( openlab_get_user_id_from_portfolio_group_id( bp_get_group_id() ) ); ?>
									</div>
								<?php endif; ?>

								<div class="description-line">
									<p class="hyphenate truncate-on-the-fly" data-link="<?php echo bp_get_group_permalink() ?>" data-basevalue="105" data-basewidth="250"><?php echo bp_get_group_description_excerpt() ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endwhile; ?>
		</div>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php echo openlab_groups_pagination_links() ?>
		</div>
	<?php else : ?>
	<div class="row group-archive-header-row">
		<?php if ( openlab_is_my_profile() ) : ?>
			<?php echo openlab_submenu_markup( 'groups', $group_type, false ); ?>
		<?php endif; ?>
	</div>

	<div class="widget-error">
		<?php _e( 'There are no items to display', 'openlab-theme' ) ?>
	</div>
<?php endif; ?>
