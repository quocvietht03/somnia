<?php

namespace SomniaElementorWidgets\Widgets\BrandSlider;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Repeater;
use ElementorPro\Base\Base_Carousel_Trait;

class Widget_BrandSlider extends Widget_Base
{
	use Base_Carousel_Trait;
	public function get_name()
	{
		return 'bt-brand-slider';
	}

	public function get_title()
	{
		return __('Brand Slider', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-carousel';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	public function get_script_depends()
	{
		return ['swiper-slider', 'elementor-widgets'];
	}

	protected function register_content_section_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Brand Items', 'somnia'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'brand_image',
			[
				'label' => __('Brand Image', 'somnia'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'brand_link',
			[
				'label' => __('Brand Link', 'somnia'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'somnia'),
				'default' => [
					'url' => '#',
					'is_external' => false,
					'nofollow' => false,
				],
			]
		);

		$this->add_control(
			'brand_list',
			[
				'label' => __('Brand List', 'somnia'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'brand_link' => ['url' => '#'],
					],
					[
						'brand_link' => ['url' => '#'],
					],
					[
						'brand_link' => ['url' => '#'],
					],
				],
				'title_field' => __('Brand Item', 'somnia'),
			]
		);

		$this->end_controls_section();
	}
	protected function register_layout_section_controls()
	{
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'somnia'),
			]
		);
		$this->add_control(
			'slider_continuous',
			[
				'label' => __('Continuous Sliding', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'default' => 'no',
				'description' => __('Enable continuous sliding animation', 'somnia'),
			]
		);

		$this->add_control(
			'slider_continuous_speed',
			[
				'label' => __('Sliding Speed', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1000,
				'max' => 10000,
				'step' => 500,
				'default' => 3000,
				'description' => __('Set sliding speed in milliseconds', 'somnia'),
				'condition' => [
					'slider_continuous' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_continuous_direction',
			[
				'label' => __('Sliding Direction', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'default' => 'ltr',
				'options' => [
					'ltr' => __('Left', 'somnia'),
					'rtl' => __('Right', 'somnia'),
				],
				'condition' => [
					'slider_continuous' => 'yes',
				],
			]
		);
		// Repeater control will be added in content section
		$this->add_control(
			'slider_autoplay',
			[
				'label' => __('Slider Autoplay', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'default' => 'no',
				'condition' => [
					'slider_continuous!' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_loop',
			[
				'label' => __('Infinite Loop', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'default' => 'yes',
				'description' => __('Enable continuous loop mode', 'somnia'),
				'condition' => [
					'slider_continuous!' => 'yes',
				],
			]
		);
		$this->add_carousel_layout_controls([
			'css_prefix' => '',
			'slides_to_show_custom_settings' => [
				'default' => '5',
				'tablet_default' => '3',
				'mobile_default' => '2',
				'selectors' => [
					'{{WRAPPER}}' => '--swiper-slides-to-display: {{VALUE}}',
				],
				'condition' => [
					'slider_continuous!' => 'yes',
				],
			],
			'slides_to_scroll_custom_settings' => [
				'default' => '0',
				'condition' => [
					'slides_to_show_custom_settings' => 100,
				],
			],
			'equal_height_custom_settings' => [
				'selectors' => [
					'{{WRAPPER}} .swiper-slide > .elementor-element' => 'height: 100%',
				],
				'condition' => [
					'slides_to_show_custom_settings' => 100,
				],
			],
			'slides_on_display' => 7,
		]);
		$this->add_responsive_control(
			'image_spacing_custom',
			[
				'label' => esc_html__( 'Gap between slides', 'somnia' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 400,
					],
				],
				'default' => [
					'size' => 10,
				],

				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}}' => '--swiper-slides-gap: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'slider_continuous!' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_speed',
			[
				'label' => __('Slider Speed', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'default' => 1000,
				'min' => 100,
				'step' => 100,
				'condition' => [
					'slider_continuous!' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_arrows',
			[
				'label' => __('Show Arrows', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'condition' => [
					'slider_continuous!' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_arrows_hidden_mobile',
			[
				'label' => __('Hidden Arrow Mobile', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'default' => 'no',
				'condition' => [
					'slider_arrows' => 'yes',
					'slider_continuous!' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_dots',
			[
				'label' => __('Show Dots', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'default' => 'no',
				'condition' => [
					'slider_continuous!' => 'yes',
				],
			]
		);

		$this->add_control(
			'slider_dots_only_mobile',
			[
				'label' => __('Mobile-Only Pagination', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'default' => 'no',
				'condition' => [
					'slider_dots' => 'yes',
					'slider_continuous!' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_offset_sides',
			[
				'label' => __('Offset Sides', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __('None', 'somnia'),
					'both' => __('Both', 'somnia'),
					'left' => __('Left', 'somnia'),
					'right' => __('Right', 'somnia'),
				],
				'condition' => [
					'slider_continuous!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'slider_offset_width',
			[
				'label' => __('Offset Width', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => 80,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-brand-slider--default' => '--slider-offset-width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'ui',
				'condition' => [
					'slider_offset_sides!' => 'none',
					'slider_continuous!' => 'yes',
				],
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

		$this->add_control(
			'svg_color',
			[
				'label' => __('SVG Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .bt-brand-slider--item svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				'{{WRAPPER}} .bt-brand-slider--item svg path' => 'fill: {{VALUE}};',
			],
				'description' => __('Set color for SVG icons', 'somnia'),
			]
		);

		$this->add_control(
			'svg_hover_color',
			[
				'label' => __('SVG Hover Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .bt-brand-slider--item:hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				'{{WRAPPER}} .bt-brand-slider--item:hover svg path' => 'fill: {{VALUE}};',
			],
			]
		);

		$this->add_control(
			'item_background',
			[
				'label' => __('Item Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f6f6f6',
			'selectors' => [
				'{{WRAPPER}} .bt-brand-slider--item' => 'background: {{VALUE}};',
			],
			]
		);
		$this->add_control(
			'hover_background',
			[
				'label' => __('Hover Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .bt-brand-slider--item:hover' => 'background: {{VALUE}};',
			],
			]
		);
		$this->add_responsive_control(
			'item_padding',
			[
				'label' => __('Padding', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bt-brand-slider--item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'item_border_radius',
			[
				'label' => __('Border Radius', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
			'selectors' => [
				'{{WRAPPER}} .bt-brand-slider--item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'item_border',
				'label' => __('Border', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-brand-slider--item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bt-brand-slider--item',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_arrows',
			[
				'label' => esc_html__('Navigation Arrows', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __('Size', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 24,
				],
				'selectors' => [
					'{{WRAPPER}} .bt-nav svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('arrows_colors_tabs');

		// Normal state
		$this->start_controls_tab(
			'arrows_colors_normal',
			[
				'label' => __('Normal', 'somnia'),
			]
		);
		$this->add_control(
			'arrows_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .bt-nav svg path' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'arrows_bg_color',
			[
				'label' => __('Background Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.5)',
				'selectors' => [
					'{{WRAPPER}} .bt-nav' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();

		// Hover state
		$this->start_controls_tab(
			'arrows_colors_hover',
			[
				'label' => __('Hover', 'somnia'),
			]
		);
		$this->add_control(
			'arrows_color_hover',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .bt-nav:hover svg path' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'arrows_bg_color_hover',
			[
				'label' => __('Background Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.7)',
				'selectors' => [
					'{{WRAPPER}} .bt-nav:hover' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'arrows_border_radius',
			[
				'label' => __('Border Radius', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bt-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dots',
			[
				'label' => esc_html__('Navigation Dots', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_dots' => 'yes',
				],
			]
		);

		$this->add_control(
			'dots_spacing',
			[
				'label' => __('Spacing Dots', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 30,
					],
				],
				'default' => [
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label' => __('Size', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 50,
					],
				],
				'default' => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('dots_colors_tabs');

		// Normal state
		$this->start_controls_tab(
			'dots_colors_normal',
			[
				'label' => __('Normal', 'somnia'),
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '#0C2C48',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// Hover state
		$this->start_controls_tab(
			'dots_colors_hover',
			[
				'label' => __('Hover', 'somnia'),
			]
		);

		$this->add_control(
			'dots_color_hover',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}};opacity: 1;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'dots_spacing_slider',
			[
				'label' => __('Spacing', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .swiper' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}
	protected function register_controls()
	{
		$this->register_content_section_controls();
		$this->register_layout_section_controls();
		$this->register_style_section_controls();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$brand_list = $settings['brand_list'];
		
		$classes = ['bt-elwg-brand-slider--default bt-elwg-brand-slider--slider'];
		if ($settings['slider_arrows_hidden_mobile'] === 'yes') {
			$classes[] = 'bt-hidden-arrow-mobile';
		}
		if ($settings['slider_dots_only_mobile'] === 'yes') {
			$classes[] = 'bt-only-dot-mobile';
		}
		$slider_settings = [];
		$breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
		$slider_settings = somnia_elwg_get_slider_settings($settings, $breakpoints);
	 if($settings['slider_continuous'] === 'yes') {
		$slider_continuous = [];
		$slider_continuous['speed'] = $settings['slider_continuous_speed'];
		$slider_continuous['direction'] = $settings['slider_continuous_direction'];
	}
		if (!empty($brand_list)) {
?>
			<div class="<?php echo esc_attr(implode(' ', $classes)); ?> bt-slider-offset-sides-<?php echo esc_attr($settings['slider_offset_sides']); ?>" <?php if($settings['slider_continuous'] === 'yes') { ?> data-slider-continuous='<?php echo json_encode($slider_continuous); ?>' <?php } ?> data-slider-settings='<?php echo json_encode($slider_settings); ?>'>
				<div class="bt-brand-slider swiper">
					<div class="swiper-wrapper">
					<?php
					foreach ($brand_list as $index => $item) {
						$brand_image = $item['brand_image'];
						$brand_link = $item['brand_link'];
						$image_id = $brand_image['id'];
						$image_url = $brand_image['url'];
						$is_svg = false;

						// Check if the image is SVG
						if ($image_url && pathinfo($image_url, PATHINFO_EXTENSION) === 'svg') {
							$is_svg = true;
						}
						
						$link_key = 'link_' . $index;
						$this->add_link_attributes($link_key, $brand_link);
					?>
						<div class="bt-brand-slider--item swiper-slide">
							<?php if (!empty($brand_link['url'])) : ?>
								<a <?php $this->print_render_attribute_string($link_key); ?> class="bt-brand-slider--link">
							<?php else : ?>
								<div class="bt-brand-slider--link">
							<?php endif; ?>
									<div class="bt-brand-slider--image">
										<?php if ($is_svg && !empty($image_url)) {
											$response = wp_safe_remote_get( $image_url, array(
												'timeout' => 20,
												'headers' => array(
													'User-Agent' => 'Mozilla/5.0 (compatible; WordPress)',
												),
											) );
											if ( is_wp_error( $response ) ) {
												return;
											}
											echo '<div class="bt-svg">' . wp_remote_retrieve_body( $response ) . '</div>';
										} else {
											if (!empty($image_id)) {
												echo wp_get_attachment_image($image_id, 'medium');
											} else {
												echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_attr__('Brand Image', 'somnia') . '">';
											}
										}
										?>
									</div>
							<?php if (!empty($brand_link['url'])) : ?>
								</a>
							<?php else : ?>
								</div>
							<?php endif; ?>
						</div>
					<?php
					}
					?>
					</div>
				</div>
				<?php if ($settings['slider_arrows'] === 'yes') : ?>
					<div class="bt-swiper-navigation">
						<div class="bt-nav bt-button-prev">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M15.5307 18.9698C15.6004 19.0395 15.6557 19.1222 15.6934 19.2132C15.7311 19.3043 15.7505 19.4019 15.7505 19.5004C15.7505 19.599 15.7311 19.6965 15.6934 19.7876C15.6557 19.8786 15.6004 19.9614 15.5307 20.031C15.461 20.1007 15.3783 20.156 15.2873 20.1937C15.1962 20.2314 15.0986 20.2508 15.0001 20.2508C14.9016 20.2508 14.804 20.2314 14.7129 20.1937C14.6219 20.156 14.5392 20.1007 14.4695 20.031L6.96948 12.531C6.89974 12.4614 6.84443 12.3787 6.80668 12.2876C6.76894 12.1966 6.74951 12.099 6.74951 12.0004C6.74951 11.9019 6.76894 11.8043 6.80668 11.7132C6.84443 11.6222 6.89974 11.5394 6.96948 11.4698L14.4695 3.96979C14.6102 3.82906 14.8011 3.75 15.0001 3.75C15.1991 3.75 15.39 3.82906 15.5307 3.96979C15.6715 4.11052 15.7505 4.30139 15.7505 4.50042C15.7505 4.69944 15.6715 4.89031 15.5307 5.03104L8.56041 12.0004L15.5307 18.9698Z" fill="currentColor" />
							</svg>
						</div>
						<div class="bt-nav bt-button-next">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<path d="M17.0306 12.531L9.53055 20.031C9.46087 20.1007 9.37815 20.156 9.2871 20.1937C9.19606 20.2314 9.09847 20.2508 8.99993 20.2508C8.90138 20.2508 8.8038 20.2314 8.71276 20.1937C8.62171 20.156 8.53899 20.1007 8.4693 20.031C8.39962 19.9614 8.34435 19.8786 8.30663 19.7876C8.26892 19.6965 8.24951 19.599 8.24951 19.5004C8.24951 19.4019 8.26892 19.3043 8.30663 19.2132C8.34435 19.1222 8.39962 19.0395 8.4693 18.9698L15.4396 12.0004L8.4693 5.03104C8.32857 4.89031 8.24951 4.69944 8.24951 4.50042C8.24951 4.30139 8.32857 4.11052 8.4693 3.96979C8.61003 3.82906 8.80091 3.75 8.99993 3.75C9.19895 3.75 9.38982 3.82906 9.53055 3.96979L17.0306 11.4698C17.1003 11.5394 17.1556 11.6222 17.1933 11.7132C17.2311 11.8043 17.2505 11.9019 17.2505 12.0004C17.2505 12.099 17.2311 12.1966 17.1933 12.2876C17.1556 12.3787 17.1003 12.4614 17.0306 12.531Z" fill="currentColor" />
							</svg>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($settings['slider_dots'] === 'yes') : ?>
					<div class="bt-swiper-pagination swiper-pagination"></div>
				<?php endif; ?>
			</div>

<?php
		} else {
			echo '<div class="bt-brand-slider--empty">' . esc_html__('No brands found. Please add some brands in the widget settings.', 'somnia') . '</div>';
		}
	}

	protected function content_template() {}
}
