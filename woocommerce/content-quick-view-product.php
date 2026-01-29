<?php

/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('bt-bottom-thumbnail', $product); ?>>
	<?php
	$ajax_add_to_cart_enabled = false;
	if (function_exists('get_field')) {
		$ajax_add_to_cart_enabled = get_field('enable_ajax_add_to_cart_buttons_on_single_product', 'options');
	}
	$bt_product_inner_class = 'bt-product-inner bt-quickview-product';
	if ($ajax_add_to_cart_enabled && $product && ($product->is_type('simple') || $product->is_type('variable'))) {
		$bt_product_inner_class .= ' bt-add-cart-ajax';
	}
	?>
	<div class="<?php echo esc_attr($bt_product_inner_class); ?>">
		<?php
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
			$product_title = $product->get_title();
			$product_link = get_permalink($product->get_id());
			echo '<h2 class="product_title entry-title"><a href="' . esc_url($product_link) . '">' . esc_html($product_title) . '</a></h2>';
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
				do_action('somnia_woocommerce_template_single_add_to_cart');
				do_action('somnia_woocommerce_template_single_meta');
				?>
			</div>
		</div>
	</div>
</div>