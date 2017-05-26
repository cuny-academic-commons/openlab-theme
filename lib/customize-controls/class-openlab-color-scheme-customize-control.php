<?php

class OpenLab_Color_Scheme_Customize_Control extends WP_Customize_Control {

	protected function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$name = '_customize-radio-' . $this->id;

		if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif;
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description ; ?></span>
		<?php endif;

		foreach ( $this->choices as $value => $label ) :
			?>
			<label class="color-scheme-<?php echo esc_attr( $value ); ?>">
				<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
				<?php echo esc_html( $label ); ?>
				(<?php echo $value ?> circle)
				<br/>
			</label>
			<?php
		endforeach;
	}
}
