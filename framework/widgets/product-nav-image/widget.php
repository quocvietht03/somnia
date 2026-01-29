<?php

namespace SomniaElementorWidgets\Widgets\ProductNavImage;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

class Widget_ProductNavImage extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-nav-image';
    }

    public function get_title()
    {
        return __('Product Nav Image', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-product-images';
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
                'label' => __('Nav Image', 'somnia'),
            ]
        );
        $this->add_control(
            'sub_heading',
            [
                'label' => __('Sub Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Enter your sub heading', 'somnia'),
                'placeholder' => __('Enter sub heading', 'somnia'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'heading',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Enter your heading', 'somnia'),
                'placeholder' => __('Enter heading', 'somnia'),
                'label_block' => true,
            ]
        );
        $this->add_control(
            'description',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Enter your description', 'somnia'),
                'placeholder' => __('Enter description', 'somnia'),
                'label_block' => true,
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
            'image_tab_item',
            [
                'label' => __('Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'heading_tab_item',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Enter heading', 'somnia'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'description_tab_item',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
                'placeholder' => __('Enter description', 'somnia'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'product_tab_item',
            [
                'label' => __('Select Product', 'somnia'),
                'type' => Controls_Manager::SELECT2,
                'options' => $this->get_supported_ids(),
                'label_block' => true,
                'multiple' => false,
            ]
        );

        $this->add_control(
            'nav_items',
            [
                'label' => __('Navigation Items', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'image_tab_item' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'heading_tab_item' => __('Item #1', 'somnia'),
                        'description_tab_item' => '',
                        'product_tab_item' => '',
                    ],
                    [
                        'image_tab_item' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'heading_tab_item' => __('Item #2', 'somnia'),
                        'description_tab_item' => '',
                        'product_tab_item' => '',
                    ],
                    [
                        'image_tab_item' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'heading_tab_item' => __('Item #3', 'somnia'),
                        'description_tab_item' => '',
                        'product_tab_item' => '',
                    ],
                    [
                        'image_tab_item' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'heading_tab_item' => __('Item #4', 'somnia'),
                        'description_tab_item' => '',
                        'product_tab_item' => '',
                    ],
                ],
                'title_field' => '{{{ heading_tab_item }}}',
            ]
        );
        $this->add_responsive_control(
            'tabs_width',
            [
                'label' => __('Tabs Width', 'somnia'),
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
                    '{{WRAPPER}} .bt-product-nav-image--tabs' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columns', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => '4',
                'options' => [
                    '1' => '1',
                    '2' => '2', 
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--tabs-inner' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
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
            'image_ratio_thumb',
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
                    '{{WRAPPER}} .bt-product-nav-image--thumb-item .bt-cover-image' => 'padding-bottom: calc( {{SIZE}} * 100% )',
                ],
            ]
        );
        $this->add_control(
            'button_text',
            [
                'label' => __('Button Text', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('View All Products', 'somnia'),
                'placeholder' => __('Enter button text', 'somnia'),
                'label_block' => true,
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
                ],
                'show_external' => true,
                'label_block' => true,
            ]
        );
        $this->end_controls_section();
    }
    protected function register_style_content_section_controls()
    {
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => __('Content', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __('Content Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_gap',
            [
                'label' => __('Content Gap', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--content' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        // Header Style
        $this->add_control(
            'heading_header_style',
            [
                'label' => __('Header', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'sub_heading_color',
            [
                'label' => __('Sub Heading Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--sub-heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_heading_typography',
                'selector' => '{{WRAPPER}} .bt-product-nav-image--sub-heading',
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => __('Heading Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} .bt-product-nav-image--heading',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Description Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .bt-product-nav-image--description',
            ]
        );

        // Button Style
        $this->add_control(
            'heading_button_style',
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

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--button-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--button-link' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover_tab',
            [
                'label' => __('Hover', 'somnia'),
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--button-link:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--button-link:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .bt-product-nav-image--button-link',
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--button-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_style_thumb_item',
            [
                'label' => __('Thumb Item', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'thumb_item_heading_color',
            [
                'label' => __('Heading Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--thumb-item-heading' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'thumb_item_heading_typography',
                'selector' => '{{WRAPPER}} .bt-product-nav-image--thumb-item-heading',
            ]
        );

        $this->add_control(
            'thumb_item_description_color',
            [
                'label' => __('Description Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-product-nav-image--thumb-item-description' => 'color: {{VALUE}};',
                ],
            ]
        );
    
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'thumb_item_description_typography',
                'selector' => '{{WRAPPER}} .bt-product-nav-image--thumb-item-description',
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


?>
        <div class="bt-elwg-product-nav-image--default">
            <div class="bt-product-nav-image">
                <div class="bt-product-nav-image--content">
                    <?php if (!empty($settings['sub_heading']) || !empty($settings['heading']) || !empty($settings['description'])) : ?>
                        <div class="bt-product-nav-image--header">
                            <?php if (!empty($settings['sub_heading'])) : ?>
                                <span class="bt-product-nav-image--sub-heading">
                                    <?php echo esc_html($settings['sub_heading']); ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($settings['heading'])) : ?>
                                <h3 class="bt-product-nav-image--heading">
                                    <?php echo esc_html($settings['heading']); ?>
                                </h3>
                            <?php endif; ?>
                            <?php if (!empty($settings['description'])) : ?>
                                <p class="bt-product-nav-image--description">
                                    <?php echo esc_html($settings['description']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="bt-product-nav-image--tabs">
                        <div class="bt-product-nav-image--tabs-inner">
                            <?php foreach ($settings['nav_items'] as $index => $item) : ?>
                                <div class="bt-product-nav-image--tab-item <?php echo esc_attr($index === 0 ? 'active' : ''); ?>" data-index="<?php echo esc_attr($index); ?>">
                                    <?php
                                    if (!empty($item['image_tab_item']['id'])) {
                                        echo '<div class="bt-item-tab-image">' . wp_get_attachment_image($item['image_tab_item']['id'], 'medium') . '</div>';
                                    } else {
                                        echo '<div class="bt-item-tab-image"><img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting image', 'somnia') . '"></div>';
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!empty($settings['button_text'])) : ?>
                            <div class="bt-product-nav-image--button">
                                <a href="<?php echo esc_url($settings['button_link']['url']); ?>" class="bt-product-nav-image--button-link">
                                    <?php echo esc_html($settings['button_text']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="bt-product-nav-image--thumb">
                    <?php foreach ($settings['nav_items'] as $index => $item) : ?>
                        <div class="bt-product-nav-image--thumb-item <?php echo esc_attr($index === 0 ? 'active' : ''); ?>" data-index="<?php echo esc_attr($index); ?>">
                            <div class="bt-cover-image">
                                <?php
                                if (!empty($item['image_tab_item']['id'])) {
                                    echo wp_get_attachment_image($item['image_tab_item']['id'], $settings['thumbnail_size']);
                                } else {
                                    echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting image', 'somnia') . '">';
                                }
                                ?>
                            </div>
                            <div class="bt-product-nav-image--thumb-item-inner">
                                <?php if (!empty($item['heading_tab_item']) || !empty($item['description_tab_item'])) : ?>
                                    <div class="bt-product-nav-image--thumb-item-content">
                                        <?php if (!empty($item['heading_tab_item'])) : ?>
                                            <h3 class="bt-product-nav-image--thumb-item-heading">
                                                <?php echo esc_html($item['heading_tab_item']); ?>
                                            </h3>
                                        <?php endif; ?>
                                        <?php if (!empty($item['description_tab_item'])) : ?>
                                            <p class="bt-product-nav-image--thumb-item-description">
                                                <?php echo esc_html($item['description_tab_item']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($item['product_tab_item'])) :
                                    $product = wc_get_product($item['product_tab_item']);
                                    if ($product) : 
                                        $is_variable = $product->is_type('variable') ? 'bt-product-variable' : '';
                                        ?>
                                        <div class="bt-product-nav-image--thumb-item-product">
                                            <div class="bt-product-item-minimal active <?php echo esc_attr($is_variable); ?>"
                                                data-product-id="<?php echo esc_attr($item['product_tab_item']); ?>">
                                                <div class="bt-product-thumbnail">
                                                    <a href="<?php echo esc_url($product->get_permalink()); ?>">
                                                        <?php
                                                        if (has_post_thumbnail($item['product_tab_item'])) {
                                                            echo get_the_post_thumbnail($item['product_tab_item'], 'thumbnail');
                                                        } else {
                                                            echo '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '" class="wp-post-image" />';
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
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
