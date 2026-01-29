<?php
function somnia_product_cron_exec()
{
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );

    $products = new WP_Query($args);

    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            $product_id = get_the_ID();
            $sale_date = get_post_meta($product_id, '_product_datetime', true);
            $product_sold_sale = (int)get_post_meta($product_id, '_product_sold_sale', true);
            $product_stock_sale = (int)get_post_meta($product_id, '_product_stock_sale', true);
            
            if ($sale_date) {
                $sale_date = date('Y-m-d H:i:s', strtotime($sale_date . ' +1 day'));
                update_post_meta($product_id, '_product_datetime', $sale_date);
            }
            
            if ($product_sold_sale + 10 > $product_stock_sale) {
                if ($product_sold_sale - 15 <= 0) {
                    update_post_meta($product_id, '_product_sold_sale', 6);
                } else {
                    update_post_meta($product_id, '_product_sold_sale', $product_sold_sale - 15);
                }
            }
        }
        wp_reset_postdata();
    }
}
add_action('somnia_product_cron_hook', 'somnia_product_cron_exec');
