<?php

/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.5.0
 */

defined('ABSPATH') || exit;

use Automattic\WooCommerce\Enums\ProductType;

if (!function_exists('wc_get_gallery_image_html')) {
    return;
}

global $product;

// Check if we're in quick view mode
// Note: In quick view, always use 'bottom-thumbnail' layout for better UX in the popup
$is_quick_view = (isset($_REQUEST['action']) && $_REQUEST['action'] === 'somnia_products_quick_view');

if ($is_quick_view) {
    // Force bottom-thumbnail layout for quick view
    $product_layout = 'bottom-thumbnail';
} else {
    // Use product meta or URL parameter for regular product pages
    $product_layout = get_post_meta($product->get_id(), '_layout_product', true);
    if (isset($_GET['layout']) && !empty($_GET['layout'])) {
        $product_layout = sanitize_text_field($_GET['layout']);
    }
}
$columns           = apply_filters('woocommerce_product_thumbnails_columns', 4);
$post_thumbnail_id = $product->get_image_id();
// Check if product has default variation and load its images
$default_variation_id = 0;
$use_variation_images = false;

// Initialize attachment_ids with default product gallery
$attachment_ids = $product->get_gallery_image_ids();

if ($product->is_type('variable') && !$is_quick_view) {
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
            if ($variation_image_id && $variation_image_id > 0 && (int)$variation_image_id !== (int)$post_thumbnail_id) {
                $post_thumbnail_id = (int)$variation_image_id;
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

$wrapper_classes   = apply_filters(
    'woocommerce_single_product_image_gallery_classes',
    array(
        'woocommerce-product-gallery',
        'woocommerce-product-gallery--' . ($post_thumbnail_id ? 'with-images' : 'without-images'),
        'woocommerce-product-gallery--columns-' . absint($columns),
        'images',
        'bt-' . $product_layout
    )
);

?>
<div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
    <?php
    // Only show video and 360 buttons on single product pages
    if (is_product()) {
       echo '<div class="bt-button-product-type-wrapper">';
        // Product Video Button and Popup
        $video_type = get_post_meta($product->get_id(), '_product_video_type', true);
        $video_link = get_post_meta($product->get_id(), '_product_video_link', true);

        if (!empty($video_link) && function_exists('somnia_get_product_video_embed')) {
            $video_html = somnia_get_product_video_embed($video_type, $video_link);

            if (!empty($video_html)) {
    ?>
                <div class="bt-button-product-video">
                    <!-- Popup Container -->
                    <div id="bt_product_video" class="bt-product-video__popup mfp-content__popup mfp-hide">
                        <div class="bt-product-video__content mfp-content__inner">
                            <?php 
                                echo wp_kses(
                                    $video_html,
                                    array(
                                        'div' => array(
                                            'class' => true,
                                            'style' => true,
                                        ),
                                        'iframe' => array(
                                            'src' => true,
                                            'width' => true,
                                            'height' => true,
                                            'style' => true,
                                            'frameborder' => true,
                                            'allow' => true,
                                            'allowfullscreen' => true,
                                        ),
                                        'video' => array(
                                            'controls' => true,
                                            'width' => true,
                                            'height' => true,
                                            'style' => true,
                                            'poster' => true,
                                            'autoplay' => true,
                                            'loop' => true,
                                            'muted' => true,
                                            'playsinline' => true,
                                            'preload' => true,
                                        ),
                                        'source' => array(
                                            'src'  => true,
                                            'type' => true,
                                        ),
                                    )
                                );
                            ?>
                        </div>
                    </div>

                    <!-- Video Button Trigger -->
                    <a href="#bt_product_video" class="bt-product-video__link bt-js-open-popup-link" title="<?php echo esc_attr__('Watch Product Video', 'somnia'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" />
                            <path d="M9.5 8.5L15.5 12L9.5 15.5V8.5Z" fill="currentColor" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
            <?php
            }
        }

        // Product 360 Button and Popup
        $product_360_file = get_post_meta($product->get_id(), '_product_360_images', true);

        if (!empty($product_360_file)) {
            $file_url = wp_get_attachment_url($product_360_file);

            if ($file_url) {
                $model_viewer_url = get_template_directory_uri() . '/assets/libs/model-viewer/model-viewer.min.js';
            ?>
                <!-- Load model-viewer as ES6 module -->
                <script type="module" src="<?php echo esc_url($model_viewer_url); ?>"></script>

                <div class="bt-button-product-360">
                    <!-- Popup Container -->
                    <div id="bt_product_360" class="bt-product-360__popup mfp-content__popup mfp-hide">
                        <div class="bt-product-360__content mfp-content__inner">
                            <model-viewer
                                src="<?php echo esc_url($file_url); ?>"
                                alt="<?php echo esc_attr($product->get_name()); ?> 360° View"
                                auto-rotate
                                camera-controls
                                shadow-intensity="1"
                                style="width: 100%; height: 600px;">
                            </model-viewer>
                        </div>
                    </div>

                    <!-- 360 Button Trigger -->
                    <a href="#bt_product_360" class="bt-product-360__link bt-js-open-popup-link" title="<?php echo esc_attr__('View 360°', 'somnia'); ?>">
                        <svg fill="currentColor" height="50px" width="50px" version="1.1" id="Layer_1" viewBox="0 0 480 480" xml:space="preserve">
                            <g>
                                <g>
                                    <g>
                                        <path d="M391.502,210.725c-5.311-1.52-10.846,1.555-12.364,6.865c-1.519,5.31,1.555,10.846,6.864,12.364
				C431.646,243.008,460,261.942,460,279.367c0,12.752-15.51,26.749-42.552,38.402c-29.752,12.82-71.958,22.2-118.891,26.425
				l-40.963-0.555c-0.047,0-0.093-0.001-0.139-0.001c-5.46,0-9.922,4.389-9.996,9.865c-0.075,5.522,4.342,10.06,9.863,10.134
				l41.479,0.562c0.046,0,0.091,0.001,0.136,0.001c0.297,0,0.593-0.013,0.888-0.039c49.196-4.386,93.779-14.339,125.538-28.024
				C470.521,316.676,480,294.524,480,279.367C480,251.424,448.57,227.046,391.502,210.725z" />
                                        <path d="M96.879,199.333c-5.522,0-10,4.477-10,10c0,5.523,4.478,10,10,10H138v41.333H96.879c-5.522,0-10,4.477-10,10
				s4.478,10,10,10H148c5.523,0,10-4.477,10-10V148c0-5.523-4.477-10-10-10H96.879c-5.522,0-10,4.477-10,10s4.478,10,10,10H138
				v41.333H96.879z" />
                                        <path d="M188.879,280.667h61.334c5.522,0,10-4.477,10-10v-61.333c0-5.523-4.477-10-10-10h-51.334V158H240c5.523,0,10-4.477,10-10
				s-4.477-10-10-10h-51.121c-5.523,0-10,4.477-10,10v122.667C178.879,276.19,183.356,280.667,188.879,280.667z M198.879,219.333
				h41.334v41.333h-41.334V219.333z" />
                                        <path d="M291.121,280.667h61.334c5.522,0,10-4.477,10-10V148c0-5.523-4.478-10-10-10h-61.334c-5.522,0-10,4.477-10,10v122.667
				C281.121,276.19,285.599,280.667,291.121,280.667z M301.121,158h41.334v102.667h-41.334V158z" />
                                        <path d="M182.857,305.537c-3.567-4.216-9.877-4.743-14.093-1.176c-4.217,3.567-4.743,9.876-1.177,14.093l22.366,26.44
				c-47.196-3.599-89.941-12.249-121.37-24.65C37.708,308.06,20,293.162,20,279.367c0-16.018,23.736-33.28,63.493-46.176
				c5.254-1.704,8.131-7.344,6.427-12.598c-1.703-5.253-7.345-8.13-12.597-6.427c-23.129,7.502-41.47,16.427-54.515,26.526
				C7.674,252.412,0,265.423,0,279.367c0,23.104,21.178,43.671,61.242,59.48c32.564,12.849,76.227,21.869,124.226,25.758
				l-19.944,22.104c-3.7,4.1-3.376,10.424,0.725,14.123c1.912,1.726,4.308,2.576,6.696,2.576c2.731,0,5.453-1.113,7.427-3.301
				l36.387-40.325c1.658-1.837,2.576-4.224,2.576-6.699v-0.764c0-2.365-0.838-4.653-2.365-6.458L182.857,305.537z" />
                                        <path d="M381.414,137.486h40.879c5.522,0,10-4.477,10-10V86.592c0-5.523-4.478-10-10-10h-40.879c-5.522,0-10,4.477-10,10v40.894
				C371.414,133.009,375.892,137.486,381.414,137.486z M391.414,96.592h20.879v20.894h-20.879V96.592z" />
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </a>
                </div>
    <?php
            }
        }
        echo '</div>';
    }
    ?>
    <div class="woocommerce-product-gallery__wrapper<?php echo (!empty($attachment_ids) && has_post_thumbnail()) ? ' bt-has-slide-thumbs' : ''; ?>">
        <?php

        if ($post_thumbnail_id) {
        ?>
            <div class="woocommerce-product-gallery__slider bt-gallery-lightbox bt-gallery-zoomable">
                <div class="swiper-wrapper">
                    <?php
                    $html = somnia_get_gallery_image_html($post_thumbnail_id, true, true);

                    if (!empty($attachment_ids)) {
                        foreach ($attachment_ids as $key => $attachment_id) {
                            $html .= somnia_get_gallery_image_html($attachment_id, true, true);
                        }
                    }
                    echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
                    ?>
                </div>

                <div class="swiper-button-prev"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5312 18.9698C15.6009 19.0395 15.6562 19.1222 15.6939 19.2132C15.7316 19.3043 15.751 19.4019 15.751 19.5004C15.751 19.599 15.7316 19.6965 15.6939 19.7876C15.6562 19.8786 15.6009 19.9614 15.5312 20.031C15.4615 20.1007 15.3788 20.156 15.2878 20.1937C15.1967 20.2314 15.0991 20.2508 15.0006 20.2508C14.902 20.2508 14.8045 20.2314 14.7134 20.1937C14.6224 20.156 14.5396 20.1007 14.47 20.031L6.96996 12.531C6.90023 12.4614 6.84491 12.3787 6.80717 12.2876C6.76943 12.1966 6.75 12.099 6.75 12.0004C6.75 11.9019 6.76943 11.8043 6.80717 11.7132C6.84491 11.6222 6.90023 11.5394 6.96996 11.4698L14.47 3.96979C14.6107 3.82906 14.8016 3.75 15.0006 3.75C15.1996 3.75 15.3905 3.82906 15.5312 3.96979C15.6719 4.11052 15.751 4.30139 15.751 4.50042C15.751 4.69944 15.6719 4.89031 15.5312 5.03104L8.5609 12.0004L15.5312 18.9698Z"/>
                </svg></div>
                <div class="swiper-button-next"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.031 12.531L9.53104 20.031C9.46136 20.1007 9.37863 20.156 9.28759 20.1937C9.19654 20.2314 9.09896 20.2508 9.00042 20.2508C8.90187 20.2508 8.80429 20.2314 8.71324 20.1937C8.6222 20.156 8.53947 20.1007 8.46979 20.031C8.40011 19.9614 8.34483 19.8786 8.30712 19.7876C8.26941 19.6965 8.25 19.599 8.25 19.5004C8.25 19.4019 8.26941 19.3043 8.30712 19.2132C8.34483 19.1222 8.40011 19.0395 8.46979 18.9698L15.4401 12.0004L8.46979 5.03104C8.32906 4.89031 8.25 4.69944 8.25 4.50042C8.25 4.30139 8.32906 4.11052 8.46979 3.96979C8.61052 3.82906 8.80139 3.75 9.00042 3.75C9.19944 3.75 9.39031 3.82906 9.53104 3.96979L17.031 11.4698C17.1008 11.5394 17.1561 11.6222 17.1938 11.7132C17.2316 11.8043 17.251 11.9019 17.251 12.0004C17.251 12.099 17.2316 12.1966 17.1938 12.2876C17.1561 12.3787 17.1008 12.4614 17.031 12.531Z" fill="#183F91"/>
                </svg></div>
            </div>

            <div class="woocommerce-product-gallery__slider-thumbs">
                <div class="swiper-wrapper">
                    <?php

                    $html = somnia_get_gallery_image_html($post_thumbnail_id, false, true);
                    
                    // Add gallery thumbnails
                    if (!empty($attachment_ids)) {
                        foreach ($attachment_ids as $key => $attachment_id) {
                            $html .= somnia_get_gallery_image_html($attachment_id, false, true, $key);
                        }
                    }
                    echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
                    ?>
                </div>
            </div>
        <?php
        } else {
            $wrapper_classname = $product->is_type(ProductType::VARIABLE) && ! empty($product->get_available_variations('image')) ?
                'woocommerce-product-gallery__image woocommerce-product-gallery__image--placeholder' :
                'woocommerce-product-gallery__image--placeholder';
            $html = sprintf('<div class="%s">', esc_attr($wrapper_classname));
            $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_single')), esc_html__('Awaiting product image', 'somnia'));
            $html .= '</div>';

            echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
        }
        ?>
    </div>
</div>