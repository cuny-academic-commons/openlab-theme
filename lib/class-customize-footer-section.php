<?php

/**
 * Footer section control for the Customizer.
 */
class OpenLab_Footer_Section_Control extends WP_Customize_Control {
	public $type = 'openlab_footer_section';

	public function render_content() {
		$content = $this->value();
		$editor_id = $this->id;
		$settings = array(
			'textarea_name' => $this->id,
			'media_buttons' => false,
			'drag_drop_upload' => false,
			'teeny' => true,
			'quicktags' => true,
			'textarea_rows' => 15,
		);

		?>

		<p>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

				<?php wp_editor( $content, $editor_id, $settings ); ?>

				<input type="hidden" <?php echo $this->get_link(); ?> value="<?php echo esc_attr( $content ); ?>" />
			</label>
		</p>

		<?php
	}
}
