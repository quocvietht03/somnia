<?php

namespace SomniaElementorWidgets\Widgets\OffersSlider;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Plugin;
use ElementorPro\Base\Base_Carousel_Trait;


class Widget_OffersSlider extends Widget_Base
{
    use Base_Carousel_Trait;

    public function get_name()
    {
        return 'bt-offers-slider';
    }

    public function get_title()
    {
        return __('Offers Slider', 'somnia');
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
                'label' => __('Content', 'somnia'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'content_type',
            [
                'label' => __('Content Type', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'image',
                'options' => [
                    'image' => __('Image', 'somnia'),
                    'sale' => __('Sale', 'somnia'),
                ],
            ]
        );

        // Image options
        $repeater->add_control(
            'offer_image',
            [
                'label' => __('Offer Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'content_type' => 'image',
                ],
            ]
        );

        // Text options
        $repeater->add_control(
            'sale_percentage',
            [
                'label' => __('Sale Percentage', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('25%', 'somnia'),
                'placeholder' => __('25%', 'somnia'),
                'condition' => [
                    'content_type' => 'sale',
                ],
            ]
        );

        $repeater->add_control(
            'off_text',
            [
                'label' => __('Off Text', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('OFF', 'somnia'),
                'condition' => [
                    'content_type' => 'sale',
                ],
            ]
        );

        // Common fields
        $repeater->add_control(
            'offer_title',
            [
                'label' => __('Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Special Offer', 'somnia'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'offer_subtitle',
            [
                'label' => __('Subtitle', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Limited Time Only', 'somnia'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'offer_link',
            [
                'label' => __('Link', 'somnia'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'somnia'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->add_control(
            'offers_items',
            [
                'label' => __('Offers Items', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'content_type' => 'sale',
                        'sale_percentage' => 25,
                        'off_text' => __('OFF', 'somnia'),
                        'offer_title' => __('Sale Off', 'somnia'),
                        'offer_subtitle' => __('52 Items', 'somnia'),
                        'offer_link' => ['url' => '#'],
                    ],
                    [
                        'content_type' => 'sale',
                        'sale_percentage' => 15,
                        'off_text' => __('OFF', 'somnia'),
                        'offer_title' => __('Best Seller', 'somnia'),
                        'offer_subtitle' => __('52 Items', 'somnia'),
                        'offer_link' => ['url' => '#'],
                    ],
                    [
                        'content_type' => 'image',
                        'offer_image' => ['url' => Utils::get_placeholder_image_src()],
                        'offer_title' => __('0-12 Months', 'somnia'),
                        'offer_subtitle' => __('25 Items', 'somnia'),
                        'offer_link' => ['url' => '#'],
                    ],
                ],
                'title_field' => '{{{ offer_title }}}',
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
                'range' => [
                    'px' => [
                        'min' => 0.3,
                        'max' => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-offers-slider--image .bt-cover-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
                ],
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
                'default' => '6',
                'tablet_default' => '4',
                'mobile_default' => '2',
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
            'slides_on_display' => 8,
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
                    '{{WRAPPER}} .bt-elwg-offers-slider' => '--slider-offset-width: {{SIZE}}{{UNIT}};',
                ],
                'render_type' => 'ui',
                'condition' => [
                    'slider_offset_sides!' => 'none',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_content_section_controls()
    {
        $this->start_controls_section(
            'section_style_item',
            [
                'label' => esc_html__('Item', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_border_radius',
            [
                'label' => __('Border Radius', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-offers-slider--item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'item_padding',
            [
                'label' => __('Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .bt-offers-slider--item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->start_controls_tabs('item_content_tabs');

        // Sale Content Tab
        $this->start_controls_tab(
            'item_sale_tab',
            [
                'label' => __('Sale', 'somnia'),
            ]
        );

        $this->add_control(
            'sale_percentage_color',
            [
                'label' => __('Sale Percentage Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-sale-percentage' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'off_text_color',
            [
                'label' => __('Off Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-off-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sale_background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-offers-slider--text-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sale_item_background_color',
            [
                'label' => __('Item Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '#FFF1EA',
                'selectors' => [
                    '{{WRAPPER}} .bt-offers-slider--item.bt-type-sale' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Image Content Tab
        $this->start_controls_tab(
            'item_image_tab',
            [
                'label' => __('Image', 'somnia'),
            ]
        );
        $this->add_control(
            'item_background_color',
            [
                'label' => __('item Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-offers-slider--item.bt-type-image' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'item_title_style',
            [
                'label' => __('Title', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'item_title_color',
            [
                'label' => esc_html__('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-offers-slider--item .bt-offers-slider--title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'item_title_typography',
				'label' => esc_html__('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-offers-slider--item .bt-offers-slider--title',
			]
		);
        $this->add_control(
            'item_subtitle_style',
            [
                'label' => __('Subtitle', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'item_subtitle_color',
            [
                'label' => esc_html__('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-offers-slider--item .bt-offers-slider--subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'item_subtitle_typography',
				'label' => esc_html__('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-offers-slider--item .bt-offers-slider--subtitle',
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
        $this->register_content_section_controls();
        $this->register_style_content_section_controls();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings['offers_items'])) {
            return;
        }

        $classes = ['bt-elwg-offers-slider'];
        if ($settings['slider_arrows_hidden_mobile'] === 'yes') {
            $classes[] = 'bt-hidden-arrow-mobile';
        }
        if ($settings['slider_dots_only_mobile'] === 'yes') {
            $classes[] = 'bt-only-dot-mobile';
        }
        $breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
        $slider_settings = bt_elwg_get_slider_settings($settings, $breakpoints);
?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?> bt-slider-offset-sides-<?php echo esc_attr($settings['slider_offset_sides']); ?>" data-slider-settings="<?php echo esc_attr(json_encode($slider_settings)); ?>">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($settings['offers_items'] as $item) : ?>
                        <div class="swiper-slide">
                            <div class="bt-offers-slider--item bt-type-<?php echo esc_attr($item['content_type']); ?>">
                                <?php
                                $link_url = !empty($item['offer_link']['url']) ? $item['offer_link']['url'] : '#';
                                $link_target = !empty($item['offer_link']['is_external']) ? ' target="_blank"' : '';
                                $link_nofollow = !empty($item['offer_link']['nofollow']) ? ' rel="nofollow"' : '';

                                echo '<a href="'. esc_url($link_url) .'" '. $link_target . $link_nofollow .' class="bt-offers-slider--link">'
                                ?>
                                    <div class="bt-offers-slider--image">
                                        <div class="bt-cover-image">
                                            <?php if ($item['content_type'] === 'image') : ?>
                                                <?php
                                                if (!empty($item['offer_image']['id'])) {
                                                    echo wp_get_attachment_image($item['offer_image']['id'], $settings['thumbnail_size']);
                                                } else {
                                                    if (!empty($item['offer_image']['url'])) {
                                                        echo '<img src="' . esc_url($item['offer_image']['url']) . '" alt="' . esc_attr($item['offer_title']) . '">';
                                                    } else {
                                                        echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_attr($item['offer_title']) . '">';
                                                    }
                                                }
                                                ?>
                                            <?php else : ?>
                                                <div class="bt-offers-slider--text-content">
                                                    <div class="bt-sale-percentage"><?php echo esc_html($item['sale_percentage']); ?></div>
                                                    <div class="bt-off-text"><?php echo esc_html($item['off_text']); ?></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="bt-offers-slider--info">
                                        <h4 class="bt-offers-slider--title"><?php echo esc_html($item['offer_title']); ?></h4>
                                        <?php if (!empty($item['offer_subtitle'])) : ?>
                                            <div class="bt-offers-slider--subtitle"><?php echo esc_html($item['offer_subtitle']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php echo '</a>' ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($settings['slider_arrows'] === 'yes') : ?>
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
