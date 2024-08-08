<?php

if ( ! defined( 'OLBADGES_VERSION' ) ) {
	return;
}

$badge_query_args = [
	'hide_empty' => false,
];

$group_type = bp_get_current_group_directory_type();
if ( $group_type ) {
	$badge_query_args['group_type'] = $group_type;
}

$badges = \OpenLab\Badges\Badge::get( $badge_query_args );

// Exclude 'Open' and 'Cloneable', which have their own UI.
$badges = array_filter(
	$badges,
	function ( $badge ) {
		$is_open_badge      = get_term_meta( $badge->get_id(), 'cboxol_is_open_badge' );
		$is_cloneable_badge = get_term_meta( $badge->get_id(), 'cboxol_is_cloneable_badge' );

		return ! $is_open_badge && ! $is_cloneable_badge;
	}
);

if ( ! $badges ) {
	return;
}

$current_badges = openlab_get_current_filter( 'badges' );

?>

<div class="sidebar-filter sidebar-filter-badges">
	<div class="form-group">
		<?php foreach ( $badges as $badge ) : ?>
			<div class="sidebar-filter-checkbox">
				<input type="checkbox" name="badges[]" id="checkbox-badge-<?php echo esc_attr( $badge->get_id() ); ?>" <?php checked( in_array( $badge->get_id(), $current_badges, true ) ); ?> value="<?php echo esc_attr( $badge->get_id() ); ?>" /> <label for="checkbox-badge-<?php echo esc_attr( $badge->get_id() ); ?>"><?php echo esc_html( $badge->get_name() ); ?></label>
			</div>
		<?php endforeach; ?>
	</div>
</div>

