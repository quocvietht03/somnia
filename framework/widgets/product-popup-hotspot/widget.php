<?php

namespace SomniaElementorWidgets\Widgets\ProductPopupHotspot;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

class Widget_ProductPopupHotspot extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-popup-hotspot';
    }

    public function get_title()
    {
        return __('Product Popup Hotspot', 'somnia');
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
        return ['magnific-popup', 'elementor-widgets'];
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
            'show_tooltip',
            [
                'label' => __('Show Tooltip', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'enable_mobile_tooltip_style',
            [
                'label' => __('Enable Mobile Tooltip Style', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('Enable compact mobile style for tooltips on all screen sizes', 'somnia'),
                'condition' => [
                    'show_tooltip' => 'yes',
                ],
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
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'left: {{SIZE}}%;',
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
                            'size' => 30,
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
                    '{{WRAPPER}} .bt-elwg-product-popup-hotspot--default .bt-hotspot-point .bt-hotspot-marker' => 'border-color: {{VALUE}} !important;',
                    '{{WRAPPER}} .bt-elwg-product-popup-hotspot--default .bt-hotspot-point .bt-hotspot-marker::before' => 'border-color: {{VALUE}} !important;',
                    '{{WRAPPER}} .bt-elwg-product-popup-hotspot--default .bt-hotspot-point .bt-hotspot-marker::after' => 'border-color: {{VALUE}} !important;',
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

        $widget_id = $this->get_id();

        // Add mobile tooltip class if enabled
        $mobile_tooltip_class = ($settings['enable_mobile_tooltip_style'] === 'yes') ? ' bt-hotspot-product-mobile' : '';

?>
        <div class="bt-elwg-product-popup-hotspot--default<?php echo esc_attr($mobile_tooltip_class); ?>">
            <div class="bt-product-popup-hotspot">
                <div class="bt-hotspot-image-wrapper">
                    <div class="bt-hotspot-image">
                        <?php
                        if (!empty($settings['hotspot_image']['id'])) {
                            echo wp_get_attachment_image($settings['hotspot_image']['id'], $settings['thumbnail_size']);
                        } else {
                            echo '<img src="' . esc_url($settings['hotspot_image']['url']) . '" alt="' . esc_html__('Hotspot Image', 'somnia') . '">';
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
                                        <div class="bt-hotspot-marker"></div>
                                        <?php if ($settings['show_tooltip'] === 'yes') : ?>
                                            <div class="bt-hotspot-product-info">
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
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($settings['hotspot_items'])) : ?>
                        <div class="bt-popup-trigger-wrapper">
                            <a href="#bt-popup-<?php echo esc_attr($widget_id); ?>" class="bt-open-popup-btn bt-js-open-popup-link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256">
                                    <path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"></path>
                                </svg>
                                <span><?php echo esc_html__('View Entire Look', 'somnia'); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Hidden Popup Content -->
                <div id="bt-popup-<?php echo esc_attr($widget_id); ?>" class="bt-product-popup-hotspot-modal mfp-hide">
                    <div class="bt-popup-inner">
                        <div class="bt-popup-image">
                            <?php
                            if (!empty($settings['hotspot_image']['id'])) {
                                echo wp_get_attachment_image($settings['hotspot_image']['id'], $settings['thumbnail_size']);
                            } else {
                                echo '<img src="' . esc_url($settings['hotspot_image']['url']) . '" alt="' . esc_html__('Hotspot Image', 'somnia') . '">';
                            }
                            ?>
                        </div>
                        <div class="bt-popup-products">
                            <h3 class="bt-popup-title"><?php echo esc_html__('Complete the look', 'somnia'); ?></h3>
                            <ul class="bt-hotspot-product-list">
                                <?php
                                if (!empty($settings['hotspot_items'])) {
                                    $hotspot_product_ids = [];
                                    foreach ($settings['hotspot_items'] as $item) {
                                        if (!empty($item['id_product'])) {
                                            $hotspot_product_ids[] = $item['id_product'];
                                        }
                                    }

                                    if (!empty($hotspot_product_ids)) {
                                        $args = [
                                            'post_type'      => 'product',
                                            'post__in'       => $hotspot_product_ids,
                                            'posts_per_page' => -1,
                                            'orderby'        => 'post__in',
                                        ];
                                        $hotspot_query = new \WP_Query($args);
                                        $product_index = 1;

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
                                ?>
                                                <li class="bt-hotspot-product-list__item"
                                                    data-product-currency="<?php echo esc_attr(get_woocommerce_currency_symbol(get_woocommerce_currency())); ?>"
                                                    data-product-single-price="<?php echo esc_attr($product->get_sale_price() ? $product->get_sale_price() : $product->get_regular_price()); ?>"
                                                    data-product-id="<?php echo esc_attr($product_id); ?>">
                                                    <a class="bt-hotspot-product-thumbnail" href="<?php echo esc_url($product->get_permalink()); ?>">
                                                        <?php
                                                        if (has_post_thumbnail($product_id)) {
                                                            echo get_the_post_thumbnail($product_id, 'medium');
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
                                                                <a href="?add-to-cart=<?php echo esc_attr($product->get_id()); ?>"
                                                                    aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product->get_id()); ?>"
                                                                    data-quantity="1"
                                                                    class="bt-button product_type_simple add_to_cart_button ajax_add_to_cart bt-button-hover"
                                                                    data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                                                                    data-product_sku=""
                                                                    rel="nofollow">
                                                                    <?php echo esc_html__('Add to cart', 'somnia'); ?>
                                                                </a>
                                                            <?php else : ?>
                                                                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="bt-button bt-view-product">
                                                                    <?php echo esc_html__('View Product', 'somnia'); ?>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </li>
                                <?php
                                                $product_index++;
                                            endwhile;
                                            wp_reset_postdata();
                                        endif;
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
