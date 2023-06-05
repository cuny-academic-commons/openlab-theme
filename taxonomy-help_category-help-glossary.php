<?php
/**
 *Glossary page
 *
 */

get_header(); ?>
	<div id="content" class="hfeed">
			<div class="col-sm-18 col-xs-24">
				<div id="openlab-main-content" class="content-wrapper">
					<?php openlab_glossary_cats_loop(); ?>
				</div>
			</div>
			<?php get_template_part( 'parts/sidebar/help' ); ?>
	</div>
<?php
get_footer();
