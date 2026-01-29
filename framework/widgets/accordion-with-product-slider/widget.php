<?php

namespace SomniaElementorWidgets\Widgets\AccordionWithProductSlider;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_AccordionWithProductSlider extends Widget_Base
{

    public function get_name()
    {
        return 'bt-accordion-with-product-slider';
    }

    public function get_title()
    {
        return __('Accordion With Product Slider', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-accordion';
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
                'label' => __('Content Settings', 'somnia'),
            ]
        );
        $this->add_control(
            'sub_heading',
            [
                'label' => __('Sub Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Discover our amazing products', 'somnia'),
                'placeholder' => __('Enter sub heading', 'somnia'),
            ]
        );
        $this->add_control(
            'heading',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Accordion With Product Slider', 'somnia'),
                'placeholder' => __('Enter main heading', 'somnia'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'accordion_title',
            [
                'label' => __('Accordion Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Accordion Title', 'somnia'),
                'placeholder' => __('Enter accordion title', 'somnia'),
            ]
        );

        $repeater->add_control(
            'accordion_description',
            [
                'label' => __('Accordion Description', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 3,
                'default' => __('Enter accordion description here', 'somnia'),
                'placeholder' => __('Type your accordion description', 'somnia'),
            ]
        );

        $repeater->add_control(
            'accordion_products',
            [
                'label' => __('Select Products for this Accordion', 'somnia'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_supported_ids(),
                'label_block' => true,
                'description' => __('Select products to show when this accordion item is active', 'somnia'),
            ]
        );

        $this->add_control(
            'accordion_items',
            [
                'label' => __('Accordion Items', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'accordion_title' => __('Featured Products', 'somnia'),
                        'accordion_description' => __('Our most popular and featured products collection', 'somnia'),
                    ],
                    [
                        'accordion_title' => __('New Arrivals', 'somnia'),
                        'accordion_description' => __('Latest products just added to our collection', 'somnia'),
                    ],
                    [
                        'accordion_title' => __('Best Sellers', 'somnia'),
                        'accordion_description' => __('Top selling products loved by our customers', 'somnia'),
                    ],
                ],
                'title_field' => '{{{ accordion_title }}}',
                'min_items' => 3,
                'max_items' => 6,
            ]
        );
        $this->add_control(
            'button_text',
            [
                'label' => __('Button Text', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('View All Products', 'somnia'),
                'placeholder' => __('Enter button text', 'somnia'),
            ]
        );

        $this->add_control(
            'button_link',
            [
                'label' => __('Button Link', 'somnia'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'somnia'),
                'default' => [
                    'url' => '#',
                    'is_external' => true,
                    'nofollow' => true,
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_slider_settings',
            [
                'label' => __('Product Slider Settings', 'somnia'),
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

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'product_thumbnail',
                'label' => __('Product Image Size', 'somnia'),
                'show_label' => true,
                'default' => 'medium',
                'exclude' => ['custom'],
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
                    '{{WRAPPER}} .bt-elwg-accordion-with-product-slider--default' => '--slider-offset-width: {{SIZE}}{{UNIT}};',
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
        // Accordion Style Section
        $this->start_controls_section(
            'section_accordion_style',
            [
                'label' => __('Accordion Style', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'sub_title_heading',
            [
                'label' => __('Sub Title', 'somnia'),
                'type' => Controls_Manager::HEADING,

            ]
        );

        $this->add_control(
            'sub_title_color',
            [
                'label' => __('Sub Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-heading .bt-accordion-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_title_typography',
                'selector' => '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-heading .bt-accordion-sub-title',
            ]
        );
        $this->add_control(
            'title_heading',
            [
                'label' => __('Title', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-heading .bt-accordion-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-heading .bt-accordion-title',
            ]
        );

        $this->add_control(
            'nav_title_heading',
            [
                'label' => __('Navigation Title', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'nav_title_color',
            [
                'label' => __('Navigation Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-nav .bt-accordion-nav-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'nav_title_typography',
                'selector' => '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-nav .bt-accordion-nav-title',
            ]
        );

        $this->add_control(
            'description_heading',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Description Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-nav .bt-accordion-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-nav .bt-accordion-description',
            ]
        );
        $this->add_control(
            'line_active_heading',
            [
                'label' => __('Line Active', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-nav .bt-accordion-nav-item.active:before' => 'background: {{VALUE}};',
                    '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-nav .bt-accordion-nav-item ' => 'border-color: {{VALUE}}1A;',
                ],
            ]
        );


        $this->end_controls_section();
        // Button Style Section
        $this->start_controls_section(
            'section_button_style',
            [
                'label' => __('Button Style', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'button_text!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,

                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-button .bt-button' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'button_hover_text_color',
            [
                'label' => __('Hover Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-button .bt-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .bt-accordion-with-product-slider .bt-accordion-left .bt-accordion-button .bt-button',
            ]
        );

        $this->end_controls_section();
        // Product Slider Style Section
        $this->start_controls_section(
            'section_product_slider_style',
            [
                'label' => __('Product Slider Style', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'product_title_heading',
            [
                'label' => __('Product Title', 'somnia'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'product_title_color',
            [
                'label' => __('Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-with-product-slider .bt-product-slider-right .bt-product-item .bt-product-content .bt-product-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'product_title_typography',
                'selector' => '{{WRAPPER}} .bt-accordion-with-product-slider .bt-product-slider-right .bt-product-item .bt-product-content .bt-product-title',
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
        $slider_settings = [
            'autoplay' => isset($settings['slider_autoplay']) && $settings['slider_autoplay'] === 'yes',
            'speed' => isset($settings['slider_speed']) ? $settings['slider_speed'] : 1000,
            'autoplay_delay' => isset($settings['slider_autoplay_delay']) ? $settings['slider_autoplay_delay'] : 3000,
        ];

        if (empty($settings['accordion_items'])) {
            return;
        }
?>
        <div class="bt-elwg-accordion-with-product-slider--default bt-slider-offset-sides-<?php echo esc_attr($settings['slider_offset_sides']); ?>" data-slider-settings='<?php echo json_encode($slider_settings); ?>'>
            <div class="bt-accordion-with-product-slider">
                <div class="bt-accordion-left">
                    <?php if (!empty($settings['heading'])) : ?>
                        <div class="bt-accordion-heading">
                            <?php if (!empty($settings['sub_heading'])) : ?>
                                <p class="bt-accordion-sub-title"><?php echo esc_html($settings['sub_heading']); ?></p>
                            <?php endif; ?>
                            <h2 class="bt-accordion-title"><?php echo esc_html($settings['heading']); ?></h2>
                        </div>
                    <?php endif; ?>
                    <div class="bt-accordion-nav">
                        <?php foreach ($settings['accordion_items'] as $index => $item) : 
                            $class_active = $index === 0 ? 'active' : '';
                            ?>
                            <div class="bt-accordion-nav-item <?php echo esc_attr($class_active); ?>" data-index="<?php echo esc_attr($index); ?>">
                                <h3 class="bt-accordion-nav-title"><?php echo esc_html($item['accordion_title']); ?></h3>
                                <?php if (!empty($item['accordion_description'])) : ?>
                                    <p class="bt-accordion-description"><?php echo esc_html($item['accordion_description']); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($settings['button_text']) && !empty($settings['button_link']['url'])) : ?>
                        <div class="bt-accordion-button">
                            <a href="<?php echo esc_url($settings['button_link']['url']); ?>" class="bt-button"><?php echo esc_html($settings['button_text']); ?></a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bt-product-slider-right">
                    <div class="swiper js-accordion-products">
                        <div class="swiper-wrapper">
                            <?php foreach ($settings['accordion_items'] as $accordion_index => $accordion_item) : ?>
                                <div class="swiper-slide">
                                    <?php
                                    if (!empty($accordion_item['accordion_products'])) {
                                        $product_id = $accordion_item['accordion_products'];
                                        $product = wc_get_product($product_id);
                                        if ($product) {
                                            $is_variable = $product->is_type('variable') ? 'bt-product-variable' : '';
                                        ?>
                                            <div class="bt-product-item <?php echo esc_attr($is_variable); ?>">
                                                <?php
                                                $post_thumbnail_id = $product->get_image_id();

                                                if ($post_thumbnail_id) {
                                                    $html = somnia_get_gallery_image_html($post_thumbnail_id, false, false);

                                                    echo apply_filters('woocommerce_loop_product_image_thumbnail_html', $html, $post_thumbnail_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
                                                } else {
                                                    $wrapper_classname = $product->is_type('variable') && ! empty($product->get_available_variations('image')) ?
                                                        'woocommerce-product-gallery__image woocommerce-product-gallery__image--placeholder' :
                                                        'woocommerce-product-gallery__image--placeholder';
                                                    $html = sprintf('<div class="%s">', esc_attr($wrapper_classname));
                                                    $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_thumbnail')), esc_html__('Awaiting product image', 'somnia'));
                                                    $html .= '</div>';

                                                    echo apply_filters('woocommerce_loop_product_image_thumbnail_html', $html, $post_thumbnail_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
                                                }
                                                ?>
                                                <div class="bt-product-content">
                                                    <h4 class="bt-product-title"><a href="<?php echo esc_url($product->get_permalink()); ?>" class="bt-product-link"><?php echo esc_html($product->get_name()); ?></a></h4>
                                                    <div class="bt-product-price">
                                                        <?php 
                                                            $price_html  = $product->get_price_html();
                                                            echo wp_kses_post($price_html); 
                                                        ?>
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
                                    } else {
                                        ?>
                                        <div class="bt-product-item">
                                            <?php
                                            $wrapper_classname = 'woocommerce-product-gallery__image woocommerce-product-gallery__image--placeholder';
                                            $html = sprintf('<div class="%s">', esc_attr($wrapper_classname));
                                            $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_single')), esc_html__('Awaiting product image', 'somnia'));
                                            $html .= '</div>';

                                            echo apply_filters('woocommerce_placeholder_product_image_thumbnail_html', $html); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
                                            ?>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
