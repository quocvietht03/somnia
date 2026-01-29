<?php

namespace SomniaElementorWidgets\Widgets\ProductBannerScrollHotspot;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

class Widget_ProductBannerScrollHotspot extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-banner-hotspot';
    }

    public function get_title()
    {
        return __('Product Banner Scroll Hotspot', 'somnia');
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
        return ['elementor-widgets'];
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

        wp_reset_postdata();

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
        $repeater = new Repeater();
        $repeater->add_control(
            'hotspot_image',
            [
                'label' => __('Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'description' => __('Upload an image for the hotspot banner. For optimal display, we recommend using images with a height equal to 80% of the screen height.', 'somnia'),
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control(
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
        $repeater->add_control(
            'hotspot_image_mobile',
            [
                'label' => __('Mobile Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'show_mobile_image' => 'yes',
                ],
            ]
        );
        $repeater->add_control(
            'hotspot_heading',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Enter heading', 'somnia'),
                'label_block' => true,
                'default' => __('Your summer, your story.', 'somnia'),
            ]
        );
        $repeater->add_control(
            'hotspot_button_text',
            [
                'label' => __('Button Text', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Enter button text', 'somnia'),
                'label_block' => true,
                'default' => __('Explore Looks', 'somnia'),
            ]
        );
        $repeater->add_control(
            'hotspot_button_link',
            [
                'label' => __('Button Link', 'somnia'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'somnia'),
                'show_external' => true,
                'default' => [
                    'url' => '#',
                    'is_external' => true,
                    'nofollow' => true,
                ],
            ]
        );
        $repeater->add_control(
            'show_content_on_mobile',
            [
                'label' => __('Show Content on mobile?', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'yes',
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

        $repeater->add_control(
            'hotspot_point_heading',
            [
                'label' => __('Hotspot Point Settings', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_responsive_control(
            'hotspot_position_x',
            [
                'label' => __('Hotspot X Position', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'left: {{SIZE}}%;',
                ],
            ]
        );
        $repeater->add_responsive_control(
            'hotspot_position_y',
            [
                'label' => __('Hotspot Y Position', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'top: {{SIZE}}%;',
                ],
            ]
        );

        $repeater->add_control(
            'product_position_heading',
            [
                'label' => __('Product Position Settings', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_responsive_control(
            'hotspot_product_position_x',
            [
                'label' => __('Product X Position', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-product-item' => 'left: {{SIZE}}%;',
                ],
                'devices' => ['desktop', 'laptop', 'tablet_extra', 'mobile_extra'],
            ]
        );
        $repeater->add_responsive_control(
            'hotspot_product_position_y',
            [
                'label' => __('Product Y Position', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-product-item' => 'top: {{SIZE}}%;',
                ],
                'devices' => ['desktop', 'laptop', 'tablet_extra', 'mobile_extra'],
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
                        ],
                        'hotspot_product_position_x' => [
                            'unit' => '%',
                            'size' => 70,
                        ],
                        'hotspot_product_position_y' => [
                            'unit' => '%',
                            'size' => 50,
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
                        ],
                        'hotspot_product_position_x' => [
                            'unit' => '%',
                            'size' => 70,
                        ],
                        'hotspot_product_position_y' => [
                            'unit' => '%',
                            'size' => 50,
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
                            'size' => 50,
                        ],
                        'hotspot_product_position_x' => [
                            'unit' => '%',
                            'size' => 70,
                        ],
                        'hotspot_product_position_y' => [
                            'unit' => '%',
                            'size' => 50,
                        ]
                    ],

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
        $this->end_controls_section();
    }
    protected function register_style_content_section_controls()
    {

        $this->start_controls_section(
            'section_style_hotspot',
            [
                'label' => __('Hotspot Points', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'hotspot_color',
            [
                'label' => __('Hotspot Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-banner-scroll-hotspot--item-image .bt-hotspot-points .bt-hotspot-point .bt-hotspot-marker' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .bt-product-banner-scroll-hotspot--item-image .bt-hotspot-points .bt-hotspot-point .bt-hotspot-marker::before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .bt-product-banner-scroll-hotspot--item-image .bt-hotspot-points .bt-hotspot-point .bt-hotspot-marker::after' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Style Content', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'items_spacing',
            [
                'label' => __('Space Between Items', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-banner-scroll-hotspot--item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'background_overlay_color',
            [
                'label' => __('Background Overlay Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-banner-scroll-hotspot--item-image-inner:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'heading_style',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'heading_color',
            [
                'label' => __('Heading Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-heading' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'label' => __('Heading Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-heading',
            ]
        );
        $this->add_control(
            'button_style',
            [
                'label' => __('Button', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs('button_style_tabs');

        $this->start_controls_tab(
            'button_normal_tab',
            [
                'label' => __('Normal', 'somnia'),
            ]
        );

        $this->add_responsive_control(
            'button_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-button-wrapper a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-button-wrapper a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .bt-button-wrapper a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'button_border_width',
            [
                'label' => __('Border Width', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .bt-button-wrapper a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_border_color',
            [
                'label' => __('Border Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-button-wrapper a' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-button-wrapper a',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover_tab',
            [
                'label' => __('Hover', 'somnia'),
            ]
        );

        $this->add_responsive_control(
            'button_hover_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-button-wrapper a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_hover_background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-button-wrapper a:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_hover_border_color',
            [
                'label' => __('Border Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-button-wrapper a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_product_display',
            [
                'label' => __('Product Display', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'product_background_color',
            [
                'label' => __('Product Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-banner-scroll-hotspot--item-image .bt-hotspot-product-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'product_title_typography',
                'label' => __('Title Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-product-banner-scroll-hotspot--item-image .bt-hotspot-product-item .bt-product-content--inner .bt-product-name',
            ]
        );
        $this->add_control(
            'product_title_color',
            [
                'label' => __('Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-banner-scroll-hotspot--item-image .bt-hotspot-product-item .bt-product-content--inner .bt-product-name' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'add_to_cart_button_color',
            [
                'label' => __('Add to Cart Button Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-banner-scroll-hotspot--item-image .bt-hotspot-product-item .bt-product-content .bt-product-add-to-cart a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'add_to_cart_button_hover_color',
            [
                'label' => __('Add to Cart Button Hover Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-banner-scroll-hotspot--item-image .bt-hotspot-product-item .bt-product-content .bt-product-add-to-cart a:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_controls()
    {
        $this->register_layout_section_controls();
        $this->register_style_content_section_controls();
    }

    protected function render()
    {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        $settings = $this->get_settings_for_display();

        $hotspot_items = $settings['hotspot_items'];
?>
        <div class="bt-elwg-product-banner-scroll-hotspot--default">
            <div class="bt-product-banner-scroll-hotspot">
                <?php foreach ($hotspot_items as $item): ?>
                    <div class="bt-product-banner-scroll-hotspot--item">
                        <div class="bt-product-banner-scroll-hotspot--item-inner">
                            <div class="bt-product-banner-scroll-hotspot--item-image <?php echo esc_attr($item['show_mobile_image'] === 'yes' ? 'bt-mobile-image' : ''); ?>">

                                <div class="bt-product-banner-scroll-hotspot--item-image-inner">
                                    <?php
                                    if (!empty($item['hotspot_image']['id'])) {
                                        echo wp_get_attachment_image($item['hotspot_image']['id'], $settings['thumbnail_size'], false, ['class' => 'bt-hotspot-image']);
                                    } else {
                                        echo '<img src="' . esc_url($item['hotspot_image']['url']) . '" alt="" class="bt-hotspot-image">';
                                    }

                                    if ($item['show_mobile_image'] === 'yes') {
                                        if (!empty($item['hotspot_image_mobile']['id'])) {
                                            echo wp_get_attachment_image($item['hotspot_image_mobile']['id'], $settings['thumbnail_size'], false, ['class' => 'bt-mobile-image-mobile']);
                                        } else {
                                            echo '<img src="' . esc_url($item['hotspot_image_mobile']['url']) . '" alt="" class="bt-mobile-image-mobile">';
                                        }
                                    }
                                    ?>
                                    <div class="bt-product-banner-scroll-hotspot--item-content <?php echo esc_attr($item['show_content_on_mobile'] != 'yes' ? 'bt-no-content-mobile' : ''); ?>">
                                        <div class="bt-container">
                                            <?php
                                            if (!empty($item['hotspot_heading'])) {
                                                echo '<h2 class="bt-heading">' . esc_html($item['hotspot_heading']) . '</h2>';
                                            }
                                            if (!empty($item['hotspot_button_text']) && !empty($item['hotspot_button_link']['url'])) {
                                                echo '<div class="bt-button-wrapper"><a href="' . esc_url($item['hotspot_button_link']['url']) . '" class="bt-button">' . esc_html($item['hotspot_button_text']) . '</a></div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="bt-hotspot-points">
                                        <div class="bt-hotspot-point elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>"
                                            data-product-id="<?php echo esc_attr($item['id_product']); ?>">
                                            <div class="bt-hotspot-marker"></div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $product_id = $item['id_product'];
                                $product = wc_get_product($product_id);

                                if ($product) {
                                ?>
                                    <div class="bt-hotspot-product-item elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>">
                                        <a class="bt-hotspot-product-thumbnail"
                                            href="<?php echo esc_url($product->get_permalink()); ?>">
                                            <?php
                                            if (has_post_thumbnail($product_id)) {
                                                echo get_the_post_thumbnail($product_id, 'thumbnail');
                                            } else {
                                                echo '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '" class="wp-post-image" />';
                                            }
                                            ?>
                                        </a>
                                        <div class="bt-product-content">
                                            <div class="bt-product-content--inner">
                                                <h4 class="bt-product-name">
                                                    <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                                        <?php echo esc_html($product->get_name()); ?>
                                                    </a>
                                                </h4>
                                                <?php
                                                if ($product->is_type('variable')) {
                                                    do_action('somnia_woocommerce_template_single_add_to_cart');
                                                }
                                                ?>
                                                <?php
                                                $price_class = $product->is_type( 'variable' ) ? 'bt-product-variable' : '';
                                                $price_html  = $product->get_price_html();
                                                ?>
                                                <div class="bt-price <?php echo esc_attr( $price_class ); ?>">
                                                    <?php echo wp_kses_post( $price_html ); ?>
                                                </div>
                                            </div>
                                            <div class="bt-product-add-to-cart">
                                                <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                                                    <a href="?add-to-cart=<?php echo esc_attr($product->get_id()); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product->get_id()); ?>" data-quantity="1" class="bt-button product_type_simple add_to_cart_button ajax_add_to_cart bt-button-hover" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
                                                <?php else : ?>
                                                    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="bt-button bt-view-product"><?php echo esc_html__('View Product', 'somnia'); ?></a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>

                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
