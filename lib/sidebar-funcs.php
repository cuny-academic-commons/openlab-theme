<?php

/**
 * Sidebar based functionality.
 *
 * @deprecated 1.2.0 Moved to template files. See /parts/sidebar and /parts/source/sidebar.
 */
function openlab_bp_sidebar( $type, $mobile_dropdown = false, $extra_classes = '' ) {

	$pull_classes  = 'groups' === $type ? ' pull-right' : '';
	$pull_classes .= $mobile_dropdown ? ' mobile-dropdown' : '';

	echo '<div id="sidebar" class="sidebar col-sm-6 col-xs-24' . esc_attr( $pull_classes ) . ' type-' . esc_attr( $type ) . esc_attr( $extra_classes ) . '"><div class="sidebar-wrapper">';

	switch ( $type ) {
		case 'actions':
			openlab_group_sidebar();
			break;
		case 'members':
			bp_get_template_part( 'members/single/sidebar' );
			break;
		case 'groups':
			get_sidebar( 'group-archive' );
			break;
		case 'about':
			$args = array(
				'theme_location' => 'aboutmenu',
				'container'      => 'div',
				'container_id'   => 'about-menu',
				'menu_class'     => 'sidebar-nav clearfix',
			);
			echo '<h2 class="sidebar-title hidden-xs">About</h2>';
			echo '<div class="sidebar-block hidden-xs">';
			wp_nav_menu( $args );
			echo '</div>';
			break;
		case 'help':
			get_sidebar( 'help' );
			break;
		default:
			get_sidebar();
	}

	echo '</div></div>';
}

/**
 * Wrapper for cboxol_get_current_filter().
 */
function openlab_get_current_filter( $param ) {
	return cboxol_get_current_filter( $param );
}

/**
 * Mobile sidebar - for when a piece of the sidebar needs to appear above the content in the mobile space
 *
 * @deprecated 1.2.0 Moved to template files. See /parts/sidebar and /parts/source/sidebar.
 *
 * @param type $type
 */
function openlab_bp_mobile_sidebar( $type ) {

	switch ( $type ) {
		case 'members':
			echo '<div id="sidebar-mobile" class="sidebar group-single-item mobile-dropdown clearfix">';
			openlab_member_sidebar_menu( true );
			echo '</div>';
			break;
		case 'about':
			echo '<div id="sidebar-mobile" class="sidebar clearfix mobile-dropdown">';
			$args = array(
				'theme_location' => 'aboutmenu',
				'container'      => 'div',
				'container_id'   => 'about-mobile-menu',
				'menu_class'     => 'sidebar-nav clearfix',
			);
			echo '<div class="sidebar-block">';
			wp_nav_menu( $args );
			echo '</div>';
			echo '</div>';
			break;
	}
}

/**
 * Output the sidebar content for a single group
 *
 * @deprecated 1.2.0 Moved to template files. See /parts/sidebar/group and /parts/source/sidebar/group.
 */
function openlab_group_sidebar( $mobile = false ) {
	$group_id = bp_get_current_group_id();

	$site_id = openlab_get_site_id_by_group_id( $group_id );
	if ( $site_id ) {
		$site_url = get_blog_option( $site_id, 'siteurl' );
	} else {
		$site_url = openlab_get_external_site_url_by_group_id( $group_id );
	}

	$show_site = ! empty( $site_url );
	if ( $site_id ) {
		$show_site = cboxol_site_can_be_viewed( $group_id );
	}

	if ( bp_has_groups() ) :
		while ( bp_groups() ) :
			bp_the_group(); ?>
		<div class="sidebar-widget sidebar-widget-wrapper" id="portfolio-sidebar-widget">
			<h2 class="sidebar-header group-single top-sidebar-header">&nbsp;</h2>

			<?php if ( $show_site ) : ?>
				<div class="wrapper-block group-sidebar-subsection">
					<?php openlab_bp_group_site_pages( $mobile ); ?>
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
}

/**
 * Member pages sidebar - modularized for easier parsing of mobile menus
 *
 * @param type $mobile
 */
function openlab_member_sidebar_menu( $mobile = false ) {
	$user_id = bp_displayed_user_id();
	if ( ! $user_id ) {
		$user_id = bp_loggedin_user_id();
	}

	if ( $mobile ) {
		$classes = 'visible-xs';
	} else {
		$classes = 'hidden-xs';
	}

	$group_types = cboxol_get_group_types(
		array(
			'exclude_portfolio' => true,
		)
	);

	$portfolio_group_type = cboxol_get_portfolio_group_type();

	$current_group_type = null;
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! empty( $_GET['group_type'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_group_type = wp_unslash( urldecode( $_GET['group_type'] ) );
	}

	if ( is_user_logged_in() && openlab_is_my_profile() ) :
		?>

		<div id="item-buttons<?php echo ( $mobile ? '-mobile' : '' ); ?>" class="mol-menu sidebar-block <?php echo esc_attr( $classes ); ?>">

			<ul class="sidebar-nav clearfix">

				<?php $selected_page = bp_is_user_activity() ? 'selected-page' : ''; ?>
				<li class="sq-bullet <?php echo esc_attr( $selected_page ); ?> mol-profile my-profile"><a href="<?php echo esc_attr( bp_members_get_user_url( $user_id ) ); ?>"><?php esc_html_e( 'My Profile', 'commons-in-box' ); ?></a></li>

				<?php $selected_page = bp_is_user_settings() ? 'selected-page' : ''; ?>
				<li class="sq-bullet <?php echo esc_attr( $selected_page ); ?> mol-settings my-settings"><a href="<?php echo esc_attr( bp_members_get_user_url( $user_id, bp_members_get_path_chunks( [ bp_get_settings_slug() ] ) ) ); ?>/"><?php esc_html_e( 'My Settings', 'commons-in-a-box' ); ?></a></li>

				<?php if ( $portfolio_group_type ) : ?>
					<?php if ( openlab_user_has_portfolio( bp_displayed_user_id() ) && ( ! cboxol_group_is_hidden( openlab_get_user_portfolio_id() ) || openlab_is_my_profile() || groups_is_user_member( bp_loggedin_user_id(), openlab_get_user_portfolio_id() ) ) ) : ?>

						<li id="portfolios-groups-li<?php echo ( $mobile ? '-mobile' : '' ); ?>" class="visible-xs mobile-anchor-link"><a href="#portfolio-sidebar-inline-widget" id="portfolios<?php echo ( $mobile ? '-mobile' : '' ); ?>"><?php echo esc_html( $portfolio_group_type->get_label( 'my_portfolio' ) ); ?></a></li>

					<?php else : ?>

						<li id="portfolios-groups-li<?php echo ( $mobile ? '-mobile' : '' ); ?>" class="visible-xs mobile-anchor-link"><a href="#portfolio-sidebar-inline-widget" id="portfolios<?php echo ( $mobile ? '-mobile' : '' ); ?>"><?php echo esc_html( $portfolio_group_type->get_label( 'create_item' ) ); ?></a></li>

					<?php endif; ?>
				<?php endif; ?>

				<?php foreach ( $group_types as $group_type ) : ?>
					<?php
					$selected = '';
					if (
						( bp_is_user_groups() && $group_type->get_slug() === $current_group_type )
						||
						openlab_is_create_group( $group_type->get_slug() )
					) {
						$selected = 'selected-page';
					}
					?>
					<li class="sq-bullet <?php echo esc_attr( $selected ); ?> mol-courses my-<?php echo esc_attr( $group_type->get_slug() ); ?>"><a href="<?php echo esc_attr( cboxol_get_user_group_type_directory_url( $group_type, bp_loggedin_user_id() ) ); ?>"><?php echo esc_html( $group_type->get_label( 'my_groups' ) ); ?></a></li>
				<?php endforeach; ?>

				<?php /* Get a friend request count */ ?>
				<?php if ( bp_is_active( 'friends' ) ) : ?>
					<?php
					$request_ids   = friends_get_friendship_request_user_ids( bp_loggedin_user_id() );
					$request_count = intval( count( (array) $request_ids ) );
					$selected_page = bp_is_user_friends() ? 'selected-page' : '';
					?>

					<li class="sq-bullet <?php echo esc_attr( $selected_page ); ?> mol-friends my-friends">
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a href="<?php echo esc_attr( bp_members_get_user_url( $user_id, bp_members_get_path_chunks( [ bp_get_friends_slug() ] ) ) ); ?>"><?php esc_html_e( 'My Friends', 'commons-in-a-box' ); ?> <?php echo openlab_get_menu_count_mup( $request_count ); ?></a>
					</li>
				<?php endif; ?>

				<?php /* Get an unread message count */ ?>
				<?php if ( bp_is_active( 'messages' ) ) : ?>
					<?php
					$message_count = bp_get_total_unread_messages_count();
					$selected_page = bp_is_user_messages() ? 'selected-page' : '';
					?>

					<li class="sq-bullet <?php echo esc_attr( $selected_page ); ?> mol-messages my-messages">
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a href="<?php echo esc_attr( bp_members_get_user_url( $user_id, bp_members_get_path_chunks( [ bp_get_messages_slug() ] ) ) ); ?>"><?php esc_html_e( 'My Messages', 'commons-in-a-box' ); ?> <?php echo openlab_get_menu_count_mup( $message_count ); ?></a>
					</li>
				<?php endif; ?>

				<?php /* Get an invitation count */ ?>
				<?php if ( bp_is_active( 'groups' ) ) : ?>
					<?php
					$invites      = groups_get_invites_for_user();
					$invite_count = isset( $invites['total'] ) ? (int) $invites['total'] : 0;

					$selected_page = bp_is_current_action( 'invites' ) || bp_is_current_action( 'sent-invites' ) || bp_is_current_action( 'invite-new-members' ) ? 'selected-page' : '';
					?>

					<li class="sq-bullet <?php echo esc_attr( $selected_page ); ?> mol-invites my-invites">
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a href="<?php echo esc_attr( bp_members_get_user_url( $user_id, bp_members_get_path_chunks( [ bp_get_groups_slug(), 'invites' ] ) ) ); ?>"><?php esc_html_e( 'My Invitations', 'commons-in-a-box' ); ?> <?php echo openlab_get_menu_count_mup( $invite_count ); ?></a>
					</li>
				<?php endif ?>
			</ul>

		</div>

	<?php else : ?>

		<div id="item-buttons<?php echo ( $mobile ? '-mobile' : '' ); ?>" class="mol-menu sidebar-block <?php echo esc_attr( $classes ); ?>">

			<ul class="sidebar-nav clearfix">

				<?php $selected_page = bp_is_user_activity() ? 'selected-page' : ''; ?>
				<li class="sq-bullet <?php echo esc_attr( $selected_page ); ?> mol-profile"><a href="<?php echo esc_attr( bp_members_get_user_url( $user_id ) ); ?>"><?php esc_html_e( 'Profile', 'commons-in-a-box' ); ?></a></li>

				<?php if ( $portfolio_group_type ) : ?>
					<?php if ( openlab_user_has_portfolio( bp_displayed_user_id() ) && ( ! cboxol_group_is_hidden( openlab_get_user_portfolio_id() ) || openlab_is_my_profile() || groups_is_user_member( bp_loggedin_user_id(), openlab_get_user_portfolio_id() ) ) ) : ?>

						<li id="portfolios-groups-li<?php echo ( $mobile ? '-mobile' : '' ); ?>" class="visible-xs mobile-anchor-link"><a href="#portfolio-sidebar-inline-widget" id="portfolios<?php echo ( $mobile ? '-mobile' : '' ); ?>"><?php echo esc_html( $portfolio_group_type->get_label( 'single' ) ); ?></a></li>

					<?php endif; ?>
				<?php endif; ?>

				<?php foreach ( $group_types as $group_type ) : ?>
					<?php $selected_page = $group_type->get_slug() === $current_group_type ? 'selected-page' : ''; ?>
					<li class="sq-bullet <?php echo esc_attr( $selected_page ); ?> mol-courses"><a href="<?php echo esc_attr( cboxol_get_user_group_type_directory_url( $group_type, bp_displayed_user_id() ) ); ?>"><?php echo esc_html( $group_type->get_label( 'plural' ) ); ?></a></li>
				<?php endforeach; ?>

				<?php $selected_page = bp_is_user_friends() ? 'selected-page' : ''; ?>
				<li class="sq-bullet <?php echo esc_attr( $selected_page ); ?> mol-friends"><a href="<?php echo esc_attr( bp_members_get_user_url( $user_id, bp_members_get_path_chunks( [ bp_get_friends_slug() ] ) ) ); ?>"><?php esc_html_e( 'Friends', 'commons-in-a-box' ); ?></a></li>

			</ul>

		</div>

		<?php
	endif;
}

/**
 * Member pages sidebar blocks (portfolio link) - modularized for easier parsing of mobile menus
 */
function openlab_members_sidebar_blocks( $mobile_hide = false ) {
	static $counter = 0;

	++$counter;

	$portfolio_group_type = cboxol_get_portfolio_group_type();

	$block_classes = '';

	if ( $mobile_hide ) {
		$block_classes = ' hidden-xs';
	}

	if ( is_user_logged_in() && openlab_is_my_profile() ) :
		?>
		<h2 class="sidebar-header top-sidebar-header hidden-xs"><?php esc_html_e( 'My Profile', 'commons-in-a-box' ); ?></h2>
	<?php else : ?>
		<h2 class="sidebar-header top-sidebar-header hidden-xs"><?php esc_html_e( 'Member Profile', 'commons-in-a-box' ); ?></h2>
	<?php endif; ?>

	<?php

	// Everything after this point is related to Portfolios.
	if ( ! $portfolio_group_type ) {
		return;
	}

	?>

	<?php
	if (
		( ( openlab_user_has_portfolio( bp_displayed_user_id() ) && ! cboxol_group_is_hidden( openlab_get_user_portfolio_id() ) && openlab_show_portfolio_link_on_user_profile() )
		||
		( openlab_is_my_profile() && openlab_user_has_portfolio( bp_displayed_user_id() ) )
		||
		groups_is_user_member( bp_loggedin_user_id(), openlab_get_user_portfolio_id() ) )
	) :
		?>

		<?php if ( ! $mobile_hide ) : ?>
			<?php if ( is_user_logged_in() && openlab_is_my_profile() ) : ?>
				<h2 class="sidebar-header top-sidebar-header visible-xs"><?php echo esc_html( $portfolio_group_type->get_label( 'my_portfolio' ) ); ?></h2>
			<?php else : ?>
				<h2 class="sidebar-header top-sidebar-header visible-xs"><?php echo esc_html( $portfolio_group_type->get_label( 'singular' ) ); ?></h2>
			<?php endif; ?>
		<?php endif; ?>

		<?php /* Abstract the displayed user id, so that this function works properly on my-* pages */ ?>
		<?php $displayed_user_id = bp_is_user() ? bp_displayed_user_id() : bp_loggedin_user_id(); ?>

		<div class="sidebar-block<?php echo esc_attr( $block_classes ); ?>">

			<ul class="sidebar-sublinks portfolio-sublinks inline-element-list">

				<li class="portfolio-site-link">
					<a class="bold no-deco" href="<?php openlab_user_portfolio_url(); ?>">
						<?php if ( openlab_is_my_profile() ) : ?>
							<?php echo esc_html( $portfolio_group_type->get_label( 'my_portfolio_site' ) ); ?>
						<?php else : ?>
							<?php echo esc_html( $portfolio_group_type->get_label( 'visit_portfolio_site' ) ); ?>
						<?php endif; ?>
						&nbsp;<span class="fa fa-chevron-circle-right cyan-circle" aria-hidden="true"></span>
					</a>
				</li>

				<li class="portfolio-dashboard-link">
					<a href="<?php openlab_user_portfolio_profile_url(); ?>"><?php esc_html_e( 'Portfolio Home', 'commons-in-a-box' ); ?></a>
					<?php if ( openlab_is_my_profile() && openlab_user_portfolio_site_is_local() ) : ?>
						| <a href="<?php openlab_user_portfolio_url(); ?>/wp-admin"><?php esc_html_e( 'Dashboard', 'commons-in-a-box' ); ?></a>
					<?php endif ?>
				</li>

			</ul>

			<?php if ( openlab_is_my_profile() && openlab_user_has_portfolio( bp_displayed_user_id() ) && ! bp_is_group_create() ) : ?>
				<li class="portfolio-profile-link-toggle-wrapper">
					<input value="1" data-counter="<?php echo esc_attr( $counter ); ?>" type="checkbox" id="portfolio-profile-link-toggle-<?php echo esc_attr( $counter ); ?>" class="portfolio-profile-link-toggle-checkbox" <?php checked( openlab_show_portfolio_link_on_user_profile() ); ?> /> <label for="portfolio-profile-link-toggle-<?php echo esc_attr( $counter ); ?>"><?php esc_html_e( 'Show link to my Portfolio on my public Profile', 'commons-in-a-box' ); ?></label>

					<?php wp_nonce_field( 'openlab_portfolio_link_visibility', 'openlab_portfolio_link_visibility_nonce_' . $counter, false ); ?>
				</li>
			<?php endif; ?>
		</div>

	<?php elseif ( openlab_is_my_profile() && ! bp_is_group_create() ) : ?>
		<?php /* Don't show the 'Create a Portfolio' link during group (ie Portfolio) creation */ ?>
		<div class="sidebar-widget" id="portfolio-sidebar-widget">

			<?php if ( is_user_logged_in() && openlab_is_my_profile() ) : ?>
				<h2 class="sidebar-header top-sidebar-header visible-xs"><?php echo esc_html( $portfolio_group_type->get_label( 'my_portfolio' ) ); ?></h2>
			<?php endif; ?>

			<div class="sidebar-block<?php echo esc_attr( $block_classes ); ?>">
				<ul class="sidebar-sublinks portfolio-sublinks inline-element-list">
					<li>
						<a class="bold" href="<?php openlab_portfolio_creation_url(); ?>">+ <?php echo esc_html( $portfolio_group_type->get_label( 'create_item' ) ); ?></a>
					</li>
				</ul>
			</div>
		</div>

		<?php
	endif;
}
