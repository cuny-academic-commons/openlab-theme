<?php

/**
 * Footer section control for the Customizer.
 */
class OpenLab_Footer_Section_Control extends WP_Customize_Control {
	public $type = 'openlab_footer_section';

	public function render_content() {
		?>

		<div class="tinymce-control">
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<textarea id="<?php echo esc_attr( $this->id ); ?>" class="customize-control-tinymce-editor" <?php $this->link(); ?>><?php echo esc_html( $this->value() ); ?></textarea>
		</div>

		<?php
	}
}
