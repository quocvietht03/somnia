<?php

namespace SomniaElementorWidgets\Widgets\ProductOverlayHotspot;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class Widget_ProductOverlayHotspot extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-overlay-hotspot';
    }

    public function get_title()
    {
        return __('Product Overlay Hotspot', 'somnia');
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

        return $supported_ids;
    }

    protected function register_layout_section_controls()
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Hotspot Content', 'somnia'),
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

        $repeater->add_control(
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
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'left: {{SIZE}}%;',
                ],
            ]
        );

        $repeater->add_control(
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
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'top: {{SIZE}}%;',
                ],
            ]
        );

        $this->add_control(
            'hotspot_items',
            [
                'label' => __('Hotspot Points', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'id_product' => '',
                        'hotspot_position_x' => [
                            'unit' => '%',
                            'size' => 30,
                        ],
                        'hotspot_position_y' => [
                            'unit' => '%',
                            'size' => 20,
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
                            'size' => 60,
                        ]
                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_section_controls()
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
                    '{{WRAPPER}} .bt-hotspot-product--image .bt-hotspot-points .bt-hotspot-point .bt-hotspot-marker' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .bt-hotspot-product--image .bt-hotspot-points .bt-hotspot-point .bt-hotspot-marker::before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .bt-hotspot-product--image .bt-hotspot-points .bt-hotspot-point .bt-hotspot-marker::after' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_product',
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
                    '{{WRAPPER}} .bt-hotspot-product-display .bt-product-item-minimal' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'product_title_typography',
                'label' => __('Title Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-hotspot-product-display .bt-product-item-minimal .bt-product-title',
            ]
        );

        $this->add_control(
            'product_title_color',
            [
                'label' => __('Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product-display .bt-product-item-minimal .bt-product-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'product_price_typography',
                'label' => __('Price Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-hotspot-product-display .bt-product-item-minimal .bt-product-price .woocommerce-Price-amount',
            ]
        );

        $this->add_control(
            'product_price_color',
            [
                'label' => __('Price Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product-display .bt-product-item-minimal .bt-product-price .woocommerce-Price-amount' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bt-hotspot-product-display .bt-product-item-minimal .bt-product-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'product_regular_price_color',
            [
                'label' => __('Regular Price Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product-display .bt-product-item-minimal .bt-product-price del .woocommerce-Price-amount' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'add_to_cart_button_color',
            [
                'label' => __('Add to Cart Button Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product-display .bt-product-item-minimal .bt-product-add-to-cart a' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'add_to_cart_button_hover_color',
            [
                'label' => __('Add to Cart Button Hover Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-product-display .bt-product-item-minimal .bt-product-add-to-cart a:hover' => 'background-color: {{VALUE}};',
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

        ?>
        <div class="bt-elwg-product-overlay-hotspot--default">
            <div class="bt-product-overlay-hotspot">
                <div class="bt-hotspot-product--image">
                    <div class="bt-hotspot-image">
                        <?php
                            if ($settings['hotspot_image']['id']) {
                                echo wp_get_attachment_image ( $settings['hotspot_image']['id'], $settings['thumbnail_size'] );
                            } else {
                                echo '<img src="' . esc_url($settings['hotspot_image']['url']) . '" alt="' . esc_html__( 'Awaiting product image', 'somnia' ) . '">';
                            }
                        ?>
                    </div>

                    <?php if (!empty($settings['hotspot_items'])) : ?>
                        <div class="bt-hotspot-points">
                            <?php foreach ($settings['hotspot_items'] as $index => $item) : ?>
                                <?php
                                $product = wc_get_product($item['id_product']);
                                if ($product) :
                                ?>
                                    <div class="bt-hotspot-point elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> "
                                        data-product-id="<?php echo esc_attr($item['id_product']); ?>">
                                        <div class="bt-hotspot-marker">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bt-hotspot-product-display">
                    <?php if (!empty($settings['hotspot_items'])) : ?>
                        <?php foreach ($settings['hotspot_items'] as $index => $item) : ?>
                            <?php
                            $product = wc_get_product($item['id_product']);
                            if ($product) :
                                $is_variable = $product->is_type('variable') ? 'bt-product-variable' : '';
                                ?>
                                <div class="bt-product-item-minimal <?php echo esc_attr($is_variable); ?>" data-product-id="<?php echo esc_attr($item['id_product']); ?>">
                                    <div class="bt-product-thumbnail">
                                        <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                            <?php 
                                                if ( has_post_thumbnail($item['id_product']) ) {
                                                    echo get_the_post_thumbnail($item['id_product'], 'thumbnail'); 
                                                } else {
                                                    echo '<img src="'. esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ) . '" alt="'. esc_html__( 'Awaiting product image', 'somnia' ) .'" class="wp-post-image" />';
                                                }
                                            ?>
                                        </a>
                                    </div>
                                    <div class="bt-product-content">
                                        <h4 class="bt-product-title"><a href="<?php echo esc_url($product->get_permalink()); ?>" class="bt-product-link"><?php echo esc_html($product->get_name()); ?></a></h4>
                                        <div class="bt-product-price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                                        <div class="bt-product-add-to-cart">
                                            <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                                                <a href="?add-to-cart=<?php echo esc_attr($product->get_id()); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product->get_id()); ?>" data-quantity="1" class="bt-button product_type_simple add_to_cart_button ajax_add_to_cart bt-button-hover" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
                                            <?php else : ?>
                                                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="bt-button bt-view-product"><?php echo esc_html__('View Product', 'somnia'); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
