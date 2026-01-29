<?php

namespace SomniaElementorWidgets\Widgets\CollectionBanner;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Utils;

class Widget_CollectionBanner extends Widget_Base
{

	public function get_name()
	{
		return 'bt-collection-banner';
	}

	public function get_title()
	{
		return __('Collection Banner', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-slider-album';
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
			'layout_type',
			[
				'label' => __('Layout', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Default', 'somnia'),
					'style-1' => __('Style 1', 'somnia'),
				],
				'default' => 'default',
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

		$repeater->add_control(
			'title',
			[
				'label' => __('Title', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Collection Title', 'somnia'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description',
			[
				'label' => __('Description', 'somnia'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __('Collection description text', 'somnia'),
				'rows' => 3,
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label' => __('Button Text', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('VIEW COLLECTION', 'somnia'),
			]
		);

		$repeater->add_control(
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

		$repeater->add_control(
			'is_default_active',
			[
				'label' => __('Default Active', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'return_value' => 'yes',
				'default' => '',
				'description' => __('Make this item expanded by default', 'somnia'),
			]
		);

		$this->add_control(
			'collection_items',
			[
				'label' => __('Collection Items', 'somnia'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __('Urban Grace', 'somnia'),
						'description' => __('Modern looks for city life.', 'somnia'),
						'button_text' => __('VIEW COLLECTION', 'somnia'),
					],
					[
						'title' => __('Weekend Mood', 'somnia'),
						'description' => __('Casual wear, effortless vibe.', 'somnia'),
						'button_text' => __('VIEW COLLECTION', 'somnia'),
						'is_default_active' => 'yes',
					],
					[
						'title' => __('Soft Edge', 'somnia'),
						'description' => __('Minimal with a bold twist.', 'somnia'),
						'button_text' => __('VIEW COLLECTION', 'somnia'),
					],
					[
						'title' => __('New Classics', 'somnia'),
						'description' => __('Timeless style, redefined fresh.', 'somnia'),
						'button_text' => __('VIEW COLLECTION', 'somnia'),
					],
				],
				'title_field' => '{{{ title }}}',
				'min_items' => 2,
				'max_items' => 4,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'label' => __('Image Size', 'somnia'),
				'show_label' => true,
				'default' => 'medium_large',
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

		$this->add_responsive_control(
			'items_height',
			[
				'label' => __('Items Height', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vh'],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 800,
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
					'{{WRAPPER}} .bt-collection-banner .collection-item' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'items_gap',
			[
				'label' => __('Items Gap', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .bt-collection-banner' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'image_object_position',
			[
				'label' => __('Image Object Position', 'somnia'),
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'options' => [
					'top' => __('Top', 'somnia'),
					'center' => __('Center', 'somnia'),
					'bottom' => __('Bottom', 'somnia'),
					'left' => __('Left', 'somnia'),
					'right' => __('Right', 'somnia'),
					'top left' => __('Top Left', 'somnia'),
					'top right' => __('Top Right', 'somnia'),
					'bottom left' => __('Bottom Left', 'somnia'),
					'bottom right' => __('Bottom Right', 'somnia'),
				],
				'default' => 'top',
				'selectors' => [
					'{{WRAPPER}} .bt-collection-banner .collection-image img' => 'object-position: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label' => __('Title', 'somnia'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-collection-banner .collection-content h3',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-collection-banner .collection-content h3' => 'color: {{VALUE}};',
				],
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'label' => __('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-collection-banner .collection-content p',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-collection-banner .collection-content p' => 'color: {{VALUE}};',
				],
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => __('Typography', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-collection-banner .collection-button',
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __('Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-collection-banner .collection-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label' => __('Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-collection-banner .collection-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => __('Hover Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-collection-banner .collection-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_background',
			[
				'label' => __('Hover Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-collection-banner .collection-button:hover' => 'background-color: {{VALUE}};',
				],
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
		$collection_items = $settings['collection_items'];

		if (empty($collection_items)) {
			return;
		}

		// Get total number of items
		$total_items = count($collection_items);
?>
		<div class="bt-elwg-collection-banner--<?php echo esc_attr($settings['layout_type']); ?>">
			<div class="bt-collection-banner bt-items-<?php echo esc_attr($total_items); ?>" data-total-items="<?php echo esc_attr($total_items); ?>">
				<?php
				$has_active_item = false; // Track if we already have an active item
				foreach ($collection_items as $index => $item) :
					$target = $item['button_link']['is_external'] ? ' target="_blank"' : '';
					$nofollow = $item['button_link']['nofollow'] ? ' rel="nofollow"' : '';

					// Only set active if this is the first item with is_default_active = yes
					$is_active = '';
					if ($item['is_default_active'] === 'yes' && !$has_active_item) {
						$is_active = 'active';
						$has_active_item = true;
					}
				?>
					<div class="collection-item <?php echo esc_attr($is_active); ?>" data-index="<?php echo esc_attr($index); ?>">
						<div class="collection-image">
							<?php if (!empty($item['button_link']['url'])) :
								echo '<a href="'. esc_url($item['button_link']['url']) .'" '. $target . $nofollow .'>';
							endif; ?>
							<?php
							if (!empty($item['image']['id'])) {
								echo wp_get_attachment_image($item['image']['id'], $settings['thumbnail_size']);
							} else {
								if (!empty($item['image']['url'])) {
									echo '<img src="' . esc_url($item['image']['url']) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '">';
								} else {
									echo '<img src="' . esc_url(Utils::get_placeholder_image_src()) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '">';
								}
							} ?>
							<?php if (!empty($item['button_link']['url'])) : 
								echo '</a>';
							endif; ?>
						</div>
						<div class="collection-content">
							<?php if (!empty($item['title'])) : ?>
								<h3>
									<?php if (!empty($item['button_link']['url'])) :
										echo '<a href="'. esc_url($item['button_link']['url']) .'" '. $target . $nofollow .'>';
									endif; ?>
										<?php echo esc_html($item['title']); ?>
									<?php if (!empty($item['button_link']['url'])) : 
										echo '</a>';
									endif; ?>
								</h3>
							<?php endif; ?>
							<?php if (!empty($item['description'])) : ?>
								<p><?php echo esc_html($item['description']); ?></p>
							<?php endif; ?>
							<?php if (!empty($item['button_text']) && !empty($item['button_link']['url'])) : ?>
								<?php echo '<a href="'. esc_url($item['button_link']['url']) .'" class="collection-button" '. $target . $nofollow .'>'. esc_html($item['button_text']) .'</a>'; ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
<?php
	}

	protected function content_template() {}
}
