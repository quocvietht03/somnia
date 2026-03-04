<?php

namespace SomniaElementorWidgets\Widgets\ProductTestimonial;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_BBorder;
use Elementor\Group_Control_Box_Shadow;

class Widget_ProductTestimonial extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-testimonial';
    }

    public function get_title()
    {
        return __('Product Testimonial', 'somnia');
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
    protected function get_supported_ids()
    {
        $supported_ids = [];

        $wp_query = new \WP_Query(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        if ($wp_query->have_posts()) {
            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                $supported_ids[get_the_ID()] = get_the_title();
            }
        }

        return $supported_ids;
    }
    protected function register_layout_section_controls()
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Testimonial', 'somnia'),
            ]
        );
        $this->add_control(
            'testimonial_heading',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Hear from Our Happy Customers', 'somnia'),
                'placeholder' => __('Enter heading text', 'somnia'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'testimonial_description',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Discover why our customers love their sleep experiences with us!', 'somnia'),
                'placeholder' => __('Enter description text', 'somnia'),
                'rows' => 3,
                'label_block' => true,
            ]
        );

        $repeater = new Repeater();
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
        $repeater->add_control(
            'testimonial_image',
            [
                'label' => __('Image Banner', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control(
            'id_product',
            [
                'label' => __('Select Product', 'somnia'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_supported_ids(),
                'label_block' => true,
                'multiple' => false,
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
                    ],
                    [
                        'testimonial_text' => __('Excellent quality and fast delivery.', 'somnia'),
                    ],
                    [
                        'testimonial_text' => __('Best purchase I made this year!', 'somnia'),
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
            'image_ratio',
            [
                'label' => __('Image Ratio', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.67,
                ],
                'range' => [
                    'px' => [
                        'min' => 0.3,
                        'max' => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--images .bt-image-cover' => 'padding-bottom: calc( {{SIZE}} * 100% );',
                ],
            ]
        );
        $this->add_responsive_control(
            'gap',
            [
                'label' => __('Gap', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial' => '--column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'testimonial_full_width',
            [
                'label' => __('Testimonial Full Width', 'somnia'),
                'description' => __('This should only be used in Elementor’s Full Width mode. Enter your site’s container width to ensure the content is aligned correctly with the layout.', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_responsive_control(
            'testimonial_container_width',
            [
                'label' => __('Container Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial' => '--width-container: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'testimonial_full_width' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'responsive_overlay_content',
            [
                'label' => __('Responsive Overlay Content', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
            'content_background_overlay',
            [
                'label' => __('Background Overlay', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial.bt-responsive-overlay-content .bt-product-testimonial--content::before' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    'responsive_overlay_content' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'content_background_overlay_opacity',
            [
                'label' => __('Overlay Opacity', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.6,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial.bt-responsive-overlay-content .bt-product-testimonial--content::before' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'responsive_overlay_content' => 'yes',
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
                'description' => __('Delay between slides in milliseconds', 'somnia'),
                'condition' => [
                    'slider_autoplay' => 'yes',
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
                'max' => 5000,
                'step' => 100,
            ]
        );
        $this->add_control(
            'slider_pagination',
            [
                'label' => __('Show Pagination', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'no',
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
        $this->end_controls_section();
    }

    protected function register_style_section_controls()
    {
        // Image Style Section
        $this->start_controls_section(
            'section_image_style',
            [
                'label' => __('Image', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'image_position',
            [
                'label' => __('Image Position', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'row-reverse' => __('Left', 'somnia'),
                    'row' => __('Right', 'somnia'),
                ],
                'default' => 'row-reverse',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial' => 'flex-direction: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'image_object_fit',
            [
                'label' => __('Image Object Fit', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'cover' => __('Cover', 'somnia'),
                    'contain' => __('Contain', 'somnia'),
                    'fill' => __('Fill', 'somnia'),
                    'none' => __('None', 'somnia'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--images .bt-image-cover img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'image_background_color',
            [
                'label' => __('Image Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--images .bt-image-cover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'image_border_radius',
            [
                'label' => __('Image Border Radius', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--images .bt-image-cover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
        // Content Style Section
        $this->start_controls_section(
            'section_content_style',
            [
                'label' => __('Content', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __('Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'content_background_color',
            [
                'label' => __('Content Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--content' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'heading_vs_description_heading',
            [
                'label' => __('Heading vs Description', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => __('Heading Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} .bt-product-testimonial--heading',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Description Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .bt-product-testimonial--description',
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
                'default' => '#0C2C48',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .bt-product-testimonial--text',
            ]
        );
        $this->add_responsive_control(
            'text_max_width',
            [
                'label' => __('Max Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--text' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_margin',
            [
                'label' => __('Margin', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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
                    '{{WRAPPER}} .bt-product-testimonial--author' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'author_typography',
                'selector' => '{{WRAPPER}} .bt-product-testimonial--author',
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
                    '{{WRAPPER}} .bt-product-testimonial--rating .star.filled svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'rating_empty_color',
            [
                'label' => __('Empty Star Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '#E9E9E9',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--rating .star svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'rating_size',
            [
                'label' => __('Star Size', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial--rating .star svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
        $image_position = isset($settings['image_position']) ? $settings['image_position'] : 'row-reverse';
        $slider_settings = [
            'autoplay' => isset($settings['slider_autoplay']) && $settings['slider_autoplay'] === 'yes',
            'speed' => isset($settings['slider_speed']) ? $settings['slider_speed'] : 500,
            'autoplay_delay' => isset($settings['slider_autoplay_delay']) ? $settings['slider_autoplay_delay'] : 3000,
        ];
        if ($settings['testimonial_full_width'] === 'yes') {
            $testimonial_container_width = $settings['testimonial_container_width']['size'];
?>
            <style>
                @media (min-width: <?php echo esc_attr($testimonial_container_width + 30); ?>px) {
                    <?php if ($image_position === 'row') { ?>.bt-elwg-product-testimonial--default .bt-product-testimonial {
                        padding-left: calc((100% + 5px - var(--width-container)) / 2) !important;
                    }

                    <?php
                    }
                    if ($image_position === 'row-reverse') { ?>.bt-elwg-product-testimonial--default .bt-product-testimonial {
                        padding-right: calc((100% + 5px - var(--width-container)) / 2) !important;
                    }

                    <?php } ?>
                }
            </style>
        <?php } ?>

        <?php
        $is_responsive_content = $settings['responsive_overlay_content'] === 'yes' ? 'bt-responsive-overlay-content' : '';
        ?>
        <div class="bt-elwg-product-testimonial--default" data-slider-settings='<?php echo json_encode($slider_settings); ?>'>
            <div class="bt-product-testimonial <?php echo esc_attr($is_responsive_content); ?>">
                <div class="bt-product-testimonial--content">
                    <svg class="bt-product-testimonial--icon-mask" xmlns="http://www.w3.org/2000/svg" width="196" height="196" viewBox="0 0 196 196" fill="none">
                        <g opacity="0.1">
                            <path d="M88.8125 55.125V122.5C88.8024 132.244 84.9272 141.585 78.0375 148.475C71.1477 155.365 61.8061 159.24 52.0625 159.25C50.438 159.25 48.8801 158.605 47.7315 157.456C46.5828 156.307 45.9375 154.749 45.9375 153.125C45.9375 151.501 46.5828 149.943 47.7315 148.794C48.8801 147.645 50.438 147 52.0625 147C58.5603 147 64.792 144.419 69.3866 139.824C73.9813 135.229 76.5625 128.998 76.5625 122.5V116.375H30.625C27.3761 116.375 24.2603 115.084 21.9629 112.787C19.6656 110.49 18.375 107.374 18.375 104.125V55.125C18.375 51.8761 19.6656 48.7603 21.9629 46.4629C24.2603 44.1656 27.3761 42.875 30.625 42.875H76.5625C79.8114 42.875 82.9272 44.1656 85.2246 46.4629C87.5219 48.7603 88.8125 51.8761 88.8125 55.125ZM165.375 42.875H119.438C116.189 42.875 113.073 44.1656 110.775 46.4629C108.478 48.7603 107.188 51.8761 107.188 55.125V104.125C107.188 107.374 108.478 110.49 110.775 112.787C113.073 115.084 116.189 116.375 119.438 116.375H165.375V122.5C165.375 128.998 162.794 135.229 158.199 139.824C153.604 144.419 147.373 147 140.875 147C139.251 147 137.693 147.645 136.544 148.794C135.395 149.943 134.75 151.501 134.75 153.125C134.75 154.749 135.395 156.307 136.544 157.456C137.693 158.605 139.251 159.25 140.875 159.25C150.619 159.24 159.96 155.365 166.85 148.475C173.74 141.585 177.615 132.244 177.625 122.5V55.125C177.625 51.8761 176.334 48.7603 174.037 46.4629C171.74 44.1656 168.624 42.875 165.375 42.875Z" fill="#FFCE52" />
                        </g>
                    </svg>
                    <?php if (!empty($settings['testimonial_heading'])) : ?>
                        <h3 class="bt-product-testimonial--heading"><?php echo esc_html($settings['testimonial_heading']); ?></h3>
                    <?php endif; ?>
                    <?php if (!empty($settings['testimonial_description'])) : ?>
                        <div class="bt-product-testimonial--description"><?php echo esc_html($settings['testimonial_description']); ?></div>
                    <?php endif; ?>

                    <div class="swiper js-testimonial-content">
                        <div class="swiper-wrapper">
                            <?php if (!empty($settings['testimonial_items'])) : ?>
                                <?php foreach ($settings['testimonial_items'] as $item) : ?>
                                    <div class="swiper-slide">
                                        <div class="bt-product-testimonial--item">

                                            <?php if (!empty($item['testimonial_text'])) : ?>
                                                <div class="bt-product-testimonial--text"><?php echo esc_html($item['testimonial_text']); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($item['testimonial_rating'])) : ?>
                                                <div class="bt-product-testimonial--rating">
                                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                        <?php if ($i <= $item['testimonial_rating']) : ?>
                                                            <span class="star filled"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="25" viewBox="0 0 26 25" fill="none">
                                                                    <path d="M24.6254 11.5605L19.7035 15.8075L21.2031 22.1589C21.2858 22.5037 21.2645 22.8653 21.1418 23.198C21.0192 23.5306 20.8007 23.8195 20.5139 24.0281C20.2272 24.2366 19.885 24.3555 19.5308 24.3698C19.1765 24.384 18.8259 24.2929 18.5234 24.108L12.9999 20.7086L7.47321 24.108C7.17071 24.2918 6.82058 24.3821 6.4669 24.3673C6.11322 24.3526 5.7718 24.2335 5.48565 24.0251C5.1995 23.8167 4.98141 23.5283 4.85883 23.1963C4.73625 22.8642 4.71467 22.5032 4.79681 22.1589L6.30181 15.8075L1.37993 11.5605C1.11229 11.3292 0.918725 11.0241 0.823413 10.6834C0.728101 10.3428 0.735265 9.98158 0.844011 9.64495C0.952757 9.30833 1.15826 9.01121 1.43487 8.79069C1.71147 8.57016 2.04692 8.43602 2.39931 8.405L8.85243 7.88438L11.3418 1.86C11.4765 1.53168 11.7059 1.25084 12.0006 1.05319C12.2954 0.855535 12.6423 0.75 12.9972 0.75C13.3521 0.75 13.699 0.855535 13.9937 1.05319C14.2885 1.25084 14.5178 1.53168 14.6526 1.86L17.1409 7.88438L23.594 8.405C23.9471 8.43487 24.2835 8.56826 24.5611 8.78848C24.8387 9.0087 25.0452 9.30594 25.1546 9.64297C25.264 9.98 25.2716 10.3418 25.1762 10.6831C25.0809 11.0244 24.887 11.33 24.6188 11.5616L24.6254 11.5605Z" fill="#181818" />
                                                                </svg></span>
                                                        <?php else : ?>
                                                            <span class="star">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="25" viewBox="0 0 26 25" fill="none">
                                                                    <path d="M24.6254 11.5605L19.7035 15.8075L21.2031 22.1589C21.2858 22.5037 21.2645 22.8653 21.1418 23.198C21.0192 23.5306 20.8007 23.8195 20.5139 24.0281C20.2272 24.2366 19.885 24.3555 19.5308 24.3698C19.1765 24.384 18.8259 24.2929 18.5234 24.108L12.9999 20.7086L7.47321 24.108C7.17071 24.2918 6.82058 24.3821 6.4669 24.3673C6.11322 24.3526 5.7718 24.2335 5.48565 24.0251C5.1995 23.8167 4.98141 23.5283 4.85883 23.1963C4.73625 22.8642 4.71467 22.5032 4.79681 22.1589L6.30181 15.8075L1.37993 11.5605C1.11229 11.3292 0.918725 11.0241 0.823413 10.6834C0.728101 10.3428 0.735265 9.98158 0.844011 9.64495C0.952757 9.30833 1.15826 9.01121 1.43487 8.79069C1.71147 8.57016 2.04692 8.43602 2.39931 8.405L8.85243 7.88438L11.3418 1.86C11.4765 1.53168 11.7059 1.25084 12.0006 1.05319C12.2954 0.855535 12.6423 0.75 12.9972 0.75C13.3521 0.75 13.699 0.855535 13.9937 1.05319C14.2885 1.25084 14.5178 1.53168 14.6526 1.86L17.1409 7.88438L23.594 8.405C23.9471 8.43487 24.2835 8.56826 24.5611 8.78848C24.8387 9.0087 25.0452 9.30594 25.1546 9.64297C25.264 9.98 25.2716 10.3418 25.1762 10.6831C25.0809 11.0244 24.887 11.33 24.6188 11.5616L24.6254 11.5605Z" fill="#E9E9E9" />
                                                                </svg></span>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($item['testimonial_author'])) : ?>
                                                <div class="bt-product-testimonial--author"><?php echo esc_html($item['testimonial_author']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </div>
                        <?php if ($settings['slider_pagination'] === 'yes') : ?>
                            <div class="bt-swiper-pagination"></div>
                        <?php endif; ?>
                        <?php if ($settings['slider_arrows'] === 'yes') : ?>
                            <div class="bt-swiper-navigation <?php echo esc_attr($settings['slider_pagination'] === 'yes' ? 'bt-pagination-yes' : ''); ?>">
                                <div class="bt-nav bt-button-prev">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="currentColor">
                                        <path d="M23.2968 28.4547C23.4013 28.5592 23.4843 28.6833 23.5408 28.8199C23.5974 28.9564 23.6265 29.1028 23.6265 29.2506C23.6265 29.3984 23.5974 29.5448 23.5408 29.6814C23.4843 29.818 23.4013 29.942 23.2968 30.0466C23.1923 30.1511 23.0682 30.234 22.9316 30.2906C22.7951 30.3471 22.6487 30.3763 22.5009 30.3763C22.3531 30.3763 22.2067 30.3471 22.0701 30.2906C21.9336 30.234 21.8095 30.1511 21.7049 30.0466L10.4549 18.7966C10.3503 18.6921 10.2674 18.568 10.2108 18.4314C10.1541 18.2949 10.125 18.1485 10.125 18.0006C10.125 17.8528 10.1541 17.7064 10.2108 17.5698C10.2674 17.4332 10.3503 17.3092 10.4549 17.2047L21.7049 5.95469C21.916 5.74359 22.2024 5.625 22.5009 5.625C22.7994 5.625 23.0857 5.74359 23.2968 5.95469C23.5079 6.16578 23.6265 6.45209 23.6265 6.75063C23.6265 7.04916 23.5079 7.33547 23.2968 7.54656L12.8414 18.0006L23.2968 28.4547Z" />
                                    </svg>
                                </div>
                                <div class="bt-nav bt-button-next">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="currentColor">
                                        <path d="M25.5466 18.7966L14.2966 30.0466C14.192 30.1511 14.068 30.234 13.9314 30.2906C13.7948 30.3471 13.6484 30.3763 13.5006 30.3763C13.3528 30.3763 13.2064 30.3471 13.0699 30.2906C12.9333 30.234 12.8092 30.1511 12.7047 30.0466C12.6002 29.942 12.5173 29.818 12.4607 29.6814C12.4041 29.5448 12.375 29.3984 12.375 29.2506C12.375 29.1028 12.4041 28.9564 12.4607 28.8199C12.5173 28.6833 12.6002 28.5592 12.7047 28.4547L23.1602 18.0006L12.7047 7.54656C12.4936 7.33547 12.375 7.04916 12.375 6.75063C12.375 6.45209 12.4936 6.16578 12.7047 5.95469C12.9158 5.74359 13.2021 5.625 13.5006 5.625C13.7992 5.625 14.0855 5.74359 14.2966 5.95469L25.5466 17.2047C25.6512 17.3092 25.7341 17.4332 25.7908 17.5698C25.8474 17.7064 25.8765 17.8528 25.8765 18.0006C25.8765 18.1485 25.8474 18.2949 25.7908 18.4314C25.7341 18.568 25.6512 18.6921 25.5466 18.7966Z" />
                                    </svg>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="swiper bt-product-testimonial--images js-testimonial-images">
                    <div class="swiper-wrapper">
                        <?php if (!empty($settings['testimonial_items'])) : ?>
                            <?php foreach ($settings['testimonial_items'] as $item) : ?>
                                <div class="bt-product-testimonial--product swiper-slide">
                                    <div class="bt-image-cover">
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
                                    <?php if (!empty($item['id_product'])) :
                                        $product = wc_get_product($item['id_product']);
                                        if ($product) :
                                            $is_variable = $product->is_type('variable') ? 'bt-product-variable' : '';
                                    ?>
                                            <div class="bt-product-item-minimal active <?php echo esc_attr($is_variable); ?>"
                                                data-product-id="<?php echo esc_attr($item['id_product']); ?>">
                                                <div class="bt-product-thumbnail">
                                                    <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                                        <?php
                                                        if (has_post_thumbnail($item['id_product'])) {
                                                            echo get_the_post_thumbnail($item['id_product'], 'thumbnail');
                                                        } else {
                                                            echo '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '" class="wp-post-image" />';
                                                        }
                                                        ?>
                                                    </a>
                                                </div>
                                                <div class="bt-product-content">
                                                    <h4 class="bt-product-title"><a href="<?php echo esc_url($product->get_permalink()); ?>" class="bt-product-link"><?php echo esc_html($product->get_name()); ?></a></h4>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
<?php
    }



    protected function content_template() {}
}
