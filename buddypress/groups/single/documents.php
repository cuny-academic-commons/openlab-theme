<?php
// instantiating the template will do the heavy lifting with all the superglobal variables
$template = new BP_Group_Documents_Template();
?>

	<div id="bp-group-documents">

		<?php do_action( 'template_notices' ); // (error/success feedback) ?>

		<?php // LIST VIEW ?>

		<?php if ( $template->document_list && 1 <= count( $template->document_list ) ) : ?>

			<?php if ( get_option( 'bp_group_documents_use_categories' ) ) : ?>
				<div id="bp-group-documents-categories">
					<form id="bp-group-documents-category-form" method="get" action="<?php echo esc_attr( $template->action_link ); ?>">
						&nbsp; <?php esc_html_e( 'Category:', 'commons-in-a-box' ); ?>
						<select name="category">
							<option value="" ><?php esc_html_e( 'All', 'commons-in-a-box' ); ?></option>
							<?php foreach ( $template->get_group_categories() as $category ) : ?>
								<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php checked( $template->category, $category->term_id ); ?>><?php echo esc_html( $category->name ); ?></option>
							<?php endforeach; ?>
						</select>
						<input type="submit" class="button" value="<?php esc_html_e( 'Go', 'commons-in-a-box' ); ?>" />
						<?php wp_nonce_field( 'bp_group_document_save_' . $template->operation, 'bp_group_document_save' ); ?>
					</form>
				</div>
			<?php endif; ?>

			<div id="bp-group-documents-sorting">
				<div class="row">
					<div class="col-sm-12">
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
				</div>
			</div>

				<ul id="bp-group-documents-list" class="item-list group-list inline-element-list">
					<?php
					// loop through each document and display content along with admin options
					$count = 0;
					foreach ( $template->document_list as $document_params ) {
						$document = new BP_Group_Documents( $document_params['id'], $document_params );

						$count++;

						$count_class = $count % 2 ? 'alt' : '';
						?>

						<li class="list-group-item <?php echo esc_attr( $count_class ); ?>">

							<?php
							// show edit and delete options if user is privileged
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
								$document->icon();
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

							echo '</li>';
					}
					?>
				</ul>

			<?php else : ?>
				<div id="message" class="info">
					<p class="bold"><?php esc_html_e( 'There are no files to view.', 'commons-in-a-box' ); ?></p>
				</div>

			<?php endif; ?>

			<div class="spacer">&nbsp;</div>

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

				$header_text = __( 'Upload a New File', 'commons-in-a-box' );
				if ( 'edit' === $template->operation ) {
					$header_text = __( 'Edit File', 'commons-in-a-box' );
				}

				?>

				<div id="<?php echo esc_attr( $this_id ); ?>">

					<form method="post" id="bp-group-documents-form" class="standard-form form-panel" action="<?php echo esc_attr( $template->action_link ); ?>" enctype="multipart/form-data">

						<div class="panel panel-default">
							<div class="panel-heading"><?php echo esc_html( $header_text ); ?></div>
							<div class="panel-body">

								<input type="hidden" name="bp_group_documents_operation" value="<?php echo esc_attr( $template->operation ); ?>" />
								<input type="hidden" name="bp_group_documents_id" value="<?php echo esc_attr( $template->id ); ?>" />

								<?php if ( 'add' === $template->operation ) { ?>

									<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo esc_attr( return_bytes( ini_get( 'post_max_size' ) ) ); ?>" />
									<label><?php esc_html_e( 'Choose File:', 'commons-in-a-box' ); ?></label>
									<div class="form-control type-file-wrapper">
										<input type="file" name="bp_group_documents_file" class="bp-group-documents-file" />
									</div>
								<?php } ?>

								<?php if ( BP_GROUP_DOCUMENTS_FEATURED ) { ?>
									<div class="checkbox">
										<label class="bp-group-documents-featured-label"><input type="checkbox" name="bp_group_documents_featured" class="bp-group-documents-featured" value="1" <?php checked( $template->featured ); ?> /> <?php esc_html_e( 'Featured File', 'commons-in-a-box' ); ?></label>
									</div>
								<?php } ?>

								<div id="document-detail-clear" class="clear"></div>
								<div class="document-info">
									<label><?php esc_html_e( 'Display Name:', 'commons-in-a-box' ); ?></label>
									<input type="text" name="bp_group_documents_name" id="bp-group-documents-name" class="form-control" value="<?php echo esc_attr( $template->name ); ?>" />
									<?php if ( BP_GROUP_DOCUMENTS_SHOW_DESCRIPTIONS ) { ?>
										<label><?php esc_html_e( 'Description:', 'commons-in-a-box' ); ?></label>
										<textarea name="bp_group_documents_description" id="bp-group-documents-description" class="form-control"><?php echo esc_html( $template->description ); ?></textarea>
									<?php } ?>
									<label></label>
								</div>
							</div>
						</div>

						<div class="notify-group-members-ui">
							<?php /* Default to checked for 'add' only, not 'edit' */ ?>
							<?php openlab_notify_group_members_ui( 'add' === $template->operation ); ?>
						</div>

						<input type="submit" class="btn btn-primary btn-margin btn-margin-top" value="<?php esc_attr_e( 'Submit', 'commons-in-a-box' ); ?>" />

						<?php if ( get_option( 'bp_group_documents_use_categories' ) ) { ?>
							<div class="bp-group-documents-category-wrapper">
								<label><?php esc_html_e( 'Category:', 'commons-in-a-box' ); ?></label>
								<div class="bp-group-documents-category-list">
									<ul class="inline-element-list">
										<?php foreach ( $template->get_group_categories( false ) as $category ) { ?>
											<li><input type="checkbox" name="bp_group_documents_categories[]" value="<?php echo esc_attr( $category->term_id ); ?>" <?php checked( $template->doc_in_category( $category->term_id ) ); ?> /><?php echo esc_html( $category->name ); ?></li>
										<?php } ?>
									</ul>
								</div>
								<input type="text" name="bp_group_documents_new_category" class="bp-group-documents-new-category" />
							</div><!-- .bp-group-documents-category-wrapper -->
						<?php } ?>

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
