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

    private function get_products_list() {
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

        $options = $this->get_products_list();
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
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <?php if ($i <= $settings['testimonial_rating']) : ?>
                                        <span class="star filled"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="25" viewBox="0 0 26 25" fill="currentColor">
                                                <path d="M24.6254 11.5605L19.7035 15.8075L21.2031 22.1589C21.2858 22.5037 21.2645 22.8653 21.1418 23.198C21.0192 23.5306 20.8007 23.8195 20.5139 24.0281C20.2272 24.2366 19.885 24.3555 19.5308 24.3698C19.1765 24.384 18.8259 24.2929 18.5234 24.108L12.9999 20.7086L7.47321 24.108C7.17071 24.2918 6.82058 24.3821 6.4669 24.3673C6.11322 24.3526 5.7718 24.2335 5.48565 24.0251C5.1995 23.8167 4.98141 23.5283 4.85883 23.1963C4.73625 22.8642 4.71467 22.5032 4.79681 22.1589L6.30181 15.8075L1.37993 11.5605C1.11229 11.3292 0.918725 11.0241 0.823413 10.6834C0.728101 10.3428 0.735265 9.98158 0.844011 9.64495C0.952757 9.30833 1.15826 9.01121 1.43487 8.79069C1.71147 8.57016 2.04692 8.43602 2.39931 8.405L8.85243 7.88438L11.3418 1.86C11.4765 1.53168 11.7059 1.25084 12.0006 1.05319C12.2954 0.855535 12.6423 0.75 12.9972 0.75C13.3521 0.75 13.699 0.855535 13.9937 1.05319C14.2885 1.25084 14.5178 1.53168 14.6526 1.86L17.1409 7.88438L23.594 8.405C23.9471 8.43487 24.2835 8.56826 24.5611 8.78848C24.8387 9.0087 25.0452 9.30594 25.1546 9.64297C25.264 9.98 25.2716 10.3418 25.1762 10.6831C25.0809 11.0244 24.887 11.33 24.6188 11.5616L24.6254 11.5605Z" />
                                            </svg></span>
                                    <?php else : ?>
                                        <span class="star">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="25" viewBox="0 0 26 25" fill="currentColor">
                                                <path d="M24.6254 11.5605L19.7035 15.8075L21.2031 22.1589C21.2858 22.5037 21.2645 22.8653 21.1418 23.198C21.0192 23.5306 20.8007 23.8195 20.5139 24.0281C20.2272 24.2366 19.885 24.3555 19.5308 24.3698C19.1765 24.384 18.8259 24.2929 18.5234 24.108L12.9999 20.7086L7.47321 24.108C7.17071 24.2918 6.82058 24.3821 6.4669 24.3673C6.11322 24.3526 5.7718 24.2335 5.48565 24.0251C5.1995 23.8167 4.98141 23.5283 4.85883 23.1963C4.73625 22.8642 4.71467 22.5032 4.79681 22.1589L6.30181 15.8075L1.37993 11.5605C1.11229 11.3292 0.918725 11.0241 0.823413 10.6834C0.728101 10.3428 0.735265 9.98158 0.844011 9.64495C0.952757 9.30833 1.15826 9.01121 1.43487 8.79069C1.71147 8.57016 2.04692 8.43602 2.39931 8.405L8.85243 7.88438L11.3418 1.86C11.4765 1.53168 11.7059 1.25084 12.0006 1.05319C12.2954 0.855535 12.6423 0.75 12.9972 0.75C13.3521 0.75 13.699 0.855535 13.9937 1.05319C14.2885 1.25084 14.5178 1.53168 14.6526 1.86L17.1409 7.88438L23.594 8.405C23.9471 8.43487 24.2835 8.56826 24.5611 8.78848C24.8387 9.0087 25.0452 9.30594 25.1546 9.64297C25.264 9.98 25.2716 10.3418 25.1762 10.6831C25.0809 11.0244 24.887 11.33 24.6188 11.5616L24.6254 11.5605Z" />
                                            </svg></span>
                                    <?php endif; ?>
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
