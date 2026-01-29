<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( $related_products || !empty($recently_viewed_products) ) :
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}
	if(function_exists('get_field')){
		$related_posts = get_field('product_related_posts', 'options');
	} else {
		$related_posts = array(
			'heading' => __( 'Related Products', 'somnia' ),
		);
	}

?>
	<section class="related products">
		<div class="bt-related-tab-heading">
			<div class="bt-tab-nav">
				<?php if ($related_products && !empty($related_posts['heading'])) : ?>
					<h2 class="bt-main-text bt-tab-title active related" data-tab="related"><?php echo esc_html($related_posts['heading']); ?></h2>
				<?php endif; ?>
				<h2 class="bt-main-text bt-tab-title recently-viewed" data-tab="recently-viewed"><?php echo esc_html__('Recently Viewed', 'somnia'); ?></h2>
			</div>
		</div>

		<div class="bt-tab-content">
			<?php if ($related_products) : ?>
			<div class="bt-tab-pane<?php echo ' active'; ?>" data-tab-content="related">
				<?php woocommerce_product_loop_start(); ?>
					<?php foreach ($related_products as $related_product) : ?>
						<?php
						$post_object = get_post($related_product->get_id());
						setup_postdata($GLOBALS['post'] =& $post_object);
						wc_get_template_part('content', 'product');
						?>
					<?php endforeach; ?>
				<?php woocommerce_product_loop_end(); ?>
			</div>
			<?php endif; ?>

			<div class="bt-tab-pane<?php echo empty($related_products) ? ' active' : ''; ?>" data-tab-content="recently-viewed">
				<div class="recently-viewed-products">
					<?php if (!empty($recently_viewed_products)) : ?>
						<?php woocommerce_product_loop_start(); ?>
						<?php foreach ($recently_viewed_products as $recent_product) : ?>
							<?php
							$post_object = get_post($recent_product->get_id());
							setup_postdata($GLOBALS['post'] =& $post_object);
							wc_get_template_part('content', 'product');
							?>
						<?php endforeach; ?>
						<?php woocommerce_product_loop_end(); ?>
					<?php else : ?>
						<p class="no-products"><?php esc_html_e('No recently viewed products.', 'somnia'); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
endif;

wp_reset_postdata();
