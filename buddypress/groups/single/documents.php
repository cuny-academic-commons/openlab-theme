<?php
	global $bp;

	// instanciating the template will do the heavy lifting with all the superglobal variables
	$template = new BP_Group_Documents_Template();
	?>

	<div id="bp-group-documents">

		<?php do_action( 'template_notices' ) // (error/success feedback)  ?>

		<?php // -----------------------------------------------------------------------LIST VIEW--  ?>

		<?php if ( $template->document_list && count( $template->document_list ) >= 1 ) { ?>

			<?php if ( get_option( 'bp_group_documents_use_categories' ) ) { ?>
				<div id="bp-group-documents-categories">
					<form id="bp-group-documents-category-form" method="get" action="<?php echo $template->action_link; ?>">
						&nbsp; <?php echo __( 'Category:', 'bp-group-documents' ); ?>
						<select name="category">
							<option value="" ><?php echo __( 'All', 'bp-group-documents' ); ?></option>
							<?php foreach ( $template->get_group_categories() as $category ) { ?>
								<option value="<?php echo $category->term_id; ?>" <?php if ( $template->category == $category->term_id ) { echo 'selected="selected"';} ?>><?php echo $category->name; ?></option>
							<?php } ?>
						</select>
						<input type="submit" class="button" value="<?php echo __( 'Go', 'bp-group-documents' ); ?>" />
						<?php wp_nonce_field( 'bp_group_document_save_' . $template->operation, 'bp_group_document_save' ); ?>
					</form>
				</div>
			<?php } ?>

			<div id="bp-group-documents-sorting">
				<div class="row">
					<div class="col-sm-12">
						<form id="bp-group-documents-sort-form" method="get" action="<?php echo $template->action_link; ?>">
							<?php _e( 'Order by:', 'bp-group-documents' ); ?>
							<select name="order" class="form-control">
								<option value="newest" <?php if ( 'newest' == $template->order ) { echo 'selected="selected"';} ?>><?php _e( 'Newest', 'bp-group-documents' ); ?></option>
								<option value="alpha" <?php if ( 'alpha' == $template->order ) { echo 'selected="selected"';} ?>><?php _e( 'Alphabetical', 'bp-group-documents' ); ?></option>
								<option value="popular" <?php if ( 'popular' == $template->order ) { echo 'selected="selected"';} ?>><?php _e( 'Most Popular', 'bp-group-documents' ); ?></option>
							</select>
							<input type="submit" class="button" value="<?php _e( 'Go', 'bp-group-documents' ); ?>" />
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
						?>

						<li class="list-group-item <?php if ( ++$count % 2 ) { echo ' alt';} ?>" >

							<?php
							// show edit and delete options if user is privileged
							echo '<div class="admin-links pull-right">';
							if ( $document->current_user_can( 'edit' ) ) {
								$edit_link = wp_nonce_url( $template->action_link . 'edit/' . $document->id, 'group-documents-edit-link' );
								echo "<a class='btn btn-primary btn-xs link-btn no-margin no-margin-top' href='$edit_link'>" . __( 'Edit', 'bp-group-documents' ) . '</a> ';
							}
							if ( $document->current_user_can( 'delete' ) ) {
								$delete_link = wp_nonce_url( $template->action_link . 'delete/' . $document->id, 'group-documents-delete-link' );
								echo "<a class='btn btn-primary btn-xs link-btn no-margin no-margin-top' href='$delete_link' id='bp-group-documents-delete'>" . __( 'Delete', 'bp-group-documents' ) . '</a>';
							}

							echo '</div>';
							?>

							<?php if ( get_option( 'bp_group_documents_display_icons' ) ) { $document->icon();} ?>

							<a class="group-documents-title" id="group-document-link-<?php echo $document->id; ?>" href="<?php $document->url(); ?>" target="_blank"><?php echo esc_html( stripslashes( $document->name ) ); ?>

								<?php
								if ( get_option( 'bp_group_documents_display_file_size' ) ) {
									echo ' <span class="group-documents-filesize">(' . get_file_size( $document ) . ')</span>';
								}
								?></a> &nbsp;

							<span class="group-documents-meta"><?php printf( __( 'Uploaded by %1$s on %2$s', 'bp-group-documents' ), bp_core_get_userlink( $document->user_id ), date( get_option( 'date_format' ), $document->created_ts ) ); ?></span>

							<?php
							if ( BP_GROUP_DOCUMENTS_SHOW_DESCRIPTIONS && $document->description ) {
								echo '<br /><span class="group-documents-description">' . nl2br( stripslashes( $document->description ) ) . '</span>';
							}

							echo '</li>';
					}
						?>
				</ul>

			<?php } else { ?>
				<div id="message" class="info">
					<p class="bold"><?php _e( 'There are no files to view.', 'openlab-theme' ) ?></p>
				</div>

			<?php } ?>
			<div class="spacer">&nbsp;</div>

			<div class="pagination no-ajax">
				<?php if ( $template->show_pagination() ) { ?>
					<div class="pagination" id="pag-bottom">

						<div id="member-dir-pag-bottom" class="pagination-links">
							<ul class="page-numbers pagination">
								<?php echo openlab_bp_group_documents_custom_pagination_links( $template ); ?>
							</ul>
						</div>
					</div>
				<?php } ?>
			</div>

			<?php // -------------------------------------------------------------------DETAIL VIEW--   ?>

			<?php if ( $template->show_detail ) { ?>

				<?php
				if ( $template->operation == 'add' ) {
					$this_id = 'bp-group-documents-upload-new';
				} else {
					$this_id = 'bp-group-documents-edit';
				}

				$header_text = __( 'Upload a New File', 'openlab-theme' );
				if ( 'edit' === $template->operation ) {
					$header_text = __( 'Edit File', 'openlab-theme' );
				}

				?>

				<div id="<?php echo $this_id; ?>">

					<form method="post" id="bp-group-documents-form" class="standard-form form-panel" action="<?php echo $template->action_link; ?>" enctype="multipart/form-data">

						<div class="panel panel-default">
							<div class="panel-heading"><?php echo esc_html( $header_text ); ?></div>
							<div class="panel-body">

								<input type="hidden" name="bp_group_documents_operation" value="<?php echo $template->operation; ?>" />
								<input type="hidden" name="bp_group_documents_id" value="<?php echo $template->id; ?>" />

								<?php if ( $template->operation == 'add' ) { ?>

									<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo return_bytes( ini_get( 'post_max_size' ) ) ?>" />
									<label><?php _e( 'Choose File:', 'bp-group-documents' ); ?></label>
									<div class="form-control type-file-wrapper">
										<input type="file" name="bp_group_documents_file" class="bp-group-documents-file" />
									</div>
								<?php } ?>

								<?php if ( BP_GROUP_DOCUMENTS_FEATURED ) { ?>
									<div class="checkbox">
										<label class="bp-group-documents-featured-label"><input type="checkbox" name="bp_group_documents_featured" class="bp-group-documents-featured" value="1" <?php if ( $template->featured ) { echo 'checked="checked"';} ?> > <?php esc_html_e( 'Featured File', 'openlab-theme' ); ?></label>
									</div>
								<?php } ?>

								<div id="document-detail-clear" class="clear"></div>
								<div class="document-info">
									<label><?php _e( 'Display Name:', 'bp-group-documents' ); ?></label>
									<input type="text" name="bp_group_documents_name" id="bp-group-documents-name" class="form-control" value="<?php echo $template->name ?>" />
									<?php if ( BP_GROUP_DOCUMENTS_SHOW_DESCRIPTIONS ) { ?>
										<label><?php _e( 'Description:', 'bp-group-documents' ); ?></label>
										<textarea name="bp_group_documents_description" id="bp-group-documents-description" class="form-control"><?php echo $template->description; ?></textarea>
									<?php } ?>
									<label></label>
								</div>
							</div>
						</div>

						<input type="submit" class="btn btn-primary btn-margin btn-margin-top" value="<?php _e( 'Submit', 'bp-group-documents' ); ?>" />

						<?php if ( get_option( 'bp_group_documents_use_categories' ) ) { ?>
							<div class="bp-group-documents-category-wrapper">
								<label><?php _e( 'Category:', 'bp-group-documents' ); ?></label>
								<div class="bp-group-documents-category-list">
									<ul class="inline-element-list">
										<?php foreach ( $template->get_group_categories( false ) as $category ) { ?>
											<li><input type="checkbox" name="bp_group_documents_categories[]" value="<?php echo $category->term_id; ?>" <?php if ( $template->doc_in_category( $category->term_id ) ) { echo 'checked="checked"';} ?> /><?php echo $category->name; ?></li>
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
					<?php if ( $template->operation == 'add' ) { ?>
						<a class="btn btn-primary link-btn" id="bp-group-documents-upload-button" href="" style="display:none;"><?php _e( 'Upload a New File', 'openlab-theme' ); ?></a>
					<?php } ?>
				</div>

			<?php } ?>

	</div><!--end #group-documents-->
