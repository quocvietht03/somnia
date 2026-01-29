<?php

namespace SomniaElementorWidgets\Widgets\TheStory;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use ElementorPro\Base\Base_Carousel_Trait;

class Widget_TheStory extends Widget_Base
{
	use Base_Carousel_Trait;

    public function get_name()
    {
        return 'bt-the-story';
    }

    public function get_title()
    {
        return __('The Story', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-carousel-loop';
    }

    public function get_categories()
    {
        return ['somnia'];
    }

    public function get_script_depends()
    {
        return ['swiper-slider', 'elementor-widgets'];
    }

    protected function register_content_section_controls()
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Story Items', 'somnia'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'story_image',
            [
                'label' => __('Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'story_title',
            [
                'label' => __('Title', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Story Title', 'somnia'),
                'placeholder' => __('Enter story title', 'somnia'),
            ]
        );

        $repeater->add_control(
            'story_description',
            [
                'label' => __('Description', 'somnia'),
                'type' => Controls_Manager::TEXTAREA,
                'rows' => 3,
                'default' => __('Enter story description here', 'somnia'),
                'placeholder' => __('Type your story description', 'somnia'),
            ]
        );

        $this->add_control(
            'story_items',
            [
                'label' => __('Story Items', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'story_title' => __('Urban Vision', 'somnia'),
                        'story_description' => __('Born from the streets, built for expression. We turn everyday moments into statements of style.', 'somnia'),
                    ],
                    [
                        'story_title' => __('Crafted Quality', 'somnia'),
                        'story_description' => __('Designed with intent crafted textures, clean forms, and attitude that stays timeless.', 'somnia'),
                    ],
                    [
                        'story_title' => __('Bold Identity', 'somnia'),
                        'story_description' => __('Not just clothes it\'s a mindset. Designed for those who walk their own path.', 'somnia'),
                    ],
                    [
                        'story_title' => __('Real Community', 'somnia'),
                        'story_description' => __('More than a label. We\'re a culture shaped by people who live and breathe the street spirit.', 'somnia'),
                    ],
                ],
                'title_field' => '{{{ story_title }}}',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'label' => __('Image Size', 'somnia'),
                'show_label' => true,
                'default' => 'full',
                'exclude' => ['custom'],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_layout_section_controls()
    {
        $this->start_controls_section(
            'section_layout',
            [
                'label' => __('Layout', 'somnia'),
            ]
        );

        $this->add_control(
            'slider_autoplay',
            [
                'label' => __('Slider Autoplay', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'slider_autoplay_delay',
            [
                'label' => __('Autoplay Delay', 'somnia'),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'min' => 1000,
                'max' => 20000,
                'step' => 500,
                'description' => __('Delay between slides in milliseconds', 'somnia'),
                'condition' => [
                    'slider_autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'slider_speed',
            [
                'label' => __('Slider Speed', 'somnia'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1000,
                'min' => 100,
                'max' => 5000,
                'step' => 100,
            ]
        );

        $this->add_carousel_layout_controls([
            'css_prefix' => '',
            'slides_to_show_custom_settings' => [
                'default' => '4',
                'tablet_default' => '3',
                'mobile_default' => '2',
                'selectors' => [
                    '{{WRAPPER}}' => '--swiper-slides-to-display: {{VALUE}}',
                ],
            ],
            'slides_to_scroll_custom_settings' => [
                'default' => '0',
                'condition' => [
                    'slides_to_show_custom_settings' => 100,
                ],
            ],
            'equal_height_custom_settings' => [
                'selectors' => [
                    '{{WRAPPER}} .swiper-slide > .elementor-element' => 'height: 100%',
                ],
                'condition' => [
                    'slides_to_show_custom_settings' => 100,
                ],
            ],
            'slides_on_display' => 7,
        ]);

        $this->add_responsive_control(
            'image_spacing_custom',
            [
                'label' => esc_html__('Gap between slides', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'max' => 400,
                    ],
                ],
                'default' => [
                    'size' => 30,
                ],
                'render_type' => 'template',
                'selectors' => [
                    '{{WRAPPER}}' => '--swiper-slides-gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'slider_offset_sides',
            [
                'label' => __('Offset Sides', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'somnia'),
                    'both' => __('Both', 'somnia'),
                    'left' => __('Left', 'somnia'),
                    'right' => __('Right', 'somnia'),
                ],
            ]
        );

        $this->add_responsive_control(
            'slider_offset_width',
            [
                'label' => __('Offset Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'default' => [
                    'size' => 80,
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-the-story--items' => '--slider-offset-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'slider_offset_sides!' => 'none',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function register_style_section_controls()
    {
        // Image Style Section
        $this->start_controls_section(
            'section_image_style',
            [
                'label' => __('Image', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => __('Image Height', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1200,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 600,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-the-story--image .bt-image-cover' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_object_fit',
            [
                'label' => __('Image Object Fit', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'cover' => __('Cover', 'somnia'),
                    'contain' => __('Contain', 'somnia'),
                    'fill' => __('Fill', 'somnia'),
                    'none' => __('None', 'somnia'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-the-story--image .bt-image-cover img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'image_padding',
            [
                'label' => __('Padding', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-the-story--image .bt-image-cover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Items Style Section
        $this->start_controls_section(
            'section_items_style',
            [
                'label' => __('Items', 'somnia'),
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
                    '{{WRAPPER}} .bt-the-story--title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .bt-the-story--title',
            ]
        );

        $this->add_control(
            'description_heading',
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
                    '{{WRAPPER}} .bt-the-story--description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .bt-the-story--description',
            ]
        ); 

        $this->end_controls_section();
    }

    protected function register_controls()
    {
        $this->register_content_section_controls();
        $this->register_layout_section_controls();
        $this->register_style_section_controls();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

        // Get slider settings using helper function
        $slider_settings = bt_elwg_get_slider_settings($settings, $breakpoints);
        $slider_settings['autoplay_delay'] = isset($settings['slider_autoplay_delay']) ? $settings['slider_autoplay_delay'] : 5000;

        if (!empty($settings['story_items'])) :
?>
            <div class="bt-elwg-the-story--default" data-slider-settings='<?php echo json_encode($slider_settings); ?>'>
                <!-- Image Slider -->
                <div class="bt-the-story--image">
                    <div class="swiper js-story-image-slider">
                        <div class="swiper-wrapper">
                            <?php foreach ($settings['story_items'] as $item) : ?>
                                <div class="swiper-slide">
                                    <div class="bt-image-cover">
                                        <?php
                                        if (!empty($item['story_image']['id'])) {
                                            echo wp_get_attachment_image($item['story_image']['id'], $settings['thumbnail_size']);
                                        } else {
                                            if (!empty($item['story_image']['url'])) {
                                                echo '<img src="' . esc_url($item['story_image']['url']) . '" alt="' . esc_attr($item['story_title']) . '">';
                                            } else {
                                                echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_attr__('Story Image', 'somnia') . '">';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Items Slider -->
                <div class="bt-the-story--items bt-slider-offset-sides-<?php echo esc_attr($settings['slider_offset_sides']); ?>">
                    <div class="swiper js-story-items-slider">
                        <div class="swiper-wrapper">
                            <?php foreach ($settings['story_items'] as $index => $item) : 
                                    $class_active = $index === 0 ? 'active' : '';
                                ?>
                                <div class="swiper-slide">
                                    <div class="bt-the-story--item <?php echo esc_attr($class_active); ?>" data-slide-index="<?php echo esc_attr($index); ?>">
                                        <?php if (!empty($item['story_title'])) : ?>
                                            <h3 class="bt-the-story--title"><?php echo esc_html($item['story_title']); ?></h3>
                                        <?php endif; ?>
                                        <?php if (!empty($item['story_description'])) : ?>
                                            <div class="bt-the-story--description"><?php echo esc_html($item['story_description']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($settings['slider_autoplay'] === 'yes') : ?>
                                            <div class="bt-the-story--progress bt-autoplay-enabled" style="--autoplayDelay: <?php echo esc_attr($settings['slider_autoplay_delay']); ?>ms;">
                                                <div class="bt-the-story--progress-line"></div>
                                            </div>
                                        <?php else : ?>
                                            <div class="bt-the-story--progress">
                                                <div class="bt-the-story--progress-line"></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
<?php
        endif;
    }

    protected function content_template() {}
}

