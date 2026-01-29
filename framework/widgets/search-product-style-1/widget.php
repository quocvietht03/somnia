<?php

namespace SomniaElementorWidgets\Widgets\SearchProductStyle1;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

class Widget_SearchProductStyle1 extends Widget_Base
{

	public function get_name()
	{
		return 'bt-search-product-style-1';
	}

	public function get_title()
	{
		return __('Search Product Style 1', 'somnia');
	}

	public function get_icon()
	{
		return 'bt-bears-icon eicon-search';
	}

	public function get_categories()
	{
		return ['somnia'];
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

	protected function get_product_list()
	{
		$products = [];
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		);
		$query = new \WP_Query($args);
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$products[get_the_ID()] = get_the_title();
			}
			wp_reset_postdata();
		}
		return $products;
	}

	protected function register_query_section_controls()
	{
		// Search Settings Section
		$this->start_controls_section(
			'search_section',
			[
				'label' => __('Search Settings', 'somnia'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'enable_category',
			[
				'label' => __('Enable Category Filter', 'somnia'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'somnia'),
				'label_off' => __('No', 'somnia'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		// Query Section
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
			'tab_query_exclude',
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
				'type' => Controls_Manager::TEXT,
				'default' => __('What we can help you find?', 'somnia'),
				'placeholder' => __('Enter placeholder text', 'somnia'),
				'label_block' => true,
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
				'label' => __('Autocomplete Limit', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'default' => 5,
				'min' => -1,
				'max' => 50,
				'step' => 1,
				'description' => __('Use -1 for unlimited', 'somnia'),
				'condition' => [
					'enable_autocomplete' => 'yes',
				],
			]
		);

		$this->add_control(
			'view_all_button_divider',
			[
				'type' => Controls_Manager::DIVIDER,
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

		// Products Display Section
		$this->start_controls_section(
			'products_section',
			[
				'label' => __('Products Display', 'somnia'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'trending_searches_heading',
			[
				'label' => __('Trending Searches Heading', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Trending Searches', 'somnia'),
			]
		);

		$this->add_control(
			'trending_searches',
			[
				'label' => __('Trending Keywords', 'somnia'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'placeholder' => __('shoes, bags, dresses', 'somnia'),
				'description' => __('Comma separated keywords', 'somnia'),
			]
		);
		$this->add_control(
			'products_section_heading',
			[
				'label' => __('Products Section Heading', 'somnia'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Recently Viewed', 'somnia'),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'products_source',
			[
				'label' => __('Products Source', 'somnia'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'recent_viewed' => __('Recently Viewed', 'somnia'),
					'latest' => __('Latest Products', 'somnia'),
					'featured' => __('Featured Products', 'somnia'),
					'custom' => __('Custom Products', 'somnia'),
				],
				'default' => 'recent_viewed',
			]
		);

		$this->add_control(
			'products_limit',
			[
				'label' => __('Products Limit', 'somnia'),
				'type' => Controls_Manager::NUMBER,
				'default' => 8,
				'min' => 1,
				'max' => 20,
				'step' => 1,
				'condition' => [
					'products_source!' => 'custom',
				],
			]
		);

		$this->add_control(
			'custom_products',
			[
				'label' => __('Select Products', 'somnia'),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->get_product_list(),
				'label_block' => true,
				'condition' => [
					'products_source' => 'custom',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_section_controls()
	{
		$this->start_controls_section(
			'style_section',
			[
				'label' => __('Style', 'somnia'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'search_max_width',
			[
				'label' => __('Search Max Width', 'somnia'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em'],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1200,
						'step' => 10,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
					'em' => [
						'min' => 10,
						'max' => 80,
						'step' => 0.5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1000,
				],
				'selectors' => [
					'{{WRAPPER}} .bt-elwg-search-product-style-1' => '--max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'input_color',
			[
				'label' => __('Input Text Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-search--form input' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __('Button Text Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-search--form button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => __('Button Background Color', 'somnia'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bt-search--form button' => 'background-color: {{VALUE}}',
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

		// Get current category
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
		<div class="bt-elwg-search-product-style-1">
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
									echo '<li class="bt-category-item ' . $all_categories_class . '" data-name="' . esc_attr__('All Categories', 'somnia') . '" data-slug="" data-url="' . esc_url($shop_url) . '">';
									echo '<a href="#">' . esc_html__('All Categories', 'somnia') . '</a>';
									echo '</li>';

									// Get categories
									$args = array('hide_empty' => true);
									if (!empty($settings['category'])) {
										$args['include'] = $settings['category'];
									} else {
										$args['parent'] = 0;
									}
									if (!empty($settings['category_exclude'])) {
										$args['exclude'] = $settings['category_exclude'];
									}

									$categories = get_terms('product_cat', $args);
									if (!empty($categories) && !is_wp_error($categories)) {
										foreach ($categories as $category) {
											$active_class = ($current_cat === $category->slug) ? 'active' : '';
											$category_url = get_term_link($category);
											echo '<li class="bt-category-item ' . $active_class . '" data-name="' . esc_attr($category->name) . '" data-slug="' . esc_attr($category->slug) . '" data-url="' . esc_url($category_url) . '">';
											echo '<a href="#">' . esc_html($category->name) . '</a>';
											echo '</li>';
										}
									}
									?>
								</ul>
							</div>
						</div>
					<?php endif; ?>

					<input type="hidden" name="product_cat" class="bt-product-cat-input bt-cat-product" value="<?php echo esc_attr($current_cat); ?>" />
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

				</form>

				<!-- Search Results & Products Section -->
				<div class="bt-products-wrapper">
					<!-- Live Search Results -->
					<div class="bt-live-search-results">
						<h2 class="bt-search-results-title" data-template="<?php echo esc_attr__('Search for "%s"', 'somnia'); ?>">
							<?php esc_html_e('Search Results', 'somnia'); ?>
						</h2>
						<span class="bt-loading-wave"></span>
						<div class="bt-load-data"></div>
						<?php if (!empty($settings['button_text'])) :
							$use_custom_link = ($settings['custom_button_link'] === 'yes');
							$button_link = '#';
							$target = '';
							$nofollow = '';
							$data_custom = '';

							if ($use_custom_link && !empty($settings['button_link']['url'])) {
								$button_link = $settings['button_link']['url'];
								$target = !empty($settings['button_link']['is_external']) ? ' target="_blank"' : '';
								$nofollow = !empty($settings['button_link']['nofollow']) ? ' rel="nofollow"' : '';
								$data_custom = ' data-custom-link="true"';
							} else {
								$button_link = get_permalink(wc_get_page_id('shop'));
								$data_custom = ' data-custom-link="false"';
							}
						?>
							<div class="bt-view-all-results">
								<?php
								$button_text_no_results = !empty($settings['button_text_no_results']) ? $settings['button_text_no_results'] : __('View All Products', 'somnia');
								echo '<a href="' . esc_url($button_link) . '" class="bt-view-all-button"' . $target . $nofollow . $data_custom . ' data-text-has-results="' . esc_attr($settings['button_text']) . '" data-text-no-results="' . esc_attr($button_text_no_results) . '">' . esc_html($settings['button_text']) . '</a>';
								?>
							</div>
						<?php endif; ?>
					</div>

					<!-- Products Display Section -->
					<div class="bt-products-display-section" data-source="<?php echo esc_attr($settings['products_source']); ?>" data-limit="<?php echo esc_attr($settings['products_limit']); ?>" data-products="<?php echo !empty($settings['custom_products']) ? esc_attr(implode(',', $settings['custom_products'])) : ''; ?>">
						<?php if (!empty($settings['trending_searches'])) :
							$trending_searches = array_map('trim', explode(',', $settings['trending_searches']));
							$trending_searches = array_filter($trending_searches);
						?>
							<div class="bt-trending-searches">
								<h3 class="bt-trending-title"><?php echo esc_html($settings['trending_searches_heading']); ?></h3>
								<div class="bt-trending-items">
									<?php foreach ($trending_searches as $keyword) : ?>
										<span class="bt-trending-keyword" data-keyword="<?php echo esc_attr($keyword); ?>">
											<?php echo esc_html($keyword); ?>
										</span>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

						<div class="bt-products-wrapper-inner">
							<h2 class="bt-section-heading"><?php echo esc_html($settings['products_section_heading']); ?></h2>
							<div class="bt-products-container">
								<!-- Products will be loaded here -->
								<?php
								$skeleton_count = $settings['products_limit'];

								// Desktop skeleton (WooCommerce structure)
								for ($i = 0; $i < $skeleton_count; $i++) {
								?>
									<div class="bt-product-skeleton bt-skeleton-desktop product">
										<div class="bt-skeleton-thumbnail"></div>
										<div class="bt-skeleton-content">
											<div class="bt-skeleton-title"></div>
											<div class="bt-skeleton-price"></div>
											<div class="bt-skeleton-rating"></div>
										</div>
									</div>
								<?php
								}

								// Mobile skeleton (bt-product-item structure)
								for ($i = 0; $i < $skeleton_count; $i++) {
								?>
									<div class="bt-product-item bt-product-skeleton bt-skeleton-mobile">
										<div class="bt-product-thumb">
											<div class="bt-skeleton-thumbnail"></div>
											<div class="bt-product-title">
												<div class="bt-skeleton-title"></div>
												<div class="bt-skeleton-price"></div>
											</div>
										</div>
										<div class="bt-product-add-to-cart">
											<div class="bt-skeleton-button"></div>
										</div>
									</div>
								<?php
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
		wp_reset_postdata();
	}

	protected function content_template() {}
}