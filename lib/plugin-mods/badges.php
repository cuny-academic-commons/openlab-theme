<?php

/**
 * Modifications to default behavior of OpenLab Badges.
 *
 * @since 1.3.0
 */

/**
 * Outputs the badge markup for the group directory.
 *
 * @since 1.2.0
 */
function openlab_group_directory_badges() {
	if ( ! defined( 'OLBADGES_VERSION' ) ) {
		return;
	}

	echo '<div class="col-xs-18 alignright group-directory-badges">';
	\OpenLab\Badges\Template::badge_links( 'directory' );
	echo '</div>';
}
add_action( 'openlab_theme_after_group_group_directory', 'openlab_group_directory_badges' );

/**
 * Outputs the badge markup for single group pages.
 *
 * @since 1.2.0
 */
function openlab_group_single_badges() {
	if ( ! defined( 'OLBADGES_VERSION' ) ) {
		return;
	}

	echo '<div class="group-single-badges">';
	\OpenLab\Badges\Template::badge_links( 'single' );
	echo '</div>';
}

/**
 * Checks whether a group has badges.
 *
 * @since 1.2.0
 *
 * @param int $group_id Group ID.
 * @return bool
 */
function openlab_group_has_badges( $group_id ) {
	if ( ! defined( 'OLBADGES_VERSION' ) ) {
		return false;
	}

	$badge_group  = new \OpenLab\Badges\Group( $group_id );
	$group_badges = $badge_group->get_badges();

	return ! empty( $group_badges );
}

/**
 * Gets the Cloneable badge.
 *
 * @since 1.3.0
 *
 * @return OpenLab\Badges\Badge|null Returns null on failure.
 */
function openlab_get_cloneable_badge() {
	static $term_id;

	if ( ! $term_id ) {
		$term_ids = get_terms(
			[
				'taxonomy'   => 'openlab_badge',
				'fields'     => 'ids',
				'hide_empty' => false,
				'meta_query' => [
					[
						'key' => 'cboxol_is_cloneable_badge',
					],
				],
			]
		);

		if ( $term_ids ) {
			$term_id = reset( $term_ids );
		}
	}

	if ( ! $term_id ) {
		return null;
	}

	return new \OpenLab\Badges\Badge( $term_id );
}

/**
 * Gets the Open badge.
 *
 * @since 1.3.0
 *
 * @return OpenLab\Badges\Badge|null Returns null on failure.
 */
function openlab_get_open_badge() {
	static $term_id;

	if ( ! $term_id ) {
		$term_ids = get_terms(
			[
				'taxonomy'   => 'openlab_badge',
				'fields'     => 'ids',
				'hide_empty' => false,
				'meta_query' => [
					[
						'key' => 'cboxol_is_open_badge',
					],
				],
			]
		);

		if ( $term_ids ) {
			$term_id = reset( $term_ids );
		}
	}

	if ( ! $term_id ) {
		return null;
	}

	return new \OpenLab\Badges\Badge( $term_id );
}

/**
 * Filters the badges belonging to a group, to dynamically inject the Open and Cloneable badges.
 *
 * @since 1.3.0
 *
 * @param array                $badges Badges.
 * @param OpenLab\Badges\Group $group  Badge-group object.
 * @return array
 */
function openlab_filter_group_badges( $badges, OpenLab\Badges\Group $group ) {
	if ( openlab_group_is_open( $group->get_group_id() ) ) {
		$open_badge = openlab_get_open_badge();
		if ( $open_badge ) {
			$badges[] = $open_badge;
		}
	}

	if ( openlab_group_can_be_cloned( $group->get_group_id() ) ) {
		$cloneable_badge = openlab_get_cloneable_badge();
		if ( $cloneable_badge ) {
			$badges[] = $cloneable_badge;
		}
	}

	usort(
		$badges,
		function( $a, $b ) {
			return $a->get_position() > $b->get_position();
		}
	);

	return $badges;
}
add_filter( 'openlab_badges_badges_of_group', 'openlab_filter_group_badges', 10, 2 );
