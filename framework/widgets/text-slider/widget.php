<?php

namespace SomniaElementorWidgets\Widgets\TextSlider;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Stroke;

class Somnia_TextSlider extends Widget_Base
{

    public function get_name()
    {
        return 'bt-text-slider';
    }

    public function get_title()
    {
        return __('Text Slider', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-carousel';
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
            'section_content',
            [
                'label' => __('Content', 'somnia'),
            ]
        );

        $repeater = new Repeater();
        $repeater->add_control(
            'image_item',
            [
                'label' => __('Image Item', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );
        $repeater->add_control(
            'text_item',
            [
                'label' => esc_html__('Text Item', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('This is text', 'somnia'),
            ]
        );


        $this->add_control(
            'list',
            [
                'label' => __('List Texts', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'text_item' => __('Lifetime Updated', 'somnia'),
                    ],
                    [
                        'text_item' => __('Free Support', 'somnia'),
                    ],
                    [
                        'text_item' => __('Premium Plugins', 'somnia'),
                    ],
                ],
            ]
        );
        $this->add_control(
            'enable_one_icon_image',
            [
                'label' => __('Enable One Icon & Image', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'yes',
            ]
        );


        $this->add_control(
            'spacing_icon_image',
            [
                'label' => __('Spacing Icon & Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => '',
                ],
                'description' => __('Select image to display between text items', 'somnia'),
                'condition' => [
                    'enable_one_icon_image' => 'yes'
                ]
            ]
        );
        $this->add_control(
            'enable_blur',
            [
                'label' => __('Enable Blur', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'no',
                'separator' => 'before',
            ]
        );
        $this->add_responsive_control(
            'blur_width',
            [
                'label' => __('Blur Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px','em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-blur-enabled' => '--width-blur: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_blur' => 'yes'
                ],
            ]
        );
        $this->add_control(
            'blur_background_color',
            [
                'label' => __('Blur Background Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-blur-enabled' => '--background-color-blur: {{VALUE}};',
                ],
                'condition' => [
                    'enable_blur' => 'yes'
                ],
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_slider',
            [
                'label' => esc_html__('Slider', 'somnia'),
            ]
        );
        $this->add_control(
            'slider_direction_rlt',
            [
                'label' => __('Slider Direction RTL', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'no',
            ]
        );
        $this->add_control(
            'slider_speed',
            [
                'label' => __('Slider Speed', 'somnia'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10000,
            ]
        );
        $this->add_control(
            'slider_spacebetween',
            [
                'label' => __('Slider SpaceBetween', 'somnia'),
                'type' => Controls_Manager::NUMBER,
                'default' => 30,
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'section_content_style',
            [
                'label' => esc_html__('Content', 'somnia'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'item_spacing',
            [
                'label' => __('Item Spacing', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-text--item' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon_width',
            [
                'label' => __('Icon & Image Width', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-text--item img' => 'width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bt-text--item svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'icon_height',
            [
                'label' => __('Icon & Image Height', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-text--item img' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bt-text--item svg' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'image_fit',
            [
                'label' => __('Image Fit', 'somnia'),
                'type' => Controls_Manager::SELECT,
                'default' => 'contain',
                'options' => [
                    'contain' => __('Contain', 'somnia'),
                    'cover' => __('Cover', 'somnia'),
                    'fill' => __('Fill', 'somnia'),
                    'none' => __('None', 'somnia'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-text--item img' => 'object-fit: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'image_border_radius',
            [
                'label' => __('Image Border Radius', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-text--item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '#111111',
                'selectors' => [
                    '{{WRAPPER}} .bt-text--item span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'date_typography',
                'label' => __('Typography', 'somnia'),
                'default' => '',
                'selector' => '{{WRAPPER}} .bt-text--item span',
            ]
        );
        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'text_stroke',
                'selector' => '{{WRAPPER}} .bt-text--item span',
            ]
        );
        $this->add_control(
            'svg_color',
            [
                'label' => __('SVG Color', 'somnia'),
                'description' => __('This option is only for SVG images', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bt-text--item svg' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .bt-text--item svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'enable_staggered_style',
            [
                'label' => __('Enable Staggered Style', 'somnia'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'somnia'),
                'label_off' => __('No', 'somnia'),
                'default' => 'no',
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'staggered_text_color',
            [
                'label' => __('Staggered Text Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'default' => '#111111',
                'selectors' => [
                    '{{WRAPPER}} .bt-staggered-enabled .bt-text--item:nth-child(odd) span' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'enable_staggered_style' => 'yes'
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'staggered_typography',
                'label' => __('Staggered Typography', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-staggered-enabled .bt-text--item:nth-child(odd) span',
                'condition' => [
                    'enable_staggered_style' => 'yes'
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Text_Stroke::get_type(),
            [
                'name' => 'staggered_text_stroke',
                'label' => __('Staggered Text Stroke', 'somnia'),
                'selector' => '{{WRAPPER}} .bt-staggered-enabled .bt-text--item:nth-child(odd) span',
                'condition' => [
                    'enable_staggered_style' => 'yes'
                ],
            ]
        );
  
        $this->end_controls_section();
    }

    protected function register_controls()
    {
        $this->register_layout_section_controls();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings['list'])) {
            return;
        }

        if ($settings['slider_direction_rlt'] === 'yes') {
            $slider_direction = 'rtl';
        } else {
            $slider_direction = 'ltr';
        }

        $slider_speed = $settings['slider_speed'];
        $slider_space_between = $settings['slider_spacebetween'];
        
        // Get custom class
        $custom_class = !empty($settings['custom_class']) ? esc_attr($settings['custom_class']) : '';
        $wrapper_class = 'bt-elwg-text-slider--default swiper';
        if (!empty($custom_class)) {
            $wrapper_class .= ' ' . $custom_class;
        }
        
        // Add blur class if enabled
        if (!empty($settings['enable_blur']) && $settings['enable_blur'] === 'yes') {
            $wrapper_class .= ' bt-blur-enabled';
        }
        
        // Add staggered style class if enabled
        if (!empty($settings['enable_staggered_style']) && $settings['enable_staggered_style'] === 'yes') {
            $wrapper_class .= ' bt-staggered-enabled';
        }
?>
        <div class="<?php echo esc_attr($wrapper_class); ?>" data-direction="<?php echo esc_attr($slider_direction) ?>" data-speed="<?php echo esc_attr($slider_speed) ?>" data-spacebetween="<?php echo esc_attr($slider_space_between) ?>">
            <ul class="bt-text-slider swiper-wrapper">
                <?php foreach ($settings['list'] as $index => $item): ?>
                    <li class="bt-text--item swiper-slide">
                        <?php
                        // Get image source based on settings
                        $image_id = $settings['enable_one_icon_image'] === 'yes'
                            ? $settings['spacing_icon_image']['id']
                            : $item['image_item']['id'];

                        if (!empty($image_id)) {
                            $image_url = wp_get_attachment_url($image_id);

                            // Handle SVG files differently
                            if ($image_url && pathinfo($image_url, PATHINFO_EXTENSION) === 'svg') {
                                $response = wp_safe_remote_get( $image_url, array(
                                    'timeout' => 20,
                                    'headers' => array(
                                        'User-Agent' => 'Mozilla/5.0 (compatible; WordPress)',
                                    ),
                                ) );
                                if ( is_wp_error( $response ) ) {
                                    return;
                                }
                                echo wp_remote_retrieve_body( $response );
                            } else {
                                echo wp_get_attachment_image($image_id, 'medium');
                            }
                        } else {
                            // Show placeholder if no image                         
                            echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting image', 'somnia') . '">';
                        }
                        ?>
                        <?php echo '<span>' . $item['text_item'] . '</span>'; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
<?php
    }

    protected function content_template() {}
}
