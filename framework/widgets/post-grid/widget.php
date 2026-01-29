<?php

namespace SomniaElementorWidgets\Widgets\PostGrid;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_PostGrid extends Widget_Base
{

	public function get_name()
	{
		return 'bt-post-grid';
	}

	public function get_title()
	{
		return __('Post Grid', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-posts-grid';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	protected function get_supported_ids()
	{
		$supported_ids = [];

		$wp_query = new \WP_Query(array(
			'post_type' => 'post',
			'post_status' => 'publish'
		));

		if ($wp_query->have_posts()) {
			while ($wp_query->have_posts()) {
				$wp_query->the_post();
				$supported_ids[get_the_ID()] = get_the_title();
			}
		}

		return $supported_ids;
	}

	public function get_supported_taxonomies()
	{
		$supported_taxonomies = [];

		$categories = get_terms(array(
			'taxonomy' => 'category',
			'hide_empty' => false,
		));
		if (! empty($categories)  && ! is_wp_error($categories)) {
			foreach ($categories as $category) {
				$supported_taxonomies[$category->term_id] = $category->name;
			}
		}

		return $supported_taxonomies;
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
			'layout',
			[
				'label' => __('Layout', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'somnia'),
					'layout-01' => __('Layout 01', 'somnia'),
					'layout-02' => __('Layout 02', 'somnia'),
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => __('Posts Per Page', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
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
			'image_ratio',
			[
				'label' => __('Image Ratio', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.66,
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
				'condition' => [
					'layout' => 'default',
				],
			]
		);
		$this->add_responsive_control(
			'image_height',
			[
				'label' => __('Image Height', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 800,
						'step' => 10,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 350,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bt-post--featured .bt-cover-image' => 'height: {{SIZE}}{{UNIT}}; padding-bottom: 0;',
				],
				'condition' => [
					'layout' => 'layout-01',
				],
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => __('Pagination', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'somnia'),
				'label_off' => __('Hide', 'somnia'),
				'default' => '',
			]
		);

		$this->end_controls_section();
	}

	protected function register_query_section_controls()
	{
		$this->start_controls_section(
			'section_query',
			[
				'label' => __('Query', 'somnia'),
			]
		);

		$this->start_controls_tabs('tabs_query');

		$this->start_controls_tab(
			'tab_query_include',
			[
				'label' => __('Include', 'somnia'),
			]
		);

		$this->add_control(
			'ids',
			[
				'label' => __('Ids', 'somnia'),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_supported_ids(),
				'label_block' => true,
				'multiple' => true,
			]
		);

		$this->add_control(
			'category',
			[
				'label' => __('Category', 'somnia'),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_supported_taxonomies(),
				'label_block' => true,
				'multiple' => true,
			]
		);

		$this->end_controls_tab();


		$this->start_controls_tab(
			'tab_query_exnlude',
			[
				'label' => __('Exclude', 'somnia'),
			]
		);

		$this->add_control(
			'ids_exclude',
			[
				'label' => __('Ids', 'somnia'),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_supported_ids(),
				'label_block' => true,
				'multiple' => true,
			]
		);

		$this->add_control(
			'category_exclude',
			[
				'label' => __('Category', 'somnia'),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_supported_taxonomies(),
				'label_block' => true,
				'multiple' => true,
			]
		);

		$this->add_control(
			'offset',
			[
				'label' => __('Offset', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
				'description' => __('Use this setting to skip over posts (e.g. \'2\' to skip over 2 posts).', 'somnia'),
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'orderby',
			[
				'label' => __('Order By', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'default' => 'post_date',
				'options' => [
					'post_date' => __('Date', 'somnia'),
					'post_title' => __('Title', 'somnia'),
					'menu_order' => __('Menu Order', 'somnia'),
					'rand' => __('Random', 'somnia'),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __('Order', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc' => __('ASC', 'somnia'),
					'desc' => __('DESC', 'somnia'),
				],
			]
		);

		$this->end_controls_section();
	}


	protected function register_style_section_controls()
	{
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
					'{{WRAPPER}} .bt-post--featured .bt-cover-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
				'selector' => '{{WRAPPER}} .bt-post--featured img',
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
				'selector' => '{{WRAPPER}} .bt-post:hover .bt-post--featured img',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-post--publish',
			]
		);

		$this->add_control(
			'title_style',
			[
				'label' => __('Title', 'somnia'),
				'type' => Controls_Manager::HEADING,
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
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-post--title a',
			]
		);
		$this->add_control(
			'excerpt_style',
			[
				'label' => __('Excerpt', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'layout' => 'layout-02',
				],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-post--excerpt' => 'color: {{VALUE}};',
				],
				'condition' => [
					'layout' => 'layout-02',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'excerpt_typography',
				'label' => __('Typography', 'somnia'),
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-post--excerpt',
				'condition' => [
					'layout' => 'layout-02',
				],
			]
		);
		$this->add_control(
			'first_post_title_style',
			[
				'label' => __('First Post Title', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'first_post_title_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-post:first-child .bt-post--title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'first_post_title_color_hover',
			[
				'label' => __('Color Hover', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-post:first-child .bt-post--title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'first_post_title_typography',
				'label' => __('Typography', 'somnia'),
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-post:first-child .bt-post--title a',
			]
		);

		$this->add_control(
			'first_post_date_style',
			[
				'label' => __('First Post Date', 'somnia'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'first_post_date_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-post:first-child .bt-post--publish' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'first_post_date_typography',
				'label' => __('Typography', 'somnia'),
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-post:first-child .bt-post--publish',
			]
		);

		$this->add_control(
			'first_post_excerpt_style',
			[
				'label' => __('First Post Excerpt', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'layout' => 'layout-02',
				],
			]
		);

		$this->add_control(
			'first_post_excerpt_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-post:first-child .bt-post--excerpt' => 'color: {{VALUE}};',
				],
				'condition' => [
					'layout' => 'layout-02',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'first_post_excerpt_typography',
				'label' => __('Typography', 'somnia'),
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-post:first-child .bt-post--excerpt',
				'condition' => [
					'layout' => 'layout-02',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => esc_html__('Pagination', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pagination!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-pagination .page-numbers:not(.current)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_color_hover',
			[
				'label' => __('Color Hover', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-pagination .page-numbers:not(.current, .dots):hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_color_current',
			[
				'label' => __('Color Current', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-pagination .page-numbers.current' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_typography',
				'label' => __('Typography', 'somnia'),
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-pagination .page-numbers',
			]
		);
		$this->add_responsive_control(
			'pagination_text_align',
			[
				'label' => esc_html__('Alignment', 'somnia'),
				'type'  => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__('Left', 'somnia'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'somnia'),
						'icon'  => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__('Right', 'somnia'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'start',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .bt-pagination' => 'justify-content: {{VALUE}};text-align: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'pagination_space',
			[
				'label' => __('Space', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 60,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_controls()
	{

		$this->register_layout_section_controls();
		$this->register_query_section_controls();

		$this->register_style_section_controls();
	}

	public function query_posts()
	{
		$settings = $this->get_settings_for_display();

		$args = [
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $settings['posts_per_page'],
			'orderby' => $settings['orderby'],
			'order' => $settings['order'],
		];

		if ($settings['show_pagination'] == 'yes') {
			$args['paged'] = (get_query_var('paged')) ? get_query_var('paged') : 1;
		}

		if (! empty($settings['ids'])) {
			$args['post__in'] = $settings['ids'];
		}

		if (! empty($settings['ids_exclude'])) {
			$args['post__not_in'] = $settings['ids_exclude'];
		}

		if (! empty($settings['category'])) {
			$args['tax_query'] = array(
				array(
					'taxonomy' 		=> 'category',
					'terms' 		=> $settings['category'],
					'field' 		=> 'term_id',
					'operator' 		=> 'IN'
				)
			);
		}

		if (! empty($settings['category_exclude'])) {
			$args['tax_query'] = array(
				array(
					'taxonomy' 		=> 'category',
					'terms' 		=> $settings['category_exclude'],
					'field' 		=> 'term_id',
					'operator' 		=> 'NOT IN'
				)
			);
		}

		if (0 !== absint($settings['offset'])) {
			$args['offset'] = $settings['offset'];
		}

		return $query = new \WP_Query($args);
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$query = $this->query_posts();
		$excerpt = 'no';
		if ($settings['layout'] == 'layout-02') {
			$excerpt = 'yes';
		} 
		$elwg_class = 'bt-elwg-post-grid--' . $settings['layout'];
		?>
		<div class="<?php echo esc_attr($elwg_class); ?>">
			<?php
			if ($query->have_posts()) {
			?>
				<div class="bt-post-grid">
					<?php
					while ($query->have_posts()) : $query->the_post();
						get_template_part('framework/templates/post', 'style', array('image-size' => $settings['thumbnail_size'], 'excerpt' => $excerpt));
					endwhile;
					?>
				</div>
			<?php
				if ($settings['show_pagination'] == 'yes') {
					somnia_paginate_links($query);
				}
			} else {
				get_template_part('framework/templates/post', 'none');
			}
			?>
		</div>
<?php
		wp_reset_postdata();
	}

	protected function content_template() {}
}
