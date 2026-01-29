<?php
get_header();
get_template_part( 'framework/templates/site', 'titlebar');

?>
<main id="bt_main" class="bt-site-main">
	<div class="bt-main-content-ss">
		<div class="bt-container">
			<!-- Start Content -->
			<div class="bt-page">
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="bt-page--content">
						<?php
							the_content();
							wp_link_pages(array(
								'before' => '<div class="page-links">' . esc_html__('Pages:', 'somnia'),
								'after' => '</div>',
							));
						?>
					</div>
					<?php if ( comments_open() || get_comments_number() ) comments_template(); ?>

				<?php endwhile; ?>
			</div>
			<!-- End Content -->
		</div>
	</div>

</main><!-- #main -->

<?php get_footer(); ?>
