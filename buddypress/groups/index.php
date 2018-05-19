<?php
/* Template Name: My Group Template */
get_header();
global $bp;
?>

<div id="content" class="hfeed row">

	<?php
	openlab_bp_mobile_sidebar( 'members' );
	$account_type = xprofile_get_field_data( 'Account Type', $bp->loggedin_user->id );
	?>

	<div class="col-sm-18 col-xs-24 my-groups-grid">
		<div class="entry-title">
			<h1 class="mol-title">
				<span class="profile-name"><?php echo esc_html( sprintf( __( '%s&rsquo;s Profile', 'openlab-theme' ), $bp->loggedin_user->fullname ) ); ?></span>
				<span class="profile-type pull-right hidden-xs"><?php echo $account_type ?></span>
				<button data-target="#sidebar-mobile" class="mobile-toggle direct-toggle pull-right visible-xs" type="button">
					<span class="sr-only"><?php esc_html_e( 'Toggle navigation', 'openlab-theme' ); ?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</h1>
		</div>

		<div class="clearfix visible-xs">
			<span class="profile-type pull-left"><?php echo $account_type ?></span>
		</div>
		<?php bp_get_template_part( 'groups/groups', 'loop' ); ?>
	</div>

	<?php openlab_bp_sidebar( 'members' ); ?>
</div><!--content-->

<?php
get_footer();
