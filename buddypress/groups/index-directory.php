<?php
get_header();

$type = bp_get_current_group_directory_type();
$type_object = cboxol_get_group_type( $type );

$can_create = is_user_logged_in() && bp_user_can_create_groups();
if ( $type_object->get_is_course() ) {
	$can_create = cboxol_user_can_create_courses( bp_loggedin_user_id() );
} elseif ( $type_object->get_is_portfolio() ) {
	$can_create = ! openlab_user_has_portfolio( bp_loggedin_user_id() );
}

$create_text = $type_object->get_can_be_cloned() ? __( 'Create / Clone', 'openlab-theme' ) : __( 'Create New', 'openlab-theme' );
$create_link = bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create/step/group-details/?group_type=' . $type_object->get_slug() . '&new=true';
?>

<div id="content" class="hfeed row">
	<?php openlab_bp_sidebar( 'groups', true ); ?>
	<div <?php post_class( 'col-sm-18 col-xs-24' ); ?>>
		<div id="openlab-main-content" class="content-wrapper">
			<div class="entry-title">
				<h1><?php echo esc_html( $type_object->get_label( 'plural' ) ); ?></h1>

				<div class="directory-title-meta">
					<?php if ( $can_create ) : ?>
						<span aria-hidden="true" class="fa fa-plus-circle hidden-xs"></span>
						<a class="hidden-xs" href="<?php echo esc_attr( $create_link ); ?>"><?php echo esc_html( $create_text ); ?></a>
					<?php endif; ?>
					<button data-target="#sidebar" data-backgroundonly="true" class="mobile-toggle direct-toggle pull-right visible-xs" type="button"></h1><span class="sr-only"><?php esc_html_e( 'Search', 'openlab-theme' ); ?></span><span class="fa fa-binoculars"></span></button>
				</div>
			</div>

			<div class="entry-content">
				<?php bp_get_template_part( 'groups/groups-loop' ); ?>
			</div><!--entry-content-->
		</div><!--hentry-->
	</div>
</div><!--content-->

<?php
get_footer();
