<?php

namespace SomniaElementorWidgets\Widgets\SiteInformation;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Widget_SiteInformation extends Widget_Base
{

	public function get_name()
	{
		return 'bt-site-information';
	}

	public function get_title()
	{
		return __('Site Information', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-bullet-list';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	protected function register_content_section_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Content', 'somnia'),
			]
		);

		$this->add_control(
			'list',
			[
				'label' => esc_html__('Show Elements', 'somnia'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'phone'  => esc_html__('Phone', 'somnia'),
					'address' => esc_html__('Address', 'somnia'),
					'email' => esc_html__('Email', 'somnia'),
				],
				'default' => ['phone', 'email'],
			]
		);
		$this->add_control(
			'title_phone',
			[
				'label' => esc_html__('Title Phone', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);
		$this->add_control(
			'title_address',
			[
				'label' => esc_html__('Title Address', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);
		$this->add_control(
			'title_email',
			[
				'label' => esc_html__('Title Email', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);
		$this->end_controls_section();
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
			'style',
			[
				'label' => esc_html__('Layout Style', 'somnia'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'column' => [
						'title' => esc_html__('Block', 'somnia'),
						'icon' => 'eicon-editor-list-ul',
					],
					'row' => [
						'title' => esc_html__('Inline', 'somnia'),
						'icon' => 'eicon-ellipsis-h',
					],
				],
				'default' => 'row',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-site-infor' => 'flex-direction: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => __('Space Between', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-site-infor' => 'column-gap: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .bt-elwg-site-infor--item:not(:last-child)::before' => 'right: calc( ({{SIZE}}{{UNIT}} / 2) * -1 )',
				],

				'condition' => [
					'style' => 'row',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => __('Space Between', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 4,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-site-infor' => 'row-gap: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'style' => 'column',
				],
			]
		);

		$this->add_responsive_control(
			'separator',
			[
				'label'    => __('Separator', 'somnia'),
				'type'     => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'somnia'),
				'label_off' => __('Hide', 'somnia'),
				'default'  => '',
				'condition' => [
					'style' => 'row',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_content_section_controls()
	{

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'icon_show',
			[
				'label' => __('Icon Show', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'somnia'),
				'label_off' => __('Hide', 'somnia'),
				'default' => 'yes',
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => __('Icon Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-site-infor--item svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .bt-elwg-site-infor--item svg path' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __('Text Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-site-infor--item' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .bt-elwg-site-infor--item a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'label' => __('Typography', 'somnia'),
				'default' => '',
				'selector' => '{{WRAPPER}} .bt-elwg-site-infor--item',
			]
		);

		$this->add_control(
			'separator_style',
			[
				'label' => __('Separator', 'somnia'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-site-infor--item:not(:last-child)::before' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'separator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'separator_width',
			[
				'label' => __('Width', 'somnia'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px',],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-site-infor--item:not(:last-child)::before' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'separator' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_controls()
	{
		$this->register_content_section_controls();
		$this->register_layout_section_controls();
		$this->register_style_content_section_controls();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		if (isset($settings['separator']) && $settings['separator'] == 'yes') {
			$separator = 'separator';
		} else {
			$separator = '';
		}

		if (isset($settings['separator_tablet']) && $settings['separator_tablet'] == 'yes') {
			$separator_tb = '';
		} else {
			$separator_tb = 'separator-tb-hide';
		}

		if (isset($settings['separator_mobile']) && $settings['separator_mobile'] == 'yes') {
			$separator_mb = '';
		} else {
			$separator_mb = 'separator-mb-hide';
		}

		$classes  = implode(' ', [$separator, $separator_tb, $separator_mb]);


		if (empty($settings['list'])) {
			return;
		}
?>

		<div class="bt-elwg-site-infor bt-elwg-site-infor--default <?php echo esc_attr($classes);
																	if ($settings['icon_show'] != 'yes') {
																		echo ' bt-no-show-icon';
																	} ?>">
			<?php get_template_part('framework/templates/site-information', 'style', array('layout' => 'default', 'data' => $settings['list'],'title_phone' => $settings['title_phone'],'title_email' => $settings['title_email'],'title_address' => $settings['title_address'])); ?>
		</div>

<?php }

	protected function content_template() {}
}
