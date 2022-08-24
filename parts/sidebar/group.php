<?php
$group_id = bp_get_current_group_id();
$site_id  = openlab_get_site_id_by_group_id( $group_id );

if ( $site_id ) {
	$site_url = get_blog_option( $site_id, 'siteurl' );
} else {
	$site_url = openlab_get_external_site_url_by_group_id( $group_id );
}

$show_site = ! empty( $site_url );
if ( $site_id ) {
	$show_site = cboxol_site_can_be_viewed( $group_id );
}
?>

<div id="sidebar" class="sidebar col-sm-6 col-xs-24 type-actions mobile-enabled" role="complementary">
	<div class="sidebar-wrapper">
		<?php
		if ( bp_has_groups() ) :
			while ( bp_groups() ) :
				bp_the_group();
				?>
			<div class="sidebar-widget sidebar-widget-wrapper" id="portfolio-sidebar-widget">
				<h2 class="sidebar-header group-single top-sidebar-header">&nbsp;</h2>

				<?php if ( $show_site ) : ?>
					<div class="wrapper-block group-sidebar-subsection">
						<?php openlab_bp_group_site_pages(); ?>
					</div>
				<?php endif; ?>

				<div id="sidebar-menu-wrapper" class="group-sidebar-subsection sidebar-menu-wrapper wrapper-block">
					<div id="item-buttons" class="profile-nav sidebar-block clearfix">
						<ul class="sidebar-nav clearfix">
							<?php bp_get_options_nav(); ?>
							<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php echo openlab_get_group_profile_mobile_anchor_links(); ?>
						</ul>
					</div><!-- #item-buttons -->
				</div>
				<?php do_action( 'bp_group_options_nav' ); ?>

				<?php if ( ! cboxol_is_portfolio() ) : ?>
					<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo openlab_get_group_activity_events_feed(); ?>
				<?php endif; ?>
			</div>
					<?php
		endwhile;
endif;
		?>
	</div>
</div>
