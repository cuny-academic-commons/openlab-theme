<?php

/**
 * Markup for the Docs nav tabs.
 *
 * @since 1.6.0 Moved to this file from openlab_docs_tabs().
 */

$group = groups_get_current_group();

$group_permalink = bp_get_group_permalink( $group );

?>

<li <?php echo ( 'list' === bp_docs_current_view() ? 'class="current-menu-item"' : '' ); ?> ><a href="<?php echo esc_url( $group_permalink . bp_docs_get_docs_slug() ); ?>/"><?php esc_html_e( 'View Docs', 'commons-in-a-box' ); ?></a></li>
<?php if ( current_user_can( 'bp_docs_create' ) && current_user_can( 'bp_docs_associate_with_group', bp_get_current_group_id() ) ) : ?>
	<li <?php echo ( 'create' === bp_docs_current_view() ? 'class="current-menu-item"' : '' ); ?> ><a href="<?php echo esc_url( $group_permalink . bp_docs_get_docs_slug() ); ?>/create/"><?php esc_html_e( 'New Doc', 'commons-in-a-box' ); ?></a></li>
<?php endif; ?>
<?php if ( ( 'edit' === bp_docs_current_view() || 'single' === bp_docs_current_view() ) && bp_docs_is_existing_doc() ) : ?>
	<?php $doc_obj = bp_docs_get_current_doc(); ?>
	<li class="current-menu-item"><?php echo esc_html( $doc_obj->post_title ); ?></li>
<?php endif; ?>
