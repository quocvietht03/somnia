<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

global $product, $is_ajax_filter_product;
if (empty($product) || ! $product->is_visible()) {
	return;
}
?>
<div <?php wc_product_class('woocommerce-loop-product', $product); ?>>
	<div class="woocommerce-loop-product__thumbnail">
		<?php
		do_action('somnia_woocommerce_template_loop_product_link_open');
		do_action('somnia_woocommerce_template_loop_product_thumbnail');
		do_action('somnia_woocommerce_template_loop_product_link_close');
		echo '<div class="woocommerce-product-sale-label">';
		do_action('somnia_woocommerce_show_product_loop_sale_flash');
		do_action('somnia_woocommerce_shop_loop_item_label');
		echo '</div>';
		if (!$product->is_type('variable')) {
			echo wc_get_stock_html($product); // WPCS: XSS ok. 
		}
		?>

	

		<?php
		do_action('somnia_woocommerce_template_loop_list_cta_button');
		do_action('somnia_template_loop_product_countdown_and_sale'); 
		?>

	</div>

	<div class="woocommerce-loop-product__infor">
		<?php
		do_action('somnia_woocommerce_template_loop_rating');
		do_action('somnia_woocommerce_template_loop_product_link_open');
		do_action('somnia_woocommerce_template_loop_product_title');
		do_action('somnia_woocommerce_template_loop_product_link_close');
		// Display default attributes as "attribute_label : value" only for variable products with default attributes
		if (isset($attributes_default) && !empty($attributes_default) && $product->is_type('variable')) {
			echo '<div class="product-default-attributes">';
			$attribute_pairs = array();
			foreach ($attributes_default as $attribute_name => $attribute_value) {
				// Get attribute label/name
				$attribute_label = wc_attribute_label($attribute_name);
				$attribute_pairs[] = esc_html($attribute_label) . ': ' . '<span>' . esc_html($attribute_value) . '</span>';
			}
			echo implode(' / ', $attribute_pairs);
			echo '</div>';
		}
		do_action('somnia_woocommerce_template_loop_price');
		?>
		<?php if (is_archive() && 'product' === get_post_type() || $is_ajax_filter_product) : ?>
			<?php if ($short_description = $product->get_short_description()) : ?>
				<div class="bt-product-short-description">
					<?php echo wp_kses_post($short_description); ?>
				</div>
			<?php endif; ?>
			
		<?php endif; ?>
		<?php do_action('somnia_woocommerce_template_loop_list_cta_button'); ?>
	</div>
</div>