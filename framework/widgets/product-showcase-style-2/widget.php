<?php

namespace SomniaElementorWidgets\Widgets\ProductShowcaseStyle2;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_ProductShowcaseStyle2 extends Widget_Base
{

	public function get_name()
	{
		return 'bt-product-showcase-style-2';
	}

	public function get_title()
	{
		return __('Product Showcase Style 2', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-single-product';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	public function get_script_depends()
	{
		return ['swiper-slider', 'elementor-widgets'];
	}

	public function get_supported_products()
	{
		$supported_products = [];

		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'post_status' => 'publish'
		);

		$products = get_posts($args);

		if (!empty($products)) {
			foreach ($products as $product) {
				$supported_products[$product->ID] = $product->post_title;
			}
		}

		return $supported_products;
	}

	protected function register_layout_section_controls()
	{
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Content', 'somnia'),
			]
		);

		$this->add_control(
			'layout_01_note',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('Select a layout in product edit page for this layout to work properly. Supported layouts are: bottom-thumbnail, left-thumbnail, right-thumbnail', 'somnia'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

		$this->add_control(
			'products',
			[
				'label' => __('Select Product', 'somnia'),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_supported_products(),
				'label_block' => true,
				'multiple' => false,
			]
		);

		$this->add_responsive_control(
			'image_ratio',
			[
				'label' => __('Image Ratio', 'somnia'),
				'type' => Controls_Manager::SLIDER, 
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-product-showcase--item-image .bt-cover-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
					'{{WRAPPER}} .bt-layout-layout-01 .bt-product-showcase--item-images .woocommerce-product-gallery__image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
				],
			]
		);

		$this->end_controls_section();
	}
	protected function register_controls()
	{
		$this->register_layout_section_controls();
	}

	protected function render()
	{
		if (!class_exists('WooCommerce')) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$products = $settings['products'];

		if (empty($products)) {
			return;
		}

		$args = array(
			'post_type' => 'product',
			'p' => $products,
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'post__in',
		)
?>
		<div class="bt-elwg-product-showcase--style-2 bt-layout-layout-01 js-product-showcase bt-add-cart-ajax">
			<?php
			$query = new \WP_Query($args);
			if ($query->have_posts()) :
				while ($query->have_posts()) : $query->the_post();
					global $product;

					if (empty($product) || ! $product->is_visible()) {
						continue;
					}

					$product_id = $product->get_id();
					$product_name = $product->get_name();
					$product_link = get_permalink($product_id);

					$is_variable = $product->is_type('variable') ? 'bt-product-variable' : '';

					// Get layout from product meta, only allow thumbnail layouts
					$product_layout = get_post_meta($product_id, '_layout_product', true);
					$allowed_layouts = ['bottom-thumbnail', 'left-thumbnail', 'right-thumbnail'];

					// If product layout is not set or not in allowed layouts, use default left-thumbnail
					if (!in_array($product_layout, $allowed_layouts)) {
						$product_layout = 'left-thumbnail';
					}

					$post_thumbnail_id = $product->get_image_id();

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
								if ($variation_image_id && $variation_image_id > 0 && (int)$variation_image_id !== (int)$post_thumbnail_id) {
									$post_thumbnail_id = (int)$variation_image_id;
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
					$columns = apply_filters('woocommerce_product_thumbnails_columns', 4);
					$wrapper_classes = apply_filters(
						'woocommerce_single_product_image_gallery_classes',
						array(
							'woocommerce-product-gallery',
							'woocommerce-product-gallery--' . ($post_thumbnail_id ? 'with-images' : 'without-images'),
							'woocommerce-product-gallery--columns-' . absint($columns),
							'images',
							'bt-' . $product_layout
						)
					);
			?>
					<div class="bt-product-showcase bt-product-showcase--horizontal<?php echo esc_attr($is_variable); ?>">
						<div class="bt-product-showcase--item-images">
							<div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>">
								<div class="woocommerce-product-gallery__wrapper<?php echo (!empty($attachment_ids) && has_post_thumbnail()) ? ' bt-has-slide-thumbs' : ''; ?>">
									<?php if ($post_thumbnail_id) : ?>
										<div class="woocommerce-product-gallery__slider bt-gallery-lightbox bt-gallery-zoomable">
											<div class="swiper-wrapper">
												<?php
												$html = somnia_get_gallery_image_html($post_thumbnail_id, true, true);
												if (!empty($attachment_ids)) {
													foreach ($attachment_ids as $key => $attachment_id) {
														$html .= somnia_get_gallery_image_html($attachment_id, true, true);
													}
												}
												echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id);
												?>
											</div>
											<div class="swiper-button-prev"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
												<path d="M15.5312 18.9698C15.6009 19.0395 15.6562 19.1222 15.6939 19.2132C15.7316 19.3043 15.751 19.4019 15.751 19.5004C15.751 19.599 15.7316 19.6965 15.6939 19.7876C15.6562 19.8786 15.6009 19.9614 15.5312 20.031C15.4615 20.1007 15.3788 20.156 15.2878 20.1937C15.1967 20.2314 15.0991 20.2508 15.0006 20.2508C14.902 20.2508 14.8045 20.2314 14.7134 20.1937C14.6224 20.156 14.5396 20.1007 14.47 20.031L6.96996 12.531C6.90023 12.4614 6.84491 12.3787 6.80717 12.2876C6.76943 12.1966 6.75 12.099 6.75 12.0004C6.75 11.9019 6.76943 11.8043 6.80717 11.7132C6.84491 11.6222 6.90023 11.5394 6.96996 11.4698L14.47 3.96979C14.6107 3.82906 14.8016 3.75 15.0006 3.75C15.1996 3.75 15.3905 3.82906 15.5312 3.96979C15.6719 4.11052 15.751 4.30139 15.751 4.50042C15.751 4.69944 15.6719 4.89031 15.5312 5.03104L8.5609 12.0004L15.5312 18.9698Z"/>
											</svg></div>
											<div class="swiper-button-next"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
												<path d="M17.031 12.531L9.53104 20.031C9.46136 20.1007 9.37863 20.156 9.28759 20.1937C9.19654 20.2314 9.09896 20.2508 9.00042 20.2508C8.90187 20.2508 8.80429 20.2314 8.71324 20.1937C8.6222 20.156 8.53947 20.1007 8.46979 20.031C8.40011 19.9614 8.34483 19.8786 8.30712 19.7876C8.26941 19.6965 8.25 19.599 8.25 19.5004C8.25 19.4019 8.26941 19.3043 8.30712 19.2132C8.34483 19.1222 8.40011 19.0395 8.46979 18.9698L15.4401 12.0004L8.46979 5.03104C8.32906 4.89031 8.25 4.69944 8.25 4.50042C8.25 4.30139 8.32906 4.11052 8.46979 3.96979C8.61052 3.82906 8.80139 3.75 9.00042 3.75C9.19944 3.75 9.39031 3.82906 9.53104 3.96979L17.031 11.4698C17.1008 11.5394 17.1561 11.6222 17.1938 11.7132C17.2316 11.8043 17.251 11.9019 17.251 12.0004C17.251 12.099 17.2316 12.1966 17.1938 12.2876C17.1561 12.3787 17.1008 12.4614 17.031 12.531Z" fill="#183F91"/>
											</svg></div>
										</div>
										<div class="woocommerce-product-gallery__slider-thumbs">
											<div class="swiper-wrapper">
												<?php
												$html = somnia_get_gallery_image_html($post_thumbnail_id, false, true);

												// Add gallery thumbnails
												if (!empty($attachment_ids)) {
													foreach ($attachment_ids as $key => $attachment_id) {
														$html .= somnia_get_gallery_image_html($attachment_id, false, true, $key);
													}
												}

												echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id);
												?>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="summary entry-summary bt-product-showcase--item-content">
							<div class="woocommerce-product-rating-sold">
								<?php
								do_action('somnia_woocommerce_shop_loop_item_label');
								do_action('somnia_woocommerce_template_single_rating');
								?>
							</div>
							<?php
							do_action('somnia_woocommerce_template_loop_product_link_open');
							do_action('somnia_woocommerce_template_single_title');
							do_action('somnia_woocommerce_template_loop_product_link_close');
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
								?>
							</div>
						</div>
					</div>
			<?php
				endwhile;
				wp_reset_postdata();
			endif;
			?>
		</div>
<?php
	}

	protected function content_template() {}
}
