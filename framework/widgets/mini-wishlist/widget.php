<?php

namespace SomniaElementorWidgets\Widgets\MiniWishlist;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_MiniWishlist extends Widget_Base
{

	public function get_name()
	{
		return 'bt-mini-wishlist';
	}

	public function get_title()
	{
		return __('Mini Wishlist', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-heart-o';
	}

	public function get_categories()
	{
		return ['somnia'];
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
			'wishlist_mini_icon',
			[
				'label' => esc_html__('Icon Wishlist', 'somnia'),
				'type' => Controls_Manager::MEDIA,
				'media_types' => ['svg'],
			]
		);
		$this->add_control(
			'wishlist_text',
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
			'section_style_content_wishlist',
			[
				'label' => esc_html__('Content Wishlist', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_wishlist',
			[
				'label' => __('Icon', 'somnia'),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_responsive_control(
			'icon_wishlist_size',
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
					'{{WRAPPER}} .bt-elwg-mini-wishlist--default .bt-mini-wishlist a svg ' => 'width: {{SIZE}}px;height:{{SIZE}}px;',
				],
			]
		);
		$this->add_control(
			'icon_wishlist_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-wishlist--default .bt-mini-wishlist a svg path' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'icon_wishlist_background',
			[
				'label' => __('Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-wishlist--default .bt-mini-wishlist a' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'number_wishlist',
			[
				'label' => __('Number Wishlist', 'somnia'),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'number_wishlist_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-wishlist--default .bt-mini-wishlist span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'number_wishlist_background',
			[
				'label' => __('Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-wishlist--default .bt-mini-wishlist span' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'number_wishlist_typography',
				'label' => __('Typography', 'somnia'),
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-elwg-mini-wishlist--default .bt-mini-wishlist span',
			]
		);
		$this->add_control(
			'text_wishlist',
			[
				'label' => __('Text', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'text_wishlist_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-mini-wishlist--default .bt-mini-wishlist .bt-text-label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_wishlist_typography',
				'label' => __('Typography', 'somnia'),
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-elwg-mini-wishlist--default .bt-mini-wishlist .bt-text-label',
			]
		);
		$this->add_responsive_control(
			'text_wishlist_spacing',
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
					'{{WRAPPER}} .bt-elwg-mini-wishlist--default .bt-mini-wishlist' => 'gap: {{SIZE}}px;',
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
		
		$icon_wishlist = !empty($settings['wishlist_mini_icon']['url']) ? $settings['wishlist_mini_icon']['url'] : '';

		// Get wishlist page URL from ACF options
		$wishlist_url = home_url('/products-wishlist/'); // Default fallback URL

		if (function_exists('get_field')) {
			$wishlist = get_field('wishlist', 'options');
			// Use custom wishlist page URL if set in options
			if ($wishlist && isset($wishlist['page_wishlist']) && $wishlist['page_wishlist'] != '') {
				$wishlist_url = get_permalink($wishlist['page_wishlist']);
			}
		}
?>
		<div class="bt-elwg-mini-wishlist--default">
			<div class="bt-mini-wishlist">
				<a class="bt-toggle-btn" href="<?php echo esc_url($wishlist_url); ?>">
					<?php if (!empty($icon_wishlist) && 'svg' === pathinfo($icon_wishlist, PATHINFO_EXTENSION)) {
						$response = wp_safe_remote_get( $icon_wishlist, array(
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
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
							<path d="M16.6875 3.75C14.7516 3.75 13.0566 4.5825 12 5.98969C10.9434 4.5825 9.24844 3.75 7.3125 3.75C5.77146 3.75174 4.29404 4.36468 3.20436 5.45436C2.11468 6.54404 1.50174 8.02146 1.5 9.5625C1.5 16.125 11.2303 21.4369 11.6447 21.6562C11.7539 21.715 11.876 21.7458 12 21.7458C12.124 21.7458 12.2461 21.715 12.3553 21.6562C12.7697 21.4369 22.5 16.125 22.5 9.5625C22.4983 8.02146 21.8853 6.54404 20.7956 5.45436C19.706 4.36468 18.2285 3.75174 16.6875 3.75ZM12 20.1375C10.2881 19.14 3 14.5959 3 9.5625C3.00149 8.41921 3.45632 7.32317 4.26475 6.51475C5.07317 5.70632 6.16921 5.25149 7.3125 5.25C9.13594 5.25 10.6669 6.22125 11.3062 7.78125C11.3628 7.91881 11.4589 8.03646 11.5824 8.11926C11.7059 8.20207 11.8513 8.24627 12 8.24627C12.1487 8.24627 12.2941 8.20207 12.4176 8.11926C12.5411 8.03646 12.6372 7.91881 12.6937 7.78125C13.3331 6.21844 14.8641 5.25 16.6875 5.25C17.8308 5.25149 18.9268 5.70632 19.7353 6.51475C20.5437 7.32317 20.9985 8.41921 21 9.5625C21 14.5884 13.71 19.1391 12 20.1375Z" />
						</svg>
					<?php } ?>
					<span class="wishlist_total"><?php echo count(WC()->session->get('productwishlistlocal', [])); ?></span></a>
				<?php if (!empty($settings['wishlist_text'])) {
					echo '<a href="' . esc_url($wishlist_url) . '" class="bt-text-label">' . esc_html($settings['wishlist_text']) . '</a>';
				} ?>
			</div>
		</div>
<?php
	}

	protected function content_template() {}
}
