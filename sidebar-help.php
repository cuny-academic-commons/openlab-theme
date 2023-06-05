<?php /**
 * The Help Sidebar
 *
 */ ?>

<h2 class="sidebar-title hidden-xs">Help</h2>
<div id="sidebar-menu-wrapper" class="sidebar-menu-wrapper">
<div class="sidebar-block clearfix">
<?php
$args = array(
	'theme_location' => 'helpmenu',
	'container'      => 'div',
	'container_id'   => 'help-menu',
	'menu_class'     => 'sidebar-nav',
);
wp_nav_menu( $args );
?>
</div>
</div>
<div class="sidebar-widget-wrapper">
<h2 class="sidebar-help-title help-search-title">Search Help</h2>
<div class="sidebar-block padded-block">
<div id="help-search-copy"><p>Find answers throughout Help that correspond to your search terms:</p></div>
<div id="help-search">
	<form method="get" action="<?php echo esc_url( openlab_get_help_search_url() ); ?>">
			<label class="sr-only" for="helpSearch">Help Search</label>
		<input type="text" name="help-search" class="help-search" id="helpSearch" />
			<button class="btn btn-default btn-block btn-primary" type="submit">Search<span class="sr-only"> Help</span></button>
	</form>
	<div class="clearfloat"></div>
</div>
</div>

<h2 class="sidebar-help-title support-team-title">Our Support Team</h2>
<div class="sidebar-block padded-block">
<div id="support-team">
	<div class="help-tags-copy"><p>The Support Team is here to answer all your OpenLab questions.</p></div>
	<?php
	$args     = array(
		'name'        => 'contact-us',
		'post_type'   => 'help',
		'post_status' => 'publish',
		'numberposts' => 1,
	);
	$my_posts = get_posts( $args );

	if ( $my_posts ) {
		$the_post_id = $my_posts[0]->ID;
		$args        = array(
			'post_type'   => 'attachment',
			'numberposts' => -1,
			'post_status' => 'any',
			'post_parent' => $the_post_id,
			'orderby'     => 'name',
			'order'       => 'ASC',
		);
		$attachments = get_posts( $args );
	} else {
		$attachments = false;
	}

	$contact_us     = cboxol_get_brand_page( 'contact-us' );
	$contact_us_url = isset( $contact_us['preview_url'] ) ? $contact_us['preview_url'] : '';

	if ( $attachments ) {
		$i = 0;

		echo '<div id="team-thumbs" class="row">';
		foreach ( $attachments as $attachment ) {
			$thumb_class = 'col-sm-12 col-xs-12 thumb-wrapper';

			echo '<div class="' . esc_attr( $thumb_class ) . '">';
			echo '<div class="team-thumb">';
			//use WordPress native thumbnail size for hard crop, then resize to fit container requirements
			$src = wp_get_attachment_image_src( $attachment->ID, 'full' );

			if ( $contact_us_url ) {
				echo '<a href="' . esc_url( $contact_us_url ) . '">';
				echo '<img alt="' . esc_html__( 'Contact Us', 'commons-in-a-box' ) . '" class="img-responsive" src="' . esc_attr( $src[0] ) . '" />';
				echo '</a>';
			}
			echo '</div>';

			echo '<div class="team-name">';
			if ( $contact_us_url ) {
				echo '<a href="' . esc_url( $contact_us_url ) . '">';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $attachment->post_excerpt;
				echo '</a>';
			}
			echo '</div>';
			echo '</div>';
			++$i;
		}//end for each
		echo '</div>';
	} //end if
	?>
	<a class="btn btn-default btn-block btn-primary link-btn" href="<?php echo esc_url( $contact_us_url ); ?>"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> <?php esc_html_e( 'Contact Us', 'commons-in-a-box' ); ?></a>
</div><!--support team-->
</div>

<h2 class="sidebar-help-title help-tags-title"><?php esc_html_e( 'Find a Help Topic With Tags', 'commons-in-a-box' ); ?></h2>
<div class="sidebar-block padded-block">
<div class="help-tags-copy"><p><?php esc_html_e( 'Find answers throughout Help that correspond to the tags below:', 'commons-in-a-box' ); ?></p></div>
<div id="help-tags">
	<?php
	$args = array(
		'orderby'      => 'name',
		'order'        => 'ASC',
		'hierarchical' => false,
	);


	$terms = get_terms( array( 'help_tags' ), $args );

	$count = count( $terms );
	if ( $count > 0 ) {
		foreach ( $terms as $the_term ) {
			echo '<a href="' . esc_attr( get_term_link( $the_term ) ) . '" class="btn btn-default btn-primary link-btn tag-btn tag-count-' . esc_attr( $the_term->count ) . '">' . esc_html( $the_term->name ) . '</a> ';
		}
	}
	?>
	<div class="clearfloat"></div>
</div>
</div>

<h2 class="sidebar-title"><?php esc_html_e( 'Creative Commons', 'commons-in-a-box' ); ?></h2>
<div class="sidebar-block padded-block">
<p><?php esc_html_e( 'Our help is licensed under Creative Commons copyright.', 'commons-in-a-box' ); ?><br />
<span class="italics cc-gloss">Attribution &mdash; NonCommercial &mdash; ShareAlike</span></p>
<div id="creative-commons">
	<a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank">Creative Commons</a>
</div>
</div>
</div>
