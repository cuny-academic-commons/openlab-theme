<?php
/* Template Name: Group Archive */
/* * begin layout* */
get_header();

$type = bp_get_current_group_directory_type();
$type_object = cboxol_get_group_type( $type );

?>

<div id="content" class="hfeed row">
	<?php openlab_bp_sidebar( 'groups', true ); ?>
	<div <?php post_class( 'col-sm-18 col-xs-24' ); ?>>
		<div id="openlab-main-content" class="content-wrapper">
			<h1 class="entry-title"><?php echo esc_html( $type_object->get_label( 'plural' ) ); ?><button data-target="#sidebar" data-backgroundonly="true" class="mobile-toggle direct-toggle pull-right visible-xs" type="button"><span class="fa fa-binoculars"></span></button></h1>

			<div class="entry-content">
				<?php openlab_group_archive(); ?>
			</div><!--entry-content-->
		</div><!--hentry-->
	</div>
</div><!--content-->

<?php
get_footer();
/**end layout**/
