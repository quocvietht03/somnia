<?php

namespace SomniaElementorWidgets\Widgets\ProductItem;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_ProductItem extends Widget_Base
{

	public function get_name()
	{
		return 'bt-product-item';
	}

	public function get_title()
	{
		return __('Product Item', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-post';
	}

	public function get_categories()
	{
		return ['somnia'];
	}
	public function get_supported_products()
	{
		$supported_products = [];

		if (!class_exists('WooCommerce')) {
			return $supported_products;
		}

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
				'label' => __('Layout', 'somnia'),
				'tab' => Controls_Manager::TAB_CONTENT,
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
				'range' => [
					'px' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery__image' => 'padding-bottom: calc( {{SIZE}} * 100% ) !important;',
				],
			]
		);

		$this->add_control(
			'show_description',
			[
				'label' => __('Show Description', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_section_controls() 
	{
		// Title Style
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __('Title', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-loop-product__infor .woocommerce-loop-product__title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'title_color_hover',
			[
				'label' => __('Hover Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-loop-product__infor .woocommerce-loop-product__title:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'title_hover_underline',
			[
				'label' => __('Hover Underline', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'), 
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .woocommerce-loop-product__infor .woocommerce-loop-product__title',
			]
		);

		$this->end_controls_section();

		// Description Style
		$this->start_controls_section(
			'section_description_style',
			[
				'label' => __('Description', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-product-short-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .bt-product-short-description',
			]
		);

		$this->end_controls_section();

		// Price Style
		$this->start_controls_section(
			'section_price_style',
			[
				'label' => __('Price', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-product-item .woocommerce-loop-product__infor .price' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-product-item .woocommerce-loop-product__infor .price ins' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-product-item .woocommerce-loop-product__infor .price .woocommerce-Price-amount' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'selector' => '{{WRAPPER}} .bt-elwg-product-item .woocommerce-loop-product__infor .price .woocommerce-Price-amount',
			]
		);
		$this->add_control(
			'price_sale_color',
			[
				'label' => __('Sale Price Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-product-item .woocommerce-loop-product__infor .price del' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-product-item .woocommerce-loop-product__infor .price del .woocommerce-Price-amount' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_sale_typography',
				'label' => __('Sale Price Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-elwg-product-item .woocommerce-loop-product__infor .price del .woocommerce-Price-amount',
			]
		);

		$this->end_controls_section();
	}

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
		<div class="bt-elwg-product-item ">
			<?php
			$query = new \WP_Query($args);
			if ($query->have_posts()) :
				while ($query->have_posts()) : $query->the_post();
					global $product;
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
							?>
							<div class="woocommerce-product-sale-label">
								<?php
								do_action('somnia_woocommerce_show_product_loop_sale_flash');
								do_action('somnia_woocommerce_shop_loop_item_label');
								?>
							</div>
							<?php
							$archive_shop_widget = function_exists('get_field') ? get_field('archive_shop', 'options') : array();
							$show_wishlist_widget = isset($archive_shop_widget['show_wishlist']) ? $archive_shop_widget['show_wishlist'] : true;
							$show_compare_widget = isset($archive_shop_widget['show_compare']) ? $archive_shop_widget['show_compare'] : true;
							$show_quickview_widget = isset($archive_shop_widget['show_quickview']) ? $archive_shop_widget['show_quickview'] : true;
							?>
							<?php if ($show_wishlist_widget || $show_compare_widget || $show_quickview_widget) : ?>
							<div class="bt-product-icon-btn">
								<?php if ($show_wishlist_widget) : ?>
								<a class="bt-icon-btn bt-product-wishlist-btn" href="#" data-id="<?php echo get_the_ID(); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
										<path d="M17.4225 4.45473C16.5734 3.60774 15.4234 3.13143 14.2241 3.12997C13.0248 3.12851 11.8737 3.602 11.0225 4.44691L10.0006 5.39613L8.97797 4.44379C8.127 3.5952 6.97378 3.11941 5.77201 3.1211C4.57023 3.12278 3.41835 3.6018 2.56977 4.45277C1.72118 5.30374 1.24539 6.45696 1.24707 7.65873C1.24876 8.86051 1.72778 10.0124 2.57875 10.861L9.55922 17.9438C9.61737 18.0028 9.68669 18.0497 9.76314 18.0817C9.8396 18.1138 9.92165 18.1302 10.0045 18.1302C10.0874 18.1302 10.1695 18.1138 10.2459 18.0817C10.3224 18.0497 10.3917 18.0028 10.4498 17.9438L17.4225 10.861C18.2717 10.0113 18.7487 8.85915 18.7487 7.65785C18.7487 6.45656 18.2717 5.30442 17.4225 4.45473ZM16.5358 9.98285L10.0006 16.611L3.46156 9.9766C2.84618 9.36122 2.50046 8.52657 2.50046 7.65629C2.50046 6.786 2.84618 5.95136 3.46156 5.33598C4.07695 4.72059 4.91159 4.37487 5.78187 4.37487C6.65216 4.37487 7.4868 4.72059 8.10219 5.33598L8.11781 5.3516L9.57484 6.70707C9.6905 6.8147 9.84263 6.87454 10.0006 6.87454C10.1586 6.87454 10.3107 6.8147 10.4264 6.70707L11.8834 5.3516L11.8991 5.33598C12.5149 4.721 13.3497 4.37585 14.22 4.37643C15.0903 4.37702 15.9247 4.7233 16.5397 5.3391C17.1547 5.9549 17.4998 6.78977 17.4992 7.66006C17.4986 8.53035 17.1524 9.36475 16.5366 9.97972L16.5358 9.98285Z" />
									</svg>
								</a>
								<?php endif; ?>
								<?php if ($show_compare_widget) : ?>
								<a class="bt-icon-btn bt-product-compare-btn" href="#" data-id="<?php echo get_the_ID(); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
										<path d="M8.75001 11.8737C8.58425 11.8737 8.42528 11.9396 8.30807 12.0568C8.19086 12.174 8.12501 12.333 8.12501 12.4987V14.7401L5.18282 11.7956C5.06621 11.6789 5.0005 11.5208 5.00001 11.3558V7.42061C5.58917 7.26849 6.10263 6.90672 6.44413 6.40311C6.78564 5.8995 6.93175 5.28862 6.85508 4.68499C6.77841 4.08136 6.48422 3.52641 6.02765 3.12416C5.57109 2.72192 4.98349 2.5 4.37501 2.5C3.76652 2.5 3.17893 2.72192 2.72236 3.12416C2.26579 3.52641 1.9716 4.08136 1.89493 4.68499C1.81826 5.28862 1.96437 5.8995 2.30588 6.40311C2.64739 6.90672 3.16084 7.26849 3.75001 7.42061V11.3566C3.74876 11.6031 3.79645 11.8474 3.89033 12.0754C3.9842 12.3034 4.12239 12.5105 4.29688 12.6847L7.24141 15.6237H5.00001C4.83425 15.6237 4.67528 15.6896 4.55806 15.8068C4.44085 15.924 4.37501 16.083 4.37501 16.2487C4.37501 16.4145 4.44085 16.5735 4.55806 16.6907C4.67528 16.8079 4.83425 16.8737 5.00001 16.8737H8.75001C8.91577 16.8737 9.07474 16.8079 9.19195 16.6907C9.30916 16.5735 9.37501 16.4145 9.37501 16.2487V12.4987C9.37501 12.333 9.30916 12.174 9.19195 12.0568C9.07474 11.9396 8.91577 11.8737 8.75001 11.8737ZM3.12501 4.99874C3.12501 4.75151 3.19832 4.50984 3.33567 4.30428C3.47302 4.09872 3.66824 3.9385 3.89665 3.84389C4.12506 3.74928 4.37639 3.72453 4.61887 3.77276C4.86135 3.82099 5.08407 3.94004 5.25889 4.11486C5.43371 4.28967 5.55276 4.5124 5.60099 4.75488C5.64922 4.99735 5.62447 5.24869 5.52986 5.47709C5.43525 5.7055 5.27503 5.90072 5.06947 6.03808C4.86391 6.17543 4.62223 6.24874 4.37501 6.24874C4.04349 6.24874 3.72554 6.11704 3.49112 5.88262C3.2567 5.6482 3.12501 5.33026 3.12501 4.99874ZM16.25 12.5769V8.64171C16.2513 8.39516 16.2036 8.15081 16.1097 7.92283C16.0158 7.69485 15.8776 7.48777 15.7031 7.31358L12.7586 4.37374H15C15.1658 4.37374 15.3247 4.30789 15.442 4.19068C15.5592 4.07347 15.625 3.9145 15.625 3.74874C15.625 3.58298 15.5592 3.42401 15.442 3.3068C15.3247 3.18959 15.1658 3.12374 15 3.12374H11.25C11.0842 3.12374 10.9253 3.18959 10.8081 3.3068C10.6909 3.42401 10.625 3.58298 10.625 3.74874V7.49874C10.625 7.6645 10.6909 7.82347 10.8081 7.94068C10.9253 8.05789 11.0842 8.12374 11.25 8.12374C11.4158 8.12374 11.5747 8.05789 11.6919 7.94068C11.8092 7.82347 11.875 7.6645 11.875 7.49874V5.25733L14.8172 8.20186C14.8752 8.25995 14.9212 8.3289 14.9526 8.40477C14.984 8.48064 15.0001 8.56195 15 8.64405V12.5769C14.4108 12.729 13.8974 13.0908 13.5559 13.5944C13.2144 14.098 13.0683 14.7089 13.1449 15.3125C13.2216 15.9161 13.5158 16.4711 13.9724 16.8733C14.4289 17.2756 15.0165 17.4975 15.625 17.4975C16.2335 17.4975 16.8211 17.2756 17.2777 16.8733C17.7342 16.4711 18.0284 15.9161 18.1051 15.3125C18.1818 14.7089 18.0356 14.098 17.6941 13.5944C17.3526 13.0908 16.8392 12.729 16.25 12.5769ZM15.625 16.2487C15.3778 16.2487 15.1361 16.1754 14.9305 16.0381C14.725 15.9007 14.5648 15.7055 14.4702 15.4771C14.3755 15.2487 14.3508 14.9974 14.399 14.7549C14.4473 14.5124 14.5663 14.2897 14.7411 14.1149C14.9159 13.94 15.1387 13.821 15.3811 13.7728C15.6236 13.7245 15.875 13.7493 16.1034 13.8439C16.3318 13.9385 16.527 14.0987 16.6643 14.3043C16.8017 14.5098 16.875 14.7515 16.875 14.9987C16.875 15.3303 16.7433 15.6482 16.5089 15.8826C16.2745 16.117 15.9565 16.2487 15.625 16.2487Z" />
									</svg>
								</a>
								<?php endif; ?>
								<?php if ($show_quickview_widget) : ?>
								<a class="bt-icon-btn bt-product-quick-view-btn" href="#" data-id="<?php echo get_the_ID(); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
										<path d="M23.1853 11.6962C23.1525 11.6222 22.3584 9.86062 20.5931 8.09531C18.2409 5.74312 15.27 4.5 12 4.5C8.72999 4.5 5.75905 5.74312 3.40687 8.09531C1.64155 9.86062 0.843741 11.625 0.814679 11.6962C0.772035 11.7922 0.75 11.896 0.75 12.0009C0.75 12.1059 0.772035 12.2097 0.814679 12.3056C0.847491 12.3797 1.64155 14.1403 3.40687 15.9056C5.75905 18.2569 8.72999 19.5 12 19.5C15.27 19.5 18.2409 18.2569 20.5931 15.9056C22.3584 14.1403 23.1525 12.3797 23.1853 12.3056C23.2279 12.2097 23.25 12.1059 23.25 12.0009C23.25 11.896 23.2279 11.7922 23.1853 11.6962ZM12 18C9.11437 18 6.59343 16.9509 4.50655 14.8828C3.65028 14.0313 2.92179 13.0603 2.34374 12C2.92164 10.9396 3.65014 9.9686 4.50655 9.11719C6.59343 7.04906 9.11437 6 12 6C14.8856 6 17.4066 7.04906 19.4934 9.11719C20.3514 9.9684 21.0814 10.9394 21.6609 12C20.985 13.2619 18.0403 18 12 18ZM12 7.5C11.11 7.5 10.2399 7.76392 9.49992 8.25839C8.7599 8.75285 8.18313 9.45566 7.84253 10.2779C7.50194 11.1002 7.41282 12.005 7.58646 12.8779C7.76009 13.7508 8.18867 14.5526 8.81801 15.182C9.44735 15.8113 10.2492 16.2399 11.1221 16.4135C11.995 16.5872 12.8998 16.4981 13.7221 16.1575C14.5443 15.8169 15.2471 15.2401 15.7416 14.5001C16.2361 13.76 16.5 12.89 16.5 12C16.4987 10.8069 16.0242 9.66303 15.1806 8.81939C14.337 7.97575 13.1931 7.50124 12 7.5ZM12 15C11.4066 15 10.8266 14.8241 10.3333 14.4944C9.83993 14.1648 9.45541 13.6962 9.22835 13.1481C9.00129 12.5999 8.94188 11.9967 9.05763 11.4147C9.17339 10.8328 9.45911 10.2982 9.87867 9.87868C10.2982 9.45912 10.8328 9.1734 11.4147 9.05764C11.9967 8.94189 12.5999 9.0013 13.148 9.22836C13.6962 9.45542 14.1648 9.83994 14.4944 10.3333C14.824 10.8266 15 11.4067 15 12C15 12.7956 14.6839 13.5587 14.1213 14.1213C13.5587 14.6839 12.7956 15 12 15Z" />
									</svg>
								</a>
								<?php endif; ?>
							</div>
							<?php endif; ?>
							<div class="bt-add-to-cart">
								<?php
								if (!$product->is_type('variable')) {
									do_action('somnia_woocommerce_template_loop_add_to_cart');
								} else {
									do_action('somnia_woocommerce_template_loop_add_to_cart_variable');
								}
								?>
							</div>
						</div>

						<div class="woocommerce-loop-product__infor <?php echo esc_attr($settings['title_hover_underline'] === 'yes' ? 'bt-title-hover-underline' : ''); ?>">
							<?php
							do_action('somnia_woocommerce_template_loop_product_link_open');
							do_action('somnia_woocommerce_template_loop_product_title');
							do_action('somnia_woocommerce_template_loop_product_link_close');
							if ($settings['show_description'] === 'yes' && ($short_description = $product->get_short_description())) : ?>
								<div class="bt-product-short-description">
									<?php echo wp_kses_post($short_description); ?>
								</div>
							<?php endif;
							do_action('somnia_woocommerce_template_loop_price');
							do_action('somnia_woocommerce_template_loop_rating');
							?>
						</div>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
<?php
	}

	protected function content_template() {}
}
