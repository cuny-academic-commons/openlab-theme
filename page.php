<?php get_header(); ?>

<div id="content" class="hfeed row">
	<?php
	global $wp_query;
	$post = $wp_query->post;
	$postID = $post->ID;
	$parent = $post->post_parent;

	$show_about_sidebar = cboxol_is_brand_page( 'about' ) || cboxol_is_brand_page( 'terms-of-use' ) || cboxol_is_brand_page( 'contact-us' );

	if ( $show_about_sidebar ) {
		openlab_bp_mobile_sidebar( 'about' );
	}
	?>

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<?php //my-<group> pages should not be displaying this ?>
			<?php if ( ! strstr( get_the_title(), 'My' ) ) :  ?>
				<div <?php post_class( 'col-sm-18 col-xs-24' ); ?>>
					<div id="openlab-main-content"  class="content-wrapper">
						<div class="entry-title">
							<h1><span class="profile-name"><?php the_title(); ?></span></h1>

							<?php if ( $show_about_sidebar ) :  ?>
								<div class="directory-title-meta">
									<button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
										<span class="sr-only"><?php esc_html_e( 'Toggle navigation', 'openlab-theme' ); ?></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</button>
								</div>
							<?php endif; ?>
						</div>
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
	if ( $show_about_sidebar ) {
		openlab_bp_sidebar( 'about' );
	}
	?>

</div><!--#content-->

<?php get_footer(); ?>
