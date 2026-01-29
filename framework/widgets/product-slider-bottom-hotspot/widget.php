<?php

namespace SomniaElementorWidgets\Widgets\ProductSliderBottomHotspot;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

class Widget_ProductSliderBottomHotspot extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-slider-bottom-hotspot';
    }

    public function get_title()
    {
        return __('Product Slider Bottom Hotspot', 'somnia');
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
        $this->add_control(
            'show_heading',
            [
                'label' => __('Show Heading', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'heading',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Complete Your Style', 'somnia'),
                'placeholder' => __('Enter heading', 'somnia'),
                'label_block' => true,
                'condition' => [
                    'show_heading' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'show_add_set_to_cart',
            [
                'label' => __('Show Add Set to Cart', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'yes',
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
            'slider_position',
            [
                'label' => __('Slider Position', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'outside',
                'options' => [
                    'inside' => __('Inside', 'somnia'),
                    'outside' => __('Outside', 'somnia'),
                ],
            ]
        );

        $this->add_control(
            'slider_direction',
            [
                'label' => __('Slider Direction', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'ltr',
                'options' => [
                    'ltr' => __('Left to Right', 'somnia'),
                    'rtl' => __('Right to Left', 'somnia'),
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

        $this->add_responsive_control(
            'slider_padding',
            [
                'label' => __('Slider Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-slider-bottom-hotspot__list-products' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            echo '<div class="bt-elwg-product-slider-bottom-hotspot--default">';
            echo '<div class="bt-product-slider-bottom-hotspot-empty-message">';
            echo '<p>' . esc_html__('Please select products in the hotspot items.', 'somnia') . '</p>';
            echo '</div>';
            echo '</div>';
            return;
        }

?>
        <div class="bt-elwg-product-slider-bottom-hotspot--default bt-slider-position-<?php echo esc_attr($settings['slider_position']); ?>" data-slider-direction="<?php echo esc_attr($settings['slider_direction']); ?>">
            <?php if ($settings['show_heading'] === 'yes' && !empty($settings['heading'])) : ?>
                <h3 class="bt-heading"><?php echo esc_html($settings['heading']); ?></h3>
            <?php endif; ?>
            <div class="bt-product-slider-bottom-hotspot">
                <div class="bt-product-slider-bottom-hotspot__image">
                    <div class="bt-hotspot-image" style="position: relative;">
                        <?php
                        if ($settings['hotspot_image']['id']) {
                            echo wp_get_attachment_image($settings['hotspot_image']['id'], $settings['thumbnail_size']);
                        } else {
                            echo '<img src="' . esc_url($settings['hotspot_image']['url']) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '">';
                        }
                        ?>
                        <?php if (!empty($settings['hotspot_items'])) :
                        ?>
                            <div class="bt-hotspot-points">
                                <?php foreach ($settings['hotspot_items'] as $index => $item) :
                                    $product = wc_get_product($item['id_product']);
                                    if ($product) :
                                        ?>
                                            <div class="bt-hotspot-point elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>"
                                                data-product-id="<?php echo esc_attr($item['id_product']); ?>">
                                                <?php echo '<div class="bt-hotspot-marker">' . $index + 1 . '</div>'; ?>
                                            </div>
                                        <?php 
                                    endif;
                                endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
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
                    if (!empty($product_ids) && $settings['show_add_set_to_cart'] === 'yes') :
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
                </div>
                <div class="bt-product-slider-bottom-hotspot__list-products">

                    <?php if (!empty($settings['hotspot_items'])) : ?>
                        <ul class="bt-hotspot-product-list swiper swiper-container">
                            <div class="swiper-wrapper">
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

                                    // If RTL mode, reverse the product IDs order
                                    if ($settings['slider_direction'] === 'rtl') {
                                        $hotspot_product_ids = array_reverse($hotspot_product_ids);
                                        $total_items = count($hotspot_product_ids);
                                    }

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
                                            <li class="bt-hotspot-product-list__item swiper-slide<?php echo esc_attr($out_of_stock_class); ?>"
                                                data-product-currency="<?php echo esc_attr($product_currencySymbol); ?>"
                                                data-product-single-price="<?php echo esc_attr($product->get_sale_price() ? $product->get_sale_price() : $product->get_regular_price()); ?>"
                                                data-product-id="<?php echo esc_attr($product_id); ?>"
                                                data-in-stock="<?php echo esc_attr($is_in_stock ? '1' : '0'); ?>">
                                                <div class="bt-number-product">
                                                    <?php
                                                    if ($settings['slider_direction'] === 'rtl') {
                                                        echo esc_html($total_items);
                                                    } else {
                                                        echo esc_html($index);
                                                    }
                                                    ?>
                                                </div>
                                                <a class="bt-hotspot-product-thumbnail" href="<?php echo esc_url($product->get_permalink()); ?>">
                                                    <?php
                                                    if (has_post_thumbnail($product_id)) {
                                                        echo get_the_post_thumbnail($product_id, 'thumbnail');
                                                    } else {
                                                        echo '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '" class="wp-post-image" />';
                                                    }
                                                    ?>
                                                </a>
                                                <div class="bt-product-content">
                                                    <div class="bt-product-content__inner">
                                                        <h4 class="bt-product-name">
                                                            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                                                <?php echo esc_html($product->get_name()); ?>
                                                            </a>
                                                        </h4>
                                                        <?php if (!$product->is_type('variable')) : ?>
                                                            <?php echo wc_get_stock_html($product); // WPCS: XSS ok. 
                                                            ?>
                                                        <?php endif; ?>
                                                        <?php
                                                        if ($product->is_type('variable')) {
                                                            do_action('somnia_woocommerce_template_single_add_to_cart');
                                                        }
                                                        
                                                        $price_class = $product->is_type( 'variable' ) ? 'bt-product-variable' : '';
                                                        $price_html  = $product->get_price_html();
                                                        ?>
                                                        <p class="bt-price <?php echo esc_attr( $price_class ); ?>">
                                                            <?php echo wp_kses_post( $price_html ); ?>
                                                        </p>
                                                    </div>
                                                    <div class="bt-product-add-to-cart">
                                                        <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                                                            <a href="?add-to-cart=<?php echo esc_attr($product->get_id()); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product->get_id()); ?>" data-quantity="1" class="bt-button product_type_simple add_to_cart_button ajax_add_to_cart bt-button-hover" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
                                                        <?php else : ?>
                                                            <a href="<?php echo esc_url($product->get_permalink()); ?>" class="bt-button bt-view-product"><?php echo esc_html__('View Product', 'somnia'); ?></a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </li>
                                <?php
                                            if ($settings['slider_direction'] === 'rtl') {
                                                $total_items--;
                                            } else {
                                                $index++;
                                            }
                                        endwhile;
                                        wp_reset_postdata();
                                    endif;
                                }
                                ?>
                            </div>
                            <div class="bt-swiper-navigation">
                                <div class="bt-button-prev"></div>
                                <div class="bt-button-next"></div>
                            </div>
                            <div class="bt-swiper-pagination"></div>

                        </ul>
                    <?php endif; ?>

                </div>

            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
