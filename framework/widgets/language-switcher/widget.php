<?php

namespace SomniaElementorWidgets\Widgets\LanguageSwitcher;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_LanguageSwitcher extends Widget_Base
{

    public function get_name()
    {
        return 'bt-language-switcher';
    }

    public function get_title()
    {
        return __('Language Switcher', 'somnia');
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
            'section_languages',
            [
                'label' => __('Languages', 'somnia'),
            ]
        );

        $this->add_control(
            'language_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('This widget displays a static language switcher dropdown.', 'somnia'),
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
        <div class="bt-elwg-language-switcher--default">
            <div class="language-switcher bt-elwg-switcher js-switcher-dropdown">
                <ul class="bt-dropdown">
                    <li class="bt-has-dropdown">
                        <a href="#" class="bt-current-item">
                            <span class="bt-current-item-text">
                                <span class="language-flag">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                        <circle cx="256" cy="256" r="256" fill="#f0f0f0" opacity="1" data-original="#f0f0f0" class=""></circle>
                                        <path d="M244.87 256H512c0-23.106-3.08-45.49-8.819-66.783H244.87zM244.87 122.435h229.556a257.35 257.35 0 0 0-59.07-66.783H244.87zM256 512c60.249 0 115.626-20.824 159.356-55.652H96.644C140.374 491.176 195.751 512 256 512zM37.574 389.565h436.852a254.474 254.474 0 0 0 28.755-66.783H8.819a254.474 254.474 0 0 0 28.755 66.783z" fill="#d80027" opacity="1" data-original="#d80027" class=""></path>
                                        <path fill="#0052b4" d="M118.584 39.978h23.329l-21.7 15.765 8.289 25.509-21.699-15.765-21.699 15.765 7.16-22.037a257.407 257.407 0 0 0-49.652 55.337h7.475l-13.813 10.035a255.58 255.58 0 0 0-6.194 10.938l6.596 20.301-12.306-8.941a253.567 253.567 0 0 0-8.372 19.873l7.267 22.368h26.822l-21.7 15.765 8.289 25.509-21.699-15.765-12.998 9.444A258.468 258.468 0 0 0 0 256h256V0c-50.572 0-97.715 14.67-137.416 39.978zm9.918 190.422-21.699-15.765L85.104 230.4l8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765zm-8.289-100.083 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822zM220.328 230.4l-21.699-15.765L176.93 230.4l8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765zm-8.289-100.083 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822zm0-74.574 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822z" opacity="1" data-original="#0052b4" class=""></path>
                                    </svg>
                                </span>
                                <span><?php esc_html_e('English', 'somnia') ?></span>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M13.5306 6.52927L8.5306 11.5293C8.46092 11.5992 8.37813 11.6547 8.28696 11.6925C8.1958 11.7304 8.09806 11.7499 7.99935 11.7499C7.90064 11.7499 7.8029 11.7304 7.71173 11.6925C7.62057 11.6547 7.53778 11.5992 7.4681 11.5293L2.4681 6.52927C2.3272 6.38837 2.24805 6.19728 2.24805 5.99802C2.24805 5.79876 2.3272 5.60767 2.4681 5.46677C2.60899 5.32587 2.80009 5.24672 2.99935 5.24672C3.19861 5.24672 3.3897 5.32587 3.5306 5.46677L7.99997 9.93614L12.4693 5.46615C12.6102 5.32525 12.8013 5.24609 13.0006 5.24609C13.1999 5.24609 13.391 5.32525 13.5318 5.46615C13.6727 5.60704 13.7519 5.79814 13.7519 5.9974C13.7519 6.19665 13.6727 6.38775 13.5318 6.52865L13.5306 6.52927Z" fill="currentColor" />
                            </svg>
                        </a>
                        <ul class="sub-dropdown bt-dropdown-position-<?php echo isset($settings['dropdown_position']) ? esc_attr($settings['dropdown_position']) : 'bottom'; ?>">
                            <li><a href="#" class="bt-item active">
                                <span class="language-flag">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                        <circle cx="256" cy="256" r="256" fill="#f0f0f0" opacity="1" data-original="#f0f0f0" class=""></circle>
                                        <path d="M244.87 256H512c0-23.106-3.08-45.49-8.819-66.783H244.87zM244.87 122.435h229.556a257.35 257.35 0 0 0-59.07-66.783H244.87zM256 512c60.249 0 115.626-20.824 159.356-55.652H96.644C140.374 491.176 195.751 512 256 512zM37.574 389.565h436.852a254.474 254.474 0 0 0 28.755-66.783H8.819a254.474 254.474 0 0 0 28.755 66.783z" fill="#d80027" opacity="1" data-original="#d80027" class=""></path>
                                        <path fill="#0052b4" d="M118.584 39.978h23.329l-21.7 15.765 8.289 25.509-21.699-15.765-21.699 15.765 7.16-22.037a257.407 257.407 0 0 0-49.652 55.337h7.475l-13.813 10.035a255.58 255.58 0 0 0-6.194 10.938l6.596 20.301-12.306-8.941a253.567 253.567 0 0 0-8.372 19.873l7.267 22.368h26.822l-21.7 15.765 8.289 25.509-21.699-15.765-12.998 9.444A258.468 258.468 0 0 0 0 256h256V0c-50.572 0-97.715 14.67-137.416 39.978zm9.918 190.422-21.699-15.765L85.104 230.4l8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765zm-8.289-100.083 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822zM220.328 230.4l-21.699-15.765L176.93 230.4l8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822l-21.7 15.765zm-8.289-100.083 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822zm0-74.574 8.289 25.509-21.699-15.765-21.699 15.765 8.289-25.509-21.7-15.765h26.822l8.288-25.509 8.288 25.509h26.822z" opacity="1" data-original="#0052b4" class=""></path>
                                    </svg>
                                </span>
                                <span><?php esc_html_e('English', 'somnia') ?></span>
                            </a></li>
                            <li><a href="#" class="bt-item">
                                <span class="language-flag">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                        <path fill="#ffda44" d="M0 256c0 31.314 5.633 61.31 15.923 89.043L256 367.304l240.077-22.261C506.367 317.31 512 287.314 512 256s-5.633-61.31-15.923-89.043L256 144.696 15.923 166.957C5.633 194.69 0 224.686 0 256z" opacity="1" data-original="#ffda44" class=""></path>
                                        <path d="M496.077 166.957C459.906 69.473 366.071 0 256 0S52.094 69.473 15.923 166.957zM15.923 345.043C52.094 442.527 145.929 512 256 512s203.906-69.473 240.077-166.957z" fill="#d80027" opacity="1" data-original="#d80027"></path>
                                    </svg>
                                </span>
                                <span><?php esc_html_e('Español', 'somnia') ?></span>
                            </a></li>
                            <li><a href="#" class="bt-item">
                                <span class="language-flag">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                        <circle cx="256" cy="256" r="256" fill="#f0f0f0" opacity="1" data-original="#f0f0f0" class=""></circle>
                                        <path fill="#d80027" d="M512 256c0-110.071-69.472-203.906-166.957-240.077v480.155C442.528 459.906 512 366.071 512 256z" opacity="1" data-original="#d80027"></path>
                                        <path fill="#0052b4" d="M0 256c0 110.071 69.473 203.906 166.957 240.077V15.923C69.473 52.094 0 145.929 0 256z" opacity="1" data-original="#0052b4" class=""></path>
                                    </svg>
                                </span>
                                <span><?php esc_html_e('Français', 'somnia') ?></span>
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
