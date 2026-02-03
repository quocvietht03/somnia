<?php

namespace SomniaElementorWidgets\Widgets\ProductCompare;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Widget_ProductCompare extends Widget_Base
{

	public function get_name()
	{
		return 'bt-product-compare';
	}

	public function get_title()
	{
		return __('Product Compare', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-sync';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	protected function register_layout_section_controls()
	{
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'somnia'),
			]
		);

		$this->end_controls_section();
	}


	protected function register_style_section_controls()
	{
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->end_controls_section();
	}

	protected function register_controls()
	{

		$this->register_layout_section_controls();
		$this->register_style_section_controls();
	}

	public function post_social_share()
	{

		$social_item = array();
		$social_item[] = '<li>
                        <a target="_blank" data-btIcon="fa fa-facebook" data-toggle="tooltip" title="' . esc_attr__('Facebook', 'somnia') . '" href="https://www.facebook.com/sharer/sharer.php?u=' . get_the_permalink() . '">
                          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                            <path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/>
                          </svg>
                        </a>
                      </li>';
		$social_item[] = '<li>
                        <a target="_blank" data-btIcon="fa fa-twitter" data-toggle="tooltip" title="' . esc_attr__('Twitter', 'somnia') . '" href="https://twitter.com/share?url=' . get_the_permalink() . '">
                          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                            <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
                          </svg>
                        </a>
                      </li>';
		$social_item[] = '<li>
                        <a target="_blank" data-btIcon="fa fa-google-plus" data-toggle="tooltip" title="' . esc_attr__('Google Plus', 'somnia') . '" href="https://plus.google.com/share?url=' . get_the_permalink() . '">
                          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 488 512">
                            <path d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/>
                          </svg>
                        </a>
                      </li>';
		$social_item[] = '<li>
                        <a target="_blank" data-btIcon="fa fa-linkedin" data-toggle="tooltip" title="' . esc_attr__('Linkedin', 'somnia') . '" href="https://www.linkedin.com/shareArticle?url=' . get_the_permalink() . '">
                          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
                            <path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/>
                          </svg>
                        </a>
                      </li>';
		$social_item[] = '<li>
                        <a target="_blank" data-btIcon="fa fa-pinterest" data-toggle="tooltip" title="' . esc_attr__('Pinterest', 'somnia') . '" href="https://pinterest.com/pin/create/button/?url=' . get_the_permalink() . '">
                          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 496 512">
                            <path d="M496 256c0 137-111 248-248 248-25.6 0-50.2-3.9-73.4-11.1 10.1-16.5 25.2-43.5 30.8-65 3-11.6 15.4-59 15.4-59 8.1 15.4 31.7 28.5 56.8 28.5 74.8 0 128.7-68.8 128.7-154.3 0-81.9-66.9-143.2-152.9-143.2-107 0-163.9 71.8-163.9 150.1 0 36.4 19.4 81.7 50.3 96.1 4.7 2.2 7.2 1.2 8.3-3.3.8-3.4 5-20.3 6.9-28.1.6-2.5.3-4.7-1.7-7.1-10.1-12.5-18.3-35.3-18.3-56.6 0-54.7 41.4-107.6 112-107.6 60.9 0 103.6 41.5 103.6 100.9 0 67.1-33.9 113.6-78 113.6-24.3 0-42.6-20.1-36.7-44.8 7-29.5 20.5-61.3 20.5-82.6 0-19-10.2-34.9-31.4-34.9-24.9 0-44.9 25.7-44.9 60.2 0 22 7.4 36.8 7.4 36.8s-24.5 103.8-29 123.2c-5 21.4-3 51.6-.9 71.2C65.4 450.9 0 361.1 0 256 0 119 111 8 248 8s248 111 248 248z"/>
                          </svg>
                        </a>
                      </li>';

		ob_start();
?>
		<div class="bt-post-share">
			<?php
			if (!empty($social_item)) {
				echo '<span>' . esc_html__('Share: ', 'somnia') . '</span><ul>' . implode(' ', $social_item) . '</ul>';
			}
			?>
		</div>
	<?php
		return ob_get_clean();
	}

	protected function render()
	{
		if (!class_exists('WooCommerce')) {
			return;
		}
		
		$settings = $this->get_settings_for_display();
		
		// Check if compare should be shown
		$archive_shop = function_exists('get_field') ? get_field('archive_shop', 'options') : array();
		$show_compare = isset($archive_shop['show_compare']) ? $archive_shop['show_compare'] : true;
		
		if (!$show_compare) {
			return;
		}
		
		$productcompare = '';
		if (isset($_GET['datashare']) && !empty($_GET['datashare'])) {
			$wishlist = sanitize_text_field($_GET['datashare']);
			$product_ids = explode(',', $wishlist);
			$product_ids = array_map('intval', $product_ids);
			$product_ids = array_filter($product_ids);
		} else {
			$product_ids = array();
		}
		$compare_settings = get_field('compare', 'options');
		if (!empty($compare_settings['fields_to_show_compare'])) {
			$fields_show_compare = $compare_settings['fields_to_show_compare'];
		} else {
			$fields_show_compare = array('price', 'rating', 'stock_status', 'weight', 'dimensions', 'color', 'size');
		}
		$ex_items = count($product_ids) < 3 ? 3 - count($product_ids) : 0;
	?>
		<div class="bt-elwg-products-compare--default">
			<div class="bt-popup-compare bt-compare-elwwg">
				<div class="bt-compare-body woocommerce">
					<div class="bt-loading-wave"></div>
					<div class="bt-compare-load">

						<div class="bt-table-title">
							<h2><?php esc_html_e('Compare products', 'somnia') ?></h2>
						</div>
						<div class="bt-table-compare">
							<div class="bt-table--head">
								<?php
								if (!empty($fields_show_compare)) {
									echo '<div class="bt-table--col">' . esc_html__('Thumbnail', 'somnia') . '</div>';
									echo '<div class="bt-table--col">' . esc_html__('Product Name', 'somnia') . '</div>';
									if (in_array('short_desc', $fields_show_compare)) {
										echo '<div class="bt-table--col">' . esc_html__('Short Description', 'somnia') . '</div>';
									}
									if (in_array('price', $fields_show_compare)) {
										echo '<div class="bt-table--col">' . esc_html__('Price', 'somnia') . '</div>';
									}
									if (in_array('rating', $fields_show_compare)) {
										echo '<div class="bt-table--col">' . esc_html__('Rating', 'somnia') . '</div>';
									}
									if (in_array('brand', $fields_show_compare)) {
										echo '<div class="bt-table--col">' . esc_html__('Brand', 'somnia') . '</div>';
									}
									if (in_array('stock_status', $fields_show_compare)) {
										echo '<div class="bt-table--col">' . esc_html__('Availability', 'somnia') . '</div>';
									}
									if (in_array('sku', $fields_show_compare)) {
										echo '<div class="bt-table--col">' . esc_html__('SKU', 'somnia') . '</div>';
									}
									if (in_array('weight', $fields_show_compare)) {
										echo '<div class="bt-table--col">' . esc_html__('Weight', 'somnia') . '</div>';
									}
									if (in_array('dimensions', $fields_show_compare)) {
										echo '<div class="bt-table--col">' . esc_html__('Dimensions', 'somnia') . '</div>';
									}
									if (in_array('color', $fields_show_compare)) {
										echo '<div class="bt-table--col bt-head-color">' . esc_html__('color', 'somnia') . '</div>';
									}
									if (in_array('size', $fields_show_compare)) {
										echo '<div class="bt-table--col">' . esc_html__('Size', 'somnia') . '</div>';
									}
								}
								?>
								<div class="bt-table--col"></div>
							</div>
							<div class="bt-table--body">
								<?php
								foreach ($product_ids as $key => $id) {
									$product = wc_get_product($id);
									if ($product) {
										$product_url = get_permalink($id);
										$product_name = $product->get_name();
										$product_image = wp_get_attachment_image_src($product->get_image_id(), 'large');
										if (!$product_image) {
											$product_image_url = wc_placeholder_img_src();
										} else {
											$product_image_url = $product_image[0];
										}
										$product_price = $product->get_price_html();
										$stock_status = $product->get_stock_status();
										if ($stock_status == 'onbackorder') {
											$stock_status_custom = '<p class="stock on-backorder">' . __('On Backorder', 'somnia') . '</p>';
										} elseif ($product->is_in_stock()) {
											$stock_status_custom = '<p class="stock in-stock">' . __('In Stock', 'somnia') . '</p>';
										} else {
											$stock_status_custom = '<p class="stock out-of-stock">' . __('Out of Stock', 'somnia') . '</p>';
										}
										$brand = wp_get_post_terms($id, 'product_brand', ['fields' => 'names']);
										$brand_list = !empty($brand) ? implode(', ', $brand) : '';

										$brands = wp_get_post_terms($id, 'product_brand', ['fields' => 'all']);
										$brand_links = [];
										foreach ($brands as $brand) {
											$brand_links[] = '<a href="' . get_term_link($brand) . '">' . esc_html($brand->name) . '</a>';
										}
										$brand_list = !empty($brand_links) ? implode(', ', $brand_links) : '';

								?>
										<div class="bt-table--row">
											<div class="bt-table--col bt-thumb">
												<a href="<?php echo esc_url($product_url); ?>">
													<img src="<?php echo esc_url($product_image_url); ?>" alt="<?php echo esc_attr($product_name); ?>">
												</a>
											</div>
											<div class="bt-table--col bt-name">
												<h3><a href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($product_name); ?></a></h3>
											</div>
											<?php if (!empty($fields_show_compare)) {
												if (in_array('short_desc', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-description">
														<?php echo '<p>' . wp_trim_words($product->get_short_description(), 20) . '</p>'; ?>
													</div>
												<?php } ?>
												<?php if (in_array('price', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-price">
														<?php echo '<p>' . $product_price . '</p>'; ?>
													</div>
												<?php } ?>
												<?php if (in_array('rating', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-rating woocommerce">
														<div class="bt-product-rating">
															<?php echo wc_get_rating_html($product->get_average_rating()); ?>
															<?php if ($product->get_rating_count()): ?>
																<div class="bt-product-rating--count">
																	(<?php echo esc_html($product->get_rating_count()); ?>)
																</div>
															<?php endif; ?>
														</div>
													</div>
												<?php } ?>
												<?php if (in_array('brand', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-brand">
														<?php echo '<p>' . $brand_list . '</p>'; ?>
													</div>
												<?php } ?>
												<?php if (in_array('stock_status', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-stock">
														<?php echo wp_kses_post($stock_status_custom); ?>
													</div>
												<?php } ?>
												<?php if (in_array('sku', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-sku">
														<?php echo '<p>' . $product->get_sku() . '</p>'; ?>
													</div>
												<?php } ?>
												<?php if (in_array('weight', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-weight">
														<?php echo '<p>' . $product->get_weight() . ' ' . get_option('woocommerce_weight_unit') . '</p>'; ?>
													</div>
												<?php } ?>
												<?php if (in_array('dimensions', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-dimensions">
														<?php echo '<p>' . wc_format_dimensions($product->get_dimensions(false)) . '</p>'; ?>
													</div>
												<?php } ?>
											<?php if (in_array('color', $fields_show_compare)) {
												$color_taxonomy = somnia_get_color_taxonomy();
												if ($color_taxonomy) {
											?>
												<div class="bt-table--col bt-color">
													<?php
													$colors = wp_get_post_terms($id, $color_taxonomy, ['fields' => 'ids']);
													$count = 0;
													foreach ($colors as $color_id) {
														if ($count >= 6) break; // Only show max 6 colors

														// Get color from metafield
														$color_value = get_term_meta($color_id, 'somnia_term_color', true);
														$color = get_term($color_id, $color_taxonomy);
														if (!$color_value) {
															$color_value = $color->slug;
														}
														echo '<div class="bt-item-color"><span style="background-color: ' . esc_attr($color_value) . ';"></span>' . esc_html($color->name) . '</div>';

														$count++;
													}
													?>
												</div>
											<?php 
												}
											} ?>
												<?php if (in_array('size', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-size">
														<?php
														$sizes = wp_get_post_terms($id, 'pa_size', ['fields' => 'names']);
														echo '<p>' . (!empty($sizes) ? implode(', ', $sizes) : 'N/A') . '</p>';
														?>
													</div>
												<?php } ?>
											<?php } ?>
											<div class="bt-table--col bt-add-to-cart">
												<?php
												$product = wc_get_product($id);
												if ($product->is_type('simple')) {
												?>
													<a href="?add-to-cart=<?php echo esc_attr($id); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($id); ?>" data-quantity="1" class="bt-button product_type_simple add_to_cart_button ajax_add_to_cart bt-button-hover" data-product_id="<?php echo esc_attr($id); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
												<?php
												} else {
												?>
													<a href="<?php echo esc_url(get_permalink($id)); ?>" class="bt-button bt-button-hover"><?php echo esc_html__('View Product', 'somnia') ?></a>
												<?php
												}
												?>
											</div>
										</div>
								<?php
									}
								}
								?>
								<?php
								if ($ex_items > 0) {
									for ($i = 0; $i < $ex_items; $i++) {
								?>
										<div class="bt-table--row bt-load-before bt-product-add-compare">
											<div class="bt-table--col bt-thumb">
												<div class="bt-cover-image">
													<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" fill="currentColor">
														<path d="M256 512a25 25 0 0 1-25-25V25a25 25 0 0 1 50 0v462a25 25 0 0 1-25 25z"></path>
														<path d="M487 281H25a25 25 0 0 1 0-50h462a25 25 0 0 1 0 50z"></path>
													</svg>
													<span> <?php echo __('Add Product To Compare', 'somnia'); ?></span>
												</div>
											</div>
											<div class="bt-table--col bt-name"></div>
											<?php if (!empty($fields_show_compare)) {
												if (in_array('short_desc', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-description"></div>
												<?php } ?>
												<?php if (in_array('price', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-price"></div>
												<?php } ?>
												<?php if (in_array('rating', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-rating woocommerce"></div>
												<?php } ?>
												<?php if (in_array('brand', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-brand"></div>
												<?php } ?>
												<?php if (in_array('stock_status', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-stock"></div>
												<?php } ?>
												<?php if (in_array('sku', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-sku"></div>
												<?php } ?>
												<?php if (in_array('weight', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-weight"></div>
												<?php } ?>
												<?php if (in_array('dimensions', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-dimensions"></div>
												<?php } ?>
												<?php if (in_array('color', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-color"></div>
												<?php } ?>
												<?php if (in_array('size', $fields_show_compare)) { ?>
													<div class="bt-table--col bt-size"></div>
												<?php } ?>
											<?php } ?>
											<div class="bt-table--col"></div>
										</div>
								<?php
									}
								}
								?>
							</div>
						</div>
					</div>
					<?php echo '<div class="bt-compare-share bt-social-share">' . $this->post_social_share() . '</div>'; ?>
				</div>
			</div>
		</div>
<?php
	}

	protected function content_template() {}
}
