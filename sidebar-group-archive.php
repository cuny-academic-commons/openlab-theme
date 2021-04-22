<?php

$group_type        = bp_get_current_group_directory_type();
$group_type_object = cboxol_get_group_type( $group_type );
if ( is_wp_error( $group_type_object ) ) {
	$group_type_object = null;
}

$sidebar_title = __( 'Search', 'commons-in-a-box' );

$reset_url = '';
if ( bp_is_members_directory() ) {
	$reset_url = bp_get_members_directory_permalink();
} elseif ( openlab_is_search_results_page() ) {
	$search_page = cboxol_get_brand_page( 'search-results' );
	$reset_url   = isset( $search_page['preview_url'] ) ? $search_page['preview_url'] : '';
} else {
	$reset_url = bp_get_group_type_directory_permalink( $group_type );
}

$is_search = openlab_is_search_results_page();

?>

<h2 class="sidebar-title"><?php echo esc_html( $sidebar_title ); ?></h2>
<div class="sidebar-block">
	<?php
	$unit_type_args = array();
	if ( bp_is_groups_directory() ) {
		$unit_type_args['group_type'] = bp_get_current_group_directory_type();
	} elseif ( bp_is_members_directory() ) {
		$member_type_slug = openlab_get_current_filter( 'member_type' );
		if ( $member_type_slug ) {
			$unit_type_args['member_type'] = $member_type_slug;
		}
	}

	$academic_unit_types = cboxol_get_academic_unit_types( $unit_type_args );

	$academic_unit_map = cboxol_get_academic_unit_map();
	echo '<script type="text/javascript">/* <![CDATA[ */
var OLAcadUnits = ' . wp_json_encode( $academic_unit_map ) . ';
/* ]]> */</script>';

	?>
	<div class="filter">
		<form id="group_seq_form" name="group_seq_form" action="#" method="get">
			<?php get_template_part( 'parts/sidebar/filter-search' ); ?>

			<p><?php esc_html_e( 'Narrow down your results using some of the filters below.', 'commons-in-a-box' ); ?></p>

			<div id="sidebarCustomSelect" class="custom-select-parent">
				<?php if ( $is_search ) : ?>
					<?php get_template_part( 'parts/sidebar/filter-group-type' ); ?>
				<?php endif; ?>

				<?php if ( ( bp_is_groups_directory() && $group_type_object && ! is_wp_error( $group_type_object ) && $group_type_object->get_is_portfolio() ) || bp_is_members_directory() || $is_search ) : ?>
					<?php get_template_part( 'parts/sidebar/filter-member-type' ); ?>
				<?php endif; ?>

				<?php foreach ( $academic_unit_types as $academic_unit_type ) : ?>
					<?php
					set_query_var( 'academic_unit_type', $academic_unit_type->get_slug() );
					get_template_part( 'parts/sidebar/filter-academic-unit' );
					?>
				<?php endforeach; ?>

				<?php if ( function_exists( 'bpcgc_get_terms_by_group_type' ) && ! bp_is_members_directory() ) : ?>
					<?php get_template_part( 'parts/sidebar/filter-group-categories' ); ?>
				<?php endif; ?>

				<?php if ( ( bp_is_groups_directory() && $group_type_object && $group_type_object->get_is_course() ) || $is_search ) : ?>
					<?php get_template_part( 'parts/sidebar/filter-term' ); ?>
				<?php endif; ?>

				<?php get_template_part( 'parts/sidebar/filter-sort' ); ?>

				<?php if ( ! bp_is_members_directory() ) : ?>
					<?php get_template_part( 'parts/sidebar/filter-open-cloneable' ); ?>
				<?php endif; ?>

				<?php if ( ! bp_is_members_directory() ) : ?>
					<?php get_template_part( 'parts/sidebar/filter-badges' ); ?>
				<?php endif; ?>
			</div>

			<div class="sidebar-buttons">
				<input class="btn btn-primary" type="submit" onchange="document.forms['group_seq_form'].submit();" value="<?php esc_attr_e( 'Submit', 'commons-in-a-box' ); ?>">
				<input class="btn btn-default" type="button" value="<?php esc_attr_e( 'Reset', 'commons-in-a-box' ); ?>" onClick="window.location.href = '<?php echo esc_url( $reset_url ); ?>'">
			</div>
		</div><!--filter-->
	</form>
</div>
