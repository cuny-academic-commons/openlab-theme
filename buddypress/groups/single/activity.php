
<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<li class="feed"><a href="<?php bp_group_activity_feed_link(); ?>" title="<?php esc_attr_e( 'RSS Feed', 'commons-in-a-box' ); ?>"><?php esc_html_e( 'RSS', 'commons-in-a-box' ); ?></a></li>

		<?php do_action( 'bp_group_activity_syndication_options' ); ?>

		<li id="activity-filter-select" class="last">
			<select>
				<option value="-1"><?php esc_html_e( 'No Filter', 'commons-in-a-box' ); ?></option>
				<option value="activity_update"><?php esc_html_e( 'Show Updates', 'commons-in-a-box' ); ?></option>

				<option value="joined_group"><?php esc_html_e( 'Show New Group Memberships', 'commons-in-a-box' ); ?></option>

				<?php do_action( 'bp_group_activity_filter_options' ); ?>
			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php do_action( 'bp_before_group_activity_post_form' ); ?>

<?php if ( is_user_logged_in() && bp_group_is_member() ) : ?>
	<?php bp_get_template_part( 'activity/post-form.php' ); ?>
<?php endif; ?>

<?php do_action( 'bp_after_group_activity_post_form' ); ?>
<?php do_action( 'bp_before_group_activity_content' ); ?>

<div class="activity single-group">
	<?php bp_get_template_part( 'activity/activity-loop.php' ); ?>
</div><!-- .activity.single-group -->

<?php do_action( 'bp_after_group_activity_content' ); ?>
