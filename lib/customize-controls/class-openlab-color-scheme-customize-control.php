<?php

class OpenLab_Color_Scheme_Customize_Control extends WP_Customize_Control {

	protected function render_content() {
		$color_schemes = openlab_color_schemes();

		$name = '_customize-radio-' . $this->id;

		foreach ( $color_schemes as $color_scheme => $scheme_data ) :
			?>
			<label class="color-scheme-option color-scheme-<?php echo esc_attr( $color_scheme ); ?>">
				<input type="radio" value="<?php echo esc_attr( $color_scheme ); ?>" name="color-scheme-option-<?php echo esc_attr( $color_scheme ); ?>" <?php $this->link(); checked( $this->value(), $color_scheme ); ?> />
				<?php echo esc_html( $scheme_data['label'] ); ?>

				<span class="color-scheme-icon" style="background-color: <?php echo esc_attr( $scheme_data['icon_color'] ); ?>"></span>
			</label>
			<?php
		endforeach;

		?>
		<style type="text/css">
		.color-scheme-option {
			display: block;
			margin-bottom: 8px;
		}

		.color-scheme-icon {
			border: 1px solid #666;
			border-radius: 50%;
			display: inline-block;
			margin-bottom: -6px;
			height: 20px;
			width: 20px;
		}
		</style>
		<?php
	}
}
