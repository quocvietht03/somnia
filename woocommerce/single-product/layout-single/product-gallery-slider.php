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
        ?>
        <div class="images bt-gallery-slider-products">
            <div class="bt-gallery-slider-product bt-gallery-lightbox bt-gallery-zoomable">
                <div class="swiper-wrapper">
                    <?php 
                        if ( $featured_image_id ) {
                            $html = somnia_get_gallery_image_html( $featured_image_id, true, true );

                            if(!empty($attachment_ids)) {
                                foreach ( $attachment_ids as $key => $attachment_id ) {
                                    $html .= somnia_get_gallery_image_html( $attachment_id, true, true );
                                }
                            }
                            echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $featured_image_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
                        }
                    ?>
                </div>
                <div class="swiper-button-prev"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.4995 10.0003C17.4995 10.1661 17.4337 10.3251 17.3165 10.4423C17.1992 10.5595 17.0403 10.6253 16.8745 10.6253H4.63311L9.1917 15.1832C9.24977 15.2412 9.29583 15.3102 9.32726 15.386C9.35869 15.4619 9.37486 15.5432 9.37486 15.6253C9.37486 15.7075 9.35869 15.7888 9.32726 15.8647C9.29583 15.9405 9.24977 16.0095 9.1917 16.0675C9.13363 16.1256 9.0647 16.1717 8.98882 16.2031C8.91295 16.2345 8.83164 16.2507 8.74951 16.2507C8.66739 16.2507 8.58607 16.2345 8.5102 16.2031C8.43433 16.1717 8.3654 16.1256 8.30733 16.0675L2.68233 10.4425C2.62422 10.3845 2.57812 10.3156 2.54667 10.2397C2.51521 10.1638 2.49902 10.0825 2.49902 10.0003C2.49902 9.91821 2.51521 9.83688 2.54667 9.76101C2.57812 9.68514 2.62422 9.61621 2.68233 9.55816L8.30733 3.93316C8.4246 3.81588 8.58366 3.75 8.74951 3.75C8.91537 3.75 9.07443 3.81588 9.1917 3.93316C9.30898 4.05044 9.37486 4.2095 9.37486 4.37535C9.37486 4.5412 9.30898 4.70026 9.1917 4.81753L4.63311 9.37535H16.8745C17.0403 9.37535 17.1992 9.4412 17.3165 9.55841C17.4337 9.67562 17.4995 9.83459 17.4995 10.0003Z"/>
                </svg></div>
                <div class="swiper-button-next"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.3172 10.4425L11.6922 16.0675C11.5749 16.1848 11.4159 16.2507 11.25 16.2507C11.0841 16.2507 10.9251 16.1848 10.8078 16.0675C10.6905 15.9503 10.6247 15.7912 10.6247 15.6253C10.6247 15.4595 10.6905 15.3004 10.8078 15.1832L15.3664 10.6253H3.125C2.95924 10.6253 2.80027 10.5595 2.68306 10.4423C2.56585 10.3251 2.5 10.1661 2.5 10.0003C2.5 9.83459 2.56585 9.67562 2.68306 9.55841C2.80027 9.4412 2.95924 9.37535 3.125 9.37535H15.3664L10.8078 4.81753C10.6905 4.70026 10.6247 4.5412 10.6247 4.37535C10.6247 4.2095 10.6905 4.05044 10.8078 3.93316C10.9251 3.81588 11.0841 3.75 11.25 3.75C11.4159 3.75 11.5749 3.81588 11.6922 3.93316L17.3172 9.55816C17.3753 9.61621 17.4214 9.68514 17.4528 9.76101C17.4843 9.83688 17.5005 9.91821 17.5005 10.0003C17.5005 10.0825 17.4843 10.1638 17.4528 10.2397C17.4214 10.3156 17.3753 10.3845 17.3172 10.4425Z"/>
                </svg></div>
                <div class="bt-swiper-pagination swiper-pagination"></div>
            </div>
        </div>
        <div class="summary entry-summary">
            <div class="bt-single-product-info">
                <div class="bt-single-product-info__inner">
                    <div class="woocommerce-product-rating-sold">
                        <?php
                        do_action('somnia_woocommerce_shop_loop_item_label');
                        do_action('somnia_woocommerce_template_single_rating');
                        ?>
                    </div>
                    <?php do_action('somnia_woocommerce_template_single_title'); ?>
                    <div class="woocommerce-product-price-wrap">
                        <?php
                        do_action('somnia_woocommerce_template_single_price');
                        do_action('somnia_woocommerce_show_product_loop_sale_flash');

                        ?>
                    </div>
                    <div class="bt-product-excerpt-add-to-cart">
                        <?php
                        // do_action('somnia_woocommerce_template_single_excerpt');
                        do_action('somnia_woocommerce_template_single_countdown'); 
                        do_action('somnia_woocommerce_template_single_add_to_cart');
                        ?>
                    </div>
                </div>
            </div>

            <div class="bt-single-product-sumary">
                <?php 
                do_action('somnia_woocommerce_template_single_more_information');
                do_action('somnia_woocommerce_template_single_meta');
                do_action('somnia_woocommerce_template_upsell_products');
                do_action('somnia_woocommerce_template_single_safe_checkout');
                do_action('somnia_woocommerce_template_single_toggle');
                ?>
            </div>
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