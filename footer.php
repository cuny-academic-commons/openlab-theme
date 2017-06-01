<?php
/**
* footer template
*
*/ ?>
                    </div><!--end .container-->

                    <?php do_action( 'bp_after_container' ) ?>
                    <?php do_action( 'bp_before_footer' ) ?>

                    <?php do_action( 'bp_footer' ) ?>

                    <?php do_action( 'bp_after_footer' ) ?>

                    </div><!--.page-table-row-->

					<div id="openlab-footer" class="oplb-bs page-table-row">
						<div class="footer-wrapper">
							<div class="container-fluid footer-desktop">
								<div class="row row-footer">
									<?php dynamic_sidebar( 'footer' ); ?>
								</div>
							</div>
						</div>
					</div>

                    <?php wp_footer(); ?>

                </div><!--.page-table-->

	</body>

</html>
