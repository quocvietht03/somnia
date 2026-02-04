<?php

/**
 * The Template for displaying products filters
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;
$current_category = null;
$category_slug = '';
$category_name = '';
if (somnia_is_category_archive_page()) {
	$current_category = get_queried_object();
	if ($current_category && isset($current_category->taxonomy) && $current_category->taxonomy === 'product_cat') {
		$category_slug = !empty($current_category->slug) ? $current_category->slug : '';
		$category_name = !empty($current_category->name) ? $current_category->name : '';
	}
}
$current_category = get_queried_object();
// Get taxonomy info for taxonomy pages (not shop or category)
$is_category_page = somnia_is_category_archive_page();
// Only consider it a taxonomy page if there's no query string (not a filter URL)
$query_string = filter_input(INPUT_SERVER, 'QUERY_STRING');
$is_taxonomy_page = !$is_category_page && (is_product_taxonomy() || is_tax()) && empty($query_string);
$current_taxonomy = null;
$taxonomy_slug = '';
$taxonomy_name = '';
$taxonomy_type = '';
if ($is_taxonomy_page) {
	$current_taxonomy = get_queried_object();
	if ($current_taxonomy && isset($current_taxonomy->taxonomy) && $current_taxonomy->taxonomy !== 'product_cat') {
		$taxonomy_slug = !empty($current_taxonomy->slug) ? $current_taxonomy->slug : '';
		$taxonomy_name = !empty($current_taxonomy->name) ? $current_taxonomy->name : '';
		$taxonomy_type = $current_taxonomy->taxonomy;
	}
}
?>
<div class="bt-product-sidebar" 
	<?php if (somnia_is_category_archive_page()) { ?>data-is-category-page="1"<?php } ?>
	<?php if ($category_slug) { ?>data-category-slug="<?php echo esc_attr($category_slug); ?>"<?php } ?>
	<?php if ($category_name) { ?>data-category-name="<?php echo esc_attr($category_name); ?>"<?php } ?>
	<?php if ($is_taxonomy_page) { ?>data-is-taxonomy-page="1"<?php } ?>
	<?php if ($taxonomy_slug) { ?>data-taxonomy-slug="<?php echo esc_attr($taxonomy_slug); ?>"<?php } ?>
	<?php if ($taxonomy_name) { ?>data-taxonomy-name="<?php echo esc_attr($taxonomy_name); ?>"<?php } ?>
	<?php if ($taxonomy_type) { ?>data-taxonomy-type="<?php echo esc_attr($taxonomy_type); ?>"<?php } ?>>
  <form class="bt-product-filter-form" action="" method="get">
    <div class="bt-form-action">
      <h2 class="bt-form-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="currentColor">
          <path d="M25.2223 5.41737C25.0877 5.10621 24.8645 4.84151 24.5806 4.65618C24.2966 4.47086 23.9645 4.37308 23.6255 4.37503H4.37546C4.03676 4.3757 3.70553 4.47464 3.42196 4.65985C3.13838 4.84506 2.91465 5.10858 2.77789 5.41845C2.64114 5.72831 2.59724 6.07121 2.65153 6.40553C2.70582 6.73985 2.85596 7.05123 3.08374 7.3019L3.09249 7.31175L10.5005 15.2217V23.625C10.5004 23.9418 10.5863 24.2526 10.749 24.5243C10.9116 24.7961 11.145 25.0186 11.4242 25.1681C11.7034 25.3176 12.018 25.3886 12.3343 25.3734C12.6507 25.3582 12.957 25.2575 13.2206 25.0819L16.7206 22.7478C16.9605 22.588 17.1573 22.3714 17.2933 22.1172C17.4294 21.8631 17.5005 21.5793 17.5005 21.291V15.2217L24.9095 7.31175L24.9183 7.3019C25.1485 7.05238 25.3 6.74058 25.3541 6.40543C25.4082 6.07028 25.3624 5.72663 25.2223 5.41737ZM15.9889 14.2822C15.8375 14.4427 15.7524 14.6544 15.7505 14.875V21.291L12.2505 23.625V14.875C12.2505 14.6528 12.1661 14.4389 12.0142 14.2767L4.37546 6.12503H23.6255L15.9889 14.2822Z"/>
        </svg>
        <span><?php echo esc_html_e('Filters', 'somnia') ?></span>
      </h2>
      <div class="bt-form-button">
        <a href="#" class="bt-reset-filter-product-btn disable">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="60" height="60" x="0" y="0" viewBox="0 0 512 512" fill="currentColor">
            <path d="M493.815 70.629c-11.001-1.003-20.73 7.102-21.733 18.102l-2.65 29.069C450.473 47.194 346.429 0 256 0 158.719 0 72.988 55.522 30.43 138.854c-5.024 9.837-1.122 21.884 8.715 26.908 9.839 5.024 21.884 1.123 26.908-8.715C102.07 86.523 174.397 40 256 40c74.377 0 141.499 38.731 179.953 99.408l-28.517-20.367c-8.989-6.419-21.48-4.337-27.899 4.651-6.419 8.989-4.337 21.479 4.651 27.899l86.475 61.761c12.674 9.035 30.155 .764 31.541-14.459l9.711-106.53c1.004-11.001-7.1-20.731-18.1-21.734zM472.855 346.238c-9.838-5.023-21.884-1.122-26.908 8.715C409.93 425.477 337.603 472 256 472c-74.377 0-141.499-38.731-179.953-99.408l28.517 20.367c8.989 6.419 21.479 4.337 27.899-4.651 6.419-8.989 4.337-21.479-4.651-27.899l-86.475-61.761c-12.519-8.944-30.141-.921-31.541 14.459L0.085 419.637c-1.003 11 7.102 20.73 18.101 21.733 11.014 1.001 20.731-7.112 21.733-18.102l3.65-29.069C87.527 464.806 165.571 512 256 512c97.281 0 183.012-55.522 225.57-138.854 5.024-9.837 1.122-21.884-8.715-26.908z"/>
          </svg>
        </a>
        <a href="#" class="bt-close-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
            <path d="M9.41183 8L15.6952 1.71665C15.7905 1.62455 15.8666 1.51437 15.9189 1.39255C15.9713 1.27074 15.9988 1.13972 16 1.00714C16.0011 0.874567 15.9759 0.743089 15.9256 0.620381C15.8754 0.497673 15.8013 0.386193 15.7076 0.292444C15.6138 0.198695 15.5023 0.124556 15.3796 0.0743523C15.2569 0.0241486 15.1254 -0.00111435 14.9929 3.76988e-05C14.8603 0.00118975 14.7293 0.0287337 14.6074 0.0810623C14.4856 0.133391 14.3755 0.209456 14.2833 0.30482L8 6.58817L1.71665 0.30482C1.52834 0.122941 1.27612 0.0223015 1.01433 0.0245764C0.752534 0.0268514 0.502106 0.131859 0.316983 0.316983C0.131859 0.502107 0.0268514 0.752534 0.0245764 1.01433C0.0223015 1.27612 0.122941 1.52834 0.30482 1.71665L6.58817 8L0.30482 14.2833C0.209456 14.3755 0.133391 14.4856 0.0810623 14.6074C0.0287337 14.7293 0.00118975 14.8603 3.76988e-05 14.9929C-0.00111435 15.1254 0.0241486 15.2569 0.0743523 15.3796C0.124556 15.5023 0.198695 15.6138 0.292444 15.7076C0.386193 15.8013 0.497673 15.8754 0.620381 15.9256C0.743089 15.9759 0.874567 16.0011 1.00714 16C1.13972 15.9988 1.27074 15.9713 1.39255 15.9189C1.51437 15.8666 1.62455 15.7905 1.71665 15.6952L8 9.41183L14.2833 15.6952C14.4226 15.8358 14.6006 15.9317 14.7945 15.9708C14.9885 16.0098 15.1898 15.9902 15.3726 15.9145C15.5554 15.8388 15.7115 15.7104 15.8211 15.5456C15.9306 15.3808 15.9886 15.1871 15.9877 14.9893C15.9878 14.8581 15.9619 14.7283 15.9117 14.6072C15.8615 14.4861 15.7879 14.376 15.6952 14.2833L9.41183 8Z"/>
          </svg>
        </a>
      </div>
    </div>
    <div class="bt-product-filter-fields">
      <!--Sort order-->
      <input type="hidden" class="bt-product-sort-order" name="sort_order" value="<?php if (isset($_GET['sort_order'])) echo esc_attr($_GET['sort_order']); ?>">

      <!--View current page-->
      <input type="hidden" class="bt-product-current-page" name="current_page" value="<?php echo isset($_GET['current_page']) ? esc_attr($_GET['current_page']) : ''; ?>">
      <!--View type-->
      <input type="hidden" class="bt-product-view-type" name="view_type" value="<?php if (isset($_GET['view_type'])) echo esc_attr($_GET['view_type']); ?>">

      <?php
      // Get custom filters settings from ACF
      $custom_filters = get_field('custom_filters', 'option');
      $enable_arrange = !empty($custom_filters['enable_arrange_filters']) ? $custom_filters['enable_arrange_filters'] : false;
      $arrange_filters = !empty($custom_filters['arrange_filters']) ? $custom_filters['arrange_filters'] : array();

      // Check if arrange filters is enabled and has items
      if ($enable_arrange && !empty($arrange_filters)) {
        // Render filters in custom order
        $rendered_filters = array(); // Track rendered filters to avoid duplicates
        
        foreach ($arrange_filters as $filter) {
          $filter_type = $filter['item_filters'];
          
          // Skip if this filter type has already been rendered
          if (in_array($filter_type, $rendered_filters)) {
            continue;
          }
          
          // Mark this filter type as rendered
          $rendered_filters[] = $filter_type;
          
          switch ($filter_type) {
            case 'search_form':
              ?>
              <div class="bt-form-field bt-field-type-search">
                <input type="text" name="search_keyword" value="<?php if (isset($_GET['search_keyword'])) echo esc_attr($_GET['search_keyword']); ?>" placeholder="<?php esc_attr_e('Search …', 'somnia'); ?>" autocomplete="off">
                <a href="#">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"/>
                  </svg>
                </a>
              </div>
              <?php
              break;

            case 'categories':
              $field_name = __('Product Categories', 'somnia');
              // Check if we're on a product category page
              if (somnia_is_category_archive_page()) {
                $current_category = get_queried_object();
                $field_value = !empty($current_category->slug) ? $current_category->slug : '';
              } else {
                $field_value = (isset($_GET['product_cat'])) ? $_GET['product_cat'] : '';
              }
              somnia_product_field_radio_html('product_cat', $field_name, $field_value );
              break;

            case 'brand':
              $field_name = __('Brand', 'somnia');
              $field_value = (isset($_GET['product_brand'])) ? $_GET['product_brand'] : '';
              somnia_product_field_multiple_html('product_brand', $field_name, $field_value);
              break;

            case 'price':
              $field_title = __('Price', 'somnia');
              $field_min_value = (isset($_GET['min_price'])) ? $_GET['min_price'] : '';
              $field_max_value = (isset($_GET['max_price'])) ? $_GET['max_price'] : '';
              somnia_product_field_price_slider($field_title, $field_min_value, $field_max_value);
              break;

            case 'colors':
              $color_taxonomy = somnia_get_color_taxonomy();
              if ($color_taxonomy) {
                $field_name = __('Colors', 'somnia');
                $field_value = (isset($_GET[$color_taxonomy])) ? $_GET[$color_taxonomy] : '';
                somnia_product_field_multiple_color_html($color_taxonomy, $field_name, $field_value);
              }
              break;

            case 'customer_rating':
              $field_name = __('Customer Rating', 'somnia');
              $field_value = (isset($_GET['product_rating'])) ? $_GET['product_rating'] : '';
              somnia_product_field_rating('product_rating', $field_name, $field_value);
              break;

            case 'text_editor':
              $custom_editor_content = !empty($filter['custom_editor']) ? $filter['custom_editor'] : '';
              if (!empty($custom_editor_content)) {
                ?>
                <div class="bt-form-field bt-field-type-text-editor">
                  <?php echo apply_filters('the_content', $custom_editor_content); ?>
                </div>
                <?php
              }
              break;
          }
        }
      } else {
        // Render filters in default order when arrange is disabled
        ?>
        <div class="bt-form-field bt-field-type-search">
          <input type="text" name="search_keyword" value="<?php if (isset($_GET['search_keyword'])) echo esc_attr($_GET['search_keyword']); ?>" placeholder="<?php esc_attr_e('Search …', 'somnia'); ?>" autocomplete="off">
          <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
              <path d="M19.3013 18.2401L14.6073 13.547C15.9678 11.9136 16.6462 9.81853 16.5014 7.69766C16.3566 5.5768 15.3998 3.5934 13.8299 2.16007C12.26 0.726741 10.1979 -0.0461652 8.07263 0.0021347C5.94738 0.0504346 3.92256 0.916222 2.41939 2.41939C0.916222 3.92256 0.0504346 5.94738 0.0021347 8.07263C-0.0461652 10.1979 0.726741 12.26 2.16007 13.8299C3.5934 15.3998 5.5768 16.3566 7.69766 16.5014C9.81853 16.6462 11.9136 15.9678 13.547 14.6073L18.2401 19.3013C18.3098 19.371 18.3925 19.4263 18.4836 19.464C18.5746 19.5017 18.6722 19.5211 18.7707 19.5211C18.8693 19.5211 18.9669 19.5017 19.0579 19.464C19.1489 19.4263 19.2317 19.371 19.3013 19.3013C19.371 19.2317 19.4263 19.1489 19.464 19.0579C19.5017 18.9669 19.5211 18.8693 19.5211 18.7707C19.5211 18.6722 19.5017 18.5746 19.464 18.4836C19.4263 18.3925 19.371 18.3098 19.3013 18.2401ZM1.52072 8.27072C1.52072 6.9357 1.9166 5.63065 2.6583 4.52062C3.4 3.41059 4.45421 2.54543 5.68761 2.03454C6.92101 1.52364 8.27821 1.38997 9.58758 1.65042C10.897 1.91087 12.0997 2.55375 13.0437 3.49775C13.9877 4.44176 14.6306 5.64449 14.891 6.95386C15.1515 8.26323 15.0178 9.62043 14.5069 10.8538C13.996 12.0872 13.1309 13.1414 12.0208 13.8831C10.9108 14.6248 9.60575 15.0207 8.27072 15.0207C6.48112 15.0187 4.76538 14.3069 3.49994 13.0415C2.2345 11.7761 1.52271 10.0603 1.52072 8.27072Z"/>
            </svg>
          </a>
        </div>
        <?php
        $field_name = __('Product Categories', 'somnia');
        // Check if we're on a product category page
        if (somnia_is_category_archive_page()) {
          $current_category = get_queried_object();
          $field_value = !empty($current_category->slug) ? $current_category->slug : '';
        } else {
          $field_value = (isset($_GET['product_cat'])) ? $_GET['product_cat'] : '';
        }
        somnia_product_field_radio_html('product_cat', $field_name, $field_value);
    
        $field_name = __('Brand', 'somnia');
        $field_value = (isset($_GET['product_brand'])) ? $_GET['product_brand'] : '';
        somnia_product_field_multiple_html('product_brand', $field_name, $field_value);

        $field_title = __('Price', 'somnia');
        $field_min_value = (isset($_GET['min_price'])) ? $_GET['min_price'] : '';
        $field_max_value = (isset($_GET['max_price'])) ? $_GET['max_price'] : '';
        somnia_product_field_price_slider($field_title, $field_min_value, $field_max_value);

        $color_taxonomy = somnia_get_color_taxonomy();
        if ($color_taxonomy) {
          $field_name = __('Colors', 'somnia');
          $field_value = (isset($_GET[$color_taxonomy])) ? $_GET[$color_taxonomy] : '';
          somnia_product_field_multiple_color_html($color_taxonomy, $field_name, $field_value);
        }

        $field_name = __('Customer Rating', 'somnia');
        $field_value = (isset($_GET['product_rating'])) ? $_GET['product_rating'] : '';
        somnia_product_field_rating('product_rating', $field_name, $field_value);
      }
      ?>
      <div class="bt-form-button-results">
        <a href="#" class="bt-reset-filter-product-btn disable"><?php echo esc_html__('Clear All Filters', 'somnia'); ?></a>
        <a href="#" class="bt-product-results-btn">
          <?php
          $total_products = isset($args['total_products']) ? intval($args['total_products']) : 0;
          $product_text = ($total_products == 1) ? __('Show %s Product', 'somnia') : __('Show %s Products', 'somnia');
          printf($total_products > 0 ? $product_text : esc_html__('No products found', 'somnia'), $total_products);
          ?>
        </a>
      </div>
    </div>
  </form>
</div>

<div class="bt-popup-overlay"></div>