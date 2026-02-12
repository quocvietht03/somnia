<?php

namespace SomniaElementorWidgets\Widgets\ProductSpotlightItem;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_ProductSpotlightItem extends Widget_Base
{

	public function get_name()
	{
		return 'bt-product-spotlight-item';
	}

	public function get_title()
	{
		return __('Product Spotlight Item', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-post';
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
			'section_layout',
			[
				'label' => __('Layout', 'somnia'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
            'featured_image',
            [
                'label' => __('Featured Image', 'somnia'),
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
					'featured_image[url]!' => '',
				],
            ]
        );

        $this->add_responsive_control(
			'image_ratio',
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
                'default' => [
                    'size' => 1.33,
                ],
				'selectors' => [
					'{{WRAPPER}} .bt-product-spotlight-item--image .bt-cover-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
				],
			]
		);

        $this->add_control(
            'enable_video_hover',
            [
                'label' => __('Enable Video on Hover', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
            'video_type',
            [
                'label' => __('Video Type', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'url',
                'options' => [
                    'url' => __('URL (Mp4)', 'somnia'),
                    'file' => __('Media File', 'somnia'),
                ],
                'condition' => [
                    'enable_video_hover' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'video_url',
            [
                'label' => __('Video URL', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('Enter video URL', 'somnia'),
                'description' => __('Enter video URL (Mp4)', 'somnia'),
                'condition' => [
                    'video_type' => ['url'],
                    'enable_video_hover' => 'yes',
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'video_file',
            [
                'label' => __('Choose Video File (Mp4)', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'media_type' => 'video',
                'condition' => [
                    'video_type' => 'file',
                    'enable_video_hover' => 'yes',
                ],
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

        $this->add_control(
            'enable_prd_title_limit',
            [
                'label' => esc_html__( 'Enable Product Title Limit', 'somnia' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
					'product_id!' => '',
				],
            ]
        );

        $this->add_control(
            'prd_title_line_limit',
            [
                'label' => esc_html__( 'Product Title Limit Lines', 'somnia' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'size' => 2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-product-mini-item--title' => '-webkit-line-clamp: {{SIZE}};display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;',
                ],
                'condition' => [
					'product_id!' => '',
                    'enable_prd_title_limit' => 'yes'
				],
            ]
        );

		$this->end_controls_section();
	}

	protected function register_style_section_controls() 
	{
		// Title Style
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __('Title', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-loop-product__infor .woocommerce-loop-product__title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'title_color_hover',
			[
				'label' => __('Hover Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce-loop-product__infor .woocommerce-loop-product__title:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .woocommerce-loop-product__infor .woocommerce-loop-product__title',
			]
		);

		$this->end_controls_section();
		
		// Price Style
		$this->start_controls_section(
			'section_price_style',
			[
				'label' => __('Price', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-product-loop-item .woocommerce-loop-product__infor .price' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-product-loop-item .woocommerce-loop-product__infor .price ins' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-product-loop-item .woocommerce-loop-product__infor .price .woocommerce-Price-amount' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'selector' => '{{WRAPPER}} .bt-elwg-product-loop-item .woocommerce-loop-product__infor .price .woocommerce-Price-amount',
			]
		);
		$this->add_control(
			'price_sale_color',
			[
				'label' => __('Sale Price Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-product-loop-item .woocommerce-loop-product__infor .price del' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-product-loop-item .woocommerce-loop-product__infor .price del .woocommerce-Price-amount' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_sale_typography',
				'label' => __('Sale Price Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-elwg-product-loop-item .woocommerce-loop-product__infor .price del .woocommerce-Price-amount',
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
		<div class="bt-elwg-product-spotlight-item">
            <div class="bt-product-spotlight-item <?php echo esc_attr($settings['enable_video_hover'] === 'yes' ? 'bt-video-hover-enabled' : ''); ?>">
                <div class="bt-cover-image">
                    <?php
                        if (!empty($settings['featured_image']['id'])) {
                            echo wp_get_attachment_image($settings['featured_image']['id'], $settings['thumbnail_size']);
                        } else {
                            if (!empty($settings['featured_image']['url'])) {
                                echo '<img src="' . esc_url($settings['featured_image']['url']) . '" alt="' . esc_html__('Awaiting testimonial image', 'somnia') . '">';
                            } else {
                                echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting testimonial image', 'somnia') . '">';
                            }
                        }
                    ?>

                    <?php if ($settings['enable_video_hover'] === 'yes') :
                        if ($settings['video_type'] === 'url') {
                            $video_url = $settings['video_url'];
                        } else {
                            $video_url = $settings['video_file']['url'];
                        }
                        if (!empty($video_url)) {
                            ?>
                            <video class="bt-hover-video" playsinline muted loop>
                                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                            </video>
                            <?php
                        }
                        ?>
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
                            <div class="bt-product-mini-item--content">
                                <h4 class="bt-product-mini-item--title">
                                    <?php echo esc_html($product->get_name()); ?>
                                </h4>
                                <div class="bt-product-mini-item--price">
                                    <?php 
                                        $price_html  = $product->get_price_html();
                                        echo wp_kses_post($price_html); 
                                    ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
		<?php
	}

	protected function content_template() {}
}
