<?php
defined('ABSPATH') || exit;

global $product;
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('bt-' . $args['layout'], $product); ?>>
	<?php
	$ajax_add_to_cart_enabled = false;
	if (function_exists('get_field')) {
		$ajax_add_to_cart_enabled = get_field('enable_ajax_add_to_cart_buttons_on_single_product', 'options');
	}
	$bt_product_inner_class = 'bt-product-inner';
	if ($ajax_add_to_cart_enabled && $product && ($product->is_type('simple') || $product->is_type('variable'))) {
		$bt_product_inner_class .= ' bt-add-cart-ajax';
	}
	$parent_image_id = $product->get_image_id();
	$parent_image_src = $parent_image_id ? wp_get_attachment_url($parent_image_id) : '';
	?>
	<div class="<?php echo esc_attr($bt_product_inner_class); ?>" data-parent-image-src="<?php echo esc_url($parent_image_src); ?>">
		<?php
		/**
		 * Hook: woocommerce_before_single_product_summary.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action('woocommerce_before_single_product_summary');
		?>

		<div class="summary entry-summary ">
			<div class="woocommerce-product-rating-sold">
				<?php
				do_action('somnia_woocommerce_shop_loop_item_label');
				do_action('somnia_woocommerce_template_single_rating');
				?>
			</div>
			<?php
			do_action('somnia_woocommerce_template_single_title');
			?>
			<div class="woocommerce-product-price-wrap">
				<?php
				do_action('somnia_woocommerce_template_single_price');
				do_action('somnia_woocommerce_show_product_loop_sale_flash');
				?>
			</div>
			<div class="bt-product-excerpt-add-to-cart">
				<?php
				do_action('somnia_woocommerce_template_single_excerpt');
				do_action('somnia_woocommerce_template_single_countdown');
				do_action('somnia_woocommerce_template_single_add_to_cart');
				do_action('somnia_woocommerce_template_single_more_information');
				?>

			</div>
			<?php do_action('somnia_woocommerce_template_single_meta');
			do_action('somnia_woocommerce_template_frequently_bought_together');
			do_action('somnia_woocommerce_template_upsell_products');
			do_action('somnia_woocommerce_template_single_safe_checkout');
			?>
		</div>
	</div>
	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked somnia_output_product_extra_content - 18
	 * @hooked woocommerce_output_related_products - 20
	 */

	do_action('woocommerce_after_single_product_summary');
	?>


</div>