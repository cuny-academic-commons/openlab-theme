<?php

wp_enqueue_script( 'openlab-group-documents' );

// Instantiating the template will do the heavy lifting with all the superglobal variables.
$template = new BP_Group_Documents_Template();

$folders = $template->get_group_categories( false );
$folders = bp_sort_by_key( $folders, 'name' );

$non_empty_folders = array_filter(
	$folders,
	function( $folder ) {
		return $folder->count > 0;
	}
);

$current_category      = false;
$current_category_data = get_term_by( 'id', $template->category, 'group-documents-category' );

if ( ! empty( $current_category_data->name ) ) {
	$current_category = $current_category_data->name;
}

$is_edit_mode = bp_is_action_variable( 'edit', 0 );

$classes = [];
if ( $non_empty_folders ) {
	$classes[] = 'has-folders';
}
if ( $is_edit_mode ) {
	$classes[] = 'is-edit-mode';
}
if ( $current_category ) {
	$classes[] = 'is-folder';
}

$user_can_upload = current_user_can( 'bp_moderate' ) || groups_is_user_member( bp_loggedin_user_id(), bp_get_current_group_id() );

$sort_form_action = $template->action_link;

$header_text = 'add' === $template->operation ? __( 'Add a New File', 'commons-in-a-box' ) : __( 'Edit a File', 'commons-in-a-box' );

?>

	<div id="bp-group-documents" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

		<?php do_action( 'template_notices' ); // (error/success feedback) ?>

		<?php // LIST VIEW ?>

		<div class="bp-group-documents-columns">
			<div class="bp-group-documents-main-column">
				<?php if ( $template->document_list && 1 <= count( $template->document_list ) ) : ?>
					<div id="bp-group-documents-sorting">
						<div class="row">
							<div class="col-sm-8 sorting-column">
								<form id="bp-group-documents-sort-form" method="get" action="<?php echo esc_attr( $template->action_link ); ?>">
									<?php esc_html_e( 'Order by:', 'commons-in-a-box' ); ?>
									<select name="order" class="form-control">
										<option value="newest" <?php selected( 'newest', $template->order ); ?>><?php esc_html_e( 'Newest', 'commons-in-a-box' ); ?></option>
										<option value="alpha" <?php selected( 'alpha', $template->order ); ?>><?php esc_html_e( 'Alphabetical', 'commons-in-a-box' ); ?></option>
										<option value="popular" <?php selected( 'popular', $template->order ); ?>><?php esc_html_e( 'Most Popular', 'commons-in-a-box' ); ?></option>
									</select>
									<input type="submit" class="button" value="<?php esc_html_e( 'Go', 'commons-in-a-box' ); ?>" />
								</form>
							</div>

							<?php if ( $user_can_upload ) : ?>
								<div class="pull-right upload-new-file">
									<?php if ( 'add' === $template->operation ) { ?>
										<a class="btn btn-primary link-btn" id="bp-group-documents-upload-button" href="" style="display:none;"><?php esc_html_e( 'Add new file', 'commons-in-a-box' ); ?></a>
									<?php } ?>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<div class="bp-group-documents-list-container">
						<?php if ( $current_category ) : ?>
							<div class="bp-group-documents-list-folder-header">
								<i class="fa fa-folder-open-o"></i> Folder: <?php echo esc_html( $current_category ); ?>
								<div class="admin-links pull-right">
									<?php
									if ( bp_is_item_admin() ) {
										$delete_link = wp_nonce_url( $template->action_link . 'delete-folder/' . $current_category_data->term_id, 'group-documents-delete-folder-link' );
										echo "<a class='btn btn-primary btn-xs link-btn no-margin no-margin-top' href='" . esc_attr( $delete_link ) . "' id='bp-group-documents-folder-delete'>Delete</a>";
									}
									?>
								</div>
							</div>
						<?php endif; ?>

						<ul id="bp-group-documents-list" class="item-list group-list inline-element-list">
							<?php
							// Loop through each document and display content along with admin options.
							$count = 0;
							foreach ( $template->document_list as $document_params ) {
								$document = new BP_Group_Documents( $document_params['id'], $document_params );

								++$count;
								$count_class = $count % 2 ? 'alt' : '';

								$document->doc_type = openlab_get_document_type( $document->file );
								$document->doc_url  = 'upload' === $document->doc_type ? $document->get_url() : $document->file;
								?>

								<li class="list-group-item <?php echo esc_attr( $count_class ); ?>">
									<?php
									// Show edit and delete options if user is privileged.
									echo '<div class="admin-links pull-right">';
									if ( $document->current_user_can( 'edit' ) ) {
										$edit_link = wp_nonce_url( $template->action_link . 'edit/' . $document->id, 'group-documents-edit-link' );
										echo '<a class="btn btn-primary btn-xs link-btn no-margin no-margin-top" href="' . esc_attr( $edit_link ) . '">' . esc_html__( 'Edit', 'commons-in-a-box' ) . '</a> ';
									}
									if ( $document->current_user_can( 'delete' ) ) {
										$delete_link = wp_nonce_url( $template->action_link . 'delete/' . $document->id, 'group-documents-delete-link' );
										echo '<a class="btn btn-primary btn-xs link-btn no-margin no-margin-top" href="' . esc_attr( $delete_link ) . '" id="bp-group-documents-delete">' . esc_html__( 'Delete', 'commons-in-a-box' ) . '</a>';
									}

									echo '</div>';
									?>

									<?php
									if ( get_option( 'bp_group_documents_display_icons' ) ) {
										if ( 'upload' === $document->doc_type ) {
											$document->icon();
										} else {
											openlab_external_link_icon( $document->file );
										}
									}
									?>

									<a class="group-documents-title" id="group-document-link-<?php echo esc_attr( $document->id ); ?>" href="<?php $document->url(); ?>" target="_blank"><?php echo esc_html( stripslashes( $document->name ) ); ?>

										<?php
										if ( get_option( 'bp_group_documents_display_file_size' ) ) {
											echo ' <span class="group-documents-filesize">(' . esc_html( get_file_size( $document ) ) . ')</span>';
										}
										?>
									</a> &nbsp;

									<span class="group-documents-meta">
										<?php
										printf(
											// translators: 1. User link, 2. upload date
											esc_html__( 'Uploaded by %1$s on %2$s', 'commons-in-a-box' ),
											// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											bp_core_get_userlink( $document->user_id ),
											esc_html( gmdate( get_option( 'date_format' ), $document->created_ts ) )
										);
										?>
									</span>

									<?php
									if ( BP_GROUP_DOCUMENTS_SHOW_DESCRIPTIONS && $document->description ) {
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo '<br /><span class="group-documents-description">' . nl2br( stripslashes( $document->description ) ) . '</span>';
									}

								// phpcs:ignore Generic.WhiteSpace.ScopeIndent.Incorrect
								echo '</li>';
							}
							?>
							</ul>
						</div><!-- .bp-group-documents-list-container -->

					<?php else : ?>
						<div id="message" class="info">
							<p class="bold">
								<?php esc_html_e( 'No files have been added yet. Add a new file below.', 'commons-in-a-box' ); ?>
							</p>

							<div class="upload-new-file">
								<?php if ( 'add' === $template->operation ) { ?>
									<a class="btn btn-primary link-btn" id="bp-group-documents-upload-button" href="" style="display:none;"><?php esc_html_e( 'Add new file', 'commons-in-a-box' ); ?></a>
								<?php } ?>
							</div>
						</div>

					<?php endif; ?>
				</div><!-- .bp-group-documents-main-column -->

				<div class="bp-group-documents-folder-links">
					<label><?php esc_html_e( 'Folders:', 'commons-in-a-box' ); ?></label>
					<div class="group-file-folder-nav">
						<ul>
							<li class="show-all-files <?php echo $current_category ? 'current-category' : ''; ?>"><i class="fa <?php echo $current_category ? 'fa-folder-o' : 'fa-folder-open-o'; ?>"></i> <a href="<?php echo esc_url( remove_query_arg( 'category', $template->action_link ) ); ?>"><?php esc_html_e( 'All Files', 'commons-in-a-box' ); ?></a></li>
							<hr>

							<?php foreach ( $non_empty_folders as $category ) { ?>
								<?php $is_current_category = ( $category->name === $current_category ); ?>
								<li class="folder <?php echo $is_current_category ? 'current-category' : ''; ?>"><i class="fa <?php echo $is_current_category ? 'fa-folder-open-o' : 'fa-folder-o'; ?>"></i> <a href="<?php echo esc_attr( add_query_arg( 'category', $category->term_id, $template->action_link ) ); ?>"><?php echo esc_html( $category->name ); ?></a></li>
							<?php } ?>
						</ul>
					</div>
				</div><!-- .bp-group-documents-folder-links -->
			</div><!-- .bp-group-documents-columns -->

			<div class="pagination no-ajax">
				<?php if ( $template->show_pagination() ) { ?>
					<div class="pagination" id="pag-bottom">

						<div id="member-dir-pag-bottom" class="pagination-links">
							<ul class="page-numbers pagination">
								<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo openlab_bp_group_documents_custom_pagination_links( $template ); ?>
							</ul>
						</div>
					</div>
				<?php } ?>
			</div>

			<?php // -----------------------------------------------DETAIL VIEW-- ?>

			<?php if ( $template->show_detail ) { ?>

				<?php
				if ( 'add' === $template->operation ) {
					$this_id = 'bp-group-documents-upload-new';
				} else {
					$this_id = 'bp-group-documents-edit';
				}

				$header_text = __( 'Add New File', 'commons-in-a-box' );
				if ( 'edit' === $template->operation ) {
					$header_text = __( 'Edit File', 'commons-in-a-box' );
				}

				// Set up 'external' or 'upload' doc_type.
				$document           = new BP_Group_Documents( $template->id );
				$template->file     = $document->file;
				$template->doc_type = openlab_get_document_type( $template->file );

				?>

				<div id="<?php echo esc_attr( $this_id ); ?>">

					<form method="post" id="bp-group-documents-form" class="standard-form form-panel" action="<?php echo esc_attr( $template->action_link ); ?>" enctype="multipart/form-data">

						<div class="panel panel-default">
							<div class="panel-heading"><?php echo esc_html( $header_text ); ?></div>
							<div class="panel-body">

								<?php if ( 'add' === $template->operation ) { ?>
									<p><?php esc_html_e( 'You can link to an external file, such as a OneDrive or Dropbox file. Or you can upload a file from your computer.', 'commons-in-a-box' ); ?></p>
								<?php } ?>

								<input type="hidden" name="bp_group_documents_operation" value="<?php echo esc_attr( $template->operation ); ?>" />
								<input type="hidden" name="bp_group_documents_id" value="<?php echo esc_attr( $template->id ); ?>" />

								<?php if ( 'edit' === $template->operation ) : ?>
									<input type="hidden" name="bp_group_documents_file_type" value="<?php echo esc_attr( $template->doc_type ); ?>" />
								<?php endif; ?>

								<div class="bp-group-documents-fields <?php echo esc_attr( 'add' === $template->operation ? 'show-upload' : 'show-' . $template->doc_type ); ?>">
									<!-- Link -->
									<?php if ( 'add' === $template->operation ) { ?>
									<div class="bp-group-documents-file-type-selector">
										<input type="radio" name="bp_group_documents_file_type" class="bp-group-documents-file-type" id="bp-group-documents-file-type-link" value="link" />
										<label for="bp-group-documents-file-type-link">Link to external file</label>
									</div>
									<?php } ?>
									<?php if ( 'add' === $template->operation || ( 'edit' === $template->operation && 'link' === $template->doc_type ) ) { ?>
									<div class="bp-group-documents-fields-for-file-type" id="bp-group-documents-fields-for-file-type-link">
										<label for="bp-group-documents-link-url"><?php esc_html_e( 'File URL:', 'bp-group-documents' ); ?></label>
										<input type="text" name="bp_group_documents_link_url" id="bp-group-documents-link-url" class="form-control" value="<?php echo esc_attr( stripslashes( $template->file ) ); ?>" />

										<label for="bp-group-documents-link-name"><?php esc_html_e( 'Display Name:', 'bp-group-documents' ); ?></label>
										<input type="text" name="bp_group_documents_link_name" id="bp-group-documents-link-name" class="form-control" value="<?php echo esc_attr( stripslashes( $template->name ) ); ?>" />

										<?php if ( BP_GROUP_DOCUMENTS_SHOW_DESCRIPTIONS ) { ?>
										<label for="bp-group-documents-link-description"><?php esc_html_e( 'Description:', 'bp-group-documents' ); ?></label>
										<textarea name="bp_group_documents_link_description" id="bp-group-documents-link-description" class="form-control"><?php echo esc_html( stripslashes( $template->description ) ); ?></textarea>
										<?php } ?>

										<div id="document-detail-clear" class="clear"></div>
										<fieldset class="group-file-folders">
											<legend><?php esc_html_e( 'Folders', 'commons-in-a-box' ); ?></legend>
											<div class="checkbox-list-container group-file-folders-container">
												<input type="hidden" name="bp_group_documents_link_categories[]" value="0" />
												<ul>
												<?php foreach ( $folders as $category ) { ?>
													<li><input type="checkbox" name="bp_group_documents_link_categories[]" value="<?php echo esc_attr( $category->term_id ); ?>" id="link-group-folder-<?php echo esc_attr( $category->term_id ); ?>" <?php checked( $template->doc_in_category( $category->term_id ) ); ?> /> <label class="passive" for="link-group-folder-<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></label></li>
												<?php } ?>
												</ul>
											</div>
											<label for="bp-group-documents-new-category" class="sr-only"><?php esc_html_e( 'Add new folder', 'commons-in-a-box' ); ?></label>
											<input type="text" name="bp_group_documents_link_new_category" class="bp-group-documents-new-folder form-control" placeholder="<?php esc_attr_e( 'Add new folder', 'commons-in-a-box' ); ?>" id="bp-group-documents-new-category" />
										</fieldset>
									</div>
									<?php } ?>

									<!-- Upload -->
									<?php if ( 'add' === $template->operation ) { ?>
									<div class="bp-group-documents-file-type-selector">
										<input type="radio" checked="checked" name="bp_group_documents_file_type" class="bp-group-documents-file-type" id="bp-group-documents-file-type-upload" value="upload" />
										<label for="bp-group-documents-file-type-upload">Upload a file</label>
									</div>
									<?php } ?>
									<?php if ( 'add' === $template->operation || ( 'edit' === $template->operation && 'upload' === $template->doc_type ) ) { ?>
									<div class="bp-group-documents-fields-for-file-type" id="bp-group-documents-fields-for-file-type-upload">
										<?php if ( 'add' === $template->operation ) { ?>
										<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo esc_attr( return_bytes( ini_get( 'post_max_size' ) ) ); ?>" />
										<label for="bp-group-documents-file"><?php esc_html_e( 'Choose File:', 'bp-group-documents' ); ?></label>
										<div class="form-control type-file-wrapper">
											<input type="file" id="bp-group-documents-file" name="bp_group_documents_file" class="bp-group-documents-file" />
										</div>
										<?php } ?>

										<div id="document-detail-clear" class="clear"></div>
										<div class="document-info">
											<label for="bp-group-documents-name"><?php esc_html_e( 'Display Name:', 'bp-group-documents' ); ?></label>
											<input type="text" name="bp_group_documents_name" id="bp-group-documents-name" class="form-control" value="<?php echo esc_attr( stripslashes( $template->name ) ); ?>" />

											<?php if ( BP_GROUP_DOCUMENTS_SHOW_DESCRIPTIONS ) { ?>
												<label for="bp-group-documents-description"><?php esc_html_e( 'Description:', 'bp-group-documents' ); ?></label>
												<textarea name="bp_group_documents_description" id="bp-group-documents-description" class="form-control"><?php echo esc_html( stripslashes( $template->description ) ); ?></textarea>
											<?php } ?>

											<fieldset class="group-file-folders">
												<legend><?php esc_html_e( 'Folders', 'commons-in-a-box' ); ?></legend>
												<div class="checkbox-list-container group-file-folders-container">
													<input type="hidden" name="bp_group_documents_categories[]" value="0" />
													<ul>
													<?php foreach ( $folders as $category ) { ?>
														<li><input type="checkbox" name="bp_group_documents_categories[]" value="<?php echo esc_attr( $category->term_id ); ?>" id="group-folder-<?php echo esc_attr( $category->term_id ); ?>" <?php checked( $template->doc_in_category( $category->term_id ) ); ?> /> <label class="passive" for="group-folder-<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></label></li>
													<?php } ?>
													</ul>
												</div>
												<label for="bp-group-documents-new-category" class="sr-only">Add new folder</label>
												<input type="text" name="bp_group_documents_new_category" class="bp-group-documents-new-folder form-control" placeholder="Add new folder" id="bp-group-documents-new-category" />
											</fieldset>
										</div>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>

						<div class="notify-group-members-ui">
							<?php /* Default to checked for 'add' only, not 'edit' */ ?>
							<?php openlab_notify_group_members_ui( 'add' === $template->operation ); ?>
						</div>

						<input type="submit" class="btn btn-primary btn-margin btn-margin-top" value="<?php esc_attr_e( 'Submit', 'commons-in-a-box' ); ?>" />

						<?php wp_nonce_field( 'bp_group_document_save_' . $template->operation, 'bp_group_document_save' ); ?>
					</form>

				</div>

				<div>
					<?php if ( 'add' === $template->operation ) { ?>
						<a class="btn btn-primary link-btn" id="bp-group-documents-upload-button" href="" style="display:none;"><?php esc_html_e( 'Upload a New File', 'commons-in-a-box' ); ?></a>
					<?php } ?>
				</div>

			<?php } ?>

	</div><!--end #group-documents-->
