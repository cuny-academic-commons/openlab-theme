<?php

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
			'quicktags' => false,
			'textarea_rows' => 5,
		);

		?>

		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

			<?php wp_editor( $content, $editor_id, $settings ); ?>
		</label>

		<?php
	}
	private function filter_editor_setting_link() {
//        add_filter( 'the_editor', function( $output ) { return preg_replace( '/<textarea/', '<textarea ' . $this->get_link(), $output, 1 ); } );
    }
}
