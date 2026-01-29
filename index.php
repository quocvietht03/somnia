<?php
get_header();
get_template_part('framework/templates/site', 'titlebar');

?>
<main id="bt_main" class="bt-site-main">
	<div class="bt-main-content-ss">
		<div class="bt-container">
			<div class="bt-main-post-row">
				<div class="bt-main-post-col">
					<?php
						if( have_posts() ) {
							?>
								<div class="bt-list-post">
									<?php
									while (have_posts()) : the_post();
										get_template_part('framework/templates/post', 'index', array('image-size' => 'large'));
									endwhile;
									?>
								</div>
							<?php
							somnia_paging_nav();
						} else {
							get_template_part('framework/templates/post', 'none');
						}
					?>
				</div>
				<div class="bt-sidebar-col">
					<div class="bt-sidebar">
						<?php if(is_active_sidebar('main-sidebar')) echo get_sidebar('main-sidebar'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>


</main><!-- #main -->

<?php get_footer(); ?>