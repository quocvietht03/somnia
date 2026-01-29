<?php

namespace SomniaElementorWidgets\Widgets\ProductListHotspot;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

class Widget_ProductListHotspot extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-list-hotspot';
    }

    public function get_title()
    {
        return __('Product List Hotspot', 'somnia');
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
            'sub_heading',
            [
                'label' => __('Sub Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Enter sub heading', 'somnia'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'heading',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Enter heading', 'somnia'),
                'label_block' => true,
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
                ],
                'default' => [
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-image img' => 'width: {{SIZE}}{{UNIT}}; margin-left:calc(-1 * ({{SIZE}}{{UNIT}} - 100%) / 2);',
                ],
            ]
        );
        $this->add_control(
            'image_position',
            [
                'label' => __('Image Position', 'somnia'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'row-reverse' => [
                        'title' => __('Left', 'somnia'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'row' => [
                        'title' => __('Right', 'somnia'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'row',
                'toggle' => false,
                'label_block' => false,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-list-hotspot' => 'flex-direction: {{VALUE}};',
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
        $this->add_control(
            'content_position',
            [
                'label' => __('Content Position', 'somnia'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'somnia'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'somnia'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'somnia'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => false,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-list-hotspot__list-products' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'sub_heading_color',
            [
                'label' => __('Sub Heading Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-list-hotspot__list-products .bt-list-header .bt-sub-heading' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'sub_heading_typography',
                'label'    => __('Typography', 'somnia'),
                'default'  => '',
                'selector' => '{{WRAPPER}} .bt-product-list-hotspot__list-products .bt-list-header .bt-sub-heading',
            ]
        );
        $this->add_control(
            'heading_color',
            [
                'label' => __('Heading Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-list-hotspot__list-products .bt-list-header .bt-heading' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'label' => __('Typography', 'somnia'),
                'default'  => '',
                'selector' => '{{WRAPPER}} .bt-product-list-hotspot__list-products .bt-list-header .bt-heading',
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
            echo '<div class="bt-elwg-product-list-hotspot--default">';
            echo '<div class="bt-product-list-hotspot-empty-message">';
            echo '<p>' . esc_html__('Please select products in the hotspot items.', 'somnia') . '</p>';
            echo '</div>';
            echo '</div>';
            return;
        }
        
?>
        <div class="bt-elwg-product-list-hotspot--default">
            <div class="bt-product-list-hotspot">
                <div class="bt-product-list-hotspot__list-products">
                    <div class="bt-list-header">
                        <?php if (!empty($settings['sub_heading'])) : ?>
                            <p class="bt-sub-heading"><?php echo esc_html($settings['sub_heading']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($settings['heading'])) : ?>
                            <h2 class="bt-heading"><?php echo esc_html($settings['heading']); ?></h2>
                        <?php endif; ?>
                    </div>
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
                                        <li class="bt-hotspot-product-list__item<?php echo esc_attr($out_of_stock_class); ?>"
                                            data-product-currency="<?php echo esc_attr($product_currencySymbol); ?>"
                                            data-product-single-price="<?php echo esc_attr($product->get_sale_price() ? $product->get_sale_price() : $product->get_regular_price()); ?>"
                                            data-product-id="<?php echo esc_attr($product_id); ?>"
                                            data-in-stock="<?php echo esc_attr($is_in_stock ? '1' : '0'); ?>">
                                            <div class="bt-number-product">
                                                <?php echo esc_html($index); ?>
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
                                                        <?php echo wc_get_stock_html($product); // WPCS: XSS ok. ?>
                                                    <?php endif; ?>
                                                    <?php
                                                    if ($product->is_type('variable')) {
                                                        do_action('somnia_woocommerce_template_single_add_to_cart');
                                                    }
                                                    ?>
                                                </div>

                                                <?php
                                                $price_class = $product->is_type( 'variable' ) ? 'bt-product-variable' : '';
                                                $price_html  = $product->get_price_html();
                                                ?>
                                                <p class="bt-price <?php echo esc_attr( $price_class ); ?>">
                                                    <?php echo wp_kses_post( $price_html ); ?>
                                                </p>
                                            </div>
                                        </li>
                            <?php
                                        $index++;
                                    endwhile;
                                    wp_reset_postdata();
                                endif;
                            }
                            ?>
                        </ul>
                    <?php endif; ?>
                    <div class="bt-button-wrapper">
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
                        if (!empty($product_ids)) :
                        ?>
                            <a class="bt-button bt-button-add-set-to-cart" data-ids="<?php echo esc_attr(json_encode($product_ids)); ?>" href="#">
                                <?php esc_html_e('Add set to cart', 'somnia'); ?>
                                <span class="bt-btn-price"></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bt-product-list-hotspot__image">
                    <div class="bt-hotspot-image" style="position: relative;">
                        <?php
                        if ($settings['hotspot_image']['id']) {
                            echo wp_get_attachment_image($settings['hotspot_image']['id'], $settings['thumbnail_size']);
                        } else {
                            echo '<img src="' . esc_url($settings['hotspot_image']['url']) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '">';
                        }
                        ?>
                        <?php if (!empty($settings['hotspot_items'])) : ?>
                            <div class="bt-hotspot-points">
                                <?php foreach ($settings['hotspot_items'] as $index => $item) :
                                    $product = wc_get_product($item['id_product']);
                                    if ($product) :

                                ?>
                                        <div class="bt-hotspot-point elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>"
                                            data-product-id="<?php echo esc_attr($item['id_product']); ?>">
                                            <div class="bt-hotspot-marker"> 
                                                <?php 
                                                    $count = $index + 1;
                                                    echo esc_html($count); 
                                                ?>
                                            </div>
                                        </div>
                                <?php endif;
                                endforeach; ?>
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
