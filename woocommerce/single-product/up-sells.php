<?php

/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.6.0
 */

if (! defined('ABSPATH')) {
	exit;
}

if ($upsells) : ?>

	<section class="up-sells upsells products">
		<?php
		$heading = apply_filters('woocommerce_product_upsells_products_heading', __('You may also like&hellip;', 'somnia'));

		if ($heading) :
		?>
			<h2><?php echo esc_html($heading); ?></h2>
		<?php endif; ?>

		<?php woocommerce_product_loop_start(); ?>

		<?php foreach ($upsells as $upsell) : ?>

			<?php
			$post_object = get_post($upsell->get_id());

			setup_postdata($GLOBALS['post'] = $post_object); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
			global $product;

			if (empty($product) || ! $product->is_visible()) {
				return;
			}
			?>
			<div class="bt-elwg-product-loop-item--style-1 layout-up-sells">
				<div class="bt-product bt-product-item-minimal active">
					<div class="bt-product-thumbnail">
						<a href="<?php echo esc_url(get_permalink()); ?>">
							<?php echo woocommerce_get_product_thumbnail(); ?>
						</a>
					</div>
					<div class="bt-product-content">
						<h3 class="bt-product-title">
							<a href="<?php echo esc_url(get_permalink()); ?>">
								<?php echo get_the_title(); ?>
							</a>
						</h3>
						<div class="bt-product-price">
							<?php 
								$price_html  = $product->get_price_html();
								echo wp_kses_post( $price_html );
							?>
						</div>
						<div class="bt-product-add-to-cart">
							<?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
								<a href="?add-to-cart=<?php echo esc_attr($product->get_id()); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product->get_id()); ?>" data-quantity="1" class="bt-button product_type_simple add_to_cart_button ajax_add_to_cart bt-button-hover" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
							<?php else : ?>
								<a href="<?php echo esc_url($product->get_permalink()); ?>" class="bt-button bt-view-product"><?php echo esc_html__('View Product', 'somnia'); ?></a>
							<?php endif; ?>
						</div>

					</div>
				</div>
			</div>
		<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>

<?php
endif;

wp_reset_postdata();
