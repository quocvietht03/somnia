<?php

namespace SomniaElementorWidgets\Widgets\ProductTestimonialItem;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

class Widget_ProductTestimonialItem extends Widget_Base
{

    public function get_name()
    {
        return 'bt-product-testimonial-item';
    }

    public function get_title()
    {
        return __('Product Testimonial Item', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-product-rating';
    }

    public function get_categories()
    {
        return ['somnia'];
    }

    private function get_supported_products() {
		$products = wc_get_products([
			'limit' => -1,
			'status' => 'publish',
		]);

		$options = [];
		foreach ( $products as $product ) {
			$options[ $product->get_id() ] = $product->get_name();
		}

		return $options;
	}

    protected function register_layout_section_controls()
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Testimonial', 'somnia'),
            ]
        );

        $this->add_control(
            'testimonial_image',
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
                'condition' => [
					'testimonial_image[url]!' => '',
				],
            ]
        );

        $this->add_control(
			'image_position',
			[
				'label' => esc_html__( 'Image Position', 'somnia' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'top',
                'tablet_default' => 'top',
                'mobile_default' => 'top',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'somnia' ),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' => esc_html__( 'Top', 'somnia' ),
						'icon' => 'eicon-v-align-top',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'somnia' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'bt-image-position--',
				'toggle' => false,
				'condition' => [
					'testimonial_image[url]!' => '',
				],
			]
		);

        $this->add_control(
            'testimonial_title',
            [
                'label' => __('Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('What Our Customers Say', 'somnia'),
                'placeholder' => __('Type your testimonial title', 'somnia'),
            ]
        );

        $this->add_control(
            'testimonial_text',
            [
                'label' => __('Text', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 5,
                'default' => __('This is a sample testimonial quote. It helps visualize how customer feedback will appear here.', 'somnia'),
                'placeholder' => __('Type your testimonial text', 'somnia'),
            ]
        );

        $this->add_control(
            'enable_text_limit',
            [
                'label' => esc_html__( 'Enable Text Limit', 'somnia' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
					'testimonial_text!' => '',
				],
            ]
        );

        $this->add_control(
            'text_line_limit',
            [
                'label' => esc_html__( 'Text Limit Lines', 'somnia' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'size' => 3,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--text' => '-webkit-line-clamp: {{SIZE}};display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;',
                ],
                'condition' => [
					'testimonial_text!' => '',
                    'enable_text_limit' => 'yes'
				],
            ]
        );

        $this->add_control(
            'testimonial_rating',
            [
                'label' => __('Rating', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => '1 Star',
                    '2' => '2 Stars',
                    '3' => '3 Stars',
                    '4' => '4 Stars',
                    '5' => '5 Stars',
                ],
                'default' => '5',
            ]
        );

        $this->add_control(
            'testimonial_author',
            [
                'label' => __('Author', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('John Doe', 'somnia'),
                'placeholder' => __('Enter author name', 'somnia'),
            ]
        );

        $options = $this->get_supported_products();
		$this->add_control(
			'product_id',
			[
				'label'       => esc_html__( 'Select Products', 'somnia' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $options, // id => title
				'multiple'    => false,
				'label_block' => true,
				'default'     => ! empty( $options ) ? array_key_first( $options ) : '',
			]
		);

        $this->end_controls_section();
    }

    protected function register_style_section_controls()
    {
        $this->start_controls_section(
            'section_layout_style',
            [
                'label' => __('Layout', 'somnia'),
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
                    '{{WRAPPER}} .bt-product-testimonial-item--content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'border',
                'label'    => esc_html__( 'Border', 'somnia' ),
                'selector' => '{{WRAPPER}} .bt-product-testimonial-item',
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'somnia' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bt-product-testimonial-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_style',
            [
                'label' => __('Content', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_heading',
            [
                'label' => __('Testimonial Title', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .bt-product-testimonial-item--title',
            ]
        );

        $this->add_control(
            'text_heading',
            [
                'label' => __('Testimonial Text', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .bt-product-testimonial-item--text',
            ]
        );

        $this->add_control(
            'author_heading',
            [
                'label' => __('Author', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'author_color',
            [
                'label' => __('Author Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--author' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'author_typography',
                'selector' => '{{WRAPPER}} .bt-product-testimonial-item--author',
            ]
        );

        $this->add_control(
            'rating_heading',
            [
                'label' => __('Rating', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'rating_color',
            [
                'label' => __('Star Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--rating .star.filled svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'rating_empty_color',
            [
                'label' => __('Empty Star Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-testimonial-item--rating .star:not(.filled) svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'line_heading',
            [
                'label' => __('Line', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'line_color',
            [
                'label' => __('Line Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-mini-item' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'product_heading',
            [
                'label' => __('Product', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'prd_name_color',
            [
                'label' => __('Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-product-mini-item--title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'prd_typography',
                'label' => __('Title Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-product-mini-item--title',
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
        
        ?>
        <div class="bt-elwg-product-testimonial-item">
            <div class="bt-product-testimonial-item">
                <div class="bt-product-testimonial-item--image">
                    <div class="bt-cover-image">
                        <?php
                        if (!empty($settings['testimonial_image']['id'])) {
                            echo wp_get_attachment_image($settings['testimonial_image']['id'], $settings['thumbnail_size']);
                        } else {
                            if (!empty($settings['testimonial_image']['url'])) {
                                echo '<img src="' . esc_url($settings['testimonial_image']['url']) . '" alt="' . esc_html__('Awaiting testimonial image', 'somnia') . '">';
                            } else {
                                echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting testimonial image', 'somnia') . '">';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="bt-product-testimonial-item--content">
                    <div class="bt-product-testimonial-item--inner">
                        <?php if (!empty($settings['testimonial_rating'])) : ?>
                            <div class="bt-product-testimonial-item--rating">
                                <?php
                                $rating = intval($item['testimonial_rating']);
                                for ($i = 1; $i <= 5; $i++) :
                                    $filled_class = ($i <= $rating) ? 'filled' : '';
                                ?>
                                    <span class="star <?php echo esc_attr($filled_class); ?>">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 1.66667L12.575 6.88334L18.3333 7.725L14.1667 11.7833L15.15 17.5167L10 14.8083L4.85 17.5167L5.83333 11.7833L1.66667 7.725L7.425 6.88334L10 1.66667Z" />
                                        </svg>
                                    </span>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['testimonial_title'])) : ?>
                            <h4 class="bt-product-testimonial-item--title"><?php echo esc_html($settings['testimonial_title']); ?></h4>
                        <?php endif; ?>
                        <?php if (!empty($settings['testimonial_text'])) : ?>
                            <div class="bt-product-testimonial-item--text"><?php echo esc_html($settings['testimonial_text']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($settings['testimonial_author'])) : ?>
                            <div class="bt-product-testimonial-item--author"><?php echo esc_html($settings['testimonial_author']); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($settings['product_id'])) : 
                            $product = wc_get_product($settings['product_id']);
                        ?>
                        <div class="bt-product-mini-item">
                        <a class="bt-product-mini-item--link" href="<?php echo esc_url($product->get_permalink()); ?>">
                            <div class="bt-product-mini-item--image">
                                <?php
                                    if (has_post_thumbnail($settings['product_id'])) {
                                        echo get_the_post_thumbnail($settings['product_id'], 'thumbnail');
                                    } else {
                                        echo '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '" class="wp-post-image" />';
                                    }
                                ?>
                            </div>
                            <h4 class="bt-product-mini-item--title">
                                <?php echo esc_html($product->get_name()); ?>
                            </h4>
                        </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php
    }



    protected function content_template() {}
}
