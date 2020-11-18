<?php
/**
 * 404 template
 *
 */

get_header(); ?>

<div id="content" class="hfeed">
	<div <?php post_class(); ?>>
		<div class="post hentry">
			<div class="entry-title">
				<h1><?php esc_html_e( 'Page Not Found', 'commons-in-a-box' ); ?></h1>
			</div>

			<div id="openlab-main-content" class="entry-content">
				<p><?php esc_html_e( 'The page you requested could not be found. Please use the menu above to find the page you need.', 'commons-in-a-box' ); ?></p>
			</div><!-- end .entry-content -->

		</div><!-- end .post -->
	</div>
</div><!--#content-->

<?php get_footer(); ?>
