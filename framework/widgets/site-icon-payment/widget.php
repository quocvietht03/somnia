<?php

namespace SomniaElementorWidgets\Widgets\IconPayment;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

class Widget_IconPayment extends Widget_Base
{

	public function get_name()
	{
		return 'bt-icon-payment';
	}

	public function get_title()
	{
		return __('Site Payment Icons', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-product-info';
	}

	public function get_categories()
	{
		return ['somnia'];
	}


	protected function register_style_content_section_controls()
	{
		$this->start_controls_section(
			'section_style',
			[
				'label' => __('Style', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => __('Gap', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-icon-payment--grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __('Icon Size', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 38,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-icon-payment--image img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label' => __('Icon Border Radius', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bt-icon-payment--image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}

	protected function register_controls()
	{
		$this->register_style_content_section_controls();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		
		if (function_exists('get_field')) {
			$site_infor = get_field('site_information', 'options');
		} else {
			return;
		}

		if (empty($site_infor['payment_icons'])) {
			return;
		}
		?>
		<div class="bt-elwg-icon-payment">
			<div class="bt-icon-payment--grid">
				<?php foreach ($site_infor['payment_icons'] as $image) : ?>
					<?php
					if (empty($image['url'])) {
						continue;
					}
					?>
					<div class="bt-icon-payment--image">
						<?php
						if (!empty($image['id'])) {
							echo wp_get_attachment_image($image['id'], 'thumbnail');
						} else {
							echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting payment icon', 'somnia') . '">';
						}
						?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	protected function content_template() {}
}
