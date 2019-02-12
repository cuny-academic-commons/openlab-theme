<?php

/**
 * BuddyPress - Users Settings
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php

switch ( bp_current_action() ) :
	case 'notifications'  :
		bp_get_template_part( 'members/single/settings/notifications' );
		break;
	case 'capabilities'   :
		bp_get_template_part( 'members/single/settings/capabilities' );
		break;
	case 'delete-account' :
		bp_get_template_part( 'members/single/settings/delete-account' );
		break;
	case 'general'        :
		bp_get_template_part( 'members/single/settings/general' );
		break;
	case 'data'        :
		echo openlab_submenu_markup();
	?>

		<div id="item-body" class="form-panel" role="main">
			<div class="panel panel-default">
				<div class="panel-heading"><?php esc_html_e( 'Export Data', 'openlab-theme' ); ?></div>
				<div class="panel-body">
					<?php bp_get_template_part( 'members/single/settings/data' ); ?>
				</div>
			</div>
		</div>

	<?php
		break;

	default:
		bp_get_template_part( 'members/single/plugins' );
		break;
endswitch;
