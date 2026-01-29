<?php

namespace SomniaElementorWidgets\Widgets\ProductTooltipHotspot;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_BBorder;
use Elementor\Group_Control_Box_Shadow;

class Widget_ProductTooltipHotspot extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-tooltip-hotspot';
    }

    public function get_title()
    {
        return __('Product Tooltip Hotspot', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-image-hotspot';
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
                'label' => __('Hotspot', 'somnia'),
            ]
        );


        $this->add_control(
            'hotspot_image',
            [
                'label' => __('Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $this->add_control(
            'show_mobile_image',
            [
                'label' => __('Show Mobile Image', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
            'hotspot_image_mobile',
            [
                'label' => __('Mobile Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'description' => __('Choose a different image to display on mobile devices', 'somnia'),
                'condition' => [
                    'show_mobile_image' => 'yes',
                ],
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
            'image_position',
            [
                'label' => __('Image Position', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'row' => __('Left', 'somnia'),
                    'row-reverse' => __('Right', 'somnia'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product' => 'flex-direction: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'image_width',
            [
                'label' => __('Image Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 100,
                        'max' => 200,
                    ],
                    'px' => [
                        'min' => 100,
                        'max' => 200,
                    ],
                ],

                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-image img' => 'width: {{SIZE}}%; margin-left:calc(-1 * ({{SIZE}}% - 100%) / 2);',
                ],
            ]
        );
        $this->add_control(
            'hotspot_point_style',
            [
                'label' => __('Hotspot Point Style', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default', 'somnia'),
                    'number' => __('Number', 'somnia'),
                ],
            ]
        );
        $this->add_control(
            'tooltip_layout',
            [
                'label' => __('Layout Tooltip', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default', 'somnia'),
                    'style1' => __('Style 1', 'somnia'),
                ],
            ]
        );
        $this->add_control(
            'show_quick_view',
            [
                'label' => __('Show Quick View', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'somnia'),
                'label_off' => __('Hide', 'somnia'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $repeater = new Repeater();
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
        $repeater->add_responsive_control(
            'hotspot_position_x',
            [
                'label' => __('X Position', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'left: {{SIZE}}%; --hotspot-translate-x: {{SIZE}}%;',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'hotspot_position_y',
            [
                'label' => __('Y Position', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'top: {{SIZE}}%; --hotspot-translate-y: {{SIZE}}%;',
                ],
            ]
        );
        $this->add_control(
            'hotspot_items',
            [
                'label' => __('Hotspot', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'id_product' => '',
                        'hotspot_position_x' => [
                            'unit' => '%',
                            'size' => 10,
                        ],
                        'hotspot_position_y' => [
                            'unit' => '%',
                            'size' => 10,
                        ]
                    ],
                    [
                        'id_product' => '',
                        'hotspot_position_x' => [
                            'unit' => '%',
                            'size' => 70,
                        ],
                        'hotspot_position_y' => [
                            'unit' => '%',
                            'size' => 30,
                        ]
                    ],
                    [
                        'id_product' => '',
                        'hotspot_position_x' => [
                            'unit' => '%',
                            'size' => 50,
                        ],
                        'hotspot_position_y' => [
                            'unit' => '%',
                            'size' => 90,
                        ]
                    ],

                ],
            ]
        );
        $this->add_control(
            'hotspot_align',
            [
                'label' => __('Hotspot Alignment', 'somnia'),
                'description' => __('Choose how to align the hotspot elements within the container', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'flex-start' => __('Start', 'somnia'),
                    'center' => __('Center', 'somnia'),
                    'flex-end' => __('End', 'somnia'),
                    'stretch' => __('Stretch', 'somnia'),
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product' => 'align-items: {{VALUE}};',
                ],
                'condition' => [
                    'show_slider' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'hotspot_full_width',
            [
                'label' => __('Hotspot Full Width', 'somnia'),
                'description' => __('This should only be used in Elementor’s Full Width mode. Enter your site’s container width to ensure the content is aligned correctly with the layout.', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'show_slider' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'hotspot_container_width',
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
                    '{{WRAPPER}} .bt-hotspot-product' => '--width-container: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'hotspot_full_width' => 'yes',
                    'show_slider' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'show_add_to_cart',
            [
                'label' => __('Show Set to Cart', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'show_content_or_image',
            [
                'label' => __('Show Content or Image', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'image',
                'options' => [
                    'content' => __('Content', 'somnia'),
                    'image' => __('Image', 'somnia'),
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
            'show_slider',
            [
                'label' => __('Show Slider', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'slider_layout',
            [
                'label' => __('Layout', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default', 'somnia'),
                    'style-1' => __('Style 1', 'somnia'),
                    'style-rectangle' => __('Style Rectangle', 'somnia'),
                ],
                'condition' => [
                    'show_slider' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'slider_max_width',
            [
                'label' => __('Max Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-slider--inner' => 'max-width: {{SIZE}}{{UNIT}};margin-left: 0;',
                ],
                'condition' => [
                    'show_slider' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'slider_sub_heading',
            [
                'label' => __('Sub Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Slider Sub Heading', 'somnia'),
                'label_block' => true,
                'condition' => [
                    'show_slider' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'slider_heading',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Slider Heading', 'somnia'),
                'label_block' => true,
                'condition' => [
                    'show_slider' => 'yes',
                ],
            ]
        );



        $this->add_control(
            'slider_description',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Slider description text goes here', 'somnia'),
                'label_block' => true,
                'condition' => [
                    'show_slider' => 'yes',
                ],
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
                    'show_slider' => 'yes',
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
                'condition' => [
                    'show_slider' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'slider_spacebetween',
            [
                'label' => __('Slider SpaceBetween', 'somnia'),
                'type' => Controls_Manager::NUMBER,
                'default' => 20,
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => 30,
                'condition' => [
                    'show_slider' => 'yes',
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
                    'show_slider' => 'yes',
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
                    'show_slider' => 'yes',
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
                    '{{WRAPPER}} .bt-hotspot-product' => '--slider-offset-width: {{SIZE}}{{UNIT}};',
                ],
                'render_type' => 'ui',
                'condition' => [
                    'slider_offset_sides!' => 'none',
                    'show_slider' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_section_controls()
    {
        $this->start_controls_section(
            'section_style_image',
            [
                'label' => __('Image', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'image_padding',
            [
                'label' => __('Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product--image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'section_style_slider_content',
            [
                'label' => __('Content', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_slider' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'content_width',
            [
                'label' => __('Content Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product' => '--width-content: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_slider' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'content_spacing',
            [
                'label' => __('Content Spacing', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product--slider .bt-hotspot-slider--inner' => 'margin-top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bt-hotspot-product--slider .bt-button-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'slider_content_padding',
            [
                'label' => __('Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product--slider' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'slider_content_background',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product--slider' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'heading_style',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-slider--heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} .bt-hotspot-slider--heading',
            ]
        );
        $this->add_responsive_control(
            'heading_padding',
            [
                'label' => __('Heading Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-slider--heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'heading_max_width',
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
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-slider--heading' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'sub_heading_style',
            [
                'label' => __('Sub Heading', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'sub_heading_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-slider--sub-heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_heading_typography',
                'selector' => '{{WRAPPER}} .bt-hotspot-slider--sub-heading',
            ]
        );

        $this->add_responsive_control(
            'sub_heading_padding',
            [
                'label' => __('Sub Heading Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-slider--sub-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sub_heading_max_width',
            [
                'label' => __('Sub Heading Max Width', 'somnia'),
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
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-slider--sub-heading' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'description_style',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-slider--description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .bt-hotspot-slider--description',
            ]
        );
        $this->add_responsive_control(
            'description_padding',
            [
                'label' => __('Description Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-slider--description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'description_max_width',
            [
                'label' => __('Description Max Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-slider--description' => 'max-width: {{SIZE}}{{UNIT}};',
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
        $this->register_layout_section_controls();
        $this->register_style_section_controls();
    }
    protected function render()
    {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        $settings = $this->get_settings_for_display();
        if (empty($settings['hotspot_image']['url'])) {
            return;
        }

        // Build $product_ids array with proper structure
        $product_ids = [];
        if (!empty($settings['hotspot_items'])) {
            foreach ($settings['hotspot_items'] as $item) {
                // Check if product ID exists and is not empty
                if (!isset($item['id_product']) || empty($item['id_product'])) {
                    continue;
                }
                $product = wc_get_product($item['id_product']);
                if (!$product) {
                    continue;
                }
                $variation_id = get_default_variation_id($product);
                $product_ids[] = [
                    'product_id'   => $item['id_product'],
                    'variation_id' => $variation_id,
                ];
            }
        }   
        if ($settings['hotspot_full_width'] === 'yes' && isset($settings['hotspot_container_width']['size'])) {
            $hotspot_container_width = $settings['hotspot_container_width']['size'];
?>
            <style>
                @media (min-width: <?php echo esc_attr($hotspot_container_width + 30); ?>px) {
                    <?php if ($settings['image_position'] === 'row-reverse') : ?>.bt-elwg-product-tooltip-hotspot--default .bt-hotspot-product .bt-hotspot-product--slider {
                        padding-left: calc((100% + 5px - var(--width-container)) / 2) !important;
                    }

                    <?php else : ?>.bt-elwg-product-tooltip-hotspot--default .bt-hotspot-product .bt-hotspot-product--slider {
                        padding-right: calc((100% + 5px - var(--width-container)) / 2) !important;
                    }

                    <?php endif; ?>
                }
            </style>
        <?php } ?>
        <div class="bt-elwg-product-tooltip-hotspot--default <?php echo esc_attr(($settings['show_slider'] === 'yes' && !empty($product_ids)) ? '' : 'bt-no-slider'); ?>">
            <div class="bt-hotspot-product bt-tooltip-<?php echo esc_attr($settings['tooltip_layout']); ?>">
                <div class="bt-hotspot-product--image">
                    <div class="bt-hotspot-image <?php echo esc_attr($settings['show_mobile_image'] === 'yes' ? 'bt-mobile-image' : ''); ?>">
                        <?php
                        if (!empty($settings['hotspot_image']['id'])) {
                            echo wp_get_attachment_image($settings['hotspot_image']['id'], $settings['thumbnail_size'], false, ['class' => 'bt-desktop-image']);
                        } else {
                            echo '<img src="' . esc_url($settings['hotspot_image']['url']) . '" alt="" class="bt-desktop-image">';
                        }
                        if ($settings['show_mobile_image'] === 'yes') {
                            if (!empty($settings['hotspot_image_mobile']['id'])) {
                                echo wp_get_attachment_image($settings['hotspot_image_mobile']['id'], $settings['thumbnail_size'], false, ['class' => 'bt-mobile-image']);
                            } else {
                                echo '<img src="' . esc_url($settings['hotspot_image_mobile']['url']) . '" alt="" class="bt-mobile-image">';
                            }
                        }
                        ?>
                    </div>
                    <?php if (!empty($settings['hotspot_items'])) : ?>
                        <?php
                        $archive_shop_hotspot = function_exists('get_field') ? get_field('archive_shop', 'options') : array();
                        $show_quickview_hotspot = isset($archive_shop_hotspot['show_quickview']) ? $archive_shop_hotspot['show_quickview'] : true;
                        $show_quickview = ($settings['show_quick_view'] === 'yes' && $show_quickview_hotspot);
                        ?>
                        <div class="bt-hotspot-points">
                            <?php foreach ($settings['hotspot_items'] as $index => $item) : ?>
                                <?php
                                $product = wc_get_product($item['id_product']);
                                if ($product) :
                                ?>
                                    <div class="bt-hotspot-point elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> <?php echo esc_attr($settings['hotspot_point_style'] === 'number' ? 'bt-hotspot-point-style-number' : ''); ?>"
                                        data-product-id="<?php echo esc_attr($item['id_product']); ?>">
                                        <div class="bt-hotspot-marker">
                                            <?php if ($settings['hotspot_point_style'] === 'number') : ?>
                                                <?php echo esc_html($index + 1); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="bt-hotspot-product-info <?php echo esc_attr($show_quickview ? 'bt-quick-view' : ''); ?>">
                                            <a class="bt-hotspot-product-thumbnail" href="<?php echo esc_url($product->get_permalink()); ?>">
                                                <?php
                                                if (has_post_thumbnail($item['id_product'])) {
                                                    echo get_the_post_thumbnail($item['id_product'], 'thumbnail');
                                                } else {
                                                    echo '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '" class="wp-post-image" />';
                                                }
                                                ?>
                                            </a>
                                            <div class="bt-product-content">
                                                <h4><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a></h4>
                                                <?php 
                                                    $price_class = $product->is_type( 'variable' ) ? 'bt-product-variable' : '';
                                                    $price_html  = $product->get_price_html();  
                                                ?>
                                                <p class="bt-price <?php echo esc_attr( $price_class ); ?>">
                                                    <?php echo wp_kses_post( $price_html ); ?>
                                                </p>
                                                <?php if ($show_quickview) : ?>
                                                <a class="btn bt-product-quick-view-btn" href="#" data-id="<?php echo esc_attr($item['id_product']); ?>">
                                                    <?php esc_html_e('Quick View', 'somnia'); ?>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($settings['show_add_to_cart'] === 'yes' && $settings['show_content_or_image'] === 'image') : ?>
                        <?php
                        if (!empty($product_ids)) :
                        ?>
                            <div class="bt-button-wrapper">
                                <a class="bt-button bt-button-add-set-to-cart" data-ids="<?php echo esc_attr(json_encode($product_ids)); ?>" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                        <path d="M22.4893 20.6861L21.1525 7.43606C21.1091 7.06951 20.9321 6.73182 20.6554 6.48759C20.3786 6.24337 20.0215 6.10975 19.6525 6.1123H16.4996C16.4996 4.91883 16.0255 3.77424 15.1816 2.93032C14.3377 2.08641 13.1931 1.6123 11.9996 1.6123C10.8062 1.6123 9.66157 2.08641 8.81766 2.93032C7.97374 3.77424 7.49964 4.91883 7.49964 6.1123H4.34308C3.97399 6.10975 3.61691 6.24337 3.34016 6.48759C3.06342 6.73182 2.88644 7.06951 2.84308 7.43606L1.5062 20.6861C1.4817 20.896 1.50186 21.1088 1.56536 21.3104C1.62885 21.5121 1.73425 21.698 1.87464 21.8561C2.01604 22.0148 2.18932 22.1419 2.38317 22.2291C2.57701 22.3163 2.78707 22.3617 2.99964 22.3623H20.9921C21.206 22.3628 21.4175 22.3179 21.6127 22.2307C21.8079 22.1434 21.9824 22.0157 22.1246 21.8561C22.2644 21.6977 22.3691 21.5116 22.4319 21.31C22.4948 21.1084 22.5143 20.8958 22.4893 20.6861ZM11.9996 3.1123C12.7953 3.1123 13.5583 3.42838 14.121 3.99098C14.6836 4.55359 14.9996 5.31666 14.9996 6.1123H8.99964C8.99964 5.31666 9.31571 4.55359 9.87832 3.99098C10.4409 3.42838 11.204 3.1123 11.9996 3.1123ZM2.99964 20.8623L4.34308 7.6123H7.49964V9.8623C7.49964 10.0612 7.57866 10.252 7.71931 10.3926C7.85996 10.5333 8.05073 10.6123 8.24964 10.6123C8.44855 10.6123 8.63932 10.5333 8.77997 10.3926C8.92062 10.252 8.99964 10.0612 8.99964 9.8623V7.6123H14.9996V9.8623C14.9996 10.0612 15.0787 10.252 15.2193 10.3926C15.36 10.5333 15.5507 10.6123 15.7496 10.6123C15.9486 10.6123 16.1393 10.5333 16.28 10.3926C16.4206 10.252 16.4996 10.0612 16.4996 9.8623V7.6123H19.6637L20.9921 20.8623H2.99964Z" fill="currentColor" />
                                    </svg>
                                    <?php esc_html_e('Add set to cart', 'somnia'); ?>
                                    <span class="bt-btn-price"></span>
                                </a>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php if (!empty($product_ids)) : ?>
                    <div class="bt-hotspot-product--slider">
                        <?php
                        $slider_settings = [
                            'autoplay' => isset($settings['slider_autoplay']) && $settings['slider_autoplay'] === 'yes',
                            'speed' => isset($settings['slider_speed']) ? $settings['slider_speed'] : 500,
                            'spaceBetween' => [
                                'desktop' => !empty($settings['slider_spacebetween']) ? $settings['slider_spacebetween'] : 30,
                                'tablet' => !empty($settings['slider_spacebetween_tablet']) ? $settings['slider_spacebetween_tablet'] : 20,
                                'mobile' => !empty($settings['slider_spacebetween_mobile']) ? $settings['slider_spacebetween_mobile'] : 15,
                            ],
                        ];
                        $classes = ['bt-hotspot-slider--inner', 'swiper'];
                        if ($settings['slider_dots_only_mobile'] === 'yes') {
                            $classes[] = 'bt-only-dot-mobile';
                        }
                        if ($settings['slider_layout'] === 'style-1') {
                            $classes[] = 'bt-slider-style-1';
                        }
                        if ($settings['slider_layout'] === 'style-rectangle') {
                            $classes[] = 'bt-slider-style-rectangle';
                        }
                        ?>
                        <div class="bt-hotspot-slider bt-slider-offset-sides-<?php echo esc_attr($settings['slider_offset_sides']); ?>" data-slider-settings='<?php echo json_encode($slider_settings); ?>'>
                            <?php if (!empty($settings['slider_sub_heading'])) : ?>
                                <h4 class="bt-hotspot-slider--sub-heading"><?php echo esc_html($settings['slider_sub_heading']); ?></h4>
                            <?php endif; ?>
                            <?php if (!empty($settings['slider_heading'])) : ?>
                                <h3 class="bt-hotspot-slider--heading"><?php echo esc_html($settings['slider_heading']); ?></h3>
                            <?php endif; ?>

                            <?php if (!empty($settings['slider_description'])) : ?>
                                <p class="bt-hotspot-slider--description"><?php echo esc_html($settings['slider_description']); ?></p>
                            <?php endif; ?>
                            <div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
                                <div class="bt-hotspot-slider--wrap swiper-wrapper">
                                    <?php
                                    // Collect all product IDs from hotspot_items
                                    $hotspot_product_ids = [];
                                    foreach ($settings['hotspot_items'] as $item) {
                                        if (!empty($item['id_product'])) {
                                            $hotspot_product_ids[] = $item['id_product'];
                                        }
                                    }

                                    // Prepare WP_Query to get products in the same order as hotspot_items
                                    if (!empty($hotspot_product_ids)) {
                                        $args = [
                                            'post_type'      => 'product',
                                            'post__in'       => $hotspot_product_ids,
                                            'posts_per_page' => -1,
                                            'orderby'        => 'post__in',
                                        ];
                                        $hotspot_query = new \WP_Query($args);
                                        $index = 1;
                                        if ($hotspot_query->have_posts()) :
                                            while ($hotspot_query->have_posts()) : $hotspot_query->the_post();
                                                global $product;
                                                $product_id = get_the_ID();
                                                if (!$product) {
                                                    $product = wc_get_product($product_id);
                                                }
                                                if (!$product) {
                                                    continue;
                                                }
                                                $order_currency = get_woocommerce_currency();
                                                $product_currencySymbol = get_woocommerce_currency_symbol($order_currency);
                                                $is_in_stock = $product->is_in_stock();
                                                $out_of_stock_class = !$is_in_stock ? ' out-of-stock' : '';
                                    ?>
                                                <li class="bt-slider-item bt-hotspot-product-list__item swiper-slide<?php echo esc_attr($out_of_stock_class); ?>"
                                                    data-product-currency="<?php echo esc_attr($product_currencySymbol); ?>"
                                                    data-product-single-price="<?php echo esc_attr($product->get_sale_price() ? $product->get_sale_price() : $product->get_regular_price()); ?>"
                                                    data-product-id="<?php echo esc_attr($product_id); ?>"
                                                    data-in-stock="<?php echo esc_attr($is_in_stock ? '1' : '0'); ?>">
                                                    <?php
                                                    wc_get_template_part('content', 'product');
                                                    ?>
                                                </li>
                                    <?php
                                                $index++;
                                            endwhile;
                                            wp_reset_postdata();
                                        endif;
                                    }
                                    ?>
                                </div>
                                <?php
                                // pagination
                                if ($settings['slider_dots'] === 'yes') {
                                    echo '<div class="bt-swiper-pagination swiper-pagination"></div>';
                                }
                                ?>

                            </div>
                            <?php if ($settings['show_add_to_cart'] === 'yes' && $settings['show_content_or_image'] === 'content') : ?>
                                <?php
                                if (!empty($product_ids)) :
                                ?>
                                    <div class="bt-button-wrapper">
                                        <a class="bt-button bt-button-add-set-to-cart" data-ids="<?php echo esc_attr(json_encode($product_ids)); ?>" href="#">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                                <path d="M22.4893 20.6861L21.1525 7.43606C21.1091 7.06951 20.9321 6.73182 20.6554 6.48759C20.3786 6.24337 20.0215 6.10975 19.6525 6.1123H16.4996C16.4996 4.91883 16.0255 3.77424 15.1816 2.93032C14.3377 2.08641 13.1931 1.6123 11.9996 1.6123C10.8062 1.6123 9.66157 2.08641 8.81766 2.93032C7.97374 3.77424 7.49964 4.91883 7.49964 6.1123H4.34308C3.97399 6.10975 3.61691 6.24337 3.34016 6.48759C3.06342 6.73182 2.88644 7.06951 2.84308 7.43606L1.5062 20.6861C1.4817 20.896 1.50186 21.1088 1.56536 21.3104C1.62885 21.5121 1.73425 21.698 1.87464 21.8561C2.01604 22.0148 2.18932 22.1419 2.38317 22.2291C2.57701 22.3163 2.78707 22.3617 2.99964 22.3623H20.9921C21.206 22.3628 21.4175 22.3179 21.6127 22.2307C21.8079 22.1434 21.9824 22.0157 22.1246 21.8561C22.2644 21.6977 22.3691 21.5116 22.4319 21.31C22.4948 21.1084 22.5143 20.8958 22.4893 20.6861ZM11.9996 3.1123C12.7953 3.1123 13.5583 3.42838 14.121 3.99098C14.6836 4.55359 14.9996 5.31666 14.9996 6.1123H8.99964C8.99964 5.31666 9.31571 4.55359 9.87832 3.99098C10.4409 3.42838 11.204 3.1123 11.9996 3.1123ZM2.99964 20.8623L4.34308 7.6123H7.49964V9.8623C7.49964 10.0612 7.57866 10.252 7.71931 10.3926C7.85996 10.5333 8.05073 10.6123 8.24964 10.6123C8.44855 10.6123 8.63932 10.5333 8.77997 10.3926C8.92062 10.252 8.99964 10.0612 8.99964 9.8623V7.6123H14.9996V9.8623C14.9996 10.0612 15.0787 10.252 15.2193 10.3926C15.36 10.5333 15.5507 10.6123 15.7496 10.6123C15.9486 10.6123 16.1393 10.5333 16.28 10.3926C16.4206 10.252 16.4996 10.0612 16.4996 9.8623V7.6123H19.6637L20.9921 20.8623H2.99964Z" fill="currentColor" />
                                            </svg>
                                            <?php esc_html_e('Add set to cart', 'somnia'); ?>
                                            <span class="bt-btn-price"></span>
                                        </a>

                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
<?php
    }



    protected function content_template() {}
}
