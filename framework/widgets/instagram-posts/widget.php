<?php

namespace SomniaElementorWidgets\Widgets\InstagramPosts;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Plugin;
use ElementorPro\Base\Base_Carousel_Trait;

class Widget_InstagramPosts extends Widget_Base
{
	use Base_Carousel_Trait;
	public function get_name()
	{
		return 'bt-instagram-posts';
	}

	public function get_title()
	{
		return __('Instagram Posts', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-instagram-post';
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
				'label' => __('Content', 'somnia'),
			]
		);


		$this->add_control(
			'gallery',
			[
				'label' => esc_html__('Add Images', 'somnia'),
				'type' => Controls_Manager::GALLERY,
				'show_label' => false,
				'default' => [],
			]
		);
		$this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'label' => __('Image Size', 'somnia'),
                'show_label' => true,
                'default' => 'medium_large',
                'exclude' => ['custom'],
            ]
        );
		$this->add_control(
			'open_type',
			[
				'label' => __('On Click', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'default' => 'popup',
				'options' => [
					'popup' => __('Popup', 'somnia'),
					'link' => __('Link', 'somnia'),
				],
				'description' => __('Choose what happens when an Instagram image is clicked: open in a popup or go to the Instagram link.', 'somnia'),
			]
		);
		$this->add_control(
			'link',
			[
				'label' => __('Link', 'somnia'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'somnia'),
				'default' => [
					'url' => 'https://www.instagram.com/',
					'is_external' => false,
					'nofollow' => false,
				],
				'condition' => [
					'open_type' => 'link',
				],
			]
		);
		$this->add_control(
			'layout',
			[
				'label' => __('Layout', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'somnia'),
					'tilted' => __('Tilted', 'somnia'),
				],
				'description' => __('Choose the layout style for the Instagram posts.', 'somnia'),
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider',
			[
				'label' => esc_html__('Slider', 'somnia'),
			]
		);

		$this->add_control(
			'enable_slider',
			[
				'label' => __('Enable Slider', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'default' => 'no',
			]
		);

		$this->add_control(
			'slider_autoplay',
			[
				'label' => __('Slider Autoplay', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'default' => 'no',
				'condition' => [
					'enable_slider' => 'yes',
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
					'enable_slider' => 'yes',
				],
			]
		);
		$this->add_carousel_layout_controls( [
			'css_prefix' => '',
			'slides_to_show_custom_settings' => [
				'default' => '5',
				'tablet_default' => '3',
				'mobile_default' => '2',
				'selectors' => [
					'{{WRAPPER}}' => '--swiper-slides-to-display: {{VALUE}}',
				],
				'condition' => [
					'enable_slider' => 'yes',
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
			'slides_on_display' => 6,
		] );
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
					'enable_slider' => 'yes',
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
					'enable_slider' => 'yes',
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
					'enable_slider' => 'yes',
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
					'enable_slider' => 'yes',
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
					'enable_slider' => 'yes',
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
					'enable_slider' => 'yes',
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
					'enable_slider' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'slider_offset_width',
			[
				'label' => __('Offset Width', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
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
					'{{WRAPPER}} .bt-elwg-instagram-posts' => '--slider-offset-width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'ui',
				'condition' => [
					'enable_slider' => 'yes',
					'slider_offset_sides!' => 'none',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'somnia'),
				'condition' => [
					'enable_slider!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __('Columns', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'default' => '6',
				'options' => [
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'desktop_default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'selectors' => [
					'{{WRAPPER}} .bt-ins-posts--grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => __('Gap', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 16,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-ins-posts--grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_content_section_controls()
	{
		$this->start_controls_section(
			'section_style',
			[
				'label' => __('Style', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => __('Border Radius', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bt-ins-posts--image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __('Icon Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-icon-view svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label' => __('Icon Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-icon-view' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label' => __('Icon Hover Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-icon-view:hover svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_hover_bg_color',
			[
				'label' => __('Icon Hover Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-icon-view:hover' => 'background-color: {{VALUE}};',
				],
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
					'enable_slider' => 'yes',
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
					'enable_slider' => 'yes',
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
		$this->register_style_content_section_controls();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		if (empty($settings['gallery'])) {
			return;
		}
		
		$classes = ['bt-elwg-instagram-posts'];
		if ($settings['layout'] === 'tilted') {
			$classes[] = 'bt-layout-tilted';
		}
		if ($settings['enable_slider'] === 'yes') {
			$classes[] = 'bt-elwg-instagram-posts--slider';
			if ($settings['slider_arrows_hidden_mobile'] === 'yes') {
				$classes[] = 'bt-hidden-arrow-mobile';
			}
			if ($settings['slider_dots_only_mobile'] === 'yes') {
				$classes[] = 'bt-only-dot-mobile';
			}
		}

		$slider_settings = [];
		if ($settings['enable_slider'] === 'yes') {
			$breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
			$slider_settings = bt_elwg_get_slider_settings($settings, $breakpoints);
		}

		$data_slider_settings = $settings['enable_slider'] === 'yes' ? esc_attr(json_encode($slider_settings)) : '';
		?>
		<div class="<?php echo esc_attr(implode(' ', $classes)); ?> bt-slider-offset-sides-<?php echo esc_attr($settings['slider_offset_sides']); ?>" data-slider-settings="<?php echo esc_attr($data_slider_settings); ?>">
			<?php if ($settings['enable_slider'] === 'yes') : ?>
				<div class="swiper">
					<div class="swiper-wrapper">
						<?php foreach ($settings['gallery'] as $item) : 
							$image_link = $settings['open_type'] === 'popup' ? $item['url'] : $settings['link']['url'];
							?>
							<div class="swiper-slide">
								<div class="bt-ins-posts--image">
									<div class="bt-cover-image">
										<?php if (!empty($item['id'])) {
											echo wp_get_attachment_image($item['id'], $settings['thumbnail_size']);
										} else {
											echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting image', 'somnia') . '">';
										} ?>
									</div>
									<?php 
										if($settings['open_type'] === 'popup') {
										?>
											<a href="<?php echo esc_url($item['url']); ?>" class="bt-icon-view elementor-clickable" data-elementor-lightbox-slideshow="bt-gallery-ins">
										<?php
										} else {
										?>
											<a href="<?php echo esc_url($settings['link']['url']); ?>" class="bt-icon-view" target="_blank">
										<?php
										}
									?>
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
											<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 4H4m0 0v4m0-4 5 5m7-5h4m0 0v4m0-4-5 5M8 20H4m0 0v-4m0 4 5-5m7 5h4m0 0v-4m0 4-5-5"></path>
										</svg>
									</a>
								</div>
							</div>
						<?php endforeach; ?>
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
			<?php else : ?>
				<div class="bt-ins-posts--grid">
					<?php foreach ($settings['gallery'] as $item) : ?>
						<div class="bt-ins-posts--image">
							<div class="bt-cover-image">
								<?php if (!empty($item['id'])) {
									echo wp_get_attachment_image($item['id'], $settings['thumbnail_size']);
								} else {
									echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting image', 'somnia') . '">';
								} ?>
							</div>
							<?php 
								if($settings['open_type'] === 'popup') {
								?>
									<a href="<?php echo esc_url($item['url']); ?>" class="bt-icon-view elementor-clickable" data-elementor-lightbox-slideshow="bt-gallery-ins">
								<?php
								} else {
								?>
									<a href="<?php echo esc_url($settings['link']['url']); ?>" class="bt-icon-view" target="_blank">
								<?php
								}
							?>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
									<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 4H4m0 0v4m0-4 5 5m7-5h4m0 0v4m0-4-5 5M8 20H4m0 0v-4m0 4 5-5m7 5h4m0 0v-4m0 4-5-5"></path>
								</svg>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
<?php
	}

	protected function content_template() {}
}