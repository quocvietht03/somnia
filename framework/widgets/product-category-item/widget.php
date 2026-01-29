<?php

namespace SomniaElementorWidgets\Widgets\ProductCategoryItem;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;

class Widget_ProductCategoryItem extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-category-item';
    }

    public function get_title()
    {
        return __('Product Category Item', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-post';
    }

    public function get_categories()
    {
        return ['somnia'];
    }

    public function get_script_depends()
    {
        return ['elementor-widgets'];
    }


    private function get_product_categories() {

        $categories = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ]);

        $options = [];

        if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
            foreach ( $categories as $category ) {
                $options[ $category->slug ] = $category->name;
            }
        }

        return $options;
    }

    protected function register_layout_section_controls()
    {
        $this->start_controls_section(
            'section_layout',
            [
                'label' => __('Layout', 'somnia'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'enable_manual_cat',
            [
                'label'        => esc_html__( 'Manual Category', 'somnia' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Enable', 'somnia' ),
                'label_off'    => esc_html__( 'Auto', 'somnia' ),
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $options = $this->get_product_categories();
        $this->add_control(
            'product_cat',
            [
                'label'        => esc_html__( 'Product Category', 'somnia' ),
                'type'         => Controls_Manager::SELECT2,
                'options'      => $options,
                'multiple'     => false,
                'label_block'  => true,
                'condition'    => [
                    'enable_manual_cat' => 'yes',
                ],
                'default'      => ! empty( $options ) ? array_key_first( $options ) : '',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'label' => __('Image Size', 'somnia'),
                'show_label' => true,
                'default' => 'medium',
                'exclude' => ['custom'],
            ]
        );

        $this->add_responsive_control(
            'image_ratio',
            [
                'label' => __('Image Ratio', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'min' => 0.3,
                        'max' => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-category--thumb .bt-cover-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
                ],
            ]
        );

        $this->add_control(
            'show_count',
            [
                'label' => __('Show Product Count', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'somnia'),
                'label_off' => __('Hide', 'somnia'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'count_unit',
            [
                'label' => __('Count Unit', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('item', 'somnia'),
                'condition' => [
                    'show_count' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_section_controls()
    {
        // Image Style Section
        $this->start_controls_section(
            'section_style_image',
            [
                'label' => esc_html__('Image', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'img_border_radius',
            [
                'label' => __('Border Radius', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-category--thumb .bt-cover-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .bt-product-category--thumb .bt-cover-image',
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs('thumbnail_effects_tabs');

        $this->start_controls_tab(
            'thumbnail_tab_normal',
            [
                'label' => __('Normal', 'somnia'),
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'thumbnail_filters',
                'selector' => '{{WRAPPER}} .bt-product-category--thumb img',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'thumbnail_tab_hover',
            [
                'label' => __('Hover', 'somnia'),
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'thumbnail_hover_filters',
                'selector' => '{{WRAPPER}} .bt-product-category--item:hover .bt-product-category--thumb img',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        // Content Style Section
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => esc_html__('Content', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'name_cat_style',
            [
                'label' => __('Category Name', 'somnia'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'name_cat_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-category--name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'name_cat_color_hover',
            [
                'label' => __('Color Hover', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-category--item:hover .bt-product-category--name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'name_cat_typography',
                'label' => __('Typography', 'somnia'),
                'default' => '',
                'selector' => '{{WRAPPER}} .bt-product-category--name'
            ]
        );

        $this->add_control(
            'count_style',
            [
                'label' => __('Product Count', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_count' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-category--count' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_count' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'label' => __('Typography', 'somnia'),
                'default' => '',
                'selector' => '{{WRAPPER}} .bt-product-category--count',
                'condition' => [
                    'show_count' => 'yes',
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
        
        if($settings['enable_manual_cat'] === 'yes') {
            $terms = get_terms([
                'taxonomy'   => 'product_cat',
                'slug'       => $settings['product_cat'],
                'hide_empty' => false,
            ]);

            $term = get_term_by( 'slug', $settings['product_cat'], 'product_cat' );
        } else {
            global $wp_query;
            
            // Check if we're in Elementor editor mode
            if (Plugin::$instance->editor->is_edit_mode() || Plugin::$instance->preview->is_preview_mode()) {
                // In editor mode, get a sample product category for preview
                $args = array(
                    'taxonomy' => 'product_cat',
                    'number' => 1,
                    'hide_empty' => false,
                );
                $terms = get_terms($args);
                
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term = $terms[0];
                } else {
                    $term = false;
                }
            } else {
                // In frontend mode, use the loop term
                if (!isset($wp_query->loop_term) || !is_a($wp_query->loop_term, 'WP_Term')) {
                    $term = false;
                }
                $term = $wp_query->loop_term;
            }
        }
        

        if ( $term ) {
            $cat_link = get_term_link( $term );
            $cat_thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);
            $cat_name = $term->name;
            $cat_count = intval($term->count);
        } else {
            $cat_link = '#';
            $cat_thumb_id = false;
            $cat_name = __('Category Name', 'somnia');
            $cat_count = 0;
        }
        
        ?>
        <div class="bt-elwg-product-category-item">
            <div class="bt-product-category--item">
                <a class="bt-product-category--link" href="<?php echo esc_url($cat_link); ?>">
                    <div class="bt-product-category--thumb">
                        <div class="bt-cover-image">
                            <?php
                                if ($cat_thumb_id) {
                                    echo wp_get_attachment_image($cat_thumb_id, $settings['thumbnail_size'], false);
                                }else{
                                    echo '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '" class="wp-post-image" />';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="bt-product-category--content">
                        <h5 class="bt-product-category--name">
                            <?php echo esc_html($cat_name); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M15.936 5V13.125C15.936 13.3736 15.8372 13.6121 15.6614 13.7879C15.4856 13.9637 15.2471 14.0625 14.9985 14.0625C14.7499 14.0625 14.5114 13.9637 14.3356 13.7879C14.1598 13.6121 14.061 13.3736 14.061 13.125V7.26562L5.66178 15.6633C5.48566 15.8394 5.24679 15.9383 4.99772 15.9383C4.74865 15.9383 4.50978 15.8394 4.33366 15.6633C4.15754 15.4872 4.05859 15.2483 4.05859 14.9992C4.05859 14.7501 4.15754 14.5113 4.33366 14.3352L12.7329 5.9375H6.8735C6.62486 5.9375 6.3864 5.83873 6.21059 5.66291C6.03477 5.4871 5.936 5.24864 5.936 5C5.936 4.75136 6.03477 4.5129 6.21059 4.33709C6.3864 4.16127 6.62486 4.0625 6.8735 4.0625H14.9985C15.2471 4.0625 15.4856 4.16127 15.6614 4.33709C15.8372 4.5129 15.936 4.75136 15.936 5Z"/>
                            </svg>
                        </h5>
                        <?php
                            if ($settings['show_count'] === 'yes'):

                                if( !empty($settings['count_unit'])) {
                                    $count_text = sprintf( _n( '%s ' . $settings['count_unit'], '%s ' . $settings['count_unit'] . 's', $cat_count, 'somnia' ), number_format_i18n( $cat_count ) );
                                } else {
                                    $count_text = sprintf( _n( '%s item', '%s items', $cat_count, 'somnia' ), number_format_i18n( $cat_count ) );
                                }

                                echo '<span class="bt-product-category--count">'. $count_text .'</span>';
                            endif;
                        ?>
                        
                    </div>
                </a>
            </div>

        </div>
        <?php
    }

    protected function content_template() {}
}
