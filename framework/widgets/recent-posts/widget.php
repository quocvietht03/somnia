<?php

namespace SomniaElementorWidgets\Widgets\RecentPosts;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_RecentPosts extends Widget_Base
{

	public function get_name()
	{
		return 'bt-recent-posts';
	}

	public function get_title()
	{
		return __('Recent Posts', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-post-list';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	protected function register_layout_section_controls()
	{
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'somnia'),
			]
		);

		$this->add_control(
			'number_posts',
			[
				'label' => __('Number of Posts', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'default' => 5,
				'min' => 1,
				'max' => 20,
			]
		);

		$this->add_control(
			'show_thumbnail',
			[
				'label' => __('Show Thumbnail', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'somnia'),
				'label_off' => __('Hide', 'somnia'),
				'default' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'label' => __( 'Image Size', 'somnia' ),
				'show_label' => true,
				'default' => 'medium',
				'exclude' => [ 'custom' ],
				'condition' => [
					'show_thumbnail' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_date',
			[
				'label' => __('Show Date', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'somnia'),
				'label_off' => __('Hide', 'somnia'),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'date_format',
			[
				'label'       => __( 'Date Format', 'somnia' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => get_option( 'date_format' ),
				'condition'   => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_category',
			[
				'label' => __('Show Category', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'somnia'),
				'label_off' => __('Hide', 'somnia'),
				'default' => 'yes',
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

		$this->end_controls_section();
	}

	protected function register_style_section_controls()
	{
		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__('Item', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'reverse_row',
			[
				'label' => __( 'Reverse Row', 'somnia' ),
				'type'  => Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'show_thumbnail' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'item_space_between',
			[
				'label' => esc_html__('Space Between Posts', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 60,
					]
				],
				'default' => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-recent-posts .bt-post:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};padding-bottom:{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumbnail',
			[
				'label' => esc_html__('Thumbnail', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_thumbnail' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'thumbnail_width',
			[
				'label' => __('Width', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 90,
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-post--thumbnail' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumbnail_height',
			[
				'label' => __('Height', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 90,
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-post--thumbnail .bt-cover-image' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'thumbnail_border_radius',
			[
				'label' => __('Border Radius', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bt-post--thumbnail .bt-cover-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'somnia'),
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
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => __('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--publish',
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'category_style',
			[
				'label' => __('Category', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'show_category' => 'yes',
				],
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
				'condition' => [
					'show_category' => 'yes',
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
				'condition' => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'category_typography',
				'label' => __('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--category a',
				'condition' => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_style',
			[
				'label' => __('Title', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-post--title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label' => __('Color Hover', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-post--title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--title',
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
		$settings = $this->get_settings_for_display();

		$recent_posts = wp_get_recent_posts(array(
			'numberposts' => $settings['number_posts'],
			'post_status' => 'publish'
		));

		$reverse = $settings['reverse_row'] === 'yes' ? 'is-reverse' : '';

		?>
		<div class="bt-elwg-recent-posts widget widget-block bt-block-recent-posts <?php echo esc_attr( $reverse ); ?>">
			<?php foreach ($recent_posts as $post_item) {
				$category = get_the_terms($post_item['ID'], 'category');
			?>
				<div class="bt-post">
					<?php if ($settings['show_thumbnail'] == 'yes') { ?>
						<a href="<?php echo get_permalink($post_item['ID']) ?>" class="bt-post--thumbnail">
							<div class="bt-cover-image">
								<?php echo get_the_post_thumbnail($post_item['ID'], $settings['thumbnail_size']); ?>
							</div>
						</a>
					<?php } ?>
					<div class="bt-post--infor">
						<?php if ($settings['show_date'] == 'yes' || $settings['show_category'] == 'yes') { ?>
							<div class="bt-post--meta">
								<?php if ($settings['show_date'] == 'yes') { ?>
									<div class="bt-post--publish">
										<?php 
										$date_format = ! empty( $settings['date_format'] ) ? $settings['date_format'] : get_option( 'date_format' );
										echo get_the_date($date_format, $post_item['ID']); 
										?>
									</div>
								<?php } ?>
								<?php if ($settings['show_category'] == 'yes' && !empty($category) && is_array($category)) {
									$first_category = reset($category); ?>
									<div class="bt-post--category">
										<a href="<?php echo esc_url(get_category_link($first_category->term_id)); ?>">
											<?php echo esc_html($first_category->name); ?>
										</a>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
						<h3 class="bt-post--title">
							<a href="<?php echo get_permalink($post_item['ID']) ?>">
								<?php echo esc_html($post_item['post_title']); ?>
							</a>
						</h3>
					</div>
				</div>
			<?php } ?>
		</div>
<?php
	}

	protected function content_template() {}
}

