<?php
/**
 * This file is responsible for displaying a featured blog on the front end of
 * the site. It is required by the core view template whenever the
 * featured content type has been set to 'blog'
 *
 * @author Dominic Giglio
 *
 */
?>

<?php
// echo out the widget title using the element selected in the widget admin
// phpcs:disable
echo "<{$cfcw_view->title_element} class='widgettitle'>";
echo esc_html( $cfcw_view->title );
echo "</{$cfcw_view->title_element}>";
// phpcs:enable
?>

<div class="cfcw-content">

	<div class="row">

		<?php if ( $cfcw_view->display_images && $cfcw_view->image_url ) : ?>


			<div class="col-xs-12 cfcw-image-wrapper">
				<a href="<?php echo esc_url( $cfcw_view->resource_link ); ?>">
					<?php // translators: featured resource title ?>
					<img src="<?php echo esc_attr( $cfcw_view->image_url ); ?>" alt="<?php echo esc_attr( sprintf( __( 'In the Spotlight: %s', 'commons-in-a-box' ), $cfcw_view->resource_title ) ); ?>" class="img-responsive" />
				</a>
			</div>

			<div class="col-xs-12 cfcw-copy-wrapper">
				<p class="cfcw-title">
					<a href="<?php echo esc_url( $cfcw_view->resource_link ); ?>"><?php echo esc_html( $cfcw_view->resource_title ); ?></a>
				</p>

				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p><?php echo bp_create_excerpt( $cfcw_view->description, $cfcw_view->crop_length ); ?></p>
				<p class="see-more"><a class="semibold" href="<?php echo esc_url( $cfcw_view->resource_link ); ?>"><?php echo esc_html_e( 'See More', 'cac-featured-content' ); ?><span class="sr-only"> <?php echo esc_html_e( 'about this In the Spotlight', 'cac-featured-content' ); ?></span></a></p>
			</div>

		<?php else : ?>

			<div class="col-md-24 cfcw-copy-wrapper">
				<p class="cfcw-title">
					<a href="<?php echo esc_url( $cfcw_view->resource_link ); ?>"><?php echo esc_html( $cfcw_view->resource_title ); ?></a>
				</p>

				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p><?php echo bp_create_excerpt( $cfcw_view->description, $cfcw_view->crop_length ); ?></p>
				<p class="see-more"><a class="semibold" href="<?php echo esc_url( $cfcw_view->resource_link ); ?>"><?php esc_html_e( 'See More', 'cac-featured-content' ); ?><span class="sr-only"> <?php esc_html_e( 'about this In the Spotlight', 'cac-featured-content' ); ?></span></a></p>
			</div>

		<?php endif; ?>



	</div>

</div>
