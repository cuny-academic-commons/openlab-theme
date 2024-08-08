<?php

/**
 * Markup for the Forum nav tabs.
 *
 * @since 1.6.0 Moved to this file from openlab_forum_tabs().
 */

$group = groups_get_current_group();

// Load up bbPress once
$bbp = bbpress();

/** Query Resets ***************************************************** */
// Forum data
$forum_ids = bbp_get_group_forum_ids( bp_get_current_group_id() );
$forum_id  = array_shift( $forum_ids );
$offset    = 0;

$bbp->current_forum_id = $forum_id;

bbp_set_query_name( 'bbp_single_forum' );

// Get the topic
bbp_has_topics(
	array(
		'name'           => bp_action_variable( $offset + 1 ),
		'posts_per_page' => 1,
		'show_stickies'  => false,
	)
);

// Setup the topic
while ( bbp_topics() ) {
	bbp_the_topic();
	$topic_title = bbp_get_topic_title();
}

$group_forum_permalink = bp_get_group_url( $group->id, bp_groups_get_path_chunks( [ 'forum' ] ) );

?>

<li <?php echo ( ! bp_action_variable() ? 'class="current-menu-item"' : '' ); ?> ><a href="<?php echo esc_attr( $group_forum_permalink ); ?>"><?php esc_html_e( 'Discussion', 'commons-in-a-box' ); ?></a></li><!--
<?php if ( bp_is_action_variable( 'topic' ) ) : ?>
	--><li class="current-menu-item hyphenate"><span><?php echo esc_html( $topic_title ); ?></span></li><!--
		<?php endif; ?>
-->
<?php
