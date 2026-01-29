<?php

namespace SomniaElementorWidgets\Widgets\CurrencySwitcher;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_BBorder;
use Elementor\Group_Control_Box_Shadow;

class Widget_CurrencySwitcher extends Widget_Base
{

    public function get_name()
    {
        return 'bt-currency-switcher';
    }

    public function get_title()
    {
        return __('Currency Switcher', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-select';
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
            'section_currencies',
            [
                'label' => __('Currencies', 'somnia'),
            ]
        );

        $this->add_control(
            'currency_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('This widget displays a static currency switcher dropdown.', 'somnia'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );
        $this->add_control(
            'dropdown_position',
            [
                'label' => __('Dropdown Position', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'bottom',
                'options' => [
                    'top' => __('Top', 'somnia'),
                    'bottom' => __('Bottom', 'somnia'), 
                ],
            ]
        );

        $this->end_controls_section();
    }
    protected function register_style_section_controls()
    {
        $this->start_controls_section(
            'section_style_current_item',
            [
                'label' => __('Current Item', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'current_item_text_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-current-item' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'current_item_background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-current-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'current_item_padding',
            [
                'label' => __('Padding Item', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .bt-current-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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
?>
        <div class="bt-elwg-currency-switcher--default">
            <div class="currency-switcher bt-elwg-switcher js-switcher-dropdown">
                <ul class="bt-dropdown">
                    <li class="bt-has-dropdown">
                        <a href="#" class="bt-current-item"><span class="bt-current-item-text">$ Dollar (US)</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M13.5306 6.52927L8.5306 11.5293C8.46092 11.5992 8.37813 11.6547 8.28696 11.6925C8.1958 11.7304 8.09806 11.7499 7.99935 11.7499C7.90064 11.7499 7.8029 11.7304 7.71173 11.6925C7.62057 11.6547 7.53778 11.5992 7.4681 11.5293L2.4681 6.52927C2.3272 6.38837 2.24805 6.19728 2.24805 5.99802C2.24805 5.79876 2.3272 5.60767 2.4681 5.46677C2.60899 5.32587 2.80009 5.24672 2.99935 5.24672C3.19861 5.24672 3.3897 5.32587 3.5306 5.46677L7.99997 9.93614L12.4693 5.46615C12.6102 5.32525 12.8013 5.24609 13.0006 5.24609C13.1999 5.24609 13.391 5.32525 13.5318 5.46615C13.6727 5.60704 13.7519 5.79814 13.7519 5.9974C13.7519 6.19665 13.6727 6.38775 13.5318 6.52865L13.5306 6.52927Z" fill="currentColor" />
                            </svg></a>
                        <ul class="sub-dropdown bt-dropdown-position-<?php echo isset($settings['dropdown_position']) ? esc_attr($settings['dropdown_position']) : 'bottom'; ?>">
                            <li><a href="#" class="bt-item active">$ Dollar (US)</a></li>
                            <li><a href="#" class="bt-item">£ Pound (UK)</a></li>
                            <li><a href="#" class="bt-item">€ Euro (EUR)</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
