<?php

/**
 * Template Name: Shop Sidebar
 */
global $wp_query;
$rows = intval(get_option('woocommerce_catalog_rows', 2));
$columns = intval(get_option('woocommerce_catalog_columns', 4));

$rows = $rows > 0 ? $rows : 1;
$columns = $columns > 0 ? $columns : 1;
$limit = $rows * $columns;
// Prepare query params - include category slug if on category page
$query_params = $_GET;
if (somnia_is_category_archive_page()) {
	// Remove product_cat from params when on category page (it's already in URL)
	unset($query_params['product_cat']);
	$current_category = get_queried_object();
	if ($current_category && !empty($current_category->slug)) {
		// Pass category slug to query function for filtering
		$query_params['product_cat'] = $current_category->slug;
	}
}
$is_category_page = somnia_is_category_archive_page();
if ($is_category_page) {
	$display_type = get_option('woocommerce_category_archive_display', '');
} else {
	$display_type = get_option('woocommerce_shop_page_display', '');
	if (isset($_GET['layout-shop']) && $_GET['layout-shop'] == 'show-categories') {
		$display_type = 'subcategories';
	}
}
$query_args = somnia_products_query_args($query_params, $limit);
$wp_query = new \WP_Query($query_args);
$current_page = isset($_GET['current_page']) && $_GET['current_page'] != '' ? absint($_GET['current_page']) : 1;
$total_page = $wp_query->max_num_pages;
$total_products = $wp_query->found_posts;
$archive_shop = get_field('archive_shop', 'option');
$pagination_type = isset($archive_shop['shop_pagination']) ? $archive_shop['shop_pagination'] : 'default';
if (isset($_GET['layout-pagination']) && !empty($_GET['layout-pagination'])) {
	$pagination_type = sanitize_text_field($_GET['layout-pagination']);
}
$content_width = isset($archive_shop['content_width']) ? $archive_shop['content_width'] : 'boxed';
if (isset($_GET['content_width']) && !empty($_GET['content_width'])) {
	$content_width = sanitize_text_field($_GET['content_width']);
}
$sidebar_position = isset($archive_shop['sidebar_position']) ? $archive_shop['sidebar_position'] : 'sidebar-left';
if (isset($_GET['sidebar_position']) && !empty($_GET['sidebar_position'])) {
	$sidebar_position = sanitize_text_field($_GET['sidebar_position']);
}
get_header('shop');
get_template_part('framework/templates/shop', 'titlebar');

?>
<?php if (somnia_should_show_categories()) { ?>
	<div class="bt-category-wrapper bt-display-<?php echo esc_attr($display_type); ?>">
		<div class="bt-container <?php echo esc_attr($content_width); ?>">
			<div class="bt-category-layout">
				<?php do_action('somnia_shop_categories_display'); ?>
			</div>
		</div>
	</div>
<?php } ?>
<?php if (somnia_should_show_products()) { ?>
	<div class="bt-filter-scroll-pos"></div>
	<main id="bt_main" class="bt-site-main">
		<div class="bt-main-content">

			<div class="bt-main-products-ss bt-template-sidebar <?php echo esc_attr($sidebar_position); ?>">
				<div class="bt-container <?php echo esc_attr($content_width); ?>">
					<div class="bt-main-product-row">
						<div class="bt-products-sidebar">
							<?php get_template_part('woocommerce/sidebar', 'product', array('total_products' => $total_products)); ?>
						</div>
						<div class="bt-main-products-inner">
							<div class="bt-products-topbar">
								<div class="bt-product-action">
									<div class="bt-product-filter-toggle">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
											<path d="M14.4125 3.09564C14.3355 2.91784 14.208 2.76658 14.0458 2.66068C13.8835 2.55477 13.6937 2.49891 13.5 2.50002H2.49998C2.30644 2.5004 2.11717 2.55694 1.95512 2.66277C1.79308 2.76861 1.66523 2.91919 1.58709 3.09626C1.50894 3.27332 1.48386 3.46926 1.51488 3.6603C1.54591 3.85134 1.6317 4.02928 1.76186 4.17252L1.76686 4.17814L5.99998 8.69814V13.5C5.99994 13.681 6.04902 13.8586 6.14198 14.0139C6.23494 14.1692 6.36831 14.2963 6.52785 14.3818C6.6874 14.4672 6.86714 14.5078 7.04792 14.4991C7.2287 14.4904 7.40373 14.4329 7.55436 14.3325L9.55436 12.9988C9.69146 12.9074 9.80388 12.7837 9.88162 12.6384C9.95936 12.4932 10 12.331 9.99998 12.1663V8.69814L14.2337 4.17814L14.2387 4.17252C14.3703 4.02993 14.4569 3.85176 14.4878 3.66025C14.5187 3.46873 14.4925 3.27236 14.4125 3.09564ZM9.13623 8.16127C9.04974 8.25297 9.00107 8.37396 8.99998 8.50002V12.1663L6.99998 13.5V8.50002C7.00002 8.37305 6.95176 8.25083 6.86498 8.15814L2.49998 3.50002H13.5L9.13623 8.16127Z" fill="#212121" />
										</svg><?php esc_html_e('Filters', 'somnia') ?>
									</div>
									<div class="bt-product-results">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
											<path d="M14.2806 7.21938C14.3504 7.28903 14.4057 7.37175 14.4434 7.4628C14.4812 7.55384 14.5006 7.65144 14.5006 7.75C14.5006 7.84856 14.4812 7.94616 14.4434 8.03721C14.4057 8.12825 14.3504 8.21097 14.2806 8.28063L9.03063 13.5306C8.96098 13.6004 8.87826 13.6557 8.78721 13.6934C8.69616 13.7312 8.59857 13.7506 8.5 13.7506C8.40144 13.7506 8.30385 13.7312 8.2128 13.6934C8.12175 13.6557 8.03903 13.6004 7.96938 13.5306L5.71938 11.2806C5.57865 11.1399 5.49959 10.949 5.49959 10.75C5.49959 10.551 5.57865 10.3601 5.71938 10.2194C5.86011 10.0786 6.05098 9.99958 6.25 9.99958C6.44903 9.99958 6.6399 10.0786 6.78063 10.2194L8.5 11.9397L13.2194 7.21938C13.289 7.14964 13.3718 7.09432 13.4628 7.05658C13.5538 7.01884 13.6514 6.99941 13.75 6.99941C13.8486 6.99941 13.9462 7.01884 14.0372 7.05658C14.1283 7.09432 14.211 7.14964 14.2806 7.21938ZM19.75 10C19.75 11.9284 19.1782 13.8134 18.1068 15.4168C17.0355 17.0202 15.5127 18.2699 13.7312 19.0078C11.9496 19.7458 9.98919 19.9389 8.09787 19.5627C6.20656 19.1865 4.46928 18.2579 3.10571 16.8943C1.74215 15.5307 0.813554 13.7934 0.437348 11.9021C0.061142 10.0108 0.254225 8.05042 0.992179 6.26884C1.73013 4.48726 2.97982 2.96452 4.58319 1.89317C6.18657 0.821828 8.07164 0.25 10 0.25C12.585 0.25273 15.0634 1.28084 16.8913 3.10872C18.7192 4.93661 19.7473 7.41498 19.75 10ZM18.25 10C18.25 8.3683 17.7662 6.77325 16.8596 5.41655C15.9531 4.05984 14.6646 3.00242 13.1571 2.37799C11.6497 1.75357 9.99085 1.59019 8.39051 1.90852C6.79017 2.22685 5.32016 3.01259 4.16637 4.16637C3.01259 5.32015 2.22685 6.79016 1.90853 8.39051C1.5902 9.99085 1.75358 11.6496 2.378 13.1571C3.00242 14.6646 4.05984 15.9531 5.41655 16.8596C6.77326 17.7661 8.36831 18.25 10 18.25C12.1873 18.2475 14.2843 17.3775 15.8309 15.8309C17.3775 14.2843 18.2475 12.1873 18.25 10Z" fill="#A0A0A0" />
										</svg>
										<div class="bt-results-count">
											<?php
											if ($total_products > 0) {
												$product_text = ($total_products == 1) ? __('%s Product Found', 'somnia') : __('%s Products Found', 'somnia');
												printf(
													$product_text,
													'<span class="highlight">' . esc_html($total_products) . '</span>'
												);
											} else {
												echo esc_html__('No results', 'somnia');
											}
											?>
										</div>
									</div>
								</div>
								<div class="bt-product-view-type">
									<?php
									// Determine $type_active from $rows if $_GET['view_type'] is not set
									if (isset($_GET['view_type'])) {
										$type_active = $_GET['view_type'];
									} else {
										// Map from $rows to $type_active
										switch ($rows) {
											case 1:
												$type_active = 'list';
												break;
											case 2:
												$type_active = 'grid-2';
												break;
											case 3:
												$type_active = 'grid-3';
												break;
											case 4:
												$type_active = 'grid-4';
												break;
											default:
												$type_active = 'grid-3';
												break;
										}
									}
									?>
									<a href="#" class="bt-view-type bt-view-list <?php if ('list' == $type_active) echo 'active'; ?>" data-view="list">
										<div class="bt-icon">
											<span class="bt-dot"></span>
											<span class="bt-dot long"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot long"></span>
										</div>
									</a>
									<a href="#" class="bt-view-type bt-view-grid-2 <?php if ('grid-2' == $type_active) echo 'active'; ?>" data-view="grid-2">
										<div class="bt-icon">
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
										</div>
									</a>
									<a href="#" class="bt-view-type bt-view-grid-3 <?php if ('grid-3' == $type_active) echo 'active'; ?>" data-view="grid-3">
										<div class="bt-icon">
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
										</div>
									</a>
									<a href="#" class="bt-view-type bt-view-grid-4 <?php if ('grid-4' == $type_active) echo 'active'; ?>" data-view="grid-4">
										<div class="bt-icon">
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
											<span class="bt-dot"></span>
										</div>
									</a>
								</div>
								<div class="bt-product-orderby">
									<div class="bt-product-sort-block">
										<span class="bt-sort-title">
											<?php echo esc_html__('Sort by:', 'somnia'); ?>
										</span>
										<div class="bt-sort-field">
											<?php
											$sort_options = array(
												'date_high' => esc_html__('Date: newest first', 'somnia'),
												'date_low' => esc_html__('Date: oldest first', 'somnia'),
												'best_selling' => esc_html__('Best Selling', 'somnia'),
												'average_rating' => esc_html__('Average Rating', 'somnia'),
												'price_high' => esc_html__('Price: highest first', 'somnia'),
												'price_low' => esc_html__('Price: lower first', 'somnia')
											);
											?>
											<select name="sort_order">
												<?php foreach ($sort_options as $key => $value) { ?>
													<?php if (isset($_GET['sort_order']) && $key == $_GET['sort_order']) { ?>
														<?php if ($key == $_GET['sort_order']) { ?>
															<option value="<?php echo esc_attr($key); ?>" selected="selected">
																<?php echo esc_html($value); ?>
															</option>
														<?php } else { ?>
															<option value="<?php echo esc_attr($key); ?>">
																<?php echo esc_html($value); ?>
															</option>
														<?php } ?>
													<?php } else { ?>
														<?php if ($key == 'date_high') { ?>
															<option value="<?php echo esc_attr($key); ?>" selected="selected">
																<?php echo esc_html($value); ?>
															</option>
														<?php } else { ?>
															<option value="<?php echo esc_attr($key); ?>">
																<?php echo esc_html($value); ?>
															</option>
														<?php } ?>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
								<div class="bt-list-tag-filter">
									<a href="#" class="bt-reset-filter-product-btn disable"><?php echo esc_html__('Remove All', 'somnia') ?>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
											<path d="M12 4L4 12" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
											<path d="M4 4L12 12" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</a>
								</div>
							</div>
							<div class="bt-product-layout" data-view="<?php echo esc_attr($type_active); ?>" data-pagination-type="<?php echo esc_attr($pagination_type); ?>">
								<span class="bt-loading-wave"></span>
								<?php
								if ($wp_query->have_posts()) {
									woocommerce_product_loop_start();
									while ($wp_query->have_posts()) {
										$wp_query->the_post();
										wc_get_template_part('content', 'product');
									}
									woocommerce_product_loop_end();

									// Render pagination based on option
									if ($pagination_type === 'button-load-more') {
										echo '<div class="bt-product-pagination-wrap" data-pagination-type="load-more">'
											. somnia_product_load_more_button($current_page, $total_page)
											. '</div>';
									} elseif ($pagination_type === 'infinite-scrolling') {
										echo '<div class="bt-product-pagination-wrap" data-pagination-type="infinite-scroll">';
										if ($current_page < $total_page) {
											echo '<div class="bt-infinite-scroll-trigger" data-page="' . esc_attr($current_page + 1) . '" data-total="' . esc_attr($total_page) . '">';
											echo '<span class="bt-loading-spinner" style="display:none;">' . esc_html__('Loading more...', 'somnia') . '</span>';
											echo '</div>';
										}
										echo '</div>';
									} else {
										// Default pagination
										echo '<div class="bt-product-pagination-wrap" data-pagination-type="default">'
											. somnia_product_pagination($current_page, $total_page)
											. '</div>';
									}
								} else {
									woocommerce_product_loop_start();
									echo '<div class="not-found-products"><svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="currentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.13,104.13,0,0,0,128,24Zm-18.34,98.34a8,8,0,0,1-11.32,11.32L88,123.31,77.66,133.66a8,8,0,0,1-11.32-11.32L76.69,112,66.34,101.66A8,8,0,0,1,77.66,90.34L88,100.69,98.34,90.34a8,8,0,0,1,11.32,11.32L99.31,112ZM128,192a12,12,0,1,1,12-12A12,12,0,0,1,128,192Zm61.66-69.66a8,8,0,0,1-11.32,11.32L168,123.31l-10.34,10.35a8,8,0,0,1-11.32-11.32L156.69,112l-10.35-10.34a8,8,0,0,1,11.32-11.32L168,100.69l10.34-10.35a8,8,0,0,1,11.32,11.32L179.31,112Z"></path></svg>' . esc_html__('Oops! We couldn\'t find any products matching your search. Try adjusting your filters or explore our full collection.', 'somnia') . '
										<a href="#" class="bt-reset-filter-product-btn">' . esc_html__('Reset All Filters', 'somnia') . '</a>
									</div>';
									woocommerce_product_loop_end();
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
<?php }
get_footer('shop');
