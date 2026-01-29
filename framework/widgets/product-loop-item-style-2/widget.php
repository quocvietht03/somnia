<?php

namespace SomniaElementorWidgets\Widgets\ProductLoopItemStyle2;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_ProductLoopItemStyle2 extends Widget_Base
{

	public function get_name()
	{
		return 'bt-product-loop-item-style-2';
	}

	public function get_title()
	{
		return __('Product Loop Item Style 2', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-post';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	protected function register_layout_section_controls() {}

	protected function register_style_section_controls() {}

	protected function register_controls()
	{
		$this->register_layout_section_controls();
		$this->register_style_section_controls();
	}

	protected function render()
	{
		if (!class_exists('WooCommerce')) {
			return;
		}
		
		$settings = $this->get_settings_for_display();
		global $product;

		if (empty($product) || ! $product->is_visible()) {
			return;
		}
?>
		<div class="bt-elwg-product-loop-item--style-2">
			<div class="bt-product">
				<div class="bt-product--image bt-cover-image">
					<a href="<?php echo esc_url(get_permalink()); ?>">
						<?php echo woocommerce_get_product_thumbnail(); ?>
					</a>
				</div>
				<div class="bt-product--content">
					<div class="bt-product--category">
						<?php
						$categories = get_the_terms(get_the_ID(), 'product_cat');
						if ($categories && !is_wp_error($categories)) {
							$cat_links = array();
							foreach ($categories as $category) {
								$cat_links[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
							}
							echo implode(', ', $cat_links);
						}
						?>
					</div>
					<h3 class="bt-product--title">
						<a href="<?php echo esc_url(get_permalink()); ?>">
							<?php echo get_the_title(); ?>
						</a>
					</h3>
					<div class="bt-product--infor">
						<div class="bt-product--info">
							<?php if ($product->get_price_html()) : ?>
								<?php echo '<div class="bt-product--price">' . $product->get_price_html() . '</div>'; ?>
							<?php endif; ?>
							<?php do_action('somnia_woocommerce_show_product_loop_sale_flash'); ?>
						</div>
						<?php if ($short_description = $product->get_short_description()) : ?>
							<div class="bt-product--short-description">
								<?php echo wp_kses_post($short_description); ?>
							</div>
						<?php endif; ?>
						<div class="bt-product--add-to-cart">
							<?php
							if ($product->is_type('simple')) {
								woocommerce_template_loop_add_to_cart();
							} else {
								echo '<a href="' . esc_url(get_permalink()) . '" class="bt-btn bt-btn-primary button">' . esc_html__('View Product', 'somnia') . '</a>';
							}
							?>

						</div>
					</div>

				</div>
			</div>
		</div>
<?php
	}

	protected function content_template() {}
}
