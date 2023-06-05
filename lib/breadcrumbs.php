<?php

/**
 * Genesis Breadcrumbs class and related functions. Adapted for OpenLab.
 * @to-do simplify this
 *
 * @author Gary Jones
 *
 * @package Genesis
 */
/**
 * Openlab specific functionality
 *
 */
add_action( 'bp_before_footer', 'openlab_do_breadcrumbs', 5 );

function custom_breadcrumb_args( $args ) {
	$args['labels']['prefix'] = '<div class="breadcrumb-inline prefix-label"><div class="breadcrumb-prefix-label">' . esc_html__( 'You are here', 'commons-in-a-box' ) . '</div><i class="fa fa-caret-right"></i></div><div class="breadcrumb-inline breadcrumbs">';
	$args['prefix']           = '<div id="breadcrumb-container"><div class="breadcrumb-col semibold uppercase"><div class="breadcrumb-wrapper">';
	$args['suffix']           = '</div></div></div></div>';
	return $args;
}
add_filter( 'openlab_breadcrumb_args', 'custom_breadcrumb_args' );

/**
 * For the help page breadcrumb
 */
function openlab_specific_blog_breadcrumb( $crumb ) {
	global $post;

	if ( 'help' === $post->post_type ) {
		// @todo This will not work to build a path.
		$crumb = '<a title="' . esc_attr__( 'View all Help', 'commons-in-a-box' ) . '" href="' . site_url( 'help/openlab-help' ) . '">' . esc_html__( 'Help', 'commons-in-a-box' ) . '</a>';

		$post_terms = get_the_terms( $post->ID, 'help_category' );
		$term       = array();
		if ( is_array( $post_terms ) ) {
			foreach ( $post_terms as $post_term ) {
				$term[] = $post_term;
			}
		}

		$term_link = '';
		if ( ! empty( $term ) ) {
			$current_term = get_term_by( 'id', $term[0]->term_id, 'help_category' );
			$term_link    = get_term_link( $current_term, 'help_category' );
		}

		if ( $term_link && ! is_wp_error( $term_link ) ) {
			$crumb .= ' <span class="breadcrumb-sep">/</span> <a href="' . esc_url( $term_link ) . '">' . esc_html( $current_term->name ) . '</a>';
		}

		$crumb .= ' <span class="breadcrumb-sep">/</span> ' . bp_create_excerpt(
			$post->post_title,
			50,
			array(
				'ending' => __( '&hellip;', 'commons-in-a-box' ),
			)
		);
	}

	return $crumb;
}
add_filter( 'openlab_single_crumb', 'openlab_specific_blog_breadcrumb', 10, 2 );

function openlab_specific_archive_breadcrumb( $crumb ) {
	global $bp, $bp_current;

	$tax = get_query_var( 'taxonomy' );
	if ( 'help_category' === $tax ) {
		$crumb = '<a title="View all Help" href="' . esc_attr( site_url( 'help/openlab-help' ) ) . '">' . esc_html__( 'Help', 'commons-in-a-box' ) . '</a>';

		$get_term = get_query_var( 'term' );
		$term     = get_term_by( 'slug', $get_term, 'help_category' );
		if ( 0 !== $term->parent ) {
			$parent_term = get_term_by( 'id', $term->parent, 'help_category' );
			$parent_link = get_term_link( $parent_term, 'help_category' );
			if ( ! is_wp_error( $parent_link ) ) {
				$crumb .= ' <span class="breadcrumb-sep">/</span> <a href="' . esc_attr( $parent_link ) . '">' . esc_html( $parent_term->name ) . '</a>';
			}
		}
		$crumb .= ' <span class="breadcrumb-sep">/</span> ' . $term->name;
	}

	if ( bp_is_group() ) {
		$group_id = $bp->groups->current_group->id;
		$b2       = $bp->groups->current_group->name;

		$group_type = cboxol_get_group_group_type( $group_id );

		if ( $group_type && ! is_wp_error( $group_type ) ) {
			$b1 = sprintf(
				'<a href="%s">%s</a>',
				esc_attr( bp_get_group_type_directory_permalink( $group_type->get_slug() ) ),
				esc_html( $group_type->get_label( 'plural' ) )
			);
		}
	}

	if ( bp_is_user() ) {
		$member_type = cboxol_get_user_member_type( bp_displayed_user_id() );

		$b1 = sprintf(
			'<a href="%s">%s</a>',
			esc_attr( bp_get_members_directory_permalink() ),
			esc_html__( 'People', 'commons-in-a-box' )
		);

		if ( $member_type && ! is_wp_error( $member_type ) ) {
			$b1 .= sprintf(
				'<a href="%s">%s</a>',
				esc_attr( bp_get_member_type_directory_permalink( $member_type->get_slug() ) ),
				esc_html( $member_type->get_label( 'plural' ) )
			);
		}

		$last_name = xprofile_get_field_data( 'Last Name', $bp->displayed_user->id );
		$b2        = ucfirst( $bp->displayed_user->fullname );
	}

	if ( bp_is_group() || bp_is_user() ) {
		$crumb = $b1 . ' <span class="breadcrumb-sep">/</span> ' . esc_html( $b2 );
	}

	return $crumb;
}
add_filter( 'openlab_archive_crumb', 'openlab_specific_archive_breadcrumb' );

require_once __DIR__ . '/class-openlab-breadcrumb.php';

/**
 * Helper function for the Genesis Breadcrumb Class
 *
 * @since 0.1.6
 */
function openlab_breadcrumb( $args = array() ) {

	global $_openlab_breadcrumb;

	if ( ! $_openlab_breadcrumb ) {
		$_openlab_breadcrumb = new Openlab_Breadcrumb();
	}

	$_openlab_breadcrumb->output( $args );
}

/**
 * Display Breadcrumbs above the Loop
 * Concedes priority to popular breadcrumb plugins
 *
 * @since 0.1.6
 */
function openlab_do_breadcrumbs() {
	if ( function_exists( 'bcn_display' ) ) {
		echo '<div class="breadcrumb">';
		bcn_display();
		echo '</div>';
	} elseif ( function_exists( 'yoast_breadcrumb' ) ) {
		yoast_breadcrumb( '<div class="breadcrumb">', '</div>' );
	} elseif ( function_exists( 'breadcrumbs' ) ) {
		breadcrumbs();
	} elseif ( function_exists( 'crumbs' ) ) {
		crumbs();
	} else {
		openlab_breadcrumb();
	}
}
