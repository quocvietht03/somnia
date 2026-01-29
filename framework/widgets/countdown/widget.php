<?php

namespace SomniaElementorWidgets\Widgets\CountDown;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use DateTime;
use DateTimeZone;

class Widget_CountDown extends Widget_Base
{
	public function get_name()
	{
		return 'bt-countdown';
	}

	public function get_title()
	{
		return __('Countdown', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-countdown';
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
				'label' => __('Content', 'somnia'),
			]
		);

		$this->add_control(
			'countdown_date',
			[
				'label' => __('Countdown Date', 'somnia'),
				'type' => Controls_Manager::DATE_TIME,
				'default' => date('Y-m-d H:i', strtotime('+1 month')),
				'description' => __('Set the date and time for the countdown', 'somnia'),
			]
		);

		$this->add_control(
			'show_infinity_date',
			[
				'label' => __('Show Infinity Date', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'somnia'),
				'label_off' => __('Hide', 'somnia'),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => __('Enable to display an infinite countdown timer that never expires', 'somnia'),
			]
		);

		$this->add_control(
			'infinity_date',
			[
				'label' => __('Days from Now', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 365,
				'default' => 12,
				'condition' => [
					'show_infinity_date' => 'yes',
				],
				'description' => __('Set the number of days from today for the countdown timer (1-365 days)', 'somnia'),
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
		$this->add_responsive_control(
			'item_background_color',
			[
				'label' => __('Item Background Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-countdown--item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' => __('Item Padding', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bt-countdown--item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'digits_color',
			[
				'label' => __('Digits Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-countdown--digits' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'digits_typography',
				'selector' => '{{WRAPPER}} .bt-countdown--digits',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __('Label Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-countdown--label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .bt-countdown--label',
			]
		);

		$this->add_control(
			'delimiter_color',
			[
				'label' => __('Delimiter Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-delimiter' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'delimiter_spacing',
			[
				'label' => __('Delimiter Spacing', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-delimiter' => 'margin: 0 {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}

	protected function register_controls()
	{
		$this->register_content_section_controls();
		$this->register_style_content_section_controls();
	}

	/**
	 * Get the option name with widget ID to make it unique per widget instance
	 *
	 * @param string $base_name Base option name
	 * @return string
	 */
	protected function get_widget_option_name($base_name)
	{
		return sprintf('somnia_countdown_%s_%s', $this->get_id(), $base_name);
	}

	/**
	 * Calculate and get the infinity countdown date
	 *
	 * @param array $settings Widget settings
	 * @return string Formatted date string
	 */
	protected function get_infinity_countdown_date($settings)
	{
		// Validate infinity date input
		$infinity_days = absint($settings['infinity_date']);
		if ($infinity_days < 1 || $infinity_days > 365) {
			$infinity_days = 12; // Default to 12 days if invalid
		}

		// Get current date in site's timezone
		$timezone = new DateTimeZone(wp_timezone_string());
		$current_date = new DateTime('now', $timezone);

		// Get option names for this widget instance
		$countdown_option = $this->get_widget_option_name('date');
		$days_option = $this->get_widget_option_name('days');

		// Get saved values
		$last_countdown = get_option($countdown_option);
		$last_days = absint(get_option($days_option));

		// Check if we need to update the countdown
		$needs_update = false;
		if (!$last_countdown || $infinity_days !== $last_days) {
			$needs_update = true;
		} else {
			$saved_date = DateTime::createFromFormat('Y-m-d H:i:s', $last_countdown, $timezone);
			if ($saved_date && $saved_date <= $current_date) {
				$needs_update = true;
			}
		}

		if ($needs_update) {
			// Calculate new date by adding days
			$current_date->modify(sprintf('+%d days', $infinity_days));
			$new_countdown = $current_date->format('Y-m-d H:i:s');

			// Save with autoload=no since this is widget-specific data
			update_option($countdown_option, $new_countdown, 'no');
			update_option($days_option, $infinity_days, 'no');

			return $new_countdown;
		}

		return $last_countdown;
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$date_countdown = $settings['countdown_date'];
		if ($settings['show_infinity_date'] === 'yes') {
			$date_countdown = $this->get_infinity_countdown_date($settings);
		}
		// Get current date in site's timezone
		$timezone = new DateTimeZone(wp_timezone_string());
		$current_date = new DateTime('now', $timezone);
		$current_date = $current_date->format('Y-m-d H:i:s');
		?>
		<div class="bt-elwg-countdown--default ">
			<div class="bt-countdown bt-countdown-js" data-infinity="<?php echo esc_attr($settings['show_infinity_date']); ?>" data-time="<?php echo esc_attr($date_countdown); ?>" data-current-time="<?php echo esc_attr($current_date); ?>">
				<div class="bt-countdown--item">
					<span class="bt-countdown--digits bt-countdown-days">--</span>
					<span class="bt-countdown--label"><?php _e('Days', 'somnia'); ?></span>
				</div>
				<div class="bt-delimiter">:</div>
				<div class="bt-countdown--item">
					<span class="bt-countdown--digits bt-countdown-hours">--</span>
					<span class="bt-countdown--label"><?php _e('Hours', 'somnia'); ?></span>
				</div>
				<div class="bt-delimiter">:</div>
				<div class="bt-countdown--item">
					<span class="bt-countdown--digits bt-countdown-mins">--</span>
					<span class="bt-countdown--label"><?php _e('Mins', 'somnia'); ?></span>
				</div>
				<div class="bt-delimiter">:</div>
				<div class="bt-countdown--item">
					<span class="bt-countdown--digits bt-countdown-secs">--</span>
					<span class="bt-countdown--label"><?php _e('Secs', 'somnia'); ?></span>
				</div>
			</div>
		</div>
<?php
	}

	protected function content_template() {}
}
