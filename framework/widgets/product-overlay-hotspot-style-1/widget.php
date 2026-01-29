<?php

namespace SomniaElementorWidgets\Widgets\ProductOverlayHotspotStyle1;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

class Widget_ProductOverlayHotspotStyle1 extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-overlay-hotspot-style-1';
    }

    public function get_title()
    {
        return __('Product Overlay Hotspot Style 1', 'somnia');
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

        $this->add_control(
            'shop_the_look_text',
            [
                'label' => __('Shop The Look Text', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Shop The Look', 'somnia'),
                'placeholder' => __('Enter text', 'somnia'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'add_all_to_cart_text',
            [
                'label' => __('Add All To Cart Text', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('ADD ALL TO CART', 'somnia'),
                'placeholder' => __('Enter text', 'somnia'),
                'label_block' => true,
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
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'left: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'top: {{SIZE}}{{UNIT}};',
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
            'section_style_shop_panel',
            [
                'label' => __('Shop Panel', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'shop_panel_header_title_color',
            [
                'label' => __('Header Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-shop-panel-header .bt-shop-panel-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'shop_panel_header_title_typography',
                'label' => __('Header Title Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-shop-panel-header .bt-shop-panel-title',
            ]
        );

        $this->add_control(
            'shop_panel_count_color',
            [
                'label' => __('Count Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-shop-panel-header .bt-shop-panel-count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'shop_panel_count_typography',
                'label' => __('Count Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-shop-panel-header .bt-shop-panel-count',
            ]
        );

        $this->add_control(
            'shop_panel_close_color',
            [
                'label' => __('Close Button Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-shop-panel-header .bt-shop-panel-close svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'shop_panel_add_all_cart_heading',
            [
                'label' => __('Add All to Cart Button', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'shop_panel_add_all_cart_background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-shop-the-look-panel .bt-shop-panel-content-footer ' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'shop_panel_add_all_cart_text_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-shop-the-look-panel .bt-shop-panel-content-footer .bt-button-add-set-to-cart' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'shop_panel_add_all_cart_typography',
                'label' => __('Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-shop-the-look-panel .bt-shop-panel-content-footer .bt-button-add-set-to-cart',
            ]
        );

  

        $this->add_control(
            'shop_panel_footer_count_color',
            [
                'label' => __('Footer Count Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-shop-the-look-panel .bt-shop-panel-content-footer .bt-shop-panel-footer-count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'shop_panel_footer_count_typography',
                'label' => __('Footer Count Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-shop-the-look-panel .bt-shop-panel-content-footer .bt-shop-panel-footer-count',
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

        // Check if any products are selected
        $has_products = false;
        if (!empty($settings['hotspot_items'])) {
            foreach ($settings['hotspot_items'] as $item) {
                if (!empty($item['id_product'])) {
                    $has_products = true;
                    break;
                }
            }
        }

        // Show message if no products selected
        if (!$has_products) {
            echo '<div class="bt-elwg-product-overlay-hotspot-style-1--default">';
            echo '<div class="bt-product-overlay-hotspot-empty-message">';
            echo '<p>' . esc_html__('Please select products in the hotspot items.', 'somnia') . '</p>';
            echo '</div>';
            echo '</div>';
            return;
        }

        // Build $product_ids array with proper structure
        $product_ids = [];
        $product_count = 0;
        if (!empty($settings['hotspot_items'])) {
            foreach ($settings['hotspot_items'] as $item) {
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
                $product_count++;
            }
        }

?>
        <div class="bt-elwg-product-overlay-hotspot-style-1--default">
            <div class="bt-product-overlay-hotspot-style-1">
                <div class="bt-hotspot-product--image">
                    <div class="bt-hotspot-image">
                        <?php
                        if ($settings['hotspot_image']['id']) {
                            echo wp_get_attachment_image($settings['hotspot_image']['id'], $settings['thumbnail_size']);
                        } else {
                            echo '<img src="' . esc_url($settings['hotspot_image']['url']) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '">';
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
                                    <div class="bt-hotspot-point elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>"
                                        data-product-id="<?php echo esc_attr($item['id_product']); ?>">
                                        <div class="bt-hotspot-marker">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Shop the Look Panel -->
                <div class="bt-shop-the-look-panel">
                    <div class="bt-shop-panel-header">
                        <h2 class="bt-shop-panel-title"><?php echo esc_html(!empty($settings['shop_the_look_text']) ? $settings['shop_the_look_text'] : __('Shop The Look', 'somnia')); ?></h2>
                        <span class="bt-shop-panel-count"><?php echo esc_html($product_count); ?> <?php echo esc_html__('Products', 'somnia'); ?></span>
                        <button class="bt-shop-panel-close" aria-label="<?php echo esc_attr__('Close', 'somnia'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M16.0675 15.1832C16.1256 15.2412 16.1717 15.3102 16.2031 15.386C16.2345 15.4619 16.2507 15.5432 16.2507 15.6253C16.2507 15.7075 16.2345 15.7888 16.2031 15.8647C16.1717 15.9405 16.1256 16.0095 16.0675 16.0675C16.0095 16.1256 15.9405 16.1717 15.8647 16.2031C15.7888 16.2345 15.7075 16.2507 15.6253 16.2507C15.5432 16.2507 15.4619 16.2345 15.386 16.2031C15.3102 16.1717 15.2412 16.1256 15.1832 16.0675L10.0003 10.8839L4.81753 16.0675C4.70026 16.1848 4.5412 16.2507 4.37535 16.2507C4.2095 16.2507 4.05044 16.1848 3.93316 16.0675C3.81588 15.9503 3.75 15.7912 3.75 15.6253C3.75 15.4595 3.81588 15.3004 3.93316 15.1832L9.11675 10.0003L3.93316 4.81753C3.81588 4.70026 3.75 4.5412 3.75 4.37535C3.75 4.2095 3.81588 4.05044 3.93316 3.93316C4.05044 3.81588 4.2095 3.75 4.37535 3.75C4.5412 3.75 4.70026 3.81588 4.81753 3.93316L10.0003 9.11675L15.1832 3.93316C15.3004 3.81588 15.4595 3.75 15.6253 3.75C15.7912 3.75 15.9503 3.81588 16.0675 3.93316C16.1848 4.05044 16.2507 4.2095 16.2507 4.37535C16.2507 4.5412 16.1848 4.70026 16.0675 4.81753L10.8839 10.0003L16.0675 15.1832Z" fill="#181818" />
                            </svg>
                        </button>
                    </div>
                    <div class="bt-shop-panel-content" style="display: none;">
                        <div class="bt-shop-panel-content-header">
                            <?php if (!empty($settings['hotspot_items'])) : ?>
                                <ul class="bt-hotspot-product-list">
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
                                                <li class="bt-hotspot-product-list__item bt-product-item<?php echo esc_attr($out_of_stock_class); ?>"
                                                    data-product-currency="<?php echo esc_attr($product_currencySymbol); ?>"
                                                    data-product-single-price="<?php echo esc_attr($product->get_sale_price() ? $product->get_sale_price() : $product->get_regular_price()); ?>"
                                                    data-product-id="<?php echo esc_attr($product_id); ?>"
                                                    data-in-stock="<?php echo esc_attr($is_in_stock ? '1' : '0'); ?>">
                                                    <a class="bt-product-thumbnail" href="<?php echo esc_url($product->get_permalink()); ?>">
                                                        <?php
                                                        if (has_post_thumbnail($product_id)) {
                                                            echo get_the_post_thumbnail($product_id, 'thumbnail');
                                                        } else {
                                                            echo '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '" class="wp-post-image" />';
                                                        }
                                                        ?>
                                                    </a>
                                                    <div class="bt-product-content">
                                                        <h4 class="bt-product-name">
                                                            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                                                <?php echo esc_html($product->get_name()); ?>
                                                            </a>
                                                        </h4>
                                                        <?php if (!$product->is_type('variable')) : ?>
                                                            <?php echo wc_get_stock_html($product); // WPCS: XSS ok. ?>
                                                        <?php endif; ?>
                                                        <?php
                                                        if ($product->is_type('variable')) {
                                                            do_action('somnia_woocommerce_template_single_add_to_cart');
                                                        }
                                                        $price_class = $product->is_type( 'variable' ) ? 'bt-product-variable' : '';
                                                        $price_html  = $product->get_price_html();
                                                        ?>
                                                        <div class="bt-price <?php echo esc_attr( $price_class ); ?> ?>">
                                                             <?php echo wp_kses_post( $price_html ); ?>
                                                        </div>
                                                    </div>
                                                    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="bt-product-link">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                            <path d="M17.3172 10.4425L11.6922 16.0675C11.5749 16.1848 11.4159 16.2507 11.25 16.2507C11.0841 16.2507 10.9251 16.1848 10.8078 16.0675C10.6905 15.9503 10.6247 15.7912 10.6247 15.6253C10.6247 15.4595 10.6905 15.3004 10.8078 15.1832L15.3664 10.6253H3.125C2.95924 10.6253 2.80027 10.5595 2.68306 10.4423C2.56585 10.3251 2.5 10.1661 2.5 10.0003C2.5 9.83459 2.56585 9.67562 2.68306 9.55841C2.80027 9.4412 2.95924 9.37535 3.125 9.37535H15.3664L10.8078 4.81754C10.6905 4.70026 10.6247 4.5412 10.6247 4.37535C10.6247 4.2095 10.6905 4.05044 10.8078 3.93316C10.9251 3.81588 11.0841 3.75 11.25 3.75C11.4159 3.75 11.5749 3.81588 11.6922 3.93316L17.3172 9.55816C17.3753 9.61621 17.4214 9.68514 17.4529 9.76101C17.4843 9.83688 17.5005 9.91821 17.5005 10.0003C17.5005 10.0825 17.4843 10.1638 17.4529 10.2397C17.4214 10.3156 17.3753 10.3845 17.3172 10.4425Z" fill="#181818" />
                                                        </svg>
                                                    </a>
                                                </li>
                                    <?php
                                            endwhile;
                                            wp_reset_postdata();
                                        endif;
                                    }
                                    ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($product_ids)) : ?>
                            <div class="bt-shop-panel-content-footer">
                                <a class="bt-button bt-button-add-set-to-cart" data-ids="<?php echo esc_attr(json_encode($product_ids)); ?>" href="#">
                                    <?php echo esc_html(!empty($settings['add_all_to_cart_text']) ? $settings['add_all_to_cart_text'] : __('ADD ALL TO CART', 'somnia')); ?> (<?php echo esc_html($product_count); ?>)
                                    <span class="bt-btn-price"></span>
                                </a>
                                <span class="bt-shop-panel-footer-count"><?php echo esc_html($product_count); ?> <?php echo esc_html__('Products', 'somnia'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
