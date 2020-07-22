<?php
/*
 * Template Name: Search Results
 */
?>

<?php get_header(); ?>

<div id="content" class="hfeed row">
	<?php get_template_part( 'parts/sidebar/groups' ); ?>
	<div <?php post_class( 'col-sm-18 col-xs-24' ); ?>>
		<div id="openlab-main-content" class="content-wrapper openlab-search-results">
			<div class="entry-title">
				<h1><?php esc_html_e( 'Search Results', 'commons-in-a-box' ); ?></h1>

				<div class="directory-title-meta">
					<button data-target="#sidebar" data-backgroundonly="true" class="mobile-toggle direct-toggle pull-right visible-xs" type="button"></h1><span class="sr-only"><?php esc_html_e( 'Search', 'commons-in-a-box' ); ?></span><span class="fa fa-binoculars"></span></button>
				</div>
			</div>

			<div class="entry-content">
				<?php bp_get_template_part( 'groups/groups-loop' ); ?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
