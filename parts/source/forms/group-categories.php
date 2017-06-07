<div class="panel panel-default">
    <div class="panel-heading"><?php esc_html_e( 'Category', 'openlab-theme' ); ?></div><div class="panel-body">
		<table>
			<tbody>
				<tr class="school-tooltip">
					<td colspan="2"><p class="ol-tooltip"><?php esc_html_e( 'Please select from the following categories.', 'openlab-theme' ); ?></p></td>
				</tr>
				<tr class="bp-categories">
					<td colspan="2" id="bp_group_categories">
						<div class="bp-group-categories-list-container checkbox-list-container">
							<?php foreach ( $categories as $category ) : ?>

								<label class="passive block"><input type="checkbox" value="<?php echo $category->term_id ?>" name="_group_categories[]" <?php checked( in_array( $category->term_id, $group_term_ids ), true, true ) ?>>&nbsp;<?php echo $category->name ?></label>

							<?php endforeach; ?>
							<?php if ( ! empty( $group_term_ids ) ) :  ?>
								<input type="hidden" name="_group_previous_categories" value="<?php echo implode( ',', $group_term_ids ); ?>">
							<?php endif; ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
