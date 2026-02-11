<?php
// WooCommerce custom hooks
add_action('somnia_woocommerce_template_loop_product_link_open', 'woocommerce_template_loop_product_link_open', 10);
add_action('somnia_woocommerce_template_loop_product_link_close', 'woocommerce_template_loop_product_link_close', 5);
add_action('somnia_woocommerce_show_product_loop_sale_flash', 'woocommerce_show_product_loop_sale_flash', 10);

add_action('somnia_woocommerce_template_loop_product_title', 'woocommerce_template_loop_product_title', 10);
add_action('somnia_woocommerce_template_loop_rating', 'woocommerce_template_loop_rating', 5);
add_action('somnia_woocommerce_template_loop_price', 'woocommerce_template_loop_price', 10);
add_action('somnia_woocommerce_template_loop_add_to_cart', 'woocommerce_template_loop_add_to_cart', 10);

add_action('somnia_woocommerce_template_single_title', 'woocommerce_template_single_title', 5);
add_action('somnia_woocommerce_template_single_rating', 'woocommerce_template_single_rating', 10);
add_action('somnia_woocommerce_template_single_price', 'woocommerce_template_single_price', 10);
add_action('somnia_woocommerce_template_single_excerpt', 'woocommerce_template_single_excerpt', 20);
add_action('somnia_woocommerce_template_single_add_to_cart', 'woocommerce_template_single_add_to_cart', 30);
add_action('woocommerce_before_add_to_cart_quantity', 'somnia_size_guide_button_before_quantity', 5);
remove_action('woocommerce_single_product_meta', 'woocommerce_template_single_meta');
add_action('woocommerce_single_product_meta', 'custom_woocommerce_single_product_meta');

add_action('somnia_woocommerce_template_single_sharing', 'woocommerce_template_single_sharing', 50);
add_action('somnia_checkout_review', 'woocommerce_order_review', 10);
add_action('somnia_checkout_order', 'woocommerce_checkout_payment', 20);
add_action('somnia_woocommerce_template_cross_sell', 'woocommerce_cross_sell_display', 50);
add_filter('woocommerce_product_description_heading', '__return_null');

add_action('somnia_woocommerce_template_single_meta', 'somnia_woocommerce_single_product_meta', 40);
add_action('somnia_woocommerce_template_related_products', 'woocommerce_output_related_products', 20);
remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');

/**
 * Filter add to cart link for loop products
 * If AJAX add to cart is disabled, change link to product page
 */
add_filter('woocommerce_loop_add_to_cart_link', 'somnia_woocommerce_loop_add_to_cart_link', 10, 3);
if (!function_exists('somnia_woocommerce_loop_add_to_cart_link')) {
    function somnia_woocommerce_loop_add_to_cart_link($link, $product, $args)
    {
        global $is_ajax_filter_product;

        if (!$product || !($product instanceof WC_Product)) {
            return $link;
        }

        // Check if AJAX add to cart is disabled
        $ajax_add_to_cart_enabled = get_option('woocommerce_enable_ajax_add_to_cart') === 'yes';

        // Check if we're on archive/shop/category pages or AJAX filter is running
        $is_archive_page = is_shop() || is_product_category() || is_product_tag() ||
            (is_archive() && 'product' === get_post_type()) ||
            !empty($is_ajax_filter_product);

        // Only modify if AJAX is disabled, on archive pages, and product is simple type
        if (!$ajax_add_to_cart_enabled && $is_archive_page && $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) {
            $product_url = get_permalink($product->get_id());
            $button_text = $product->add_to_cart_text();

            // Create new link pointing to product page, remove AJAX-related classes and attributes
            $link = sprintf(
                '<a href="%s" class="button product_type_%s bt-btn-add-to-cart-link" rel="nofollow">%s</a>',
                esc_url($product_url),
                esc_attr($product->get_type()),
                esc_html($button_text)
            );
        }

        return $link;
    }
}


remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);

add_action('somnia_woocommerce_template_upsell_products', 'woocommerce_upsell_display', 20);
add_action('woocommerce_before_add_to_cart_quantity', 'somnia_display_frequently_bought_together', 10);

//Remove categories from product loop
add_filter('woocommerce_product_loop_start', function ($html) {
    // Get columns from loop prop, default to 3 if not set
    $columns = absint(max(1, wc_get_loop_prop('columns', 3)));
    // Return original loop start HTML without categories (strip any category HTML that might have been added)
    return '<div class="woocommerce-loop-products products columns-' . esc_attr($columns) . '">';
}, 20);

/**
 * Prevent product_brand, comfort_scale, mattress_type query params from being treated as taxonomy archive
 * Remove from query vars when it's just a filter parameter
 */
function somnia_remove_brand_from_query_vars($query_vars)
{
    if (!is_admin()) {
        if (isset($_GET['product_brand']) && isset($query_vars['product_brand'])) {
            unset($query_vars['product_brand']);
        }
        if (isset($_GET['comfort_scale']) && isset($query_vars['comfort_scale'])) {
            unset($query_vars['comfort_scale']);
        }
        if (isset($_GET['mattress_type']) && isset($query_vars['mattress_type'])) {
            unset($query_vars['mattress_type']);
        }
    }
    return $query_vars;
}
add_filter('request', 'somnia_remove_brand_from_query_vars', 1, 1);

/**
 * Check if we're on a product category archive page (not shop page with query params)
 * Uses queried object to check taxonomy
 * 
 * @return bool True if on category archive page (not shop page)
 */
function somnia_is_category_archive_page()
{
    $current_category = get_queried_object();
    $is_category_page = $current_category && isset($current_category->taxonomy) && $current_category->taxonomy === 'product_cat' && !isset($_GET['product_cat']);

    // Also verify we're not on shop page (shop page with product_cat query param)
    return $is_category_page;
}

// Display categories in sidebar template
function somnia_shop_categories_display()
{
    // Use the helper functions to determine what to show
    if (!somnia_should_show_categories()) {
        return;
    }

    // Determine if we're on shop page or category page to get correct display mode
    $is_category_page = isset($_GET['product_cat']) || somnia_is_category_archive_page();

    if ($is_category_page) {
        $display_type = get_option('woocommerce_category_archive_display', '');
    } else {
        $display_type = get_option('woocommerce_shop_page_display', '');
        if (isset($_GET['layout-shop']) && $_GET['layout-shop'] == 'show-categories') {
            $display_type = 'subcategories';
        }
    }

    $category_output = '';

    if ('subcategories' === $display_type || 'both' === $display_type) {
        ob_start();
        woocommerce_output_product_categories(
            array(
                'parent_id' => somnia_is_category_archive_page() ? get_queried_object_id() : 0,
            )
        );
        $category_output = ob_get_clean();

        // Keep original category permalink links (no modification needed)
    }

    echo wp_kses_post($category_output);
}
add_action('somnia_shop_categories_display', 'somnia_shop_categories_display');

function somnia_should_show_categories()
{
    $shop_page_display = get_option('woocommerce_shop_page_display', '');
    if (isset($_GET['layout-shop']) && $_GET['layout-shop'] == 'show-categories') {
        $shop_page_display = 'subcategories';
    }
    $category_display = get_option('woocommerce_category_archive_display', '');

    // Check if we're on category page
    $is_category_page = isset($_GET['product_cat']) || somnia_is_category_archive_page();

    if ($is_category_page) {
        // Get current category
        $current_category = null;
        if (isset($_GET['product_cat'])) {
            $current_category = get_term_by('slug', $_GET['product_cat'], 'product_cat');
        } elseif (somnia_is_category_archive_page()) {
            $current_category = get_queried_object();
        }

        // Check if current category has children
        if ($current_category) {
            $children = get_terms(array(
                'taxonomy' => 'product_cat',
                'parent' => $current_category->term_id,
                'hide_empty' => false
            ));

            // If no children, don't show categories
            if (empty($children)) {
                return false;
            }
        }

        // Category page: show categories if display is 'subcategories' or 'both'
        return in_array($category_display, ['subcategories', 'both']);
    } else {
        // Shop page: show categories if display is 'subcategories' or 'both'
        return in_array($shop_page_display, ['subcategories', 'both']);
    }
    return false;
}
function somnia_should_show_products()
{
    $shop_page_display = get_option('woocommerce_shop_page_display', '');
    if (isset($_GET['layout-shop']) && $_GET['layout-shop'] == 'show-categories') {
        $shop_page_display = 'subcategories';
    }
    $category_display = get_option('woocommerce_category_archive_display', '');

    // Check if we're on category page
    $is_category_page = isset($_GET['product_cat']) || somnia_is_category_archive_page();

    if ($is_category_page) {
        // Get current category
        $current_category = null;
        if (isset($_GET['product_cat'])) {
            $current_category = get_term_by('slug', $_GET['product_cat'], 'product_cat');
        } elseif (somnia_is_category_archive_page()) {
            $current_category = get_queried_object();
        }

        // Check if current category has no children
        if ($current_category) {
            $children = get_terms(array(
                'taxonomy' => 'product_cat',
                'parent' => $current_category->term_id,
                'hide_empty' => false
            ));

            // If no children, always show products
            if (empty($children)) {
                return true;
            }
        }

        // Category page: show products if display is '' (empty/products only) or 'both'
        return $category_display === '' || $category_display === 'both';
    } else {
        // Shop page: show products if display is '' (empty/products only) or 'both'
        return $shop_page_display === '' || $shop_page_display === 'both';
    }
    return true; // Default to showing products
}

function somnia_register_product_taxonomy()
{
    $labels = [
        'name' => __('Comfort Scale', 'somnia'),
        'singular_name' => __('Comfort Scale', 'somnia'),
        'search_items' => __('Search Comfort Scales', 'somnia'),
        'all_items' => __('All Comfort Scales', 'somnia'),
        'edit_item' => __('Edit Comfort Scale', 'somnia'),
        'update_item' => __('Update Comfort Scale', 'somnia'),
        'add_new_item' => __('Add New Comfort Scale', 'somnia'),
        'new_item_name' => __('New Comfort Scale', 'somnia'),
        'menu_name' => __('Comfort Scale', 'somnia'),
    ];

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => false,
        'query_var' => true,
        'show_in_rest' => true,
        'publicly_queryable' => false,
    );

    register_taxonomy('comfort_scale', array('product'), $args);

    // Mattress Type
    $mattress_labels = [
        'name' => __('Mattress Type', 'somnia'),
        'singular_name' => __('Mattress Type', 'somnia'),
        'search_items' => __('Search Mattress Types', 'somnia'),
        'all_items' => __('All Mattress Types', 'somnia'),
        'edit_item' => __('Edit Mattress Type', 'somnia'),
        'update_item' => __('Update Mattress Type', 'somnia'),
        'add_new_item' => __('Add New Mattress Type', 'somnia'),
        'new_item_name' => __('New Mattress Type', 'somnia'),
        'menu_name' => __('Mattress Type', 'somnia'),
    ];

    $mattress_args = array(
        'hierarchical' => true,
        'labels' => $mattress_labels,
        'show_ui' => true,
        'show_admin_column' => false,
        'query_var' => true,
        'show_in_rest' => true,
        'publicly_queryable' => false,
    );

    register_taxonomy('mattress_type', array('product'), $mattress_args);
}

add_action('init', 'somnia_register_product_taxonomy');

/**
 * Display product meta in single product page
 */
function somnia_woocommerce_single_product_meta()
{
    global $product;

    echo '<ul class="bt-product-meta">';

    $sku = $product->get_sku();
    if ($sku) {
        echo '<li class="sku"><span>' . esc_html__('SKU:', 'somnia') . '</span> ' . esc_html($sku) . '</li>';
    }

    $post = get_post($product->get_id());
    $author_id = $post->post_author;
    $author = get_the_author_meta('display_name', $author_id);
    if ($author) {
        echo '<li class="vendor"><span>' . esc_html__('Vendor:', 'somnia') . '</span> ' . esc_html($author) . '</li>';
    }

    $availability = $product->is_in_stock() ? esc_html__('In stock', 'somnia') : esc_html__('Out of stock', 'somnia');
    echo '<li class="availability"><span>' . esc_html__('Availability:', 'somnia') . '</span> ' . esc_html($availability) . '</li>';
    $terms = get_the_terms($product->get_id(), 'product_cat');
    if ($terms && !is_wp_error($terms)) {
        $cat_links = array();
        foreach ($terms as $term) {
            $term_link = get_term_link($term->term_id, 'product_cat');
            if (!is_wp_error($term_link)) {
                $cat_links[] = '<a href="' . esc_url($term_link) . '">' . esc_html($term->name) . '</a>';
            }
        }
        echo '<li class="categories"><span>' . esc_html__('Categories:', 'somnia') . '</span> ' . implode(', ', $cat_links) . '</li>';
    }
    echo '</ul>';
}

// Size Guide Button - Display before quantity (for simple products or products without size)
function somnia_size_guide_button_before_quantity()
{
    // Only show on single product pages
    if (!is_product()) {
        return;
    }

    global $product;

    // Get product if global is not set
    if (!$product) {
        $product = wc_get_product();
    }

    // Check if product is valid
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    // Check if size guide is enabled for this product
    $enable_size_guide = get_post_meta($product->get_id(), '_enable_size_guide', true);
    if ($enable_size_guide !== 'yes') {
        return; // Size guide is disabled for this product
    }

    // Only show if product doesn't have size variation
    if ($product && $product->is_type('variable')) {
        $attributes = $product->get_variation_attributes();
        foreach ($attributes as $attribute_name => $options) {
            $attr_name_lower = strtolower($attribute_name);
            if (strpos($attr_name_lower, 'size') !== false) {
                return; // Has size variation, button will show inline in variable.php template
            }
        }
    }

    $size_guide = get_field('size_guide', 'option');

    if (!empty($size_guide)) {
?>
        <div class="bt-size-guide-wrapper">
            <a href="#bt-size-guide-popup" class="bt-size-guide-button bt-js-open-popup-link">
                <?php echo esc_html__('Size Guide', 'somnia'); ?>
            </a>
        </div>
    <?php
    }
}

// Size Guide Popup Content
function somnia_size_guide_popup_content()
{
    if (!is_product()) {
        return;
    }

    global $product;

    // Check if size guide is enabled for this product
    $enable_size_guide = get_post_meta($product->get_id(), '_enable_size_guide', true);
    if ($enable_size_guide !== 'yes') {
        return; // Size guide is disabled for this product
    }

    $size_guide = get_field('size_guide', 'option');

    if (!empty($size_guide)) {
    ?>
        <div id="bt-size-guide-popup" class="bt-size-guide-popup mfp-content__popup mfp-hide ">
            <div class="bt-size-guide-popup-content mfp-content__inner">
                <div class="bt-size-guide-popup-header">
                    <h3><?php echo esc_html__('Size Guide', 'somnia'); ?></h3>
                </div>
                <div class="bt-size-guide-popup-body">
                    <?php echo wp_kses_post($size_guide); ?>
                </div>
            </div>
        </div>
    <?php
    }
}
add_action('wp_footer', 'somnia_size_guide_popup_content');

// custom product loop image
add_action('somnia_woocommerce_template_loop_product_thumbnail', 'somnia_woocommerce_template_loop_product_thumbnail', 10);

function somnia_woocommerce_template_loop_product_thumbnail()
{
    global $product;

    // Get product if global is not set
    if (!$product) {
        $product = wc_get_product();
    }

    // Check if product is valid
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    $post_thumbnail_id = $product->get_image_id();
    echo '<div class="bt-product-images-wrapper">';
    if ($post_thumbnail_id) {
        // Always show main image
        $html = somnia_get_gallery_image_html($post_thumbnail_id, false, false);

        // If there are gallery images, show the first one
        $attachment_ids = $product->get_gallery_image_ids();

        if (!empty($attachment_ids) && isset($attachment_ids[0])) {
            $html .= somnia_get_gallery_image_html($attachment_ids[0], false, false);
        } else {
            // If no gallery images, show main image again
            $html .= somnia_get_gallery_image_html($post_thumbnail_id, false, false);
        }

        echo apply_filters('woocommerce_loop_product_image_thumbnail_html', $html, $post_thumbnail_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
    } else {
        $wrapper_classname = $product->is_type('variable') && ! empty($product->get_available_variations('image')) ?
            'woocommerce-product-gallery__image woocommerce-product-gallery__image--placeholder' :
            'woocommerce-product-gallery__image--placeholder';
        $html = sprintf('<div class="%s">', esc_attr($wrapper_classname));
        $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_thumbnail')), esc_html__('Awaiting product image', 'somnia'));
        $html .= '</div>';

        echo apply_filters('woocommerce_loop_product_image_thumbnail_html', $html, $post_thumbnail_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
    }
    echo '</div>';
}

add_action('woocommerce_cart_updated', 'somnia_redirect_after_add_to_cart');

function somnia_redirect_after_add_to_cart()
{
    if (WC()->session->get('redirect_after_add_to_cart')) {
        WC()->session->__unset('redirect_after_add_to_cart');
        wp_redirect(wc_get_cart_url());
        exit();
    }
}

// Add sale marquee to product loop
add_action('somnia_template_loop_product_countdown_and_sale', 'somnia_display_product_countdown_and_sale_marquee', 10);

function somnia_display_product_countdown_and_sale_marquee()
{
    global $product;

    // Check for countdown timer first (priority)
    $enable_countdown = get_post_meta($product->get_id(), '_enable_loop_countdown', true);
    $time = get_post_meta($product->get_id(), '_product_datetime', true);
    $stock_status = $product->get_stock_status();
    $has_countdown = ($enable_countdown === 'yes' && $time && $stock_status != 'outofstock');

    if ($has_countdown) {
        // Show countdown timer
        $timezone = new DateTimeZone(wp_timezone_string());
        $current_time = new DateTime('now', $timezone);
        $current_time = $current_time->format('Y-m-d H:i:s');

        $time = strtotime($time);
        $time = date('Y-m-d H:i:s', $time);
        if ($current_time > $time) {
            return;
        }
    ?>
        <div class="bt-product-countdown-timer bt-countdown-product-sale">
            <div class="bt-countdown bt-countdown-product-js"
                data-idproduct="<?php echo esc_attr($product->get_id()); ?>"
                data-time="<?php echo esc_attr($time); ?>" data-current-time="<?php echo esc_attr($current_time); ?>">
                <div class="bt-countdown--item">
                    <span class="bt-countdown--digits bt-countdown-days">--</span>
                    <span class="bt-countdown--label"><?php esc_html_e('Days', 'somnia'); ?></span>
                </div>
                <div class="bt-delimiter">:</div>
                <div class="bt-countdown--item">
                    <span class="bt-countdown--digits bt-countdown-hours">--</span>
                    <span class="bt-countdown--label"><?php esc_html_e('Hours', 'somnia'); ?></span>
                </div>
                <div class="bt-delimiter">:</div>
                <div class="bt-countdown--item">
                    <span class="bt-countdown--digits bt-countdown-mins">--</span>
                    <span class="bt-countdown--label"><?php esc_html_e('Mins', 'somnia'); ?></span>
                </div>
                <div class="bt-delimiter">:</div>
                <div class="bt-countdown--item">
                    <span class="bt-countdown--digits bt-countdown-secs">--</span>
                    <span class="bt-countdown--label"><?php esc_html_e('Secs', 'somnia'); ?></span>
                </div>
            </div>
        </div>
        <?php
    } elseif ($product->is_on_sale()) {
        // Show sale marquee if no countdown
        $enable_sale_marquee = get_post_meta($product->get_id(), '_enable_loop_sale_marquee', true);

        if ($enable_sale_marquee === 'yes') {
            // Calculate percentage using the helper function logic
            $percentage = '';

            if ($product->is_type('variable')) {
                $percentages = array();

                // Get all variation prices
                $prices = $product->get_variation_prices();

                // Loop through variation prices
                foreach ($prices['price'] as $key => $price) {
                    // Only on sale variations
                    if ($prices['regular_price'][$key] !== $price) {
                        // Calculate and set in the array the percentage for each variation on sale
                        $percentages[] = round(100 - ($prices['sale_price'][$key] / $prices['regular_price'][$key] * 100));
                    }
                }
                // We keep the highest value
                $percentage = !empty($percentages) ? max($percentages) : 0;
            } elseif ($product->is_type('grouped')) {
                $percentages = array();

                $children = $product->get_children();
                if (!empty($children)) {
                    foreach ($children as $child_id) {
                        $child = wc_get_product($child_id);
                        if ($child && $child->get_sale_price()) {
                            $regular_price = (float)$child->get_regular_price();
                            $sale_price = (float)$child->get_sale_price();
                            if ($regular_price > 0) {
                                $percentages[] = round(100 - ($sale_price / $regular_price * 100));
                            }
                        }
                    }
                }

                $percentage = !empty($percentages) ? max($percentages) : 0;
            } else {
                $regular_price = (float) $product->get_regular_price();
                $sale_price = (float) $product->get_sale_price();

                if ($regular_price > 0 && $sale_price > 0) {
                    $percentage = round(100 - ($sale_price / $regular_price * 100));
                }
            }

            if ($percentage > 0) {
                $sale_text = sprintf(
                    '%s <span class="on-sale">%s%%</span> %s',
                    esc_html__('Hot Sale', 'somnia'),
                    esc_html($percentage),
                    esc_html__('off', 'somnia')
                );

                // Generate marquee items
                $marquee_items = '';
                for ($i = 0; $i < 5; $i++) {
                    $marquee_items .= '<div class="bt-marquee-item">' . wp_kses_post($sale_text) . '</div>';
                    $marquee_items .= '<div class="bt-marquee-separator"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="23" viewBox="0 0 17 23" fill="none">
                        <path d="M16.2991 11.0156L5.79911 22.2656C5.68784 22.3844 5.54097 22.4637 5.38065 22.4917C5.22034 22.5196 5.05528 22.4947 4.91039 22.4206C4.7655 22.3465 4.64863 22.2273 4.57743 22.0809C4.50623 21.9346 4.48455 21.7691 4.51568 21.6094L5.89005 14.7347L0.487238 12.7059C0.371205 12.6625 0.267731 12.5911 0.186059 12.4979C0.104388 12.4048 0.0470632 12.2928 0.0192066 12.1721C-0.00865008 12.0514 -0.0061709 11.9257 0.0264224 11.8061C0.0590157 11.6866 0.120708 11.577 0.205988 11.4872L10.706 0.237181C10.8173 0.118433 10.9641 0.0390974 11.1244 0.0111465C11.2848 -0.0168043 11.4498 0.00814581 11.5947 0.082232C11.7396 0.156318 11.8565 0.27552 11.9277 0.421851C11.9989 0.568182 12.0205 0.7337 11.9894 0.893431L10.6113 7.77562L16.0141 9.80156C16.1293 9.84525 16.2319 9.91664 16.313 10.0094C16.394 10.1022 16.4509 10.2135 16.4787 10.3335C16.5065 10.4535 16.5044 10.5785 16.4724 10.6975C16.4404 10.8165 16.3796 10.9257 16.2954 11.0156H16.2991Z" fill="white"/>
                    </svg></div>';
                }
        ?>
                <div class="bt-product-sale-marquee">
                    <div class="bt-marquee">
                        <?php echo '<div class="bt-marquee-items">' . $marquee_items . '</div>'; ?>
                    </div>
                    <div class="bt-marquee">
                        <?php echo '<div class="bt-marquee-items">' . $marquee_items . '</div>'; ?>
                    </div>
                </div>
    <?php
            }
        }
    }
}

// WooCommerce percentage flash
add_filter('woocommerce_sale_flash', 'somnia_woocommerce_percentage_sale', 10, 3);

function somnia_woocommerce_percentage_sale($html, $post, $product)
{
    if ($product->is_type('variable')) {
        $percentages = array();

        // Get all variation prices
        $prices = $product->get_variation_prices();

        // Loop through variation prices
        foreach ($prices['price'] as $key => $price) {
            // Only on sale variations
            if ($prices['regular_price'][$key] !== $price) {
                // Calculate and set in the array the percentage for each variation on sale
                $percentages[] = round(100 - ($prices['sale_price'][$key] / $prices['regular_price'][$key] * 100));
            }
        }
        // We keep the highest value
        $percentage = max($percentages) . '%';
    } elseif ($product->is_type('grouped')) {
        $percentages = array();

        $children = $product->get_children();
        if (!empty($children)) {
            foreach ($children as $child_id) {
                $child = wc_get_product($child_id);
                if ($child && $child->get_sale_price()) {
                    $regular_price = (float)$child->get_regular_price();
                    $sale_price = (float)$child->get_sale_price();
                    if ($regular_price > 0) {
                        $percentages[] = round(100 - ($sale_price / $regular_price * 100));
                    }
                }
            }
        }

        $percentage = !empty($percentages) ? max($percentages) . '%' : '0%';
    } else {
        $regular_price = (float) $product->get_regular_price();
        $sale_price = (float) $product->get_sale_price();

        $percentage = round(100 - ($sale_price / $regular_price * 100)) . '%';
    }

    if (is_product() && is_single()) {
        return '<span class="onsale">-' . $percentage . ' ' . esc_html__('off', 'somnia') . '</span>';
    } else {
        return '<span class="onsale">-' . $percentage . '</span>';
    }
}

add_filter('woocommerce_pagination_args', 'somnia_woocommerce_pagination_args');

function somnia_woocommerce_pagination_args()
{
    $total = isset($total) ? $total : wc_get_loop_prop('total_pages');
    $current = isset($current) ? $current : wc_get_loop_prop('current_page');
    $base = isset($base) ? $base : esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false))));
    $format = isset($format) ? $format : '';

    if ($total <= 1) {
        return;
    }

    return array(
        'base' => $base,
        'format' => $format,
        'total' => $total,
        'current' => $current,
        'mid_size' => 1,
        'add_args' => false,
        'prev_text' => '<svg width="19" height="16" viewBox="0 0 19 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                      <path d="M9.71889 15.782L10.4536 15.0749C10.6275 14.9076 10.6275 14.6362 10.4536 14.4688L4.69684 8.92851L17.3672 8.92852C17.6131 8.92852 17.8125 8.73662 17.8125 8.49994L17.8125 7.49994C17.8125 7.26326 17.6131 7.07137 17.3672 7.07137L4.69684 7.07137L10.4536 1.53101C10.6275 1.36366 10.6275 1.0923 10.4536 0.924907L9.71889 0.2178C9.545 0.0504438 9.26304 0.0504438 9.08911 0.2178L1.31792 7.69691C1.14403 7.86426 1.14403 8.13562 1.31792 8.30301L9.08914 15.782C9.26304 15.9494 9.545 15.9494 9.71889 15.782Z"/>
                    </svg> ' . esc_html__('Prev', 'somnia'),
        'next_text' => esc_html__('Next', 'somnia') . '<svg width="19" height="16" viewBox="0 0 19 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.28111 0.217951L8.54638 0.925058C8.37249 1.09242 8.37249 1.36377 8.54638 1.53117L14.3032 7.07149L1.63283 7.07149C1.38691 7.07149 1.18752 7.26338 1.18752 7.50006L1.18752 8.50006C1.18752 8.73674 1.38691 8.92863 1.63283 8.92863L14.3032 8.92863L8.54638 14.469C8.37249 14.6363 8.37249 14.9077 8.54638 15.0751L9.28111 15.7822C9.455 15.9496 9.73696 15.9496 9.91089 15.7822L17.6821 8.30309C17.856 8.13574 17.856 7.86438 17.6821 7.69699L9.91086 0.217952C9.73696 0.0505587 9.455 0.0505586 9.28111 0.217951Z"/>
                  </svg>',
    );
}

// WooCommerce ralated params
add_filter('woocommerce_output_related_products_args', 'somnia_woocommerce_related_products_args', 20);

function somnia_woocommerce_related_products_args($args)
{
    if (function_exists('get_field')) {
        $related_posts = get_field('product_related_posts', 'options');
        $args['posts_per_page'] = !empty($related_posts['number_posts']) ? $related_posts['number_posts'] : 4;
    } else {
        $args['posts_per_page'] = 4;
    }

    $args['columns'] = 4;
    return $args;
}

/* Remove After Single Product Summary */
function somnia_remove_after_single_product_summary()
{
    if (function_exists('get_field')) {
        $related_posts = get_field('product_related_posts', 'options');

        $enable_related_product = isset($related_posts['enable_related_product']) ? $related_posts['enable_related_product'] : false;

        if (!$enable_related_product) {
            remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
        }
    }
}
add_action('init', 'somnia_remove_after_single_product_summary');

/* Sold Product */
function somnia_woocommerce_item_sold($product_id)
{
    global $post;
    $args = array(
        'status' => 'completed',
        'limit' => -1,
    );
    $orders = wc_get_orders($args);

    $total_quantity_sold = 0;
    if (!empty($orders)) {
        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                if ($item->get_product_id() == $product_id) {
                    $total_quantity_sold += $item->get_quantity();
                }
            }
        }
    }
    $quantity_sold_option = get_post_meta($post->ID, '_product_sold', true);
    if ($quantity_sold_option != '' && $quantity_sold_option > 0) {
        $total_quantity_sold = $quantity_sold_option;
    }
    echo '<div class="woocommerce-loop-product__sold">';

    echo '<svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
  <path d="M17.106 9.80077L8.35603 19.1758C8.26331 19.2747 8.14091 19.3408 8.00731 19.3641C7.87372 19.3874 7.73617 19.3666 7.61543 19.3049C7.49468 19.2432 7.3973 19.1438 7.33796 19.0219C7.27863 18.8999 7.26057 18.762 7.2865 18.6289L8.43181 12.9L3.92947 11.2094C3.83277 11.1732 3.74655 11.1136 3.67849 11.036C3.61043 10.9584 3.56266 10.8651 3.53944 10.7645C3.51623 10.6639 3.5183 10.5591 3.54546 10.4595C3.57262 10.3599 3.62403 10.2686 3.69509 10.1937L12.4451 0.818744C12.5378 0.719788 12.6602 0.653675 12.7938 0.630383C12.9274 0.60709 13.065 0.627882 13.1857 0.68962C13.3064 0.751359 13.4038 0.850694 13.4632 0.972636C13.5225 1.09458 13.5406 1.23251 13.5146 1.36562L12.3662 7.10077L16.8685 8.78906C16.9645 8.82547 17.05 8.88496 17.1176 8.96228C17.1851 9.0396 17.2326 9.13236 17.2557 9.23237C17.2789 9.33237 17.2771 9.43655 17.2504 9.53569C17.2238 9.63482 17.1731 9.72587 17.1029 9.80077H17.106Z" fill="#C72929"/>
</svg>' . esc_html($total_quantity_sold);
    if ($total_quantity_sold > 1) {
        echo esc_html__(' items sold', 'somnia');
    } else {
        echo esc_html__(' item sold', 'somnia');
    }
    echo '</div>';
}

add_action('somnia_woocommerce_shop_loop_item_sold', 'somnia_woocommerce_item_sold', 10, 2);

/* Add Sold Product affer Quanty Single Product */
function somnia_display_sold_after_rating()
{
    global $product;

    // Get product if global is not set
    if (!$product) {
        $product = wc_get_product();
    }

    // Check if product is valid
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    somnia_woocommerce_item_sold($product->get_id());
}

add_action('somnia_woocommerce_template_single_rating', 'somnia_display_sold_after_rating', 15);
/* custom the "Additional information" tab title */
add_filter('woocommerce_product_tabs', 'somnia_woocommerce_custom_additional_information_tab_title');

function somnia_woocommerce_custom_additional_information_tab_title($tabs)
{
    if (isset($tabs['additional_information'])) {
        $tabs['additional_information']['title'] = esc_html__('Information', 'somnia');
    }
    return $tabs;
}


// Add meta box for review title
function somnia_add_review_title_meta_box()
{
    add_meta_box(
        'review_title_meta_box',
        __('Review Title', 'somnia'),
        'somnia_render_review_title_meta_box',
        'comment',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes_comment', 'somnia_add_review_title_meta_box');

// Render meta box content
function somnia_render_review_title_meta_box($comment)
{
    $review_title = get_comment_meta($comment->comment_ID, 'review_title', true);
    wp_nonce_field('somnia_review_title_update', 'review_title_nonce');
    ?>
    <p>
        <label for="review_title"><?php esc_html_e('Review Title', 'somnia'); ?></label><br>
        <input type="text" id="review_title" name="review_title" value="<?php echo esc_attr($review_title); ?>" size="30" maxlength="100">
    </p>
    <?php
}

// Save review title from both admin and frontend
function somnia_save_review_title($comment_id)
{
    // For admin form submission
    if (isset($_POST['review_title_nonce']) && wp_verify_nonce($_POST['review_title_nonce'], 'somnia_review_title_update')) {
        if (isset($_POST['review_title'])) {
            $review_title = sanitize_text_field($_POST['review_title']);
            update_comment_meta($comment_id, 'review_title', $review_title);
        }
    }
    // For frontend form submission
    elseif (isset($_POST['comment_post_ID'])) {
        if (isset($_POST['review_title'])) {
            $review_title = sanitize_text_field($_POST['review_title']);
            update_comment_meta($comment_id, 'review_title', $review_title);
        }
    }
}

add_action('comment_post', 'somnia_save_review_title');
add_action('edit_comment', 'somnia_save_review_title');

/* auto update mini cart */
add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_icon_add_to_cart_fragment');
if (!function_exists('woocommerce_icon_add_to_cart_fragment')) {
    function woocommerce_icon_add_to_cart_fragment($fragments)
    {
        global $woocommerce;
        ob_start();
    ?>
        <span class="cart_total"><?php echo esc_html($woocommerce->cart->cart_contents_count); ?></span>
    <?php
        $fragments['span.cart_total'] = ob_get_clean();
        return $fragments;
    }
}

/* Create Product Wishlist Page And Compare Page */
function somnia_product_create_pages_support()
{
    $product_wishlist_page = get_posts(array(
        'title' => 'Products Wishlist',
        'post_type' => 'page',
        'post_status' => 'any'
    ));

    if (count($product_wishlist_page) == 0) {
        wp_insert_post(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'Products Wishlist',
            'post_content' => 'Products Wishlist Page.',
            'post_name' => 'products-wishlist',
        ));
    }

    $product_compare_page = get_posts(array(
        'title' => 'Products Compare',
        'post_type' => 'page',
        'post_status' => 'any'
    ));

    if (count($product_compare_page) == 0) {
        wp_insert_post(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'Products Compare',
            'post_content' => 'Products Compare Page.',
            'post_name' => 'products-compare',
        ));
    }
}

add_action('init', 'somnia_product_create_pages_support', 1);

function somnia_get_products_by_rating($rating)
{
    $args = [
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => '_wc_average_rating',
                'value' => $rating,
                'compare' => '=',
                'type' => 'NUMERIC',
            ],
        ],
    ];

    // Check if we're on a product category page
    if (somnia_is_category_archive_page()) {
        $current_category = get_queried_object();
        if ($current_category && isset($current_category->term_id)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $current_category->term_id,
                ],
            ];
        }
    }
    $query = new WP_Query($args);
    return '(' . $query->found_posts . ')';
}

/* Helper function to recursively get all nested children */
function somnia_get_nested_category_children($parent_id, $taxonomy, $all_terms = null)
{
    if ($all_terms === null) {
        $all_terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
        ));

        if (is_wp_error($all_terms) || empty($all_terms)) {
            return array();
        }
    }

    $children = array();
    foreach ($all_terms as $term) {
        if (isset($term->parent) && intval($term->parent) === intval($parent_id)) {
            $children[] = $term;
            // Recursively get children of this child
            $grandchildren = somnia_get_nested_category_children($term->term_id, $taxonomy, $all_terms);
            if (!empty($grandchildren)) {
                $term->children = $grandchildren;
            }
        }
    }

    return $children;
}

/* Helper function to recursively render nested categories */
function somnia_render_nested_category($term, $slug, $field_value, $parent_url = '', $level = 0)
{
    $is_checked = ($term->slug == $field_value);
    $has_children = !empty($term->children);
    $url_category = '';

    if ($slug === 'product_cat') {
        // If on category page, always create URL for all categories to redirect
        if (somnia_is_category_archive_page()) {
            // Use category permalink for redirect
            $url_category = get_term_link($term->term_id, 'product_cat');
            if (is_wp_error($url_category)) {
                $url_category = '';
            }
        } else {
            // On shop page, use category permalink
            $is_category_page = isset($_GET['product_cat']);
            if ($is_category_page) {
                $category_display = get_option('woocommerce_category_archive_display', '');
                if ($category_display == 'both') {
                    $term_link = get_term_link($term->term_id, 'product_cat');
                    if (!is_wp_error($term_link)) {
                        $url_category = $term_link;
                    }
                }
            } else {
                $shop_page_display = get_option('woocommerce_shop_page_display', '');
                if ($shop_page_display == 'both') {
                    $term_link = get_term_link($term->term_id, 'product_cat');
                    if (!is_wp_error($term_link)) {
                        $url_category = $term_link;
                    }
                }
            }
        }
    }

    $item_class = 'item-radio';
    if ($level > 0) {
        $item_class .= ' item-radio-child';
    }
    if ($has_children) {
        $item_class .= ' has-children';
    }
    ?>
    <div class="<?php echo esc_attr($item_class); ?>" <?php if (!empty($url_category)) { ?>data-url="<?php echo esc_attr($url_category); ?>" <?php } ?>>
        <?php if ($is_checked) { ?>
            <input type="radio" name="<?php echo esc_attr($slug); ?>" id="<?php echo esc_attr($term->slug); ?>" value="<?php echo esc_attr($term->slug); ?>" checked>
        <?php } else { ?>
            <input type="radio" name="<?php echo esc_attr($slug); ?>" id="<?php echo esc_attr($term->slug); ?>" value="<?php echo esc_attr($term->slug); ?>">
        <?php } ?>
        <label for="<?php echo esc_attr($term->slug); ?>" data-slug="<?php echo esc_attr($term->slug); ?>">
            <?php echo esc_html($term->name); ?>
        </label>
        <span class="bt-count"><?php echo '(' . $term->count . ')'; ?></span>

        <?php if ($has_children) { ?>
            <span class="bt-toggle-children">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <div class="bt-children-categories">
                <?php foreach ($term->children as $child) {
                    somnia_render_nested_category($child, $slug, $field_value, $url_category, $level + 1);
                } ?>
            </div>
        <?php } ?>
    </div>
    <?php
}

/* Field Product */
function somnia_product_field_radio_html($slug = '', $field_title = '', $field_value = '')
{
    if (empty($slug)) {
        return;
    }

    // Get settings for product_cat from ACF
    $category_mode = 'none'; // default
    $custom_categories = array();


    if ($slug === 'product_cat') {
        $custom_filters = get_field('custom_filters', 'option');
        $category_mode = !empty($custom_filters['setting_product_category']) ? $custom_filters['setting_product_category'] : 'none';
        $custom_categories = !empty($custom_filters['select_category_product_custom']) ? $custom_filters['select_category_product_custom'] : array();
    }

    // Get terms based on mode
    $terms_args = array(
        'taxonomy' => $slug,
        'hide_empty' => true,
        'parent'   => 0
    );

    // If custom mode and has custom categories, override
    if ($slug === 'product_cat' && $category_mode === 'custom' && !empty($custom_categories)) {
        $terms_args = array(
            'taxonomy' => $slug,
            'hide_empty' => true,
            'include' => $custom_categories
        );
    }

    $terms = get_terms($terms_args);
    $field_title_default = !empty($field_title) ? $field_title : 'Choose';

    if (!empty($terms) && !is_wp_error($terms)) {
        // If mode is 'parent', get nested children for all terms
        if ($slug === 'product_cat' && $category_mode === 'parent') {
            // Get all terms to build nested structure
            $all_terms = get_terms(array(
                'taxonomy' => $slug,
                'hide_empty' => true,
            ));

            // Only proceed if we got valid terms
            if (!is_wp_error($all_terms) && !empty($all_terms)) {
                // Build nested structure for each parent term
                foreach ($terms as $term) {
                    $term->children = somnia_get_nested_category_children($term->term_id, $slug, $all_terms);
                }
            }
        }
    ?>
        <div class="bt-form-field bt-field-type-radio <?php echo 'bt-field-' . esc_attr($slug); ?> bt-field-mode-<?php echo esc_attr($category_mode); ?>" data-name="<?php echo esc_attr($slug); ?>">
            <div class="bt-field-title">
                <?php echo esc_html($field_title_default) ?>
                <span class="bt-field-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M13.5326 6.52927L8.53255 11.5293C8.46287 11.5992 8.38008 11.6547 8.28892 11.6925C8.19775 11.7304 8.10001 11.7499 8.0013 11.7499C7.90259 11.7499 7.80485 11.7304 7.71369 11.6925C7.62252 11.6547 7.53973 11.5992 7.47005 11.5293L2.47005 6.52927C2.32915 6.38837 2.25 6.19728 2.25 5.99802C2.25 5.79876 2.32915 5.60767 2.47005 5.46677C2.61095 5.32587 2.80204 5.24672 3.0013 5.24672C3.20056 5.24672 3.39165 5.32587 3.53255 5.46677L8.00193 9.93614L12.4713 5.46615C12.6122 5.32525 12.8033 5.24609 13.0026 5.24609C13.2018 5.24609 13.3929 5.32525 13.5338 5.46615C13.6747 5.60704 13.7539 5.79814 13.7539 5.9974C13.7539 6.19665 13.6747 6.38775 13.5338 6.52865L13.5326 6.52927Z" />
                    </svg>
                </span>
            </div>
            <div class="bt-field-list">
                <?php foreach ($terms as $term) {
                    somnia_render_nested_category($term, $slug, $field_value, '', 0);
                } ?>
            </div>
        </div>
    <?php
    }
}

/**
 * Get product count for a term within current category (if on category page)
 * 
 * @param object $term Term object
 * @param string $taxonomy Taxonomy name
 * @return int Product count
 */
function somnia_get_term_count_in_category($term, $taxonomy)
{
    // If not on category page, return default count
    if (!somnia_is_category_archive_page()) {
        return $term->count;
    }

    // Get current category
    $current_category = get_queried_object();
    if (!$current_category || empty($current_category->slug)) {
        return $term->count;
    }

    // Query products in category with this term
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $current_category->slug,
            ),
            array(
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => $term->term_id,
            ),
        ),
    );

    $query = new WP_Query($args);
    return $query->found_posts;
}

function somnia_product_field_multiple_html($slug = '', $field_title = '', $field_value = '')
{
    if (empty($slug)) {
        return;
    }

    // Check if this is a product attribute taxonomy and if it's enabled in search
    if (strpos($slug, 'pa_') === 0) {
        if (!function_exists('somnia_is_attribute_enabled_in_search')) {
            return;
        }
        if (!somnia_is_attribute_enabled_in_search($slug)) {
            return;
        }
    }

    $terms = get_terms(array(
        'taxonomy' => $slug,
        'hide_empty' => true
    ));


    if (!empty($terms) && !is_wp_error($terms)) {
        // First, check if there's at least one term with count > 0
        $has_valid_terms = false;

        foreach ($terms as $term) {
            // Calculate count - if on category page, count products in that category
            $term_count = somnia_get_term_count_in_category($term, $slug);
            // Check if count > 0
            if ($term_count > 0) {
                $has_valid_terms = true;
                break;
            }
        }

        // If no terms have count > 0, don't show the field
        if (!$has_valid_terms) {
            return;
        }
    ?>
        <div class="bt-form-field bt-field-type-multi" data-name="<?php echo esc_attr($slug); ?>">
            <?php
            if (!empty($field_title)) {
                echo '<div class="bt-field-title">' . $field_title . '<span class="bt-field-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M13.5326 6.52927L8.53255 11.5293C8.46287 11.5992 8.38008 11.6547 8.28892 11.6925C8.19775 11.7304 8.10001 11.7499 8.0013 11.7499C7.90259 11.7499 7.80485 11.7304 7.71369 11.6925C7.62252 11.6547 7.53973 11.5992 7.47005 11.5293L2.47005 6.52927C2.32915 6.38837 2.25 6.19728 2.25 5.99802C2.25 5.79876 2.32915 5.60767 2.47005 5.46677C2.61095 5.32587 2.80204 5.24672 3.0013 5.24672C3.20056 5.24672 3.39165 5.32587 3.53255 5.46677L8.00193 9.93614L12.4713 5.46615C12.6122 5.32525 12.8033 5.24609 13.0026 5.24609C13.2018 5.24609 13.3929 5.32525 13.5338 5.46615C13.6747 5.60704 13.7539 5.79814 13.7539 5.9974C13.7539 6.19665 13.6747 6.38775 13.5338 6.52865L13.5326 6.52927Z"/></svg></span></div>';
            }
            ?>

            <div class="bt-field-list">
                <?php foreach ($terms as $term) {
                    // Calculate count - if on category page, count products in that category
                    $term_count = somnia_get_term_count_in_category($term, $slug);
                    // Only show if count > 0
                    if ($term_count <= 0) {
                        continue;
                    }
                ?>
                    <div class="<?php echo (str_contains($field_value, $term->slug)) ? 'bt-field-item checked' : 'bt-field-item' ?>">
                        <a href="#" data-slug="<?php echo esc_attr($term->slug); ?>">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" viewBox="0 0 33 33" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M28.1489 8.44723C28.6566 8.98059 28.6358 9.82456 28.1025 10.3323L12.6951 24.9989C12.4319 25.2494 12.078 25.3817 11.7151 25.3652C11.3522 25.3486 11.0118 25.1848 10.7725 24.9114L4.8466 18.1422C4.36156 17.5882 4.41752 16.7458 4.97159 16.2607C5.52565 15.7757 6.36802 15.8317 6.85306 16.3857L11.8633 22.109L26.2639 8.4008C26.7972 7.89308 27.6412 7.91387 28.1489 8.44723Z" fill="white" />
                                </svg>
                            </span>
                            <?php
                            echo esc_html($term->name);
                            // If this is an attribute taxonomy, append description
                            if (strpos($slug, 'pa_') === 0 && !empty($term->description)) {
                                echo ' - ' . esc_html($term->description);
                            }
                            ?>
                            <div class="bt-count"><?php echo '(' . $term_count . ')'; ?></div>
                        </a>
                    </div>
                <?php } ?>
            </div>

            <input type="hidden" name="<?php echo esc_attr($slug); ?>" value="<?php echo esc_attr($field_value); ?>">
        </div>
    <?php
    }
}

function somnia_product_field_multiple_color_html($slug = '', $field_title = '', $field_value = '')
{
    if (empty($slug)) {
        return;
    }

    // Check if this is a product attribute taxonomy and if it's enabled in search
    if (strpos($slug, 'pa_') === 0) {
        if (!function_exists('somnia_is_attribute_enabled_in_search')) {
            return;
        }
        if (!somnia_is_attribute_enabled_in_search($slug)) {
            return;
        }
    }

    $terms = get_terms(array(
        'taxonomy' => $slug,
        'hide_empty' => true
    ));

    if (!empty($terms) && !is_wp_error($terms)) {
        // First, check if there's at least one term with count > 0
        $has_valid_terms = false;

        foreach ($terms as $term) {
            // Calculate count - if on category page, count products in that category
            $term_count = somnia_get_term_count_in_category($term, $slug);
            // Check if count > 0
            if ($term_count > 0) {
                $has_valid_terms = true;
                break;
            }
        }

        // If no terms have count > 0, don't show the field
        if (!$has_valid_terms) {
            return;
        }
    ?>
        <div class="bt-form-field bt-field-type-multi bt-field-color" data-name="<?php echo esc_attr($slug); ?>">
            <?php
            if (!empty($field_title)) {
                echo '<div class="bt-field-title">' . $field_title . '<span class="bt-field-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M13.5326 6.52927L8.53255 11.5293C8.46287 11.5992 8.38008 11.6547 8.28892 11.6925C8.19775 11.7304 8.10001 11.7499 8.0013 11.7499C7.90259 11.7499 7.80485 11.7304 7.71369 11.6925C7.62252 11.6547 7.53973 11.5992 7.47005 11.5293L2.47005 6.52927C2.32915 6.38837 2.25 6.19728 2.25 5.99802C2.25 5.79876 2.32915 5.60767 2.47005 5.46677C2.61095 5.32587 2.80204 5.24672 3.0013 5.24672C3.20056 5.24672 3.39165 5.32587 3.53255 5.46677L8.00193 9.93614L12.4713 5.46615C12.6122 5.32525 12.8033 5.24609 13.0026 5.24609C13.2018 5.24609 13.3929 5.32525 13.5338 5.46615C13.6747 5.60704 13.7539 5.79814 13.7539 5.9974C13.7539 6.19665 13.6747 6.38775 13.5338 6.52865L13.5326 6.52927Z"/></svg></span></div>';
            }
            ?>

            <div class="bt-field-list">
                <?php
                foreach ($terms as $term) {
                    // Calculate count - if on category page, count products in that category
                    $term_count = somnia_get_term_count_in_category($term, $slug);
                    // Only show if count > 0
                    if ($term_count <= 0) {
                        continue;
                    }

                    $term_id = $term->term_id;
                    // Get color from metafield
                    $color = get_term_meta($term_id, 'somnia_term_color', true);
                    if (!$color) {
                        $color = $term->slug;
                    }
                ?>
                    <div class="<?php echo (str_contains($field_value, $term->slug)) ? 'bt-field-item checked' : 'bt-field-item' ?>">
                        <a href="#" data-slug="<?php echo esc_attr($term->slug); ?>">
                            <span style="background:<?php echo esc_attr($color); ?>">
                            </span>
                            <?php echo esc_html($term->name); ?>
                            <div class="bt-count"><?php echo '(' . $term_count . ')'; ?></div>
                        </a>
                    </div>
                <?php } ?>
            </div>

            <input type="hidden" name="<?php echo esc_attr($slug); ?>" value="<?php echo esc_attr($field_value); ?>">
        </div>
    <?php
    }
}

function somnia_product_field_price_slider($field_title = '', $field_min_value = '', $field_max_value = '')
{
    $prices = somnia_highest_and_lowest_product_price();
    $currency_symbol = get_woocommerce_currency_symbol();
    if ($prices['lowest_price'] == $prices['highest_price']) {
        return;
    }

    $start_min_value = !empty($field_min_value) ? $field_min_value : $prices['lowest_price'];
    $start_max_value = !empty($field_max_value) ? $field_max_value : $prices['highest_price'];

    ?>
    <div class="bt-form-field bt-field-price" data-name="product_price">
        <?php
        if (!empty($field_title)) {
            echo '<div class="bt-field-title">' . $field_title . '<span class="bt-field-toggle"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M13.5326 6.52927L8.53255 11.5293C8.46287 11.5992 8.38008 11.6547 8.28892 11.6925C8.19775 11.7304 8.10001 11.7499 8.0013 11.7499C7.90259 11.7499 7.80485 11.7304 7.71369 11.6925C7.62252 11.6547 7.53973 11.5992 7.47005 11.5293L2.47005 6.52927C2.32915 6.38837 2.25 6.19728 2.25 5.99802C2.25 5.79876 2.32915 5.60767 2.47005 5.46677C2.61095 5.32587 2.80204 5.24672 3.0013 5.24672C3.20056 5.24672 3.39165 5.32587 3.53255 5.46677L8.00193 9.93614L12.4713 5.46615C12.6122 5.32525 12.8033 5.24609 13.0026 5.24609C13.2018 5.24609 13.3929 5.32525 13.5338 5.46615C13.6747 5.60704 13.7539 5.79814 13.7539 5.9974C13.7539 6.19665 13.6747 6.38775 13.5338 6.52865L13.5326 6.52927Z"/></svg></span></div>';
        }
        ?>
        <div class="bt-field-price-slider-wrapper">
            <div id="bt-price-slider" data-range-min="<?php echo intval($prices['lowest_price']); ?>" data-range-max="<?php echo intval($prices['highest_price']); ?>" data-start-min="<?php echo intval($start_min_value); ?>" data-start-max="<?php echo intval($start_max_value); ?>"></div>
            <div class="bt-field-price-options">
                <div class="bt-field-min-price">
                    <label for="bt-min-price"><?php esc_html_e('Min price', 'somnia') ?></label>
                    <input type="number" id="bt-min-price" name="min_price" value="" placeholder="<?php echo esc_attr($start_min_value); ?>">
                    <span class="bt-currency"><?php echo esc_html($currency_symbol); ?></span>
                </div>
                <div class="bt-field-max-price">
                    <label for="bt-max-price"><?php esc_html_e('Max price', 'somnia') ?></label>
                    <input type="number" id="bt-max-price" name="max_price" value="" placeholder="<?php echo esc_attr($start_max_value); ?>">
                    <span class="bt-currency"><?php echo esc_html($currency_symbol); ?></span>
                </div>
            </div>
        </div>
    </div>
<?php
}

function somnia_product_field_rating($slug = '', $field_title = '', $field_value = '')
{
    if (empty($slug)) {
        return;
    }

    $field_title_default = !empty($field_title) ? $field_title : 'Choose';
?>
    <div class="bt-form-field bt-field-type-rating <?php echo 'bt-field-' . $slug; ?>" data-name="<?php echo esc_attr($slug); ?>">
        <div class="bt-field-title">
            <?php echo esc_html($field_title_default) ?>
            <span class="bt-field-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M13.5326 6.52927L8.53255 11.5293C8.46287 11.5992 8.38008 11.6547 8.28892 11.6925C8.19775 11.7304 8.10001 11.7499 8.0013 11.7499C7.90259 11.7499 7.80485 11.7304 7.71369 11.6925C7.62252 11.6547 7.53973 11.5992 7.47005 11.5293L2.47005 6.52927C2.32915 6.38837 2.25 6.19728 2.25 5.99802C2.25 5.79876 2.32915 5.60767 2.47005 5.46677C2.61095 5.32587 2.80204 5.24672 3.0013 5.24672C3.20056 5.24672 3.39165 5.32587 3.53255 5.46677L8.00193 9.93614L12.4713 5.46615C12.6122 5.32525 12.8033 5.24609 13.0026 5.24609C13.2018 5.24609 13.3929 5.32525 13.5338 5.46615C13.6747 5.60704 13.7539 5.79814 13.7539 5.9974C13.7539 6.19665 13.6747 6.38775 13.5338 6.52865L13.5326 6.52927Z" />
                </svg>
            </span>
        </div>
        <div class="bt-field-rating-wrapper">
            <?php
            for ($rating = 5; $rating >= 1; $rating--) {
                $stars = str_repeat('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M14.6431 7.17815L11.8306 9.60502L12.6875 13.2344C12.7347 13.4314 12.7226 13.638 12.6525 13.8281C12.5824 14.0182 12.4575 14.1833 12.2937 14.3025C12.1298 14.4217 11.9343 14.4896 11.7319 14.4977C11.5294 14.5059 11.3291 14.4538 11.1562 14.3481L7.99996 12.4056L4.84184 14.3481C4.66898 14.4532 4.4689 14.5048 4.2668 14.4963C4.06469 14.4879 3.8696 14.4199 3.70609 14.3008C3.54257 14.1817 3.41795 14.0169 3.3479 13.8272C3.27786 13.6374 3.26553 13.4312 3.31246 13.2344L4.17246 9.60502L1.35996 7.17815C1.20702 7.04597 1.09641 6.87166 1.04195 6.67699C0.987486 6.48232 0.99158 6.27592 1.05372 6.08356C1.11586 5.89121 1.23329 5.72142 1.39135 5.59541C1.54941 5.4694 1.7411 5.39274 1.94246 5.37502L5.62996 5.07752L7.05246 1.63502C7.12946 1.44741 7.26051 1.28693 7.42894 1.17398C7.59738 1.06104 7.7956 1.00073 7.9984 1.00073C8.2012 1.00073 8.39942 1.06104 8.56785 1.17398C8.73629 1.28693 8.86734 1.44741 8.94434 1.63502L10.3662 5.07752L14.0537 5.37502C14.2555 5.39209 14.4477 5.46831 14.6064 5.59415C14.765 5.71999 14.883 5.88984 14.9455 6.08243C15.008 6.27502 15.0123 6.48178 14.9579 6.6768C14.9034 6.87183 14.7926 7.04644 14.6393 7.17877L14.6431 7.17815Z" fill="#FDCC0D"/>
            </svg>', $rating) . str_repeat('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M14.9483 6.07866C14.8858 5.88649 14.7678 5.71712 14.6092 5.59189C14.4506 5.46665 14.2585 5.39116 14.0571 5.37491L10.3696 5.07741L8.9458 1.63429C8.86881 1.44667 8.73776 1.28619 8.56932 1.17325C8.40088 1.06031 8.20267 1 7.99987 1C7.79707 1 7.59885 1.06031 7.43041 1.17325C7.26197 1.28619 7.13093 1.44667 7.05393 1.63429L5.63143 5.07679L1.94205 5.37491C1.74029 5.39198 1.54805 5.4682 1.38941 5.59404C1.23078 5.71988 1.11281 5.88974 1.05028 6.08232C0.987751 6.27491 0.983448 6.48167 1.03791 6.67669C1.09237 6.87172 1.20317 7.04633 1.35643 7.17866L4.16893 9.60554L3.31205 13.2343C3.26413 13.4314 3.27586 13.6384 3.34577 13.8288C3.41567 14.0193 3.54058 14.1847 3.70465 14.304C3.86873 14.4234 4.06456 14.4913 4.26729 14.4991C4.47002 14.5069 4.67051 14.4544 4.8433 14.348L7.99955 12.4055L11.1577 14.348C11.3305 14.4531 11.5306 14.5047 11.7327 14.4962C11.9348 14.4878 12.1299 14.4198 12.2934 14.3007C12.4569 14.1816 12.5816 14.0168 12.6516 13.8271C12.7217 13.6373 12.734 13.431 12.6871 13.2343L11.8271 9.60491L14.6396 7.17804C14.7941 7.04593 14.9059 6.87094 14.9608 6.67523C15.0158 6.47952 15.0114 6.27189 14.9483 6.07866ZM13.9896 6.42054L10.9458 9.04554C10.8764 9.10537 10.8248 9.18312 10.7965 9.27031C10.7683 9.3575 10.7646 9.45076 10.7858 9.53992L11.7158 13.4649C11.7182 13.4703 11.7184 13.4765 11.7165 13.482C11.7145 13.4876 11.7105 13.4922 11.7052 13.4949C11.6939 13.5037 11.6908 13.5018 11.6814 13.4949L8.26143 11.3918C8.18266 11.3434 8.09201 11.3177 7.99955 11.3177C7.90709 11.3177 7.81644 11.3434 7.73768 11.3918L4.31768 13.4962C4.3083 13.5018 4.3058 13.5037 4.29393 13.4962C4.28865 13.4935 4.28461 13.4889 4.28263 13.4833C4.28066 13.4777 4.2809 13.4716 4.2833 13.4662L5.2133 9.54117C5.2345 9.45201 5.23078 9.35875 5.20257 9.27156C5.17435 9.18437 5.12272 9.10662 5.0533 9.04679L2.00955 6.42179C2.00205 6.41554 1.99518 6.40991 2.00143 6.39054C2.00768 6.37116 2.01268 6.37366 2.02205 6.37241L6.01705 6.04991C6.10868 6.04206 6.19637 6.00908 6.27047 5.9546C6.34457 5.90013 6.40221 5.82628 6.43705 5.74116L7.9758 2.01554C7.9808 2.00491 7.98268 1.99991 7.99768 1.99991C8.01268 1.99991 8.01455 2.00491 8.01955 2.01554L9.56205 5.74116C9.59722 5.82631 9.65523 5.90008 9.72967 5.95434C9.80412 6.00861 9.89211 6.04125 9.98393 6.04866L13.9789 6.37116C13.9883 6.37116 13.9939 6.37116 13.9996 6.38929C14.0052 6.40741 13.9996 6.41429 13.9896 6.42054Z" fill="#cfc8d8"/>
            </svg>', 5 - $rating);
            ?>
                <?php if ($rating == $field_value) { ?>
                    <div class="item-rating">
                        <span class="check-rating"></span>
                        <input type="radio" name="<?php echo esc_attr($slug); ?>" id="<?php echo 'rating' . $rating ?>" value="<?php echo esc_attr($rating); ?>" checked>
                        <?php
                        echo '<label class="bt-star" for="rating' . $rating . '">' . $stars . '</label>';
                        ?>
                        <span class="bt-count"><?php echo somnia_get_products_by_rating($rating) ?></span>
                    </div>
                <?php } else { ?>
                    <div class="item-rating">
                        <span class="check-rating"></span>
                        <input type="radio" name="<?php echo esc_attr($slug); ?>" id="<?php echo 'rating' . $rating ?>" value="<?php echo esc_attr($rating); ?>">
                        <?php
                        echo '<label class="bt-star" for="rating' . $rating . '">' . $stars . '</label>';
                        ?>
                        <span class="bt-count"><?php echo somnia_get_products_by_rating($rating) ?></span>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
<?php
}

function somnia_highest_and_lowest_product_price()
{

    global $wpdb;

    // Use WooCommerce lookup table (fast & accurate)
    $prices = $wpdb->get_row(
        "
        SELECT 
            MIN(min_price) AS min_price,
            MAX(max_price) AS max_price
        FROM {$wpdb->wc_product_meta_lookup}
        WHERE min_price > 0
        "
    );

    if (! $prices) {
        return [
            'highest_price' => 0,
            'lowest_price'  => 0,
        ];
    }

    return [
        'highest_price' => ceil((float) $prices->max_price),
        'lowest_price'  => floor((float) $prices->min_price),
    ];
}

function somnia_product_pagination($current_page, $total_page)
{
    if (1 >= $total_page) {
        return;
    }

    ob_start();
?>
    <nav class="bt-pagination bt-product-pagination" role="navigation">
        <?php if (1 != $current_page) { ?>
            <a class="prev page-numbers" href="#" data-page="<?php echo esc_attr($current_page - 1); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="13" viewBox="0 0 8 13" fill="currentColor">
                    <path d="M0.839282 12.4903C0.630446 12.2842 0.611461 11.9616 0.782327 11.7343L0.839282 11.6692L5.91327 6.6604L0.839282 1.65162C0.630446 1.44548 0.611461 1.1229 0.782327 0.895592L0.839282 0.830468C1.04812 0.624326 1.37491 0.605586 1.6052 0.774247L1.67117 0.830468L7.16137 6.24982C7.3702 6.45596 7.38919 6.77854 7.21832 7.00585L7.16137 7.07098L1.67117 12.4903C1.44145 12.7171 1.069 12.7171 0.839282 12.4903Z"></path>
                </svg> <?php echo esc_html__('Prev', 'somnia'); ?></a>
        <?php } ?>

        <?php
        for ($i = 1; $i <= $total_page; $i++) {
            if (7 > $total_page) {
                if ($i == $current_page) {
                    echo '<span class="page-numbers current">' . $i . '</span>';
                } else {
                    echo '<a class="page-numbers" href="#" data-page="' . $i . '">' . $i . '</a>';
                }
            } else {
                if ($i == $current_page) {
                    echo '<span class="page-numbers current">' . $i . '</span>';
                }

                if (5 > $current_page) {
                    if ($i != $current_page && $i < $current_page + 3) {
                        echo '<a class="page-numbers" href="#" data-page="' . $i . '">' . $i . '</a>';
                    }

                    if ($i == $current_page + 3) {
                        echo '<span class="page-numbers dots">...</span>';
                    }

                    if ($i == $total_page) {
                        echo '<a class="page-numbers" href="#" data-page="' . $i . '">' . $i . '</a>';
                    }
                }

                if ($total_page - 4 < $current_page) {
                    if ($i != $current_page && $i > $current_page - 3) {
                        echo '<a class="page-numbers" href="#" data-page="' . $i . '">' . $i . '</a>';
                    }

                    if ($i == $current_page - 3) {
                        echo '<span class="page-numbers dots">...</span>';
                    }

                    if ($i == 1) {
                        echo '<a class="page-numbers" href="#" data-page="' . $i . '">' . $i . '</a>';
                    }
                }

                if ($total_page - 4 >= $current_page && 5 <= $current_page) {
                    if ($i != $current_page && $i > $current_page - 3 && $i < $current_page + 3) {
                        echo '<a class="page-numbers" href="#" data-page="' . $i . '">' . $i . '</a>';
                    }

                    if ($i == $current_page - 3 || $i == $current_page + 3) {
                        echo '<span class="page-numbers dots">...</span>';
                    }

                    if ($i == 1) {
                        echo '<a class="page-numbers" href="#" data-page="' . $i . '">' . $i . '</a>';
                    }

                    if ($i == $total_page) {
                        echo '<a class="page-numbers" href="#" data-page="' . $i . '">' . $i . '</a>';
                    }
                }
            }
        }
        ?>

        <?php if ($total_page != $current_page) { ?>
            <a class="next page-numbers" href="#" data-page="<?php echo esc_attr($current_page + 1); ?>"><?php echo esc_html__('Next', 'somnia'); ?><svg xmlns="http://www.w3.org/2000/svg" width="8" height="13" viewBox="0 0 8 13" fill="currentColor">
                    <path d="M0.839282 12.4903C0.630446 12.2842 0.611461 11.9616 0.782327 11.7343L0.839282 11.6692L5.91327 6.6604L0.839282 1.65162C0.630446 1.44548 0.611461 1.1229 0.782327 0.895592L0.839282 0.830468C1.04812 0.624326 1.37491 0.605586 1.6052 0.774247L1.67117 0.830468L7.16137 6.24982C7.3702 6.45596 7.38919 6.77854 7.21832 7.00585L7.16137 7.07098L1.67117 12.4903C1.44145 12.7171 1.069 12.7171 0.839282 12.4903Z"></path>
                </svg></a>
        <?php } ?>
    </nav>
<?php
    return ob_get_clean();
}

function somnia_product_load_more_button($current_page, $total_page)
{
    if (1 >= $total_page || $current_page >= $total_page) {
        return;
    }

    ob_start();
?>
    <div class="bt-load-more-button-wrap">
        <button class="bt-load-more-btn" data-page="<?php echo esc_attr($current_page + 1); ?>" data-total="<?php echo esc_attr($total_page); ?>">
            <span class="bt-btn-text"><?php echo esc_html__('Load More Products', 'somnia'); ?></span>
            <span class="bt-btn-loading" style="display: none;">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="2" fill="none" opacity="0.25" />
                    <path d="M10 2 A8 8 0 0 1 18 10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round">
                        <animateTransform attributeName="transform" type="rotate" from="0 10 10" to="360 10 10" dur="1s" repeatCount="indefinite" />
                    </path>
                </svg>
                <?php echo esc_html__('Loading...', 'somnia'); ?>
            </span>
        </button>
    </div>
    <?php
    return ob_get_clean();
}

function somnia_products_query_args($params = array(), $limit = 9)
{
    $query_args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => $limit
    );

    if (isset($params['current_page']) && $params['current_page'] != '') {
        $query_args['paged'] = absint($params['current_page']);
    }

    if (isset($params['search_keyword']) && $params['search_keyword'] != '') {
        $query_args['s'] = $params['search_keyword'];
    }

    if (isset($params['sort_order']) && $params['sort_order'] != '') {
        if ($params['sort_order'] == 'date_high' || $params['sort_order'] == 'date_low') {
            $query_args['orderby'] = 'date';

            if ($params['sort_order'] == 'date_high') {
                $query_args['order'] = 'DESC';
            } else {
                $query_args['order'] = 'ASC';
            }
        }
        if ($params['sort_order'] == 'price_high' || $params['sort_order'] == 'price_low') {
            $query_args['meta_key'] = '_price';
            $query_args['orderby'] = 'meta_value_num';

            if ($params['sort_order'] == 'price_high') {
                $query_args['order'] = 'DESC';
            } else {
                $query_args['order'] = 'ASC';
            }
        }
        if ($params['sort_order'] == 'best_selling') {
            $query_args['meta_key'] = 'total_sales';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['order'] = 'DESC';
        }
        if ($params['sort_order'] == 'average_rating') {
            $query_args['meta_key'] = '_wc_average_rating';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['order'] = 'DESC';
        }
    }

    $query_tax = array();

    // Check for product category - from params or current category page
    $product_cat = '';
    if (isset($params['product_cat']) && $params['product_cat'] != '') {
        $product_cat = $params['product_cat'];
    } elseif (somnia_is_category_archive_page()) {
        // If on category page and no product_cat in params, get from queried object
        $current_category = get_queried_object();
        if ($current_category && !empty($current_category->slug)) {
            $product_cat = $current_category->slug;
        }
    }

    if ($product_cat != '') {
        $query_tax[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => explode(',', $product_cat)
        );
    }
    // Handle excluded product categories
    if (isset($params['excluded_product_cat']) && $params['excluded_product_cat'] != '') {
        $excluded_cats = explode(',', $params['excluded_product_cat']);
        $query_tax[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $excluded_cats,
            'operator' => 'NOT IN'
        );
    }
    if (isset($params['product_brand']) && $params['product_brand'] != '') {
        $query_tax[] = array(
            'taxonomy' => 'product_brand',
            'field' => 'slug',
            'terms' => explode(',', $params['product_brand'])
        );
    }
    if (isset($params['comfort_scale']) && $params['comfort_scale'] != '') {
        $query_tax[] = array(
            'taxonomy' => 'comfort_scale',
            'field' => 'slug',
            'terms' => explode(',', $params['comfort_scale'])
        );
    }
    if (isset($params['mattress_type']) && $params['mattress_type'] != '') {
        $query_tax[] = array(
            'taxonomy' => 'mattress_type',
            'field' => 'slug',
            'terms' => explode(',', $params['mattress_type'])
        );
    }
    // Handle all enabled WooCommerce attributes (non-color) dynamically
    $enabled_attributes = function_exists('somnia_get_all_enabled_search_attributes') ? somnia_get_all_enabled_search_attributes() : array();
    if (!empty($enabled_attributes)) {
        foreach ($enabled_attributes as $attr_data) {
            $taxonomy = $attr_data['taxonomy'];
            if (isset($params[$taxonomy]) && $params[$taxonomy] != '') {
                $query_tax[] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => explode(',', $params[$taxonomy])
                );
            }
        }
    }
    if (isset($params['product_tag']) && $params['product_tag'] != '') {
        $query_tax[] = array(
            'taxonomy' => 'product_tag',
            'field' => 'slug',
            'terms' => explode(',', $params['product_tag'])
        );
    }
    // Handle color taxonomies dynamically
    $color_taxonomies = function_exists('somnia_get_all_color_taxonomies') ? somnia_get_all_color_taxonomies() : array();
    if (!empty($color_taxonomies)) {
        foreach ($color_taxonomies as $color_taxonomy) {
            if (isset($params[$color_taxonomy]) && $params[$color_taxonomy] != '') {
                $query_tax[] = array(
                    'taxonomy' => $color_taxonomy,
                    'field' => 'slug',
                    'terms' => explode(',', $params[$color_taxonomy])
                );
            }
        }
    }
    if (!empty($query_tax)) {
        $query_args['tax_query'] = $query_tax;
    }

    $query_meta = array();

    if (isset($params['min_price']) && $params['min_price'] != '' && isset($params['max_price']) && $params['max_price'] != '') {
        $min_price = $params['min_price'];
        $max_price = $params['max_price'];

        $query_meta['price_clause'] = array(
            array(
                'key' => '_price',
                'value' => array($min_price, $max_price),
                'compare' => 'BETWEEN',
                'type' => 'DECIMAL'
            ),
        );
    }
    if (isset($params['product_rating']) && $params['product_rating'] != '') {
        $query_meta['rating_clause'] = array(
            array(
                'key' => '_wc_average_rating',
                'value' => $params['product_rating'],
                'compare' => '=',
                'type' => 'NUMERIC'
            ),
        );
    }

    if (!empty($query_meta)) {
        $query_args['meta_query'] = $query_meta;
        $query_args['relation'] = 'AND';
    }

    return $query_args;
}

function somnia_products_filter()
{
    $rows = intval(get_option('woocommerce_catalog_rows', 2));
    $columns = intval(get_option('woocommerce_catalog_columns', 4));
    $rows = $rows > 0 ? $rows : 1;
    $columns = $columns > 0 ? $columns : 1;
    $limit = $rows * $columns;
    $query_args = somnia_products_query_args($_POST, $limit);
    $wp_query = new \WP_Query($query_args);
    $current_page = isset($_POST['current_page']) && $_POST['current_page'] != '' ? absint($_POST['current_page']) : 1;
    $total_page = $wp_query->max_num_pages;

    $paged = !empty($wp_query->query_vars['paged']) ? $wp_query->query_vars['paged'] : 1;

    $total_products = $wp_query->found_posts;

    // Get pagination type from ACF option
    $archive_shop = get_field('archive_shop', 'option');
    $pagination_type = isset($archive_shop['shop_pagination']) ? $archive_shop['shop_pagination'] : 'default';
    // Check both GET (for normal page load) and POST (for AJAX requests)
    if (isset($_POST['layout-pagination']) && !empty($_POST['layout-pagination'])) {
        $pagination_type = sanitize_text_field($_POST['layout-pagination']);
    } elseif (isset($_GET['layout-pagination']) && !empty($_GET['layout-pagination'])) {
        $pagination_type = sanitize_text_field($_GET['layout-pagination']);
    }
    // Update Results Block
    ob_start();
    if ($total_products > 0) {
        $product_text = ($total_products == 1) ? __('%s Product Found', 'somnia') : __('%s Products Found', 'somnia');
        printf(
            $product_text,
            '<span class="highlight">' . esc_html($total_products) . '</span>'
        );
    } else {
        echo esc_html__('No results', 'somnia');
    }
    $output['results'] = ob_get_clean();
    // update button Results
    $product_text = ($total_products == 1) ? __('Show %s Product', 'somnia') : __('Show %s Products', 'somnia');
    printf($total_products > 0 ? $product_text : esc_html__('No products found', 'somnia'), $total_products);
    $output['button-results'] = ob_get_clean();
    // Update Loop Post
    if ($wp_query->have_posts()) {
        ob_start();
        global $is_ajax_filter_product;
        $is_ajax_filter_product = true;
        while ($wp_query->have_posts()) {
            $wp_query->the_post();

            wc_get_template_part('content', 'product');
        }
        $is_ajax_filter_product = false;
        $output['items'] = ob_get_clean();

        // Generate pagination based on type
        if ($pagination_type === 'button-load-more') {
            $output['pagination'] = somnia_product_load_more_button($current_page, $total_page);
        } elseif ($pagination_type === 'infinite-scrolling') {
            if ($current_page < $total_page) {
                $output['pagination'] = '<div class="bt-infinite-scroll-trigger" data-page="' . esc_attr($current_page + 1) . '" data-total="' . esc_attr($total_page) . '">'
                    . '<span class="bt-loading-spinner" style="display:none;">' . esc_html__('Loading more...', 'somnia') . '</span>'
                    . '</div>';
            } else {
                $output['pagination'] = '';
            }
        } else {
            $output['pagination'] = somnia_product_pagination($current_page, $total_page);
        }

        // Add metadata for pagination
        $output['pagination_meta'] = array(
            'current_page' => $current_page,
            'total_page' => $total_page,
            'has_more' => $current_page < $total_page,
            'pagination_type' => $pagination_type
        );
    } else {
        $output['items'] = '<div class="not-found-products"><svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="currentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.13,104.13,0,0,0,128,24Zm-18.34,98.34a8,8,0,0,1-11.32,11.32L88,123.31,77.66,133.66a8,8,0,0,1-11.32-11.32L76.69,112,66.34,101.66A8,8,0,0,1,77.66,90.34L88,100.69,98.34,90.34a8,8,0,0,1,11.32,11.32L99.31,112ZM128,192a12,12,0,1,1,12-12A12,12,0,0,1,128,192Zm61.66-69.66a8,8,0,0,1-11.32,11.32L168,123.31l-10.34,10.35a8,8,0,0,1-11.32-11.32L156.69,112l-10.35-10.34a8,8,0,0,1,11.32-11.32L168,100.69l10.34-10.35a8,8,0,0,1,11.32,11.32L179.31,112Z"></path></svg>' . esc_html__('Oops! We couldn\'t find any products matching your search. Try adjusting your filters or explore our full collection.', 'somnia') . '
										<a href="#" class="bt-reset-filter-product-btn">' . esc_html__('Reset All Filters', 'somnia') . '</a>
									</div>';
        $output['pagination'] = '';
        $output['pagination_meta'] = array(
            'current_page' => 1,
            'total_page' => 0,
            'has_more' => false,
            'pagination_type' => $pagination_type
        );
    }

    wp_reset_postdata();

    // Get category title and description if product_cat is set
    if (isset($_POST['product_cat']) && !empty($_POST['product_cat'])) {
        $cat_slug = sanitize_text_field($_POST['product_cat']);
        $category_term = get_term_by('slug', $cat_slug, 'product_cat');

        if ($category_term && !is_wp_error($category_term)) {
            $output['category_title'] = $category_term->name;
            $output['category_description'] = term_description($category_term->term_id, 'product_cat');
            $output['has_category_filter'] = true; // Flag to indicate category filter is active
        }
    } else {
        // No category selected, return empty to indicate reset to original
        $output['category_title'] = '';
        $output['category_description'] = '';
        $output['has_category_filter'] = false; // No category filter
    }

    wp_send_json_success($output);

    die();
}

add_action('wp_ajax_somnia_products_filter', 'somnia_products_filter');
add_action('wp_ajax_nopriv_somnia_products_filter', 'somnia_products_filter');

function somnia_products_compare()
{
    $productcompare = '';
    $product_ids = array();
    $ex_items = count($product_ids) < 3 ? 3 - count($product_ids) : 0;
    $productcompare = isset($_POST['compare_data']) ? $_POST['compare_data'] : '';
    if (!empty($productcompare)) {
        $product_ids = explode(',', $productcompare);
    }
    $ex_items = count($product_ids) < 3 ? 3 - count($product_ids) : 0;
    ob_start();
    $compare_settings = get_field('compare', 'options');
    if (!empty($compare_settings['fields_to_show_compare'])) {
        $fields_show_compare = $compare_settings['fields_to_show_compare'];
    } else {
        $fields_show_compare = array('price', 'rating', 'stock_status', 'weight', 'dimensions', 'color', 'size');
    }
    if (!empty($product_ids)) {

    ?>
        <div class="bt-table-title">
            <h2><?php esc_html_e('Compare products', 'somnia') ?></h2>
        </div>
        <div class="bt-wrap-compare">
            <div class="bt-table-compare">
                <div class="bt-table--head">
                    <?php
                    if (!empty($fields_show_compare)) {
                        echo '<div class="bt-table--col">' . esc_html__('Thumbnail', 'somnia') . '</div>';
                        echo '<div class="bt-table--col">' . esc_html__('Product Name', 'somnia') . '</div>';
                        if (in_array('short_desc', $fields_show_compare)) {
                            echo '<div class="bt-table--col">' . esc_html__('Short Description', 'somnia') . '</div>';
                        }
                        if (in_array('price', $fields_show_compare)) {
                            echo '<div class="bt-table--col">' . esc_html__('Price', 'somnia') . '</div>';
                        }
                        if (in_array('rating', $fields_show_compare)) {
                            echo '<div class="bt-table--col">' . esc_html__('Rating', 'somnia') . '</div>';
                        }
                        if (in_array('brand', $fields_show_compare)) {
                            echo '<div class="bt-table--col">' . esc_html__('Brand', 'somnia') . '</div>';
                        }
                        if (in_array('stock_status', $fields_show_compare)) {
                            echo '<div class="bt-table--col">' . esc_html__('Availability', 'somnia') . '</div>';
                        }
                        if (in_array('sku', $fields_show_compare)) {
                            echo '<div class="bt-table--col">' . esc_html__('SKU', 'somnia') . '</div>';
                        }
                        if (in_array('weight', $fields_show_compare)) {
                            echo '<div class="bt-table--col">' . esc_html__('Weight', 'somnia') . '</div>';
                        }
                        if (in_array('dimensions', $fields_show_compare)) {
                            echo '<div class="bt-table--col">' . esc_html__('Dimensions', 'somnia') . '</div>';
                        }
                        if (in_array('color', $fields_show_compare)) {
                            echo '<div class="bt-table--col bt-head-color">' . esc_html__('color', 'somnia') . '</div>';
                        }
                        if (in_array('size', $fields_show_compare)) {
                            echo '<div class="bt-table--col">' . esc_html__('Size', 'somnia') . '</div>';
                        }
                    }
                    ?>
                    <div class="bt-table--col"></div>
                </div>
                <div class="bt-table--body">
                    <?php
                    foreach ($product_ids as $key => $id) {
                        $product = wc_get_product($id);
                        if ($product) {
                            $product_url = get_permalink($id);
                            $product_name = $product->get_name();
                            $product_image = wp_get_attachment_image_src($product->get_image_id(), 'large');
                            if (!$product_image) {
                                $product_image_url = wc_placeholder_img_src();
                            } else {
                                $product_image_url = $product_image[0];
                            }
                            $product_price = $product->get_price_html();
                            $stock_status = $product->get_stock_status();
                            if ($stock_status == 'onbackorder') {
                                $stock_status_custom = '<p class="stock on-backorder">' . __('On Backorder', 'somnia') . '</p>';
                            } elseif ($product->is_in_stock()) {
                                $stock_status_custom = '<p class="stock in-stock">' . __('In Stock', 'somnia') . '</p>';
                            } else {
                                $stock_status_custom = '<p class="stock out-of-stock">' . __('Out of Stock', 'somnia') . '</p>';
                            }
                            $brand = wp_get_post_terms($id, 'product_brand', ['fields' => 'names']);
                            $brand_list = !empty($brand) ? implode(', ', $brand) : '';

                            $brands = wp_get_post_terms($id, 'product_brand', ['fields' => 'all']);
                            $brand_links = [];
                            foreach ($brands as $brand) {
                                $brand_links[] = '<a href="' . get_term_link($brand) . '">' . esc_html($brand->name) . '</a>';
                            }
                            $brand_list = !empty($brand_links) ? implode(', ', $brand_links) : '';

                    ?>
                            <div class="bt-table--row">
                                <div class="bt-table--col bt-thumb">
                                    <div class="bt-remove-item" data-id="<?php echo esc_attr($id) ?>">
                                        <div class="bt-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M9.41183 8L15.6952 1.71665C15.7905 1.62455 15.8666 1.51437 15.9189 1.39255C15.9713 1.27074 15.9988 1.13972 16 1.00714C16.0011 0.874567 15.9759 0.743089 15.9256 0.620381C15.8754 0.497673 15.8013 0.386193 15.7076 0.292444C15.6138 0.198695 15.5023 0.124556 15.3796 0.0743523C15.2569 0.0241486 15.1254 -0.00111435 14.9929 3.76988e-05C14.8603 0.00118975 14.7293 0.0287337 14.6074 0.0810623C14.4856 0.133391 14.3755 0.209456 14.2833 0.30482L8 6.58817L1.71665 0.30482C1.52834 0.122941 1.27612 0.0223015 1.01433 0.0245764C0.752534 0.0268514 0.502106 0.131859 0.316983 0.316983C0.131859 0.502107 0.0268514 0.752534 0.0245764 1.01433C0.0223015 1.27612 0.122941 1.52834 0.30482 1.71665L6.58817 8L0.30482 14.2833C0.209456 14.3755 0.133391 14.4856 0.0810623 14.6074C0.0287337 14.7293 0.00118975 14.8603 3.76988e-05 14.9929C-0.00111435 15.1254 0.0241486 15.2569 0.0743523 15.3796C0.124556 15.5023 0.198695 15.6138 0.292444 15.7076C0.386193 15.8013 0.497673 15.8754 0.620381 15.9256C0.743089 15.9759 0.874567 16.0011 1.00714 16C1.13972 15.9988 1.27074 15.9713 1.39255 15.9189C1.51437 15.8666 1.62455 15.7905 1.71665 15.6952L8 9.41183L14.2833 15.6952C14.4226 15.8358 14.6006 15.9317 14.7945 15.9708C14.9885 16.0098 15.1898 15.9902 15.3726 15.9145C15.5554 15.8388 15.7115 15.7104 15.8211 15.5456C15.9306 15.3808 15.9886 15.1871 15.9877 14.9893C15.9878 14.8581 15.9619 14.7283 15.9117 14.6072C15.8615 14.4861 15.7879 14.376 15.6952 14.2833L9.41183 8Z" fill="#0C2C48" />
                                            </svg>
                                        </div>
                                    </div>
                                    <a href="<?php echo esc_url($product_url); ?>">
                                        <img src="<?php echo esc_url($product_image_url); ?>" alt="<?php echo esc_attr($product_name); ?>">
                                    </a>
                                </div>
                                <div class="bt-table--col bt-name">
                                    <h3><a href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($product_name); ?></a></h3>
                                </div>
                                <?php if (!empty($fields_show_compare)) {
                                    if (in_array('short_desc', $fields_show_compare)) { ?>
                                        <div class="bt-table--col bt-description">
                                            <?php echo '<p>' . wp_trim_words($product->get_short_description(), 20) . '</p>'; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (in_array('price', $fields_show_compare)) { ?>
                                        <div class="bt-table--col bt-price">
                                            <?php echo '<p>' . wp_kses_post($product_price) . '</p>'; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (in_array('rating', $fields_show_compare)) { ?>
                                        <div class="bt-table--col bt-rating woocommerce">
                                            <div class="bt-product-rating">
                                                <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                                                <?php if ($product->get_rating_count()): ?>
                                                    <div class="bt-product-rating--count">
                                                        (<?php echo esc_html($product->get_rating_count()); ?>)
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (in_array('brand', $fields_show_compare)) { ?>
                                        <div class="bt-table--col bt-brand">
                                            <?php echo '<p>' . $brand_list . '</p>'; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (in_array('stock_status', $fields_show_compare)) { ?>
                                        <div class="bt-table--col bt-stock">
                                            <?php echo wp_kses_post($stock_status_custom); ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (in_array('sku', $fields_show_compare)) { ?>
                                        <div class="bt-table--col bt-sku">
                                            <?php echo '<p>' . $product->get_sku() . '</p>'; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (in_array('weight', $fields_show_compare)) { ?>
                                        <div class="bt-table--col bt-weight">
                                            <?php echo '<p>' . $product->get_weight() . ' ' . get_option('woocommerce_weight_unit') . '</p>'; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (in_array('dimensions', $fields_show_compare)) { ?>
                                        <div class="bt-table--col bt-dimensions">
                                            <?php echo '<p>' . wc_format_dimensions($product->get_dimensions(false)) . '</p>'; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (in_array('color', $fields_show_compare)) {
                                        $color_taxonomy = somnia_get_color_taxonomy();
                                        if ($color_taxonomy) {
                                    ?>
                                            <div class="bt-table--col bt-color">
                                                <?php
                                                $colors = wp_get_post_terms($id, $color_taxonomy, ['fields' => 'ids']);
                                                $count = 0;
                                                foreach ($colors as $color_id) {
                                                    if ($count >= 6) break; // Only show max 6 colors
                                                    // Get color from metafield
                                                    $color_value = get_term_meta($color_id, 'somnia_term_color', true);
                                                    $color = get_term($color_id, $color_taxonomy);
                                                    if (!$color_value) {
                                                        $color_value = $color->slug;
                                                    }
                                                    echo '<div class="bt-item-color"><span style="background-color: ' . esc_attr($color_value) . ';"></span>' . esc_html($color->name) . '</div>';
                                                    $count++;
                                                }
                                                ?>
                                            </div>
                                    <?php
                                        }
                                    } ?>
                                    <?php if (in_array('size', $fields_show_compare)) { ?>
                                        <div class="bt-table--col bt-size">
                                            <?php
                                            $sizes = wp_get_post_terms($id, 'pa_size', ['fields' => 'names']);
                                            echo '<p>' . (!empty($sizes) ? implode(', ', $sizes) : 'N/A') . '</p>';
                                            ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <div class="bt-table--col bt-add-to-cart">
                                    <?php
                                    $product = wc_get_product($id);
                                    if ($product->is_type('simple')) {
                                    ?>
                                        <a href="?add-to-cart=<?php echo esc_attr($id); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($id); ?>" data-quantity="1" class="bt-button product_type_simple add_to_cart_button ajax_add_to_cart bt-button-hover" data-product_id="<?php echo esc_attr($id); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
                                    <?php
                                    } else {
                                    ?>
                                        <a href="<?php echo esc_url(get_permalink($id)); ?>" class="bt-button bt-button-hover"><?php echo esc_html__('View Product', 'somnia') ?></a>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    //     if ($ex_items > 0) {
                    for ($i = 0; $i < 3; $i++) {
                        $class_active = ($i < $ex_items) ? ' active' : '';
                    ?>
                        <div class="bt-table--row bt-product-add-compare<?php echo esc_attr($class_active); ?>">
                            <div class="bt-table--col bt-thumb">
                                <div class="bt-cover-image">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M256 512a25 25 0 0 1-25-25V25a25 25 0 0 1 50 0v462a25 25 0 0 1-25 25z"></path>
                                        <path d="M487 281H25a25 25 0 0 1 0-50h462a25 25 0 0 1 0 50z"></path>
                                    </svg>
                                    <span> <?php echo __('Add Product To Compare', 'somnia'); ?></span>
                                </div>
                            </div>
                            <div class="bt-table--col bt-name"></div>
                            <?php if (!empty($fields_show_compare)) {
                                if (in_array('short_desc', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-description"></div>
                                <?php } ?>
                                <?php if (in_array('price', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-price"></div>
                                <?php } ?>
                                <?php if (in_array('rating', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-rating woocommerce"></div>
                                <?php } ?>
                                <?php if (in_array('brand', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-brand"></div>
                                <?php } ?>
                                <?php if (in_array('stock_status', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-stock"></div>
                                <?php } ?>
                                <?php if (in_array('sku', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-sku"></div>
                                <?php } ?>
                                <?php if (in_array('weight', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-weight"></div>
                                <?php } ?>
                                <?php if (in_array('dimensions', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-dimensions"></div>
                                <?php } ?>
                                <?php if (in_array('color', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-color"></div>
                                <?php } ?>
                                <?php if (in_array('size', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-size"></div>
                                <?php } ?>
                            <?php } ?>
                            <div class="bt-table--col"></div>
                        </div>
                    <?php
                    }
                    //    }
                    ?>
                </div>
            </div>
        </div>
    <?php
        $count = count($product_ids);
        $output['count'] = $count;
    } else {
    ?>
        <div class="bt-table-title">
            <h2><?php esc_html_e('Compare products', 'somnia') ?></h2>
        </div>
        <div class="bt-table-compare">
            <div class="bt-table--head">
                <?php
                if (!empty($fields_show_compare)) {
                    echo '<div class="bt-table--col">' . esc_html__('Thumbnail', 'somnia') . '</div>';
                    echo '<div class="bt-table--col">' . esc_html__('Product Name', 'somnia') . '</div>';
                    if (in_array('short_desc', $fields_show_compare)) {
                        echo '<div class="bt-table--col">' . esc_html__('Short Description', 'somnia') . '</div>';
                    }
                    if (in_array('price', $fields_show_compare)) {
                        echo '<div class="bt-table--col">' . esc_html__('Price', 'somnia') . '</div>';
                    }
                    if (in_array('rating', $fields_show_compare)) {
                        echo '<div class="bt-table--col">' . esc_html__('Rating', 'somnia') . '</div>';
                    }
                    if (in_array('brand', $fields_show_compare)) {
                        echo '<div class="bt-table--col">' . esc_html__('Brand', 'somnia') . '</div>';
                    }
                    if (in_array('stock_status', $fields_show_compare)) {
                        echo '<div class="bt-table--col">' . esc_html__('Availability', 'somnia') . '</div>';
                    }
                    if (in_array('sku', $fields_show_compare)) {
                        echo '<div class="bt-table--col">' . esc_html__('SKU', 'somnia') . '</div>';
                    }
                    if (in_array('weight', $fields_show_compare)) {
                        echo '<div class="bt-table--col">' . esc_html__('Weight', 'somnia') . '</div>';
                    }
                    if (in_array('dimensions', $fields_show_compare)) {
                        echo '<div class="bt-table--col">' . esc_html__('Dimensions', 'somnia') . '</div>';
                    }
                    if (in_array('color', $fields_show_compare)) {
                        echo '<div class="bt-table--col bt-head-color">' . esc_html__('color', 'somnia') . '</div>';
                    }
                    if (in_array('size', $fields_show_compare)) {
                        echo '<div class="bt-table--col">' . esc_html__('Size', 'somnia') . '</div>';
                    }
                }
                ?>
                <div class="bt-table--col"></div>
            </div>
            <div class="bt-table--body">
                <?php
                if ($ex_items > 0) {
                    for ($i = 0; $i < $ex_items; $i++) {
                ?>
                        <div class="bt-table--row bt-load-before bt-product-add-compare">
                            <div class="bt-table--col bt-thumb">
                                <div class="bt-cover-image">
                                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M256 512a25 25 0 0 1-25-25V25a25 25 0 0 1 50 0v462a25 25 0 0 1-25 25z"></path>
                                        <path d="M487 281H25a25 25 0 0 1 0-50h462a25 25 0 0 1 0 50z"></path>
                                    </svg>
                                    <span> <?php echo __('Add Product To Compare', 'somnia'); ?></span>
                                </div>
                            </div>
                            <div class="bt-table--col bt-name"></div>
                            <?php if (!empty($fields_show_compare)) {
                                if (in_array('short_desc', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-description"></div>
                                <?php } ?>
                                <?php if (in_array('price', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-price"></div>
                                <?php } ?>
                                <?php if (in_array('rating', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-rating woocommerce"></div>
                                <?php } ?>
                                <?php if (in_array('brand', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-brand"></div>
                                <?php } ?>
                                <?php if (in_array('stock_status', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-stock"></div>
                                <?php } ?>
                                <?php if (in_array('sku', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-sku"></div>
                                <?php } ?>
                                <?php if (in_array('weight', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-weight"></div>
                                <?php } ?>
                                <?php if (in_array('dimensions', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-dimensions"></div>
                                <?php } ?>
                                <?php if (in_array('color', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-color"></div>
                                <?php } ?>
                                <?php if (in_array('size', $fields_show_compare)) { ?>
                                    <div class="bt-table--col bt-size"></div>
                                <?php } ?>
                            <?php } ?>
                            <div class="bt-table--col"></div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }
    $output['product'] = ob_get_clean();

    wp_send_json_success($output);
    die();
}

add_action('wp_ajax_somnia_products_compare', 'somnia_products_compare');
add_action('wp_ajax_nopriv_somnia_products_compare', 'somnia_products_compare');

function somnia_products_wishlist()
{
    if (isset($_POST['productwishlist_data']) && !empty($_POST['productwishlist_data'])) {
        $product_ids = explode(',', $_POST['productwishlist_data']);
        $output['count'] = count($product_ids);

        ob_start();
        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                $product_price = $product->get_price_html();
                $stock_status = $product->is_in_stock() ? __('In Stock', 'somnia') : __('Out of Stock', 'somnia');
        ?>
                <div class="bt-table--row bt-product-item">
                    <div class="bt-table--col bt-product-remove">
                        <a href="#" data-id="<?php echo esc_attr($product_id); ?>" class="bt-product-remove-wishlist">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M9.64052 9.10965C9.67536 9.14449 9.703 9.18586 9.72186 9.23138C9.74071 9.2769 9.75042 9.32569 9.75042 9.37496C9.75042 9.42424 9.74071 9.47303 9.72186 9.51855C9.703 9.56407 9.67536 9.60544 9.64052 9.64028C9.60568 9.67512 9.56432 9.70276 9.51879 9.72161C9.47327 9.74047 9.42448 9.75017 9.37521 9.75017C9.32594 9.75017 9.27714 9.74047 9.23162 9.72161C9.1861 9.70276 9.14474 9.67512 9.1099 9.64028L6.00021 6.53012L2.89052 9.64028C2.82016 9.71064 2.72472 9.75017 2.62521 9.75017C2.5257 9.75017 2.43026 9.71064 2.3599 9.64028C2.28953 9.56991 2.25 9.47448 2.25 9.37496C2.25 9.27545 2.28953 9.18002 2.3599 9.10965L5.47005 5.99996L2.3599 2.89028C2.28953 2.81991 2.25 2.72448 2.25 2.62496C2.25 2.52545 2.28953 2.43002 2.3599 2.35965C2.43026 2.28929 2.5257 2.24976 2.62521 2.24976C2.72472 2.24976 2.82016 2.28929 2.89052 2.35965L6.00021 5.46981L9.1099 2.35965C9.18026 2.28929 9.2757 2.24976 9.37521 2.24976C9.47472 2.24976 9.57016 2.28929 9.64052 2.35965C9.71089 2.43002 9.75042 2.52545 9.75042 2.62496C9.75042 2.72448 9.71089 2.81991 9.64052 2.89028L6.53036 5.99996L9.64052 9.10965Z" fill="#C72929" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" fill="#C72929">
                                <path d="M493.815 70.629c-11.001-1.003-20.73 7.102-21.733 18.102l-2.65 29.069C424.473 47.194 346.429 0 256 0 158.719 0 72.988 55.522 30.43 138.854c-5.024 9.837-1.122 21.884 8.715 26.908 9.839 5.024 21.884 1.123 26.908-8.715C102.07 86.523 174.397 40 256 40c74.377 0 141.499 38.731 179.953 99.408l-28.517-20.367c-8.989-6.419-21.48-4.337-27.899 4.651-6.419 8.989-4.337 21.479 4.651 27.899l86.475 61.761c12.674 9.035 30.155.764 31.541-14.459l9.711-106.53c1.004-11.001-7.1-20.731-18.1-21.734zM472.855 346.238c-9.838-5.023-21.884-1.122-26.908 8.715C409.93 425.477 337.603 472 256 472c-74.377 0-141.499-38.731-179.953-99.408l28.517 20.367c8.989 6.419 21.479 4.337 27.899-4.651 6.419-8.989 4.337-21.479-4.651-27.899l-86.475-61.761c-12.519-8.944-30.141-.921-31.541 14.459L.085 419.637c-1.003 11 7.102 20.73 18.101 21.733 11.014 1.001 20.731-7.112 21.733-18.102l2.65-29.069C87.527 464.806 165.571 512 256 512c97.281 0 183.012-55.522 225.57-138.854 5.024-9.837 1.122-21.884-8.715-26.908z"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="bt-table--col bt-product-thumb">
                        <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="bt-thumb">
                            <?php echo wp_kses_post($product->get_image('medium')); ?>
                        </a>
                    </div>
                    <div class="bt-table--col bt-product-title">
                        <h3 class="bt-title">
                            <a href="<?php echo esc_url(get_permalink($product_id)); ?>">
                                <?php echo esc_html($product->get_name()); ?>
                            </a>
                        </h3>
                        <?php
                        if ($product_price) {
                            echo '<span class="bt-price-mobile">' . wp_kses_post($product_price) . '</span>';
                        }
                        ?>
                    </div>
                    <div class="bt-table--col bt-product-price<?php echo !$product->is_type('simple') ? ' bt-type-variable' : ''; ?>">
                        <?php
                        if ($product_price) {
                            echo '<span>' . wp_kses_post($product_price) . '</span>';
                        }
                        ?>
                    </div>
                    <div class="bt-table--col bt-product-stock">
                        <span><?php echo esc_html($stock_status); ?></span>
                    </div>
                    <div class="bt-table--col bt-product-add-to-cart">
                        <?php
                        $product = wc_get_product($product_id);
                        if ($product->is_type('simple')) {
                        ?>
                            <a href="?add-to-cart=<?php echo esc_attr($product_id); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product_id); ?>" data-quantity="1" class="bt-button product_type_simple add_to_cart_button ajax_add_to_cart bt-button-hover" data-product_id="<?php echo esc_attr($product_id); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
                        <?php
                        } else {
                        ?>
                            <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="bt-button bt-button-hover"><?php echo esc_html__('View Product', 'somnia') ?></a>
                        <?php
                        }
                        ?>
                    </div>
                </div>
    <?php
            }
        }
        $output['items'] = ob_get_clean();
    } else {
        $output['count'] = 0;
        $output['items'] = '<div class="bt-no-results">' . __('No products found! ', 'somnia') . '<a href="/shop/">' . __('Back to Shop', 'somnia') . '</a></div>';
    }

    wp_send_json_success($output);

    die();
}

add_action('wp_ajax_somnia_products_wishlist', 'somnia_products_wishlist');
add_action('wp_ajax_nopriv_somnia_products_wishlist', 'somnia_products_wishlist');

function somnia_products_quick_view()
{
    if (!isset($_POST['productid'])) {
        wp_send_json_error('Product ID is required');
        die();
    }

    $product_id = intval($_POST['productid']);
    $product = wc_get_product($product_id);
    $source = isset($_POST['source']) ? sanitize_text_field($_POST['source']) : 'quickview';

    if (!$product) {
        wp_send_json_error('Product not found');
        die();
    }

    ob_start();

    ?>
    <div class="bt-quickview-title">
        <h2><?php echo ($source === 'add-to-cart') ? esc_html__('Select options', 'somnia') : esc_html__('Quick View', 'somnia'); ?></h2>
        <div class="bt-quick-view-close"></div>
    </div>
    <div class="bt-quickview-wrap woocommerce">
        <?php
        if ($product_id) {
            $args = array(
                'p' => $product_id,
                'post_type' => 'product'
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    wc_get_template_part('content', 'quick-view-product');
                }
                wp_reset_postdata();
            }
        }
        ?>
    </div>
<?php
    $output['product'] = ob_get_clean();

    wp_send_json_success($output);
}

add_action('wp_ajax_somnia_products_quick_view', 'somnia_products_quick_view');
add_action('wp_ajax_nopriv_somnia_products_quick_view', 'somnia_products_quick_view');

/* get price freeship */
function somnia_get_free_shipping_minimum_amount()
{
    $shipping_zones = WC_Shipping_Zones::get_zones();

    foreach ($shipping_zones as $zone) {
        $shipping_methods = $zone['shipping_methods'];

        foreach ($shipping_methods as $method) {
            if ($method->id === 'free_shipping') {
                if (isset($method->min_amount)) {
                    return $method->min_amount;
                }
            }
        }
    }
    return 0;
}

function somnia_get_free_shipping()
{
    $free_shipping_threshold = somnia_get_free_shipping_minimum_amount();
    $cart_total = WC()->cart->get_cart_contents_total();
    $currency_symbol = get_woocommerce_currency_symbol();
    if ($cart_total < $free_shipping_threshold) {
        $amount_left = $free_shipping_threshold - $cart_total;
        $output['percentage'] = ($cart_total / $free_shipping_threshold) * 100;
        $output['message'] = sprintf(
            __('<p class="bt-buy-more">Buy <span>%1$s%2$.2f</span> more to get <span>Free Shipping</span></p>', 'somnia'),
            $currency_symbol,
            $amount_left
        );
    } else {
        $output['message'] = __('<p class="bt-congratulation">Congratulations! You have free shipping!</p>', 'somnia');
        $output['percentage'] = 100;
    }
?>
<?php
    wp_send_json_success($output);
}

add_action('wp_ajax_somnia_get_free_shipping', 'somnia_get_free_shipping');
add_action('wp_ajax_nopriv_somnia_get_free_shipping', 'somnia_get_free_shipping');

add_filter('woocommerce_cross_sells_total', 'bt_limit_cross_sells_display');
add_filter('woocommerce_cross_sells_columns', 'bt_set_cross_sells_columns');
add_filter('woocommerce_product_cross_sells_products_heading', 'bt_custom_cross_sells_title');

function bt_custom_cross_sells_title($title)
{
    $heading = get_field('heading_cross_sells', 'options');
    return !empty($heading) ? esc_html($heading) : esc_html__('You may be interested in…', 'somnia');
}

function bt_limit_cross_sells_display($limit)
{
    return 4;  // Limit to 4 products
}

function bt_set_cross_sells_columns($columns)
{
    return 4;  // Set columns to 2
}

/* add button wishlist */
function somnia_display_button_wishlist($enable_status, $product_id)
{
    if (!$enable_status) {
        return;
    }

    ob_start();
?>
    <a class="bt-icon-btn bt-product-wishlist-btn" href="#" data-id="<?php echo esc_attr($product_id); ?>">
        <svg class="bt-icon-added" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="currentColor">
            <path d="M26.25 11.1562C26.25 18.8125 14.898 25.0097 14.4145 25.2656C14.2871 25.3342 14.1447 25.37 14 25.37C13.8553 25.37 13.7129 25.3342 13.5855 25.2656C13.102 25.0097 1.75 18.8125 1.75 11.1562C1.75203 9.35837 2.46713 7.63471 3.73842 6.36342C5.00971 5.09213 6.73337 4.37703 8.53125 4.375C10.7898 4.375 12.7673 5.34625 14 6.98797C15.2327 5.34625 17.2102 4.375 19.4688 4.375C21.2666 4.37703 22.9903 5.09213 24.2616 6.36342C25.5329 7.63471 26.248 9.35837 26.25 11.1562Z" />
        </svg>
        <svg class="bt-icon-not-added" xmlns="http://www.w3.org/2000/svg" width="21" height="18" viewBox="0 0 21 18" fill="currentColor">
            <path d="M15.1875 0C13.2516 0 11.5566 0.8325 10.5 2.23969C9.44344 0.8325 7.74844 0 5.8125 0C4.27146 0.00173694 2.79404 0.614681 1.70436 1.70436C0.614681 2.79404 0.00173694 4.27146 0 5.8125C0 12.375 9.73031 17.6869 10.1447 17.9062C10.2539 17.965 10.376 17.9958 10.5 17.9958C10.624 17.9958 10.7461 17.965 10.8553 17.9062C11.2697 17.6869 21 12.375 21 5.8125C20.9983 4.27146 20.3853 2.79404 19.2956 1.70436C18.206 0.614681 16.7285 0.00173694 15.1875 0ZM10.5 16.3875C8.78813 15.39 1.5 10.8459 1.5 5.8125C1.50149 4.66921 1.95632 3.57317 2.76475 2.76475C3.57317 1.95632 4.66921 1.50149 5.8125 1.5C7.63594 1.5 9.16687 2.47125 9.80625 4.03125C9.86275 4.16881 9.95888 4.28646 10.0824 4.36926C10.2059 4.45207 10.3513 4.49627 10.5 4.49627C10.6487 4.49627 10.7941 4.45207 10.9176 4.36926C11.0411 4.28646 11.1372 4.16881 11.1937 4.03125C11.8331 2.46844 13.3641 1.5 15.1875 1.5C16.3308 1.50149 17.4268 1.95632 18.2353 2.76475C19.0437 3.57317 19.4985 4.66921 19.5 5.8125C19.5 10.8384 12.21 15.3891 10.5 16.3875Z" />
        </svg>
    </a>
<?php
    return ob_get_clean();
}

/* add button compare */
function somnia_display_button_compare($enable_status, $product_id)
{
    if (!$enable_status) {
        return;
    }

    ob_start();
?>
    <a class="bt-icon-btn bt-product-compare-btn" href="#" data-id="<?php echo esc_attr($product_id); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="currentColor">
            <path d="M12.25 16.6232C12.0179 16.6232 11.7954 16.7154 11.6313 16.8795C11.4672 17.0436 11.375 17.2662 11.375 17.4982V20.6362L8.28188 17.542C7.87419 17.1369 7.55095 16.6549 7.33091 16.1239C7.11086 15.593 6.99839 15.0236 7.00001 14.4489V10.3889C7.82483 10.1759 8.54367 9.66941 9.02178 8.96435C9.4999 8.2593 9.70445 7.40407 9.59711 6.55899C9.48977 5.7139 9.0779 4.93697 8.43871 4.37383C7.79952 3.81069 6.97688 3.5 6.12501 3.5C5.27313 3.5 4.4505 3.81069 3.8113 4.37383C3.17211 4.93697 2.76024 5.7139 2.6529 6.55899C2.54556 7.40407 2.75012 8.2593 3.22823 8.96435C3.70634 9.66941 4.42518 10.1759 5.25001 10.3889V14.45C5.24797 15.2546 5.40545 16.0517 5.71334 16.7951C6.02122 17.5386 6.47341 18.2136 7.04376 18.7812L10.138 21.8732H7.00001C6.76794 21.8732 6.54538 21.9654 6.38129 22.1295C6.2172 22.2936 6.12501 22.5162 6.12501 22.7482C6.12501 22.9803 6.2172 23.2029 6.38129 23.367C6.54538 23.531 6.76794 23.6232 7.00001 23.6232H12.25C12.4821 23.6232 12.7046 23.531 12.8687 23.367C13.0328 23.2029 13.125 22.9803 13.125 22.7482V17.4982C13.125 17.2662 13.0328 17.0436 12.8687 16.8795C12.7046 16.7154 12.4821 16.6232 12.25 16.6232ZM4.37501 6.99824C4.37501 6.65212 4.47764 6.31377 4.66994 6.02599C4.86223 5.7382 5.13554 5.5139 5.45531 5.38145C5.77508 5.24899 6.12695 5.21434 6.46642 5.28186C6.80588 5.34939 7.1177 5.51606 7.36244 5.7608C7.60719 6.00554 7.77386 6.31736 7.84138 6.65683C7.90891 6.99629 7.87425 7.34816 7.7418 7.66793C7.60934 7.9877 7.38504 8.26101 7.09726 8.45331C6.80947 8.6456 6.47113 8.74824 6.12501 8.74824C5.66088 8.74824 5.21576 8.56386 4.88757 8.23567C4.55938 7.90748 4.37501 7.46236 4.37501 6.99824ZM22.75 17.6076V13.5476C22.752 12.7429 22.5946 11.9459 22.2867 11.2024C21.9788 10.459 21.5266 9.78397 20.9563 9.21636L17.862 6.12324H21C21.2321 6.12324 21.4546 6.03105 21.6187 5.86695C21.7828 5.70286 21.875 5.4803 21.875 5.24824C21.875 5.01617 21.7828 4.79361 21.6187 4.62952C21.4546 4.46542 21.2321 4.37324 21 4.37324H15.75C15.5179 4.37324 15.2954 4.46542 15.1313 4.62952C14.9672 4.79361 14.875 5.01617 14.875 5.24824V10.4982C14.875 10.7303 14.9672 10.9529 15.1313 11.117C15.2954 11.281 15.5179 11.3732 15.75 11.3732C15.9821 11.3732 16.2046 11.281 16.3687 11.117C16.5328 10.9529 16.625 10.7303 16.625 10.4982V7.36027L19.7181 10.4545C20.1258 10.8596 20.4491 11.3416 20.6691 11.8726C20.8892 12.4035 21.0016 12.9729 21 13.5476V17.6076C20.1752 17.8206 19.4563 18.3271 18.9782 19.0321C18.5001 19.7372 18.2956 20.5924 18.4029 21.4375C18.5102 22.2826 18.9221 23.0595 19.5613 23.6226C20.2005 24.1858 21.0231 24.4965 21.875 24.4965C22.7269 24.4965 23.5495 24.1858 24.1887 23.6226C24.8279 23.0595 25.2398 22.2826 25.3471 21.4375C25.4545 20.5924 25.2499 19.7372 24.7718 19.0321C24.2937 18.3271 23.5748 17.8206 22.75 17.6076ZM21.875 22.7482C21.5289 22.7482 21.1905 22.6456 20.9028 22.4533C20.615 22.261 20.3907 21.9877 20.2582 21.6679C20.1258 21.3482 20.0911 20.9963 20.1586 20.6568C20.2262 20.3174 20.3928 20.0055 20.6376 19.7608C20.8823 19.5161 21.1941 19.3494 21.5336 19.2819C21.8731 19.2143 22.2249 19.249 22.5447 19.3814C22.8645 19.5139 23.1378 19.7382 23.3301 20.026C23.5224 20.3138 23.625 20.6521 23.625 20.9982C23.625 21.4624 23.4406 21.9075 23.1124 22.2357C22.7843 22.5639 22.3391 22.7482 21.875 22.7482Z" />
        </svg>
    </a>
<?php
    return ob_get_clean();
}

/* add button quick view */
function somnia_display_button_quick_view($enable_status, $product_id)
{
    if (!$enable_status) {
        return;
    }

    ob_start();
?>
    <a class="bt-icon-btn bt-product-quick-view-btn" href="#" data-id="<?php echo esc_attr($product_id); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="currentColor">
            <path d="M27.0495 13.6456C27.0113 13.5592 26.0848 11.5041 24.0253 9.44453C21.2811 6.70031 17.815 5.25 14 5.25C10.185 5.25 6.71891 6.70031 3.97469 9.44453C1.91516 11.5041 0.984376 13.5625 0.95047 13.6456C0.900719 13.7575 0.875011 13.8786 0.875011 14.0011C0.875011 14.1236 0.900719 14.2447 0.95047 14.3566C0.988751 14.443 1.91516 16.497 3.97469 18.5566C6.71891 21.2997 10.185 22.75 14 22.75C17.815 22.75 21.2811 21.2997 24.0253 18.5566C26.0848 16.497 27.0113 14.443 27.0495 14.3566C27.0993 14.2447 27.125 14.1236 27.125 14.0011C27.125 13.8786 27.0993 13.7575 27.0495 13.6456ZM14 21C10.6334 21 7.69235 19.7761 5.25766 17.3633C4.25868 16.3698 3.40877 15.237 2.73438 14C3.40859 12.7629 4.25851 11.63 5.25766 10.6367C7.69235 8.22391 10.6334 7 14 7C17.3666 7 20.3077 8.22391 22.7423 10.6367C23.7433 11.6298 24.595 12.7627 25.2711 14C24.4825 15.4722 21.047 21 14 21ZM14 8.75C12.9616 8.75 11.9466 9.05791 11.0833 9.63478C10.2199 10.2117 9.54699 11.0316 9.14963 11.9909C8.75227 12.9502 8.64831 14.0058 8.85088 15.0242C9.05345 16.0426 9.55346 16.9781 10.2877 17.7123C11.0219 18.4465 11.9574 18.9466 12.9758 19.1491C13.9942 19.3517 15.0498 19.2477 16.0091 18.8504C16.9684 18.453 17.7883 17.7801 18.3652 16.9167C18.9421 16.0534 19.25 15.0384 19.25 14C19.2486 12.6081 18.695 11.2735 17.7107 10.2893C16.7265 9.30504 15.3919 8.75145 14 8.75ZM14 17.5C13.3078 17.5 12.6311 17.2947 12.0555 16.9101C11.4799 16.5256 11.0313 15.9789 10.7664 15.3394C10.5015 14.6999 10.4322 13.9961 10.5673 13.3172C10.7023 12.6383 11.0356 12.0146 11.5251 11.5251C12.0146 11.0356 12.6383 10.7023 13.3172 10.5673C13.9961 10.4322 14.6999 10.5015 15.3394 10.7664C15.9789 11.0313 16.5256 11.4799 16.9101 12.0555C17.2947 12.6311 17.5 13.3078 17.5 14C17.5 14.9283 17.1313 15.8185 16.4749 16.4749C15.8185 17.1313 14.9283 17.5 14 17.5Z" />
        </svg>
    </a>
<?php
    return ob_get_clean();
}

/* add button wishlist, compare and quick view */
function somnia_display_button_wishlist_compare_quick_view()
{
    global $product;

    $archive_shop = function_exists('get_field') ? get_field('archive_shop', 'options') : array();
    $show_wishlist = isset($archive_shop['show_wishlist']) ? $archive_shop['show_wishlist'] : true;
    $show_compare = isset($archive_shop['show_compare']) ? $archive_shop['show_compare'] : true;
    $show_quickview = isset($archive_shop['show_quickview']) ? $archive_shop['show_quickview'] : true;

?>
    <div class="bt-product-icon-btn">
        <?php
        if (!$product->is_type('variable')) {
            do_action('somnia_woocommerce_template_loop_add_to_cart');
        } else {
            do_action('somnia_woocommerce_template_loop_add_to_cart_variable');
        }
        echo somnia_display_button_wishlist($show_wishlist, get_the_ID());
        echo somnia_display_button_compare($show_compare, get_the_ID());
        echo somnia_display_button_quick_view($show_quickview, get_the_ID());
        ?>
    </div>
<?php
}

add_action('somnia_woocommerce_template_loop_list_cta_button', 'somnia_display_button_wishlist_compare_quick_view');


/* add button wishlist and compare */
function somnia_display_button_wishlist_compare()
{
    global $product;

    // Get product if global is not set
    if (!$product) {
        $product = wc_get_product();
    }

    // Check if product is valid
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    $archive_shop = function_exists('get_field') ? get_field('archive_shop', 'options') : array();
    $show_wishlist = isset($archive_shop['show_wishlist']) ? $archive_shop['show_wishlist'] : true;
    $show_compare = isset($archive_shop['show_compare']) ? $archive_shop['show_compare'] : true;

    if (!$show_wishlist && !$show_compare) {
        return;
    }
?>
    <div class="bt-product-icon-btn">
        <?php
        echo somnia_display_button_compare($show_compare, $product->get_id());
        echo somnia_display_button_wishlist($show_wishlist, $product->get_id());
        ?>
    </div>
<?php
}

add_action('woocommerce_after_add_to_cart_button', 'somnia_display_button_wishlist_compare');

// WooCommerce custom field
add_action('woocommerce_product_options_advanced', 'somnia_woocommerce_custom_field');

function somnia_woocommerce_custom_field()
{
    global $post, $product_object;
    // Loop Product Display Options
    echo '<div class="options_group">';
    echo '<h4 style="padding: 10px 12px; margin: 0; border-bottom: 1px solid #eee;">' . __('Loop Product Display', 'somnia') . '</h4>';

    woocommerce_wp_checkbox(array(
        'id' => '_enable_loop_sale_marquee',
        'label' => __('Enable Sale Marquee', 'somnia'),
        'description' => __('Show sale percentage marquee in product loop', 'somnia'),
        'desc_tip' => true
    ));

    woocommerce_wp_checkbox(array(
        'id' => '_enable_loop_countdown',
        'label' => __('Enable Countdown Timer', 'somnia'),
        'description' => __('Show countdown timer in product loop (requires countdown date below)', 'somnia'),
        'desc_tip' => true
    ));

    echo '</div>';
    // Add layout selector
    $layout_options = array(
        'bottom-thumbnail' => __('Bottom Thumbnail', 'somnia'),
        'left-thumbnail' => __('Left Thumbnail', 'somnia'),
        'right-thumbnail' => __('Right Thumbnail', 'somnia'),
        'gallery-one-column' => __('Gallery 1 Column', 'somnia'),
        'gallery-two-columns' => __('Gallery 2 Columns', 'somnia'),
        'gallery-three-columns' => __('Gallery 3 Columns', 'somnia'),
        'gallery-four-columns' => __('Gallery 4 Columns', 'somnia'),
        'gallery-grid-fullwidth' => __('Gallery Grid Fullwidth', 'somnia'),
        'gallery-stacked' => __('Gallery Stacked', 'somnia'),
        'gallery-slider-container' => __('Gallery Slider Container', 'somnia'),
        'gallery-slider-fullwidth' => __('Gallery Slider Fullwidth', 'somnia')
    );

    // Save layout value or use default layout-1
    $layout_value = get_post_meta($post->ID, '_layout_product', true);
    if (empty($layout_value)) {
        $layout_value = 'bottom-thumbnail'; // Default layout
    }

    woocommerce_wp_select(array(
        'id' => '_layout_product',
        'label' => __('Product Layout', 'somnia'),
        'description' => '',
        'options' => $layout_options,
        'value' => $layout_value
    ));

    // Product Info Display Mode Settings
    echo '<div class="options_group">';

    $info_display_options = array(
        'tab' => __('Tab', 'somnia'),
        'toggle' => __('Toggle', 'somnia'),
        'disable' => __('Disable', 'somnia')
    );

    $info_display_value = get_post_meta($post->ID, '_product_info_display_mode', true);
    if (empty($info_display_value)) {
        $info_display_value = 'tab'; // Default
    }

    woocommerce_wp_select(array(
        'id' => '_product_info_display_mode',
        'label' => __('Product Info Display', 'somnia'),
        'description' => __('Choose how to display product information section', 'somnia'),
        'options' => $info_display_options,
        'value' => $info_display_value,
        'desc_tip' => true
    ));

    // Tab Position (only for Tab mode and only for thumbnail layouts)
    $tab_position_options = array(
        'top' => __('Top', 'somnia'),
        'left' => __('Left', 'somnia'),
        'right' => __('Right', 'somnia')
    );

    $tab_position_value = get_post_meta($post->ID, '_product_tab_position', true);
    if (empty($tab_position_value)) {
        $tab_position_value = 'top'; // Default
    }

    woocommerce_wp_select(array(
        'id' => '_product_tab_position',
        'label' => __('Tab Position', 'somnia'),
        'description' => __('Position of tabs (only available for thumbnail layouts)', 'somnia'),
        'options' => $tab_position_options,
        'value' => $tab_position_value,
        'desc_tip' => true,
        'wrapper_class' => 'somnia_tab_position_field'
    ));

    // Toggle Default State (only for Toggle mode)
    $toggle_state_options = array(
        'first' => __('Open First Toggle', 'somnia'),
        'all' => __('Open All Toggles', 'somnia')
    );

    $toggle_state_value = get_post_meta($post->ID, '_product_toggle_state', true);
    if (empty($toggle_state_value)) {
        $toggle_state_value = 'first'; // Default
    }

    woocommerce_wp_select(array(
        'id' => '_product_toggle_state',
        'label' => __('Toggle Default State', 'somnia'),
        'description' => __('Choose which toggles should be open by default', 'somnia'),
        'options' => $toggle_state_options,
        'value' => $toggle_state_value,
        'desc_tip' => true,
        'wrapper_class' => 'somnia_toggle_state_field'
    ));

    echo '</div>';

    // Product Video Settings
    $video_type_options = array(
        'youtube' => __('YouTube', 'somnia'),
        'vimeo' => __('Vimeo', 'somnia'),
        'mp4' => __('MP4', 'somnia')
    );

    $video_type_value = get_post_meta($post->ID, '_product_video_type', true);
    if (empty($video_type_value)) {
        $video_type_value = 'youtube';
    }

    woocommerce_wp_select(array(
        'id' => '_product_video_type',
        'label' => __('Product Video Type', 'somnia'),
        'description' => '',
        'options' => $video_type_options,
        'value' => $video_type_value
    ));

    // Video link input
    woocommerce_wp_text_input(array(
        'id' => '_product_video_link',
        'label' => __('Product Video Link', 'somnia'),
        'description' => __('Enter the video URL (YouTube, Vimeo, or MP4 file URL)', 'somnia'),
        'type' => 'url',
        'placeholder' => 'https://'
    ));

    // Product 360 GLB file upload
    $product_360_file = get_post_meta($post->ID, '_product_360_images', true);

    echo '<p class="form-field _product_360_images_field">';
    echo '<label for="_product_360_images">' . __('Product 360° GLB File', 'somnia') . '</label>';
    echo '<button type="button" class="button upload_360_images_button">' . __('Upload GLB File', 'somnia') . '</button>';
    echo '<input type="hidden" id="_product_360_images" name="_product_360_images" value="' . esc_attr($product_360_file) . '" />';
    echo '<span class="description">' . __('Upload a .glb file for 360° product view', 'somnia') . '</span>';
    echo '</p>';

    // Display selected file name
    if (!empty($product_360_file)) {
        $file_path = get_attached_file($product_360_file);
        $file_name = basename($file_path);
        $file_url = wp_get_attachment_url($product_360_file);

        if ($file_path && file_exists($file_path)) {
            echo '<div class="product-360-preview" style="margin-left: 150px;">';
            echo '<div class="file-preview" style="display: flex; align-items: center; padding: 10px; background: #f5f5f5; border-radius: 4px; max-width: 400px;">';
            echo '<span class="dashicons dashicons-media-document" style="font-size: 24px; margin-right: 10px;"></span>';
            echo '<div style="flex: 1;">';
            echo '<strong>' . esc_html($file_name) . '</strong><br>';
            echo '<small style="color: #666;">' . size_format(filesize($file_path)) . '</small>';
            echo '</div>';
            if ($file_url) {
                echo '<a href="' . esc_url($file_url) . '" target="_blank" class="button button-small" style="margin-left: 10px;">' . __('View', 'somnia') . '</a>';
            }
            echo '<button type="button" class="button button-small remove_360_file_button" style="margin-left: 5px; color: #b32d2e;">' . __('Remove', 'somnia') . '</button>';
            echo '</div>';
            echo '</div>';
        }
    }
    // Add size guide toggle option
    woocommerce_wp_checkbox(array(
        'id' => '_enable_size_guide',
        'label' => __('Enable Size Guide', 'somnia'),
        'description' => __('Show size guide button for this product', 'somnia'),
        'value' => get_post_meta($post->ID, '_enable_size_guide', true)
    ));
    // add product sold
    woocommerce_wp_text_input(array(
        'id' => '_product_sold',
        'label' => __('Product Sold', 'somnia'),
        'description' => '',
        'type' => 'number',
        'custom_attributes' => array(
            'min' => 0
        )
    ));
    // Add product label selector
    $label_options = array(
        '' => __('Select Label', 'somnia'),
        'best-seller' => __('Best Seller', 'somnia'),
        'featured' => __('Featured', 'somnia'),
        'new-arrival' => __('New Arrival', 'somnia'),
        'on-sale' => __('On Sale', 'somnia'),
        'trending' => __('Trending', 'somnia'),
        'hot-deal' => __('Hot Deal', 'somnia'),
        'pre-order' => __('Pre-Order', 'somnia'),
        'exclusive' => __('Exclusive', 'somnia'),
        'top-rated' => __('Top Rated', 'somnia')
    );

    $label_value = get_post_meta($post->ID, '_label', true);

    woocommerce_wp_select(array(
        'id' => '_label',
        'label' => __('Product Label', 'somnia'),
        'description' => '',
        'options' => $label_options,
        'value' => $label_value
    ));

    // Only show checkbox for simple and variable products
    if ($product_object && ($product_object->is_type('simple') || $product_object->is_type('variable'))) {
        woocommerce_wp_text_input(array(
            'id' => '_product_start_datetime',
            'label' => __('Date Start Sale', 'somnia'),
            'description' => __("Set the date and time when this product's sale price will expire.", 'somnia'),
            'type' => 'datetime-local',
            'custom_attributes' => array(
                'step' => '60',  // 1 minute steps
                'pattern' => '[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}'  // YYYY-MM-DDThh:mm format
            )
        ));
        woocommerce_wp_text_input(array(
            'id' => '_product_datetime',
            'label' => __('Product Date & Time Sale', 'somnia'),
            'description' => __("Set the date and time when this product's sale price will expire.", 'somnia'),
            'type' => 'datetime-local',
            'custom_attributes' => array(
                'step' => '60',  // 1 minute steps
                'pattern' => '[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}'  // YYYY-MM-DDThh:mm format
            )
        ));
        woocommerce_wp_checkbox(array(
            'id' => '_disable_sale_price',
            'label' => __('Disable Sale Price', 'somnia'),
            'description' => __('Check this box to automatically remove the sale price when the sale period ends. The product will return to its regular price.', 'somnia')
        ));
        woocommerce_wp_checkbox(array(
            'id' => '_enable_percentage_sold',
            'label' => __('Enable Percentage Sold', 'somnia'),
            'description' => __('Check this box to display the percentage of products sold.', 'somnia')
        ));
        woocommerce_wp_text_input(array(
            'id' => '_product_sold_sale',
            'label' => __('Product Sold Sale', 'somnia'),
            'description' => '',
            'type' => 'number',
            'custom_attributes' => array(
                'min' => 0
            )
        ));
        woocommerce_wp_text_input(array(
            'id' => '_product_stock_sale',
            'label' => __('Product Stock Sale', 'somnia'),
            'description' => '',
            'type' => 'number',
            'custom_attributes' => array(
                'min' => 0
            )
        ));
    }
}

// Add Frequently Bought Together field to Linked Products tab
add_action('woocommerce_product_options_related', 'somnia_add_frequently_bought_together_field');

function somnia_add_frequently_bought_together_field()
{
    global $post;

    $fbt_title = get_post_meta($post->ID, '_frequently_bought_together_title', true);
?>
    <p class="form-field">
        <label for="frequently_bought_together_title">
            <?php _e('Title (FBT)', 'somnia'); ?>
        </label>
        <input type="text" class="short" name="frequently_bought_together_title" id="frequently_bought_together_title" value="<?php echo esc_attr($fbt_title); ?>" placeholder="<?php esc_attr_e('Title for Frequently Bought Together', 'somnia'); ?>" />
        <?php echo wc_help_tip(__('Custom title for the frequently bought together section.', 'somnia')); ?>
    </p>
    <p class="form-field">
        <label for="frequently_bought_together_ids">
            <?php _e('Frequently Bought Together', 'somnia'); ?>

        </label>
        <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="frequently_bought_together_ids" name="frequently_bought_together_ids[]" data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'somnia'); ?>" data-action="somnia_json_search_simple_products" data-exclude="<?php echo intval($post->ID); ?>">
            <?php
            $product_ids = get_post_meta($post->ID, '_frequently_bought_together_ids', true);

            if (!empty($product_ids) && is_array($product_ids)) {
                foreach ($product_ids as $product_id) {
                    $product = wc_get_product($product_id);
                    if (is_object($product)) {
                        echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . esc_html(wp_strip_all_tags($product->get_formatted_name())) . '</option>';
                    }
                }
            }
            ?>
        </select>
        <?php echo wc_help_tip(__('Select products (simple or variations) that are frequently bought together with this product.', 'somnia')); ?>
    </p>
    <?php
}

// AJAX handler to search simple products and variations Frequently Bought Together
add_action('wp_ajax_somnia_json_search_simple_products', 'somnia_json_search_simple_products');

function somnia_json_search_simple_products()
{
    check_ajax_referer('search-products', 'security');

    if (!current_user_can('edit_products')) {
        wp_die(-1);
    }

    $term = isset($_GET['term']) ? (string) wc_clean(wp_unslash($_GET['term'])) : '';
    $exclude = isset($_GET['exclude']) ? array_map('intval', (array) $_GET['exclude']) : array();

    if (empty($term)) {
        wp_die();
    }

    $data_store = WC_Data_Store::load('product');
    $ids = $data_store->search_products($term, '', true, false, null);

    $products = array();

    foreach ($ids as $id) {
        $product = wc_get_product($id);

        if (!$product || in_array($id, $exclude)) {
            continue;
        }

        // Include simple products
        if ($product->is_type('simple')) {
            $formatted_name = $product->get_formatted_name();
            $products[$id] = rawurldecode(wp_strip_all_tags($formatted_name));
        }

        // Include variations (not parent variable product)
        if ($product->is_type('variable')) {
            $variations = $product->get_available_variations();
            foreach ($variations as $variation) {
                $variation_id = $variation['variation_id'];
                if (in_array($variation_id, $exclude)) {
                    continue;
                }

                $variation_obj = wc_get_product($variation_id);
                if ($variation_obj) {
                    $attributes = [];
                    foreach ($variation['attributes'] as $attr_name => $attr_value) {
                        $attr_label = str_replace('attribute_', '', $attr_name);
                        $attr_label = str_replace('pa_', '', $attr_label);
                        $attr_label = ucfirst($attr_label);

                        // Get term name if it's a taxonomy
                        if (taxonomy_exists($attr_name)) {
                            $term_obj = get_term_by('slug', $attr_value, $attr_name);
                            $attr_value = $term_obj ? $term_obj->name : $attr_value;
                        }

                        $attributes[] = ucfirst($attr_value);
                    }
                    $variation_name = $product->get_name() . ' - ' . implode('/', $attributes);
                    $products[$variation_id] = rawurldecode($variation_name);
                }
            }
        }
    }

    wp_send_json($products);
}

add_action('woocommerce_process_product_meta', 'somnia_woocommerce_custom_field_save');

function somnia_woocommerce_custom_field_save($post_id)
{
    // Save Frequently Bought Together title
    if (isset($_POST['frequently_bought_together_title'])) {
        $title = sanitize_text_field($_POST['frequently_bought_together_title']);
        update_post_meta($post_id, '_frequently_bought_together_title', $title);
    } else {
        delete_post_meta($post_id, '_frequently_bought_together_title');
    }

    // Save Frequently Bought Together products
    if (isset($_POST['frequently_bought_together_ids'])) {
        $product_ids = array_map('intval', (array) $_POST['frequently_bought_together_ids']);

        // Filter to only save simple products and variations
        $valid_product_ids = array();
        foreach ($product_ids as $id) {
            $product = wc_get_product($id);
            if ($product && ($product->is_type('simple') || $product->is_type('variation'))) {
                $valid_product_ids[] = $id;
            }
        }

        if (!empty($valid_product_ids)) {
            update_post_meta($post_id, '_frequently_bought_together_ids', $valid_product_ids);
        } else {
            delete_post_meta($post_id, '_frequently_bought_together_ids');
        }
    } else {
        delete_post_meta($post_id, '_frequently_bought_together_ids');
    }

    if (isset($_POST['_label'])) {
        $label = sanitize_text_field($_POST['_label']);
        update_post_meta($post_id, '_label', $label);
    }
    if (isset($_POST['_layout_product'])) {
        $layout = sanitize_text_field($_POST['_layout_product']);
        update_post_meta($post_id, '_layout_product', $layout);
    }
    // Save product info display mode
    if (isset($_POST['_product_info_display_mode'])) {
        $info_display_mode = sanitize_text_field($_POST['_product_info_display_mode']);
        update_post_meta($post_id, '_product_info_display_mode', $info_display_mode);
    }
    // Save product tab position
    if (isset($_POST['_product_tab_position'])) {
        $tab_position = sanitize_text_field($_POST['_product_tab_position']);
        update_post_meta($post_id, '_product_tab_position', $tab_position);
    }
    // Save product toggle state
    if (isset($_POST['_product_toggle_state'])) {
        $toggle_state = sanitize_text_field($_POST['_product_toggle_state']);
        update_post_meta($post_id, '_product_toggle_state', $toggle_state);
    }
    // Save product video type
    if (isset($_POST['_product_video_type'])) {
        $video_type = sanitize_text_field($_POST['_product_video_type']);
        update_post_meta($post_id, '_product_video_type', $video_type);
    }
    // Save product video link
    if (isset($_POST['_product_video_link'])) {
        $video_link = esc_url_raw($_POST['_product_video_link']);
        update_post_meta($post_id, '_product_video_link', $video_link);
    }
    // Save product 360 images
    if (isset($_POST['_product_360_images'])) {
        $images_360 = sanitize_text_field($_POST['_product_360_images']);
        update_post_meta($post_id, '_product_360_images', $images_360);
    }
    // Save size guide toggle option
    if (isset($_POST['_enable_size_guide'])) {
        $enable_size_guide = sanitize_text_field($_POST['_enable_size_guide']);
        update_post_meta($post_id, '_enable_size_guide', $enable_size_guide);
    }

    // Save loop product display options
    $enable_loop_sale_marquee = isset($_POST['_enable_loop_sale_marquee']) ? 'yes' : 'no';
    update_post_meta($post_id, '_enable_loop_sale_marquee', $enable_loop_sale_marquee);

    $enable_loop_countdown = isset($_POST['_enable_loop_countdown']) ? 'yes' : 'no';
    update_post_meta($post_id, '_enable_loop_countdown', $enable_loop_countdown);
    if (isset($_POST['_product_sold'])) {
        $product_sold = intval($_POST['_product_sold']);
        update_post_meta($post_id, '_product_sold', $product_sold);
    }
    if (isset($_POST['_product_datetime'])) {
        $datetime = sanitize_text_field($_POST['_product_datetime']);
        update_post_meta($post_id, '_product_datetime', $datetime);
        if (empty(get_post_meta($post_id, '_product_start_datetime', true))) {
            update_post_meta($post_id, '_product_start_datetime', current_time('Y-m-d\TH:i'));
        }
    }
    $disable_sale_price = isset($_POST['_disable_sale_price']) ? 'yes' : 'no';
    update_post_meta($post_id, '_disable_sale_price', $disable_sale_price);
    $enable_percentage_sold = isset($_POST['_enable_percentage_sold']) ? 'yes' : 'no';
    update_post_meta($post_id, '_enable_percentage_sold', $enable_percentage_sold);
    if (isset($_POST['_product_sold_sale'])) {
        $product_sold_sale = intval($_POST['_product_sold_sale']);
        update_post_meta($post_id, '_product_sold_sale', $product_sold_sale);
    }
    if (isset($_POST['_product_stock_sale'])) {
        $product_stock_sale = intval($_POST['_product_stock_sale']);
        update_post_meta($post_id, '_product_stock_sale', $product_stock_sale);
    }
}

function somnia_convert_title_to_slug($title)
{
    $slug = strtolower($title);
    $slug = str_replace(' ', '-', $slug);
    $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');

    return $slug;
}

add_action('somnia_woocommerce_shop_loop_item_label', 'somnia_woocommerce_product_label', 10);

function somnia_woocommerce_product_label()
{
    global $product;

    // Get product if global is not set
    if (!$product) {
        $product = wc_get_product();
    }

    // Check if product is valid
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    $label = get_post_meta($product->get_id(), '_label', true);
    $label_text = str_replace('-', ' ', $label);

    if (!empty($label)) {
        echo '<div class="woocommerce-product-label ' . esc_attr(somnia_convert_title_to_slug($label)) . '">' . esc_html($label_text) . '</div>';
    }
}
// hook button buy now after add to cart
add_action('woocommerce_after_add_to_cart_button', 'somnia_display_button_buy_now');
function somnia_display_button_buy_now()
{
    global $product;

    // Get product if global is not set
    if (!$product) {
        $product = wc_get_product();
    }

    // Check if product is valid
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    if ($product->is_type('simple')) {
        if ($product->is_in_stock() && $product->is_purchasable()) {
            echo '<div class="bt-button-buy-now">';
            echo '<a href="#" class="button" data-id="' . get_the_ID() . '">' . esc_html__('Buy it now ', 'somnia') . '</a>';
            echo '</div>';
        }
    } else if ($product->is_type('variable')) {
        if ($product->is_type('variable')) {
            $variations = $product->get_available_variations();
            if (!empty($variations)) {
                // Check if there's a default variation set
                $default_variation_id = 0;
                if (function_exists('get_default_variation_id')) {
                    $default_variation_id = get_default_variation_id($product);
                }

                $button_class = 'button';
                $data_variation = '';

                // If default variation ID exists, add data-variation and remove disabled class
                if ($default_variation_id && $default_variation_id > 0) {
                    $data_variation = ' data-variation="' . esc_attr($default_variation_id) . '"';
                } else {
                    $button_class .= ' disabled';
                }

                echo '<div class="bt-button-buy-now">';
                echo '<a href="#" class="' . esc_attr($button_class) . '" data-id="' . get_the_ID() . '"' . $data_variation . '>' . esc_html__('Buy it now ', 'somnia') . '</a>';
                echo '</div>';
            }
        }
    } else if ($product->is_type('grouped')) {
        if ($product->is_type('grouped')) {
            $children = $product->get_children();
            if (!empty($children)) {
                echo '<div class="bt-button-buy-now">';
                echo '<a href="#" class="button disabled" data-id="' . get_the_ID() . '">' . esc_html__('Buy it now ', 'somnia') . '</a>';
                echo '</div>';
            }
        }
    }
}

/* ajax by now product */
add_action('wp_ajax_somnia_products_buy_now', 'somnia_products_buy_now');
add_action('wp_ajax_nopriv_somnia_products_buy_now', 'somnia_products_buy_now');

function somnia_products_buy_now()
{
    if (isset($_POST['product_id_grouped']) && !empty($_POST['product_id_grouped'])) {
        $product_data = explode(',', $_POST['product_id_grouped']);
        // Loop through product data and add to cart
        foreach ($product_data as $item) {
            $item_data = explode(':', $item);
            $product_id = intval($item_data[0]);
            $quantity = isset($item_data[1]) ? intval($item_data[1]) : 1;

            WC()->cart->add_to_cart($product_id, $quantity);
        }
        $redirect_url = wc_get_checkout_url();
        wp_send_json_success(array('redirect_url' => $redirect_url));
        wp_die();
    } else if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $variation = $variation_id > 0 ? array() : null;

        $product = wc_get_product($product_id);

        if ($product) {
            if ($variation_id > 0 && $product->is_type('variable')) {
                $variation_product = wc_get_product($variation_id);
                if ($variation_product) {
                    $attributes = $variation_product->get_attributes();
                    foreach ($attributes as $attribute_name => $attribute_value) {
                        $variation[$attribute_name] = $attribute_value;
                    }
                }
            }

            WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);

            $redirect_url = wc_get_checkout_url();
            wp_send_json_success(array('redirect_url' => $redirect_url));
            wp_die();
        }
    }
}

/* ajax add to cart variable */

function somnia_products_add_to_cart_variable()
{
    $product_id = intval($_POST['product_id']);
    $variation_id = intval($_POST['variation_id']);
    $quantity = intval($_POST['quantity']);
    $fbt_product_id = isset($_POST['fbt_product_id']) ? intval($_POST['fbt_product_id']) : 0;
    $variation = $variation_id > 0 ? array() : null;

    $product = wc_get_product($product_id);
    if ($product) {
        if ($variation_id > 0 && $product->is_type('variable')) {
            $variation_product = wc_get_product($variation_id);
            if ($variation_product) {
                $attributes = $variation_product->get_attributes();
                foreach ($attributes as $attribute_name => $attribute_value) {
                    $variation[$attribute_name] = $attribute_value;
                }
            }
        }

        // Add to cart - hook 'woocommerce_add_to_cart' will handle fbt_product_id automatically
        WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);

        // Return fbt_product_id in response if it exists
        $response = array('success' => true);
        if ($fbt_product_id > 0) {
            $response['fbt_product_id'] = $fbt_product_id;
        }

        wp_send_json_success($response);
        wp_die();
    }
}
add_action('wp_ajax_somnia_products_add_to_cart_variable', 'somnia_products_add_to_cart_variable');
add_action('wp_ajax_nopriv_somnia_products_add_to_cart_variable', 'somnia_products_add_to_cart_variable');

/* ajax add to cart simple */

function somnia_products_add_to_cart_simple()
{
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $fbt_product_id = isset($_POST['fbt_product_id']) ? intval($_POST['fbt_product_id']) : 0;

    $product = wc_get_product($product_id);
    if ($product && $product->is_type('simple')) {
        // Add to cart - hook 'woocommerce_add_to_cart' will handle fbt_product_id automatically
        WC()->cart->add_to_cart($product_id, $quantity);

        // Return fbt_product_id in response if it exists
        $response = array('success' => true);
        if ($fbt_product_id > 0) {
            $response['fbt_product_id'] = $fbt_product_id;
        }

        wp_send_json_success($response);
        wp_die();
    }
}
add_action('wp_ajax_somnia_products_add_to_cart_simple', 'somnia_products_add_to_cart_simple');
add_action('wp_ajax_nopriv_somnia_products_add_to_cart_simple', 'somnia_products_add_to_cart_simple');

/* Ensure shipping is calculated before mini cart is rendered */
function somnia_calculate_shipping_before_mini_cart()
{
    if (WC()->cart && !WC()->cart->is_empty() && WC()->cart->needs_shipping()) {
        // Set constant to ensure WooCommerce processes cart correctly
        wc_maybe_define_constant('WOOCOMMERCE_CART', true);

        // Calculate shipping if not already calculated
        if (!WC()->cart->has_calculated_shipping()) {
            WC()->cart->calculate_shipping();
        }

        // Ensure totals are calculated
        WC()->cart->calculate_totals();
    }
}
add_action('woocommerce_before_mini_cart', 'somnia_calculate_shipping_before_mini_cart', 5);

/* Product share */
if (!function_exists('somnia_product_share_render')) {
    function somnia_product_share_render()
    {
        $social_item = array();
        $social_item[] = '<li>
                        <a target="_blank" data-btIcon="fa fa-linkedin" data-toggle="tooltip" title="' . esc_attr__('Linkedin', 'somnia') . '" href="https://www.linkedin.com/shareArticle?url=' . get_the_permalink() . '">
                          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/>
                          </svg>
                        </a>
                      </li>';
        $social_item[] = '<li>
                        <a target="_blank" data-btIcon="fa fa-facebook" data-toggle="tooltip" title="' . esc_attr__('Facebook', 'somnia') . '" href="https://www.facebook.com/sharer/sharer.php?u=' . get_the_permalink() . '">
                          <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512" fill="currentColor">
                            <path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/>
                          </svg>
                        </a>
                      </li>';
        $social_item[] = '<li>
                      <a target="_blank" data-btIcon="fa fa-twitter" data-toggle="tooltip" title="' . esc_attr__('Twitter', 'somnia') . '" href="https://twitter.com/share?url=' . get_the_permalink() . '">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor">
                          <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
                        </svg>
                      </a>
                    </li>';
        $social_item[] = '<li>
                        <a target="_blank" data-btIcon="fa fa-pinterest" data-toggle="tooltip" title="' . esc_attr__('Pinterest', 'somnia') . '" href="https://pinterest.com/pin/create/button/?url=' . get_the_permalink() . '&media=' . wp_get_attachment_url(get_post_thumbnail_id()) . '&description=' . get_the_title() . '">
                          <svg xmlns="http://www.w3.org/2000/svg" width="13" height="16" viewBox="0 0 13 16" fill="currentColor">
                            <path d="M6.53967 0C3.2506 0 0 2.19271 0 5.74145C0 7.99827 1.26947 9.28056 2.03884 9.28056C2.3562 9.28056 2.53893 8.39578 2.53893 8.14574C2.53893 7.8476 1.77918 7.21287 1.77918 5.97226C1.77918 3.39486 3.74108 1.5676 6.28001 1.5676C8.4631 1.5676 10.0788 2.80821 10.0788 5.08748C10.0788 6.78972 9.39597 9.98261 7.18402 9.98261C6.3858 9.98261 5.70298 9.40558 5.70298 8.57851C5.70298 7.36675 6.54929 6.19345 6.54929 4.94322C6.54929 2.82103 3.53912 3.20572 3.53912 5.7703C3.53912 6.30886 3.60644 6.90512 3.84686 7.3956C3.40448 9.2998 2.50046 12.1369 2.50046 14.0988C2.50046 14.7046 2.58702 15.3009 2.64472 15.9068C2.75371 16.0286 2.69922 16.0158 2.86591 15.9549C4.4816 13.7429 4.42389 13.3102 5.1548 10.4154C5.5491 11.1655 6.56852 11.5694 7.37636 11.5694C10.7808 11.5694 12.31 8.25152 12.31 5.26059C12.31 2.07731 9.55946 0 6.53967 0Z"/>
                          </svg>
                        </a>
                      </li>';

        ob_start();
        if (is_singular('product')) {
    ?>
            <div class="bt-product-share">
                <div id="bt_product_share" class="bt-product-share__popup mfp-content__popup mfp-hide">
                    <div class="bt-product-share__content mfp-content__inner">
                        <h3 class="bt-product-share__title"><?php echo esc_html__('Share', 'somnia'); ?></h3>
                        <?php
                        if (!empty($social_item)) {
                            echo '<ul class="bt-product-share__socials">' . implode(' ', $social_item) . '</ul>';
                        }
                        ?>
                        <div class="bt-product-share__link bt-copy-link-wrap">
                            <h5 class="bt-copy-link-title"><?php echo esc_html__('Copy URL', 'somnia'); ?></h5>
                            <form class="bt-product-share-form">
                                <input id="bt-product-share-url" type="text" value="<?php the_permalink(); ?>" readonly="">
                                <button class="button bt-copy-btn" data-copy="<?php echo esc_attr__('Copy', 'somnia'); ?>" data-copied="<?php echo esc_attr__('Copied', 'somnia'); ?>"><?php echo esc_html__('Copy', 'somnia'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>

                <a href="#bt_product_share" class="bt-product-share__link bt-js-open-popup-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M7.926 10.898 15 7.727m-7.074 5.39L15 16.29M8 12a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm12 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm0-11a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                    </svg>
                    <?php echo esc_html__('Share', 'somnia'); ?>
                </a>
            </div>
        <?php
        }

        return ob_get_clean();
    }
}

add_action('wp_ajax_somnia_remove_section', 'somnia_remove_section');
add_action('wp_ajax_nopriv_somnia_remove_section', 'somnia_remove_section');

function somnia_remove_section()
{
    session_start();
    if (isset($_SESSION['coupon'])) {
        unset($_SESSION['coupon']);
    }
}

// search live product header
function somnia_search_live()
{
    $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
    $category_slug = isset($_POST['category_slug']) ? sanitize_text_field($_POST['category_slug']) : '';

    // Get widget settings for category filtering
    $widget_category_include = isset($_POST['widget_category_include']) ? sanitize_text_field($_POST['widget_category_include']) : '';
    $widget_category_exclude = isset($_POST['widget_category_exclude']) ? sanitize_text_field($_POST['widget_category_exclude']) : '';

    // Get autocomplete limit setting
    $autocomplete_limit = isset($_POST['autocomplete_limit']) ? intval($_POST['autocomplete_limit']) : 5;

    // Validate: -1 for unlimited, or between 1 and 50. If 0 or invalid, default to 5
    if ($autocomplete_limit === 0) {
        $autocomplete_limit = 5; // Default to 5 if 0 is provided
    } elseif ($autocomplete_limit !== -1) {
        $autocomplete_limit = max(1, min(50, $autocomplete_limit));
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $autocomplete_limit,
        's' => $search_term,
        'tax_query' => array()
    );

    // Build tax_query based on widget settings and user selection
    $tax_query_parts = array();

    // If user selected a specific category from dropdown
    if (!empty($category_slug)) {
        $tax_query_parts[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $category_slug
        );
    }
    // If no category selected but widget has category include settings
    // widget_category_include now contains slugs (comma-separated), not IDs
    elseif (!empty($widget_category_include)) {
        $include_slugs = array_map('trim', explode(',', $widget_category_include));
        $include_slugs = array_filter($include_slugs); // Remove empty values
        if (!empty($include_slugs)) {
            $tax_query_parts[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $include_slugs,
                'operator' => 'IN'
            );
        }
    }

    // If widget has category exclude settings
    // widget_category_exclude now contains slugs (comma-separated), not IDs
    if (!empty($widget_category_exclude)) {
        $exclude_slugs = array_map('trim', explode(',', $widget_category_exclude));
        $exclude_slugs = array_filter($exclude_slugs); // Remove empty values
        if (!empty($exclude_slugs)) {
            $tax_query_parts[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $exclude_slugs,
                'operator' => 'NOT IN'
            );
        }
    }

    // Apply tax_query if we have any conditions
    if (!empty($tax_query_parts)) {
        if (count($tax_query_parts) > 1) {
            $args['tax_query']['relation'] = 'AND';
        }
        $args['tax_query'] = array_merge($args['tax_query'], $tax_query_parts);
    }

    $query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product(get_the_ID());
            if (!$product) {
                continue;
            }
            $product_price = $product->get_price_html();
        ?>
            <div class="bt-product-item">
                <div class="bt-product-thumb">
                    <a href="<?php echo esc_url(get_permalink()); ?>" class="bt-thumb">
                        <?php echo wp_kses_post($product->get_image('medium')); ?>
                    </a>
                    <div class="bt-product-title">
                        <h3 class="bt-title">
                            <a href="<?php echo esc_url(get_permalink()); ?>">
                                <?php echo esc_html($product->get_name()); ?>
                            </a>
                        </h3>
                        <?php if ($product_price) : ?>
                            <span><?php echo wp_kses_post($product_price); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bt-product-add-to-cart">
                    <?php if ($product->is_type('simple')) { ?>
                        <a href="?add-to-cart=<?php echo esc_attr(get_the_ID()); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr(get_the_ID()); ?>" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart bt-btn-add-to-cart" data-product_id="<?php echo esc_attr(get_the_ID()); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
                    <?php } else { ?>
                        <a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>" class="bt-button bt-button-hover bt-btn-view-product"><?php echo esc_html__('View Product', 'somnia') ?></a>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
        wp_reset_postdata();
        $output['items'] = ob_get_clean();
        $output['has_products'] = true;
    } else {
        $output['items'] = '<div class="bt-no-results"><svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="currentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.13,104.13,0,0,0,128,24Zm-18.34,98.34a8,8,0,0,1-11.32,11.32L88,123.31,77.66,133.66a8,8,0,0,1-11.32-11.32L76.69,112,66.34,101.66A8,8,0,0,1,77.66,90.34L88,100.69,98.34,90.34a8,8,0,0,1,11.32,11.32L99.31,112ZM128,192a12,12,0,1,1,12-12A12,12,0,0,1,128,192Zm61.66-69.66a8,8,0,0,1-11.32,11.32L168,123.31l-10.34,10.35a8,8,0,0,1-11.32-11.32L156.69,112l-10.35-10.34a8,8,0,0,1,11.32-11.32L168,100.69l10.34-10.35a8,8,0,0,1,11.32,11.32L179.31,112Z"></path></svg>' . esc_html__('Oops! We couldn\'t find any products matching your search. Try adjusting your filters or explore our full collection.', 'somnia') . '</div>';
        $output['has_products'] = false;
    }

    wp_send_json_success($output);

    die();
}

add_action('wp_ajax_somnia_search_live', 'somnia_search_live');
add_action('wp_ajax_nopriv_somnia_search_live', 'somnia_search_live');

// AJAX handler for search product style 1 (grid layout)
function somnia_search_live_style1()
{
    $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
    $category_slug = isset($_POST['category_slug']) ? sanitize_text_field($_POST['category_slug']) : '';
    $is_mobile = isset($_POST['is_mobile']) ? filter_var($_POST['is_mobile'], FILTER_VALIDATE_BOOLEAN) : false;

    // Get widget settings for category filtering
    $widget_category_include = isset($_POST['widget_category_include']) ? sanitize_text_field($_POST['widget_category_include']) : '';
    $widget_category_exclude = isset($_POST['widget_category_exclude']) ? sanitize_text_field($_POST['widget_category_exclude']) : '';

    // Get autocomplete limit setting
    $autocomplete_limit = isset($_POST['autocomplete_limit']) ? intval($_POST['autocomplete_limit']) : 8;

    // Validate: -1 for unlimited, or between 1 and 50. If 0 or invalid, default to 8
    if ($autocomplete_limit === 0) {
        $autocomplete_limit = 8;
    } elseif ($autocomplete_limit !== -1) {
        $autocomplete_limit = max(1, min(50, $autocomplete_limit));
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $autocomplete_limit,
        's' => $search_term,
        'tax_query' => array()
    );

    // Build tax_query based on widget settings and user selection
    $tax_query_parts = array();

    // If user selected a specific category from dropdown
    if (!empty($category_slug)) {
        $tax_query_parts[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $category_slug
        );
    }
    // If no category selected but widget has category include settings
    // widget_category_include now contains slugs (comma-separated), not IDs
    elseif (!empty($widget_category_include)) {
        $include_slugs = array_map('trim', explode(',', $widget_category_include));
        $include_slugs = array_filter($include_slugs); // Remove empty values
        if (!empty($include_slugs)) {
            $tax_query_parts[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $include_slugs,
                'operator' => 'IN'
            );
        }
    }

    // If widget has category exclude settings
    // widget_category_exclude now contains slugs (comma-separated), not IDs
    if (!empty($widget_category_exclude)) {
        $exclude_slugs = array_map('trim', explode(',', $widget_category_exclude));
        $exclude_slugs = array_filter($exclude_slugs); // Remove empty values
        if (!empty($exclude_slugs)) {
            $tax_query_parts[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $exclude_slugs,
                'operator' => 'NOT IN'
            );
        }
    }

    // Apply tax_query if we have any conditions
    if (!empty($tax_query_parts)) {
        if (count($tax_query_parts) > 1) {
            $args['tax_query']['relation'] = 'AND';
        }
        $args['tax_query'] = array_merge($args['tax_query'], $tax_query_parts);
    }

    $query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) {
        if ($is_mobile) {
            // Render mobile layout (horizontal list)
            while ($query->have_posts()) {
                $query->the_post();
                global $product;
                $product_id = get_the_ID();
                $product_link = get_permalink($product_id);
                $product_image = $product->get_image('medium');
                $product_title = $product->get_name();
                $product_price = $product->get_price_html();
            ?>
                <div class="bt-product-item">
                    <div class="bt-product-thumb">
                        <a href="<?php echo esc_url($product_link); ?>" class="bt-thumb">
                            <?php echo wp_kses_post($product_image); ?>
                        </a>
                        <div class="bt-product-title">
                            <h3 class="bt-title">
                                <a href="<?php echo esc_url($product_link); ?>">
                                    <?php echo esc_html($product_title); ?>
                                </a>
                            </h3>
                            <?php if ($product_price) : ?>
                                <span><?php echo wp_kses_post($product_price); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="bt-product-add-to-cart">
                        <?php if ($product->is_type('simple')) { ?>
                            <a href="?add-to-cart=<?php echo esc_attr($product_id); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product_id); ?>" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart bt-btn-add-to-cart" data-product_id="<?php echo esc_attr($product_id); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
                        <?php } else { ?>
                            <a href="<?php echo esc_url($product_link); ?>" class="bt-button bt-button-hover bt-btn-view-product"><?php echo esc_html__('View Product', 'somnia') ?></a>
                        <?php } ?>
                    </div>
                </div>
            <?php
            }
        } else {
            // Render desktop layout (WooCommerce template)
            while ($query->have_posts()) {
                $query->the_post();
                wc_get_template_part('content', 'product');
            }
        }
        wp_reset_postdata();
        $output['items'] = ob_get_clean();
        $output['has_products'] = true;
    } else {
        $output['items'] = '<div class="bt-no-results"><svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" fill="currentColor" viewBox="0 0 256 256"><path d="M128,24A104,104,0,1,0,232,128,104.13,104.13,0,0,0,128,24Zm-18.34,98.34a8,8,0,0,1-11.32,11.32L88,123.31,77.66,133.66a8,8,0,0,1-11.32-11.32L76.69,112,66.34,101.66A8,8,0,0,1,77.66,90.34L88,100.69,98.34,90.34a8,8,0,0,1,11.32,11.32L99.31,112ZM128,192a12,12,0,1,1,12-12A12,12,0,0,1,128,192Zm61.66-69.66a8,8,0,0,1-11.32,11.32L168,123.31l-10.34,10.35a8,8,0,0,1-11.32-11.32L156.69,112l-10.35-10.34a8,8,0,0,1,11.32-11.32L168,100.69l10.34-10.35a8,8,0,0,1,11.32,11.32L179.31,112Z"></path></svg>' . esc_html__('Oops! We couldn\'t find any products matching your search. Try adjusting your filters or explore our full collection.', 'somnia') . '</div>';
        $output['has_products'] = false;
    }

    wp_send_json_success($output);

    die();
}

add_action('wp_ajax_somnia_search_live_style1', 'somnia_search_live_style1');
add_action('wp_ajax_nopriv_somnia_search_live_style1', 'somnia_search_live_style1');

// Add multiple to cart ajax widget hotspot
function somnia_add_multiple_to_cart()
{
    if (!isset($_POST['product_ids']) || empty($_POST['product_ids'])) {
        wp_send_json_error(__('No products selected', 'somnia'));
        return;
    }

    $product_ids = $_POST['product_ids'];
    $added_count = 0;
    $cart_count = 0;

    foreach ($product_ids as $product_id) {
        $product_id = absint($product_id);
        if ($product_id > 0) {
            $added = WC()->cart->add_to_cart($product_id);
            if ($added) {
                $added_count++;
            }
        }
    }

    if ($added_count > 0) {
        $cart_count = WC()->cart->get_cart_contents_count();
        wp_send_json_success(array(
            'message' => sprintf(__('%d products added to cart', 'somnia'), $added_count),
            'cart_count' => $cart_count
        ));
    } else {
        wp_send_json_error(__('Failed to add products to cart', 'somnia'));
    }
}

add_action('wp_ajax_somnia_add_multiple_to_cart', 'somnia_add_multiple_to_cart');
add_action('wp_ajax_nopriv_somnia_add_multiple_to_cart', 'somnia_add_multiple_to_cart');
/* add multiple to cart variable widget hotspot normal */
function somnia_add_multiple_to_cart_variable()
{
    if (!isset($_POST['product_ids']) || empty($_POST['product_ids'])) {
        wp_send_json_error(__('No products selected', 'somnia'));
        return;
    }

    $product_ids = $_POST['product_ids'];
    $added_count = 0;
    $cart_count = 0;

    if (!is_array($product_ids)) {
        wp_send_json_error(__('Invalid product data', 'somnia'));
        return;
    }

    foreach ($product_ids as $item) {
        // Support both array and object format, and fallback to scalar for backward compatibility
        if (is_array($item)) {
            $product_id = isset($item['product_id']) ? absint($item['product_id']) : 0;
            $variation_id = isset($item['variation_id']) ? absint($item['variation_id']) : 0;
        } elseif (is_object($item)) {
            $product_id = isset($item->product_id) ? absint($item->product_id) : 0;
            $variation_id = isset($item->variation_id) ? absint($item->variation_id) : 0;
        } else {
            $product_id = absint($item);
            $variation_id = 0;
        }

        if ($product_id > 0) {
            if ($variation_id && $variation_id > 0) {
                // Add variable product to cart
                $added = WC()->cart->add_to_cart($product_id, 1, $variation_id);
            } else {
                // Add simple product to cart
                $added = WC()->cart->add_to_cart($product_id);
            }
            if ($added) {
                $added_count++;
            }
        }
    }

    if ($added_count > 0) {
        $cart_count = WC()->cart->get_cart_contents_count();
        wp_send_json_success(array(
            'message' => sprintf(__('%d products added to cart', 'somnia'), $added_count),
            'cart_count' => $cart_count
        ));
    } else {
        wp_send_json_error(__('Failed to add products to cart', 'somnia'));
    }
}
add_action('wp_ajax_somnia_add_multiple_to_cart_variable', 'somnia_add_multiple_to_cart_variable');
add_action('wp_ajax_nopriv_somnia_add_multiple_to_cart_variable', 'somnia_add_multiple_to_cart_variable');
// AJAX handler for loading recently viewed products
function somnia_load_recently_viewed_products()
{
    if (!isset($_POST['recently_viewed']) || !is_array($_POST['recently_viewed'])) {
        wp_send_json_error();
        return;
    }

    $recently_viewed_ids = array_map('intval', $_POST['recently_viewed']);
    $recently_viewed_products = array();

    // Skip first ID and get next 4 products
    $count = 0;
    foreach ($recently_viewed_ids as $product_id) {
        if ($count > 0 && $count <= 4) {  // Skip first item and limit to 4 items
            $product = wc_get_product($product_id);
            if ($product && $product->is_visible()) {
                $recently_viewed_products[] = $product;
            }
        }
        $count++;
    }

    ob_start();
    if (!empty($recently_viewed_products)) {
        echo '<div class="woocommerce-loop-products products columns-4">';
        foreach ($recently_viewed_products as $recent_product) {
            $post_object = get_post($recent_product->get_id());
            setup_postdata($GLOBALS['post'] = &$post_object);
            wc_get_template_part('content', 'product');
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p class="no-products">' . esc_html__('No recently viewed products.', 'somnia') . '</p>';
    }
    $content = ob_get_clean();

    wp_send_json_success($content);
}

add_action('wp_ajax_load_recently_viewed', 'somnia_load_recently_viewed_products');
add_action('wp_ajax_nopriv_load_recently_viewed', 'somnia_load_recently_viewed_products');

// AJAX handler for loading products display section widget search product style 1
function somnia_load_products_display()
{
    $source = isset($_POST['source']) ? sanitize_text_field($_POST['source']) : 'recent_viewed';
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 8;
    $custom_products = isset($_POST['products']) ? sanitize_text_field($_POST['products']) : '';
    $is_mobile = isset($_POST['is_mobile']) ? filter_var($_POST['is_mobile'], FILTER_VALIDATE_BOOLEAN) : false;

    $products = array();

    switch ($source) {
        case 'recent_viewed':
            // Load from localStorage (passed via AJAX)
            if (!empty($_POST['recently_viewed']) && is_array($_POST['recently_viewed'])) {
                $recently_viewed_ids = array_map('intval', $_POST['recently_viewed']);
                foreach ($recently_viewed_ids as $product_id) {
                    $product = wc_get_product($product_id);
                    if ($product && $product->is_visible()) {
                        $products[] = $product;
                    }
                }
            }
            break;

        case 'latest':
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => $limit,
                'orderby' => 'date',
                'order' => 'DESC',
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $product = wc_get_product(get_the_ID());
                    if ($product && $product->is_visible()) {
                        $products[] = $product;
                    }
                }
                wp_reset_postdata();
            }
            break;

        case 'featured':
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => $limit,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_visibility',
                        'field' => 'name',
                        'terms' => 'featured',
                    ),
                ),
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $product = wc_get_product(get_the_ID());
                    if ($product && $product->is_visible()) {
                        $products[] = $product;
                    }
                }
                wp_reset_postdata();
            }
            break;

        case 'custom':
            if (!empty($custom_products)) {
                $product_ids = array_map('intval', explode(',', $custom_products));
                foreach ($product_ids as $product_id) {
                    $product = wc_get_product($product_id);
                    if ($product && $product->is_visible()) {
                        $products[] = $product;
                    }
                }
            }
            break;
    }

    ob_start();
    if (!empty($products)) {
        if ($is_mobile) {
            // Render mobile layout (horizontal list)
            foreach ($products as $product) {
                $product_id = $product->get_id();
                $product_link = get_permalink($product_id);
                $product_image = $product->get_image('medium');
                $product_title = $product->get_name();
                $product_price = $product->get_price_html();
            ?>
                <div class="bt-product-item">
                    <div class="bt-product-thumb">
                        <a href="<?php echo esc_url($product_link); ?>" class="bt-thumb">
                            <?php echo wp_kses_post($product_image); ?>
                        </a>
                        <div class="bt-product-title">
                            <h3 class="bt-title">
                                <a href="<?php echo esc_url($product_link); ?>">
                                    <?php echo esc_html($product_title); ?>
                                </a>
                            </h3>
                            <?php if ($product_price) : ?>
                                <span><?php echo wp_kses_post($product_price); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="bt-product-add-to-cart">
                        <?php if ($product->is_type('simple')) { ?>
                            <a href="?add-to-cart=<?php echo esc_attr($product_id); ?>" aria-describedby="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr($product_id); ?>" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart bt-btn-add-to-cart" data-product_id="<?php echo esc_attr($product_id); ?>" data-product_sku="" rel="nofollow"><?php echo esc_html__('Add to cart', 'somnia') ?></a>
                        <?php } else { ?>
                            <a href="<?php echo esc_url($product_link); ?>" class="bt-button bt-button-hover bt-btn-view-product"><?php echo esc_html__('View Product', 'somnia') ?></a>
                        <?php } ?>
                    </div>
                </div>
        <?php
            }
        } else {
            // Render desktop layout (WooCommerce template)
            foreach ($products as $product) {
                $post_object = get_post($product->get_id());
                setup_postdata($GLOBALS['post'] = &$post_object);
                wc_get_template_part('content', 'product');
            }
            wp_reset_postdata();
        }
        $output['content'] = ob_get_clean();
        $output['has_products'] = true;
    } else {
        ob_get_clean();
        $output['content'] = '<p class="no-products">' . esc_html__('No products found.', 'somnia') . '</p>';
        $output['has_products'] = false;
    }

    wp_send_json_success($output);
}

add_action('wp_ajax_load_products_display', 'somnia_load_products_display');
add_action('wp_ajax_nopriv_load_products_display', 'somnia_load_products_display');

// Hook the function to run after checkout is completed
add_action('woocommerce_thankyou', 'somnia_after_checkout_product', 10, 1);
// Add a flag to prevent duplicate processing
add_action('woocommerce_checkout_order_processed', 'mark_order_as_processed', 10, 1);

function mark_order_as_processed($order_id)
{
    update_post_meta($order_id, '_somnia_order_processed', 'no');
}
function somnia_after_checkout_product($order_id)
{
    // Check if this order has already been processed
    if (get_post_meta($order_id, '_somnia_order_processed', true) === 'yes') {
        return;
    }
    $order = wc_get_order($order_id);
    $items = $order->get_items();
    $order_time = $order->get_date_created();
    if (!$order_time) {
        return;
    }
    $order_time->setTimezone(new DateTimeZone(wp_timezone_string()));

    foreach ($items as $item) {
        $item_product_id = $item->get_product_id();
        $product = wc_get_product($item_product_id);

        // Skip if product is invalid
        if (!$product || !is_a($product, 'WC_Product')) {
            continue;
        }

        $sale_date_start = get_post_meta($item_product_id, '_product_start_datetime', true);
        $sale_date = get_post_meta($item_product_id, '_product_datetime', true);
        $sale_date_start = new DateTime($sale_date_start, new DateTimeZone(wp_timezone_string()));
        $sale_date = new DateTime($sale_date, new DateTimeZone(wp_timezone_string()));
        if (empty($sale_date_start) || empty($sale_date)) {
            return;
        }
        if ($order_time < $sale_date_start || $order_time > $sale_date) {
            continue;
        }
        $enable_percentage_sold = get_post_meta($item_product_id, '_enable_percentage_sold', true);

        if ($enable_percentage_sold === 'yes') {
            $quantity = $item->get_quantity();
            $current_sold = absint(get_post_meta($item_product_id, '_product_sold_sale', true));
            $new_sold = $current_sold + $quantity;
            update_post_meta($item_product_id, '_product_sold_sale', $new_sold);
            $product_sold_sale = get_post_meta($item_product_id, '_product_sold_sale', true);
            $product_stock_sale = get_post_meta($item_product_id, '_product_stock_sale', true);
            $disable_sale = get_post_meta($item_product_id, '_disable_sale_price', true);
            if ($product_sold_sale >= $product_stock_sale) {
                if ($disable_sale === 'yes') {
                    // Handle variable products
                    if ($product->is_type('variable')) {
                        // Remove sale price for each variation
                        foreach ($product->get_available_variations() as $variation) {
                            $variation_id = $variation['variation_id'];
                            $variation_obj = wc_get_product($variation_id);
                            update_post_meta($variation_id, '_sale_price', '');
                            $variation_obj->set_sale_price('');
                            $variation_obj->save();
                        }
                    } else {
                        // Handle simple, grouped, external products
                        update_post_meta($product->get_id(), '_sale_price', '');
                        $product->set_sale_price('');
                    }
                }

                // Update common metadata for all product types
                update_post_meta($item_product_id, '_product_datetime', '');
                update_post_meta($item_product_id, '_disable_sale_price', 'no');
                update_post_meta($item_product_id, '_enable_percentage_sold', 'no');
                update_post_meta($item_product_id, '_product_start_datetime', '');
                update_post_meta($item_product_id, '_product_sold_sale', '');
                update_post_meta($item_product_id, '_product_stock_sale', '');
                $product->save();
            }
        }
    }
    // Mark this order as processed
    update_post_meta($order_id, '_somnia_order_processed', 'yes');
}
/* hook check sale date countdown product */
function somnia_check_sale_date_countdown()
{
    global $product;

    // Get product if global is not set
    if (!$product) {
        $product = wc_get_product();
    }

    // Check if product is valid
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    // Get sale end date from product meta
    $sale_date = get_post_meta($product->get_id(), '_product_datetime', true);
    $disable_sale = get_post_meta($product->get_id(), '_disable_sale_price', true);

    if (!empty($sale_date)) {
        // Get current time in site's timezone (same as countdown function)
        $timezone = new DateTimeZone(wp_timezone_string());
        $current_time = new DateTime('now', $timezone);
        $current_timestamp = $current_time->getTimestamp();

        $sale_timestamp = strtotime($sale_date);
        // If sale date has passed and disable sale is checked
        if ($current_timestamp > $sale_timestamp) {
            if ($disable_sale === 'yes') {
                // Handle variable products
                if ($product->is_type('variable')) {
                    // Remove sale price for each variation
                    foreach ($product->get_available_variations() as $variation) {
                        $variation_id = $variation['variation_id'];
                        $variation_obj = wc_get_product($variation_id);
                        update_post_meta($variation_id, '_sale_price', '');
                        $variation_obj->set_sale_price('');
                        $variation_obj->save();
                    }
                } else {
                    // Handle simple, grouped, external products
                    update_post_meta($product->get_id(), '_sale_price', '');
                    $product->set_sale_price('');
                }
            }

            // Update common metadata for all product types
            update_post_meta($product->get_id(), '_product_datetime', '');
            update_post_meta($product->get_id(), '_disable_sale_price', 'no');
            update_post_meta($product->get_id(), '_enable_percentage_sold', 'no');
            update_post_meta($product->get_id(), '_product_start_datetime', '');
            update_post_meta($product->get_id(), '_product_sold_sale', '');
            update_post_meta($product->get_id(), '_product_stock_sale', '');
            $product->save();
        }
    }
}

add_action('woocommerce_before_single_product', 'somnia_check_sale_date_countdown', 25);

// Add countdown to single product
add_action('somnia_woocommerce_template_single_countdown', 'somnia_woocommerce_single_product_countdown', 40);

function somnia_woocommerce_single_product_countdown()
{
    global $product;
    $time = get_post_meta($product->get_id(), '_product_datetime', true);
    $stock_status = $product->get_stock_status();
    // Get current time in site's timezone
    $timezone = new DateTimeZone(wp_timezone_string());
    $current_time = new DateTime('now', $timezone);
    $current_time = $current_time->format('Y-m-d H:i:s');
    if ($time && $stock_status != 'outofstock') {
        $time = strtotime($time);
        $time = date('Y-m-d H:i:s', $time);
        if ($current_time > $time) {
            return;
        }
        ?>
        <div class="bt-countdown-product-sale">
            <span class="bt-heading"><?php echo esc_html__('Hurry Up! Offer ends in:', 'somnia'); ?></span>
            <div class="bt-countdown bt-countdown-product-js"
                data-idproduct="<?php echo esc_attr($product->get_id()); ?>"
                data-time="<?php echo esc_attr($time); ?>" data-current-time="<?php echo esc_attr($current_time); ?>">

                <div class="bt-countdown--item">
                    <span class="bt-countdown--digits bt-countdown-days">--</span>
                    <span class="bt-countdown--label"><?php _e('Days', 'somnia'); ?></span>
                </div>

                <div class="bt-delimiter">:</div>

                <div class="bt-countdown--item">
                    <span class="bt-countdown--digits bt-countdown-hours">--</span>
                    <span class="bt-countdown--label"><?php _e('Hours', 'somnia'); ?></span>
                </div>

                <div class="bt-delimiter">:</div>

                <div class="bt-countdown--item">
                    <span class="bt-countdown--digits bt-countdown-mins">--</span>
                    <span class="bt-countdown--label"><?php _e('Mins', 'somnia'); ?></span>
                </div>

                <div class="bt-delimiter">:</div>

                <div class="bt-countdown--item">
                    <span class="bt-countdown--digits bt-countdown-secs">--</span>
                    <span class="bt-countdown--label"><?php _e('Secs', 'somnia'); ?></span>
                </div>
            </div>
        </div>
        <?php

        $enable_percentage_sold = get_post_meta($product->get_id(), '_enable_percentage_sold', true);
        if ($enable_percentage_sold === 'yes') {
            $product_sold_sale = get_post_meta($product->get_id(), '_product_sold_sale', true);
            $product_stock_sale = get_post_meta($product->get_id(), '_product_stock_sale', true);
            $percentage = min(100, max(0, ($product_sold_sale / $product_stock_sale) * 100));
            $remaining_products = $product_stock_sale - $product_sold_sale;
        ?>
            <div class="bt-product-percentage-sold">
                <span class="bt-heading"><?php echo esc_html__('Sold It:', 'somnia'); ?></span>
                <div class="bt-product-stock">
                    <div class="bt-progress">
                        <div class="bt-progress-bar-sold" data-width="<?php echo esc_attr($percentage); ?>"></div>
                    </div>
                    <span class="bt-quantity_sold">
                        <?php printf(esc_html__('%d%% Sold', 'somnia'), $percentage); ?> -
                    </span>
                    <span class="bt-stock-text">
                        <?php printf(esc_html__('Only %d item(s) left in stock!', 'somnia'), $remaining_products); ?>
                    </span>
                </div>
            </div>

    <?php
        }
    }
}

// Add more information single product
add_action('somnia_woocommerce_template_single_more_information', 'somnia_woocommerce_single_product_more_information', 40);
function somnia_woocommerce_single_product_more_information()
{
    // Check if ACF function exists
    if (!function_exists('get_field')) {
        return;
    }

    $more_information = get_field('more_information', 'options');

    // Validate more information settings
    if (empty($more_information) || empty($more_information['enable_more_information'])) {
        return;
    }

    $estimated_delivery = !empty($more_information['estimated_delivery']) ? $more_information['estimated_delivery'] : false;
    $product_return = !empty($more_information['product_return']) ? $more_information['product_return'] : false;
    $store_location = !empty($more_information['store_location']) ? $more_information['store_location'] : false;
    $maps_store_location = !empty($more_information['maps_store_location']) ? $more_information['maps_store_location'] : false;
    $delivery_return = !empty($more_information['delivery_return']) ? $more_information['delivery_return'] : false;
    $ask_a_question = !empty($more_information['ask_a_question']) ? $more_information['ask_a_question'] : false;
    $product_share = !empty($more_information['product_share']) ? $more_information['product_share'] : false;

    ?>
    <div class="bt-more-information">
        <?php if ($estimated_delivery) { ?>
            <div class="bt-estimated-delivery">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <?php echo '<span>' . $estimated_delivery . '</span>'; ?>
            </div>
        <?php } ?>

        <?php if ($product_return) { ?>
            <div class="bt-product-return">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3M3.22302 14C4.13247 18.008 7.71683 21 12 21c4.9706 0 9-4.0294 9-9 0-4.97056-4.0294-9-9-9-3.72916 0-6.92858 2.26806-8.29409 5.5M7 9H3V5" />
                </svg>
                <?php echo '<span>' . $product_return . '</span>'; ?>
            </div>
        <?php } ?>

        <?php if ($store_location) {
            $has_map = $maps_store_location ? 'bt-has-map' : '';
        ?>
            <div class="bt-store-location">
                <div id="bt_store_location" class="bt-store-location__popup mfp-content__popup mfp-hide">
                    <div class="bt-store-location__content mfp-content__inner <?php echo esc_attr($has_map); ?>">
                        <div class="bt-store-location__text">
                            <?php echo wp_kses_post($store_location); ?>
                        </div>
                        <?php if ($maps_store_location) { ?>
                            <div class="bt-store-location__map">
                                <?php
                                echo wp_kses(
                                    $maps_store_location,
                                    array(
                                        'iframe' => array(
                                            'src'               => true,
                                            'width'             => true,
                                            'height'            => true,
                                            'style'             => true,
                                            'allowfullscreen'   => true,
                                            'loading'           => true,
                                            'referrerpolicy'    => true,
                                            'frameborder'       => true,
                                        ),
                                    )
                                );
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <a href="#bt_store_location" class="bt-store-location__link bt-js-open-popup-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.8 13.938h-.011a7 7 0 1 0-11.464.144h-.016l.14.171c.1.127.2.251.3.371L12 21l5.13-6.248c.194-.209.374-.429.54-.659l.13-.155Z" />
                    </svg>
                    <span><?php echo esc_html__('View Store Information', 'somnia'); ?></span>
                </a>
            </div>
        <?php } ?>

        <?php if ($delivery_return || $ask_a_question || $product_share) { ?>
            <div class="bt-policy-share">
                <?php if ($delivery_return) { ?>
                    <div class="bt-delivery-return">
                        <div id="bt_delivery_return" class="bt-delivery-return__popup mfp-content__popup mfp-hide">
                            <?php echo '<div class="bt-delivery-return__content mfp-content__inner">' . $delivery_return . '</div>'; ?>
                        </div>
                        <a href="#bt_delivery_return" class="bt-delivery-return__link bt-js-open-popup-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                            </svg>
                            <?php echo esc_html__('Delivery & Return', 'somnia'); ?>
                        </a>
                    </div>
                <?php } ?>

                <?php if ($ask_a_question) { ?>
                    <div class="bt-ask-a-question">
                        <div id="bt_ask_a_question" class="bt-ask-a-question__popup mfp-content__popup mfp-hide">
                            <?php echo '<div class="bt-ask-a-question__content mfp-content__inner">' . do_shortcode($ask_a_question) . '</div>'; ?>
                        </div>
                        <a href="#bt_ask_a_question" class="bt-ask-a-question__link bt-js-open-popup-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.529 9.988a2.502 2.502 0 1 1 5 .191A2.441 2.441 0 0 1 12 12.582V14m-.01 3.008H12M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <?php echo esc_html__('Ask A Question', 'somnia'); ?>
                        </a>
                    </div>
                <?php } ?>

                <?php if ($product_share) { ?>
                    <?php echo somnia_product_share_render(); ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
<?php
}
// add safe checkout single product
add_action('somnia_woocommerce_template_single_safe_checkout', 'somnia_woocommerce_single_product_safe_checkout', 40);
function somnia_woocommerce_single_product_safe_checkout()
{
    // Check if ACF function exists
    if (!function_exists('get_field')) {
        return;
    }

    $safe_checkout = get_field('safe_checkout', 'options');

    // Validate safe checkout settings
    if (empty($safe_checkout) || empty($safe_checkout['enable_safe_checkout'])) {
        return;
    }

    $heading = !empty($safe_checkout['heading']) ? $safe_checkout['heading'] : '';
    $gallery_safe = !empty($safe_checkout['list_safe']) ? $safe_checkout['list_safe'] : array();

?>
    <div class="bt-safe-checkout">
        <?php if (!empty($heading)) : ?>
            <span><?php echo esc_html($heading); ?></span>
        <?php endif; ?>

        <?php if (!empty($gallery_safe)) : ?>
            <ul class="bt-safe-checkout-list">
                <?php foreach ($gallery_safe as $item) : ?>
                    <?php
                    if (!empty($item['ID'])) {
                        $image_url = wp_get_attachment_image_url($item['ID'], 'full');
                        $image_alt = !empty($item['alt']) ? $item['alt'] : '';
                    ?>
                        <li>
                            <img src="<?php echo esc_url($image_url); ?>"
                                alt="<?php echo esc_attr($image_alt); ?>">
                        </li>
                    <?php
                    }
                    ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php
}

// add out of stock single product
add_action('somnia_woocommerce_template_single_out_of_stock', 'somnia_woocommerce_single_product_out_of_stock', 10);
function somnia_woocommerce_single_product_out_of_stock()
{
    global $product;

    // Get product if global is not set
    if (!$product) {
        $product = wc_get_product();
    }

    // Check if product is valid
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    // Check if ACF function exists
    if (!function_exists('get_field')) {
        return;
    }

    $out_of_stock = get_field('out_of_stock', 'options');

    // Validate out of stock settings
    if (empty($out_of_stock) || empty($out_of_stock['enable_out_of_stock'])) {
        if (!$product->is_type('variable')) {
            echo '<p class="stock out-of-stock">' . esc_html__('Out of stock', 'somnia') . '</p>';
        }
        return;
    }

    $notification_form = !empty($out_of_stock['notification_form']) ? $out_of_stock['notification_form'] : '';

    if (empty($notification_form)) {
        return;
    }
?>
    <div class="bt-notification-form">
        <?php echo do_shortcode($notification_form); ?>
    </div>
<?php
}

/**
 * Handle thumbnail layouts display mode
 * - Tab mode: Let WooCommerce render default tabs, just add classes
 * - Toggle mode: Remove WooCommerce tabs, render toggle
 * - Disable mode: Remove WooCommerce tabs
 */
function somnia_handle_thumbnail_layout_mode()
{
    global $product;

    if (!$product) {
        return;
    }

    $layout = get_post_meta($product->get_id(), '_layout_product', true);
    if (isset($_GET['layout']) && !empty($_GET['layout'])) {
        $layout = sanitize_text_field($_GET['layout']);
    }
    if (empty($layout)) {
        $layout = 'bottom-thumbnail';
    }

    $thumbnail_layouts = array('bottom-thumbnail', 'left-thumbnail', 'right-thumbnail');

    // Get display mode
    $display_mode = get_post_meta($product->get_id(), '_product_info_display_mode', true);

    // If layout is not thumbnail layout, force toggle mode only if display mode is not set or if layout is changed via $_GET
    if (!in_array($layout, $thumbnail_layouts)) {
        if (empty($display_mode) || (isset($_GET['layout']) && !empty($_GET['layout']))) {
            $display_mode = 'toggle';
        }
    } else {
        // For thumbnail layouts, use default tab mode if empty
        if (empty($display_mode)) {
            $display_mode = 'tab';
        }
    }

    if ($display_mode === 'tab') {
        // Do nothing here, classes added via somnia_add_tab_position_body_class filter
    } elseif ($display_mode === 'toggle') {
        // Toggle mode: Remove default tabs, add toggle
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);

        // If layout is not thumbnail layout, add to somnia_woocommerce_template_single_toggle
        if (!in_array($layout, $thumbnail_layouts)) {
            add_action('somnia_woocommerce_template_single_toggle', 'somnia_render_product_info_toggle', 10);
        } else {
            add_action('woocommerce_after_single_product_summary', 'somnia_render_product_info_toggle', 10);
        }
    } elseif ($display_mode === 'disable') {
        // Disable mode: Remove default tabs 
        remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
    }
}
add_action('woocommerce_before_single_product', 'somnia_handle_thumbnail_layout_mode', 1);
/**
 * Add tab position to body class for CSS targeting
 * CSS will use body.bt-tabs-position-{position} .woocommerce-tabs selector
 */
function somnia_add_tab_position_body_class($classes)
{
    if (is_product()) {
        global $post;

        if ($post && isset($post->ID)) {
            $display_mode = get_post_meta($post->ID, '_product_info_display_mode', true);

            // Get layout
            $layout = get_post_meta($post->ID, '_layout_product', true);
            if (isset($_GET['layout']) && !empty($_GET['layout'])) {
                $layout = sanitize_text_field($_GET['layout']);
            }
            if (empty($layout)) {
                $layout = 'bottom-thumbnail';
            }

            $thumbnail_layouts = array('bottom-thumbnail', 'left-thumbnail', 'right-thumbnail');

            // Only add tab position class if display mode is 'tab'
            if ($display_mode === 'tab') {
                $tab_position = get_post_meta($post->ID, '_product_tab_position', true);
                if (empty($tab_position)) {
                    $tab_position = 'top';
                }

                $classes[] = 'bt-tabs-position-' . $tab_position;
            }

            // Add class if layout is thumbnail layout and display mode is toggle
            if (in_array($layout, $thumbnail_layouts) && $display_mode === 'toggle') {
                $classes[] = 'bt-thumbnail-toggle-mode';
            }
        }
    }

    return $classes;
}
add_filter('body_class', 'somnia_add_tab_position_body_class');

/**
 * Render product info as TOGGLE/ACCORDION
 * For all layouts that support toggle mode
 */
function somnia_render_product_info_toggle()
{
    global $product;

    if (!$product) {
        return;
    }

    // Get toggle state setting
    $toggle_state = get_post_meta($product->get_id(), '_product_toggle_state', true);
    if (empty($toggle_state)) {
        $toggle_state = 'first'; // Default: open first toggle
    }

    $product_tabs = apply_filters('woocommerce_product_tabs', array());

    if (empty($product_tabs)) {
        return;
    }
?>
    <div class="woocommerce-tabs bt-product-toggle bt-product-toggle-js" data-toggle-state="<?php echo esc_attr($toggle_state); ?>">
        <?php
        $i = 0;
        foreach ($product_tabs as $key => $product_tab) :
            $i++;
            $is_first = ($i === 1);
            $is_active = ($toggle_state === 'all') || ($toggle_state === 'first' && $is_first);
            $class_active = $is_active ? 'active' : '';
        ?>
            <div class="bt-item">
                <div class="bt-item-inner">
                    <div class="bt-item-title <?php echo esc_attr($class_active); ?>" data-tab="<?php echo esc_attr($key); ?>">
                        <h3><?php echo wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key)); ?></h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="plus" width="18" height="18" viewBox="0 0 160 160">
                            <rect class="vertical-line" x="70" width="15" height="160" rx="7" ry="7" />
                            <rect class="horizontal-line" y="70" width="160" height="15" rx="7" ry="7" />
                        </svg>
                    </div>
                    <?php
                    echo '<div class="bt-item-content"' . ($is_active ? ' style="display:block;"' : '') . ' id="tab-' . esc_attr($key) . '">';
                    if (isset($product_tab['callback'])) {
                        call_user_func($product_tab['callback'], $key, $product_tab);
                    }
                    echo '</div>';
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php
}

/**
 * Remove reviews tab from product tabs
 * This allows reviews to be rendered separately
 */
function somnia_remove_reviews_from_tabs($tabs)
{
    if (is_product() && isset($tabs['reviews'])) {
        unset($tabs['reviews']);
    }

    return $tabs;
}
add_filter('woocommerce_product_tabs', 'somnia_remove_reviews_from_tabs', 98);

/**
 * Render product reviews separately
 * This function can be called anywhere you want to display reviews
 */
add_action('woocommerce_after_single_product_summary', 'somnia_render_product_reviews', 19);
add_action('somnia_woocommerce_single_product_after_summary', 'somnia_render_product_reviews', 19);
function somnia_render_product_reviews()
{
    global $product;

    if (!$product) {
        return;
    }

    // Check if comments are open
    if (!comments_open()) {
        return;
    }

    // Get product tabs to access reviews tab data (before filter removes it)
    // We need to get original tabs, so we temporarily remove our filter
    remove_filter('woocommerce_product_tabs', 'somnia_remove_reviews_from_tabs', 98);
    $product_tabs = apply_filters('woocommerce_product_tabs', array());
    add_filter('woocommerce_product_tabs', 'somnia_remove_reviews_from_tabs', 98);

    // Check if reviews tab exists
    if (!isset($product_tabs['reviews'])) {
        return;
    }

    $reviews_tab = $product_tabs['reviews'];

    // Default: "Customer Reviews" + "Share your thoughts with other customers!"
    $reviews_title   = __('Customer Reviews', 'somnia');
    $reviews_subtitle = __('Share your thoughts with other customers!', 'somnia');

    // Override from Theme Options > Single Products > Customer Reviews
    if (function_exists('get_field')) {
        $customer_reviews = get_field('customer_reviews', 'option');
        if (!empty($customer_reviews['enable_custom_reviews'])) {
            if (!empty($customer_reviews['heading'])) {
                $reviews_title = $customer_reviews['heading'];
            }
            if (isset($customer_reviews['description']) && $customer_reviews['description'] !== '') {
                $reviews_subtitle = $customer_reviews['description'];
            }
        }
    }
    $reviews_title = apply_filters('woocommerce_product_reviews_tab_title', $reviews_title);

    $reviews_callback = isset($reviews_tab['callback']) ? $reviews_tab['callback'] : 'comments_template';
?>
    <section class="somnia-product-reviews">
        <?php if (!empty($reviews_title)) : ?>
            <h3 class="somnia-reviews-title"><?php echo wp_kses_post($reviews_title); ?></h3>
        <?php endif; ?>
        <?php if (!empty($reviews_subtitle)) : ?>
            <p class="somnia-reviews-subtitle"><?php echo wp_kses_post($reviews_subtitle); ?></p>
        <?php endif; ?>
        <div class="somnia-reviews-content">
            <?php
            if (is_callable($reviews_callback)) {
                call_user_func($reviews_callback, 'reviews', $reviews_tab);
            }
            ?>
        </div>
    </section>
<?php
}

/**
 * Render benefits tab content
 * 
 * @param string $key Tab key
 * @param array $tab Tab data
 */
function somnia_render_benefits_tab($key, $tab)
{
    global $product;

    if (!$product || !function_exists('get_field')) {
        return;
    }

    // Get list_benefits field from product
    $list_benefits = get_field('list_benefits', $product->get_id());

    if (empty($list_benefits) || !is_array($list_benefits)) {
        return;
    }
?>
    <div class="benefits-list">
        <?php foreach ($list_benefits as $benefit) :
            $icon = isset($benefit['icon']) ? $benefit['icon'] : '';
            $heading = isset($benefit['heading']) ? $benefit['heading'] : '';
            $description = isset($benefit['description']) ? $benefit['description'] : '';

            if (empty($heading) && empty($description)) {
                continue;
            }
        ?>
            <div class="benefit-item">
                <?php if (!empty($icon)) :
                    $icon_url = is_array($icon) ? $icon['url'] : $icon;
                    $icon_alt = is_array($icon) ? $icon['alt'] : '';
                ?>
                    <div class="benefit-icon">
                        <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($icon_alt); ?>" />
                    </div>
                <?php endif; ?>

                <div class="benefit-content">
                    <?php if (!empty($heading)) : ?>
                        <h4 class="benefit-heading"><?php echo esc_html($heading); ?></h4>
                    <?php endif; ?>

                    <?php if (!empty($description)) : ?>
                        <div class="benefit-description"><?php echo wp_kses_post(nl2br($description)); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php
}

/**
 * Add Benefits tab to WooCommerce product tabs
 * 
 * @param array $tabs Existing tabs
 * @return array Modified tabs
 */
function somnia_add_benefits_tab($tabs)
{
    global $product;

    if (!is_product() || !$product || !function_exists('get_field')) {
        return $tabs;
    }

    // Check if product has benefits
    $list_benefits = get_field('list_benefits', $product->get_id());

    if (empty($list_benefits) || !is_array($list_benefits)) {
        return $tabs;
    }

    // Add Benefits tab - priority 5 to appear first
    $tabs['benefits'] = array(
        'title'    => __('Benefits', 'somnia'),
        'priority' => 5,
        'callback' => 'somnia_render_benefits_tab',
    );

    return $tabs;
}
add_filter('woocommerce_product_tabs', 'somnia_add_benefits_tab', 5);

/**
 * Add or modify placeholders for existing WooCommerce checkout fields
 */

add_filter('woocommerce_checkout_fields', 'somnia_checkout_field_placeholders');

function somnia_checkout_field_placeholders($fields)
{
    // Billing fields placeholders
    $fields['billing']['billing_first_name']['placeholder'] = 'First Name *';
    $fields['billing']['billing_last_name']['placeholder'] = 'Last Name *';
    $fields['billing']['billing_company']['placeholder'] = 'Company name';
    $fields['billing']['billing_address_1']['placeholder'] = 'Street,...';
    $fields['billing']['billing_city']['placeholder'] = 'Town/City *';
    $fields['billing']['billing_postcode']['placeholder'] = 'Postal Code *';
    $fields['billing']['billing_state']['placeholder'] = 'Select state/province';
    $fields['billing']['billing_phone']['placeholder'] = 'Phone Number *';
    $fields['billing']['billing_email']['placeholder'] = 'Email Address *';

    // Shipping fields placeholders
    $fields['shipping']['shipping_first_name']['placeholder'] = 'First Name *';
    $fields['shipping']['shipping_last_name']['placeholder'] = 'Last Name *';
    $fields['shipping']['shipping_company']['placeholder'] = 'Company name';
    $fields['shipping']['shipping_address_1']['placeholder'] = 'Street,...';
    $fields['shipping']['shipping_city']['placeholder'] = 'Town/City *';
    $fields['shipping']['shipping_postcode']['placeholder'] = 'Postal Code *';

    // Order fields placeholders
    $fields['order']['order_comments']['placeholder'] = 'Write note...';

    return $fields;
}
/* hook redirect after logout */
add_action('wp_logout', 'somnia_redirect_after_logout');
function somnia_redirect_after_logout()
{
    if (class_exists('WooCommerce')) {
        wp_redirect(wc_get_page_permalink('myaccount'));
        exit();
    }
}

// Add gallery field to variable product attributes
add_action('woocommerce_product_after_variable_attributes', 'add_variation_gallery_field', 10, 3);

function add_variation_gallery_field($loop, $variation_data, $variation)
{
    // Get saved gallery images
    $variation_gallery = get_post_meta($variation->ID, '_variation_gallery', true);
    $gallery_images = $variation_gallery ? explode(',', $variation_gallery) : array();
?>
    <div class="form-row form-row-full variation-gallery-wrapper">
        <h4><?php esc_html_e('Variation Gallery Images', 'somnia'); ?></h4>
        <div class="variation-gallery-images">
            <?php
            if (!empty($gallery_images)) {
                foreach ($gallery_images as $image_id) {
                    $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                    if ($image) {
            ?>
                        <div class="image" data-id="<?php echo esc_attr($image_id); ?>">
                            <img src="<?php echo esc_url($image[0]); ?>" />
                            <a href="#" class="delete-variation-gallery-image">×</a>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>
        <input type="hidden" name="variation_gallery[<?php echo esc_attr($loop); ?>]" class="variation-gallery-ids" value="<?php echo esc_attr($variation_gallery); ?>" />
        <button type="button" class="button add-variation-gallery-image"><?php esc_html_e('Add Gallery Images', 'somnia'); ?></button>
    </div>
    <?php
}

// Save variation gallery data
add_action('woocommerce_save_product_variation', 'somnia_save_variation_gallery', 10, 2);

function somnia_save_variation_gallery($variation_id, $loop)
{
    if (isset($_POST['variation_gallery'][$loop])) {
        update_post_meta($variation_id, '_variation_gallery', wc_clean($_POST['variation_gallery'][$loop]));
    }
}

/**
 * Get HTML for a gallery image.
 *
 * Hooks: woocommerce_gallery_thumbnail_size, woocommerce_gallery_image_size and woocommerce_gallery_full_size accept name based image sizes, or an array of width/height values.
 *
 * @since 3.3.2
 * @param int  $attachment_id Attachment ID.
 * @param bool $main_image Is this the main image or a thumbnail?.
 * @param int  $image_index The image index in the gallery.
 * @return string
 */

/**
 * Get video embed HTML based on video type and link
 * @param string $video_type Type of video (youtube, vimeo, mp4)
 * @param string $video_link Video URL
 * @return string Video embed HTML
 */
function somnia_get_product_video_embed($video_type, $video_link)
{
    if (empty($video_link)) {
        return '';
    }

    $video_html = '';

    switch ($video_type) {
        case 'youtube':
            // Extract YouTube video ID
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $video_link, $matches);
            $video_id = !empty($matches[1]) ? $matches[1] : '';

            if ($video_id) {
                $video_src  = esc_url("https://www.youtube.com/embed/$video_id?rel=0&enablejsapi=1");
                $video_html = '<div class="bt-video-embed bt-video-youtube" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">';
                $video_html .= '<iframe src="' . $video_src . '" ';
                $video_html .= 'style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" ';
                $video_html .= 'frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>';
                $video_html .= '</iframe>';
                $video_html .= '</div>';
            }
            break;

        case 'vimeo':
            // Extract Vimeo video ID
            preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $video_link, $matches);
            $video_id = !empty($matches[1]) ? $matches[1] : '';

            if ($video_id) {
                $video_src  = esc_url("https://player.vimeo.com/video/$video_id?api=1");
                $video_html = '<div class="bt-video-embed bt-video-vimeo" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">';
                $video_html .= '<iframe src="' . $video_src . '" ';
                $video_html .= 'style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" ';
                $video_html .= 'frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen>';
                $video_html .= '</iframe>';
                $video_html .= '</div>';
            }
            break;

        case 'mp4':
            $video_html = '<div class="bt-video-embed bt-video-mp4">';
            $video_html .= '<video controls style="width: 100%; height: auto;">';
            $video_html .= '<source src="' . esc_url($video_link) . '" type="video/mp4">';
            $video_html .= __('Your browser does not support the video tag.', 'somnia');
            $video_html .= '</video>';
            $video_html .= '</div>';
            break;
    }

    return $video_html;
}

function somnia_get_gallery_image_html($attachment_id, $main_image = false, $swiper_slide = false, $image_index = -1)
{
    global $product;

    $flexslider        = (bool) apply_filters('woocommerce_single_product_flexslider_enabled', get_theme_support('wc-product-gallery-slider'));
    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
        $gallery_thumbnail = wc_get_image_size('woocommerce_thumbnail');
    } else {
        // Check if we're in related products section first (priority check)
        global $somnia_is_related_products;
        if (isset($somnia_is_related_products) && $somnia_is_related_products) {
            // Use woocommerce_thumbnail for related products (same as shop page)
            $gallery_thumbnail = wc_get_image_size('woocommerce_thumbnail');
        } elseif (is_product() && !is_shop() && !is_product_category() && !is_product_tag()) {
            // Single product page gallery (not in related products)
            $gallery_thumbnail = wc_get_image_size('gallery_thumbnail');
        } else {
            // Shop/archive pages - use woocommerce_thumbnail
            $gallery_thumbnail = wc_get_image_size('woocommerce_thumbnail');
        }
    }
    $thumbnail_size    = apply_filters('woocommerce_gallery_thumbnail_size', array((int)$gallery_thumbnail['width'], (int)$gallery_thumbnail['height']));
    $image_size        = apply_filters('woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size);
    $full_size         = apply_filters('woocommerce_gallery_full_size', apply_filters('woocommerce_product_thumbnails_large_size', 'full'));
    $thumbnail_src     = wp_get_attachment_image_src($attachment_id, $thumbnail_size);
    $thumbnail_srcset  = wp_get_attachment_image_srcset($attachment_id, $thumbnail_size);
    $thumbnail_sizes   = wp_get_attachment_image_sizes($attachment_id, $thumbnail_size);
    $full_src          = wp_get_attachment_image_src($attachment_id, $full_size);
    $alt_text          = trim(wp_strip_all_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)));
    $alt_text          = (empty($alt_text) && ($product instanceof WC_Product)) ? woocommerce_get_alt_from_product_title_and_position($product->get_title(), $main_image, $image_index) : $alt_text;

    /**
     * Filters the attributes for the image markup.
     *
     * @since 3.3.2
     *
     * @param array $image_attributes Attributes for the image markup.
     */
    $image_params = apply_filters(
        'woocommerce_gallery_image_html_attachment_image_params',
        array(
            'title'                   => _wp_specialchars(get_post_field('post_title', $attachment_id), ENT_QUOTES, 'UTF-8', true),
            'data-caption'            => _wp_specialchars(get_post_field('post_excerpt', $attachment_id), ENT_QUOTES, 'UTF-8', true),
            'data-src'                => isset($full_src[0]) ? esc_url($full_src[0]) : '',
            'data-large_image'        => isset($full_src[0]) ? esc_url($full_src[0]) : '',
            'data-large_image_width'  => isset($full_src[1]) ? esc_attr($full_src[1]) : '',
            'data-large_image_height' => isset($full_src[2]) ? esc_attr($full_src[2]) : '',
            'class'                   => esc_attr($main_image ? 'wp-post-image' : ''),
            'alt'                     => esc_attr($alt_text),
        ),
        $attachment_id,
        $image_size,
        $main_image
    );

    if (isset($image_params['title'])) {
        unset($image_params['title']);
    }

    $image = wp_get_attachment_image(
        $attachment_id,
        $image_size,
        'false',
        $image_params
    );

    if ($swiper_slide) {
        return '<div class="swiper-slide">
                <div data-thumb="' . esc_url(isset($thumbnail_src[0]) ? $thumbnail_src[0] : '') . '" data-thumb-alt="' . esc_attr($alt_text) . '" data-thumb-srcset="' . esc_attr(isset($thumbnail_srcset) ? $thumbnail_srcset : '') . '"  data-thumb-sizes="' . esc_attr(isset($thumbnail_sizes) ? $thumbnail_sizes : '') . '" class="woocommerce-product-gallery__image zoomable">' . $image . '</div>
            </div>';
    } else {
        return '<div data-thumb="' . esc_url(isset($thumbnail_src[0]) ? $thumbnail_src[0] : '') . '" data-thumb-alt="' . esc_attr($alt_text) . '" data-thumb-srcset="' . esc_attr(isset($thumbnail_srcset) ? $thumbnail_srcset : '') . '"  data-thumb-sizes="' . esc_attr(isset($thumbnail_sizes) ? $thumbnail_sizes : '') . '" class="woocommerce-product-gallery__image zoomable">' . $image . '</div>';
    }
}

// ajax load product gallery variation
function somnia_load_product_gallery()
{
    if (!isset($_POST['variation_id'])) {
        return;
    }
    // Get product gallery images
    $gallery_layout = $_POST['gallery_layout'];
    $variation_id = intval($_POST['variation_id']);
    $variation = wc_get_product($variation_id);

    // Get parent product to compare images
    $product = wc_get_product($variation->get_parent_id());
    $product_image_id = $product ? $product->get_image_id() : 0;

    // Get variation image
    $variation_image_id = $variation->get_image_id();

    // Check if variation has custom image (different from parent product image)
    // If not, use default product gallery
    if ($variation_image_id && $variation_image_id > 0 && (int)$variation_image_id !== (int)$product_image_id) {
        // Variation has custom image, use variation images
        $variation_gallery = get_post_meta($variation_id, '_variation_gallery', true);
        $gallery_images = $variation_gallery ? explode(',', $variation_gallery) : array();
        $main_image_id = $variation_image_id;
    } else {
        // Variation doesn't have custom image, use default product gallery
        $main_image_id = $product_image_id;
        $gallery_images = $product ? $product->get_gallery_image_ids() : array();
    }

    if ($gallery_layout == 'quickview-slider') {
        ob_start();
        echo '<div class="bt-gallery-slider-product">';
        echo '<div class="swiper-wrapper">';
        if ($main_image_id) {
            $html = somnia_get_gallery_image_html($main_image_id, true, true);

            if (!empty($gallery_images)) {
                foreach ($gallery_images as $key => $attachment_id) {
                    $html .= somnia_get_gallery_image_html($attachment_id, true, true);
                }
            }
            echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $main_image_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
        }
        echo '</div>';
        echo '</div>';
        echo '<div class="bt-swiper-pagination swiper-pagination"></div>';

        $output['gallery-slider'] = ob_get_clean();
    } else if ($gallery_layout == 'gallery-slider') {
        ob_start();
        echo '<div class="bt-gallery-slider-product bt-gallery-lightbox bt-gallery-zoomable">';
        echo '<div class="swiper-wrapper">';
        if ($main_image_id) {
            $html = somnia_get_gallery_image_html($main_image_id, true, true);

            if (!empty($gallery_images)) {
                foreach ($gallery_images as $key => $attachment_id) {
                    $html .= somnia_get_gallery_image_html($attachment_id, true, true);
                }
            }
            echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $main_image_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
        }
        echo '</div>';
        echo '<div class="swiper-button-prev"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.4995 10.0003C17.4995 10.1661 17.4337 10.3251 17.3165 10.4423C17.1992 10.5595 17.0403 10.6253 16.8745 10.6253H4.63311L9.1917 15.1832C9.24977 15.2412 9.29583 15.3102 9.32726 15.386C9.35869 15.4619 9.37486 15.5432 9.37486 15.6253C9.37486 15.7075 9.35869 15.7888 9.32726 15.8647C9.29583 15.9405 9.24977 16.0095 9.1917 16.0675C9.13363 16.1256 9.0647 16.1717 8.98882 16.2031C8.91295 16.2345 8.83164 16.2507 8.74951 16.2507C8.66739 16.2507 8.58607 16.2345 8.5102 16.2031C8.43433 16.1717 8.3654 16.1256 8.30733 16.0675L2.68233 10.4425C2.62422 10.3845 2.57812 10.3156 2.54667 10.2397C2.51521 10.1638 2.49902 10.0825 2.49902 10.0003C2.49902 9.91821 2.51521 9.83688 2.54667 9.76101C2.57812 9.68514 2.62422 9.61621 2.68233 9.55816L8.30733 3.93316C8.4246 3.81588 8.58366 3.75 8.74951 3.75C8.91537 3.75 9.07443 3.81588 9.1917 3.93316C9.30898 4.05044 9.37486 4.2095 9.37486 4.37535C9.37486 4.5412 9.30898 4.70026 9.1917 4.81753L4.63311 9.37535H16.8745C17.0403 9.37535 17.1992 9.4412 17.3165 9.55841C17.4337 9.67562 17.4995 9.83459 17.4995 10.0003Z"/>
                </svg></div>
                <div class="swiper-button-next"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.3172 10.4425L11.6922 16.0675C11.5749 16.1848 11.4159 16.2507 11.25 16.2507C11.0841 16.2507 10.9251 16.1848 10.8078 16.0675C10.6905 15.9503 10.6247 15.7912 10.6247 15.6253C10.6247 15.4595 10.6905 15.3004 10.8078 15.1832L15.3664 10.6253H3.125C2.95924 10.6253 2.80027 10.5595 2.68306 10.4423C2.56585 10.3251 2.5 10.1661 2.5 10.0003C2.5 9.83459 2.56585 9.67562 2.68306 9.55841C2.80027 9.4412 2.95924 9.37535 3.125 9.37535H15.3664L10.8078 4.81753C10.6905 4.70026 10.6247 4.5412 10.6247 4.37535C10.6247 4.2095 10.6905 4.05044 10.8078 3.93316C10.9251 3.81588 11.0841 3.75 11.25 3.75C11.4159 3.75 11.5749 3.81588 11.6922 3.93316L17.3172 9.55816C17.3753 9.61621 17.4214 9.68514 17.4528 9.76101C17.4843 9.83688 17.5005 9.91821 17.5005 10.0003C17.5005 10.0825 17.4843 10.1638 17.4528 10.2397C17.4214 10.3156 17.3753 10.3845 17.3172 10.4425Z"/>
                </svg></div>';
        echo '</div>';

        $output['gallery-slider'] = ob_get_clean();
    } else if ($gallery_layout == 'gallery-grid' || $gallery_layout == 'gallery-grid-top') {
        ob_start();
        $html = '<div class="bt-gallery-grid-product__item">' . somnia_get_gallery_image_html($main_image_id, true, false) . '</div>';

        if (!empty($gallery_images)) {
            foreach ($gallery_images as $key => $attachment_id) {
                $html .= '<div class="bt-gallery-grid-product__item">' . somnia_get_gallery_image_html($attachment_id, true, false) . '</div>';
            }
        }
        echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $main_image_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

        $itemgallery = count($gallery_images) + 1;
        $output['gallery-grid'] = ob_get_clean();
        $output['itemgallery'] = $itemgallery;
    } else {
        ob_start();
        echo '<div class="woocommerce-product-gallery__slider bt-gallery-lightbox bt-gallery-zoomable">';
        echo '<div class="swiper-wrapper">';
        if ($main_image_id) {
            $html = somnia_get_gallery_image_html($main_image_id, true, true);

            if (!empty($gallery_images)) {
                foreach ($gallery_images as $key => $attachment_id) {
                    $html .= somnia_get_gallery_image_html($attachment_id, true, true);
                }
            }
            echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $main_image_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
        }
        echo '</div>';
        echo '<div class="swiper-button-prev"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.4995 10.0003C17.4995 10.1661 17.4337 10.3251 17.3165 10.4423C17.1992 10.5595 17.0403 10.6253 16.8745 10.6253H4.63311L9.1917 15.1832C9.24977 15.2412 9.29583 15.3102 9.32726 15.386C9.35869 15.4619 9.37486 15.5432 9.37486 15.6253C9.37486 15.7075 9.35869 15.7888 9.32726 15.8647C9.29583 15.9405 9.24977 16.0095 9.1917 16.0675C9.13363 16.1256 9.0647 16.1717 8.98882 16.2031C8.91295 16.2345 8.83164 16.2507 8.74951 16.2507C8.66739 16.2507 8.58607 16.2345 8.5102 16.2031C8.43433 16.1717 8.3654 16.1256 8.30733 16.0675L2.68233 10.4425C2.62422 10.3845 2.57812 10.3156 2.54667 10.2397C2.51521 10.1638 2.49902 10.0825 2.49902 10.0003C2.49902 9.91821 2.51521 9.83688 2.54667 9.76101C2.57812 9.68514 2.62422 9.61621 2.68233 9.55816L8.30733 3.93316C8.4246 3.81588 8.58366 3.75 8.74951 3.75C8.91537 3.75 9.07443 3.81588 9.1917 3.93316C9.30898 4.05044 9.37486 4.2095 9.37486 4.37535C9.37486 4.5412 9.30898 4.70026 9.1917 4.81753L4.63311 9.37535H16.8745C17.0403 9.37535 17.1992 9.4412 17.3165 9.55841C17.4337 9.67562 17.4995 9.83459 17.4995 10.0003Z"/>
                </svg></div>
                <div class="swiper-button-next"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.3172 10.4425L11.6922 16.0675C11.5749 16.1848 11.4159 16.2507 11.25 16.2507C11.0841 16.2507 10.9251 16.1848 10.8078 16.0675C10.6905 15.9503 10.6247 15.7912 10.6247 15.6253C10.6247 15.4595 10.6905 15.3004 10.8078 15.1832L15.3664 10.6253H3.125C2.95924 10.6253 2.80027 10.5595 2.68306 10.4423C2.56585 10.3251 2.5 10.1661 2.5 10.0003C2.5 9.83459 2.56585 9.67562 2.68306 9.55841C2.80027 9.4412 2.95924 9.37535 3.125 9.37535H15.3664L10.8078 4.81753C10.6905 4.70026 10.6247 4.5412 10.6247 4.37535C10.6247 4.2095 10.6905 4.05044 10.8078 3.93316C10.9251 3.81588 11.0841 3.75 11.25 3.75C11.4159 3.75 11.5749 3.81588 11.6922 3.93316L17.3172 9.55816C17.3753 9.61621 17.4214 9.68514 17.4528 9.76101C17.4843 9.83688 17.5005 9.91821 17.5005 10.0003C17.5005 10.0825 17.4843 10.1638 17.4528 10.2397C17.4214 10.3156 17.3753 10.3845 17.3172 10.4425Z"/>
                </svg></div>';
        echo '</div>';

        echo '<div class="woocommerce-product-gallery__slider-thumbs">';
        echo '<div class="swiper-wrapper">';
        $html = somnia_get_gallery_image_html($main_image_id, false, true);
        foreach ($gallery_images as $key => $attachment_id) {
            $html .= somnia_get_gallery_image_html($attachment_id, false, true);
        }
        echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $main_image_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
        echo '</div>';
        echo '</div>';
        $itemgallery = count($gallery_images) + 1;
        $output['slider-thumb'] = ob_get_clean();
        $output['itemgallery'] = $itemgallery;
    }

    wp_send_json_success($output);
}
add_action('wp_ajax_somnia_load_product_gallery', 'somnia_load_product_gallery');
add_action('wp_ajax_nopriv_somnia_load_product_gallery', 'somnia_load_product_gallery');

// ajax load product single variation
function somnia_load_product_variation()
{
    if (!isset($_POST['variation_id'])) {
        return;
    }
    // Get product gallery images
    $variation_id = intval($_POST['variation_id']);
    $variation = wc_get_product($variation_id);
    $variation_image_id = $variation->get_image_id();
    ob_start();
    // Add main product image variation
    if ($variation_image_id) {
        $full_size_image = wp_get_attachment_image_src($variation_image_id, 'full');
        $attributes = array(
            'title' => get_post_field('post_title', $variation_image_id),
            'data-caption' => get_post_field('post_excerpt', $variation_image_id),
            'data-src' => $full_size_image[0],
        )
    ?>
        <?php echo wp_get_attachment_image($variation_image_id, 'woocommerce_single', false, $attributes); ?>
    <?php
    }
    $output['image-variation'] = ob_get_clean();
    wp_send_json_success($output);
}
add_action('wp_ajax_somnia_load_product_variation', 'somnia_load_product_variation');
add_action('wp_ajax_nopriv_somnia_load_product_variation', 'somnia_load_product_variation');


// ajax load product toast
function somnia_load_product_toast()
{
    // Verify product_id is set
    if (!isset($_POST['idproduct'])) {
        wp_send_json_error('Product ID not provided');
        return;
    }

    // Sanitize inputs
    $product_id = absint($_POST['idproduct']);
    $status = sanitize_text_field(isset($_POST['status']) ? $_POST['status'] : 'add');
    $tools = sanitize_text_field(isset($_POST['tools']) ? $_POST['tools'] : 'wishlist');

    // Verify product exists
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error('Invalid product ID');
        return;
    }
    // Get wishlist URL
    $wishlist_url = home_url('/products-wishlist/');
    if (function_exists('get_field')) {
        $wishlist = get_field('wishlist', 'options');
        if (!empty($wishlist['page_wishlist'])) {
            $wishlist_url = get_permalink($wishlist['page_wishlist']);
        }
    }

    // Get compare URL
    $compare_url = home_url('/products-compare/');
    if (function_exists('get_field')) {
        $compare = get_field('compare', 'options');
        if (!empty($compare['page_compare'])) {
            $compare_url = get_permalink($compare['page_compare']);
        }
    }

    // Get cart URL
    $cart_url = home_url('/cart/');
    if (function_exists('wc_get_page_id')) {
        $cart_page_id = wc_get_page_id('cart');
        if ($cart_page_id > 0) {
            $cart_url = get_permalink($cart_page_id);
        }
    }
    // Get checkout URL
    $checkout_url = home_url('/checkout/');
    if (function_exists('wc_get_page_id')) {
        $checkout_page_id = wc_get_page_id('checkout');
        if ($checkout_page_id > 0) {
            $checkout_url = get_permalink($checkout_page_id);
        }
    }
    ob_start();
    ?>
    <div class="bt-product-toast">
        <div class="bt-product-toast--close">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M9.41183 8L15.6952 1.71665C15.7905 1.62455 15.8666 1.51437 15.9189 1.39255C15.9713 1.27074 15.9988 1.13972 16 1.00714C16.0011 0.874567 15.9759 0.743089 15.9256 0.620381C15.8754 0.497673 15.8013 0.386193 15.7076 0.292444C15.6138 0.198695 15.5023 0.124556 15.3796 0.0743523C15.2569 0.0241486 15.1254 -0.00111435 14.9929 3.76988e-05C14.8603 0.00118975 14.7293 0.0287337 14.6074 0.0810623C14.4856 0.133391 14.3755 0.209456 14.2833 0.30482L8 6.58817L1.71665 0.30482C1.52834 0.122941 1.27612 0.0223015 1.01433 0.0245764C0.752534 0.0268514 0.502106 0.131859 0.316983 0.316983C0.131859 0.502107 0.0268514 0.752534 0.0245764 1.01433C0.0223015 1.27612 0.122941 1.52834 0.30482 1.71665L6.58817 8L0.30482 14.2833C0.209456 14.3755 0.133391 14.4856 0.0810623 14.6074C0.0287337 14.7293 0.00118975 14.8603 3.76988e-05 14.9929C-0.00111435 15.1254 0.0241486 15.2569 0.0743523 15.3796C0.124556 15.5023 0.198695 15.6138 0.292444 15.7076C0.386193 15.8013 0.497673 15.8754 0.620381 15.9256C0.743089 15.9759 0.874567 16.0011 1.00714 16C1.13972 15.9988 1.27074 15.9713 1.39255 15.9189C1.51437 15.8666 1.62455 15.7905 1.71665 15.6952L8 9.41183L14.2833 15.6952C14.4226 15.8358 14.6006 15.9317 14.7945 15.9708C14.9885 16.0098 15.1898 15.9902 15.3726 15.9145C15.5554 15.8388 15.7115 15.7104 15.8211 15.5456C15.9306 15.3808 15.9886 15.1871 15.9877 14.9893C15.9878 14.8581 15.9619 14.7283 15.9117 14.6072C15.8615 14.4861 15.7879 14.376 15.6952 14.2833L9.41183 8Z" fill="#0C2C48" />
            </svg>
        </div>
        <div class="bt-product-toast--content">
            <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="bt-product-toast--image">
                <?php echo wp_kses_post($product->get_image('medium')); ?>
            </a>
            <div class="bt-product-toast--info">
                <p class="bt-product-toast--title">
                    <a href="<?php echo esc_url(get_permalink($product_id)); ?>">
                        <?php echo esc_html($product->get_name()); ?>
                    </a>
                    <?php
                    $message = '';
                    if ($tools === 'wishlist') {
                        $message = $status === 'add'
                            ? esc_html__('has been added to your wishlist.', 'somnia')
                            : esc_html__('has been removed from your wishlist.', 'somnia');
                    } elseif ($tools === 'compare') {
                        $message = $status === 'add'
                            ? esc_html__('has been added to your compare.', 'somnia')
                            : esc_html__('has been removed from your compare.', 'somnia');
                    } else {
                        $message = $status === 'add'
                            ? esc_html__('has been added to your cart.', 'somnia')
                            : esc_html__('has been removed from your cart.', 'somnia');
                    }
                    echo wp_kses_post($message);
                    ?>
                </p>
                <div class="bt-product-toast--button<?php echo esc_attr($tools === 'cart' ? ' bt-button-cart' : ''); ?>">
                    <?php
                    if ($tools === 'wishlist') {
                        echo '<a href="' . esc_url($wishlist_url) . '" class="bt-btn bt-button-hover">' . esc_html__('View Wishlist', 'somnia') . '</a>';
                    } else if ($tools === 'compare') {
                        echo '<a href="' . esc_url($compare_url) . '" class="bt-btn bt-button-hover">' . esc_html__('View Compare', 'somnia') . '</a>';
                    } else {
                        echo '<a href="' . esc_url($cart_url) . '" class="bt-btn bt-button-hover">' . esc_html__('View Cart', 'somnia') . '</a>';
                        echo '<a href="' . esc_url($checkout_url) . '" class="bt-btn bt-button-hover">' . esc_html__('Checkout', 'somnia') . '</a>';
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>
    <?php
    $output = array(
        'toast' => ob_get_clean()
    );
    wp_send_json_success($output);
}
add_action('wp_ajax_somnia_load_product_toast', 'somnia_load_product_toast');
add_action('wp_ajax_nopriv_somnia_load_product_toast', 'somnia_load_product_toast');


add_action('somnia_woocommerce_template_loop_add_to_cart_variable', 'somnia_woocommerce_template_loop_add_to_cart_variable', 10);
function somnia_woocommerce_template_loop_add_to_cart_variable()
{
    global $product;

    // Get product if global is not set
    if (!$product) {
        $product = wc_get_product();
    }

    // Check if product is valid
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    if ($product->is_type('variable')) {
        $product_id = $product->get_id();
        echo '<a class="button add_to_cart_button product_type_variable bt-loop-add-to-cart-btn" href="#" data-id="' . esc_attr($product_id) . '">';
        echo esc_html__('Choose Options', 'somnia');
        echo '</a>';
    }
}
// hook button add to cart variable after add to cart
add_action('woocommerce_after_add_to_cart_button', 'somnia_woocommerce_after_add_to_cart_button', 1);
function somnia_woocommerce_after_add_to_cart_button()
{
    global $product;

    // Check if product exists
    if (empty($product) || ! is_a($product, 'WC_Product')) {
        return;
    }

    $variation_id = 0;
    if (isset($_REQUEST['variation_id'])) {
        $variation_id = intval($_REQUEST['variation_id']);
    }

    if ($product->is_type('simple')) {
        echo '<a href="#"
        class="single_add_to_cart_button bt-button-hover bt-js-add-to-cart-simple"
        data-product-id="' . esc_attr($product->get_id()) . '">' . esc_html__('Add To Cart', 'somnia') . '</a>';
    }
    if ($product->is_type('variable')) {
        echo '<a href="#"
        class="bt-btn-add-to-cart-variable single_add_to_cart_button bt-button-hover bt-js-add-to-cart-variable disabled"
        data-product-quantity="1"
        data-product-id="' . esc_attr($product->get_id()) . '"
        data-variation="' . esc_attr($variation_id) . '">'
            . esc_html__('Add To Cart', 'somnia') .
            '</a>';

        echo '<a href="' . esc_url($product->get_permalink()) . '" 
        class="bt-btn-read-more bt-button-hover" 
        rel="nofollow">' . esc_html__('Read more', 'somnia') . '</a>';
    }
}

/**
 * Output single-product sticky bar in footer.
 * Shown only when product is simple or variable, purchasable and in stock.
 */
add_action('wp_footer', 'somnia_single_product_sticky_bar', 20);
function somnia_single_product_sticky_bar()
{
    if (!function_exists('get_field') || !get_field('enable_sticky_add_to_cart_buttons_on_single_product', 'options')) {
        return;
    }
    if (!is_product() || !function_exists('wc_get_product')) {
        return;
    }
    global $product;
    if (!$product || !is_a($product, 'WC_Product')) {
        $product = wc_get_product(get_the_ID());
    }
    if (!$product || !$product->is_purchasable() || !$product->is_in_stock()) {
        return;
    }
    $is_simple   = $product->is_type('simple');
    $is_variable = $product->is_type('variable');
    if (!$is_simple && !$is_variable) {
        return;
    }

    $title = $product->get_name();
    $image_id = $product->get_image_id();
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : wc_placeholder_img_src('thumbnail');
    $rating_html = $product->get_rating_count() > 0 ? wc_get_rating_html($product->get_average_rating()) : '';
    $price_html = $product->get_price_html();
    $product_id = $product->get_id();

    $btn_label_add = esc_html__('Add to cart', 'somnia');
    $btn_label_select = esc_html__('Select options', 'somnia');

    $sticky_bar_attrs = 'id="bt-single-product-sticky-bar" aria-hidden="true"';
    if ($is_variable) {
        $variation_names = array();
        $variation_prices = array();
        foreach ($product->get_available_variations() as $var) {
            $vid = (int) $var['variation_id'];
            $labels = array();
            if (!empty($var['attributes']) && is_array($var['attributes'])) {
                foreach ($var['attributes'] as $attr_key => $value_slug) {
                    if ((string) $value_slug === '') {
                        continue;
                    }
                    $attr_name = str_replace('attribute_', '', $attr_key);
                    if (taxonomy_exists($attr_name)) {
                        $term = get_term_by('slug', $value_slug, $attr_name);
                        $labels[] = $term && !is_wp_error($term) ? $term->name : $value_slug;
                    } else {
                        $labels[] = $value_slug;
                    }
                }
            }
            $variation_names[$vid] = implode(' / ', $labels);
            $variation_prices[$vid] = isset($var['price_html']) ? $var['price_html'] : '';
        }
        $sticky_bar_attrs .= ' data-variation-names="' . esc_attr(wp_json_encode($variation_names)) . '"';
        $sticky_bar_attrs .= ' data-variation-prices="' . esc_attr(wp_json_encode($variation_prices)) . '"';
    }
    $default_variation_name = '';
    if ($is_variable) {
        $default_variation_id = get_default_variation_id($product);
        if ($default_variation_id && !empty($variation_names[$default_variation_id])) {
            $default_variation_name = $variation_names[$default_variation_id];
        }
    }
    ?>
    <div class="bt-single-product-sticky-bar" <?php echo $sticky_bar_attrs; ?>>
        <div class="bt-single-product-sticky-bar__inner">
            <div class="bt-single-product-sticky-bar__product">
                <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="bt-single-product-sticky-bar__thumb">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" />
                </a>
                <div class="bt-single-product-sticky-bar__info">
                    <h3 class="bt-single-product-sticky-bar__title"><?php echo esc_html($title); ?></h3>
                    <?php if ($price_html) { ?>
                        <div class="bt-single-product-sticky-bar__price<?php echo $is_variable ? ' bt-product-type-variable' : ''; ?>"><?php echo $price_html; ?></div>
                    <?php } ?>
                    <?php if ($is_variable) { ?>
                    <div class="bt-single-product-sticky-bar__variation">
                        <div class="bt-single-product-sticky-bar__variation-name"><?php echo esc_html($default_variation_name); ?></div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="bt-single-product-sticky-bar__action">
                <?php if ($is_simple) { ?>
                    <a href="#" class="bt-single-product-sticky-bar__btn bt-js-sticky-add-to-cart bt-button-hover" data-product-id="<?php echo esc_attr($product_id); ?>" data-type="simple"><?php echo $btn_label_add; ?></a>
                <?php } else { ?>
                    <a href="#" class="bt-single-product-sticky-bar__btn bt-js-sticky-add-to-cart bt-button-hover" data-product-id="<?php echo esc_attr($product_id); ?>" data-type="variable" data-label-add="<?php echo esc_attr($btn_label_add); ?>" data-label-select="<?php echo esc_attr($btn_label_select); ?>"><?php echo $btn_label_select; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
}

function get_default_variation_id($product)
{
    if (!$product->is_type('variable')) {
        return 0;
    }

    $default_attributes = $product->get_default_attributes();
    $variation_id = 0;

    foreach ($product->get_available_variations() as $variation_values) {
        foreach ($variation_values['attributes'] as $key => $attribute_value) {
            $attribute_name = str_replace('attribute_', '', $key);
            $default_value = $product->get_variation_default_attribute($attribute_name);
            if ($default_value == $attribute_value) {
                $is_default_variation = true;
            } else {
                $is_default_variation = false;
                break;
            }
        }
        if ($is_default_variation) {
            $variation_id = $variation_values['variation_id'];
            break;
        } else {
            $variation_id = null;
        }
    }

    return $variation_id;
}

/* Bundle Save Widget - Get bundle products for modal */
function somnia_get_bundle_products()
{
    if (!isset($_POST['product_ids']) || empty($_POST['product_ids'])) {
        wp_send_json_error(array('message' => 'No products found'));
    }

    $product_ids = array_map('intval', $_POST['product_ids']);
    $html = '';

    foreach ($product_ids as $item_id) {
        $product = wc_get_product($item_id);
        if (!$product) {
            continue;
        }

        $is_variation = $product->is_type('variation');
        $product_name = $product->get_name();

        // Get variation attributes if applicable
        $variation_text = '';
        if ($is_variation) {
            $attributes = $product->get_attributes();
            $attr_labels = [];
            foreach ($attributes as $attr_name => $attr_value) {
                // Get term name if taxonomy
                if (taxonomy_exists($attr_name)) {
                    $term = get_term_by('slug', $attr_value, $attr_name);
                    $attr_value = $term ? $term->name : $attr_value;
                }

                $attr_labels[] = ucfirst($attr_value);
            }
            if (!empty($attr_labels)) {
                $variation_text = implode('/', $attr_labels);
            }
        }

        $product_image = $product->get_image('thumbnail');
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $price = $product->get_price();

        ob_start();
    ?>
        <div class="bt-modal-product--item">
            <?php echo '<div class="bt-product-thumb">' . $product_image . '</div>'; ?>

            <div class="bt-product-info">
                <h4 class="bt-product-name"><?php echo esc_html($product_name); ?></h4>
                <div class="bt-product-price">
                    <?php
                    $price_html  = $product->get_price_html();
                    echo wp_kses_post($price_html);
                    ?>
                </div>
                <?php if ($variation_text) : ?>
                    <div class="bt-product-variation"><?php echo esc_html($variation_text); ?></div>
                <?php endif; ?>
            </div>
            <button class="bt-modal-add-product" data-product-id="<?php echo esc_attr($item_id); ?>">
                <?php _e('Add', 'somnia'); ?>
            </button>
        </div>
    <?php
        $html .= ob_get_clean();
    }

    if (empty($html)) {
        wp_send_json_error(array('message' => 'No valid products found'));
    }

    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_somnia_get_bundle_products', 'somnia_get_bundle_products');
add_action('wp_ajax_nopriv_somnia_get_bundle_products', 'somnia_get_bundle_products');

/* Bundle Save Widget - Get single product item */
function somnia_get_bundle_product_item()
{
    if (!isset($_POST['product_id'])) {
        wp_send_json_error(array('message' => 'No product ID provided'));
    }

    $item_id = intval($_POST['product_id']);
    $product = wc_get_product($item_id);

    if (!$product) {
        wp_send_json_error(array('message' => 'Product not found'));
    }

    $is_variation = $product->is_type('variation');
    $parent_id = $is_variation ? $product->get_parent_id() : $item_id;
    $variation_id = $is_variation ? $item_id : 0;

    $product_link = get_permalink($parent_id);
    $product_name = $product->get_name();

    // Get variation attributes if applicable
    $variation_text = '';
    if ($is_variation) {
        $attributes = $product->get_attributes();
        $attr_labels = [];
        foreach ($attributes as $attr_name => $attr_value) {
            // Get term name if taxonomy
            if (taxonomy_exists($attr_name)) {
                $term = get_term_by('slug', $attr_value, $attr_name);
                $attr_value = $term ? $term->name : $attr_value;
            }

            $attr_labels[] = ucfirst($attr_value);
        }
        if (!empty($attr_labels)) {
            $variation_text = implode('/', $attr_labels);
        }
    }

    $product_image = $product->get_image('thumbnail');
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    $price = $product->get_price();

    ob_start();
    ?>
    <div class="bt-bundle-product--item"
        data-product-id="<?php echo esc_attr($parent_id); ?>"
        data-variation-id="<?php echo esc_attr($variation_id); ?>"
        data-price="<?php echo esc_attr($price); ?>"
        data-regular-price="<?php echo esc_attr($regular_price ? $regular_price : $price); ?>">
        <div class="bt-product-thumb">
            <?php echo '<a href="' . esc_url($product_link) . '">' . $product_image . '</a>'; ?>
        </div>
        <div class="bt-product-info">
            <h4 class="bt-product-name">
                <?php echo '<a href="' . esc_url($product_link) . '">' . esc_html($product_name) . '</a>'; ?>
            </h4>
            <div class="bt-product-price">
                <?php
                $price_html  = $product->get_price_html();
                echo wp_kses_post($price_html);
                ?>
            </div>
            <?php if ($variation_text) : ?>
                <div class="bt-product-variation"><?php echo esc_html($variation_text); ?></div>
            <?php endif; ?>
        </div>
        <div class="bt-product-actions">
            <button class="bt-product-remove" data-item-id="<?php echo esc_attr($item_id); ?>">
                <span class="bt-remove-text"><?php _e('REMOVE', 'somnia'); ?></span>
            </button>
        </div>
    </div>
<?php
    $html = ob_get_clean();
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_somnia_get_bundle_product_item', 'somnia_get_bundle_product_item');
add_action('wp_ajax_nopriv_somnia_get_bundle_product_item', 'somnia_get_bundle_product_item');

/**
 * Get Frequently Bought Together products for a given product
 * 
 * @param int $product_id Product ID
 * @return array Array of WC_Product objects
 */
function somnia_get_frequently_bought_together($product_id = null)
{
    if (empty($product_id)) {
        global $product;
        if (!$product) {
            return array();
        }
        $product_id = $product->get_id();
    }

    $product_ids = get_post_meta($product_id, '_frequently_bought_together_ids', true);

    if (empty($product_ids) || !is_array($product_ids)) {
        return array();
    }

    $products = array();
    foreach ($product_ids as $id) {
        $fbt_product = wc_get_product($id);
        if ($fbt_product && $fbt_product->is_visible() && $fbt_product->is_in_stock()) {
            $products[] = $fbt_product;
        }
    }

    return $products;
}

/**
 * Display Frequently Bought Together products section
 * 
 * @param int $product_id Product ID
 */
function somnia_display_frequently_bought_together($product_id = null)
{
    global $product;

    // Get current product
    if (empty($product_id)) {
        if (!$product) {
            return;
        }
        $product_id = $product->get_id();
    } else {
        $product = wc_get_product($product_id);
    }

    if (!$product) {
        return;
    }

    $fbt_products = somnia_get_frequently_bought_together($product_id);

    if (empty($fbt_products)) {
        return;
    }

    // Currency settings
    $currency_symbol = get_woocommerce_currency_symbol();
    $decimal_separator = wc_get_price_decimal_separator();
    $thousand_separator = wc_get_price_thousand_separator();

?>
    <div class="somnia-frequently-bought-together">
        <?php
        // Get heading from product meta
        $custom_heading = get_post_meta($product_id, '_frequently_bought_together_title', true);

        // Use custom heading if available, otherwise use default
        $default_heading = __('Adjustable Base and Box Spring:', 'somnia');
        $heading = !empty($custom_heading) ? $custom_heading : $default_heading;

        // Allow filtering
        $heading = apply_filters('somnia_product_frequently_bought_together_heading', $heading, $product_id);

        if ($heading) :
        ?>
            <h2 class="fbt-heading"><?php echo esc_html($heading); ?></h2>
        <?php endif; ?>

        <div class="fbt-products-select-wrapper">
            <select class="fbt-products-select"
                id="fbt-products-select"
                name="fbt_product_id"
                data-main-product-id="<?php echo esc_attr($product_id); ?>"
                data-currency="<?php echo esc_attr($currency_symbol); ?>"
                data-decimal-separator="<?php echo esc_attr($decimal_separator); ?>"
                data-thousand-separator="<?php echo esc_attr($thousand_separator); ?>">
                <option value=""><?php esc_html_e('No Thanks', 'somnia'); ?></option>
                <?php
                // Display frequently bought together products only
                foreach ($fbt_products as $fbt_product) :
                    if (empty($fbt_product) || !$fbt_product->is_visible()) {
                        continue;
                    }

                    $fbt_price = $fbt_product->get_price();
                    $fbt_regular_price = $fbt_product->get_regular_price() ? $fbt_product->get_regular_price() : $fbt_price;
                    $is_variation = $fbt_product->is_type('variation');
                    $product_name = $fbt_product->get_name();

                    if ($is_variation) {
                        $parent_id = $fbt_product->get_parent_id();
                        $parent = wc_get_product($parent_id);
                        $attributes = $fbt_product->get_attributes();
                        $attr_labels = [];

                        foreach ($attributes as $attr_name => $attr_value) {
                            if (taxonomy_exists($attr_name)) {
                                $term = get_term_by('slug', $attr_value, $attr_name);
                                $attr_value = $term ? $term->name : $attr_value;
                            }
                            $attr_labels[] = ucfirst($attr_value);
                        }

                        if (!empty($attr_labels)) {
                            $product_name = $parent->get_name() . ' - ' . implode('/', $attr_labels);
                        }
                    }

                    // Format: Product Name - $Price
                    $formatted_price = wc_price($fbt_price);
                    $formatted_price = strip_tags($formatted_price); // Remove HTML tags from price
                    $option_text = $product_name . ' - ' . $formatted_price;
                ?>
                    <option value="<?php echo esc_attr($fbt_product->get_id()); ?>"
                        data-product-id="<?php echo esc_attr($fbt_product->get_id()); ?>"
                        data-price="<?php echo esc_attr($fbt_price); ?>"
                        data-regular-price="<?php echo esc_attr($fbt_regular_price); ?>">
                        <?php echo esc_html($option_text); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php
}

// Hook to automatically add FBT product when main product is added to cart
add_action('woocommerce_add_to_cart', 'somnia_auto_add_fbt_product', 10, 6);

function somnia_auto_add_fbt_product($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
{
    // Check if FBT product ID is provided from form submission
    if (isset($_REQUEST['fbt_product_id']) && !empty($_REQUEST['fbt_product_id'])) {
        $fbt_product_id = intval($_REQUEST['fbt_product_id']);

        // Verify it's a valid product
        $fbt_product = wc_get_product($fbt_product_id);

        if ($fbt_product && $fbt_product->is_purchasable() && $fbt_product->is_in_stock()) {
            // Check if FBT product is not already in cart to avoid duplicates
            $cart = WC()->cart->get_cart();
            $already_in_cart = false;

            foreach ($cart as $item_key => $item) {
                if ($item['product_id'] == $fbt_product_id || $item['variation_id'] == $fbt_product_id) {
                    $already_in_cart = true;
                    break;
                }
            }

            // Add FBT product to cart if not already there
            if (!$already_in_cart) {
                WC()->cart->add_to_cart($fbt_product_id, 1);
            }
        }
    }
}

/* Register Custom Order Statuses for Order Tracking */
if (!function_exists('somnia_register_custom_order_statuses')) {
    function somnia_register_custom_order_statuses()
    {
        // Register "Approved" status
        register_post_status('wc-approved', array(
            'label'                     => _x('Approved', 'Order status', 'somnia'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Approved <span class="count">(%s)</span>', 'Approved <span class="count">(%s)</span>', 'somnia')
        ));

        // Register "Shipped" status
        register_post_status('wc-shipped', array(
            'label'                     => _x('Shipped', 'Order status', 'somnia'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>', 'somnia')
        ));
    }
    add_action('init', 'somnia_register_custom_order_statuses');
}

/* Add Custom Statuses to WooCommerce Order Statuses */
if (!function_exists('somnia_add_custom_order_statuses')) {
    function somnia_add_custom_order_statuses($order_statuses)
    {
        $new_order_statuses = array();

        // Add custom statuses in the right position
        foreach ($order_statuses as $key => $status) {
            $new_order_statuses[$key] = $status;

            // Add "Approved" after "On hold"
            if ('wc-on-hold' === $key) {
                $new_order_statuses['wc-approved'] = _x('Approved', 'Order status', 'somnia');
            }

            // Add "Shipped" after "Processing"
            if ('wc-processing' === $key) {
                $new_order_statuses['wc-shipped'] = _x('Shipped', 'Order status', 'somnia');
            }
        }

        return $new_order_statuses;
    }
    add_filter('wc_order_statuses', 'somnia_add_custom_order_statuses');
}

/* Add Custom Statuses to Bulk Actions */
if (!function_exists('somnia_add_bulk_actions_custom_statuses')) {
    function somnia_add_bulk_actions_custom_statuses($bulk_actions)
    {
        // Add "Approved" status
        $bulk_actions['mark_approved'] = __('Change status to approved', 'somnia');

        // Add "Shipped" status  
        $bulk_actions['mark_shipped'] = __('Change status to shipped', 'somnia');

        return $bulk_actions;
    }
    add_filter('bulk_actions-edit-shop_order', 'somnia_add_bulk_actions_custom_statuses', 20, 1);
}

/* Order Tracking AJAX Handler */
if (!function_exists('somnia_track_order_callback')) {
    function somnia_track_order_callback()
    {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'somnia_order_tracking_nonce')) {
            wp_send_json_error(array(
                'message' => esc_html__('Security check failed.', 'somnia')
            ));
        }

        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            wp_send_json_error(array(
                'message' => esc_html__('WooCommerce is not active.', 'somnia')
            ));
        }

        // Get form data
        $order_id = isset($_POST['order_id']) ? sanitize_text_field($_POST['order_id']) : '';
        $billing_email = isset($_POST['billing_email']) ? sanitize_email($_POST['billing_email']) : '';

        // Validate inputs
        if (empty($order_id) || empty($billing_email)) {
            wp_send_json_error(array(
                'message' => esc_html__('Please fill in all required fields.', 'somnia')
            ));
        }

        // Remove # from order ID if present
        $order_id = str_replace('#', '', $order_id);

        // Try to get order
        $order = wc_get_order($order_id);

        // Check if order exists and email matches
        if (!$order) {
            wp_send_json_error(array(
                'message' => esc_html__('Order not found. Please check your Order ID.', 'somnia')
            ));
        }

        // Verify billing email matches
        if (strtolower($order->get_billing_email()) !== strtolower($billing_email)) {
            wp_send_json_error(array(
                'message' => esc_html__('The email address does not match the order. Please try again.', 'somnia')
            ));
        }

        // Get order status
        $order_status = $order->get_status();

        // Map WooCommerce statuses to tracking steps
        $status_map = array(
            'pending' => 1,      // Placed
            'on-hold' => 1,      // Placed
            'approved' => 2,     // Approved (Custom status)
            'processing' => 3,   // Processed
            'shipped' => 4,      // Shipped (Custom status)
            'completed' => 5,    // Delivery
            'failed' => 0,
            'cancelled' => 0,
            'refunded' => 0
        );

        $current_step = isset($status_map[$order_status]) ? $status_map[$order_status] : 1;

        // Build order tracking HTML with timeline
        ob_start();
    ?>
        <div class="bt-order-tracking-tabs">
            <div class="bt-tabs-nav">
                <button class="bt-tab-btn active" data-tab="tracking">
                    <?php esc_html_e('TRACK ORDER', 'somnia'); ?>
                </button>
                <button class="bt-tab-btn" data-tab="details">
                    <?php esc_html_e('ORDER DETAILS', 'somnia'); ?>
                </button>
            </div>

            <!-- Track Order Tab -->
            <div class="bt-tab-content active" id="tracking-tab">
                <div class="bt-order-tracking-timeline">
                    <!-- Progress Bar -->
                    <div class="bt-timeline-progress">

                        <div class="bt-timeline-steps">
                            <?php $progress_percent = ($current_step - 1) * 25; ?>
                            <div class="bt-progress-line" style="--progress-width: <?php echo esc_attr($progress_percent); ?>%;"></div>

                            <?php
                            $is_completed = $current_step >= 1 ? 'completed' : '';
                            $is_active = $current_step == 1 ? 'active' : '';
                            ?>
                            <div class="bt-step <?php echo esc_attr($is_completed); ?> <?php echo esc_attr($is_active); ?>">
                                <div class="bt-step-circle">
                                    <?php if ($current_step > 1) : ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="bt-step-label"><?php esc_html_e('Placed', 'somnia'); ?></div>
                            </div>

                            <?php
                            $is_completed = $current_step >= 2 ? 'completed' : '';
                            $is_active = $current_step == 2 ? 'active' : '';
                            ?>
                            <div class="bt-step <?php echo esc_attr($is_completed); ?> <?php echo esc_attr($is_active); ?>">
                                <div class="bt-step-circle">
                                    <?php if ($current_step > 2) : ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="bt-step-label"><?php esc_html_e('Approved', 'somnia'); ?></div>
                            </div>

                            <?php
                            $is_completed = $current_step >= 3 ? 'completed' : '';
                            $is_active = $current_step == 3 ? 'active' : '';
                            ?>
                            <div class="bt-step <?php echo esc_attr($is_completed); ?> <?php echo esc_attr($is_active); ?>">
                                <div class="bt-step-circle">
                                    <?php if ($current_step > 3) : ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="bt-step-label"><?php esc_html_e('Processed', 'somnia'); ?></div>
                            </div>

                            <?php
                            $is_completed = $current_step >= 4 ? 'completed' : '';
                            $is_active = $current_step == 4 ? 'active' : '';
                            ?>
                            <div class="bt-step <?php echo esc_attr($is_completed); ?> <?php echo esc_attr($is_active); ?>">
                                <div class="bt-step-circle">
                                    <?php if ($current_step > 4) : ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="bt-step-label"><?php esc_html_e('Shipped', 'somnia'); ?></div>
                            </div>

                            <?php
                            $is_completed = $current_step >= 5 ? 'completed' : '';
                            $is_active = $current_step == 5 ? 'active' : '';
                            ?>
                            <div class="bt-step <?php echo esc_attr($is_completed); ?> <?php echo esc_attr($is_active); ?>">
                                <div class="bt-step-circle">
                                    <?php if ($current_step >= 5) : ?>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="bt-step-label"><?php esc_html_e('Completed', 'somnia'); ?></div>
                            </div>
                        </div>

                    </div>

                    <!-- Status Details -->
                    <div class="bt-tracking-details">
                        <?php
                        // Get order notes/status history
                        $notes = wc_get_order_notes(array(
                            'order_id' => $order->get_id(),
                            'limit' => 10,
                            'order_by' => 'date_created',
                            'order' => 'DESC'
                        ));

                        if (!empty($notes)) :
                        ?>
                            <div class="bt-status-section">
                                <h4 class="bt-section-title"><?php esc_html_e('Order Notes', 'somnia'); ?></h4>
                                <?php
                                foreach ($notes as $note) {
                                    if ($note->customer_note) continue;
                                ?>
                                    <div class="bt-status-item">
                                        <span class="bt-status-date">
                                            <?php echo esc_html($note->date_created->date_i18n(get_option('date_format') . ' ' . get_option('time_format'))); ?>
                                        </span>
                                        <span class="bt-status-text">
                                            <?php echo wp_kses_post($note->content); ?>
                                        </span>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Order Details Tab -->
            <div class="bt-tab-content" id="details-tab">
                <div class="bt-order-details">
                    <div class="bt-order-info">
                        <div class="bt-order-row">
                            <span class="bt-label"><?php esc_html_e('Order Number:', 'somnia'); ?></span>
                            <span class="bt-value">#<?php echo esc_html($order->get_order_number()); ?></span>
                        </div>
                        <div class="bt-order-row">
                            <span class="bt-label"><?php esc_html_e('Order Date:', 'somnia'); ?></span>
                            <span class="bt-value"><?php echo esc_html($order->get_date_created()->date_i18n(get_option('date_format'))); ?></span>
                        </div>
                        <div class="bt-order-row">
                            <span class="bt-label"><?php esc_html_e('Total:', 'somnia'); ?></span>
                            <span class="bt-value"><?php echo wc_price($order->get_total(), array('currency' => $order->get_currency())); ?></span>
                        </div>
                    </div>

                    <?php if ($order->get_customer_note()) : ?>
                        <div class="bt-order-note">
                            <h4><?php esc_html_e('Order Note:', 'somnia'); ?></h4>
                            <p><?php echo esc_html($order->get_customer_note()); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="bt-order-items">
                        <h4><?php esc_html_e('Order Items', 'somnia'); ?></h4>
                        <div class="bt-order-items-list">
                            <?php
                            foreach ($order->get_items() as $item_id => $item) {
                                $product = $item->get_product();
                                $line_total = $order->get_formatted_line_subtotal($item);
                            ?>
                                <div class="bt-order-item">
                                    <div class="bt-order-item-thumb">
                                        <?php
                                        if ($product && $product->get_image_id()) {
                                            echo wp_kses_post($product->get_image('thumbnail'));
                                        } else {
                                            echo wc_placeholder_img('thumbnail');
                                        }
                                        ?>
                                    </div>
                                    <div class="bt-order-item-content">
                                        <div class="bt-order-item-title">
                                            <?php echo esc_html($item->get_name()); ?>
                                            <?php
                                            if ($item->get_variation_id()) {
                                                $variation_data = wc_get_formatted_variation($item->get_product(), true);
                                                echo '<div class="bt-variation">' . wp_kses_post($variation_data) . '</div>';
                                            }
                                            ?>
                                        </div>
                                        <div class="bt-order-item-meta">
                                            <span class="bt-qty-price">
                                                <?php echo esc_html($item->get_quantity()); ?> × <?php echo wp_kses_post($line_total); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <?php
                    // Get shipping or billing info
                    $use_shipping = !empty($order->get_shipping_first_name()) || !empty($order->get_shipping_address_1());

                    if ($use_shipping) {
                        $name = trim($order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name());

                        // Build address without name and company
                        $address_parts = array_filter(array(
                            $order->get_shipping_address_1(),
                            $order->get_shipping_address_2(),
                            $order->get_shipping_city(),
                            $order->get_shipping_state(),
                            WC()->countries->countries[$order->get_shipping_country()] ?? $order->get_shipping_country()
                        ));
                        $address = implode(', ', $address_parts);

                        $phone = $order->get_billing_phone(); // Phone usually from billing
                        $email = $order->get_billing_email(); // Email usually from billing
                        $address_title = __('Shipping Information', 'somnia');
                    } else {
                        $name = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());

                        // Build address without name and company
                        $address_parts = array_filter(array(
                            $order->get_billing_address_1(),
                            $order->get_billing_address_2(),
                            $order->get_billing_city(),
                            $order->get_billing_state(),
                            WC()->countries->countries[$order->get_billing_country()] ?? $order->get_billing_country()
                        ));
                        $address = implode(', ', $address_parts);

                        $phone = $order->get_billing_phone();
                        $email = $order->get_billing_email();
                        $address_title = __('Billing Information', 'somnia');
                    }
                    ?>
                    <div class="bt-order-shipping">
                        <h4><?php echo esc_html($address_title); ?></h4>

                        <?php if (!empty($name)) : ?>
                            <div class="bt-info-row">
                                <span class="bt-info-label"><?php esc_html_e('Name:', 'somnia'); ?></span>
                                <span class="bt-info-value"><?php echo esc_html($name); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($address)) : ?>
                            <div class="bt-info-row">
                                <span class="bt-info-label"><?php esc_html_e('Address:', 'somnia'); ?></span>
                                <span class="bt-info-value">
                                    <?php echo wp_kses_post($address); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($phone)) : ?>
                            <div class="bt-info-row">
                                <span class="bt-info-label"><?php esc_html_e('Phone:', 'somnia'); ?></span>
                                <span class="bt-info-value">
                                    <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($email)) : ?>
                            <div class="bt-info-row">
                                <span class="bt-info-label"><?php esc_html_e('Email:', 'somnia'); ?></span>
                                <span class="bt-info-value">
                                    <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
<?php
        $html = ob_get_clean();

        wp_send_json_success(array(
            'message' => esc_html__('Order found successfully!', 'somnia'),
            'html' => $html,
            'current_step' => $current_step
        ));
    }
    add_action('wp_ajax_somnia_track_order', 'somnia_track_order_callback');
    add_action('wp_ajax_nopriv_somnia_track_order', 'somnia_track_order_callback');
}
