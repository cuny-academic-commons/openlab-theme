<?php bp_get_template_part( 'docs/docs-header' ); ?>

<?php if ( bp_docs_is_doc_edit_locked() && bp_docs_current_user_can( 'edit' ) ) : ?>
	<div class="toggleable doc-is-locked">
		<span class="toggle-switch" id="toggle-doc-is-locked"><?php esc_html_e( 'Locked', 'commons-in-a-box' ); ?> <span class="hide-if-no-js description"><?php esc_html_e( '(click for more info)', 'commons-in-a-box' ); ?></span></span>
		<div class="toggle-content">
			<?php /* translators: name of user currently editing Doc */ ?>
			<p><?php printf( esc_html__( 'This doc is currently being edited by %1$s. In order to prevent edit conflicts, only one user can edit a doc at a time.', 'commons-in-a-box' ), esc_html( bp_docs_get_current_doc_locker_name() ) ); ?></p>

			<?php if ( is_super_admin() || bp_group_is_admin() ) : ?>
				<?php /* translators: URL to cancel edit lock */ ?>
				<p><?php printf( esc_html__( 'Please try again in a few minutes. Or, as an admin, you can <a href="%s">force cancel</a> the edit lock.', 'commons-in-a-box' ), esc_attr( bp_docs_get_force_cancel_edit_lock_link() ) ); ?></p>
			<?php else : ?>
				<p><?php esc_html_e( 'Please try again in a few minutes.', 'commons-in-a-box' ); ?></p>
			<?php endif ?>
		</div>
	</div>

	<?php bp_docs_inline_toggle_js(); ?>
<?php endif ?>

<div class="doc-content img-rounded">
	<?php the_content(); ?>
</div>

<div class="doc-meta">
	<?php do_action( 'bp_docs_single_doc_meta' ); ?>
</div>

<?php comments_template( '/docs/comments.php' ); ?>
