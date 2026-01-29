<?php

namespace SomniaElementorWidgets\Widgets\MiniCompare;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_MiniCompare extends Widget_Base
{

    public function get_name()
    {
        return 'bt-mini-compare';
    }

    public function get_title()
    {
        return __('Mini Compare', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-sync';
    }

    public function get_categories()
    {
        return ['somnia'];
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
            'compare_mini_icon',
            [
                'label' => esc_html__('Icon Compare', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'media_types' => ['svg'],
            ]
        );
        $this->add_control(
            'compare_text',
            [
                'label' => esc_html__('Text', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => esc_html__('Enter text to display below icon', 'somnia'),
            ]
        );

        $this->end_controls_section();
    }


    protected function register_style_content_section_controls()
    {

        $this->start_controls_section(
            'section_style_content_compare',
            [
                'label' => esc_html__('Content Compare', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_compare',
            [
                'label' => __('Icon', 'somnia'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->add_responsive_control(
            'icon_compare_size',
            [
                'label' => __('Icon size', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-mini-compare--default .bt-mini-compare a svg ' => 'width: {{SIZE}}px;height:{{SIZE}}px;',
                ],
            ]
        );
        $this->add_control(
            'icon_compare_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-mini-compare--default .bt-mini-compare a svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'icon_compare_background',
            [
                'label' => __('Background', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-mini-compare--default .bt-mini-compare a' => 'background: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'number_compare',
            [
                'label' => __('Number Compare', 'somnia'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->add_control(
            'number_compare_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-mini-compare--default .bt-mini-compare span' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'number_compare_background',
            [
                'label' => __('Background', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-mini-compare--default .bt-mini-compare span' => 'background: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'number_compare_typography',
                'label' => __('Typography', 'somnia'),
                'default' => '',
                'selector' => '{{WRAPPER}} .bt-elwg-mini-compare--default .bt-mini-compare span',
            ]
        );
        $this->add_control(
            'text_compare',
            [
                'label' => __('Text', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'text_compare_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-mini-compare--default .bt-mini-compare .bt-text-label' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_compare_typography',
                'label' => __('Typography', 'somnia'),
                'default' => '',
                'selector' => '{{WRAPPER}} .bt-elwg-mini-compare--default .bt-mini-compare .bt-text-label',
            ]
        );
        $this->add_responsive_control(
            'text_compare_spacing',
            [
                'label' => __('Spacing', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-mini-compare--default .bt-mini-compare' => 'gap: {{SIZE}}px;',
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
        if (!class_exists('WooCommerce')) {
            return;
        }

        $settings = $this->get_settings_for_display();

        // Check if compare should be shown
        $archive_shop = function_exists('get_field') ? get_field('archive_shop', 'options') : array();
        $show_compare = isset($archive_shop['show_compare']) ? $archive_shop['show_compare'] : true;

        if (!$show_compare) {
            return;
        }

        $icon_compare = !empty($settings['compare_mini_icon']['url']) ? $settings['compare_mini_icon']['url'] : '';

        // Get compare page URL from ACF options
        $compare_url = home_url('/products-compare/'); // Default fallback URL

        if (function_exists('get_field')) {
            $compare = get_field('compare', 'options');
            // Use custom compare page URL if set in options
            if ($compare && isset($compare['page_compare']) && $compare['page_compare'] != '') {
                $compare_url = get_permalink($compare['page_compare']);
            }
        }

        // Get compare count from localStorage (will be updated by JavaScript)
        $compare_count = 0;
?>
        <div class="bt-elwg-mini-compare--default">
            <div class="bt-mini-compare">
                <a class="bt-toggle-btn" href="<?php echo esc_url($compare_url); ?>">
                    <?php if (!empty($icon_compare) && 'svg' === pathinfo($icon_compare, PATHINFO_EXTENSION)) {
                        $response = wp_safe_remote_get($icon_compare, array(
                            'timeout' => 20,
                            'headers' => array(
                                'User-Agent' => 'Mozilla/5.0 (compatible; WordPress)',
                            ),
                        ));
                        if (is_wp_error($response)) {
                            return;
                        }
                        echo wp_remote_retrieve_body($response);
                    } else { ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8.75001 11.8737C8.58425 11.8737 8.42528 11.9396 8.30807 12.0568C8.19086 12.174 8.12501 12.333 8.12501 12.4987V14.7401L5.18282 11.7956C5.06621 11.6789 5.0005 11.5208 5.00001 11.3558V7.42061C5.58917 7.26849 6.10263 6.90672 6.44413 6.40311C6.78564 5.8995 6.93175 5.28862 6.85508 4.68499C6.77841 4.08136 6.48422 3.52641 6.02765 3.12416C5.57109 2.72192 4.98349 2.5 4.37501 2.5C3.76652 2.5 3.17893 2.72192 2.72236 3.12416C2.26579 3.52641 1.9716 4.08136 1.89493 4.68499C1.81826 5.28862 1.96437 5.8995 2.30588 6.40311C2.64739 6.90672 3.16084 7.26849 3.75001 7.42061V11.3566C3.74876 11.6031 3.79645 11.8474 3.89033 12.0754C3.9842 12.3034 4.12239 12.5105 4.29688 12.6847L7.24141 15.6237H5.00001C4.83425 15.6237 4.67528 15.6896 4.55806 15.8068C4.44085 15.924 4.37501 16.083 4.37501 16.2487C4.37501 16.4145 4.44085 16.5735 4.55806 16.6907C4.67528 16.8079 4.83425 16.8737 5.00001 16.8737H8.75001C8.91577 16.8737 9.07474 16.8079 9.19195 16.6907C9.30916 16.5735 9.37501 16.4145 9.37501 16.2487V12.4987C9.37501 12.333 9.30916 12.174 9.19195 12.0568C9.07474 11.9396 8.91577 11.8737 8.75001 11.8737ZM3.12501 4.99874C3.12501 4.75151 3.19832 4.50984 3.33567 4.30428C3.47302 4.09872 3.66824 3.9385 3.89665 3.84389C4.12506 3.74928 4.37639 3.72453 4.61887 3.77276C4.86135 3.82099 5.08407 3.94004 5.25889 4.11486C5.43371 4.28967 5.55276 4.5124 5.60099 4.75488C5.64922 4.99735 5.62447 5.24869 5.52986 5.47709C5.43525 5.7055 5.27503 5.90072 5.06947 6.03808C4.86391 6.17543 4.62223 6.24874 4.37501 6.24874C4.04349 6.24874 3.72554 6.11704 3.49112 5.88262C3.2567 5.6482 3.12501 5.33026 3.12501 4.99874ZM16.25 12.5769V8.64171C16.2513 8.39516 16.2036 8.15081 16.1097 7.92283C16.0158 7.69485 15.8776 7.48777 15.7031 7.31358L12.7586 4.37374H15C15.1658 4.37374 15.3247 4.30789 15.442 4.19068C15.5592 4.07347 15.625 3.9145 15.625 3.74874C15.625 3.58298 15.5592 3.42401 15.442 3.3068C15.3247 3.18959 15.1658 3.12374 15 3.12374H11.25C11.0842 3.12374 10.9253 3.18959 10.8081 3.3068C10.6909 3.42401 10.625 3.58298 10.625 3.74874V7.49874C10.625 7.6645 10.6909 7.82347 10.8081 7.94068C10.9253 8.05789 11.0842 8.12374 11.25 8.12374C11.4158 8.12374 11.5747 8.05789 11.6919 7.94068C11.8092 7.82347 11.875 7.6645 11.875 7.49874V5.25733L14.8172 8.20186C14.8752 8.25995 14.9212 8.3289 14.9526 8.40477C14.984 8.48064 15.0001 8.56195 15 8.64405V12.5769C14.4108 12.729 13.8974 13.0908 13.5559 13.5944C13.2144 14.098 13.0683 14.7089 13.1449 15.3125C13.2216 15.9161 13.5158 16.4711 13.9724 16.8733C14.4289 17.2756 15.0165 17.4975 15.625 17.4975C16.2335 17.4975 16.8211 17.2756 17.2777 16.8733C17.7342 16.4711 18.0284 15.9161 18.1051 15.3125C18.1818 14.7089 18.0356 14.098 17.6941 13.5944C17.3526 13.0908 16.8392 12.729 16.25 12.5769ZM15.625 16.2487C15.3778 16.2487 15.1361 16.1754 14.9305 16.0381C14.725 15.9007 14.5648 15.7055 14.4702 15.4771C14.3755 15.2487 14.3508 14.9974 14.399 14.7549C14.4473 14.5124 14.5663 14.2897 14.7411 14.1149C14.9159 13.94 15.1387 13.821 15.3811 13.7728C15.6236 13.7245 15.875 13.7493 16.1034 13.8439C16.3318 13.9385 16.527 14.0987 16.6643 14.3043C16.8017 14.5098 16.875 14.7515 16.875 14.9987C16.875 15.3303 16.7433 15.6482 16.5089 15.8826C16.2745 16.117 15.9565 16.2487 15.625 16.2487Z"></path>
                        </svg>
                    <?php } ?>
                    <span class="compare_total"><?php echo esc_html($compare_count); ?></span></a>
                <?php if (!empty($settings['compare_text'])) {
                    echo '<a href="' . esc_url($compare_url) . '" class="bt-text-label">' . esc_html($settings['compare_text']) . '</a>';
                } ?>
            </div>
        </div>
        <script>
            (function() {
                // Update compare count from localStorage
                function updateCompareCount() {
                    var compare_local = window.localStorage.getItem('productcomparelocal');
                    var count = 0;
                    if (compare_local && compare_local !== '') {
                        var compare_arr = compare_local.split(',');
                        count = compare_arr.filter(function(item) {
                            return item !== '';
                        }).length;
                    }
                    jQuery('.bt-mini-compare .compare_total').text(count);
                }

                // Update on page load
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', updateCompareCount);
                } else {
                    updateCompareCount();
                }

                // Listen for storage changes
                window.addEventListener('storage', updateCompareCount);

                // Also update when compare is modified (for same-tab updates)
                if (typeof jQuery !== 'undefined') {
                    jQuery(document).on('somnia_compare_updated', updateCompareCount);
                }
            })();
        </script>
<?php
    }

    protected function content_template() {}
}
