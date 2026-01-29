<?php

namespace SomniaElementorWidgets\Widgets\MiniCart;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_MiniCart extends Widget_Base
{
	// Static variable to track if sidebar has been added to footer
	private static $sidebar_hooked = false;

	public function get_name()
	{
		return 'bt-mini-cart';
	}

	public function get_title()
	{
		return __('Mini Cart', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-woo-cart';
	}

	public function get_categories()
	{
		return ['somnia'];
	}
	public function get_script_depends()
	{
		return ['elementor-widgets'];
	}

	protected function register_content_section_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Content', 'somnia'),
			]
		);
		$this->add_control(
			'cart_mini_icon',
			[
				'label' => esc_html__('Icon Cart', 'somnia'),
				'type' => Controls_Manager::MEDIA,
				'media_types' => ['svg'],
			]
		);
		$this->add_control(
			'enable_sidebar_cart',
			[
				'label' => esc_html__('Enable Sidebar Cart', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'somnia'),
				'label_off' => esc_html__('No', 'somnia'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'cart_text',
			[
				'label' => esc_html__('Text', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__('Enter text to display below icon', 'somnia'),
			]
		);


		$this->end_controls_section();
	}


	protected function register_style_content_section_controls()
	{

		$this->start_controls_section(
			'section_style_content_cart',
			[
				'label' => esc_html__('Content Cart', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_cart',
			[
				'label' => __('Icon', 'somnia'),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_responsive_control(
			'icon_cart_size',
			[
				'label' => __('Icon size', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-cart--default .bt-mini-cart a svg ' => 'width: {{SIZE}}px;height:{{SIZE}}px;',
				],
			]
		);
		$this->add_control(
			'icon_cart_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-cart--default .bt-mini-cart a svg path' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'icon_cart_background',
			[
				'label' => __('Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-cart--default .bt-mini-cart a' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'number_cart',
			[
				'label' => __('Number Cart', 'somnia'),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'number_cart_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-cart--default .bt-mini-cart span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'number_cart_background',
			[
				'label' => __('Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-cart--default .bt-mini-cart span' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'number_cart_typography',
				'label' => __('Typography', 'somnia'),
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-elwg-mini-cart--default .bt-mini-cart span',
			]
		);
		$this->add_control(
			'text_cart',
			[
				'label' => __('Text', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'text_cart_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-cart--default .bt-mini-cart .bt-text-label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_cart_typography',
				'label' => __('Typography', 'somnia'),
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-elwg-mini-cart--default .bt-mini-cart .bt-text-label',
			]
		);
		$this->add_responsive_control(
			'text_cart_spacing',
			[
				'label' => __('Spacing', 'somnia'),
				'type' => Controls_Manager::SLIDER,

				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-cart--default .bt-mini-cart' => 'gap: {{SIZE}}px;',
				],
			]
		);
		$this->end_controls_section();
	}

	protected function register_controls()
	{
		$this->register_content_section_controls();
		$this->register_style_content_section_controls();
	}

	/**
	 * Render mini cart sidebar
	 */
	public function render_mini_cart_sidebar()
	{
		if (!class_exists('WooCommerce')) {
			return;
		}
		
		$free_shipping_threshold = somnia_get_free_shipping_minimum_amount();
		$cart_total = WC()->cart->get_cart_contents_total();
		$currency_symbol = get_woocommerce_currency_symbol();
		if ($cart_total < $free_shipping_threshold) {
			$amount_left = $free_shipping_threshold - $cart_total;

			$percentage = ($cart_total / $free_shipping_threshold) * 100;
			$message = sprintf(
				__('<p class="bt-buy-more">Buy <span>%1$s%2$.2f</span> more to get <span>Free Shipping</span></p>', 'somnia'),
				$currency_symbol,
				$amount_left
			);
		} else {
			$percentage = 100;
			$message = __('<p class="bt-congratulation"> Congratulations! You have free shipping!</p>', 'somnia');
		}
		
		$class_sidebar = ($free_shipping_threshold > 0) ? 'bt-show-free-shipping' : '';

		?>
		<div class="bt-mini-cart-sidebar <?php echo esc_attr($class_sidebar); ?>">
			<div class="bt-mini-cart-sidebar-overlay"></div>
			<div class="bt-mini-cart-sidebar-content">
				<div class="bt-mini-cart-sidebar-header">
					<h4><?php echo esc_html__('Shopping Cart', 'somnia'); ?><span class="cart_total"><?php echo WC()->cart->get_cart_contents_count(); ?> </span></h4>
					<button class="bt-mini-cart-close">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
							<path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</button>
				</div>
			
				<div class="bt-mini-cart-sidebar-body">
				<?php if ($free_shipping_threshold > 0) {
				?>
					<div class="bt-progress-content <?php echo (WC()->cart->is_empty()) ? 'bt-hide' : ''; ?>">
						<?php echo '<div id="bt-free-shipping-message">' . $message . '</div>'; ?>
						<div class="bt-progress-container-cart">
							<div class="bt-progress-bar" data-width="<?php echo esc_attr($percentage) ?>">
								<div class="bt-icon-shipping">
									<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
										<g clip-path="url(#clip0_2134_37062)">
											<path d="M14.375 6.25H17.7016C17.8261 6.24994 17.9478 6.28709 18.0511 6.35669C18.1544 6.42629 18.2345 6.52517 18.2812 6.64063L19.375 9.375" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
											<path d="M1.875 11.25H14.375" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
											<path d="M15 16.875C16.0355 16.875 16.875 16.0355 16.875 15C16.875 13.9645 16.0355 13.125 15 13.125C13.9645 13.125 13.125 13.9645 13.125 15C13.125 16.0355 13.9645 16.875 15 16.875Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
											<path d="M6.25 16.875C7.28553 16.875 8.125 16.0355 8.125 15C8.125 13.9645 7.28553 13.125 6.25 13.125C5.21447 13.125 4.375 13.9645 4.375 15C4.375 16.0355 5.21447 16.875 6.25 16.875Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
											<path d="M13.125 15H8.125" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
											<path d="M14.375 9.375H19.375V14.375C19.375 14.5408 19.3092 14.6997 19.1919 14.8169C19.0747 14.9342 18.9158 15 18.75 15H16.875" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
											<path d="M4.375 15H2.5C2.33424 15 2.17527 14.9342 2.05806 14.8169C1.94085 14.6997 1.875 14.5408 1.875 14.375V5.625C1.875 5.45924 1.94085 5.30027 2.05806 5.18306C2.17527 5.06585 2.33424 5 2.5 5H14.375V13.232" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
										</g>
										<defs>
											<clipPath id="clip0_2134_37062">
												<rect width="20" height="20" fill="white" />
											</clipPath>
										</defs>
									</svg>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
					<div class="widget_shopping_cart_content">
						<?php woocommerce_mini_cart(); ?>
					</div>

				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Hook mini cart sidebar to wp_footer (only once)
	 */
	private function hook_sidebar_to_footer()
	{
		if (!self::$sidebar_hooked) {
			add_action('wp_footer', array($this, 'render_mini_cart_sidebar'));
			self::$sidebar_hooked = true;
		}
	}
	protected function render()
	{
		if (!class_exists('WooCommerce')) {
			return;
		}
		
		$settings = $this->get_settings_for_display();
		$icon_cart = $settings['cart_mini_icon']['url'];

		// Hook sidebar to footer if enabled and not on cart page
		if ('yes' === $settings['enable_sidebar_cart'] && !is_cart()) {
			$this->hook_sidebar_to_footer();
		}
	?>
		<div class="bt-elwg-mini-cart--default">
			<div class="bt-mini-cart">
				<a class="bt-toggle-btn <?php echo ('yes' === $settings['enable_sidebar_cart'] && !is_cart()) ? 'js-cart-sidebar' : ''; ?>" href="<?php echo esc_url(wc_get_cart_url()) ?>">
					<?php if (!empty($icon_cart) && 'svg' === pathinfo($icon_cart, PATHINFO_EXTENSION)) {
						$response = wp_safe_remote_get( $icon_cart, array(
							'timeout' => 20,
							'headers' => array(
								'User-Agent' => 'Mozilla/5.0 (compatible; WordPress)',
							),
						) );
						if ( is_wp_error( $response ) ) {
							return;
						}
						echo wp_remote_retrieve_body( $response );
					} else { ?>
						<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="currentColor">
							<path d="M21.4893 19.6753L20.1525 6.42531C20.1091 6.05877 19.9321 5.72108 19.6554 5.47685C19.3786 5.23262 19.0215 5.09901 18.6525 5.10156H15.4996C15.4996 3.90809 15.0255 2.7635 14.1816 1.91958C13.3377 1.07567 12.1931 0.601563 10.9996 0.601562C9.80616 0.601563 8.66157 1.07567 7.81766 1.91958C6.97374 2.7635 6.49964 3.90809 6.49964 5.10156H3.34308C2.97399 5.09901 2.61691 5.23262 2.34016 5.47685C2.06342 5.72108 1.88644 6.05877 1.84308 6.42531L0.506201 19.6753C0.481702 19.8853 0.501861 20.0981 0.565357 20.2997C0.628852 20.5013 0.734249 20.6873 0.874639 20.8453C1.01604 21.004 1.18932 21.1311 1.38317 21.2184C1.57701 21.3056 1.78707 21.351 1.99964 21.3516H19.9921C20.206 21.352 20.4175 21.3072 20.6127 21.2199C20.8079 21.1326 20.9824 21.005 21.1246 20.8453C21.2644 20.687 21.3691 20.5009 21.4319 20.2993C21.4948 20.0976 21.5143 19.885 21.4893 19.6753ZM10.9996 2.10156C11.7953 2.10156 12.5583 2.41763 13.121 2.98024C13.6836 3.54285 13.9996 4.30591 13.9996 5.10156H7.99964C7.99964 4.30591 8.31571 3.54285 8.87832 2.98024C9.44093 2.41763 10.204 2.10156 10.9996 2.10156ZM1.99964 19.8516L3.34308 6.60156H6.49964V8.85156C6.49964 9.05048 6.57866 9.24124 6.71931 9.38189C6.85996 9.52254 7.05073 9.60156 7.24964 9.60156C7.44855 9.60156 7.63932 9.52254 7.77997 9.38189C7.92062 9.24124 7.99964 9.05048 7.99964 8.85156V6.60156H13.9996V8.85156C13.9996 9.05048 14.0787 9.24124 14.2193 9.38189C14.36 9.52254 14.5507 9.60156 14.7496 9.60156C14.9486 9.60156 15.1393 9.52254 15.28 9.38189C15.4206 9.24124 15.4996 9.05048 15.4996 8.85156V6.60156H18.6637L19.9921 19.8516H1.99964Z"></path>
						</svg>
					<?php } ?>
					<span class="cart_total"><?php echo WC()->cart->get_cart_contents_count(); ?></span></a>
				<?php if (!empty($settings['cart_text'])) { ?>
					<a class="bt-text-label <?php echo ('yes' === $settings['enable_sidebar_cart'] && !is_cart()) ? 'js-cart-sidebar' : ''; ?>" href="<?php echo esc_url(wc_get_cart_url()) ?>"><?php echo esc_html($settings['cart_text']); ?></a>
				<?php } ?>
			</div>
		</div>
<?php
	}

	protected function content_template() {}
}
