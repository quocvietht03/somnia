<?php
namespace SomniaElementorWidgets\Widgets\PostLoopItem;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_PostLoopItem extends Widget_Base {


	public function get_name() {
		return 'bt-post-loop-item';
	}

	public function get_title() {
		return __( 'Post Loop Item', 'somnia' );
	}

	public function get_icon() {
		return 'bt-bears-icon eicon-post';
	}

	public function get_categories() {
		return [ 'somnia' ];
	}

	protected function register_layout_section_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'somnia' ),
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'label' => __( 'Image Size', 'somnia' ),
				'show_label' => true,
				'default' => 'medium_large',
				'exclude' => [ 'custom' ],
			]
		);

		$this->add_responsive_control(
			'image_ratio',[
				'label' => __( 'Image Ratio', 'somnia' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.75,
				],
				'range' => [
					'px' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-post--featured .bt-cover-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
				],
			]
		);
		$this->add_control(
            'enable_title_limit',
            [
                'label' => esc_html__( 'Enable Title Limit', 'somnia' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'title_line_limit',
            [
                'label' => esc_html__( 'Title Limit Lines', 'somnia' ),
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
                    '{{WRAPPER}} .bt-post--title' => '-webkit-line-clamp: {{SIZE}};display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;',
                ],
                'condition' => [
                    'enable_title_limit' => 'yes'
				],
            ]
        );

		$this->add_control(
			'show_excerpt',
			[
				'label' => esc_html__('Show Excerpt', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'somnia'),
				'label_off' => esc_html__('Hide', 'somnia'),
				'default' => 'yes',
			]
		);

		$this->add_control(
            'enable_excerpt_limit',
            [
                'label' => esc_html__( 'Enable Excerpt Limit', 'somnia' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
				'condition' => [
                    'show_excerpt' => 'yes'
				],
            ]
        );

        $this->add_control(
            'excerpt_line_limit',
            [
                'label' => esc_html__( 'Excerpt Limit Lines', 'somnia' ),
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
                    '{{WRAPPER}} .bt-post--excerpt' => '-webkit-line-clamp: {{SIZE}};display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;',
                ],
                'condition' => [
					'show_excerpt' => 'yes',
                    'enable_excerpt_limit' => 'yes'
				],
            ]
        );

		$this->add_control(
			'show_read_more',
			[
				'label' => esc_html__('Show Read More', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'somnia'),
				'label_off' => esc_html__('Hide', 'somnia'),
				'default' => 'no',
			]
		);
		$this->add_control(
			'read_more_text',
			[
				'label' => esc_html__('Read More Text', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Read More', 'somnia'),
				'condition' => [
					'show_read_more' => 'yes',
				],
			]
		);
		$this->end_controls_section();
	}

	protected function register_style_section_controls() {

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Image', 'somnia' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'img_border_radius',
			[
				'label' => __( 'Border Radius', 'somnia' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bt-post--featured .bt-cover-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'thumbnail_effects_tabs' );

		$this->start_controls_tab( 'thumbnail_tab_normal',
			[
				'label' => __( 'Normal', 'somnia' ),
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),[
				'name' => 'thumbnail_filters',
				'selector' => '{{WRAPPER}} .bt-post--featured img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'thumbnail_tab_hover',[
				'label' => __( 'Hover', 'somnia' ),
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),[
				'name'     => 'thumbnail_hover_filters',
				'selector' => '{{WRAPPER}} .bt-post:hover .bt-post--featured img',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',[
				'label' => esc_html__( 'Content', 'somnia' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'date_style',
			[
				'label' => __('Date', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-post--publish' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => __('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--publish',
			]
		);

		$this->add_control(
			'category_style',
			[
				'label' => __('Category', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'category_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-post--category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_color_hover',
			[
				'label' => __('Color Hover', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-post--category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'category_typography',
				'label' => __('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--category a',
			]
		);

		$this->add_control(
			'post_title_heading',
			[
				'label' => esc_html__('Title', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-post--title a' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'title_hover_color',
			[
				'label' => esc_html__('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-post--title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--title a',
			]
		);

		// Post Excerpt
		$this->add_control(
			'post_excerpt_heading',
			[
				'label' => esc_html__('Excerpt', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'excerpt_typography',
				'label' => esc_html__('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--excerpt',
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label' => esc_html__('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-post--excerpt' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]	
		);

		$this->end_controls_section();

	}

	protected function register_controls() {
		$this->register_layout_section_controls();
		$this->register_style_section_controls();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
			<div class="bt-elwg-post-loop-item--default">
				<?php get_template_part( 'framework/templates/post', 'style', array('image-size' => $settings['thumbnail_size'], 'excerpt' => $settings['show_excerpt'], 'read_more' => $settings['show_read_more'], 'read_more_text' => $settings['read_more_text'])); ?>
	    	</div>
		<?php
	}

	protected function content_template() {

	}
}
