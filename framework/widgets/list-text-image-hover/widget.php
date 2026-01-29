<?php

namespace SomniaElementorWidgets\Widgets\ListTextImageHover;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;

class Widget_ListTextImageHover extends Widget_Base
{

    public function get_name()
    {
        return 'list-text-image-hover';
    }

    public function get_title()
    {
        return __('List Text Image Hover', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-slider-vertical';
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
            'section_content',
            [
                'label' => __('Content', 'somnia'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Item Title', 'somnia'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => __('Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => __('Link', 'somnia'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'somnia'),
                'default' => [
                    'url' => '',
                ],
            ]
        );

        $repeater->add_control(
            'is_default_active',
            [
                'label' => __('Set as Default Active', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => '',
                'description' => __('Only one item can be set as default active', 'somnia'),
            ]
        );

        $this->add_control(
            'list_items',
            [
                'label' => __('List Items', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => __('Item One', 'somnia'),
                    ],
                    [
                        'title' => __('Item Two', 'somnia'),
                    ],
                    [
                        'title' => __('Item Three', 'somnia'),
                    ],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'label' => __('Large Image Size', 'somnia'),
                'show_label' => true,
                'default' => 'large',
                'exclude' => ['custom'],
            ]
        );

        $this->add_control(
            'show_number',
            [
                'label' => __('Show Number', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_arrow',
            [
                'label' => __('Show Arrow', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_section_controls()
    {
        $this->start_controls_section(
            'section_style_general',
            [
                'label' => esc_html__('General', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'container_width',
            [
                'label' => __('Container Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['%', 'px'],
                'range' => [
                    '%' => [
                        'min' => 20,
                        'max' => 80,
                        'step' => 1,
                    ],
                    'px' => [
                        'min' => 200,
                        'max' => 2000,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1770,
                ],
                'selectors' => [
                    '{{WRAPPER}} .list-text-image-hover--container' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_list',
            [
                'label' => esc_html__('List', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'list_item_background',
            [
                'label' => __('Item Background', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .list-text-image-hover--item' => 'background-color: {{VALUE}};',
                ],
                'default' => '#ffffff',
            ]
        );

        $this->add_control(
            'list_item_hover_background',
            [
                'label' => __('Item Hover Background', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .list-text-image-hover--item:hover' => 'background-color: {{VALUE}};',
                ],
                'default' => '#f5f5f5',
            ]
        );

        $this->add_responsive_control(
            'list_item_padding',
            [
                'label' => __('Item Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .list-text-image-hover--item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_title',
            [
                'label' => esc_html__('Title', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .list-text-image-hover--title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .list-text-image-hover--title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_number',
            [
                'label' => esc_html__('Number', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'number_typography',
                'label' => __('Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .list-text-image-hover--number',
            ]
        );

        $this->add_control(
            'number_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .list-text-image-hover--number' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_arrow',
            [
                'label' => esc_html__('Arrow', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'arrow_size',
            [
                'label' => __('Size', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],

                'selectors' => [
                    '{{WRAPPER}} .list-text-image-hover--arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .list-text-image-hover--arrow svg path' => 'fill: {{VALUE}};',
                ],
                'default' => '#181818',
            ]
        );

        $this->end_controls_section();
    }

    protected function register_controls()
    {
        $this->register_content_section_controls();
        $this->register_style_section_controls();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $list_items = $settings['list_items'];

        if (empty($list_items)) {
            return;
        }

        // Find default active item - only take the first one if multiple are set
        $default_active_index = 0;
        $found_default_active = false;
        foreach ($list_items as $index => $item) {
            if (!empty($item['is_default_active']) && $item['is_default_active'] === 'yes' && !$found_default_active) {
                $default_active_index = $index;
                $found_default_active = true;
                break; // Only take the first one
            }
        }
?>
        <div class="bt-elwg-list-text-image-hover--default" data-default-active="<?php echo esc_attr($default_active_index); ?>">
            <div class="list-text-image-hover--list">
                <?php
                foreach ($list_items as $index => $item) :
                    $item_number = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                    $image = $item['image'];
                    $link = $item['link'];
                    $has_link = !empty($link['url']);
                    $target = $has_link && $link['is_external'] ? ' target="_blank"' : '';
                    $nofollow = $has_link && $link['nofollow'] ? ' rel="nofollow"' : '';
                    // Only set active for the exact default active index
                    $classes = array(
                        'list-text-image-hover--item',
                    );
                    if ( ! empty( $index === $default_active_index ) ) {
                        $classes[] = 'active';
                    }
                    if ( ! empty( $has_link ) ) {
                        $classes[] = 'has-link';
                    }
                    $class_string = implode( ' ', $classes );

                    ?>
                    <div class="<?php echo esc_attr($class_string); ?>" data-index="<?php echo esc_attr($index); ?>" data-image-id="<?php echo esc_attr($index); ?>">
                        <?php if ($has_link) : 
                                echo '<a href="'. esc_url($link['url']) .'" class="list-text-image-hover--link" '. $target . $nofollow .'>';
                            ?>
                            <?php endif; ?>
                            <div class="list-text-image-hover--container">
                                <h3 class="list-text-image-hover--title">
                                    <?php if (!empty($settings['show_number']) && $settings['show_number'] === 'yes') : ?>
                                        <span class="list-text-image-hover--number"><?php echo esc_html($item_number); ?>.</span>
                                    <?php endif; ?>
                                    <?php echo esc_html($item['title']); ?>
                                </h3>
                                <?php if (!empty($image)) : ?>
                                    <div class="list-text-image-hover--image">
                                        <?php
                                        if (!empty($image['id'])) {
                                            echo wp_get_attachment_image($image['id'], $settings['thumbnail_size']);
                                        } else {
                                            if (!empty($image['url'])) {
                                                echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($item['title']) . '">';
                                            } else {
                                                echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_attr($item['title']) . '">';
                                            }
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($settings['show_arrow']) && $settings['show_arrow'] === 'yes') : ?>
                                    <div class="list-text-image-hover--arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                                            <path d="M51.6497 30.849L40.8999 41.6484C40.6758 41.8735 40.3718 42 40.0549 42C39.7379 42 39.4339 41.8735 39.2098 41.6484C38.9857 41.4232 38.8598 41.1178 38.8598 40.7994C38.8598 40.481 38.9857 40.1756 39.2098 39.9504L47.9216 31.1999H9.19442C8.87764 31.1999 8.57383 31.0735 8.34984 30.8485C8.12584 30.6235 8 30.3182 8 30C8 29.6818 8.12584 29.3765 8.34984 29.1515C8.57383 28.9265 8.87764 28.8001 9.19442 28.8001H47.9216L39.2098 20.0496C38.9857 19.8244 38.8598 19.519 38.8598 19.2006C38.8598 18.8822 38.9857 18.5768 39.2098 18.3516C39.4339 18.1265 39.7379 18 40.0549 18C40.3718 18 40.6758 18.1265 40.8999 18.3516L51.6497 29.151C51.7607 29.2625 51.8488 29.3948 51.909 29.5405C51.9691 29.6862 52 29.8423 52 30C52 30.1577 51.9691 30.3138 51.909 30.4595C51.8488 30.6052 51.7607 30.7375 51.6497 30.849Z" fill="#181818" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($has_link) : ?>
                            
                        <?php 
                        echo '</a>';
                        endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
