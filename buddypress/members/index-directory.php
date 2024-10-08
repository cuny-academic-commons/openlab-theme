<?php
/* Template Name: People Archive */
get_header();
?>

<div id="content" class="hfeed row">
	<?php get_template_part( 'parts/sidebar/groups' ); ?>
	<div <?php post_class( 'col-sm-18 col-xs-24' ); ?> role="main">
		<div id="openlab-main-content" class="content-wrapper">
			<div class="entry-title">
				<h1><?php echo esc_html( bp_get_directory_title( 'members' ) ); ?></h1>

				<div class="directory-title-meta">
					<button data-target="#sidebar" class="mobile-toggle direct-toggle pull-right visible-xs" type="button"><span class="fa fa-binoculars"></span><span class="sr-only"><?php esc_html_e( 'Search', 'commons-in-a-box' ); ?></span></button>
				</div>
			</div>

			<div class="entry-content">
				<div id="people-listing">
					<?php openlab_list_members( 'more' ); ?>
				</div><!--people-listing-->
			</div><!--entry-content-->
		</div>
	</div><!--hentry-->
</div><!--content-->

<?php
get_footer();
