<?php

namespace SomniaElementorWidgets\Widgets\OrderTracking;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_OrderTracking extends Widget_Base
{

	public function get_name()
	{
		return 'bt-order-tracking';
	}

	public function get_title()
	{
		return __('Order Tracking', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-price-list';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	public function get_script_depends()
	{
		return ['elementor-widgets'];
	}

	protected function register_content_section_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Content', 'somnia'),
			]
		);

		$this->add_control(
			'note_text',
			[
				'label' => esc_html__('Note Text', 'somnia'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __('To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'somnia'),
				'rows' => 5,
			]
		);

		$this->add_control(
			'order_id_label',
			[
				'label' => esc_html__('Order ID Label', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Order ID', 'somnia'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'order_id_placeholder',
			[
				'label' => esc_html__('Order ID Placeholder', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Found in your order confirmation email.', 'somnia'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'billing_email_label',
			[
				'label' => esc_html__('Billing Email Label', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Billing email', 'somnia'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'billing_email_placeholder',
			[
				'label' => esc_html__('Billing Email Placeholder', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Email you used during checkout.', 'somnia'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => esc_html__('Button Text', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Track', 'somnia'),
				'label_block' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_section_controls()
	{
		// Title Style
		$this->start_controls_section(
			'section_style_note_text',
			[
				'label' => esc_html__('Note Text', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'note_text_color',
			[
				'label' => esc_html__('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-order-tracking-note-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'note_text_typography',
				'selector' => '{{WRAPPER}} .bt-order-tracking-note-text',
			]
		);

		$this->end_controls_section();

		// Label Style
		$this->start_controls_section(
			'section_style_label',
			[
				'label' => esc_html__('Label', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-order-tracking-form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .bt-order-tracking-form label',
			]
		);


		$this->end_controls_section();

		// Button Style
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__('Button', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('button_tabs');

		$this->start_controls_tab(
			'button_normal',
			[
				'label' => esc_html__('Normal', 'somnia'),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => esc_html__('Text Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-order-tracking-form button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__('Background Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-order-tracking-form button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover',
			[
				'label' => esc_html__('Hover', 'somnia'),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => esc_html__('Text Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-order-tracking-form button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_bg_color',
			[
				'label' => esc_html__('Background Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-order-tracking-form button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .bt-order-tracking-form button',
				'separator' => 'before',
			]
		);


		$this->end_controls_section();
	}

	protected function register_controls()
	{
		$this->register_content_section_controls();
		$this->register_style_section_controls();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
?>
		<div class="bt-elwg-order-tracking">
			<form class="bt-order-tracking-form" method="post">
				<?php if (!empty($settings['note_text'])) : ?>
					<div class="bt-order-tracking-note-text">
						<?php echo wp_kses_post($settings['note_text']); ?>
					</div>
				<?php endif; ?>
				<div class="bt-form-field">
					<label for="order-id"><?php echo esc_html($settings['order_id_label']); ?></label>
					<input
						type="text"
						id="order-id"
						name="order_id"
						placeholder="<?php echo esc_attr($settings['order_id_placeholder']); ?>"
						required />
				</div>

				<div class="bt-form-field">
					<label for="billing-email"><?php echo esc_html($settings['billing_email_label']); ?></label>
					<input
						type="email"
						id="billing-email"
						name="billing_email"
						placeholder="<?php echo esc_attr($settings['billing_email_placeholder']); ?>"
						required />
				</div>

				<div class="bt-form-submit">
					<button type="submit" class="bt-track-button">
						<?php echo esc_html($settings['button_text']); ?>
					</button>
				</div>

				<div class="bt-order-tracking-message"></div>
			</form>

			<div class="bt-order-tracking-result" style="display: none;">
				<!-- Order tracking result will be displayed here -->
			</div>
		</div>
	<?php
	}

	protected function content_template()
	{
	}
}
