<?php

/**
 * Block Name: Widget - Recent Posts
 **/

$number_posts = get_field('number_posts');

$recent_posts = wp_get_recent_posts(array(
	'numberposts' => $number_posts,
	'post_status' => 'publish'
));

?>
<div id="<?php echo 'bt_block--' . $block['id']; ?>" class="widget widget-block bt-block-recent-posts">
	<?php foreach ($recent_posts as $post_item) {
		$category = get_the_terms($post_item['ID'], 'category');
	?>
		<div class="bt-post">
			<a href="<?php echo get_permalink($post_item['ID']) ?>" class="bt-post--thumbnail">
				<div class="bt-cover-image">
					<?php echo get_the_post_thumbnail($post_item['ID'], 'thumbnail'); ?>
				</div>
			</a>
			<div class="bt-post--infor">
				<div class="bt-post--meta">
					<div class="bt-post--publish">
						<?php echo get_the_date(get_option('date_format'), $post_item['ID']); ?>
					</div>
					<?php if (!empty($category) && is_array($category)) {
						$first_category = reset($category); ?>
						<div class="bt-post--category">
							<a href="<?php echo esc_url(get_category_link($first_category->term_id)); ?>">
								<?php echo esc_html($first_category->name); ?>
							</a>
						</div>
					<?php } ?>
				</div>
				<h3 class="bt-post--title">
					<a href="<?php echo get_permalink($post_item['ID']) ?>">
						<?php echo esc_html($post_item['post_title']); ?>
					</a>
				</h3>
			</div>
		</div>
	<?php } ?>
</div>