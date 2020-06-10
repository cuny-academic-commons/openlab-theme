<?php require apply_filters( 'bp_docs_header_template', bp_docs_locate_template( 'docs-header.php' ) ); ?>

<?php bp_docs_inline_toggle_js(); ?>

<?php if ( bp_docs_has_docs() ) : ?>

<div class="docs-info-header img-rounded">
	<div class="row">
		<div class="col-sm-24">
			<div class="doc-search-element pull-right align-right">
				<form action="" method="get" class="form-inline">
					<div class="form-group">
						<input class="form-control" name="s" value="<?php the_search_query(); ?>">
						<input class="btn btn-primary top-align" name="search_submit" type="submit" value="<?php esc_html_e( 'Search', 'bp-docs' ); ?>" />
					</div>
				</form>
			</div>
			<?php bp_docs_info_header(); ?>
		</div>
	</div>
</div>

<div class="info-panel panel panel-default doctable-panel">
	<table class="doctable table table-striped">

		<thead>
			<tr valign="bottom">
				<th scope="column" class="title-cell<?php bp_docs_is_current_orderby_class( 'title' ); ?>">
					<a href="<?php bp_docs_order_by_link( 'title' ); ?>"><?php esc_html_e( 'Title', 'bp-docs' ); ?></a>
				</th>

				<th scope="column" class="author-cell<?php bp_docs_is_current_orderby_class( 'author' ); ?> hidden-xs">
					<a href="<?php bp_docs_order_by_link( 'author' ); ?>"><?php esc_html_e( 'Author', 'bp-docs' ); ?></a>
				</th>

				<th scope="column" class="created-date-cell<?php bp_docs_is_current_orderby_class( 'created' ); ?> hidden-sm hidden-xs">
					<a href="<?php bp_docs_order_by_link( 'created' ); ?>"><?php esc_html_e( 'Created', 'bp-docs' ); ?></a>
				</th>

				<th scope="column" class="edited-date-cell<?php bp_docs_is_current_orderby_class( 'modified' ); ?> hidden-sm hidden-xs">
					<a href="<?php bp_docs_order_by_link( 'modified' ); ?>"><?php esc_html_e( 'Last Edited', 'bp-docs' ); ?></a>
				</th>

				<th scope="column" class="tags-cell hidden-sm hidden-xs"><?php esc_html_e( 'Tags', 'bp-docs' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			while ( bp_docs_has_docs() ) :
				bp_docs_the_doc();
				?>
				<tr>
					<td class="title-cell">
						<span class="title-wrapper">
							<a class="hyphenate truncate-on-the-fly" href="<?php bp_docs_group_doc_permalink(); ?>" data-basevalue="80" data-minvalue="55" data-basewidth="376"><?php the_title(); ?></a>
							<span class="original-copy hidden"><?php the_title(); ?></span>
						</span>

						<span class="hyphenate">
							<?php the_excerpt(); ?>
						</span>

						<div class="row-actions">
							<?php bp_docs_doc_action_links(); ?>
						</div>

						<div class="author-info-mobile visible-xs">
							<span class="bold"><?php esc_html_e( 'Author', 'commons-in-a-box' ); ?>:</span> <a href="<?php bp_docs_order_by_link( 'author' ); ?>"><?php esc_html_e( 'Author', 'commons-in-a-box' ); ?></a>
						</div>
					</td>

					<td class="author-cell hidden-xs">
						<a href="<?php echo esc_attr( bp_core_get_user_domain( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo esc_attr( bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo esc_html( bp_core_get_user_displayname( get_the_author_meta( 'ID' ) ) ); ?></a>
					</td>

					<td class="date-cell created-date-cell hidden-sm hidden-xs">
						<?php echo get_the_date(); ?>
					</td>

					<td class="date-cell edited-date-cell hidden-sm hidden-xs">
						<?php echo esc_html( get_the_modified_date() ); ?>
					</td>

					<?php
					$doc_tags = wp_get_post_terms( get_the_ID(), buddypress()->bp_docs->docs_tag_tax_name );
					$tagtext  = array();

					foreach ( $doc_tags as $doc_tag ) {
						$tagtext[] = bp_docs_get_tag_link( array( 'tag' => $doc_tag->name ) );
					}
					?>

					<td class="tags-cell hidden-sm hidden-xs">
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo implode( ', ', $tagtext ); ?>
					</td>

				</tr>
			<?php endwhile ?>
		</tbody>

	</table>
</div>

	<div id="bp-docs-pagination">
		<div id="bp-docs-pagination-count">
			<?php /* translators: 1. Pagination start number, 2. Pagination end number, 3. Total pagination number */ ?>
			<?php printf( esc_html__( 'Viewing %1$s-%2$s of %3$s docs', 'commons-in-a-box' ), esc_html( bp_docs_get_current_docs_start() ), esc_html( bp_docs_get_current_docs_end() ), esc_html( bp_docs_get_total_docs_num() ) ); ?>
		</div>

		<div id="bp-docs-paginate-links">
			<?php bp_docs_paginate_links(); ?>
		</div>
	</div>

<?php else : ?>
	<?php if ( groups_is_user_member( get_current_user_id(), bp_get_current_group_id() ) ) : ?>
		<?php /* translators: link to the Doc creation page */ ?>
		<p class="no-docs bold"><?php printf( esc_html__( 'There are no docs to view. Why not <a href="%s">create one</a>?', 'commons-in-a-box' ), esc_attr( bp_docs_get_item_docs_link() . BP_DOCS_CREATE_SLUG ) ); ?></p>
	<?php else : ?>
		<p class="no-docs bold"><?php esc_html_e( 'There are no docs to view.', 'commons-in-a-box' ); ?></p>
	<?php endif; ?>

<?php endif ?>
