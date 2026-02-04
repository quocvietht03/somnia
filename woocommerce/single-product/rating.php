<?php

/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

if (! defined('ABSPATH')) {
    exit;
}

global $product;

if (! wc_review_ratings_enabled()) {
    return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();

if ($rating_count < 1) {
    return;
}
?>
<div class="bt-product-rating woocommerce-product-rating">
    <?php echo wc_get_rating_html($average, $rating_count); ?>
    <?php if ($rating_count): ?>
        <span class="bt-product-rating--average"><?php echo esc_html(number_format((float) $average, 1)); ?></span>
        <span class="bt-product-rating--count"><?php echo esc_html('('. $rating_count .')'); ?></span>
    <?php endif; ?>
</div>
