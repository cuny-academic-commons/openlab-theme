<?php if ( 'comments.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	return;} ?>
<section id="comments">
<?php
if ( have_comments() ) :
	global $comments_by_type;
	$comments_by_type = &separate_comments( $comments );
	if ( ! empty( $comments_by_type['comment'] ) ) :
		?>
<section id="comments-list" class="comments">
<h2 class="comments-title"><?php comments_number(); ?></h3>
		<?php if ( get_comment_pages_count() > 1 ) : ?>
<nav id="comments-nav-above" class="comments-navigation" role="navigation">
<div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
</nav>
<?php endif; ?>
<ul>
		<?php wp_list_comments( 'type=comment' ); ?>
</ul>
		<?php if ( get_comment_pages_count() > 1 ) : ?>
<nav id="comments-nav-below" class="comments-navigation" role="navigation">
<div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
</nav>
<?php endif; ?>
</section>
		<?php
endif;
	if ( ! empty( $comments_by_type['pings'] ) ) :
		$ping_count = count( $comments_by_type['pings'] );
		?>
<section id="trackbacks-list" class="comments">

	<h3 class="comments-title">
		<?php
		echo esc_html(
			sprintf(
				// translators: Trackback count
				_n( '%s Trackback', '%s Trackbacks', $ping_count, 'commons-in-a-box' ),
				number_format_i18n( $ping_count )
			)
		);
		?>
	</h3>

<ul>
		<?php wp_list_comments( 'type=pings&callback=blankslate_custom_pings' ); ?>
</ul>
</section>
		<?php
endif;
endif;
if ( comments_open() ) {
	comment_form(
		array(
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
		)
	);
}
?>
</section>
