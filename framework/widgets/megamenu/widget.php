<?php

namespace SomniaElementorWidgets\Widgets\MegaMenu;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Widget_MegaMenu extends Widget_Base
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
		return 'bt-megamenu';
	}

	public function get_title()
	{
		return __('BT MegaMenu', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-nav-menu';
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
					'{{WRAPPER}} .bt-megamenu > li > a' => 'color: {{VALUE}}; fill: {{VALUE}};',
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
					'{{WRAPPER}} .bt-megamenu > li > a:hover,
					{{WRAPPER}} .bt-megamenu > li > a:focus' => 'color: {{VALUE}}; fill: {{VALUE}};',
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
					'{{WRAPPER}} .bt-megamenu > li.current-menu-item > a' => 'color: {{VALUE}}; fill: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .bt-megamenu > li > a',
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
					'{{WRAPPER}} .bt-megamenu > li > a' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'padding_horizontal_main_menu_item',
			[
				'label' => esc_html__( 'Horizontal Padding', 'somnia' ),
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
					'{{WRAPPER}} .bt-megamenu > li > a' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_megamenu',
			[
				'label' => esc_html__('Mega Menu', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'megamenu_background',
			[
				'label' => esc_html__( 'Background Color', 'somnia' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-megamenu-dropdown' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'megamenu_padding',
			[
				'label' => esc_html__( 'Padding', 'somnia' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .bt-megamenu-dropdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
	 * Create custom Walker class for mega menu
	 *
	 * @return \Walker_Nav_Menu Custom walker instance
	 */
	private function create_megamenu_walker() {
		// Custom walker to support mega menu
		return new class extends \Walker_Nav_Menu {
			private $parent_items = array(); // Track parent items by depth

			public function start_lvl( &$output, $depth = 0, $args = null ) {
				// Check if parent item (at depth - 1) has mega menu enabled
				$parent_depth = $depth - 1;
				if ( $parent_depth >= 0 && isset( $this->parent_items[ $parent_depth ] ) ) {
					$parent_item_id = $this->parent_items[ $parent_depth ];
					$megamenu_enabled = get_post_meta( $parent_item_id, '_somnia_megamenu_enabled', true );
					$megamenu_block_id = get_post_meta( $parent_item_id, '_somnia_megamenu_block_id', true );
					
					// Validate block exists and is published
					if ( $megamenu_block_id ) {
						$block_status = get_post_status( $megamenu_block_id );
						$block_type = get_post_type( $megamenu_block_id );
						if ( $block_status !== 'publish' || $block_type !== 'megamenu_block' ) {
							$megamenu_block_id = false;
						}
					}

					// If parent has mega menu enabled with valid block, don't render sub-menu
					if ( $megamenu_enabled === '1' && $megamenu_block_id ) {
						return;
					}
				}

				$indent = str_repeat("\t", $depth);
				$output .= "\n$indent<ul class=\"sub-menu\">\n";
			}

			public function end_lvl( &$output, $depth = 0, $args = null ) {
				// Check if parent item (at depth - 1) has mega menu enabled
				$parent_depth = $depth - 1;
				if ( $parent_depth >= 0 && isset( $this->parent_items[ $parent_depth ] ) ) {
					$parent_item_id = $this->parent_items[ $parent_depth ];
					$megamenu_enabled = get_post_meta( $parent_item_id, '_somnia_megamenu_enabled', true );
					$megamenu_block_id = get_post_meta( $parent_item_id, '_somnia_megamenu_block_id', true );
					
					// Validate block exists and is published
					if ( $megamenu_block_id ) {
						$block_status = get_post_status( $megamenu_block_id );
						$block_type = get_post_type( $megamenu_block_id );
						if ( $block_status !== 'publish' || $block_type !== 'megamenu_block' ) {
							$megamenu_block_id = false;
						}
					}

					// If parent has mega menu enabled with valid block, don't render sub-menu
					if ( $megamenu_enabled === '1' && $megamenu_block_id ) {
						return;
					}
				}

				$indent = str_repeat("\t", $depth);
				$output .= "$indent</ul>\n";
			}

			public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
				if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
					$t = '';
					$n = '';
				} else {
					$t = "\t";
					$n = "\n";
				}
				$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

				$classes = empty( $item->classes ) ? array() : (array) $item->classes;
				$classes[] = 'menu-item-' . $item->ID;

				// Check if mega menu is enabled for this item (only for depth 0)
				$megamenu_enabled = false;
				$megamenu_block_id = false;
				$megamenu_content_width = 'full-width';
				$megamenu_horizontal_position = 'default';
				
				if ( $depth === 0 ) {
					$megamenu_enabled = get_post_meta( $item->ID, '_somnia_megamenu_enabled', true );
					$megamenu_block_id = get_post_meta( $item->ID, '_somnia_megamenu_block_id', true );
					$megamenu_content_width = get_post_meta( $item->ID, '_somnia_megamenu_content_width', true );
					if ( empty( $megamenu_content_width ) ) {
						$megamenu_content_width = 'full-width';
					}
					$megamenu_horizontal_position = get_post_meta( $item->ID, '_somnia_megamenu_horizontal_position', true );
					if ( empty( $megamenu_horizontal_position ) ) {
						$megamenu_horizontal_position = 'default';
					}
					
					// Validate block exists and is published
					if ( $megamenu_block_id ) {
						$block_status = get_post_status( $megamenu_block_id );
						$block_type = get_post_type( $megamenu_block_id );
						if ( $block_status !== 'publish' || $block_type !== 'megamenu_block' ) {
							$megamenu_block_id = false;
						}
					}

					// Only add class if mega menu is enabled AND has valid block
					if ( $megamenu_enabled === '1' && $megamenu_block_id ) {
						$classes[] = 'menu-item-has-megamenu';
					}

					// Store parent item ID for children to check
					$this->parent_items[ $depth ] = $item->ID;
				} else {
					// Store parent item ID for this depth
					$this->parent_items[ $depth ] = $item->ID;
				}

				$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

				$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
				$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

				$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
				$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

				$output .= $indent . '<li' . $id . $class_names . '>';

				$atts = array();
				$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
				$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
				$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
				$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

				$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

				$attributes = '';
				foreach ( $atts as $attr => $value ) {
					if ( ! empty( $value ) ) {
						$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
						$attributes .= ' ' . $attr . '="' . $value . '"';
					}
				}

				$title = apply_filters( 'the_title', $item->title, $item->ID );
				$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

				$item_output = isset( $args->before ) ? $args->before : '';
				$item_output .= '<a' . $attributes . '>';
				$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
				$item_output .= '</a>';

				// Add mega menu dropdown ONLY if enabled AND has valid block (depth 0 only)
				if ( $depth === 0 && $megamenu_enabled === '1' && $megamenu_block_id ) {
					$content_width_class = 'bt-megamenu-' . esc_attr( $megamenu_content_width );
					$horizontal_position_class = '';
					// Only add horizontal position class if content width is fit-to-content
					if ( $megamenu_content_width === 'fit-to-content' && in_array( $megamenu_horizontal_position, array( 'left', 'center', 'right' ), true ) ) {
						$horizontal_position_class = 'bt-megamenu-horizontal-' . esc_attr( $megamenu_horizontal_position );
					}
					$dropdown_classes = trim( 'bt-megamenu-dropdown ' . $content_width_class . ' ' . $horizontal_position_class );
					$item_output .= '<div class="' . esc_attr( $dropdown_classes ) . '">';
					if ( function_exists( 'somnia_display_megamenu_block' ) ) {
						ob_start();
						somnia_display_megamenu_block( $megamenu_block_id );
						$item_output .= ob_get_clean();
					}
					$item_output .= '</div>';
				}

				$item_output .= isset( $args->after ) ? $args->after : '';

				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}

			public function end_el( &$output, $item, $depth = 0, $args = null ) {
				// Remove parent item from tracking when ending
				if ( isset( $this->parent_items[ $depth ] ) ) {
					unset( $this->parent_items[ $depth ] );
				}

				if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
					$t = '';
					$n = '';
				} else {
					$t = "\t";
					$n = "\n";
				}
				$output .= "</li>{$n}";
			}
		};
	}

	protected function render()
	{
		$available_menus = $this->get_available_menus();

		if ( ! $available_menus ) {
			return;
		}

		$settings = $this->get_active_settings();
		$walker = $this->create_megamenu_walker();
		
		?>
			<div class="bt-elwg-megamenu--default">
				<?php
					wp_nav_menu(
						array(
							'menu' 				=> $settings['menu'],
							'container_class' 	=> 'bt-megamenu-wrapper',
							'menu_class' 		=> 'bt-megamenu',
							'items_wrap'      	=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
							'fallback_cb'     	=> false,
							'theme_location' 	=> '',
							'walker'			=> $walker,
							'link_before'		=> '<span>',
							'link_after'		=> '</span>',
						)
					);
				?>
			</div>
		<?php
	}

	protected function content_template() {}
}
