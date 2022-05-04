<?php
/**
* footer template
*
*/ ?>
					</div><!--end .container-->

					<div class="before-footer-content" role="complementary">
						<?php do_action( 'bp_after_container' ); ?>
						<?php do_action( 'bp_before_footer' ); ?>

						<?php do_action( 'bp_footer' ); ?>

						<?php do_action( 'bp_after_footer' ); ?>
					</div>

					</div><!--.page-table-row-->

					<?php openlab_site_footer(); ?>

					<?php wp_footer(); ?>

				</div><!--.page-table-->

	</body>

</html>
