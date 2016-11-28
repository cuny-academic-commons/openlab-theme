<?php get_header(); ?>

<div id="content" class="hfeed row">
	<?php
	global $wp_query;
	$post = $wp_query->post;
	$postID = $post->ID;
	$parent = $post->post_parent;

	// @todo This should not be hardcoded.
	$about_page_obj = get_page_by_path( 'about' );
	$calendar_page_obj = get_page_by_path( 'about/calendar' );
	$is_about_or_calendar = ( $about_page_obj && ( $postID == $about_page_obj->ID || $parent == $about_page_obj->ID ) ) || ( $calendar_page_obj && $parent == $calendar_page_obj->ID );

	if ( $is_about_or_calendar) {
		openlab_bp_mobile_sidebar( 'about' );
	}
	?>

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<?php //my-<group> pages should not be displaying this ?>
			<?php if ( ! strstr( get_the_title(), 'My' ) ) :  ?>
				<div <?php post_class( 'col-sm-18 col-xs-24' ); ?>>
					<div id="openlab-main-content"  class="content-wrapper">
						<h1 class="entry-title"><span class="profile-name"><?php the_title(); ?></span>
							<?php if ( $is_about_or_calendar ) :  ?>
								<button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
									<span class="sr-only">Toggle navigation</span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								</button>
							<?php endif; ?>
						</h1>
						<div class="entry-content"><?php the_content(); ?></div>
					</div>
				</div><!--hentry-->
			<?php endif; ?>

			<?php
		endwhile;
	endif;
	?>

	<?php
	//add the about-page sidebar to just the about page and any child about page
	if ( $is_about_or_calendar ) {
		openlab_bp_sidebar( 'about' );
	}
	?>

</div><!--#content-->

<?php get_footer(); ?>
