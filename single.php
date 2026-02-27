<?php
/**
 * Single Post Template
 */

get_header();

// Get layout and banner settings
$layout = 'layout-default';
$banner = '';

if (function_exists('get_field')) {
	$banner = get_field('banner_post', get_the_ID()) ?: '';
	$layout = get_field('layout_post', get_the_ID()) ?: 'layout-default';
}

?>

<main id="bt_main" class="bt-site-main <?php echo esc_attr($layout); ?>">
	<?php if (did_action('elementor/loaded') && \Elementor\Plugin::$instance->preview->is_preview_mode()): ?>
		<?php while (have_posts()): the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile; ?>
	<?php else: ?>
		
		<?php if ($layout == 'layout-01'): ?>
			<!-- Layout 01: Full width image -->
			<div class="bt-single-post-breadcrumb">
				<div class="bt-container">
					<div class="bt-row-breadcrumb-single-post">
						<div class="bt-breadcrumb">
							<?php echo somnia_page_breadcrumb('Home', '/'); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="bt-main-image-full">
				<?php if (!empty($banner)): ?>
					<div class="bt-post--featured">
						<div class="bt-cover-image">
							<?php echo wp_get_attachment_image($banner['id'], 'full'); ?>
						</div>
					</div>
				<?php else: ?>
					<?php echo somnia_post_featured_render('full'); ?>
				<?php endif; ?>
			</div>
			
			<div class="bt-container-single">
				<?php while (have_posts()): the_post(); ?>
					<div class="bt-main-post">
						<?php get_template_part('framework/templates/post', null, array('layout' => $layout)); ?>
					</div>
					<div class="bt-main-actions">
						<?php 
						echo somnia_tags_render();
						echo somnia_share_render();
						?>
					</div>
					<?php 
					somnia_post_nav();
					if (comments_open() || get_comments_number()) comments_template();
					?>
				<?php endwhile; ?>
			</div>
			
		<?php elseif ($layout == 'layout-02'): ?>
			<!-- Layout 02: Image with overlay info -->
			<div class="bt-main-image-full">
				<?php if (!empty($banner)): 
					?>
					<div class="bt-post--featured">
						<div class="bt-cover-image">
							<?php echo wp_get_attachment_image($banner['id'], 'full'); ?>
						</div>
					</div>
				<?php else: ?>
					<?php echo somnia_post_featured_render('full'); ?>
				<?php endif; ?>
				
				<div class="bt-single-information">
					<div class="bt-container">
						<div class="bt-row-breadcrumb-single-post">
							<div class="bt-breadcrumb">
								<?php echo somnia_page_breadcrumb('Home', '/'); ?>
							</div>
						</div>
						<?php 
						echo somnia_single_post_title_render();
						echo somnia_post_meta_single_render();
						?>
					</div>
				</div>
			</div>
			
			<div class="bt-main-content-ss bt-main-content-sidebar">
				<div class="bt-container">
					<div class="bt-main-post-row">
						<div class="bt-main-post-col">
							<?php while (have_posts()): the_post(); ?>
								<div class="bt-main-post bt-post-sidebar">
									<?php get_template_part('framework/templates/post', null, array('layout' => $layout)); ?>
								</div>
								<div class="bt-main-actions">
									<?php 
									echo somnia_tags_render();
									echo somnia_share_render();
									?>
								</div>
								<?php 
								somnia_post_nav();
								if (comments_open() || get_comments_number()) comments_template();
								?>
							<?php endwhile; ?>
						</div>
						<div class="bt-sidebar-col">
							<div class="bt-sidebar">
								<?php if (is_active_sidebar('main-sidebar')) echo get_sidebar('main-sidebar'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		<?php else: ?>
			<!-- Default layout: Breadcrumb + Sidebar -->
			<div class="bt-single-post-breadcrumb">
				<div class="bt-container">
					<div class="bt-row-breadcrumb-single-post">
						<div class="bt-breadcrumb">
							<?php echo somnia_page_breadcrumb('Home', '/'); ?>
						</div>
					</div>
				</div>
			</div>
			
			<div class="bt-main-content-ss bt-main-content-sidebar">
				<div class="bt-container">
					<div class="bt-main-post-row">
						<div class="bt-main-post-col">
							<?php while (have_posts()): the_post(); ?>
								<div class="bt-main-post bt-post-sidebar">
									<?php get_template_part('framework/templates/post', null, array('layout' => $layout)); ?>
								</div>
								<div class="bt-main-actions">
									<?php 
									echo somnia_tags_render();
									echo somnia_share_render();
									?>
								</div>
								<?php 
								somnia_post_nav();
								if (comments_open() || get_comments_number()) comments_template();
								?>
							<?php endwhile; ?>
						</div>
						<div class="bt-sidebar-col">
							<div class="bt-sidebar">
								<?php if (is_active_sidebar('main-sidebar')) echo get_sidebar('main-sidebar'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		
		<?php echo somnia_related_posts(); ?>
	<?php endif; ?>
</main>

<?php get_footer(); ?>