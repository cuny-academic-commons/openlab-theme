<?php

// post type declerations
// custom taxonomy to organize help
add_action( 'init', 'openlab_help_taxonomies', 0 );

function openlab_help_taxonomies() {
	$labels = array(
		'name'              => __( 'Help Categories', 'commons-in-a-box' ),
		'singular_name'     => __( 'Help Category', 'commons-in-a-box' ),
		'search_items'      => __( 'Search Help Categories', 'commons-in-a-box' ),
		'all_items'         => __( 'All Help Categories', 'commons-in-a-box' ),
		'parent_item'       => __( 'Parent Help Category', 'commons-in-a-box' ),
		'parent_item_colon' => __( 'Parent Help Category:', 'commons-in-a-box' ),
		'edit_item'         => __( 'Edit Help Category', 'commons-in-a-box' ),
		'update_item'       => __( 'Update Help Category', 'commons-in-a-box' ),
		'add_new_item'      => __( 'Add New Help Category', 'commons-in-a-box' ),
		'new_item_name'     => __( 'New Help Category Name', 'commons-in-a-box' ),
		'menu_name'         => __( 'Help Category', 'commons-in-a-box' ),
	);

	register_taxonomy(
		'help_category',
		array( 'help' ),
		array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'help/help-category' ),
		)
	);
	$labels_tags = array(
		'name'                       => _x( 'Help Tags', 'taxonomy general name' ),
		'singular_name'              => _x( 'Help Tag', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Help Tags' ),
		'popular_items'              => __( 'Popular Help Tags' ),
		'all_items'                  => __( 'All Help Tags' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Help Tag' ),
		'update_item'                => __( 'Update Help Tag' ),
		'add_new_item'               => __( 'Add New Help Tag' ),
		'new_item_name'              => __( 'New Help Tag Name' ),
		'separate_items_with_commas' => __( 'Separate help tags with commas' ),
		'add_or_remove_items'        => __( 'Add or remove help tags' ),
		'choose_from_most_used'      => __( 'Choose from the most used help tags' ),
		'menu_name'                  => __( 'Help Tags' ),
	);

	register_taxonomy(
		'help_tags',
		'help',
		array(
			'hierarchical'          => false,
			'labels'                => $labels_tags,
			'show_ui'               => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'help-tags' ),
		)
	);
}

// help post type
add_action( 'init', 'openlab_register_help' );

function openlab_register_help() {

	$labels = array(
		'name'               => __( 'Help', 'commons-in-a-box' ),
		'singular_name'      => __( 'Help', 'commons-in-a-box' ),
		'add_new'            => __( 'Add New', 'commons-in-a-box' ),
		'add_new_item'       => __( 'Add New Help', 'commons-in-a-box' ),
		'edit_item'          => __( 'Edit Help', 'commons-in-a-box' ),
		'new_item'           => __( 'New Help', 'commons-in-a-box' ),
		'view_item'          => __( 'View Help', 'commons-in-a-box' ),
		'search_items'       => __( 'Search Help', 'commons-in-a-box' ),
		'not_found'          => __( 'No help found', 'commons-in-a-box' ),
		'not_found_in_trash' => __( 'No help found in Trash', 'commons-in-a-box' ),
		'parent_item_colon'  => __( 'Parent Help:', 'commons-in-a-box' ),
		'menu_name'          => __( 'Help', 'commons-in-a-box' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => true,
		'description'         => __( 'Help Pages', 'commons-in-a-box' ),
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'revisions', 'page-attributes' ),
		'taxonomies'          => array( '' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 20,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => false,
		'query_var'           => true,
		'menu_icon'           => 'dashicons-editor-help',
		'can_export'          => true,
		'capability_type'     => 'post',
	);

	register_post_type( 'help', $args );
}

// add some information to the Help overview page
add_filter( 'manage_edit-help_columns', 'help_edit_columns' );
add_action( 'manage_help_posts_custom_column', 'help_custom_columns' );
add_filter( 'manage_edit-help_sortable_columns', 'help_column_register_sortable' );

function help_edit_columns( $columns ) {
	$columns = array(
		'cb'              => '<input type="checkbox" />',
		'title'           => __( 'Title', 'commons-in-a-box' ),
		'author'          => __( 'Author', 'commons-in-a-box' ),
		'help_categories' => __( 'Help Categories', 'commons-in-a-box' ),
		'help_tags'       => __( 'Help Tags', 'commons-in-a-box' ),
		'menu_order'      => __( 'Menu Order', 'commons-in-a-box' ),
		'date'            => __( 'Date', 'commons-in-a-box' ),
	);

	return $columns;
}

function help_custom_columns( $column ) {
	global $post;

	switch ( $column ) {
		case 'help_categories':
			if ( get_the_term_list( $post->ID, 'help_category' ) ) {
				echo get_the_term_list( $post->ID, 'help_category', '', ', ', '' );
			} else {
				echo 'None';
			}
			break;
		case 'help_tags':
			if ( get_the_term_list( $post->ID, 'help_tags' ) ) {
				echo get_the_term_list( $post->ID, 'help_tags', '', ', ', '' );
			} else {
				echo 'None';
			}
			break;
		case 'menu_order':
			$order = $post->menu_order;
			echo esc_html( $order );
			break;
	}
}

function help_column_register_sortable( $columns ) {
	$columns['menu_order'] = 'menu_order';
	return $columns;
}

// custom post type - help glossary
add_action( 'init', 'openlab_register_help_glossary' );

function openlab_register_help_glossary() {

	$labels = array(
		'name'               => __( 'Help Glossary', 'commons-in-a-box' ),
		'singular_name'      => __( 'Help Glossary', 'commons-in-a-box' ),
		'add_new'            => __( 'Add New', 'commons-in-a-box' ),
		'add_new_item'       => __( 'Add New Help Glossary', 'commons-in-a-box' ),
		'edit_item'          => __( 'Edit Help Glossary', 'commons-in-a-box' ),
		'new_item'           => __( 'New Help Glossary', 'commons-in-a-box' ),
		'view_item'          => __( 'View Help Glossary', 'commons-in-a-box' ),
		'search_items'       => __( 'Search Help Glossary', 'commons-in-a-box' ),
		'not_found'          => __( 'No help glossary found', 'commons-in-a-box' ),
		'not_found_in_trash' => __( 'No help glossary found in Trash', 'commons-in-a-box' ),
		'parent_item_colon'  => __( 'Parent Help Glossary:', 'commons-in-a-box' ),
		'menu_name'          => __( 'Help Glossary', 'commons-in-a-box' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => true,
		'description'         => __( 'Help Glossary Pages', 'commons-in-a-box' ),
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'revisions', 'page-attributes' ),
		'taxonomies'          => array( '' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 20,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => true,
		'menu_icon'           => 'dashicons-editor-help',
		'can_export'          => true,
		'capability_type'     => 'post',
	);

	register_post_type( 'help_glossary', $args );
}

// add some information to the Glossary overview page
add_filter( 'manage_edit-help_glossary_columns', 'help_glossary_edit_columns' );
add_action( 'manage_help_glossary_posts_custom_column', 'help_glossary_custom_columns' );
add_filter( 'manage_edit-help_glossary_sortable_columns', 'help_glossary_column_register_sortable' );

function help_glossary_edit_columns( $columns ) {
	$columns = array(
		'cb'         => '<input type="checkbox" />',
		'title'      => __( 'Title', 'commons-in-a-box' ),
		'author'     => __( 'Author', 'commons-in-a-box' ),
		'menu_order' => __( 'Menu Order', 'commons-in-a-box' ),
		'date'       => __( 'Date', 'commons-in-a-box' ),
	);

	return $columns;
}

function help_glossary_custom_columns( $column ) {
	global $post;

	switch ( $column ) {
		case 'menu_order':
			$order = $post->menu_order;
			echo esc_html( $order );
			break;
	}
}

function help_glossary_column_register_sortable( $columns ) {
	$columns['menu_order'] = 'menu_order';
	return $columns;
}

// adding slider post type
function register_cpt_slider() {
	$labels = array(
		'name'               => __( 'Sliders', 'commons-in-a-box' ),
		'singular_name'      => __( 'Slider', 'commons-in-a-box' ),
		'add_new'            => __( 'Add New', 'commons-in-a-box' ),
		'add_new_item'       => __( 'Add New Slider', 'commons-in-a-box' ),
		'edit_item'          => __( 'Edit Slider', 'commons-in-a-box' ),
		'new_item'           => __( 'New Slider', 'commons-in-a-box' ),
		'view_item'          => __( 'View Slider', 'commons-in-a-box' ),
		'search_items'       => __( 'Search Sliders', 'commons-in-a-box' ),
		'not_found'          => __( 'No sliders found', 'commons-in-a-box' ),
		'not_found_in_trash' => __( 'No sliders found in Trash', 'commons-in-a-box' ),
		'parent_item_colon'  => __( 'Parent Slider:', 'commons-in-a-box' ),
		'menu_name'          => __( 'Sliders', 'commons-in-a-box' ),
	);
	$args   = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-images-alt2',
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => false,
		'capability_type'     => 'post',
	);
	register_post_type( 'slider', $args );
}

add_action( 'init', 'register_cpt_slider' );
