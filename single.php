<?php get_header(); ?>

<div id="content" class="hfeed row">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>

		<div <?php post_class(); ?>>
			<div id="openlab-main-content"  class="content-wrapper">
				<div class="entry-title">
					<h1><span class="profile-name"><?php the_title(); ?></span></h1>
				</div>
				<div class="entry-meta">
					<?php
					$author_id = get_the_author_meta( 'ID' );
					printf(
						/* translators: 1. post date, 2. author link */
						esc_html__( 'Posted on %1$s by %2$s', 'commons-in-a-box' ),
						esc_html( get_the_date( 'F j, Y' ) ),
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						bp_core_get_userlink( get_the_author_meta( 'ID' ) )
					);
					?>
				</div>

				<div class="entry-content"><?php the_content(); ?></div>
			</div>
		</div><!--hentry-->

			<?php
			if ( ! post_password_required() ) {
				comments_template( '', true );
			}

		endwhile;
	endif;
	?>

</div><!--#content-->

<?php get_footer(); ?>
