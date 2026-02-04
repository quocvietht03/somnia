<?php

namespace SomniaElementorWidgets\Widgets\SiteNotification;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_BBorder;
use Elementor\Group_Control_Box_Shadow;

class Widget_SiteNotification extends Widget_Base
{

    public function get_name()
    {
        return 'bt-site-notification';
    }

    public function get_title()
    {
        return __('Site Notification', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-posts-ticker';
    }

    public function get_categories()
    {
        return ['somnia'];
    }

    public function get_script_depends()
    {
        return ['swiper-slider', 'elementor-widgets'];
    }
    protected function register_layout_section_controls()
    {
        $this->start_controls_section(
            'section_slider',
            [
                'label' => __('Notification', 'somnia'),
            ]
        );
        $this->add_control(
            'show_icon',
            [
                'label' => __('Show Icon', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'slider_autoplay',
            [
                'label' => __('Notification Autoplay', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'no',
            ]
        );
        $this->add_control(
            'slider_autoplay_delay',
            [
                'label' => __('Notification Delay', 'somnia'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
                'min' => 1000,
                'max' => 10000,
                'step' => 500,
                'description' => __('Delay between notification in milliseconds', 'somnia'),
                'condition' => [
                    'slider_autoplay' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'slider_navigation',
            [
                'label' => __('Navigation', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'yes',
            ]
        );
        $this->end_controls_section();
    }

    protected function register_style_section_controls()
    {
        // Content Style Section
        $this->start_controls_section(
            'section_content_style',
            [
                'label' => __('Content Style', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => __('Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-site-notification--text',
            ]
        );

        $this->add_responsive_control(
            'text_align',
            [
                'label' => __('Alignment', 'somnia'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'somnia'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'somnia'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'somnia'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .bt-site-notification--item' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .bt-site-notification--text' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-site-notification--text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'link_color',
            [
                'label' => __('Link Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-site-notification--text a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'link_hover_color',
            [
                'label' => __('Link Hover Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-site-notification--text a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'nav_color',
            [
                'label' => __('Navigation Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-site-notification--nav svg' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'slider_navigation' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'somnia'),
                'description' => __('Set color for SVG icons', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-site-notification--item svg' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .bt-site-notification--item svg path' => 'fill: {{VALUE}};',
                ],
                'condition' => [
                    'show_icon' => 'yes',
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
        $slider_settings = [
            'autoplay' => $settings['slider_autoplay'] == 'yes' ? true : false,
            'autoplayDelay' => $settings['slider_autoplay_delay'],
            'speed' => 1000,
        ];
        if (function_exists('get_field')) {
            $site_infor = get_field('site_information', 'options') ?: '';
        } else {
            $site_infor = '';
        }
        if (!empty($site_infor) && isset($site_infor) && isset($site_infor['site_notification']) && is_array($site_infor['site_notification'])) { 
            $is_enable = $settings['slider_navigation'] == 'yes' ? 'bt-navigation-enabled' : '';
            ?>
            <div class="bt-elwg-site-notification--default " data-slider-settings='<?php echo json_encode($slider_settings); ?>'>
                <div class="bt-site-notification <?php echo esc_attr($is_enable); ?>">
                    <div class="swiper bt-site-notification--content js-notification-content">
                        <div class="swiper-wrapper">
                            <?php foreach ($site_infor['site_notification'] as $item) : ?>
                                <div class="swiper-slide">
                                    <div class="bt-site-notification--item">
                                        <?php if ($settings['show_icon'] == 'yes') :
                                            if (!empty($item['icon']['id'])) {
                                                $image_id = $item['icon']['id'];
                                                $image_url = $item['icon']['url'];
                                                $is_svg = false;

                                                // Check if the image is SVG
                                                if ($image_url && pathinfo($image_url, PATHINFO_EXTENSION) === 'svg') {
                                                    $is_svg = true;
                                                }

                                                if ($is_svg) {
                                                    $response = wp_safe_remote_get( $image_url, array(
                                                        'timeout' => 20,
                                                        'headers' => array(
                                                            'User-Agent' => 'Mozilla/5.0 (compatible; WordPress)',
                                                        ),
                                                    ) );
                                                    if ( is_wp_error( $response ) ) {
                                                        return;
                                                    }
                                                    echo '<div class="bt-svg">' . wp_remote_retrieve_body( $response ) . '</div>';
                                                } else {
                                                    echo wp_get_attachment_image($image_id, 'thumbnail');
                                                }
                                            } else {
                                                echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting icon', 'somnia') . '">';
                                            }
                                        endif; ?>
                                        <?php if (!empty($item['text_notification'])) :
                                            echo  '<div class="bt-site-notification--text">' . $item['text_notification'] . '</div>';
                                        endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if ($settings['slider_navigation'] == 'yes') : ?>
                        <div class="bt-site-notification--nav swiper-button-wrapper">
                            <div class="bt-site-notification--prev"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M12.9417 15.8079C12.9998 15.866 13.0458 15.9349 13.0773 16.0108C13.1087 16.0867 13.1249 16.168 13.1249 16.2501C13.1249 16.3322 13.1087 16.4135 13.0773 16.4894C13.0458 16.5653 12.9998 16.6342 12.9417 16.6923C12.8836 16.7504 12.8147 16.7964 12.7388 16.8278C12.663 16.8593 12.5816 16.8755 12.4995 16.8755C12.4174 16.8755 12.3361 16.8593 12.2602 16.8278C12.1843 16.7964 12.1154 16.7504 12.0573 16.6923L5.80733 10.4423C5.74922 10.3842 5.70312 10.3153 5.67167 10.2394C5.64021 10.1636 5.62402 10.0822 5.62402 10.0001C5.62402 9.91797 5.64021 9.83664 5.67167 9.76077C5.70312 9.68489 5.74922 9.61596 5.80733 9.55792L12.0573 3.30792C12.1746 3.19064 12.3337 3.12476 12.4995 3.12476C12.6654 3.12476 12.8244 3.19064 12.9417 3.30792C13.059 3.42519 13.1249 3.58425 13.1249 3.7501C13.1249 3.91596 13.059 4.07502 12.9417 4.19229L7.13311 10.0001L12.9417 15.8079Z" fill="currentColor" />
                                </svg></div>
                            <div class="bt-site-notification--next"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M14.1925 10.4423L7.94254 16.6923C7.88447 16.7504 7.81553 16.7964 7.73966 16.8278C7.66379 16.8593 7.58247 16.8755 7.50035 16.8755C7.41823 16.8755 7.33691 16.8593 7.26104 16.8278C7.18517 16.7964 7.11623 16.7504 7.05816 16.6923C7.00009 16.6342 6.95403 16.5653 6.9226 16.4894C6.89117 16.4135 6.875 16.3322 6.875 16.2501C6.875 16.168 6.89117 16.0867 6.9226 16.0108C6.95403 15.9349 7.00009 15.866 7.05816 15.8079L12.8668 10.0001L7.05816 4.19229C6.94088 4.07502 6.875 3.91596 6.875 3.7501C6.875 3.58425 6.94088 3.42519 7.05816 3.30792C7.17544 3.19064 7.3345 3.12476 7.50035 3.12476C7.6662 3.12476 7.82526 3.19064 7.94254 3.30792L14.1925 9.55792C14.2506 9.61596 14.2967 9.68489 14.3282 9.76077C14.3597 9.83664 14.3758 9.91797 14.3758 10.0001C14.3758 10.0822 14.3597 10.1636 14.3282 10.2394C14.2967 10.3153 14.2506 10.3842 14.1925 10.4423Z" fill="currentColor" />
                                </svg></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
<?php
        }
    }

    protected function content_template() {}
}
