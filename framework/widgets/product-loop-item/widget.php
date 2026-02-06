<?php

namespace SomniaElementorWidgets\Widgets\ProductLoopItem;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_ProductLoopItem extends Widget_Base
{

	public function get_name()
	{
		return 'bt-product-loop-item';
	}

	public function get_title()
	{
		return __('Product Loop Item', 'somnia');
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
            'enable_manual_prd',
            [
                'label'        => esc_html__( 'Manual Product', 'somnia' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Enable', 'somnia' ),
                'label_off'    => esc_html__( 'Auto', 'somnia' ),
                'return_value' => 'yes',
                'default'      => '',
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
				'condition'   => [
                    'enable_manual_prd' => 'yes',
                ],
				'default'     => ! empty( $options ) ? array_key_first( $options ) : '',
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
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery__image' => 'padding-bottom: calc( {{SIZE}} * 100% ) !important;',
				],
			]
		);

		$this->add_responsive_control(
			'content_text_align',
			[
				'label' => esc_html__('Alignment', 'somnia'),
				'type'  => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'somnia'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'somnia'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'somnia'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
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
		<div class="bt-elwg-product-loop-item <?php echo esc_attr($settings['content_text_align']); ?>">
			<?php
				if($settings['enable_manual_prd'] === 'yes' && !empty($settings['product_id'])) {
					$product_id = (int) $settings['product_id'];

					$query = new \WP_Query([
						'post_type'      => 'product',
						'p'              => $product_id,
						'posts_per_page' => 1,
					]);
					if ($query->have_posts())  {
						while ($query->have_posts()) { $query->the_post();
							global $product;

							if (empty($product) || ! $product->is_visible()) {
								get_template_part('woocommerce/content', 'product-placeholder');
							} else {
								wc_get_template_part('content', 'product');
							}
						}
					} else {
						get_template_part('woocommerce/content', 'product-placeholder');
					}
				} else {
					global $product;
				
					if (empty($product) || ! $product->is_visible()) {
						get_template_part('woocommerce/content', 'product-placeholder');
					} else {
						wc_get_template_part('content', 'product');
					}
				}

				
			?>
		</div>
		<?php
	}

	protected function content_template() {}
}
