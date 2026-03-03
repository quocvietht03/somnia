<?php

namespace SomniaElementorWidgets\Widgets\LocationList;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;


class Widget_LocationList extends Widget_Base
{

    public function get_name()
    {
        return 'bt-location-list';
    }

    public function get_title()
    {
        return __('Location List', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-google-maps';
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

        $this->add_control(
            'search_title',
            [
                'label' => __('Search Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Find A Location', 'somnia'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'search_subtitle',
            [
                'label' => __('Search Subtitle', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Locate Your Nearest Store or Showroom', 'somnia'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'search_placeholder',
            [
                'label' => __('Search Placeholder', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('New York, NY, US', 'somnia'),
                'label_block' => true,
            ]
        );



        $repeater = new Repeater();


        $repeater->add_control(
            'location_heading',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $repeater->add_control(
            'location_title',
            [
                'label' => __('Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Our Locations', 'somnia'),
            ]
        );
        $repeater->add_control(
            'location_address',
            [
                'label' => __('Address', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '',
            ]
        );
        $repeater->add_control(
            'location_maps_zoom',
            [
                'label' => __('Maps zoom', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 12,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
            ]
        );
        $repeater->add_control(
            'location_phone',
            [
                'label' => __('Phone', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '',
            ]
        );
        $repeater->add_control(
            'location_button',
            [
                'label' => __('Button', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Get direction', 'somnia'),
            ]
        );
        $repeater->add_control(
            'location_button_link',
            [
                'label' => __('Button Link', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'location_status_heading',
            [
                'label' => __('Status', 'somnia'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $repeater->add_control(
            'location_status_open',
            [
                'label' => __('Open / Close', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Open', 'somnia'),
                'label_off' => __('Close', 'somnia'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $repeater->add_control(
            'location_closes_until',
            [
                'label' => __('Closing time note', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('(Close 6PM)', 'somnia'),
                'placeholder' => __('e.g. Closes until 5:00 pm', 'somnia'),
            ]
        );

        $this->add_control(
            'list',
            [
                'label' => __('Location list', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'location_title' => __('Sydney Double Bay', 'somnia'),
                        'location_address' => __('66 Broklyn New Golden Street USA', 'somnia'),
                        'location_button' => __('Enquire', 'somnia'),
                        'location_button_link' => '#',
                        'location_phone' => __('+1(800)123-4566', 'somnia'),
                        'location_status_open' => 'yes',
                        'location_closes_until' => __('(Close 6PM)', 'somnia'),
                        'location_maps_zoom' => '12',
                    ],
                    [
                        'location_title' => __('Newport Beach', 'somnia'),
                        'location_address' => __('854 Avocado Ave. Street USA', 'somnia'),
                        'location_button' => __('Enquire', 'somnia'),
                        'location_button_link' => '#',
                        'location_phone' => __('+1(800)123-4566', 'somnia'),
                        'location_status_open' => 'yes',
                        'location_closes_until' => __('(Close 6PM)', 'somnia'),
                        'location_maps_zoom' => '12',
                    ],
                    [
                        'location_title' => __('Waikoloa, USA', 'somnia'),
                        'location_address' => __('Waikoloa Beach Resort, Unit D-8 250 Waikoloa Beach', 'somnia'),
                        'location_button' => __('Enquire', 'somnia'),
                        'location_button_link' => '#',
                        'location_phone' => __('+1(800)123-4566', 'somnia'),
                        'location_status_open' => 'yes',
                        'location_closes_until' => __('(Close 6PM)', 'somnia'),
                        'location_maps_zoom' => '12',
                    ],
                ],
                'title_field' => '{{{ location_title }}}',
            ]
        );


        $this->end_controls_section();
    }

    protected function register_style_section_controls()
    {
        $this->start_controls_section(
            'section_style_item',
            [
                'label' => esc_html__('General', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'location_list_border',
            [
                'label' => __('Border Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--item' => 'border-bottom: {{SIZE}}{{UNIT}} solid #e9e9e9;',
                ],
            ]
        );
        $this->add_control(
            'location_list_color',
            [
                'label' => __('Border Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--item' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'location_list_maps_height',
            [
                'label' => __('Maps height', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 213,
                ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 800,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--maps iframe' => 'height: {{SIZE}}px;',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_heading',
            [
                'label' => esc_html__('Heading', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'location_title_style',
            [
                'label' => esc_html__('Title', 'somnia'),
                'type' => Controls_Manager::HEADING,
            ]
        );
        $this->add_control(
            'location_title_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-infor .bt-location-title-wrap h2' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'location_title_hover_color',
            [
                'label' => __('Color Hover', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-infor:hover .bt-location-title-wrap h2' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'location_title_typography',
                'label' => __('Typography', 'somnia'),
                'default' => '',
                'selector' => '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-infor .bt-location-title-wrap h2 ',
            ]
        );
        $this->add_control(
            'location_address_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-infor .bt-location-title-wrap span' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'location_address_typography',
                'label' => __('Typography', 'somnia'),
                'default' => '',
                'selector' => '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-infor .bt-location-title-wrap span',
            ]
        );
        $this->start_controls_tabs('button_style_tabs');

        $this->start_controls_tab(
            'style_normal',
            [
                'label' => __('Normal', 'somnia'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-button a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-button a' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_border_color',
            [
                'label' => __('border Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-button a' => 'border-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
            'style_hover',
            [
                'label' => __('Hover', 'somnia'),
            ]
        );

        $this->add_control(
            'button_text_color_hover',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-button a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color_hover',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-button a:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_border_color_hover',
            [
                'label' => __('border Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bt-elwg-location-list--default .bt-location-list--heading-button a:hover' => 'border-color: {{VALUE}};',
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
        $this->register_style_section_controls();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        if (empty($settings['list'])) {
            return;
        }

?>
        <div class="bt-elwg-location-list--finder">
            <div class="bt-location-finder">
                <!-- Search Section -->
                <div class="bt-location-finder--search">
                    <div class="bt-search-header">
                        <?php if (!empty($settings['search_title'])) : ?>
                            <h2 class="bt-search-title"><?php echo esc_html($settings['search_title']); ?></h2>
                        <?php endif; ?>
                        <?php if (!empty($settings['search_subtitle'])) : ?>
                            <p class="bt-search-subtitle"><?php echo esc_html($settings['search_subtitle']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="bt-search-form">
                        <div class="bt-search-input-wrapper">
                            <input type="text"
                                class="bt-search-input"
                                placeholder="<?php echo esc_attr($settings['search_placeholder']); ?>"
                                id="location-search">
                            <button type="button" class="bt-search-button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M21.5318 20.4667L16.8378 15.7735C18.1983 14.1401 18.8767 12.0451 18.7319 9.92422C18.5871 7.80336 17.6302 5.81996 16.0603 4.38663C14.4904 2.9533 12.4284 2.1804 10.3031 2.2287C8.17785 2.277 6.15303 3.14278 4.64986 4.64595C3.14669 6.14912 2.2809 8.17394 2.2326 10.2992C2.1843 12.4245 2.95721 14.4865 4.39054 16.0564C5.82387 17.6263 7.80726 18.5832 9.92813 18.728C12.049 18.8728 14.144 18.1944 15.7774 16.8338L20.4706 21.5279C20.5403 21.5976 20.623 21.6529 20.714 21.6906C20.8051 21.7283 20.9026 21.7477 21.0012 21.7477C21.0997 21.7477 21.1973 21.7283 21.2884 21.6906C21.3794 21.6529 21.4621 21.5976 21.5318 21.5279C21.6015 21.4582 21.6568 21.3755 21.6945 21.2845C21.7322 21.1934 21.7516 21.0958 21.7516 20.9973C21.7516 20.8987 21.7322 20.8012 21.6945 20.7101C21.6568 20.6191 21.6015 20.5363 21.5318 20.4667ZM3.75119 10.4973C3.75119 9.16226 4.14707 7.85722 4.88877 6.74719C5.63047 5.63715 6.68468 4.77199 7.91808 4.2611C9.15148 3.75021 10.5087 3.61653 11.8181 3.87698C13.1274 4.13743 14.3302 4.78031 15.2742 5.72431C16.2182 6.66832 16.861 7.87105 17.1215 9.18043C17.3819 10.4898 17.2483 11.847 16.7374 13.0804C16.2265 14.3138 15.3613 15.368 14.2513 16.1097C13.1413 16.8514 11.8362 17.2473 10.5012 17.2473C8.71159 17.2453 6.99585 16.5335 5.73041 15.2681C4.46497 14.0026 3.75318 12.2869 3.75119 10.4973Z" fill="#183F91" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Location List -->
                    <div class="bt-location-list">
                        <?php foreach ($settings['list'] as $index => $item) : ?>
                            <div class="bt-location-item <?php echo $index === 0 ? 'active' : ''; ?>"
                                data-location-index="<?php echo esc_attr($index); ?>"
                                data-address="<?php echo esc_attr($item['location_address']); ?>"
                                data-zoom="<?php echo esc_attr($item['location_maps_zoom']['size']); ?>">
                                <div class="bt-location-info">
                                    <?php if (!empty($item['location_title'])) : ?>
                                        <h3 class="bt-location-title"><?php echo esc_html($item['location_title']); ?></h3>
                                    <?php endif; ?>

                                    <?php if (!empty($item['location_address'])) : ?>
                                        <p class="bt-location-address"><?php echo esc_html($item['location_address']); ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($item['location_phone'])) : ?>
                                        <a class="bt-location-phone" href="tel:<?php echo esc_attr($item['location_phone']); ?>">
                                            <?php echo esc_html($item['location_phone']); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="bt-location-status-wrapper">
                                    <div class="bt-location-status">
                                        <?php $is_open = !empty($item['location_status_open']) && $item['location_status_open'] === 'yes'; ?>
                                        <span class="bt-status-indicator <?php echo $is_open ? 'open' : 'close'; ?>">
                                            <?php echo $is_open ? esc_html__('Open', 'somnia') : esc_html__('Close', 'somnia'); ?>
                                        </span>
                                        <?php if (!empty($item['location_closes_until'])) : ?>
                                            <span class="bt-status-text"><?php echo esc_html($item['location_closes_until']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="bt-location-actions">
                                        <?php if (!empty($item['location_button']) && !empty($item['location_button_link'])) : ?>
                                            <a href="<?php echo esc_url($item['location_button_link']); ?>" target="_blank" class="bt-direction-link">
                                                <?php echo esc_html($item['location_button']); ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                    <path d="M15.6253 5V13.125C15.6253 13.2908 15.5595 13.4497 15.4423 13.5669C15.3251 13.6842 15.1661 13.75 15.0003 13.75C14.8346 13.75 14.6756 13.6842 14.5584 13.5669C14.4412 13.4497 14.3753 13.2908 14.3753 13.125V6.50859L5.44254 15.4422C5.32526 15.5595 5.1662 15.6253 5.00035 15.6253C4.8345 15.6253 4.67544 15.5595 4.55816 15.4422C4.44088 15.3249 4.375 15.1659 4.375 15C4.375 14.8341 4.44088 14.6751 4.55816 14.5578L13.4918 5.625H6.87535C6.70959 5.625 6.55062 5.55915 6.43341 5.44194C6.3162 5.32473 6.25035 5.16576 6.25035 5C6.25035 4.83424 6.3162 4.67527 6.43341 4.55806C6.55062 4.44085 6.70959 4.375 6.87535 4.375H15.0003C15.1661 4.375 15.3251 4.44085 15.4423 4.55806C15.5595 4.67527 15.6253 4.83424 15.6253 5Z" fill="#183F91" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Map Section -->
                <div class="bt-location-finder--map">
                    <div class="bt-map-container">
                        <!-- Maps will be dynamically loaded here by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

<?php }

    protected function content_template() {}
}
