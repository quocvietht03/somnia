<?php

namespace SomniaElementorWidgets\Widgets\MobileMenu;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Widget_MobileMenu extends Widget_Base
{
	private function get_available_menus() {
		$menus = wp_get_nav_menus();

		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
	}

	public function get_name()
	{
		return 'bt-mobile-menu';
	}

	public function get_title()
	{
		return __('Mobile Menu', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-menu-toggle';
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
			'section_layout',
			[
				'label' => __('Layout', 'somnia'),
			]
		);
		
		$menus = $this->get_available_menus();

		if ( ! empty( $menus ) ) {
			$this->add_control(
				'menu',
				[
					'label' => esc_html__( 'Menu', 'somnia' ),
					'type' => Controls_Manager::SELECT,
					'options' => $menus,
					'default' => array_keys( $menus )[0],
					'save_default' => true,
					'description' => sprintf(
						/* translators: 1: Link opening tag, 2: Link closing tag. */
						esc_html__( 'Go to the %1$sMenus screen%2$s to manage your menus.', 'somnia' ),
						sprintf( '<a href="%s" target="_blank">', admin_url( 'nav-menus.php' ) ),
						'</a>'
					),
				]
			);
		} else {
			$this->add_control(
				'menu',
				[
					'type' => Controls_Manager::ALERT,
					'alert_type' => 'info',
					'heading' => esc_html__( 'There are no menus in your site.', 'somnia' ),
					'content' => sprintf(
						/* translators: 1: Link opening tag, 2: Link closing tag. */
						esc_html__( 'Go to the %1$sMenus screen%2$s to create one.', 'somnia' ),
						sprintf( '<a href="%s" target="_blank">', admin_url( 'nav-menus.php?action=edit&menu=0' ) ),
						'</a>'
					),
				]
			);
		}

		$this->end_controls_section();
	}


	protected function register_style_content_section_controls()
	{

		$this->start_controls_section(
			'section_style_main_menu',
			[
				'label' => esc_html__('Main Menu', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_main_menu_item_style' );

		$this->start_controls_tab(
			'tab_main_menu_item_normal',
			[
				'label' => esc_html__( 'Normal', 'somnia' ),
			]
		);

		$this->add_control(
			'color_main_menu_item',
			[
				'label' => esc_html__( 'Text Color', 'somnia' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-mobile-menu > li > a' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_main_menu_item_hover',
			[
				'label' => esc_html__( 'Hover', 'somnia' ),
			]
		);

		$this->add_control(
			'color_main_menu_item_hover',
			[
				'label' => esc_html__( 'Text Color', 'somnia' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-mobile-menu > li > a:hover,
					{{WRAPPER}} .bt-mobile-menu > li > a:focus' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_main_menu_item_active',
			[
				'label' => esc_html__( 'Active', 'somnia' ),
			]
		);

		$this->add_control(
			'color_main_menu_item_active',
			[
				'label' => esc_html__( 'Text Color', 'somnia' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-mobile-menu > li.current-menu-item > a' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'main_menu_separator',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'main_menu_typography',
				'selector' => '{{WRAPPER}} .bt-mobile-menu > li > a',
			]
		);

		$this->add_control(
			'padding_vertical_main_menu_item',
			[
				'label' => esc_html__( 'Vertical Padding', 'somnia' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-mobile-menu > li > a' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'toggle_main_menu_offset',
			[
				'label' => esc_html__( 'Toggle Offset', 'somnia' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bt-mobile-menu > li.menu-item-has-children > span.bt-toggle-icon' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub_menu',
			[
				'label' => esc_html__('Sub Menu', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_sub_menu_item_style' );

		$this->start_controls_tab(
			'tab_sub_menu_item_normal',
			[
				'label' => esc_html__( 'Normal', 'somnia' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item',
			[
				'label' => esc_html__( 'Text Color', 'somnia' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .sub-menu > li > a' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_item_hover',
			[
				'label' => esc_html__( 'Hover', 'somnia' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item_hover',
			[
				'label' => esc_html__( 'Text Color', 'somnia' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .sub-menu > li > a:hover,
					{{WRAPPER}} .sub-menu > li > a:focus' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_menu_item_active',
			[
				'label' => esc_html__( 'Active', 'somnia' ),
			]
		);

		$this->add_control(
			'color_sub_menu_item_active',
			[
				'label' => esc_html__( 'Text Color', 'somnia' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .sub-menu > li.current-menu-item > a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'sub_menu_separator',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sub_menu_typography',
				'selector' => '{{WRAPPER}} .sub-menu > li > a',
			]
		);

		$this->add_control(
			'padding_vertical_sub_menu_item',
			[
				'label' => esc_html__( 'Vertical Padding', 'somnia' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sub-menu > li > a' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'toggle_sub_menu_offset',
			[
				'label' => esc_html__( 'Toggle Offset', 'somnia' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .sub-menu > li.menu-item-has-children > span.bt-toggle-icon' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_additional_menu',
			[
				'label' => esc_html__('Additional Options', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_toggle_menu',
			[
				'label' => esc_html__( 'Toggle Menu', 'somnia' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'color_toggle_menu',
			[
				'label' => esc_html__( 'Color', 'somnia' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bt-mobile-menu span.bt-toggle-icon:before,
					{{WRAPPER}} .bt-mobile-menu span.bt-toggle-icon:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_divider_menu',
			[
				'label' => esc_html__( 'Divider Menu', 'somnia' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'divider_menu_style',
			[
				'label' => esc_html__( 'Style', 'somnia' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__( 'None', 'somnia' ),
					'solid' => esc_html__( 'Solid', 'somnia' ),
					'double' => esc_html__( 'Double', 'somnia' ),
					'dotted' => esc_html__( 'Dotted', 'somnia' ),
					'dashed' => esc_html__( 'Dashed', 'somnia' ),
				],
				'default' => 'solid',
				'selectors' => [
					'{{WRAPPER}} ul.bt-mobile-menu > li:not(:last-child)' => 'border-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'divider_menu_weight',
			[
				'label' => esc_html__( 'Width', 'somnia' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
					'em' => [
						'max' => 2,
					],
					'rem' => [
						'max' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} ul.bt-mobile-menu > li:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'divider_menu_style!' => 'none',
				],
			]
		);

		$this->add_control(
			'divider_menu_color',
			[
				'label' => esc_html__( 'Color', 'somnia' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.bt-mobile-menu > li:not(:last-child)' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'divider_menu_style!' => 'none',
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

	protected function render()
	{
		$available_menus = $this->get_available_menus();

		if ( ! $available_menus ) {
			return;
		}

		$settings = $this->get_active_settings();
		
		?>
			<div class="bt-elwg-mobile-menu--default">
				<?php
					wp_nav_menu(
						array(
						'menu' 				=> $settings['menu'],
						'container_class' 	=> 'bt-mobile-menu-wrapper',
						'menu_class' 		=> 'bt-mobile-menu',
						'items_wrap'      	=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'fallback_cb'     	=> false,
						'theme_location' 	=> ''
						)
					);
				?>
			</div>
		<?php
	}

	protected function content_template() {}
}
