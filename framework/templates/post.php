<article <?php post_class('bt-post'); ?>>
	<div class="bt-post--infor">
		<?php
		echo somnia_post_category_render();
		if (is_single()) {
			echo somnia_single_post_title_render();
		} else {
			echo somnia_post_title_render();
		}
		echo somnia_post_meta_single_render();
		?>
	</div>
	<?php
	$layout = isset($args['layout']) ? $args['layout'] : 'layout-default';
	if ($layout == 'layout-default') {
		echo somnia_post_featured_render();
	}
	echo somnia_post_content_render();
	?>
</article>