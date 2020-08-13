<?php if ( bp_docs_is_existing_doc() ) : ?>
	<div class="doc-tabs">
		<ul>
			<?php $li_class = 'single' === bp_docs_current_view() ? 'current' : ''; ?>
			<li class="<?php echo esc_attr( $li_class ); ?>">
				<a href="<?php echo esc_attr( bp_docs_get_group_doc_permalink() ); ?>"><?php esc_html_e( 'Read', 'commons-in-a-box' ); ?></a>
			</li>

			<?php if ( current_user_can( 'bp_docs_edit', bp_docs_get_current_doc()->ID ) ) : ?>
				<?php $li_class = 'edit' === bp_docs_current_view() ? 'current' : ''; ?>
				<li class="<?php echo esc_attr( $li_class ); ?>">
					<a href="<?php echo esc_url( bp_docs_get_group_doc_permalink() . '/' . BP_DOCS_EDIT_SLUG ); ?>"><?php esc_html_e( 'Edit', 'commons-in-a-box' ); ?></a>
				</li>
			<?php endif ?>

			<?php do_action( 'bp_docs_header_tabs' ); ?>
		</ul>
	</div>
<?php endif ?>
