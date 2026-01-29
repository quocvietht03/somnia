<?php

namespace SomniaElementorWidgets\Widgets\HeadingAnimation;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_HeadingAnimation extends Widget_Base
{

	public function get_name()
	{
		return 'bt-heading-animation';
	}

	public function get_title()
	{
		return __('Heading Animation', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-animation-text';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	public function get_script_depends() {
        return ['elementor-widgets'];
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
			'animation_text',
			[
				'label'       => esc_html__('Heading Animation', 'somnia'),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default'     => esc_html__('Animation', 'somnia'),
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
				],
			]
		);
		$this->add_control(
			'entrance_animation',
			[
				'label' => esc_html__( 'Entrance Animation', 'somnia' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'None', 'somnia' ),
					'bt-animation-right' => esc_html__( 'Fade In Right', 'somnia' ),
					'bt-animation-left' => esc_html__( 'Fade In Left', 'somnia' ),
					'bt-animation-up' => esc_html__( 'Fade In Up', 'somnia' ),
					'bt-animation-down' => esc_html__( 'Fade In Down', 'somnia' ),
					'bt-animation-zoom' => esc_html__( 'Zoom In', 'somnia' ),
				],
			]
		);
		$this->add_responsive_control(
			'animation_delay',
			[
				'label' => __('Animation Delay (ms)', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'default' => 50,
				'min' => 10,
				'max' => 100000,
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
					'{{WRAPPER}} .bt-elwg-heading-animation' => 'justify-content: {{VALUE}};text-align: {{VALUE}};',
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
					'{{WRAPPER}} .bt-elwg-heading-animation .bt-animation' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'text_color_hover',
			[
				'label' => __('Color Hover', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-heading-animation .bt-animation:hover' => 'color: {{VALUE}};',
			
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => __('Typography', 'somnia'),
				'default'  => '',
				'selector' => '{{WRAPPER}} .bt-elwg-heading-animation h1, {{WRAPPER}} .bt-elwg-heading-animation h2, {{WRAPPER}} .bt-elwg-heading-animation h3, {{WRAPPER}} .bt-elwg-heading-animation h4, {{WRAPPER}} .bt-elwg-heading-animation h5, {{WRAPPER}} .bt-elwg-heading-animation h6',
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
		$hl_text     = isset($settings['animation_text']) ? $settings['animation_text'] : '';

?>
		<div class="bt-elwg-heading-animation" data-animation="<?php echo esc_attr($settings['entrance_animation']); ?>" data-delay="<?php echo esc_attr($settings['animation_delay']); ?>">
			<?php echo "<$html_tag>"; ?>

			<?php if (!empty($link)) : ?>
				<a href="<?php echo esc_url($link); ?>">
					<?php if (!empty($hl_text)) echo '<span class="bt-animation bt-heading-animation-js">' . esc_html($hl_text) . '</span>'; ?>
				</a>
			<?php else : ?>
				<?php if (!empty($hl_text)) echo '<span class="bt-animation bt-heading-animation-js">' . esc_html($hl_text) . '</span>'; ?>
			<?php endif; ?>

			<?php echo "</$html_tag>"; ?>
		</div>
<?php
	}

	protected function content_template()
	{
	}
}
