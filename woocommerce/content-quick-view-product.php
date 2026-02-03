<?php

/**
 * The template for displaying product content in the quick view popup
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('bt-quickview-slider', $product); ?>>
	<?php
	$ajax_add_to_cart_enabled = false;
	if (function_exists('get_field')) {
		$ajax_add_to_cart_enabled = get_field('enable_ajax_add_to_cart_buttons_on_single_product', 'options');
	}
	$bt_product_inner_class = 'bt-product-inner bt-quickview-product';
	if ($ajax_add_to_cart_enabled && $product && ($product->is_type('simple') || $product->is_type('variable'))) {
		$bt_product_inner_class .= ' bt-add-cart-ajax';
	}
	$parent_image_id = $product->get_image_id();
	$parent_image_src = $parent_image_id ? wp_get_attachment_url($parent_image_id) : '';
	?>
	<div class="<?php echo esc_attr($bt_product_inner_class); ?>" data-parent-image-src="<?php echo esc_url($parent_image_src); ?>">
		<?php
		// Load gallery images like product-gallery-slider.php
		$featured_image_id = $product->get_image_id();
		
		// Initialize attachment_ids with default product gallery
		$attachment_ids = $product->get_gallery_image_ids();
		
		// Check if product has default variation and load its images
		$default_variation_id = 0;
		$use_variation_images = false;
		
		if ($product->is_type('variable')) {
			// Get default variation ID using the helper function
			if (function_exists('get_default_variation_id')) {
				$default_variation_id = get_default_variation_id($product);
			}
			
			// If we have a default variation, check if it has custom image
			if ($default_variation_id && $default_variation_id > 0) {
				$variation = wc_get_product($default_variation_id);
				if ($variation) {
					$variation_image_id = $variation->get_image_id();
					
					// Only use variation images if variation has a custom image that's different from parent
					if ($variation_image_id && $variation_image_id > 0 && (int)$variation_image_id !== (int)$featured_image_id) {
						$featured_image_id = (int)$variation_image_id;
						$use_variation_images = true;
						
						// Get variation gallery images
						$variation_gallery = get_post_meta($default_variation_id, '_variation_gallery', true);
						if (!empty($variation_gallery)) {
							$attachment_ids = explode(',', $variation_gallery);
							$attachment_ids = array_map('intval', $attachment_ids);
							$attachment_ids = array_filter($attachment_ids);
						} else {
							$attachment_ids = array();
						}
					}
					// If variation doesn't have custom image, use default product gallery (already set above)
				}
			}
		}
		?>
		<div class="images bt-gallery-slider-products">
			<div class="bt-gallery-slider-product">
				<div class="swiper-wrapper">
					<?php 
						if ( $featured_image_id ) {
							$html = somnia_get_gallery_image_html( $featured_image_id, true, true );

							if(!empty($attachment_ids)) {
								foreach ( $attachment_ids as $key => $attachment_id ) {
									$html .= somnia_get_gallery_image_html( $attachment_id, true, true );
								}
							}
							echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $featured_image_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
						}
					?>
				</div>
			</div>
			<div class="bt-swiper-pagination swiper-pagination"></div>
		</div>

		<div class="summary entry-summary">
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