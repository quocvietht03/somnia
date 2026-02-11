<?php

namespace SomniaElementorWidgets\Widgets\ProductTestimonialSlider;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Plugin;
use ElementorPro\Base\Base_Carousel_Trait;

class Widget_ProductTestimonialSlider extends Widget_Base
{
    use Base_Carousel_Trait;

    public function get_name()
    {
        return 'bt-product-testimonial-slider';
    }

    public function get_title()
    {
        return __('Product Testimonial Slider', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-product-rating';
    }

    public function get_categories()
    {
        return ['somnia'];
    }

    public function get_script_depends()
    {
        return ['swiper-slider', 'elementor-widgets'];
    }
    protected function get_supported_products()
    {
        $products = wc_get_products([
			'limit' => -1,
			'status' => 'publish',
		]);

		$options = [];
		foreach ( $products as $product ) {
			$options[ $product->get_id() ] = $product->get_name();
		}

		return $options;
    }
    protected function register_layout_section_controls()
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Testimonial', 'somnia'),
            ]
        );


        $repeater = new Repeater();

        $repeater->add_control(
            'testimonial_image',
            [
                'label' => __('Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'testimonial_title',
            [
                'label' => __('Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('What Our Customers Say', 'somnia'),
                'placeholder' => __('Type your testimonial title', 'somnia'),
            ]
        );

        $repeater->add_control(
            'testimonial_text',
            [
                'label' => __('Testimonial Text', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 5,
                'default' => __('This is a sample testimonial quote. It helps visualize how customer feedback will appear here.', 'somnia'),
                'placeholder' => __('Type your testimonial text', 'somnia'),
            ]
        );

        $repeater->add_control(
            'testimonial_rating',
            [
                'label' => __('Rating', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => '1 Star',
                    '2' => '2 Stars',
                    '3' => '3 Stars',
                    '4' => '4 Stars',
                    '5' => '5 Stars',
                ],
                'default' => '5',
            ]
        );

        $repeater->add_control(
            'testimonial_author',
            [
                'label' => __('Author', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('John Doe', 'somnia'),
                'placeholder' => __('Enter author name', 'somnia'),
            ]
        );

        $options = $this->get_supported_products();
		$repeater->add_control(
			'product_id',
			[
				'label'       => esc_html__( 'Select Products', 'somnia' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $options, // id => title
				'multiple'    => false,
				'label_block' => true,
				'default'     => ! empty( $options ) ? array_key_first( $options ) : '',
			]
		);

        $this->add_control(
            'testimonial_items',
            [
                'label' => __('Testimonial', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'testimonial_title' => __('Perfect neck support and versatile for lounging.', 'somnia'),
                    ],
                    [
                        'testimonial_title' => __('Incredibly soft and holds its shape perfectly.', 'somnia'),
                    ],
                    [
                        'testimonial_title' => __('Reduces neck pain while providing great comfort.', 'somnia'),
                    ],
                    [
                        'testimonial_title' => __('Cozy and firm, ideal for back support and relaxation.', 'somnia'),
                    ],
                ],
                'title_field' => '{{{ testimonial_title }}}',
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
            'enable_text_limit',
            [
                'label' => esc_html__( 'Enable Text Limit', 'somnia' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'text_line_limit',
            [
                'label' => esc_html__( 'Text Limit Lines', 'somnia' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--text' => '-webkit-line-clamp: {{SIZE}};display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;',
                ],
                'condition' => [
                    'enable_text_limit' => 'yes'
				],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_slider',
            [
                'label' => __('Slider', 'somnia'),
            ]
        );

        $this->add_control(
            'slider_autoplay',
            [
                'label' => __('Autoplay', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'no',
            ]
        );

        $this->add_control(
            'slider_autoplay_delay',
            [
                'label' => __('Autoplay Delay', 'somnia'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
                'min' => 1000,
                'max' => 10000,
                'step' => 500,
                'condition' => [
                    'slider_autoplay' => 'yes',
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
            ]
        );
        $this->add_carousel_layout_controls([
            'css_prefix' => '',
            'slides_to_show_custom_settings' => [
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'selectors' => [
                    '{{WRAPPER}}' => '--swiper-slides-to-display: {{VALUE}}',
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
            'slides_on_display' => 5,
        ]);

        $this->add_responsive_control(
            'image_spacing_custom',
            [
                'label' => esc_html__('Gap between slides', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'max' => 400,
                    ],
                ],
                'default' => [
                    'size' => 30,
                ],

                'render_type' => 'template',
                'selectors' => [
                    '{{WRAPPER}}' => '--swiper-slides-gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'slider_speed',
            [
                'label' => __('Slider Speed', 'somnia'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
                'min' => 100,
                'step' => 100,
            ]
        );

        $this->add_control(
            'slider_arrows',
            [
                'label' => __('Show Arrows', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'yes',
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
            ]
        );

        $this->add_responsive_control(
            'slider_offset_width',
            [
                'label' => __('Offset Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'default' => [
                    'size' => 80,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-product-testimonial--style-1' => '--slider-offset-width: {{SIZE}}{{UNIT}};',
                ],
                'render_type' => 'ui',
                'condition' => [
                    'slider_offset_sides!' => 'none',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_section_controls()
    {
        // Content Style Section
        $this->start_controls_section(
            'section_layout_style',
            [
                'label' => __('Layout', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __('Content Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'border',
                'label'    => esc_html__( 'Border', 'somnia' ),
                'selector' => '{{WRAPPER}} .bt-product-testimonial-item',
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'somnia' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bt-product-testimonial-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_style',
            [
                'label' => __('Content', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_heading',
            [
                'label' => __('Testimonial Title', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .bt-product-testimonial-item--title',
            ]
        );

        $this->add_control(
            'text_heading',
            [
                'label' => __('Testimonial Text', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .bt-product-testimonial-item--text',
            ]
        );

        $this->add_control(
            'author_heading',
            [
                'label' => __('Author', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'author_color',
            [
                'label' => __('Author Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--author' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'author_typography',
                'selector' => '{{WRAPPER}} .bt-product-testimonial-item--author',
            ]
        );

        $this->add_control(
            'rating_heading',
            [
                'label' => __('Rating', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'rating_color',
            [
                'label' => __('Star Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--rating .star.filled svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'rating_empty_color',
            [
                'label' => __('Empty Star Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--rating .star:not(.filled) svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'line_heading',
            [
                'label' => __('Line', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'line_color',
            [
                'label' => __('Line Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-mini-item' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'product_heading',
            [
                'label' => __('Product', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'prd_name_color',
            [
                'label' => __('Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-mini-item--title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'prd_typography',
                'label' => __('Title Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-product-mini-item--title',
            ]
        );
        
        $this->end_controls_section();

        // Navigation Arrows Style Section
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
        $this->add_control(
            'arrows_position_heading',
            [
                'label' => __('Position', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'arrow_left_position',
            [
                'label' => __('Left Arrow Position', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -50,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-nav.bt-button-prev' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_right_position',
            [
                'label' => __('Right Arrow Position', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -100,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -50,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-nav.bt-button-next' => 'right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        // Navigation Dots Style Section
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
        $this->register_layout_section_controls();
        $this->register_style_section_controls();
    }
    protected function render()
    {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        $settings = $this->get_settings_for_display();

        $classes = ['bt-elwg-product-testimonial--style-1'];
        if ($settings['slider_arrows_hidden_mobile'] === 'yes') {
            $classes[] = 'bt-hidden-arrow-mobile';
        }
        if ($settings['slider_dots_only_mobile'] === 'yes') {
            $classes[] = 'bt-only-dot-mobile';
        }

        $breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
        
        $slider_settings = somnia_elwg_get_slider_settings($settings, $breakpoints);
        
        $slider_settings['autoplay_delay'] = isset($settings['slider_autoplay_delay']) ? $settings['slider_autoplay_delay'] : 3000;
?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?> bt-slider-offset-sides-<?php echo esc_attr($settings['slider_offset_sides']); ?> js-data-product-testimonial-slider" data-slider-settings='<?php echo json_encode($slider_settings); ?>'>
            <div class="bt-product-testimonial">
                <div class="swiper js-testimonial-slider">
                    <div class="swiper-wrapper">
                        <?php if (!empty($settings['testimonial_items'])) : ?>
                            <?php foreach ($settings['testimonial_items'] as $item) : ?>
                                <div class="swiper-slide">
                                    <div class="bt-product-testimonial-item">
                                        <div class="bt-product-testimonial-item--image">
                                            <div class="bt-cover-image">
                                                <?php
                                                if (!empty($item['testimonial_image']['id'])) {
                                                    echo wp_get_attachment_image($item['testimonial_image']['id'], $settings['thumbnail_size']);
                                                } else {
                                                    if (!empty($item['testimonial_image']['url'])) {
                                                        echo '<img src="' . esc_url($item['testimonial_image']['url']) . '" alt="' . esc_html__('Awaiting testimonial image', 'somnia') . '">';
                                                    } else {
                                                        echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting testimonial image', 'somnia') . '">';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="bt-product-testimonial-item--content">
                                            <div class="bt-product-testimonial-item--inner">
                                                <?php if (!empty($item['testimonial_rating'])) : ?>
                                                    <div class="bt-product-testimonial-item--rating">
                                                        <?php
                                                        $rating = intval($item['testimonial_rating']);
                                                        for ($i = 1; $i <= 5; $i++) :
                                                            $filled_class = ($i <= $rating) ? 'filled' : '';
                                                        ?>
                                                            <span class="star <?php echo esc_attr($filled_class); ?>">
                                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10 1.66667L12.575 6.88334L18.3333 7.725L14.1667 11.7833L15.15 17.5167L10 14.8083L4.85 17.5167L5.83333 11.7833L1.66667 7.725L7.425 6.88334L10 1.66667Z" />
                                                                </svg>
                                                            </span>
                                                        <?php endfor; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($item['testimonial_title'])) : ?>
                                                    <h4 class="bt-product-testimonial-item--title"><?php echo esc_html($item['testimonial_title']); ?></h4>
                                                <?php endif; ?>
                                                <?php if (!empty($item['testimonial_text'])) : ?>
                                                    <div class="bt-product-testimonial-item--text"><?php echo esc_html($item['testimonial_text']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($item['testimonial_author'])) : ?>
                                                    <div class="bt-product-testimonial-item--author"><?php echo esc_html($item['testimonial_author']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($item['product_id'])) : 
                                                    $product = wc_get_product($item['product_id']);
                                                ?>
                                                <div class="bt-product-mini-item">
                                                <a class="bt-product-mini-item--link" href="<?php echo esc_url($product->get_permalink()); ?>">
                                                    <div class="bt-product-mini-item--image">
                                                        <?php
                                                            if (has_post_thumbnail($item['product_id'])) {
                                                                echo get_the_post_thumbnail($item['product_id'], 'thumbnail');
                                                            } else {
                                                                echo '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '" class="wp-post-image" />';
                                                            }
                                                        ?>
                                                    </div>
                                                    <h4 class="bt-product-mini-item--title">
                                                        <?php echo esc_html($product->get_name()); ?>
                                                    </h4>
                                                </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($settings['slider_arrows'] === 'yes') : ?>
                    <div class="bt-swiper-navigation">
                        <div class="bt-nav bt-button-prev">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="currentColor">
                                <path d="M23.2968 28.4547C23.4013 28.5592 23.4843 28.6833 23.5408 28.8199C23.5974 28.9564 23.6265 29.1028 23.6265 29.2506C23.6265 29.3984 23.5974 29.5448 23.5408 29.6814C23.4843 29.818 23.4013 29.942 23.2968 30.0466C23.1923 30.1511 23.0682 30.234 22.9316 30.2906C22.7951 30.3471 22.6487 30.3763 22.5009 30.3763C22.3531 30.3763 22.2067 30.3471 22.0701 30.2906C21.9336 30.234 21.8095 30.1511 21.7049 30.0466L10.4549 18.7966C10.3503 18.6921 10.2674 18.568 10.2108 18.4314C10.1541 18.2949 10.125 18.1485 10.125 18.0006C10.125 17.8528 10.1541 17.7064 10.2108 17.5698C10.2674 17.4332 10.3503 17.3092 10.4549 17.2047L21.7049 5.95469C21.916 5.74359 22.2024 5.625 22.5009 5.625C22.7994 5.625 23.0857 5.74359 23.2968 5.95469C23.5079 6.16578 23.6265 6.45209 23.6265 6.75063C23.6265 7.04916 23.5079 7.33547 23.2968 7.54656L12.8414 18.0006L23.2968 28.4547Z"/>
                            </svg>
                        </div>
                        <div class="bt-nav bt-button-next">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="currentColor">
                                <path d="M25.5466 18.7966L14.2966 30.0466C14.192 30.1511 14.068 30.234 13.9314 30.2906C13.7948 30.3471 13.6484 30.3763 13.5006 30.3763C13.3528 30.3763 13.2064 30.3471 13.0699 30.2906C12.9333 30.234 12.8092 30.1511 12.7047 30.0466C12.6002 29.942 12.5173 29.818 12.4607 29.6814C12.4041 29.5448 12.375 29.3984 12.375 29.2506C12.375 29.1028 12.4041 28.9564 12.4607 28.8199C12.5173 28.6833 12.6002 28.5592 12.7047 28.4547L23.1602 18.0006L12.7047 7.54656C12.4936 7.33547 12.375 7.04916 12.375 6.75063C12.375 6.45209 12.4936 6.16578 12.7047 5.95469C12.9158 5.74359 13.2021 5.625 13.5006 5.625C13.7992 5.625 14.0855 5.74359 14.2966 5.95469L25.5466 17.2047C25.6512 17.3092 25.7341 17.4332 25.7908 17.5698C25.8474 17.7064 25.8765 17.8528 25.8765 18.0006C25.8765 18.1485 25.8474 18.2949 25.7908 18.4314C25.7341 18.568 25.6512 18.6921 25.5466 18.7966Z"/>
                            </svg>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($settings['slider_dots'] === 'yes') : ?>
                    <div class="bt-swiper-pagination swiper-pagination"></div>
                <?php endif; ?>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}