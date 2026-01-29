<?php

namespace SomniaElementorWidgets\Widgets\OurTeams;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_OurTeams extends Widget_Base
{

    public function get_name()
    {
        return 'bt-our-teams';
    }

    public function get_title()
    {
        return __('Our Teams', 'somnia');
    }

    public function get_icon()
    {
        return 'bt-bears-icon eicon-person';
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
                'label' => __('Team Member Image', 'somnia'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'name',
            [
                'label' => __('Name', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('John Doe', 'somnia'),
            ]
        );

        $repeater->add_control(
            'position',
            [
                'label' => __('Position', 'somnia'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Developer', 'somnia'),
            ]
        );

        $repeater->add_control(
            'facebook',
            [
                'label' => __('Facebook URL', 'somnia'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://facebook.com', 'somnia'),
            ]
        );

        $repeater->add_control(
            'twitter',
            [
                'label' => __('Twitter URL', 'somnia'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://twitter.com', 'somnia'),
            ]
        );

        $repeater->add_control(
            'pinterest',
            [
                'label' => __('Pinterest URL', 'somnia'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://pinterest.com', 'somnia'),
            ]
        );

        $repeater->add_control(
            'instagram',
            [
                'label' => __('Instagram URL', 'somnia'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://instagram.com', 'somnia'),
            ]
        );

        $this->add_control(
            'team_members',
            [
                'label' => __('Team Members', 'somnia'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'name' => __('Team Member #1', 'somnia'),
                        'position' => __('Developer', 'somnia'),
                    ],
                    [
                        'name' => __('Team Member #2', 'somnia'),
                        'position' => __('Designer', 'somnia'),
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
                    '{{WRAPPER}} .bt-member-image .bt-cover-image' => 'padding-bottom: calc( {{SIZE}} * 100% );',
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
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bt-team-members' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
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
                    '{{WRAPPER}} .bt-team-members' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
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
                    '{{WRAPPER}} .bt-team-members' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
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
                'selector' => '{{WRAPPER}} .bt-member-image .bt-cover-image' 
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __('Border Radius', 'somnia'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bt-member-image .bt-cover-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .bt-member-image .bt-cover-image',
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
            'heading_name',
            [
                'label' => __('Name', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  .bt-team-members .bt-team-member .bt-member-info .bt-member-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'selector' => '{{WRAPPER}}  .bt-team-members .bt-team-member .bt-member-info .bt-member-name',
            ]
        );

        $this->add_responsive_control(
            'name_spacing',
            [
                'label' => __('Spacing', 'somnia'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}  .bt-team-members .bt-team-member .bt-member-info .bt-member-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'heading_position',
            [
                'label' => __('Position', 'somnia'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'position_color',
            [
                'label' => __('Color', 'somnia'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  .bt-team-members .bt-team-member .bt-member-info .bt-member-position' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'position_typography',
                'selector' => '{{WRAPPER}}  .bt-team-members .bt-team-member .bt-member-info .bt-member-position',
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
        <div class="bt-elwg-our-teams--default">
            <?php if ($settings['team_members']) : ?>
                <div class="bt-team-members">
                    <?php foreach ($settings['team_members'] as $member) : ?>
                        <div class="bt-team-member">
                            <div class="bt-member-image">
                                <div class="bt-cover-image">
                                    <?php
                                    if (!empty($member['image']['id'])) {
                                        echo wp_get_attachment_image($member['image']['id'], $settings['thumbnail_size']);
                                    } else {
                                        if (!empty($member['image']['url'])) {
                                            echo '<img src="' . esc_url($member['image']['url']) . '" alt="'. esc_html__('Awaiting team member image', 'somnia') . '">';
                                        } else {
                                            echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="'. esc_html__('Awaiting team member image', 'somnia') . '">';
                                        }
                                    }
                                    ?>

                                </div>
                                <div class="bt-member-social">
                                    <?php if (!empty($member['facebook']['url'])) : ?>
                                        <a href="<?php echo esc_url($member['facebook']['url']); ?>" class="bt-social-link facebook">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M18.125 10.001C18.1224 11.9868 17.3938 13.9031 16.0764 15.389C14.7591 16.8749 12.9438 17.8278 10.9726 18.0682C10.9287 18.0732 10.8843 18.0688 10.8422 18.0553C10.8001 18.0419 10.7614 18.0196 10.7286 17.9901C10.6957 17.9606 10.6695 17.9244 10.6516 17.884C10.6338 17.8436 10.6247 17.7999 10.625 17.7557V11.876H12.5C12.5856 11.8762 12.6704 11.8588 12.7491 11.8248C12.8278 11.7908 12.8986 11.7411 12.9572 11.6786C13.0158 11.6161 13.061 11.5422 13.0898 11.4615C13.1187 11.3808 13.1306 11.2951 13.125 11.2096C13.1112 11.0489 13.037 10.8994 12.9174 10.7911C12.7979 10.6828 12.6417 10.6238 12.4804 10.626H10.625V8.75102C10.625 8.41949 10.7567 8.10155 10.9911 7.86713C11.2255 7.63271 11.5434 7.50102 11.875 7.50102H13.125C13.2106 7.5012 13.2954 7.48377 13.3741 7.44981C13.4528 7.41584 13.5236 7.36606 13.5822 7.30357C13.6408 7.24107 13.686 7.16719 13.7148 7.08652C13.7437 7.00585 13.7556 6.9201 13.75 6.83461C13.7361 6.67362 13.6618 6.52386 13.5419 6.41556C13.422 6.30725 13.2654 6.24845 13.1039 6.25102H11.875C11.2119 6.25102 10.576 6.51441 10.1072 6.98325C9.63836 7.45209 9.37496 8.08797 9.37496 8.75102V10.626H7.49996C7.41429 10.6258 7.32948 10.6433 7.25082 10.6772C7.17216 10.7112 7.10133 10.761 7.04271 10.8235C6.9841 10.886 6.93897 10.9598 6.91011 11.0405C6.88125 11.1212 6.86929 11.2069 6.87497 11.2924C6.88879 11.4534 6.96316 11.6032 7.08306 11.7115C7.20297 11.8198 7.3595 11.8786 7.52106 11.876H9.37496V17.7573C9.37523 17.8014 9.36616 17.845 9.34836 17.8854C9.33055 17.9257 9.3044 17.9618 9.27164 17.9913C9.23887 18.0208 9.20023 18.0431 9.15826 18.0566C9.11628 18.0701 9.07192 18.0746 9.02809 18.0698C7.00415 17.8233 5.1465 16.8259 3.82283 15.2751C2.49917 13.7243 1.80597 11.7331 1.88043 9.69555C2.03668 5.4768 5.45387 2.04711 9.67575 1.88305C10.7688 1.84071 11.8591 2.01926 12.8816 2.40802C13.904 2.79678 14.8376 3.38777 15.6263 4.14562C16.4151 4.90348 17.0429 5.81264 17.4723 6.81873C17.9016 7.82482 18.1236 8.90716 18.125 10.001Z" fill="currentColor" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($member['twitter']['url'])) : ?>
                                        <a href="<?php echo esc_url($member['twitter']['url']); ?>" class="bt-social-link twitter">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M16.7972 17.1758C16.7434 17.2738 16.6642 17.3556 16.568 17.4126C16.4719 17.4696 16.3621 17.4998 16.2503 17.5H12.5003C12.3951 17.5 12.2916 17.4734 12.1995 17.4227C12.1073 17.3721 12.0294 17.2989 11.973 17.2102L8.80968 12.2391L4.2128 17.2953C4.10074 17.4157 3.94575 17.487 3.78143 17.4939C3.61712 17.5008 3.45671 17.4426 3.335 17.332C3.21329 17.2214 3.14008 17.0673 3.13125 16.903C3.12241 16.7388 3.17866 16.5777 3.2878 16.4547L8.11359 11.1422L3.22296 3.46094C3.16273 3.36644 3.12901 3.25749 3.12534 3.14548C3.12166 3.03348 3.14817 2.92255 3.20208 2.82431C3.256 2.72607 3.33533 2.64413 3.43178 2.58708C3.52823 2.53002 3.63824 2.49995 3.7503 2.5H7.5003C7.60549 2.50003 7.70897 2.52661 7.80115 2.57728C7.89334 2.62795 7.97124 2.70106 8.02765 2.78984L11.1909 7.76094L15.7878 2.70469C15.8999 2.58431 16.0549 2.51296 16.2192 2.50609C16.3835 2.49923 16.5439 2.5574 16.6656 2.66801C16.7873 2.77862 16.8605 2.93275 16.8694 3.09697C16.8782 3.2612 16.8219 3.42228 16.7128 3.54531L11.887 8.85391L16.7776 16.5398C16.8375 16.6344 16.871 16.7433 16.8744 16.8551C16.8778 16.967 16.8512 17.0777 16.7972 17.1758Z" fill="currentColor" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($member['pinterest']['url'])) : ?>
                                        <a href="<?php echo esc_url($member['pinterest']['url']); ?>" class="bt-social-link pinterest">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M18.7505 10.0551C18.7208 14.4683 15.1208 18.0809 10.7083 18.1246C10.0256 18.1318 9.34474 18.0531 8.68173 17.8902C8.64184 17.8803 8.60431 17.8625 8.57128 17.8381C8.53826 17.8136 8.51039 17.7828 8.48927 17.7476C8.46815 17.7123 8.4542 17.6732 8.44821 17.6325C8.44223 17.5919 8.44432 17.5504 8.45439 17.5105L9.12939 14.8113C9.78776 15.1413 10.5141 15.313 11.2505 15.3129C14.1411 15.3129 16.4614 12.6996 16.2356 9.58163C16.1744 8.77235 15.9388 7.98591 15.5449 7.2763C15.151 6.56668 14.6083 5.9507 13.9539 5.47064C13.2995 4.99058 12.549 4.65781 11.7539 4.49518C10.9587 4.33254 10.1378 4.3439 9.34749 4.52846C8.55717 4.71303 7.81614 5.06643 7.17528 5.5644C6.53442 6.06238 6.00891 6.69314 5.63483 7.41338C5.26075 8.13361 5.04695 8.92627 5.00812 9.73693C4.96928 10.5476 5.10633 11.3571 5.40985 12.1098C5.44139 12.1883 5.48854 12.2596 5.54844 12.3193C5.60835 12.3791 5.67977 12.4261 5.75837 12.4574C5.83697 12.4887 5.92112 12.5038 6.00571 12.5017C6.0903 12.4995 6.17357 12.4802 6.25048 12.4449C6.39716 12.3738 6.51117 12.2494 6.56924 12.0971C6.62732 11.9448 6.62508 11.7761 6.56298 11.6254C6.32938 11.0396 6.2252 10.4102 6.25755 9.78039C6.28991 9.15057 6.45805 8.53519 6.75045 7.97642C7.04285 7.41765 7.45259 6.9287 7.95161 6.54307C8.45062 6.15745 9.02711 5.88426 9.64156 5.74224C10.256 5.60022 10.8939 5.59272 11.5115 5.72026C12.1291 5.84779 12.7119 6.10734 13.2198 6.48113C13.7278 6.85491 14.1489 7.3341 14.4544 7.88584C14.7598 8.43758 14.9424 9.04884 14.9895 9.67772C15.1567 12.0629 13.4169 14.0629 11.2505 14.0629C10.6134 14.0626 9.98835 13.8886 9.44267 13.5598L10.6067 8.90194C10.6438 8.74227 10.6166 8.57445 10.531 8.43463C10.4455 8.29481 10.3085 8.19418 10.1495 8.15443C9.99046 8.11468 9.82219 8.13898 9.68093 8.2221C9.53966 8.30522 9.4367 8.44051 9.39423 8.59882L7.29579 16.9934C7.2844 17.0391 7.26282 17.0816 7.23266 17.1178C7.20251 17.154 7.16456 17.1829 7.12166 17.2024C7.07876 17.2218 7.03201 17.2313 6.98492 17.2302C6.93782 17.229 6.8916 17.2172 6.8497 17.1957C5.52425 16.5002 4.4167 15.452 3.64924 14.1669C2.88177 12.8818 2.48423 11.4096 2.50048 9.91288C2.54735 5.50975 6.14657 1.916 10.5474 1.87538C11.6255 1.86499 12.695 2.06929 13.6934 2.47639C14.6918 2.88348 15.5992 3.48522 16.3627 4.24654C17.1262 5.00787 17.7305 5.91354 18.1405 6.91078C18.5504 7.90802 18.7578 8.97688 18.7505 10.0551Z" fill="currentColor" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($member['instagram']['url'])) : ?>
                                        <a href="<?php echo esc_url($member['instagram']['url']); ?>" class="bt-social-link instagram">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M13.75 1.875H6.25C5.09006 1.87624 3.97798 2.33758 3.15778 3.15778C2.33758 3.97798 1.87624 5.09006 1.875 6.25V13.75C1.87624 14.9099 2.33758 16.022 3.15778 16.8422C3.97798 17.6624 5.09006 18.1238 6.25 18.125H13.75C14.9099 18.1238 16.022 17.6624 16.8422 16.8422C17.6624 16.022 18.1238 14.9099 18.125 13.75V6.25C18.1238 5.09006 17.6624 3.97798 16.8422 3.15778C16.022 2.33758 14.9099 1.87624 13.75 1.875ZM10 13.75C9.25832 13.75 8.5333 13.5301 7.91661 13.118C7.29993 12.706 6.81928 12.1203 6.53545 11.4351C6.25162 10.7498 6.17736 9.99584 6.32206 9.26841C6.46675 8.54098 6.8239 7.8728 7.34835 7.34835C7.8728 6.8239 8.54098 6.46675 9.26841 6.32206C9.99584 6.17736 10.7498 6.25162 11.4351 6.53545C12.1203 6.81928 12.706 7.29993 13.118 7.91661C13.5301 8.5333 13.75 9.25832 13.75 10C13.749 10.9942 13.3535 11.9475 12.6505 12.6505C11.9475 13.3535 10.9942 13.749 10 13.75ZM14.6875 6.25C14.5021 6.25 14.3208 6.19502 14.1667 6.092C14.0125 5.98899 13.8923 5.84257 13.8214 5.67127C13.7504 5.49996 13.7318 5.31146 13.768 5.1296C13.8042 4.94775 13.8935 4.7807 14.0246 4.64959C14.1557 4.51848 14.3227 4.42919 14.5046 4.39301C14.6865 4.35684 14.875 4.37541 15.0463 4.44636C15.2176 4.51732 15.364 4.63748 15.467 4.79165C15.57 4.94582 15.625 5.12708 15.625 5.3125C15.625 5.56114 15.5262 5.7996 15.3504 5.97541C15.1746 6.15123 14.9361 6.25 14.6875 6.25ZM12.5 10C12.5 10.4945 12.3534 10.9778 12.0787 11.3889C11.804 11.8 11.4135 12.1205 10.9567 12.3097C10.4999 12.4989 9.99723 12.5484 9.51227 12.452C9.02732 12.3555 8.58186 12.1174 8.23223 11.7678C7.8826 11.4181 7.6445 10.9727 7.54804 10.4877C7.45157 10.0028 7.50108 9.50011 7.6903 9.04329C7.87952 8.58648 8.19995 8.19603 8.61107 7.92133C9.0222 7.64662 9.50555 7.5 10 7.5C10.663 7.5 11.2989 7.76339 11.7678 8.23223C12.2366 8.70107 12.5 9.33696 12.5 10Z" fill="currentColor" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="bt-member-info">
                                <?php if (!empty($member['name'])) : ?>
                                    <h4 class="bt-member-name"><?php echo esc_html($member['name']); ?></h4>
                                <?php endif; ?>

                                <?php if (!empty($member['position'])) : ?>
                                    <div class="bt-member-position"><?php echo esc_html($member['position']); ?></div>
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
