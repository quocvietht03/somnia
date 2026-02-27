<?php

namespace SomniaElementorWidgets\Widgets\PostLoopItemStyle3;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_PostLoopItemStyle3 extends Widget_Base
{


	public function get_name()
	{
		return 'bt-post-loop-item-style-3';
	}

	public function get_title()
	{
		return __('Post Loop Item Style 3', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-post';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	protected function register_layout_section_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Content', 'somnia'),
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
				'name'     => 'thumbnail_hover_filters',
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
		// box setting

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_border',
				'label' => esc_html__('Border', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--inner',
			]
		);
		$this->add_responsive_control(
			'content_border_radius',
			[
				'label' => __('Border Radius', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bt-post--inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __('Padding', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bt-post--content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		// Post Date
		$this->add_control(
			'post_date_heading',
			[
				'label' => esc_html__('Date', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => esc_html__('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--publish',
			]
		);

		$this->add_control(
			'date_color',
			[
				'label' => esc_html__('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-post--publish' => 'color: {{VALUE}};',
				],
			]
		);
		// Post Category
		$this->add_control(
			'post_category_heading',
			[
				'label' => esc_html__('Category', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'category_typography',
				'label' => esc_html__('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--category, {{WRAPPER}} .bt-post--category a',
			]
		);

		$this->start_controls_tabs('category_color_tabs');

		$this->start_controls_tab(
			'category_color_normal',
			[
				'label' => esc_html__('Normal', 'somnia'),
			]
		);

		$this->add_control(
			'category_color',
			[
				'label' => esc_html__('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-post--category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'category_color_hover',
			[
				'label' => esc_html__('Hover', 'somnia'),
			]
		);

		$this->add_control(
			'category_hover_color',
			[
				'label' => esc_html__('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-post--category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Post Title
		$this->add_control(
			'post_title_heading',
			[
				'label' => esc_html__('Title', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
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

		$this->start_controls_tabs('title_color_tabs');

		$this->start_controls_tab(
			'title_color_normal',
			[
				'label' => esc_html__('Normal', 'somnia'),
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

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_color_hover',
			[
				'label' => esc_html__('Hover', 'somnia'),
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

		$this->add_control(
			'title_hover_decoration',
			[
				'label' => esc_html__('Text Decoration', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => [
					'none' => esc_html__('None', 'somnia'),
					'underline' => esc_html__('Underline', 'somnia'),
					'overline' => esc_html__('Overline', 'somnia'),
					'line-through' => esc_html__('Line Through', 'somnia'),
				],
				'selectors' => [
					'{{WRAPPER}} .bt-post--title a:hover' => 'text-decoration: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		// Post Excerpt
		$this->add_control(
			'post_excerpt_heading',
			[
				'label' => esc_html__('Excerpt', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'excerpt_typography',
				'label' => esc_html__('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--excerpt',
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
			]
		);

		// Read More Button
		$this->add_control(
			'post_button_heading',
			[
				'label' => esc_html__('Read More Button', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => esc_html__('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-post--button a',
			]
		);

		$this->start_controls_tabs('button_style_tabs');

		$this->start_controls_tab(
			'button_style_normal',
			[
				'label' => esc_html__('Normal', 'somnia'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__('Text Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-post--button a' => 'color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_style_hover',
			[
				'label' => esc_html__('Hover', 'somnia'),
			]
		);

		$this->add_control(
			'button_hover_text_color',
			[
				'label' => esc_html__('Text Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-post--button a:hover' => 'color: {{VALUE}};',
				],
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();
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
?>
		<div class="bt-elwg-post-loop-item--style3">
			<?php get_template_part('framework/templates/post', 'index', array('image-size' => $settings['thumbnail_size'])); ?>
		</div>
<?php
	}

	protected function content_template() {}
}
