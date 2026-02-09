<?php

namespace SomniaElementorWidgets\Widgets\TestimonialsStaggeredSlider;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_BBorder;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;
use ElementorPro\Base\Base_Carousel_Trait;

class Widget_TestimonialsStaggeredSlider extends Widget_Base
{
    use Base_Carousel_Trait;
    public function get_name()
    {
        return 'bt-testimonials-staggered-slider';
    }

    public function get_title()
    {
        return __('Testimonials Staggered Slider', 'somnia');
    }
    public function get_icon()
    {
        return 'bt-bears-icon eicon-comments';
    }

    public function get_categories()
    {
        return ['somnia'];
    }

    public function get_script_depends()
    {
        return ['swiper-slider', 'elementor-widgets'];
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
                'label' => __('Testimonial Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control(
            'testimonial_text',
            [
                'label' => __('Testimonial Text', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 5,
                'default' => __('Enter testimonial text here', 'somnia'),
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

        // Position/Job title field
        $repeater->add_control(
            'testimonial_position',
            [
                'label' => __('Position/Job Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('CEO', 'somnia'),
                'placeholder' => __('Enter position or job title', 'somnia'),
                'description' => __('Enter the job title or position of the person giving the testimonial.', 'somnia'),
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
                        'testimonial_text' => __('Great product! Highly recommend it.', 'somnia'),
                        'testimonial_position' => __('CEO', 'somnia'),
                    ],
                    [
                        'testimonial_text' => __('Excellent quality and fast delivery.', 'somnia'),
                        'testimonial_position' => __('Marketing Manager', 'somnia'),
                    ],
                    [
                        'testimonial_text' => __('Best purchase I made this year!', 'somnia'),
                        'testimonial_position' => __('Product Manager', 'somnia'),
                    ],
                ],
                'title_field' => '{{{ testimonial_text }}}',
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
        $this->add_responsive_control(
            'image_ratio_even',
            [
                'label' => __('Image Ratio (Even)', 'somnia'),
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
                    '{{WRAPPER}} .swiper-wrapper .swiper-slide:nth-child(even) .bt-testimonial--image.bt-cover-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
                ],
            ]
        );
        $this->add_responsive_control(
            'image_ratio_odd',
            [
                'label' => __('Image Ratio (Odd)', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1.35,
                ],
                'range' => [
                    'px' => [
                        'min' => 0.3,
                        'max' => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-wrapper .swiper-slide:nth-child(odd) .bt-testimonial--image.bt-cover-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
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
                'label' => __('Slider Autoplay', 'somnia'),
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
                    'size' => 10,
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
                'default' => 1000,
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
                'default' => 'yes',
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
                    '{{WRAPPER}} .bt-elwg-testimonials-staggered--default' => '--slider-offset-width: {{SIZE}}{{UNIT}};',
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
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => esc_html__('Content', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'content_alignment',
            [
                'label' => __('Content Alignment', 'somnia'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'somnia'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'somnia'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'somnia'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .bt-testimonial--content' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __('Content Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .bt-testimonial--content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'content_background_color',
            [
                'label' => __('Content Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-testimonial--content' => 'background-color: {{VALUE}};',
                ],
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
                'selectors' => [
                    '{{WRAPPER}} .bt-testimonial--text' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .bt-testimonial--text',
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
                'selectors' => [
                    '{{WRAPPER}} .bt-testimonial--author' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'author_typography',
                'selector' => '{{WRAPPER}} .bt-testimonial--author',
            ]
        );
        $this->add_control(
            'position_heading',
            [
                'label' => __('Position/Job Title', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'position_color',
            [
                'label' => __('Position Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-testimonial--position' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'position_typography',
                'selector' => '{{WRAPPER}} .bt-testimonial--position',
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
                'selectors' => [
                    '{{WRAPPER}} .bt-testimonial--rating .star.filled svg path' => 'fill: {{VALUE}};',
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
                    'size' => 20,
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
        $settings = $this->get_settings_for_display();
?>
        <?php
        $classes = ['bt-elwg-testimonials-staggered--default', 'js-data-testimonials-staggered-slider'];
        if (!empty($settings['slider_arrows_hidden_mobile']) && $settings['slider_arrows_hidden_mobile'] === 'yes') {
            $classes[] = 'bt-hidden-arrow-mobile';
        }
        if (!empty($settings['slider_dots_only_mobile']) && $settings['slider_dots_only_mobile'] === 'yes') {
            $classes[] = 'bt-only-dot-mobile';
        }

        $breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
        $slider_settings = somnia_elwg_get_slider_settings($settings, $breakpoints);
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?> bt-slider-offset-sides-<?php echo esc_attr($settings['slider_offset_sides']); ?>" data-slider-settings='<?php echo esc_attr(json_encode($slider_settings)); ?>'>
            <div class="bt-testimonial js-testimonials-staggered-slider swiper">
                <div class="swiper-wrapper">
                    <?php if (!empty($settings['testimonial_items'])) : ?>
                        <?php foreach ($settings['testimonial_items'] as $item) : ?>
                            <div class="swiper-slide">
                                <div class="bt-testimonial--item">
                                    <div class="bt-testimonial--image bt-cover-image">
                                        <?php if (!empty($item['testimonial_image']['id'])) {
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
                                    <div class="bt-testimonial--content" data-col-item="<?php echo !empty($settings['slides_to_show']) ? esc_attr($settings['slides_to_show']) : 3; ?>">
                                        <div class="bt-testimonial--inner">
                                            <?php if (!empty($item['testimonial_rating'])) : ?>
                                                <div class="bt-testimonial--rating">
                                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                        <?php if ($i <= $item['testimonial_rating']) : ?>
                                                            <span class="star filled"><svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.39035 13.5438L2.68595 10.4773C1.98677 9.91816 2.29343 8.79375 3.18069 8.667L8.43472 8.43292L10.6784 2.80783C10.8348 2.49606 11.1527 2.2998 11.5013 2.2998C11.8498 2.2998 12.1677 2.49709 12.3241 2.80783L14.5678 8.43292L19.8218 8.667C20.7091 8.79375 21.0158 9.91816 20.3166 10.4773L16.6122 13.5438L17.6231 19.5308C17.7397 20.3475 16.8912 20.9588 16.1543 20.5898L11.5013 17.6326L6.84828 20.5888C6.11027 20.9578 5.26288 20.3465 5.37941 19.5298L6.39035 13.5438Z" fill="#FDCC0D" />
                                                                </svg></span>
                                                        <?php else : ?>
                                                            <span class="star"><svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.39035 13.5438L2.68595 10.4773C1.98677 9.91816 2.29343 8.79375 3.18069 8.667L8.43472 8.43292L10.6784 2.80783C10.8348 2.49606 11.1527 2.2998 11.5013 2.2998C11.8498 2.2998 12.1677 2.49709 12.3241 2.80783L14.5678 8.43292L19.8218 8.667C20.7091 8.79375 21.0158 9.91816 20.3166 10.4773L16.6122 13.5438L17.6231 19.5308C17.7397 20.3475 16.8912 20.9588 16.1543 20.5898L11.5013 17.6326L6.84828 20.5888C6.11027 20.9578 5.26288 20.3465 5.37941 19.5298L6.39035 13.5438Z" fill="#cfc8d8" />
                                                                </svg></span>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($item['testimonial_text'])) : ?>
                                                <div class="bt-testimonial--text"><?php echo esc_html($item['testimonial_text']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($item['testimonial_author'])) : ?>
                                            <div class="bt-testimonial--author"><?php echo esc_html($item['testimonial_author']); ?>
                                                <?php if (!empty($item['testimonial_position'])) : ?>
                                                    <div class="bt-testimonial--position"><?php echo esc_html($item['testimonial_position']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>



                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>
            <?php if (!empty($settings['slider_arrows']) && $settings['slider_arrows'] === 'yes') : ?>
                <div class="bt-swiper-navigation">
                    <div class="bt-nav bt-button-prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
                            <path d="M15.4995 7.00035C15.4995 7.16611 15.4337 7.32508 15.3165 7.44229C15.1992 7.5595 15.0403 7.62535 14.8745 7.62535H2.63311L7.1917 12.1832C7.24977 12.2412 7.29583 12.3102 7.32726 12.386C7.35869 12.4619 7.37486 12.5432 7.37486 12.6253C7.37486 12.7075 7.35869 12.7888 7.32726 12.8647C7.29583 12.9405 7.24977 13.0095 7.1917 13.0675C7.13363 13.1256 7.0647 13.1717 6.98882 13.2031C6.91295 13.2345 6.83164 13.2507 6.74951 13.2507C6.66739 13.2507 6.58607 13.2345 6.5102 13.2031C6.43433 13.1717 6.3654 13.1256 6.30733 13.0675L0.682328 7.44254C0.624217 7.38449 0.578118 7.31556 0.546665 7.23969C0.515213 7.16381 0.499023 7.08248 0.499023 7.00035C0.499023 6.91821 0.515213 6.83688 0.546665 6.76101C0.578118 6.68514 0.624217 6.61621 0.682328 6.55816L6.30733 0.93316C6.4246 0.815885 6.58366 0.75 6.74951 0.75C6.91537 0.75 7.07443 0.815885 7.1917 0.93316C7.30898 1.05044 7.37486 1.2095 7.37486 1.37535C7.37486 1.5412 7.30898 1.70026 7.1917 1.81753L2.63311 6.37535H14.8745C15.0403 6.37535 15.1992 6.4412 15.3165 6.55841C15.4337 6.67562 15.4995 6.83459 15.4995 7.00035Z" fill="currentColor" />
                        </svg>
                    </div>
                    <div class="bt-nav bt-button-next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M17.3172 10.4425L11.6922 16.0675C11.5749 16.1848 11.4159 16.2507 11.25 16.2507C11.0841 16.2507 10.9251 16.1848 10.8078 16.0675C10.6905 15.9503 10.6247 15.7912 10.6247 15.6253C10.6247 15.4595 10.6905 15.3004 10.8078 15.1832L15.3664 10.6253H3.125C2.95924 10.6253 2.80027 10.5595 2.68306 10.4423C2.56585 10.3251 2.5 10.1661 2.5 10.0003C2.5 9.83459 2.56585 9.67562 2.68306 9.55841C2.80027 9.4412 2.95924 9.37535 3.125 9.37535H15.3664L10.8078 4.81753C10.6905 4.70026 10.6247 4.5412 10.6247 4.37535C10.6247 4.2095 10.6905 4.05044 10.8078 3.93316C10.9251 3.81588 11.0841 3.75 11.25 3.75C11.4159 3.75 11.5749 3.81588 11.6922 3.93316L17.3172 9.55816C17.3753 9.61621 17.4214 9.68514 17.4528 9.76101C17.4843 9.83688 17.5005 9.91821 17.5005 10.0003C17.5005 10.0825 17.4843 10.1638 17.4528 10.2397C17.4214 10.3156 17.3753 10.3845 17.3172 10.4425Z" fill="currentColor" />
                        </svg>
                    </div>
                </div>
            <?php endif;
            // pagination
            if (!empty($settings['slider_dots']) && $settings['slider_dots'] === 'yes') {
                echo '<div class="bt-swiper-pagination swiper-pagination"></div>';
            }
            ?>
        </div>
<?php
    }



    protected function content_template() {}
}
