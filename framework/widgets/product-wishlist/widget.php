<?php

namespace SomniaElementorWidgets\Widgets\ProductWishlist;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class Widget_ProductWishlist extends Widget_Base
{

	public function get_name()
	{
		return 'bt-product-wishlist';
	}

	public function get_title()
	{
		return __('Product Wishlist', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-heart-o';
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
		
		// Check if wishlist should be shown
		$archive_shop = function_exists('get_field') ? get_field('archive_shop', 'options') : array();
		$show_wishlist = isset($archive_shop['show_wishlist']) ? $archive_shop['show_wishlist'] : true;
		
		if (!$show_wishlist) {
			return;
		}
		
		$productwishlist = '';
		if (isset($_GET['datashare']) && !empty($_GET['datashare'])) {
			$wishlist = sanitize_text_field($_GET['datashare']);
			$product_ids = explode(',', $wishlist);
			$product_ids = array_map('intval', $product_ids);
			$product_ids = array_filter($product_ids);
		} else {
			$product_ids = array();
		}
	?>
		<div class="bt-elwg-products-wishlist--default">
			<form class="bt-products-wishlist-form" action="" method="post">
				<input type="hidden" class="bt-productwishlistlocal" name="productwishlistlocal" value="">

				<div class="bt-table">
					<div class="bt-table--head">
						<div class="bt-table--row">
							<div class="bt-table--col bt-product-remove">
								<?php echo '<span>' . __('Remove', 'somnia') . '</span>'; ?>
							</div>
							<div class="bt-table--col bt-product-thumb">
								<?php echo '<span>' . __('Thumbnail', 'somnia') . '</span>'; ?>
							</div>
							<div class="bt-table--col bt-product-title">
								<?php echo '<span>' . __('Product Name', 'somnia') . '</span>'; ?>
							</div>
							<div class="bt-table--col bt-product-price">
								<?php echo '<span>' . __('Price', 'somnia') . '</span>'; ?>
							</div>
							<div class="bt-table--col bt-product-stock">
								<?php echo '<span>' . __('Stock status', 'somnia') . '</span>'; ?>
							</div>
							<div class="bt-table--col bt-product-add-to-cart">
							</div>
						</div>
					</div>

					<div class="bt-table--body woocommerce">
						<span class="bt-loading-wave"></span>

						<?php if (!empty($product_ids)) { ?>
							<div class="bt-product-list">
								<?php foreach ($product_ids as $product_id) {
									$product = wc_get_product($product_id);
									if ($product) {
										$product_price = $product->get_price_html();
										$stock_status = $product->is_in_stock() ? __('In Stock', 'somnia') : __('Out of Stock', 'somnia');
								?>
										<div class="bt-table--row bt-product-item">
											<div class="bt-table--col bt-product-remove">

											</div>
											<div class="bt-table--col bt-product-thumb">
												<a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="bt-thumb">
													<?php echo wp_kses_post($product->get_image('medium')); ?>
												</a>
											</div>
											<div class="bt-table--col bt-product-title">
												<h3 class="bt-title">
													<a href="<?php echo esc_url(get_permalink($product_id)); ?>">
														<?php echo esc_html($product->get_name()); ?>
													</a>
												</h3>
												<?php
												if ($product_price) {
													echo '<span class="bt-price-mobile">' . $product_price . '</span>';
												}
												?>
											</div>
											<div class="bt-table--col bt-product-price<?php echo !$product->is_type('simple') ? ' bt-type-variable' : ''; ?>">
												<?php
												if ($product_price) {
													echo '<span>' . $product_price . '</span>';
												}
												?>
											</div>
											<div class="bt-table--col bt-product-stock">
												<span><?php echo esc_html($stock_status); ?></span>
											</div>
											<div class="bt-table--col bt-product-add-to-cart">
												<?php
												if ($product->is_type('simple')) {
												?>
													<a href="?add-to-cart=<?php echo esc_attr($product_id); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product_id); ?>" data-quantity="1" class="bt-button product_type_simple add_to_cart_button ajax_add_to_cart bt-button-hover" data-product_id="<?php echo esc_attr($product_id); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
												<?php
												} else {
												?>
													<a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="bt-button bt-button-hover"><?php echo esc_html__('View Product', 'somnia') ?></a>
												<?php
												}
												?>
											</div>
										</div>
									<?php } ?>
								<?php } ?>
							</div>
						<?php } else { ?>
							<div class="bt-product-list">
								<div class="bt-no-results">
									<?php echo __('No products found!', 'somnia'); ?>
									<a href="/shop/"><?php echo __('Back to Shop', 'somnia'); ?></a>
								</div>
							</div>
						<?php } ?>
					</div>


					<div class="bt-table--foot">
						<div class="bt-table--row">
							<?php echo '<div class="bt-table--col bt-social-share">' . $this->post_social_share() . '</div>'; ?>
						</div>
					</div>
				</div>
			</form>
		</div>
<?php
	}

	protected function content_template() {}
}
