<?php

namespace SomniaElementorWidgets\Widgets\OurStore;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_OurStore extends Widget_Base
{
    public function get_name()
    {
        return 'bt-our-store';
    }
    public function get_title()
    {
        return __('Our Store', 'somnia');
    }
    public function get_icon()
    {
        return 'bt-bears-icon eicon-google-maps';
    }
    public function get_categories()
    {
        return ['somnia'];
    }
    protected function register_layout_section_controls()
    {
        $this->start_controls_section(
            'section_layout',
            [
                'label' => __('Layout', 'somnia'),
            ]
        );
        $repeater = new Repeater();
        $repeater->add_control(
            'image',
            [
                'label' => __('Store Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control(
            'name',
            [
                'label' => __('Store Name', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Store Name', 'somnia'),
            ]
        );

        $repeater->add_control(
            'address',
            [
                'label' => __('Address', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('123 Street Name, City, Country', 'somnia'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'phone',
            [
                'label' => __('Phone', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('+1 234 567 890', 'somnia'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'email',
            [
                'label' => __('Email', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('store@example.com', 'somnia'),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'direction_text',
            [
                'label' => __('Direction Button Text', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Get Directions', 'somnia'),
                'placeholder' => __('Enter button text', 'somnia'),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'direction_url',
            [
                'label' => __('Direction URL', 'somnia'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://maps.google.com/?q=...', 'somnia'),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
            ]
        );
        $this->add_control(
            'store_list',
            [
                'label' => __('Store List', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'name' => __('Store #1', 'somnia'),
                        'position' => __('Location 1', 'somnia'),
                    ],
                    [
                        'name' => __('Store #2', 'somnia'),
                        'position' => __('Location 2', 'somnia'),
                    ],
                ],
                'title_field' => '{{{ name }}}',
            ]
        );
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'label' => __('Image Size', 'somnia'),
                'show_label' => true,
                'default' => 'medium',
                'exclude' => ['custom'],
            ]
        );
        $this->add_responsive_control(
            'image_ratio',
            [
                'label' => __('Image Ratio', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.66,
                ],
                'range' => [
                    'px' => [
                        'min' => 0.3,
                        'max' => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-store--image .bt-cover-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
                ],
            ]
        );
        $this->end_controls_section();
    }
    protected function register_style_section_controls()
    {
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Layout', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columns', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'prefix_class' => 'elementor-grid-',
                'selectors' => [
                    '{{WRAPPER}} .bt-store' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
            ]
        );

        $this->add_responsive_control(
            'column_gap',
            [
                'label' => __('Columns Gap', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 30,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-store' => 'column-gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'row_gap',
            [
                'label' => __('Rows Gap', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 35,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-store' => 'row-gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_style_image',
            [
                'label' => __('Image', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .bt-store--image .bt-cover-image',
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __('Border Radius', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-store--image .bt-cover-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .bt-store--image .bt-cover-image',
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => __('Content', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'name_heading',
            [
                'label' => __('Store Name', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'selector' => '{{WRAPPER}} .bt-store--name',
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-store--name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'info_heading',
            [
                'label' => __('Store Information', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'info_typography',
                'selector' => '{{WRAPPER}} .bt-store--location, {{WRAPPER}} .bt-store--phone, {{WRAPPER}} .bt-store--email',
            ]
        );

        $this->add_control(
            'info_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-store--location' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bt-store--phone' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bt-store--email' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'direction_heading',
            [
                'label' => __('Direction Button', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'direction_typography',
                'selector' => '{{WRAPPER}} .bt-store--direction',
            ]
        );

        $this->start_controls_tabs('direction_tabs');

        $this->start_controls_tab(
            'direction_normal_tab',
            [
                'label' => __('Normal', 'somnia'),
            ]
        );

        $this->add_control(
            'direction_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-store--direction' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'direction_hover_tab',
            [
                'label' => __('Hover', 'somnia'),
            ]
        );

        $this->add_control(
            'direction_hover_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-store--direction:hover' => 'color: {{VALUE}};',
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
?>
        <div class="bt-elwg-our-store--default">
            <?php if ($settings['store_list']) : ?>
                <div class="bt-store">
                    <?php foreach ($settings['store_list'] as $store) : ?>
                        <div class="bt-store--item">
                            <div class="bt-store--image">
                                <div class="bt-cover-image">
                                    <?php
                                    if (!empty($store['image']['id'])) {
                                        echo wp_get_attachment_image($store['image']['id'], $settings['thumbnail_size']);
                                    } else {
                                        if (!empty($store['image']['url'])) {
                                            echo '<img src="' . esc_url($store['image']['url']) . '" alt="' . esc_html__('Awaiting store image', 'somnia') . '">';
                                        } else {
                                            echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting store image', 'somnia') . '">';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="bt-store--info">
                                <?php if (!empty($store['name'])) : ?>
                                    <h3 class="bt-store--name"><?php echo esc_html($store['name']); ?></h3>
                                <?php endif; ?>
                                <?php if (!empty($store['address'])) : ?>
                                    <div class="bt-store--location"><?php echo esc_html__('Address:', 'somnia') . ' ' . esc_html($store['address']); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($store['phone'])) : ?>
                                    <div class="bt-store--phone">
                                        <?php echo esc_html__('Phone:', 'somnia'); ?>
                                        <a href="tel:<?php echo esc_attr(str_replace(' ', '', $store['phone'])); ?>">
                                            <?php echo esc_html($store['phone']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($store['email'])) : ?>
                                    <div class="bt-store--email">
                                        <?php echo esc_html__('Email:', 'somnia'); ?>
                                        <a href="mailto:<?php echo esc_attr($store['email']); ?>">
                                            <?php echo esc_html($store['email']); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($store['direction_url']['url']) && !empty($store['direction_text'])) : ?>
                                    <a href="<?php echo esc_url($store['direction_url']['url']); ?>" class="bt-store--direction" <?php echo esc_attr($store['direction_url']['is_external'] ? 'target="_blank"' : ''); ?>>
                                        <?php echo esc_html($store['direction_text']); ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M15.6253 5V13.125C15.6253 13.2908 15.5595 13.4497 15.4423 13.5669C15.3251 13.6842 15.1661 13.75 15.0003 13.75C14.8346 13.75 14.6756 13.6842 14.5584 13.5669C14.4412 13.4497 14.3753 13.2908 14.3753 13.125V6.50859L5.44254 15.4422C5.32526 15.5595 5.1662 15.6253 5.00035 15.6253C4.8345 15.6253 4.67544 15.5595 4.55816 15.4422C4.44088 15.3249 4.375 15.1659 4.375 15C4.375 14.8341 4.44088 14.6751 4.55816 14.5578L13.4918 5.625H6.87535C6.70959 5.625 6.55062 5.55915 6.43341 5.44194C6.3162 5.32473 6.25035 5.16576 6.25035 5C6.25035 4.83424 6.3162 4.67527 6.43341 4.55806C6.55062 4.44085 6.70959 4.375 6.87535 4.375H15.0003C15.1661 4.375 15.3251 4.44085 15.4423 4.55806C15.5595 4.67527 15.6253 4.83424 15.6253 5Z" fill="currentColor" />
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
<?php
    }

    protected function content_template() {}
}
