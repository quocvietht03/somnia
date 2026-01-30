<?php

namespace SomniaElementorWidgets\Widgets\AccordionHotspot;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

class Widget_AccordionHotspot extends Widget_Base
{

    public function get_name()
    {
        return 'bt-accordion-hotspot';
    }

    public function get_title()
    {
        return __('Accordion Hotspot', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-image-hotspot';
    }

    public function get_categories()
    {
        return ['somnia'];
    }

    public function get_script_depends()
    {
        return ['elementor-widgets'];
    }

    protected function register_layout_section_controls()
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Hotspot', 'somnia'),
            ]
        );
        $this->add_control(
            'hotspot_image',
            [
                'label' => __('Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'label' => __('Image Size', 'somnia'),
                'show_label' => true,
                'default' => 'medium_large',
                'exclude' => ['custom'],
            ]
        );

        $repeater = new Repeater();
        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Enter title', 'somnia'),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'desc',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
                'placeholder' => __('Enter description', 'somnia'),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'hotspot_position_x',
            [
                'label' => __('X Position', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_control(
            'hotspot_position_y',
            [
                'label' => __('Y Position', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.bt-hotspot-point' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'hotspot_items',
            [
                'label' => __('Hotspot', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => esc_html__( 'Accordion Title #1', 'somnia' ),
                        'desc'  => esc_html__( 'This is a short description for accordion item #1.', 'somnia' ),
                        'hotspot_position_x' => [
                            'unit' => '%',
                            'size' => 10,
                        ],
                        'hotspot_position_y' => [
                            'unit' => '%',
                            'size' => 10,
                        ]
                    ],
                    [
                        'title' => esc_html__( 'Accordion Title #2', 'somnia' ),
                        'desc'  => esc_html__( 'This is a short description for accordion item #2.', 'somnia' ),
                        'hotspot_position_x' => [
                            'unit' => '%',
                            'size' => 70,
                        ],
                        'hotspot_position_y' => [
                            'unit' => '%',
                            'size' => 30,
                        ]
                    ],
                    [
                        'title' => esc_html__( 'Accordion Title #3', 'somnia' ),
                        'desc'  => esc_html__( 'This is a short description for accordion item #3.', 'somnia' ),
                        'hotspot_position_x' => [
                            'unit' => '%',
                            'size' => 50,
                        ],
                        'hotspot_position_y' => [
                            'unit' => '%',
                            'size' => 90,
                        ]
                    ],

                ],
            ]
        );
        $this->end_controls_section();
    }
    protected function register_style_content_section_controls()
    {
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Accordion', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
			'title_heading',
			[
				'label' => __('Title', 'somnia'),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-hotspot__item--title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bt-accordion-hotspot__item .bt-accordion-toggle rect' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Title Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-accordion-hotspot__item--title',
            ]
        );

        $this->add_control(
			'desc_heading',
			[
				'label' => __('Description', 'somnia'),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->add_control(
            'desc_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-hotspot__item--desc' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typography',
                'label' => __('Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-accordion-hotspot__item--desc',
            ]
        );

        $this->add_control(
			'point_heading',
			[
				'label' => __('Point', 'somnia'),
				'type' => Controls_Manager::HEADING,
                'separator' => 'before',
			]
		);

        $this->start_controls_tabs('dots_colors_tabs');

		$this->start_controls_tab(
			'points_colors_normal',
			[
				'label' => __('Normal', 'somnia'),
			]
		);

		$this->add_control(
            'points_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-point .bt-hotspot-marker' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'points_bg_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-point .bt-hotspot-marker' => 'background-color: {{VALUE}};',
                ],
            ]
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'points_colors_hover',
			[
				'label' => __('Hover', 'somnia'),
			]
		);

		$this->add_control(
            'points_color_hover',
            [
                'label' => __('Color Hover', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-point:hover .bt-hotspot-marker,
                    {{WRAPPER}} .bt-hotspot-point.__is_active .bt-hotspot-marker' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'points_bg_color_hover',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-hotspot-point:hover .bt-hotspot-marker,
                    {{WRAPPER}} .bt-hotspot-point.__is_active .bt-hotspot-marker' => 'background-color: {{VALUE}};',
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
        $this->register_style_content_section_controls();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (empty($settings['hotspot_image']['url'])) {
            return;
        }
        
        ?>
        <div class="bt-elwg-accordion-hotspot">
            <div class="bt-accordion-hotspot">
                <div class="bt-accordion-hotspot__list">
                    <div class="bt-accordion-hotspot__list">
                        <?php if (!empty($settings['hotspot_items'])) : ?>
                            <?php foreach ($settings['hotspot_items'] as $index => $item) :
                                if ($item['title'] && $item['desc']) :
                                    $count = $index + 1;
                                ?>
                                    <div class="bt-accordion-hotspot__item" data-index="<?php echo esc_attr($count); ?>">
                                        <h3 class="bt-accordion-hotspot__item--title">
                                            <?php echo wp_kses_post( $count .' - '. $item['title'] ); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="bt-accordion-toggle" width="18" height="18" viewBox="0 0 160 160">
                                                <rect class="vertical-line" x="70" width="15" height="160" rx="7" ry="7" />
                                                <rect class="horizontal-line" y="70" width="160" height="15" rx="7" ry="7" />
                                            </svg>
                                        </h3>
                                        <div class="bt-accordion-hotspot__item--desc"><?php echo wp_kses_post( $item['desc'] ); ?></div>
                                    </div>
                                <?php endif;
                            endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bt-accordion-hotspot__image">
                    <div class="bt-hotspot-image" style="position: relative;">
                        <?php
                        if ($settings['hotspot_image']['id']) {
                            echo wp_get_attachment_image($settings['hotspot_image']['id'], $settings['thumbnail_size']);
                        } else {
                            echo '<img src="' . esc_url($settings['hotspot_image']['url']) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '">';
                        }
                        ?>
                        <?php if (!empty($settings['hotspot_items'])) : ?>
                            <div class="bt-hotspot-points">
                                <?php foreach ($settings['hotspot_items'] as $index => $item) :
                                    if ($item['title'] && $item['desc']) :
                                         $count = $index + 1;
                                    ?>
                                        <div class="bt-hotspot-point elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>" data-index="<?php echo esc_attr($count); ?>">
                                            <div class="bt-hotspot-marker"> 
                                                <?php
                                                    echo esc_html($count); 
                                                ?>
                                            </div>
                                        </div>
                                <?php endif;
                                endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
