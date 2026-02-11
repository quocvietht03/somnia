<?php

namespace SomniaElementorWidgets\Widgets\HighlightedHeading;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_HighlightedHeading extends Widget_Base
{

	public function get_name()
	{
		return 'bt-highlighted-heading';
	}

	public function get_title()
	{
		return __('Highlighted Heading', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-heading';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	protected function register_content_section_controls()
	{
		$this->start_controls_section(
			'section_heading',
			[
				'label' => __('Heading', 'somnia'),
			]
		);

		$this->add_control(
			'before_text',
			[
				'label'       => esc_html__('Before Text', 'somnia'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__('This is the heading', 'somnia'),
			]
		);

		$this->add_control(
			'highlighted_text',
			[
				'label'       => esc_html__('Highlighted Text', 'somnia'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__('Highlighted', 'somnia'),
			]
		);

		$this->add_control(
			'after_text',
			[
				'label'       => esc_html__('After Text', 'somnia'),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
			]
		);

		$this->add_control(
			'link_heading',
			[
				'label'       => esc_html__('Link', 'somnia'),
				'type'        => Controls_Manager::TEXT,
				'input_type'  => 'url',
				'default'     => '',
			]
		);

		$this->add_control(
			'html_tag',
			[
				'label' => __('HTML Tag', 'somnia'),
				'type'  => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => [
					'h1' => 'h1',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
					'p' => 'p',
					'span' => 'span',
					'div' => 'div',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_content_section_controls()
	{

		$this->start_controls_section(
			'section_style_heading',
			[
				'label' => esc_html__('Heading', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'text_align',
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
					'{{WRAPPER}} .bt-elwg-highlighted-heading' => 'justify-content: {{VALUE}};text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-highlighted-heading h1' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-highlighted-heading h2' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-highlighted-heading h3' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-highlighted-heading h4' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-highlighted-heading h5' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-highlighted-heading h6' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-highlighted-heading p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-highlighted-heading span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-highlighted-heading div' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => __('Typography', 'somnia'),
				'default'  => '',
				'selector' => '{{WRAPPER}} .bt-elwg-highlighted-heading h1, {{WRAPPER}} .bt-elwg-highlighted-heading h2, {{WRAPPER}} .bt-elwg-highlighted-heading h3, {{WRAPPER}} .bt-elwg-highlighted-heading h4, {{WRAPPER}} .bt-elwg-highlighted-heading h5, {{WRAPPER}} .bt-elwg-highlighted-heading h6, {{WRAPPER}} .bt-elwg-highlighted-heading p, {{WRAPPER}} .bt-elwg-highlighted-heading span, {{WRAPPER}} .bt-elwg-highlighted-heading div',
			]
		);

		$this->add_control(
			'highlighted_text_style',
			[
				'label' => __('Highlighted Text', 'somnia'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'highlighted_text_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-highlighted-heading .__text-highlighted' => 'color: {{VALUE}};'
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'highlighted_text_typography',
				'label'    => __('Typography', 'somnia'),
				'default'  => '',
				'selector' => '{{WRAPPER}} .bt-elwg-highlighted-heading h1 .__text-highlighted, {{WRAPPER}} .bt-elwg-highlighted-heading h2 .__text-highlighted, {{WRAPPER}} .bt-elwg-highlighted-heading h3 .__text-highlighted, {{WRAPPER}} .bt-elwg-highlighted-heading h4 .__text-highlighted, {{WRAPPER}} .bt-elwg-highlighted-heading h5 .__text-highlighted, {{WRAPPER}} .bt-elwg-highlighted-heading h6 .__text-highlighted, {{WRAPPER}} .bt-elwg-highlighted-heading p .__text-highlighted, {{WRAPPER}} .bt-elwg-highlighted-heading span .__text-highlighted, {{WRAPPER}} .bt-elwg-highlighted-heading div .__text-highlighted',
			]
		);


		$this->end_controls_section();
	}

	protected function register_controls()
	{
		$this->register_content_section_controls();
		$this->register_style_content_section_controls();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$html_tag = isset($settings['html_tag']) ? $settings['html_tag'] : '';
		$link     = isset($settings['link_heading']) ? $settings['link_heading'] : '';
		$before_text = isset($settings['before_text']) ? $settings['before_text'] : '';
		$after_text  = isset($settings['after_text']) ? $settings['after_text'] : '';
		$hl_text     = isset($settings['highlighted_text']) ? $settings['highlighted_text'] : '';
?>

		<div class="bt-elwg-highlighted-heading">
			<?php echo "<$html_tag>"; ?>

			<?php if (!empty($link)) : ?>
				<a href="<?php echo esc_url($link); ?>">
					<?php echo !empty($before_text) ? esc_html($before_text) : ''; ?>

					<?php if (!empty($hl_text)) : ?>
						<span class="__text-highlighted">
							<?php echo esc_html($hl_text); ?>
						</span>
					<?php endif; ?>

					<?php echo !empty($after_text) ? esc_html($after_text) : ''; ?>
				</a>
			<?php else : ?>
				<?php echo !empty($before_text) ? esc_html($before_text) : ''; ?>

				<?php if (!empty($hl_text)) : ?>
					<span class="__text-highlighted">
						<?php echo esc_html($hl_text); ?>
					</span>
				<?php endif; ?>

				<?php echo !empty($after_text) ? esc_html($after_text) : ''; ?>
			<?php endif; ?>

			<?php echo "</$html_tag>"; ?>
		</div>
<?php
	}

	protected function content_template()
	{
	}
}
