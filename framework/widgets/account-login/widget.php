<?php

namespace SomniaElementorWidgets\Widgets\AccountLogin;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_AccountLogin extends Widget_Base
{

	public function get_name()
	{
		return 'bt-account-login';
	}

	public function get_title()
	{
		return __('Account Login', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-lock-user';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	public function get_script_depends()
	{
		return ['magnific-popup', 'elementor-widgets'];
	}

	protected function register_content_section_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Settings', 'somnia'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_popup',
			[
				'label' => esc_html__('Enable Popup Mode', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'somnia'),
				'label_off' => esc_html__('No', 'somnia'),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => esc_html__('Enable to show login form in popup instead of redirecting to login page', 'somnia'),
			]
		);

		$this->add_control(
			'popup_title',
			[
				'label' => esc_html__('Login Title', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Log In', 'somnia'),
				'condition' => [
					'enable_popup' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_register_link',
			[
				'label' => esc_html__('Show Register Link', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'somnia'),
				'label_off' => esc_html__('No', 'somnia'),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'enable_popup' => 'yes',
				],
			]
		);
		$this->add_control(
			'register_title',
			[
				'label' => esc_html__('Register Title', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Create A Free Account', 'somnia'),
				'condition' => [
					'enable_popup' => 'yes',
					'show_register_link' => 'yes',
				],
			]
		);
		$this->add_control(
			'terms_url',
			[
				'label' => esc_html__('Terms of User URL', 'somnia'),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__('https://your-site.com/terms', 'somnia'),
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => false,
				],
				'condition' => [
					'enable_popup' => 'yes',
					'show_register_link' => 'yes',
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
			'account_login_color',
			[
				'label'     => esc_html__('Color', 'somnia'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-account-login .bt-account a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'account_login_typography',
				'label'    => esc_html__('Typography', 'somnia'),
				'default'  => '',
				'selector' => '{{WRAPPER}} .bt-elwg-account-login .bt-account a',
			]
		);
		$this->end_controls_section();

		// Popup Styling Section
		$this->start_controls_section(
			'section_popup_style',
			[
				'label' => esc_html__('Popup Style', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'enable_popup' => 'yes',
				],
			]
		);

		$this->add_control(
			'popup_width',
			[
				'label' => esc_html__('Popup Width', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 300,
						'max' => 800,
						'step' => 10,
					],
					'%' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 460,
				],
				'selectors' => [
					'{{WRAPPER}} .bt-login-popup-modal' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'popup_background',
			[
				'label' => esc_html__('Background Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .bt-login-popup-modal' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'popup_border_radius',
			[
				'label' => esc_html__('Border Radius', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'default' => [
					'top' => 8,
					'right' => 8,
					'bottom' => 8,
					'left' => 8,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .bt-login-popup-modal' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'popup_box_shadow',
				'label' => esc_html__('Box Shadow', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-login-popup-modal',
			]
		);

		$this->end_controls_section();
	}

	protected function register_controls()
	{
		$this->register_content_section_controls();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$widget_id = $this->get_id();
		$enable_popup = $settings['enable_popup'] === 'yes';
?>
		<div class="bt-elwg-account-login">
			<div class="bt-elwg-account-login-inner">
				<?php if (is_user_logged_in()) { ?>
					<?php $current_user = wp_get_current_user(); ?>
					<div class="bt-account bt-my-account">
						<?php if (class_exists('Woocommerce')) { ?>
							<a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
									<g id="User">
										<path id="Vector" d="M21.6489 19.8756C20.2211 17.4072 18.0208 15.6372 15.4529 14.7981C16.7231 14.042 17.7099 12.8898 18.2619 11.5185C18.8139 10.1473 18.9004 8.63272 18.5083 7.20749C18.1162 5.78226 17.2671 4.52515 16.0914 3.62921C14.9156 2.73327 13.4783 2.24805 12.0001 2.24805C10.5219 2.24805 9.08463 2.73327 7.90891 3.62921C6.73318 4.52515 5.88406 5.78226 5.49195 7.20749C5.09984 8.63272 5.18641 10.1473 5.73837 11.5185C6.29033 12.8898 7.27716 14.042 8.54732 14.7981C5.97951 15.6362 3.77919 17.4062 2.35138 19.8756C2.29902 19.961 2.26429 20.056 2.24924 20.155C2.23419 20.254 2.23912 20.355 2.26375 20.4521C2.28837 20.5492 2.33219 20.6404 2.39262 20.7202C2.45305 20.8001 2.52887 20.867 2.61559 20.9171C2.70232 20.9672 2.7982 20.9995 2.89758 21.0119C2.99695 21.0243 3.09782 21.0167 3.19421 20.9896C3.29061 20.9624 3.38059 20.9162 3.45884 20.8537C3.53709 20.7912 3.60203 20.7136 3.64982 20.6256C5.41607 17.5731 8.53794 15.7506 12.0001 15.7506C15.4623 15.7506 18.5842 17.5731 20.3504 20.6256C20.3982 20.7136 20.4632 20.7912 20.5414 20.8537C20.6197 20.9162 20.7097 20.9624 20.806 20.9896C20.9024 21.0167 21.0033 21.0243 21.1027 21.0119C21.2021 20.9995 21.2979 20.9672 21.3847 20.9171C21.4714 20.867 21.5472 20.8001 21.6076 20.7202C21.6681 20.6404 21.7119 20.5492 21.7365 20.4521C21.7611 20.355 21.7661 20.254 21.751 20.155C21.736 20.056 21.7012 19.961 21.6489 19.8756ZM6.75013 9.0006C6.75013 7.96225 7.05804 6.94721 7.63492 6.08385C8.21179 5.2205 9.03173 4.54759 9.99104 4.15023C10.9504 3.75287 12.006 3.6489 13.0244 3.85147C14.0428 4.05405 14.9782 4.55406 15.7124 5.28829C16.4467 6.02251 16.9467 6.95797 17.1493 7.97637C17.3518 8.99477 17.2479 10.0504 16.8505 11.0097C16.4531 11.969 15.7802 12.7889 14.9169 13.3658C14.0535 13.9427 13.0385 14.2506 12.0001 14.2506C10.6082 14.2491 9.27371 13.6955 8.28947 12.7113C7.30522 11.727 6.75162 10.3925 6.75013 9.0006Z"></path>
									</g>
								</svg>
								<?php esc_html_e('My Account', 'somnia'); ?>
							</a>
						<?php } else { ?>
							<a href="<?php echo esc_url(wp_logout_url()); ?>">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
									<g id="User">
										<path id="Vector" d="M21.6489 19.8756C20.2211 17.4072 18.0208 15.6372 15.4529 14.7981C16.7231 14.042 17.7099 12.8898 18.2619 11.5185C18.8139 10.1473 18.9004 8.63272 18.5083 7.20749C18.1162 5.78226 17.2671 4.52515 16.0914 3.62921C14.9156 2.73327 13.4783 2.24805 12.0001 2.24805C10.5219 2.24805 9.08463 2.73327 7.90891 3.62921C6.73318 4.52515 5.88406 5.78226 5.49195 7.20749C5.09984 8.63272 5.18641 10.1473 5.73837 11.5185C6.29033 12.8898 7.27716 14.042 8.54732 14.7981C5.97951 15.6362 3.77919 17.4062 2.35138 19.8756C2.29902 19.961 2.26429 20.056 2.24924 20.155C2.23419 20.254 2.23912 20.355 2.26375 20.4521C2.28837 20.5492 2.33219 20.6404 2.39262 20.7202C2.45305 20.8001 2.52887 20.867 2.61559 20.9171C2.70232 20.9672 2.7982 20.9995 2.89758 21.0119C2.99695 21.0243 3.09782 21.0167 3.19421 20.9896C3.29061 20.9624 3.38059 20.9162 3.45884 20.8537C3.53709 20.7912 3.60203 20.7136 3.64982 20.6256C5.41607 17.5731 8.53794 15.7506 12.0001 15.7506C15.4623 15.7506 18.5842 17.5731 20.3504 20.6256C20.3982 20.7136 20.4632 20.7912 20.5414 20.8537C20.6197 20.9162 20.7097 20.9624 20.806 20.9896C20.9024 21.0167 21.0033 21.0243 21.1027 21.0119C21.2021 20.9995 21.2979 20.9672 21.3847 20.9171C21.4714 20.867 21.5472 20.8001 21.6076 20.7202C21.6681 20.6404 21.7119 20.5492 21.7365 20.4521C21.7611 20.355 21.7661 20.254 21.751 20.155C21.736 20.056 21.7012 19.961 21.6489 19.8756ZM6.75013 9.0006C6.75013 7.96225 7.05804 6.94721 7.63492 6.08385C8.21179 5.2205 9.03173 4.54759 9.99104 4.15023C10.9504 3.75287 12.006 3.6489 13.0244 3.85147C14.0428 4.05405 14.9782 4.55406 15.7124 5.28829C16.4467 6.02251 16.9467 6.95797 17.1493 7.97637C17.3518 8.99477 17.2479 10.0504 16.8505 11.0097C16.4531 11.969 15.7802 12.7889 14.9169 13.3658C14.0535 13.9427 13.0385 14.2506 12.0001 14.2506C10.6082 14.2491 9.27371 13.6955 8.28947 12.7113C7.30522 11.727 6.75162 10.3925 6.75013 9.0006Z"></path>
									</g>
								</svg>
								<?php esc_html_e('Sign Out', 'somnia'); ?>
							</a>
						<?php } ?>
					</div>
				<?php } else { ?>
					<div class="bt-account bt-login">
						<?php if ($enable_popup) { ?>
							<a href="#bt-login-popup-<?php echo esc_attr($widget_id); ?>" class="bt-js-open-popup-link">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
									<g id="User">
										<path id="Vector" d="M21.6489 19.8756C20.2211 17.4072 18.0208 15.6372 15.4529 14.7981C16.7231 14.042 17.7099 12.8898 18.2619 11.5185C18.8139 10.1473 18.9004 8.63272 18.5083 7.20749C18.1162 5.78226 17.2671 4.52515 16.0914 3.62921C14.9156 2.73327 13.4783 2.24805 12.0001 2.24805C10.5219 2.24805 9.08463 2.73327 7.90891 3.62921C6.73318 4.52515 5.88406 5.78226 5.49195 7.20749C5.09984 8.63272 5.18641 10.1473 5.73837 11.5185C6.29033 12.8898 7.27716 14.042 8.54732 14.7981C5.97951 15.6362 3.77919 17.4062 2.35138 19.8756C2.29902 19.961 2.26429 20.056 2.24924 20.155C2.23419 20.254 2.23912 20.355 2.26375 20.4521C2.28837 20.5492 2.33219 20.6404 2.39262 20.7202C2.45305 20.8001 2.52887 20.867 2.61559 20.9171C2.70232 20.9672 2.7982 20.9995 2.89758 21.0119C2.99695 21.0243 3.09782 21.0167 3.19421 20.9896C3.29061 20.9624 3.38059 20.9162 3.45884 20.8537C3.53709 20.7912 3.60203 20.7136 3.64982 20.6256C5.41607 17.5731 8.53794 15.7506 12.0001 15.7506C15.4623 15.7506 18.5842 17.5731 20.3504 20.6256C20.3982 20.7136 20.4632 20.7912 20.5414 20.8537C20.6197 20.9162 20.7097 20.9624 20.806 20.9896C20.9024 21.0167 21.0033 21.0243 21.1027 21.0119C21.2021 20.9995 21.2979 20.9672 21.3847 20.9171C21.4714 20.867 21.5472 20.8001 21.6076 20.7202C21.6681 20.6404 21.7119 20.5492 21.7365 20.4521C21.7611 20.355 21.7661 20.254 21.751 20.155C21.736 20.056 21.7012 19.961 21.6489 19.8756ZM6.75013 9.0006C6.75013 7.96225 7.05804 6.94721 7.63492 6.08385C8.21179 5.2205 9.03173 4.54759 9.99104 4.15023C10.9504 3.75287 12.006 3.6489 13.0244 3.85147C14.0428 4.05405 14.9782 4.55406 15.7124 5.28829C16.4467 6.02251 16.9467 6.95797 17.1493 7.97637C17.3518 8.99477 17.2479 10.0504 16.8505 11.0097C16.4531 11.969 15.7802 12.7889 14.9169 13.3658C14.0535 13.9427 13.0385 14.2506 12.0001 14.2506C10.6082 14.2491 9.27371 13.6955 8.28947 12.7113C7.30522 11.727 6.75162 10.3925 6.75013 9.0006Z"></path>
									</g>
								</svg>
								<?php esc_html_e('Sign In', 'somnia'); ?>
							</a>
						<?php } else { ?>
							<?php if (class_exists('Woocommerce')) { ?>
								<a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
										<g id="User">
											<path id="Vector" d="M21.6489 19.8756C20.2211 17.4072 18.0208 15.6372 15.4529 14.7981C16.7231 14.042 17.7099 12.8898 18.2619 11.5185C18.8139 10.1473 18.9004 8.63272 18.5083 7.20749C18.1162 5.78226 17.2671 4.52515 16.0914 3.62921C14.9156 2.73327 13.4783 2.24805 12.0001 2.24805C10.5219 2.24805 9.08463 2.73327 7.90891 3.62921C6.73318 4.52515 5.88406 5.78226 5.49195 7.20749C5.09984 8.63272 5.18641 10.1473 5.73837 11.5185C6.29033 12.8898 7.27716 14.042 8.54732 14.7981C5.97951 15.6362 3.77919 17.4062 2.35138 19.8756C2.29902 19.961 2.26429 20.056 2.24924 20.155C2.23419 20.254 2.23912 20.355 2.26375 20.4521C2.28837 20.5492 2.33219 20.6404 2.39262 20.7202C2.45305 20.8001 2.52887 20.867 2.61559 20.9171C2.70232 20.9672 2.7982 20.9995 2.89758 21.0119C2.99695 21.0243 3.09782 21.0167 3.19421 20.9896C3.29061 20.9624 3.38059 20.9162 3.45884 20.8537C3.53709 20.7912 3.60203 20.7136 3.64982 20.6256C5.41607 17.5731 8.53794 15.7506 12.0001 15.7506C15.4623 15.7506 18.5842 17.5731 20.3504 20.6256C20.3982 20.7136 20.4632 20.7912 20.5414 20.8537C20.6197 20.9162 20.7097 20.9624 20.806 20.9896C20.9024 21.0167 21.0033 21.0243 21.1027 21.0119C21.2021 20.9995 21.2979 20.9672 21.3847 20.9171C21.4714 20.867 21.5472 20.8001 21.6076 20.7202C21.6681 20.6404 21.7119 20.5492 21.7365 20.4521C21.7611 20.355 21.7661 20.254 21.751 20.155C21.736 20.056 21.7012 19.961 21.6489 19.8756ZM6.75013 9.0006C6.75013 7.96225 7.05804 6.94721 7.63492 6.08385C8.21179 5.2205 9.03173 4.54759 9.99104 4.15023C10.9504 3.75287 12.006 3.6489 13.0244 3.85147C14.0428 4.05405 14.9782 4.55406 15.7124 5.28829C16.4467 6.02251 16.9467 6.95797 17.1493 7.97637C17.3518 8.99477 17.2479 10.0504 16.8505 11.0097C16.4531 11.969 15.7802 12.7889 14.9169 13.3658C14.0535 13.9427 13.0385 14.2506 12.0001 14.2506C10.6082 14.2491 9.27371 13.6955 8.28947 12.7113C7.30522 11.727 6.75162 10.3925 6.75013 9.0006Z"></path>
										</g>
									</svg>
									<?php esc_html_e('Sign In', 'somnia'); ?>
								</a>
							<?php } else { ?>
								<a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
										<g id="User">
											<path id="Vector" d="M21.6489 19.8756C20.2211 17.4072 18.0208 15.6372 15.4529 14.7981C16.7231 14.042 17.7099 12.8898 18.2619 11.5185C18.8139 10.1473 18.9004 8.63272 18.5083 7.20749C18.1162 5.78226 17.2671 4.52515 16.0914 3.62921C14.9156 2.73327 13.4783 2.24805 12.0001 2.24805C10.5219 2.24805 9.08463 2.73327 7.90891 3.62921C6.73318 4.52515 5.88406 5.78226 5.49195 7.20749C5.09984 8.63272 5.18641 10.1473 5.73837 11.5185C6.29033 12.8898 7.27716 14.042 8.54732 14.7981C5.97951 15.6362 3.77919 17.4062 2.35138 19.8756C2.29902 19.961 2.26429 20.056 2.24924 20.155C2.23419 20.254 2.23912 20.355 2.26375 20.4521C2.28837 20.5492 2.33219 20.6404 2.39262 20.7202C2.45305 20.8001 2.52887 20.867 2.61559 20.9171C2.70232 20.9672 2.7982 20.9995 2.89758 21.0119C2.99695 21.0243 3.09782 21.0167 3.19421 20.9896C3.29061 20.9624 3.38059 20.9162 3.45884 20.8537C3.53709 20.7912 3.60203 20.7136 3.64982 20.6256C5.41607 17.5731 8.53794 15.7506 12.0001 15.7506C15.4623 15.7506 18.5842 17.5731 20.3504 20.6256C20.3982 20.7136 20.4632 20.7912 20.5414 20.8537C20.6197 20.9162 20.7097 20.9624 20.806 20.9896C20.9024 21.0167 21.0033 21.0243 21.1027 21.0119C21.2021 20.9995 21.2979 20.9672 21.3847 20.9171C21.4714 20.867 21.5472 20.8001 21.6076 20.7202C21.6681 20.6404 21.7119 20.5492 21.7365 20.4521C21.7611 20.355 21.7661 20.254 21.751 20.155C21.736 20.056 21.7012 19.961 21.6489 19.8756ZM6.75013 9.0006C6.75013 7.96225 7.05804 6.94721 7.63492 6.08385C8.21179 5.2205 9.03173 4.54759 9.99104 4.15023C10.9504 3.75287 12.006 3.6489 13.0244 3.85147C14.0428 4.05405 14.9782 4.55406 15.7124 5.28829C16.4467 6.02251 16.9467 6.95797 17.1493 7.97637C17.3518 8.99477 17.2479 10.0504 16.8505 11.0097C16.4531 11.969 15.7802 12.7889 14.9169 13.3658C14.0535 13.9427 13.0385 14.2506 12.0001 14.2506C10.6082 14.2491 9.27371 13.6955 8.28947 12.7113C7.30522 11.727 6.75162 10.3925 6.75013 9.0006Z"></path>
										</g>
									</svg>
									<?php esc_html_e('Sign In', 'somnia'); ?>
								</a>
							<?php } ?>
						<?php } ?>
					</div>
				<?php } ?>
			</div>

			<?php if ($enable_popup && !is_user_logged_in()) { ?>
				<!-- Hidden Login/Register Popup Content -->
				<div id="bt-login-popup-<?php echo esc_attr($widget_id); ?>" class="bt-login-popup-modal mfp-hide">
					<div class="bt-popup-inner">
						<div class="bt-popup-header">
							<div class="bt-popup-tabs">
								<button type="button" class="bt-tab-btn active" data-tab="login">
									<?php echo esc_html($settings['popup_title']); ?>
								</button>
								<button type="button" class="bt-tab-btn" data-tab="register">
									<?php echo esc_html($settings['register_title']); ?>
								</button>
							</div>
						</div>
						<div class="bt-popup-content">
							<!-- Login Form -->
							<div class="bt-tab-content active" data-tab="login">
								<form class="bt-login-form" method="post" data-widget-id="<?php echo esc_attr($widget_id); ?>">
								<div class="bt-form-group">
									<label for="bt-username-<?php echo esc_attr($widget_id); ?>"><?php esc_html_e('Username or email address', 'somnia'); ?><span class="required">*</span></label>
									<input type="text" id="bt-username-<?php echo esc_attr($widget_id); ?>" name="username" class="bt-form-control" required>
								</div>
								
								<div class="bt-form-group">
									<label for="bt-password-<?php echo esc_attr($widget_id); ?>"><?php esc_html_e('Password', 'somnia'); ?><span class="required">*</span></label>
									<div class="bt-password-wrapper">
										<input type="password" id="bt-password-<?php echo esc_attr($widget_id); ?>" name="password" class="bt-form-control" required>
										<button type="button" class="bt-toggle-password" aria-label="<?php esc_attr_e('Show password', 'somnia'); ?>">
											<svg class="bt-eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
												<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
												<circle cx="12" cy="12" r="3"/>
											</svg>
											<svg class="bt-eye-off-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
												<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
												<line x1="1" y1="1" x2="23" y2="23"/>
											</svg>
										</button>
									</div>
								</div>

								<div class="bt-form-group bt-form-row">
									<label class="bt-checkbox-wrapper">
										<input type="checkbox" name="remember" value="1">
										<span class="bt-checkmark"></span>
										<?php esc_html_e('Remember me', 'somnia'); ?>
									</label>
									<a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="bt-forgot-password"><?php esc_html_e('Forgot Your Password?', 'somnia'); ?></a>
								</div>

								<div class="bt-form-group">
									<button type="submit" class="bt-login-btn"><?php esc_html_e('Login', 'somnia'); ?></button>
								</div>

								<div class="bt-login-messages"></div>

								<?php if ($settings['show_register_link'] === 'yes') { ?>
									<div class="bt-register-link">
										<?php esc_html_e('Not registered yet?', 'somnia'); ?> 
										<a href="#" class="bt-switch-to-register"><?php esc_html_e('Sign Up', 'somnia'); ?></a>
									</div>
								<?php } ?>


								<?php wp_nonce_field('bt_login_nonce', 'bt_login_nonce'); ?>
								</form>
							</div>

							<!-- Register Form -->
							<div class="bt-tab-content" data-tab="register">
								<form class="bt-register-form" method="post" data-widget-id="<?php echo esc_attr($widget_id); ?>">
									<div class="bt-form-group">
										<label for="bt-reg-username-<?php echo esc_attr($widget_id); ?>"><?php esc_html_e('Username', 'somnia'); ?><span class="required">*</span></label>
										<input type="text" id="bt-reg-username-<?php echo esc_attr($widget_id); ?>" name="username" class="bt-form-control" required>
									</div>
									
									<div class="bt-form-group">
										<label for="bt-reg-email-<?php echo esc_attr($widget_id); ?>"><?php esc_html_e('Email address', 'somnia'); ?><span class="required">*</span></label>
										<input type="email" id="bt-reg-email-<?php echo esc_attr($widget_id); ?>" name="email" class="bt-form-control" required>
									</div>
									
									<div class="bt-form-group">
										<label for="bt-reg-password-<?php echo esc_attr($widget_id); ?>"><?php esc_html_e('Password', 'somnia'); ?><span class="required">*</span></label>
										<div class="bt-password-wrapper">
											<input type="password" id="bt-reg-password-<?php echo esc_attr($widget_id); ?>" name="password" class="bt-form-control" required>
											<button type="button" class="bt-toggle-password" aria-label="<?php esc_attr_e('Show password', 'somnia'); ?>">
												<svg class="bt-eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
													<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
													<circle cx="12" cy="12" r="3"/>
												</svg>
												<svg class="bt-eye-off-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
													<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
													<line x1="1" y1="1" x2="23" y2="23"/>
												</svg>
											</button>
										</div>
									</div>

									<div class="bt-form-group">
										<label for="bt-reg-confirm-password-<?php echo esc_attr($widget_id); ?>"><?php esc_html_e('Confirm password', 'somnia'); ?><span class="required">*</span></label>
										<div class="bt-password-wrapper">
											<input type="password" id="bt-reg-confirm-password-<?php echo esc_attr($widget_id); ?>" name="confirm_password" class="bt-form-control" required>
											<button type="button" class="bt-toggle-password" aria-label="<?php esc_attr_e('Show password', 'somnia'); ?>">
												<svg class="bt-eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
													<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
													<circle cx="12" cy="12" r="3"/>
												</svg>
												<svg class="bt-eye-off-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
													<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
													<line x1="1" y1="1" x2="23" y2="23"/>
												</svg>
											</button>
										</div>
									</div>

									<div class="bt-form-group">
										<label class="bt-checkbox-wrapper">
											<input type="checkbox" name="agree_terms" value="1" required>
											<span class="bt-checkmark"></span>
											<?php esc_html_e('I agree to the', 'somnia'); ?> 
											<?php 
											$terms_url = $settings['terms_url'];
											if (!empty($terms_url['url'])) {
												$target = $terms_url['is_external'] ? ' target="_blank"' : '';
												$nofollow = $terms_url['nofollow'] ? ' rel="nofollow"' : '';
												echo '<a href="' . esc_url($terms_url['url']) . '"' . $target . $nofollow . '>';
												esc_html_e('Terms Of User', 'somnia');
												echo '</a>';
											} else {
												echo '<a href="#" target="_blank">';
												esc_html_e('Terms Of User', 'somnia');
												echo '</a>';
											}
											?>
										</label>
									</div>

									<div class="bt-form-group">
										<button type="submit" class="bt-register-btn"><?php esc_html_e('Create A New Account', 'somnia'); ?></button>
									</div>

									<div class="bt-register-messages"></div>

									<div class="bt-login-link">
										<?php esc_html_e('Already have an account?', 'somnia'); ?> 
										<a href="#" class="bt-switch-to-login"><?php esc_html_e('Login Here', 'somnia'); ?></a>
									</div>


									<?php wp_nonce_field('bt_register_nonce', 'bt_register_nonce'); ?>
								</form>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
<?php
	}

	protected function content_template() {}
}
