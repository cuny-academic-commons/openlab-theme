<?php
/**
 * Group plugins - includes files
 *
 */
global $bp, $wp_query;

$div_class = sprintf(
	'plugins action-%s component-%s',
	bp_current_action(),
	bp_current_component()
);
if ( function_exists( 'openlab_eo_is_event_detail_screen' ) && openlab_eo_is_event_detail_screen() ) {
	$div_class .= ' event-detail';
}
?>

<div id="single-course-body" class="<?php echo esc_attr( $div_class ); ?>">
	<div class="row submenu-row"><div class="col-md-24">
			<div class="submenu">
				<?php if ( bp_is_current_action( 'invite-anyone' ) || bp_is_current_action( 'notifications' ) ) : ?>

					<ul class="nav nav-inline">
						<?php openlab_group_membership_tabs(); ?>
					</ul>
				<?php elseif ( bp_is_current_action( 'docs' ) ) : ?>

					<ul class="nav nav-inline">
						<?php openlab_docs_tabs(); ?>
					</ul>

				<?php elseif ( bp_is_current_action( BP_GROUP_DOCUMENTS_SLUG ) ) : ?>

					<div class="row">
						<div class="submenu col-sm-17">
							<ul class="nav nav-inline">
								<li class="current-menu-item"><a href=""><?php esc_html_e( 'Files', 'commons-in-a-box' ); ?></a></li>
							</ul>
						</div>
						<div class="group-count col-sm-7 pull-right"><?php echo esc_html( openlab_get_files_count() ); ?></div>
					</div>

				<?php elseif ( bp_is_current_component( 'events' ) || bp_is_current_action( 'events' ) ) : ?>

					<?php //do nothing - event sub nav is handled via template override in buddypress/groups/single/subnav-events.php ?>

				<?php else : ?>
					<ul class="nav nav-inline">
						<?php do_action( 'bp_group_plugin_options_nav' ); ?>
					</ul>
				<?php endif; ?>
			</div>
		</div></div>

	<div id="item-body">

		<?php do_action( 'bp_before_group_plugin_template' ); ?>

		<div class="entry-content">
			<?php do_action( 'bp_template_content' ); ?>
		</div>

		<?php do_action( 'bp_after_group_plugin_template' ); ?>
	</div><!-- #item-body -->
</div>
