<?php

namespace SomniaElementorWidgets\Widgets\SearchProduct;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Widget_SearchProduct extends Widget_Base
{

	public function get_name()
	{
		return 'bt-search-product';
	}

	public function get_title()
	{
		return __('Search Product', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-search';
	}

	public function get_categories()
	{
		return ['somnia'];
	}

	public function get_script_depends()
	{
		return ['elementor-widgets'];
	}

	public function get_supported_taxonomies()
	{
		$supported_taxonomies = [];

		if (!class_exists('WooCommerce')) {
			return $supported_taxonomies;
		}

		$categories = get_terms(array(
			'taxonomy' => 'product_cat',
			'hide_empty' => false,
		));
		if (!empty($categories)  && !is_wp_error($categories)) {
			foreach ($categories as $category) {
				$supported_taxonomies[$category->term_id] = $category->name;
			}
		}

		return $supported_taxonomies;
	}

	protected function register_query_section_controls()
	{
		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'somnia'),
			]
		);
		$this->add_control(
			'layout_type',
			[
				'label' => __('Layout Type', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'layout-01' => __('Layout 01', 'somnia'),
					'layout-02' => __('Layout 02', 'somnia'),
					'layout-03' => __('Layout 03', 'somnia'),
				],
				'default' => 'layout-01',
			]
		);
		$this->add_control(
			'enable_category',
			[
				'label' => __('Enable Category', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'layout_type!' => 'layout-02',
				],
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_query',
			[
				'label' => __('Query', 'somnia'),
			]
		);

		$this->start_controls_tabs('tabs_query');

		$this->start_controls_tab(
			'tab_query_include',
			[
				'label' => __('Include', 'somnia'),
			]
		);

		$this->add_control(
			'category',
			[
				'label' => __('Category', 'somnia'),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_supported_taxonomies(),
				'label_block' => true,
				'multiple' => true,
			]
		);

		$this->end_controls_tab();


		$this->start_controls_tab(
			'tab_query_exnlude',
			[
				'label' => __('Exclude', 'somnia'),
			]
		);

		$this->add_control(
			'category_exclude',
			[
				'label' => __('Category', 'somnia'),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_supported_taxonomies(),
				'label_block' => true,
				'multiple' => true,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->add_control(
			'placeholder_text',
			[
				'label' => __('Placeholder Text', 'somnia'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __('What are you looking for today?', 'somnia'),
				'placeholder' => __('Enter placeholder text', 'somnia'),
			]
		);
		$this->add_control(
			'enable_autocomplete',
			[
				'label' => __('Enable Autocomplete', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'autocomplete_limit',
			[
				'label' => __('Autocomplete Products Limit', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'default' => 5,
				'min' => -1,
				'max' => 50,
				'step' => 1,
				'description' => __('Number of products to show in autocomplete results. Use -1 for unlimited.', 'somnia'),
				'condition' => [
					'enable_autocomplete' => 'yes',
				],
			]
		);
		$this->add_control(
			'add_to_cart_style',
			[
				'label' => __('Add to Cart Style', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'text' => __('Text', 'somnia'),
					'icon' => __('Icon', 'somnia'),
				],
				'default' => 'text',
				'condition' => [
					'enable_autocomplete' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_text',
			[
				'label' => __('Button Text (Has Results)', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('View All Results', 'somnia'),
				'placeholder' => __('Enter button text', 'somnia'),
				'description' => __('Text shown when search has results', 'somnia'),
				'condition' => [
					'enable_autocomplete' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_text_no_results',
			[
				'label' => __('Button Text (No Results)', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('View All Products', 'somnia'),
				'placeholder' => __('Enter button text', 'somnia'),
				'description' => __('Text shown when search has no results', 'somnia'),
				'condition' => [
					'enable_autocomplete' => 'yes',
				],
			]
		);
		$this->add_control(
			'custom_button_link',
			[
				'label' => __('Custom Button Link', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'enable_autocomplete' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_link',
			[
				'label' => __('Button Link', 'somnia'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'somnia'),
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => false,
				],
				'condition' => [
					'enable_autocomplete' => 'yes',
					'custom_button_link' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'enable_keyword_suggest',
			[
				'label' => __('Enable Keyword Suggestion', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'keyword_source',
			[
				'label' => __('Keyword Source', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'from_titles' => __('From Product Titles', 'somnia'),
					'manual' => __('Manual Keywords', 'somnia'),
				],
				'default' => 'from_titles',
				'condition' => [
					'enable_keyword_suggest' => 'yes',
				],
			]
		);
		
		$this->add_control(
			'keyword_word_limit',
			[
				'label' => __('Word Limit', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'default' => 100,
				'min' => 1,
				'max' => 1000,
				'step' => 1,
				'description' => __('Maximum number of words to extract from product titles', 'somnia'),
				'condition' => [
					'enable_keyword_suggest' => 'yes',
					'keyword_source' => 'from_titles',
				],
			]
		);
		
		$this->add_control(
			'manual_keywords',
			[
				'label' => __('Manual Keywords', 'somnia'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'placeholder' => __('Enter keywords, one per line or separated by commas', 'somnia'),
				'description' => __('Enter keywords for suggestion. Each line or comma-separated value will be used.', 'somnia'),
				'condition' => [
					'enable_keyword_suggest' => 'yes',
					'keyword_source' => 'manual',
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
				'label' => __('Style', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'input_color',
			[
				'label' => __('Input Text Color', 'somnia'), 
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-search--form input[type="search"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label' => __('Placeholder Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-search--form input[type="search"]::placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'search_icon_color',
			[
				'label' => __('Search Icon Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-search--form .bt-search-submit svg path' => 'stroke: {{VALUE}};',
				],
				'condition' => [
					'layout_type' => ['layout-02', 'layout-03'],
				],
			]
		);

		$this->add_control(
			'form_background',
			[
				'label' => __('Form Background', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-search--form' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'layout_type' => ['layout-02', 'layout-03'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border',
				'label' => __('Form Border', 'somnia'),
				'selector' => '{{WRAPPER}} .bt-search--form',
				'condition' => [
					'layout_type' => ['layout-02', 'layout-03'],
				],
			]
		);
		$this->add_control(
			'form_border_radius',
			[
				'label' => __('Border Radius', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bt-search--form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'layout_type' => ['layout-02', 'layout-03'],
				],
			]
		);
		$this->add_control(
			'input_padding',
			[
				'label' => __('Input Padding', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bt-search--form input[type="search"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bt-search--form .bt-keyword-ghost' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'layout_type' => ['layout-02', 'layout-03'],
				],
			]
		);

		$this->add_control(
			'form_padding',
			[
				'label' => __('Padding', 'somnia'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bt-search--form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'layout_type' => ['layout-02', 'layout-03'],
				],
			]
		);
       
		$this->end_controls_section();
	}

	protected function register_controls()
	{
		$this->register_query_section_controls();
		$this->register_style_section_controls();
	}

	/**
	 * Get keyword suggestions based on source - returns word pool array
	 */
	protected function get_keyword_suggestions($settings, $widget_category_slugs, $widget_category_exclude_slugs)
	{
		$word_pool = array();
		
		if (empty($settings['enable_keyword_suggest']) || $settings['enable_keyword_suggest'] !== 'yes') {
			return $word_pool;
		}
		
		$keyword_source = !empty($settings['keyword_source']) ? $settings['keyword_source'] : 'from_titles';
		
		if ($keyword_source === 'manual' && !empty($settings['manual_keywords'])) {
			// Get from manual keywords - extract all words
			$manual_keywords = $settings['manual_keywords'];
			$keyword_list = preg_split('/[,\n]/', $manual_keywords);
			
			foreach ($keyword_list as $keyword) {
				$keyword = trim($keyword);
				if (!empty($keyword)) {
					// Split keyword into words
					$words = preg_split('/\s+/', strtolower($keyword));
					foreach ($words as $word) {
						$word = preg_replace('/[^a-z0-9]/', '', $word);
						if (strlen($word) > 2 && !in_array($word, $word_pool)) {
							$word_pool[] = $word;
						}
					}
				}
			}
		} elseif ($keyword_source === 'from_titles') {
			// Get from product titles
			$word_limit = !empty($settings['keyword_word_limit']) ? intval($settings['keyword_word_limit']) : 100;
			
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'tax_query' => array()
			);
			
			// Build tax_query based on widget settings
			if (!empty($widget_category_slugs)) {
				$include_slugs = array_map('trim', explode(',', $widget_category_slugs));
				$args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field' => 'slug',
					'terms' => $include_slugs,
					'operator' => 'IN'
				);
			} elseif (!empty($widget_category_exclude_slugs)) {
				$exclude_slugs = array_map('trim', explode(',', $widget_category_exclude_slugs));
				$args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field' => 'slug',
					'terms' => $exclude_slugs,
					'operator' => 'NOT IN'
				);
			}
			
			if (!empty($args['tax_query'])) {
				$args['tax_query']['relation'] = 'AND';
			}
			
			$products = get_posts($args);
			
			foreach ($products as $product) {
				$title = get_the_title($product->ID);
				if (!empty($title)) {
					$words = preg_split('/\s+/', strtolower($title));
					foreach ($words as $word) {
						$word = preg_replace('/[^a-z0-9]/', '', $word);
						if (strlen($word) > 2 && !in_array($word, $word_pool)) {
							$word_pool[] = $word;
							if (count($word_pool) >= $word_limit) {
								break 2;
							}
						}
					}
				}
			}
		}
		
		return $word_pool;
	}

	protected function render()
	{
		if (!class_exists('WooCommerce')) {
			return;
		}
		
		$settings = $this->get_settings_for_display();

		// Get current category from URL or category page
		$current_cat = '';
		if (isset($_GET['product_cat'])) {
			$product_cat_value = sanitize_text_field($_GET['product_cat']);
			// If product_cat contains comma, it means multiple categories, so treat as "All Categories"
			if (strpos($product_cat_value, ',') !== false) {
				$current_cat = ''; // Multiple categories = All Categories
			} else {
				$current_cat = $product_cat_value;
			}
		} elseif (somnia_is_category_archive_page()) {
			$current_category = get_queried_object();
			if ($current_category && isset($current_category->slug)) {
				$current_cat = $current_category->slug;
			}
		}
	
	$add_to_cart_class = !empty($settings['add_to_cart_style']) && $settings['add_to_cart_style'] === 'icon' ? ' bt-add-to-cart-icon' : '';

	// Prepare category slugs for widget include categories (convert IDs to slugs)
	$widget_category_slugs = '';
	$widget_category_count = 0;
	$widget_single_category_url = '';
	if (!empty($settings['category']) && is_array($settings['category'])) {
		$widget_category_count = count($settings['category']);
		$category_slugs = array();
		foreach ($settings['category'] as $cat_id) {
			$term = get_term($cat_id, 'product_cat');
			if ($term && !is_wp_error($term)) {
				$category_slugs[] = $term->slug;
				// Store first category URL for single category case
				if ($widget_category_count === 1 && empty($widget_single_category_url)) {
					$widget_single_category_url = get_term_link($term);
				}
			}
		}
		$widget_category_slugs = implode(',', $category_slugs);
	}

	// Prepare category slugs for widget exclude categories (convert IDs to slugs)
	$widget_category_exclude_slugs = '';
	if (!empty($settings['category_exclude']) && is_array($settings['category_exclude'])) {
		$exclude_slugs = array();
		foreach ($settings['category_exclude'] as $cat_id) {
			$term = get_term($cat_id, 'product_cat');
			if ($term && !is_wp_error($term)) {
				$exclude_slugs[] = $term->slug;
			}
		}
		$widget_category_exclude_slugs = implode(',', $exclude_slugs);
	}

?>
		<div class="bt-elwg-search-product <?php echo esc_attr($settings['layout_type'] . $add_to_cart_class); ?>">
			<div class="bt-search">
				<form method="get" class="bt-search--form" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" data-widget-single-category-url="<?php echo esc_url($widget_single_category_url); ?>">
					<?php if ($settings['enable_category'] === 'yes') : ?>
						<div class="bt-search--category">
							<div class="bt-category-dropdown">

								<div class="bt-selected-category">
									<span><?php echo !empty($current_cat) ? esc_html(get_term_by('slug', $current_cat, 'product_cat')->name) : esc_html__('All Categories', 'somnia'); ?></span>
									<svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="currentColor">
										<path d="M14.0306 6.53122L9.0306 11.5312C8.96092 11.6011 8.87813 11.6566 8.78696 11.6945C8.6958 11.7323 8.59806 11.7518 8.49935 11.7518C8.40064 11.7518 8.3029 11.7323 8.21173 11.6945C8.12057 11.6566 8.03778 11.6011 7.9681 11.5312L2.9681 6.53122C2.8272 6.39033 2.74805 6.19923 2.74805 5.99997C2.74805 5.80072 2.8272 5.60962 2.9681 5.46872C3.10899 5.32783 3.30009 5.24867 3.49935 5.24867C3.69861 5.24867 3.8897 5.32783 4.0306 5.46872L8.49997 9.9381L12.9693 5.4681C13.1102 5.3272 13.3013 5.24805 13.5006 5.24805C13.6999 5.24805 13.891 5.3272 14.0318 5.4681C14.1727 5.60899 14.2519 5.80009 14.2519 5.99935C14.2519 6.19861 14.1727 6.3897 14.0318 6.5306L14.0306 6.53122Z" />
									</svg>
								</div>

							<ul class="bt-category-list">
								<?php
								// Always show "All Categories" option
								$all_categories_class = empty($current_cat) ? 'active' : '';
								$shop_url = get_permalink(wc_get_page_id('shop'));
								echo '<li class="bt-category-item ' . $all_categories_class . '" data-cat-slug="" data-cat-url="' . esc_url($shop_url) . '">';
								echo '<a href="#">' . esc_html__('All Categories', 'somnia') . '</a>';
								echo '</li>';

								// Get categories with proper filtering
								$args = array(
									'hide_empty' => true,
								);
								
								// If category (include) is set, show those specific categories (can include child categories)
								if (!empty($settings['category'])) {
									$args['include'] = $settings['category'];
								} else {
									// Default: Only show parent categories (top level)
									$args['parent'] = 0;
								}
								
								// If category_exclude is set, exclude those categories
									if (!empty($settings['category_exclude'])) {
										$args['exclude'] = $settings['category_exclude'];
									}
									
									$categories = get_terms('product_cat', $args);

									if (!empty($categories) && !is_wp_error($categories)) {
										foreach ($categories as $category) {
											$active_class = ($current_cat === $category->slug) ? 'active' : '';
											$category_url = get_term_link($category);
											echo '<li class="bt-category-item ' . $active_class . '" data-cat-slug="' . esc_attr($category->slug) . '" data-cat-url="' . esc_url($category_url) . '">';
											echo '<a href="#">' . esc_html($category->name) . '</a>';
											echo '</li>';
										}
									}
									?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
					<input type="hidden" name="product_cat" class="bt-product-cat-input" value="<?php echo esc_attr($current_cat); ?>" />
					<!-- Widget settings for category filtering -->
					<!-- bt-widget-category-include now contains slugs instead of IDs -->
					<input type="hidden" name="widget_category_include" class="bt-widget-category-include" value="<?php echo esc_attr($widget_category_slugs); ?>" />
					<!-- bt-widget-category-exclude now contains slugs instead of IDs -->
					<input type="hidden" name="widget_category_exclude" class="bt-widget-category-exclude" value="<?php echo esc_attr($widget_category_exclude_slugs); ?>" />
					<input type="hidden" name="autocomplete_limit" class="bt-autocomplete-limit" value="<?php echo !empty($settings['autocomplete_limit']) ? esc_attr($settings['autocomplete_limit']) : '5'; ?>" />
					<?php 
					// Get keyword suggestions
					$keyword_suggestions = $this->get_keyword_suggestions($settings, $widget_category_slugs, $widget_category_exclude_slugs);
					$show_keyword_suggest = false;
					
					if (!empty($settings['enable_keyword_suggest']) && $settings['enable_keyword_suggest'] === 'yes' && !empty($keyword_suggestions)) {
						$show_keyword_suggest = true;
						// Convert array to JSON string for data attribute
						$keywords_json = json_encode($keyword_suggestions);
					}
					?>
					<?php if ($show_keyword_suggest) : ?>
					<div class="bt-search-wrap" data-suggest="<?php echo esc_attr($keywords_json); ?>">
						<input type="text" id="bt-keyword-ghost-<?php echo esc_attr($this->get_id()); ?>" class="bt-keyword-ghost" disabled autocomplete="off" aria-hidden="true" />
						<input type="search" class="bt-search-field <?php echo !empty($settings['enable_autocomplete']) ? ' bt-live-search' : ''; ?> bt-keyword-suggest" placeholder="<?php echo esc_attr($settings['placeholder_text']); ?>" value="<?php echo isset($_GET['search_keyword']) ? esc_attr($_GET['search_keyword']) : ''; ?>" name="search_keyword" autocomplete="off"/>
					</div>
					<?php else : ?>
					<input type="search" class="bt-search-field <?php echo !empty($settings['enable_autocomplete']) ? ' bt-live-search' : ''; ?>" placeholder="<?php echo esc_attr($settings['placeholder_text']); ?>" value="<?php echo isset($_GET['search_keyword']) ? esc_attr($_GET['search_keyword']) : ''; ?>" name="search_keyword" autocomplete="off"/>
					<?php endif; ?>
					<button type="submit" class="bt-search-submit">
						<?php esc_html_e('Search', 'somnia'); ?>
						<svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<g clip-path="url(#clip0_12147_43)">
								<path d="M10.5 18C14.6421 18 18 14.6421 18 10.5C18 6.35786 14.6421 3 10.5 3C6.35786 3 3 6.35786 3 10.5C3 14.6421 6.35786 18 10.5 18Z" stroke="#181818" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M15.8027 15.8037L20.9993 21.0003" stroke="#181818" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</g>
							<defs>
								<clipPath id="clip0_12147_43">
									<rect width="24" height="24" fill="white" />
								</clipPath>
							</defs>
						</svg>
					</button>
					<div class="bt-live-search-results">
						<span class="bt-loading-wave"></span>
						<div class="bt-load-data"></div>
						<?php if (!empty($settings['button_text'])) : 
							// Check if custom link is enabled
							$use_custom_link = ($settings['custom_button_link'] === 'yes');
							$button_link = '#';
							$target = '';
							$nofollow = '';
							$data_custom = '';
							
							if ($use_custom_link && !empty($settings['button_link']['url'])) {
								// Use custom link
								$button_link = $settings['button_link']['url'];
								$target = !empty($settings['button_link']['is_external']) ? ' target="_blank"' : '';
								$nofollow = !empty($settings['button_link']['nofollow']) ? ' rel="nofollow"' : '';
								$data_custom = ' data-custom-link="true"';
							} else {
								// Use dynamic link (will be updated by JS)
								$button_link = get_permalink(wc_get_page_id('shop'));
								$data_custom = ' data-custom-link="false"';
							}
						?>
							<div class="bt-view-all-results" >
								<?php 
								$button_text_no_results = !empty($settings['button_text_no_results']) ? $settings['button_text_no_results'] : __('View All Products', 'somnia');
								echo '<a href="' . esc_url($button_link) . '" class="bt-view-all-button"' . $target . $nofollow . $data_custom . ' data-text-has-results="' . esc_attr($settings['button_text']) . '" data-text-no-results="' . esc_attr($button_text_no_results) . '">' . esc_html($settings['button_text']) . '</a>'; 
								?>
							</div>
						<?php endif; ?>
					</div>
				</form>
			</div>
		</div>
<?php
		wp_reset_postdata();
	}

	protected function content_template() {}
}