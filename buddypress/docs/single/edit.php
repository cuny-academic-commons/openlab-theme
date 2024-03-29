<?php require apply_filters( 'bp_docs_header_template', bp_docs_locate_template( 'docs-header.php' ) ); ?>

<?php
$doc    = bp_docs_get_current_doc();
$doc_id = isset( $doc->ID ) ? (int) $doc->ID : 0;
?>

<?php
// No media support at the moment. Want to integrate with something like BP Group Documents
// include_once ABSPATH . '/wp-admin/includes/media.php' ;

if ( ! function_exists( 'wp_editor' ) ) {
	require_once ABSPATH . '/wp-admin/includes/post.php';
	// phpcs:ignore WordPress.WP.DeprecatedFunctions.wp_tiny_mceFound
	wp_tiny_mce();
}
?>

<div class="doc-content img-rounded edit-doc">

	<form action="" method="post" class="form-group form-panel" id="doc-form">

		<div class="panel panel-default">
			<div class="panel-heading"><?php echo $doc_id ? esc_html__( 'Edit Doc', 'commons-in-a-box' ) : esc_html__( 'New Doc', 'commons-in-a-box' ); ?></div>
			<div class="panel-body">

				<?php do_action( 'template_notices' ); ?>

				<div id="idle-warning" style="display:none">
					<p><?php esc_html_e( 'You have been idle for <span id="idle-warning-time"></span>', 'commons-in-a-box' ); ?></p>
				</div>

				<div class="doc-header">
					<?php if ( bp_docs_is_existing_doc() ) : ?>
						<input type="hidden" name="existing-doc-id" value="<?php the_ID(); ?>" />
						<input type="hidden" name="doc_id" value="<?php echo esc_attr( $doc_id ); ?>" />
					<?php endif ?>
				</div>
				<div class="doc-content-wrapper">
					<div id="doc-content-title">
						<label for="doc[title]"><?php esc_html_e( 'Title', 'commons-in-a-box' ); ?></label>
						<input type="text" id="doc-title" name="doc[title]" class="form-control" value="<?php bp_docs_edit_doc_title(); ?>" />
					</div>

					<?php if ( bp_docs_is_existing_doc() ) : ?>
						<div id="doc-content-permalink">
							<label for="doc[permalink]"><?php esc_html_e( 'Permalink', 'commons-in-a-box' ); ?></label>
							<code><?php echo esc_url( trailingslashit( bp_get_group_permalink() ) . BP_DOCS_SLUG . '/' ); ?></code><input type="text" id="doc-permalink" name="doc[permalink]" class="long" value="<?php bp_docs_edit_doc_slug(); ?>" />
						</div>
					<?php endif ?>

					<div id="doc-content-textarea">
						<label id="content-label" for="doc[content]"><?php esc_html_e( 'Content', 'commons-in-a-box' ); ?></label>
						<div id="editor-toolbar">
							<?php
							wp_editor(
								bp_docs_get_edit_doc_content(),
								'doc_content',
								array(
									'media_buttons' => false,
									'dfw'           => false,
								)
							);
							?>
						</div>
					</div>

					<div id="doc-meta">
						<div id="doc-tax" class="doc-meta-box">
							<div class="toggleable toggle-closed">
								<p id="tags-toggle-edit" class="toggle-switch"><?php esc_html_e( 'Tags', 'commons-in-a-box' ); ?></p>

								<div class="toggle-content">
									<table class="toggle-table" id="toggle-table-tags">
										<tr>
											<td class="desc-column">
												<label for="bp_docs_tag"><?php esc_html_e( 'Tags are words or phrases that help to describe and organize your Docs.', 'commons-in-a-box' ); ?></label>
												<span class="description"><?php esc_html_e( 'Separate tags with commas (for example: <em>orchestra, snare drum, piccolo, Brahms</em>)', 'commons-in-a-box' ); ?></span>
											</td>

											<td>
												<?php bp_docs_post_tags_meta_box(); ?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>

						<div id="doc-parent" class="doc-meta-box">
							<div class="toggleable toggle-closed">
								<p class="toggle-switch" id="parent-toggle"><?php esc_html_e( 'Parent', 'commons-in-a-box' ); ?></p>

								<div class="toggle-content">
									<table class="toggle-table" id="toggle-table-parent">
										<tr>
											<td class="desc-column">
												<label for="parent_id"><?php esc_html_e( 'Select a parent for this Doc.', 'commons-in-a-box' ); ?></label>

												<span class="description"><?php esc_html_e( '(Optional) Assigning a parent Doc means that a link to the parent will appear at the bottom of this Doc, and a link to this Doc will appear at the bottom of the parent.', 'commons-in-a-box' ); ?></span>
											</td>

											<td class="content-column">
												<?php bp_docs_edit_parent_dropdown(); ?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>

						<?php if ( current_user_can( 'bp_docs_manage', $doc_id ) && apply_filters( 'bp_docs_allow_access_settings', true ) ) : ?>
							<div id="doc-settings" class="doc-meta-box">
								<div class="toggleable toggle-closed">
									<p class="toggle-switch" id="settings-toggle"><?php esc_html_e( 'Settings', 'commons-in-a-box' ); ?></p>

									<div class="toggle-content">
										<table class="toggle-table" id="toggle-table-settings">
											<?php bp_docs_doc_settings_markup(); ?>
										</table>
									</div>
								</div>
							</div>
						<?php endif ?>
					</div>

					<?php do_action( 'bp_docs_closing_meta_box', $doc_id ); ?>

					<div style="clear: both"> </div>

					<div id="doc-submit-options">

						<div class="notify-group-members-ui">
							<?php openlab_notify_group_members_ui( ! bp_docs_is_existing_doc() ); ?>
						</div>

						<?php wp_nonce_field( 'bp_docs_save' ); ?>

						<input class="btn btn-primary" type="submit" name="doc-edit-submit" id="doc-edit-submit" value="<?php esc_html_e( 'Save', 'commons-in-a-box' ); ?>"> <a href="<?php bp_docs_cancel_edit_link(); ?>" class="action safe btn btn-default no-deco"><?php esc_html_e( 'Cancel', 'commons-in-a-box' ); ?></a>

						<?php if ( bp_docs_is_existing_doc() && current_user_can( 'bp_docs_manage', $doc_id ) ) : ?>
							<a class="delete-doc-button confirm" href="<?php bp_docs_delete_doc_link(); ?>"><?php esc_html_e( 'Delete', 'commons-in-a-box' ); ?></a>
						<?php endif ?>
					</div>

					<div style="clear: both"> </div>
				</div>

			</div>
		</div>
	</form>

</div><!-- .doc-content -->

<?php bp_docs_inline_toggle_js(); ?>

<?php if ( ! function_exists( 'wp_editor' ) ) : ?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			/* On some setups, it helps TinyMCE to load if we fire the switchEditors event on load */
			if (typeof (switchEditors) == 'object') {
				if (!$("#edButtonPreview").hasClass('active')) {
					switchEditors.go('doc[content]', 'tinymce');
				}
			}
		}, (jQuery));
	</script>
<?php endif ?>

<?php /* Important - do not remove. Needed for autosave stuff */ ?>
<div style="display:none;">
	<div id="still_working_content" name="still_working_content">
		<br />
		<h3><?php esc_html_e( 'Are you still there?', 'commons-in-a-box' ); ?></h3>

		<p><?php esc_html_e( 'In order to prevent overwriting content, only one person can edit a given doc at a time. For that reason, you must periodically ensure the system that you\'re still actively editing. If you are idle for more than 30 minutes, your changes will be auto-saved, and you\'ll be sent out of Edit mode so that others can access the doc.', 'commons-in-a-box' ); ?></p>

		<a href="#" onclick="jQuery.colorbox.close();
				return false" class="button"><?php esc_html_e( 'I\'m still editing!', 'commons-in-a-box' ); ?></a>
	</div>
</div>
