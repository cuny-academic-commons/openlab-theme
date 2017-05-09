<?php
/**
 * Group loop
 *
 * @todo All the other group templates (my-*.php as well as *-archive.php) should at some point
 *       be refactored to include this file instead. Filter stuff will probably have to be
 *       abstracted
 */
// Set up the group meta filters
global $bp;

// Set up the bp_has_groups() args: per_page, page, search_terms
$group_args = array(
	'per_page' => 12,
);

$group_type_slug = bp_get_current_group_directory_type();
if ( ! $group_type_slug && ! empty( $_GET['group_type'] ) ) {
	$group_type_slug = wp_unslash( urldecode( $_GET['group_type'] ) );
}

$group_type = cboxol_get_group_type( $group_type_slug );

// @todo
if ( ! empty( $search_terms_raw ) ) {
	$group_args['search_terms'] = $search_terms_raw;
}

if ( ! empty( $_GET['group_sequence'] ) ) {
	$group_args['type'] = $_GET['group_sequence'];
}
?>

<?php if ( bp_has_groups( $group_args ) ) : ?>
	<div id="openlab-main-content"></div>

	<div class="row">
		<?php
		if ( openlab_is_my_profile() ) {
			echo openlab_submenu_markup( 'groups', $group_type, false );
		}
		?>

		<div class="group-count col-sm-5 pull-right"><?php cuny_groups_pagination_count(); ?></div>
	</div>

	<div id="group-list" class="item-list group-list row">
		<?php
		$count = 1;
		while ( bp_groups() ) : bp_the_group();
			$group_id = bp_get_group_id(); ?>
			<div class="group-item col-xs-12">
					<div class="group-item-wrapper">
						<div class="row">
				<div class="item-avatar alignleft col-xs-6">
								<a href="<?php bp_group_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar( array( 'item_id' => $group_id, 'object' => 'group', 'type' => 'full', 'html' => false ) ) ?>" alt="<?php echo $group->name; ?>"/></a>
							</div>
				<div class="item col-xs-18">
									<div class="item-content-wrapper">
											<h2 class="item-title">
												<a class="no-deco truncate-on-the-fly hyphenate" href="<?php bp_group_permalink() ?>" data-basevalue="<?php echo ($group_type == 'course' ? 50 : 65 ) ?>" data-minvalue="20" data-basewidth="290"><?php bp_group_name() ?></a>
												<span class="original-copy hidden"><?php bp_group_name() ?></span>
											</h2>
											<?php
											//course group type
											if ( $group_type == 'course' ) :
												?>

												<div class="info-line uppercase">
													<?php echo openlab_output_course_info_line( $group_id ); ?>
												</div>
											<?php elseif ( $group_type == 'portfolio' ) :  ?>

												<div class="info-line"><?php echo bp_core_get_userlink( openlab_get_user_id_from_portfolio_group_id( bp_get_group_id() ) ); ?></div>

											<?php endif; ?>
												<div class="description-line">
													<p class="truncate-on-the-fly" data-link="<?php echo bp_get_group_permalink() ?>" data-basevalue="105" data-basewidth="290"><?php echo bp_get_group_description_excerpt() ?></p>
													<p class="original-copy hidden"><?php echo bp_get_group_description_excerpt() ?></p>
												</div>
									</div>
				</div>

						</div>
					</div>
						</div>
			<?php $count++ ?>
		<?php endwhile; ?>
		</div>
		<script type="text/javascript">
			(function($){
			  $('.item-content-wrapper p').css('opacity','0');
			})(jQuery);
		</script>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php echo openlab_groups_pagination_links() ?>
		</div>
<?php else : ?>
<div class="row">
	  	<?php
		if ( openlab_is_my_profile() ) {
			echo openlab_submenu_markup( 'groups', $group_type, false );
		}
		?>
</div>
	<div class="widget-error">
		<?php _e( 'There are no items to display', 'openlab-theme' ) ?>
	</div>

<?php endif; ?>
