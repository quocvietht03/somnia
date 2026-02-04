<?php
defined('ABSPATH') || exit;

global $product;
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('bt-' . $args['layout'], $product); ?>>
    <?php
    $ajax_add_to_cart_enabled = false;
    if (function_exists('get_field')) {
        $ajax_add_to_cart_enabled = get_field('enable_ajax_add_to_cart_buttons_on_single_product', 'options');
    }
    $bt_product_inner_class = 'bt-product-inner';
    if ($ajax_add_to_cart_enabled && $product && ($product->is_type('simple') || $product->is_type('variable'))) {
        $bt_product_inner_class .= ' bt-add-cart-ajax';
    }
    $parent_image_id = $product->get_image_id();
    $parent_image_src = $parent_image_id ? wp_get_attachment_url($parent_image_id) : '';
    ?>
    <div class="<?php echo esc_attr($bt_product_inner_class); ?>" data-parent-image-src="<?php echo esc_url($parent_image_src); ?>">
        <?php

        /**
         * Hook: woocommerce_before_single_product_summary.
         *
         * @hooked woocommerce_show_product_sale_flash - 10
         * @hooked woocommerce_show_product_images - 20
         */
        //   do_action('woocommerce_before_single_product_summary');

        $featured_image_id = $product->get_image_id();
        
        // Initialize attachment_ids with default product gallery
        $attachment_ids = $product->get_gallery_image_ids();
        
        // Check if product has default variation and load its images
        $default_variation_id = 0;
        $use_variation_images = false;
        
        if ($product->is_type('variable')) {
            // Get default variation ID using the helper function
            if (function_exists('get_default_variation_id')) {
                $default_variation_id = get_default_variation_id($product);
            }
            
            // If we have a default variation, check if it has custom image
            if ($default_variation_id && $default_variation_id > 0) {
                $variation = wc_get_product($default_variation_id);
                if ($variation) {
                    $variation_image_id = $variation->get_image_id();
                    
                    // Only use variation images if variation has a custom image that's different from parent
                    if ($variation_image_id && $variation_image_id > 0 && (int)$variation_image_id !== (int)$featured_image_id) {
                        $featured_image_id = (int)$variation_image_id;
                        $use_variation_images = true;
                        
                        // Get variation gallery images
                        $variation_gallery = get_post_meta($default_variation_id, '_variation_gallery', true);
                        if (!empty($variation_gallery)) {
                            $attachment_ids = explode(',', $variation_gallery);
                            $attachment_ids = array_map('intval', $attachment_ids);
                            $attachment_ids = array_filter($attachment_ids);
                        } else {
                            $attachment_ids = array();
                        }
                    }
                    // If variation doesn't have custom image, use default product gallery (already set above)
                }
            }
        }
        
        $itemgallery = count($attachment_ids) + 1;
        if ($args['layout'] === 'gallery-four-columns') {
            $show_number = 8;
        } elseif ($args['layout'] === 'gallery-three-columns') {
            $show_number = 6;
        } elseif ($args['layout'] === 'gallery-two-columns') {
            $show_number = 6;
        } elseif ($args['layout'] === 'gallery-stacked') {
            $show_number = 5;
        } else {
            $show_number = 3;
        }

        ?>
        <div class="images bt-gallery-grid-products" data-items="<?php echo esc_attr($itemgallery); ?>" data-shown="<?php echo esc_attr($show_number); ?>">
            
            <div class="bt-gallery-grid-product bt-gallery-lightbox bt-gallery-zoomable">
                <?php 
                    $html = '<div class="bt-gallery-grid-product__item">' . somnia_get_gallery_image_html( $featured_image_id, true, false ) . '</div>';

                    if(!empty($attachment_ids)) {
                        foreach ( $attachment_ids as $key => $attachment_id ) {
                            $html .= '<div class="bt-gallery-grid-product__item">' . somnia_get_gallery_image_html( $attachment_id, true, false ) . '</div>';
                        }
                    }
                    echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $featured_image_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
                ?>
            </div>
            <?php
            echo '<button class="bt-show-gallery-lightbox"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 4H4m0 0v4m0-4 5 5m7-5h4m0 0v4m0-4-5 5M8 20H4m0 0v-4m0 4 5-5m7 5h4m0 0v-4m0 4-5-5"></path>
                </svg></button>';
            echo '<button class="bt-show-more">' . esc_html__('Show More', 'somnia') . '</button>';
            ?>
        </div>
        <div class="summary entry-summary">
            <div class="woocommerce-product-rating-sold">
                <?php
                do_action('somnia_woocommerce_shop_loop_item_label');
                do_action('somnia_woocommerce_template_single_rating');
                ?>
            </div>
            <?php
            do_action('somnia_woocommerce_template_single_title');
            ?>
            <div class="woocommerce-product-price-wrap">
                <?php
                do_action('somnia_woocommerce_template_single_price');
                do_action('somnia_woocommerce_show_product_loop_sale_flash');

                ?>
            </div>
            <div class="bt-product-excerpt-add-to-cart">
                <?php
                do_action('somnia_woocommerce_template_single_excerpt');
                do_action('somnia_woocommerce_template_single_countdown'); 
                do_action('somnia_woocommerce_template_single_add_to_cart');
                do_action('somnia_woocommerce_template_single_more_information');
                ?>
            </div>
            <?php 
            do_action('somnia_woocommerce_template_single_meta');
            do_action('somnia_woocommerce_template_upsell_products');
            do_action('somnia_woocommerce_template_single_safe_checkout');
            do_action('somnia_woocommerce_template_single_toggle');
            ?>

        </div>
    </div>
    <?php

    /**
     * Hook: somnia_woocommerce_template_related_products.
     *
     * @hooked somnia_output_product_extra_content - 18
     * @hooked woocommerce_output_related_products - 20
     */
    if (function_exists('get_field')) {
        $related_posts = get_field('product_related_posts', 'options');
        $enable_related_product = $related_posts['enable_related_product'];
        if ($enable_related_product) {
            do_action('somnia_woocommerce_template_related_products');
        }
    }
    ?>


</div>