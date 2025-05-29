<?php

class Quill_Customizer_Control extends WP_Customize_Control {
	public $type = 'quill_editor';

	public function render_content() {
		$editor_id = esc_attr( '_customize-input-' . $this->id );
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div id="<?php echo esc_attr( $editor_id ); ?>-quill" class="quill-editor" style="height: 200px;"></div>
			<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>
		</label>
		<?php
	}
}
