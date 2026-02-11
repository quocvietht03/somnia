<?php

namespace SomniaElementorWidgets\Widgets\FlickerCollage;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Plugin;

class Widget_FlickerCollage extends Widget_Base
{

    public function get_name()
    {
        return 'flicker-collage';
    }

    public function get_title()
    {
        return __('Flicker Collage', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-animation';
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
        $this->add_control(
            'sub_heading',
            [
                'label' => __('Sub Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('CURATE YOUR WARDROBE WITH PIECES', 'somnia'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'heading',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Save Up To 50% On Our Signature Collection', 'somnia'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('', 'somnia'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __('Button Text', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('EXPLORE COLLECTION', 'somnia'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'button_link',
            [
                'label' => __('Button Link', 'somnia'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'somnia'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->add_control(
            'heading_collage_items',
            [
                'type' => Controls_Manager::HEADING,
                'label' => __('Collage Items', 'somnia'),
                'separator' => 'before',
            ]
        );

        $repeater = new Repeater();

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

        $repeater->add_responsive_control(
            'grid_area',
            [
                'label' => __('Grid Area', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => '1 / 1 / 3 / 3',
                'description' => __('Enter grid area in format: "row_start / col_start / row_end / col_end". Example: "8 / 2 / 11 / 8" means row 8-10, column 2-7. Grid has 10 rows (1-11) and 22 columns (1-23).', 'somnia'),
                'label_block' => true,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'grid-area: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'collage_items',
            [
                'label' => __('Collage Items', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'grid_area' => '7 / 2 / 10 / 6',
                    ],
                    [
                        'grid_area' => '6 / 17 / 9 / 20',
                    ],
                    [
                        'grid_area' => '2 / 2 / 6 / 5',
                    ],
                    [
                        'grid_area' => '1 / 9 / 3 / 11',
                    ],
                ],
                'title_field' => '<# if (image.url) { #>Image<# } else { #>Item #<# } #>',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'label' => __('Image Size', 'somnia'),
                'show_label' => true,
                'default' => 'large',
                'exclude' => ['custom'],
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
            'background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-flicker-collage' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'collage_height',
            [
                'label' => __('Height', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', '%'],
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 1200,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 30,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 30,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 800,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-flicker-collage--list-images' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'content_max_width',
            [
                'label' => __('Content Max Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 300,
                        'max' => 1400,
                        'step' => 10,
                    ],
                    '%' => [
                        'min' => 30,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1000,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-flicker-collage-content' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'sub_heading_style_heading',
            [
                'label' => __('Sub Heading', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'sub_heading_color',
            [
                'label' => __('Sub Heading Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-flicker-collage--sub-heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_heading_typography',
                'label' => __('Sub Heading Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-flicker-collage--sub-heading',
            ]
        );

        $this->add_control(
            'heading_style_heading',
            [
                'label' => __('Heading', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => __('Heading Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-flicker-collage--heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'label' => __('Heading Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-flicker-collage--heading',
            ]
        );

        $this->add_control(
            'description_style_heading',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Description Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-flicker-collage--desc' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bt-flicker-collage--desc p' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label' => __('Description Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-flicker-collage--desc, {{WRAPPER}} .bt-flicker-collage--desc p',
            ]
        );

        $this->add_control(
            'button_heading',
            [
                'label' => __('Button', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs('button_style_tabs');

        $this->start_controls_tab(
            'button_normal_tab',
            [
                'label' => __('Normal', 'somnia'),
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-flicker-collage--button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-flicker-collage--button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-flicker-collage--button',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover_tab',
            [
                'label' => __('Hover', 'somnia'),
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-flicker-collage--button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_background_color',
            [
                'label' => __('Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-flicker-collage--button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
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
        $sub_heading = $settings['sub_heading'];
        $heading = $settings['heading'];
        $description = $settings['description'];
        $button_text = $settings['button_text'];
        $button_link = $settings['button_link'];
        $collage_items = $settings['collage_items'];

        $has_link = !empty($button_link['url']);
        $target = $has_link && $button_link['is_external'] ? ' target="_blank"' : '';
        $nofollow = $has_link && $button_link['nofollow'] ? ' rel="nofollow"' : '';

        // Check if Elementor editor mode
        $is_editor = Plugin::$instance->editor->is_edit_mode();
        $enable_editor = $is_editor ? 'true' : 'false';
        ?>
        <div class="bt-elwg-flicker-collage" data-elementor-editor="<?php echo esc_attr($enable_editor); ?>">
            <div class="bt-flicker-collage">
                <div class="bt-flicker-collage-wrapper">
                    <div class="bt-flicker-collage-content">
                        <?php if (!empty($sub_heading)) : ?>
                            <span class="bt-flicker-collage--sub-heading">
                                <?php echo esc_html($sub_heading); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($heading)) : ?>
                            <h2 class="bt-flicker-collage--heading">
                                <?php echo esc_html($heading); ?>
                            </h2>
                        <?php endif; ?>

                        <?php if (!empty($description)) : ?>
                            <div class="bt-flicker-collage--desc">
                                <p><?php echo wp_kses_post($description); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($button_text) && $has_link) : ?>
                            <?php echo '<a href="' . esc_url($button_link['url']) . '" class="bt-flicker-collage--button" ' . $target . $nofollow . '>' . esc_html($button_text) . '</a>'; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($collage_items)) :
                        $breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
                        // Get responsive grid area positions
                        $grid_positions = somnia_elwg_get_grid_area_positions($collage_items, $breakpoints);
                    ?>
                        <div class="bt-flicker-collage--list-images" data-grid-positions="<?php echo esc_attr(json_encode($grid_positions)); ?>">
                            <?php
                            foreach ($collage_items as $index => $item) :
                                $image = $item['image'];

                                $data_id = 'item_' . $index . '_' . uniqid();

                                // In editor mode, show all items, otherwise let JS handle visibility
                                $visible_class = $is_editor ? ' visible' : '';
                            ?>
                                <div class="bt-flicker-collage_item<?php echo esc_attr($visible_class); ?> elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>"
                                    data-id="<?php echo esc_attr($data_id); ?>"
                                    data-index="<?php echo esc_attr($index); ?>">
                                    <?php
                                    if (!empty($image['id'])) {
                                        $image_html = wp_get_attachment_image($image['id'], $settings['thumbnail_size']);
                                        // Add loading and sizes attributes
                                        $image_html = str_replace('<img ', '<img loading="lazy" sizes="100vw" ', $image_html);
                                        echo '<div class="bt-flicker-collage_img">' . $image_html . '</div>';
                                    } else {
                                        echo '<div class="bt-flicker-collage_img">';
                                            if (!empty($image['url'])) {
                                                echo '<img src="' . esc_url($image['url']) . '" alt="" loading="lazy" sizes="100vw">';
                                            } else {
                                                echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="" loading="lazy" sizes="100vw">';
                                            }
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
    }

    protected function content_template() {}
}
