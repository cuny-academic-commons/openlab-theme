<?php
global $bp, $wp_query;
$group_type = bp_get_current_group_directory_type();
$group_type_object = cboxol_get_group_type( $group_type );
if ( is_wp_error( $group_type_object ) ) {
	$group_type_object = null;
}
$group_slug = $group_type . 's';

$sidebar_title = __( 'Search', 'openlab-theme' );

$member_type_slug = '';
if ( isset( $_GET['member_type'] ) ) {
	$member_type_slug = urldecode( wp_unslash( $_GET['member_type'] ) );
}

?>

<h2 class="sidebar-title"><?php echo $sidebar_title; ?></h2>
<div class="sidebar-block">
	<?php
	//determine class type for filtering
	$semester_color = 'passive';
	$sort_color = 'passive';
	$user_color = 'passive';
	$bpcgc_color = 'passive';

	//categories
	if ( empty( $_GET['cat'] ) ) {
		$display_option_bpcgc = 'Select Category';
		$option_value_bpcgc = '';
	} elseif ( $_GET['cat'] == 'cat_all' ) {
		$display_option_bpcgc = 'All';
		$option_value_bpcgc = 'cat_all';
	} else {
		$dept_color = 'active';
		$display_option_bpcgc = ucwords( str_replace( '-', ' ', $_GET['cat'] ) );
		$display_option_bpcgc = str_replace( 'And', '&', $display_option_bpcgc );
		$option_value_bpcgc = $_GET['cat'];
	}

	//semesters
	if ( empty( $_GET['semester'] ) ) {
		$_GET['semester'] = '';
	} else {
		$semester_color = 'active';
	}
	//processing the semester value - now dynamic instead of a switch statement
	if ( empty( $_GET['semester'] ) ) {
		$display_option_semester = 'Select Semester';
		$option_value_semester = '';
	} elseif ( $_GET['semester'] == 'semester_all' ) {
		$display_option_semester = 'All';
		$option_value_semester = 'semester_all';
	} else {
		$dept_color = 'active';
		$display_option_semester = ucfirst( str_replace( '-', ' ', $_GET['semester'] ) );
		$option_value_semester = $_GET['semester'];
	}

	//sequence filter - easy enough to keep this as a switch for now
	if ( empty( $_GET['group_sequence'] ) ) {
		$_GET['group_sequence'] = 'active';
	} else {
		$sort_color = 'active';
	}
	switch ( $_GET['group_sequence'] ) {
		case 'alphabetical':
			$display_option = 'Alphabetical';
			$option_value = 'alphabetical';
			break;
		case 'newest':
			$display_option = 'Newest';
			$option_value = 'newest';
			break;
		case 'active':
			$display_option = 'Last Active';
			$option_value = 'active';
			break;
		default:
			$display_option = 'Order By';
			$option_value = '';
			break;
	}

	$unit_type_args = array();
	if ( bp_is_groups_directory() ) {
		$unit_type_args['group_type'] = bp_get_current_group_directory_type();
	} elseif ( bp_is_members_directory() && $member_type_slug ) {
		$unit_type_args['member_type'] = $member_type_slug;
	}

	$academic_unit_types = cboxol_get_academic_unit_types( $unit_type_args );

	$academic_unit_map = cboxol_get_academic_unit_map();
	echo '<script type="text/javascript">/* <![CDATA[ */
var OLAcadUnits = ' . wp_json_encode( $academic_unit_map ) . ';
/* ]]> */</script>';

	?>
	<div class="filter">
		<p><?php esc_html_e( 'Narrow down your search using the filters or search box below.', 'openlab-theme' ); ?></p>
		<form id="group_seq_form" name="group_seq_form" action="#" method="get">
			<div id="sidebarCustomSelect" class="custom-select-parent">
				<?php foreach ( $academic_unit_types as $academic_unit_type ) : ?>
					<?php
					$url_param = 'academic-unit-' . $academic_unit_type->get_slug();
					$current_unit = isset( $_GET[ $url_param ] ) ? wp_unslash( $_GET[ $url_param ] ) : null;
					$color_class = empty( $current_unit ) ? 'passive' : 'active';
					$units_of_type = cboxol_get_academic_units( array(
						'type' => $academic_unit_type->get_slug(),
					) );
					?>
					<div class="custom-select academic-unit-type-select" id="academic-unit-type-select-<?php echo esc_attr( $academic_unit_type->get_slug() ); ?>">
						<select name="<?php echo esc_attr( $url_param ); ?>" class="last-select <?php echo esc_attr( $color_class ); ?>-text" id="<?php echo esc_attr( $academic_unit_type->get_slug() ); ?>-select" data-unittype="<?php echo esc_attr( $academic_unit_type->get_slug() ); ?>">
							<option class="academic-unit" value="" data-parent="" <?php selected( '', $current_unit ) ?>><?php echo esc_html( $academic_unit_type->get_name() ); ?></option>
							<option class="academic-unit" value="all" data-parent="" <?php selected( 'all', $current_unit ) ?>><?php esc_html_e( 'All', 'openlab-theme' ); ?></option>

							<?php foreach ( $units_of_type as $unit ) : ?>
								<option class="academic-unit academic-unit-nonempty" data-parent="<?php echo esc_html( $unit->get_parent() ); ?>" value='<?php echo esc_attr( $unit->get_slug() ); ?>' <?php selected( $unit->get_slug(), $current_unit ) ?>><?php echo esc_html( $unit->get_name() ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endforeach; ?>

				<?php if ( function_exists( 'bpcgc_get_terms_by_group_type' ) ) :  ?>

						<?php $group_terms = bpcgc_get_terms_by_group_type( $group_type ); ?>

						<?php if ( $group_terms && ! empty( $group_terms ) ) :  ?>

							<div class="custom-select">
								<select name="cat" class="last-select <?php echo $bpcgc_color; ?>-text" id="bp-group-categories-select">
									<option value="" <?php selected( '', $option_value_bpcgc ) ?>>Select Category</option>
									<option value='cat_all' <?php selected( 'cat_all', $option_value_bpcgc ) ?>>All</option>
									<?php foreach ( $group_terms as $term ) : ?>
										<option value="<?php echo $term->slug ?>" <?php selected( $option_value_bpcgc, $term->slug ) ?>><?php echo $term->name ?></option>
									<?php endforeach; ?>
								</select>
							</div>

					<?php endif; ?>

				<?php endif;
				?>

				<?php // @todo figure out a way to make this dynamic ?>
				<?php if ( bp_is_groups_directory() && $group_type_object && $group_type_object->is_course() ) :  ?>
					<div class="custom-select">
						<select name="semester" class="last-select <?php echo $semester_color; ?>-text">
							<option value='' <?php selected( '', $option_value_semester ) ?>>Select Semester</option>
							<option value='semester_all' <?php selected( 'semester_all', $option_value_semester ) ?>>All</option>
							<?php foreach ( openlab_get_active_semesters() as $sem ) : ?>
								<option value="<?php echo esc_attr( $sem['option_value'] ) ?>" <?php selected( $option_value_semester, $sem['option_value'] ) ?>><?php echo esc_attr( $sem['option_label'] ) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>

				<?php if ( ( bp_is_groups_directory() && $group_type_object->is_portfolio() ) || bp_is_members_directory() ) :  ?>
					<div class="custom-select">
						<select name="member_type" class="last-select <?php echo $user_color; ?>-text">
							<option value='' <?php selected( '', $member_type_slug ) ?>><?php esc_html_e( 'Select User Type', 'openlab-theme' ); ?></option>
							<?php foreach ( cboxol_get_member_types() as $member_type ) : ?>
								<option value='<?php echo esc_attr( $member_type->get_slug() ); ?>' <?php selected( $member_type->get_slug(), $member_type_slug ) ?>><?php echo esc_html( $member_type->get_label( 'singular' ) ); ?></option>
							<?php endforeach; ?>
							<option value='all' <?php selected( 'all', $member_type_slug ) ?>>All</option>
						</select>
					</div>
				<?php endif; ?>
				<div class="custom-select">
					<select name="group_sequence" class="last-select <?php echo $sort_color; ?>-text">
						<option <?php selected( $option_value, 'alphabetical' ) ?> value='alphabetical'>Alphabetical</option>
						<option <?php selected( $option_value, 'newest' ) ?>  value='newest'>Newest</option>
						<option <?php selected( $option_value, 'active' ) ?> value='active'>Last Active</option>
					</select>
				</div>

			</div>
			<input class="btn btn-primary" type="submit" onchange="document.forms['group_seq_form'].submit();" value="Submit">
			<input class="btn btn-default" type="button" value="Reset" onClick="window.location.href = '<?php echo $bp->root_domain ?>/<?php echo $group_slug; ?>/'">
		</form>

		<div class="archive-search">
			<h3 class="bold font-size font-14">Search</h3>
			<form method="get" class="form-inline btn-combo" role="form">
				<div class="form-group">
					<input id="search-terms" class="form-control" type="text" name="search" placeholder="Enter keyword" /><label class="sr-only" for="search-terms">Enter keyword</label><button class="btn btn-primary top-align" id="search-submit" type="submit"><i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search</span></button>
				</div>
			</form>
			<div class="clearfloat"></div>
		</div><!--archive search-->
	</div><!--filter-->
</div>
<?php

function slug_maker( $full_string ) {
	$slug_val = str_replace( ' ', '-', $full_string );
	$slug_val = strtolower( $slug_val );
	return $slug_val;
}
