<div class="panel panel-default">
	<div class="panel-heading"><?php esc_html_e( 'Category', 'commons-in-a-box' ); ?></div><div class="panel-body">
		<table>
			<tbody>
				<tr class="school-tooltip">
					<td colspan="2"><p class="ol-tooltip"><?php esc_html_e( 'Please select from the following categories.', 'commons-in-a-box' ); ?></p></td>
				</tr>
				<tr class="bp-categories">
					<td colspan="2" id="bp_group_categories">
						<div class="bp-group-categories-list-container checkbox-list-container">
							<?php foreach ( $categories as $category ) : ?>

								<label class="passive block"><input type="checkbox" value="<?php echo esc_attr( $category->term_id ); ?>" id="group-category-<?php echo esc_attr( $category->term_id ); ?>" name="_group_categories[]" <?php checked( in_array( $category->term_id, $group_term_ids, true ), true, true ); ?>>&nbsp;<?php echo esc_html( $category->name ); ?></label>

							<?php endforeach; ?>
							<?php if ( ! empty( $group_term_ids ) ) : ?>
								<input type="hidden" name="_group_previous_categories" value="<?php echo esc_attr( implode( ',', $group_term_ids ) ); ?>">
							<?php endif; ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
