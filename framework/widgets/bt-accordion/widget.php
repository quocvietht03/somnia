<?php

namespace SomniaElementorWidgets\Widgets\BtAccordion;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;

class Widget_BtAccordion extends Widget_Base
{

    public function get_name()
    {
        return 'bt-accordion';
    }

    public function get_title()
    {
        return __('Accordion', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-accordion';
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
                'label' => __('Content', 'somnia'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'accordion_icon',
            [
                'label' => __('Icon', 'somnia'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'accordion_title',
            [
                'label' => __('Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Accordion Title', 'somnia'),
            ]
        );

        $repeater->add_control(
            'accordion_description',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
                'default' => __('Accordion description content goes here.', 'somnia'),
            ]
        );

        $this->add_control(
            'accordion_list',
            [
                'label' => __('Accordion Items', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'accordion_icon' => [
                            'value' => 'fas fa-question-circle',
                            'library' => 'fa-solid',
                        ],
                        'accordion_title' => __('What is BT Accordion?', 'somnia'),
                        'accordion_description' => __('BT Accordion is a flexible and customizable accordion widget that allows you to create collapsible content sections with icons and descriptions.', 'somnia')
                    ],
                    [
                        'accordion_icon' => [
                            'value' => 'fas fa-cog',
                            'library' => 'fa-solid',
                        ],
                        'accordion_title' => __('How to customize?', 'somnia'),
                        'accordion_description' => __('You can easily customize the accordion by adding your own icons, titles, and descriptions. Use the style controls to match your design.', 'somnia')
                    ],
                    [
                        'accordion_icon' => [
                            'value' => 'fas fa-headset',
                            'library' => 'fa-solid',
                        ],
                        'accordion_title' => __('Need support?', 'somnia'),
                        'accordion_description' => __('Our dedicated support team is here to help you with any questions or issues you might encounter.', 'somnia')
                    ],
                ],
                'title_field' => '<# if (accordion_icon.value) { #><i class="{{ accordion_icon.value }}"></i><# } #> {{{ accordion_title }}}',
            ]
        );

        $this->add_control(
            'open_first_item',
            [
                'label' => __('Open First Item', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('Open the first accordion item by default', 'somnia'),
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_section_controls()
    {
        // Item Styles
        $this->start_controls_section(
            'section_style_item',
            [
                'label' => __('Item', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'selector' => '{{WRAPPER}} .accordion-item-inner',
            ]
        );

        $this->add_control(
            'item_padding',
            [
                'label' => __('Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .accordion-item-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'items_spacing',
            [
                'label' => __('Items Spacing', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .accordion-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'description' => __('Set spacing between accordion items', 'somnia'),
            ]
        );

        $this->end_controls_section();

        // Title Styles
        $this->start_controls_section(
            'section_style_title',
            [
                'label' => __('Title', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-title h3' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-accordion-title h3',
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bt-accordion-icon svg' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .bt-accordion-icon svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => __('Icon Size', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 32,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bt-accordion-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Description Styles
        $this->start_controls_section(
            'section_style_description',
            [
                'label' => __('Description', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .bt-accordion-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label' => __('Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-accordion-content',
            ]
        );

        $this->end_controls_section();
    }

    protected function register_controls()
    {
        $this->register_layout_section_controls();
        $this->register_style_section_controls();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (empty($settings['accordion_list'])) {
            return;
        }

?>
        <div class="bt-elwg-accordion--default" data-open-first="<?php echo esc_attr($settings['open_first_item']); ?>">
            <div class="bt-elwg-accordion-inner">
                <?php foreach ($settings['accordion_list'] as $index => $item): 
                        $class_active = ($index === 0 && $settings['open_first_item'] === 'yes') ? ' active' : '';
                    ?>
                    <div class="accordion-item">
                        <div class="accordion-item-inner">
                            <div class="bt-accordion-title<?php echo esc_attr($class_active); ?>">
                                <div class="bt-accordion-title-content">
                                    <?php if (!empty($item['accordion_icon']['value'])): ?>
                                        <div class="bt-accordion-icon">
                                            <?php Icons_Manager::render_icon($item['accordion_icon'], ['aria-hidden' => 'true']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['accordion_title'])): ?>
                                        <h3><?php echo esc_html($item['accordion_title']) ?></h3>
                                    <?php endif; ?>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="accordion-toggle" width="18" height="18" viewBox="0 0 160 160">
                                    <rect class="vertical-line" x="70" width="15" height="160" rx="7" ry="7" />
                                    <rect class="horizontal-line" y="70" width="160" height="15" rx="7" ry="7" />
                                </svg>
                            </div>
                            <?php if (!empty($item['accordion_description'])): 
                                    $style_active = ($index === 0 && $settings['open_first_item'] === 'yes') ? 'display: block;' : '';
                                ?>
                                <div class="bt-accordion-content" style="<?php echo esc_attr($style_active); ?>">
                                    <?php echo esc_html($item['accordion_description']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
<?php }

    protected function content_template() {}
}
