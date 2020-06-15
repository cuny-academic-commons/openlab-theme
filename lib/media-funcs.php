<?php

/*
 * Media-oriented functionality
 */

function openlab_get_home_slider() {
	$slider_mup    = '';
	$slider_sr_mup = '';

	$slider_args = array(
		'post_type'      => 'slider',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	);

	$slider_query = new WP_Query( $slider_args );

	if ( $slider_query->have_posts() ) {
		$slider_mup    = '<div class="camera_wrap clearfix" tabindex="-1" aria-hidden="true">';
		$slider_sr_mup = '<div class="camera_wrap_sr" role="widget"><h2 class="sr-only">Slideshow Content</h2><ul class="list-unstyled">';
		while ( $slider_query->have_posts() ) :
			$slider_query->the_post();
			// if the featured image is not set, slider will not be added
			if ( get_post_thumbnail_id() ) {

				$img_obj = wp_get_attachment_image_src( get_post_thumbnail_id(), 'front-page-slider' );

				$slider_mup    .= '<div data-alt="' . get_the_title() . '" data-src="' . $img_obj[0] . '"><div class="fadeIn camera_content"><h2 class="regular">' . get_the_title() . '</h2>' . get_the_content_with_formatting() . '</div></div>';
				$slider_sr_mup .= '<li class="sr-only sr-only-focusable camera_content" tabindex="0"><h2 class="regular">' . get_the_title() . '</h2>' . get_the_content_with_formatting() . '</li>';
			}
		endwhile;
		$slider_mup    .= '</div>';
		$slider_sr_mup .= '</ul></div>';
	} else {
		$slider_mup .= '<div class="slider-empty">' . esc_html__( 'You haven\'t added any slides yet!', 'commons-in-a-box' ) . '</div>';
	}

	wp_reset_postdata();

	return $slider_mup . $slider_sr_mup;
}

/**
 * Set avatar dimensions.
 */
add_filter(
	'bp_core_avatar_full_width',
	function() {
		return 225;
	}
);
add_filter(
	'bp_core_avatar_full_height',
	function() {
		return 225;
	}
);

/**
 * WordPress adds dimensions to embedded images; this is totally not responsive WordPress
 *
 * @param type $html
 * @return type
 */
function openlab_remove_thumbnail_dimensions( $html ) {

	$html = preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );

	return $html;
}

add_filter( 'post_thumbnail_html', 'openlab_remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', 'openlab_remove_thumbnail_dimensions', 10 );
add_filter( 'the_content', 'openlab_remove_thumbnail_dimensions', 10 );

function openlab_activity_user_avatar() {
	global $activities_template;
	$current_activity_item = isset( $activities_template->activity->current_comment ) ? $activities_template->activity->current_comment : $activities_template->activity;
	$item_id               = ! empty( $user_id ) ? $user_id : $current_activity_item->user_id;
	$item_id               = apply_filters( 'bp_get_activity_avatar_item_id', $item_id );

	return '<img class="img-responsive" src ="' . bp_core_fetch_avatar(
		array(
			'item_id' => $item_id,
			'object'  => 'user',
			'type'    => 'full',
			'html'    => false,
		)
	) . '" alt="' . bp_get_displayed_user_fullname() . '"/>';
}

function openlab_activity_group_avatar( $current_activity_item = null ) {
	global $activities_template;

	if ( null === $current_activity_item ) {
		$current_activity_item = isset( $activities_template->activity->current_comment ) ? $activities_template->activity->current_comment : $activities_template->activity;
	}

	$item_id = $current_activity_item->item_id;

	$group = groups_get_group( array( 'group_id' => $item_id ) );

	return '<img class="img-responsive" src ="' . bp_core_fetch_avatar(
		array(
			'item_id' => $item_id,
			'object'  => 'group',
			'type'    => 'full',
			'html'    => false,
		)
	) . '" alt="' . $group->name . '"/>';
}

function openlab_activity_group_link( $current_activity_item = null ) {
	global $bp, $activities_template;

	if ( null === $current_activity_item ) {
		$current_activity_item = isset( $activities_template->activity->current_comment ) ? $activities_template->activity->current_comment : $activities_template->activity;
	}

	$item_id = $current_activity_item->item_id;

	$group = groups_get_group( array( 'group_id' => $item_id ) );

	return get_site_url( 0, $bp->groups->slug . '/' . $group->slug );
}

/**
 * Get the list of activity types that should appear in the What's Happening feed.
 *
 * @return array
 */
function openlab_whats_happening_activity_types() {
	return array( 'created_group', 'added_group_document', 'bbp_reply_create', 'bbp_topic_create', 'bpeo_create_event', 'bpeo_edit_event', 'bp_doc_comment', 'bp_doc_created', 'bp_doc_edited', 'deleted_group_document', 'joined_group', 'new_blog', 'new_blog_comment', 'new_blog_post', 'new_forum_post', 'new_forum_topic', 'group_details_updated' );
}
/**
 * Get activity items for the What's Happening feed.
 *
 * @return array
 */
function openlab_whats_happening_activity_items() {
	$cached = wp_cache_get( 'whats_happening_items', 'openlab' );
	if ( ! $cached ) {
		$now           = new DateTime();
		$activity_args = array(
			'per_page'          => 10,
			'filter'            => array(
				'object' => 'groups',
				'action' => openlab_whats_happening_activity_types(),
			),
			'update_meta_cache' => false, // we'll be hitting this alot
			'date_query'        => array(
				'before' => $now->format( 'Y-m-d H:i:s' ),
			),
		);

		$a      = bp_activity_get( $activity_args );
		$cached = $a['activities'];
		wp_cache_set( 'whats_happening_items', $cached, 'openlab' );
	}

	return $cached;
}

/**
 * Invalidate whats_happening cache when a new activity item is posted.
 */
function openlab_invalidate_whats_happening_cache( $args ) {
	if ( in_array( $args['type'], openlab_whats_happening_activity_types(), true ) ) {
		wp_cache_delete( 'whats_happening_items', 'openlab' );
	}
}
add_action( 'bp_activity_add', 'openlab_invalidate_whats_happening_cache' );
