<?php

/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */
if (!defined('ABSPATH')) {
    exit;  // Exit if accessed directly
}

get_header('shop');
$product_id = get_the_ID();
$product = wc_get_product($product_id);
$product_type = $product->get_type();
?>
<div class="bt-product-breadcrumb">
	<div class="bt-container">
		<div class="bt-row-breadcrumb-product">
		<?php
        $home_text = 'Home';
        $delimiter = '/';
        echo '<div class="bt-breadcrumb">';
        echo somnia_page_breadcrumb($home_text, $delimiter);
        echo '</div>';
        // Product Navigation
        $previous_product = get_previous_post(true, '', 'product_cat');
        $next_product = get_next_post(true, '', 'product_cat');
        ?>
		<div class="bt-product-navigation">
			<?php if ($previous_product): ?>
				<a href="<?php echo get_permalink($previous_product->ID); ?>" class="prev-product">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M15.5312 18.9698C15.6009 19.0395 15.6562 19.1222 15.6939 19.2132C15.7316 19.3043 15.751 19.4019 15.751 19.5004C15.751 19.599 15.7316 19.6965 15.6939 19.7876C15.6562 19.8786 15.6009 19.9614 15.5312 20.031C15.4615 20.1007 15.3788 20.156 15.2878 20.1937C15.1967 20.2314 15.0991 20.2508 15.0006 20.2508C14.902 20.2508 14.8045 20.2314 14.7134 20.1937C14.6224 20.156 14.5396 20.1007 14.47 20.031L6.96996 12.531C6.90023 12.4614 6.84491 12.3787 6.80717 12.2876C6.76943 12.1966 6.75 12.099 6.75 12.0004C6.75 11.9019 6.76943 11.8043 6.80717 11.7132C6.84491 11.6222 6.90023 11.5394 6.96996 11.4698L14.47 3.96979C14.6107 3.82906 14.8016 3.75 15.0006 3.75C15.1996 3.75 15.3905 3.82906 15.5312 3.96979C15.6719 4.11052 15.751 4.30139 15.751 4.50042C15.751 4.69944 15.6719 4.89031 15.5312 5.03104L8.5609 12.0004L15.5312 18.9698Z" fill="#0C2C48"/>
</svg>
				</a>
			<?php endif; ?>

				<div class="bt-delimiter"><a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="shop-page"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M9.75 3.75H5.25C4.85218 3.75 4.47064 3.90804 4.18934 4.18934C3.90804 4.47064 3.75 4.85218 3.75 5.25V9.75C3.75 10.1478 3.90804 10.5294 4.18934 10.8107C4.47064 11.092 4.85218 11.25 5.25 11.25H9.75C10.1478 11.25 10.5294 11.092 10.8107 10.8107C11.092 10.5294 11.25 10.1478 11.25 9.75V5.25C11.25 4.85218 11.092 4.47064 10.8107 4.18934C10.5294 3.90804 10.1478 3.75 9.75 3.75ZM9.75 9.75H5.25V5.25H9.75V9.75ZM18.75 3.75H14.25C13.8522 3.75 13.4706 3.90804 13.1893 4.18934C12.908 4.47064 12.75 4.85218 12.75 5.25V9.75C12.75 10.1478 12.908 10.5294 13.1893 10.8107C13.4706 11.092 13.8522 11.25 14.25 11.25H18.75C19.1478 11.25 19.5294 11.092 19.8107 10.8107C20.092 10.5294 20.25 10.1478 20.25 9.75V5.25C20.25 4.85218 20.092 4.47064 19.8107 4.18934C19.5294 3.90804 19.1478 3.75 18.75 3.75ZM18.75 9.75H14.25V5.25H18.75V9.75ZM9.75 12.75H5.25C4.85218 12.75 4.47064 12.908 4.18934 13.1893C3.90804 13.4706 3.75 13.8522 3.75 14.25V18.75C3.75 19.1478 3.90804 19.5294 4.18934 19.8107C4.47064 20.092 4.85218 20.25 5.25 20.25H9.75C10.1478 20.25 10.5294 20.092 10.8107 19.8107C11.092 19.5294 11.25 19.1478 11.25 18.75V14.25C11.25 13.8522 11.092 13.4706 10.8107 13.1893C10.5294 12.908 10.1478 12.75 9.75 12.75ZM9.75 18.75H5.25V14.25H9.75V18.75ZM18.75 12.75H14.25C13.8522 12.75 13.4706 12.908 13.1893 13.1893C12.908 13.4706 12.75 13.8522 12.75 14.25V18.75C12.75 19.1478 12.908 19.5294 13.1893 19.8107C13.4706 20.092 13.8522 20.25 14.25 20.25H18.75C19.1478 20.25 19.5294 20.092 19.8107 19.8107C20.092 19.5294 20.25 19.1478 20.25 18.75V14.25C20.25 13.8522 20.092 13.4706 19.8107 13.1893C19.5294 12.908 19.1478 12.75 18.75 12.75ZM18.75 18.75H14.25V14.25H18.75V18.75Z" fill="#0C2C48"/>
</svg></a></div>
			<?php if ($next_product): ?>
				<a href="<?php echo get_permalink($next_product->ID); ?>" class="next-product">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
  <path d="M17.031 12.531L9.53104 20.031C9.46136 20.1007 9.37863 20.156 9.28759 20.1937C9.19654 20.2314 9.09896 20.2508 9.00042 20.2508C8.90187 20.2508 8.80429 20.2314 8.71324 20.1937C8.6222 20.156 8.53947 20.1007 8.46979 20.031C8.40011 19.9614 8.34483 19.8786 8.30712 19.7876C8.26941 19.6965 8.25 19.599 8.25 19.5004C8.25 19.4019 8.26941 19.3043 8.30712 19.2132C8.34483 19.1222 8.40011 19.0395 8.46979 18.9698L15.4401 12.0004L8.46979 5.03104C8.32906 4.89031 8.25 4.69944 8.25 4.50042C8.25 4.30139 8.32906 4.11052 8.46979 3.96979C8.61052 3.82906 8.80139 3.75 9.00042 3.75C9.19944 3.75 9.39031 3.82906 9.53104 3.96979L17.031 11.4698C17.1008 11.5394 17.1561 11.6222 17.1938 11.7132C17.2316 11.8043 17.251 11.9019 17.251 12.0004C17.251 12.099 17.2316 12.1966 17.1938 12.2876C17.1561 12.3787 17.1008 12.4614 17.031 12.531Z" fill="#0C2C48"/>
</svg>
				</a>
			<?php endif; ?>
		</div>
		</div>
	</div>
</div>

<main id="bt_main" class="bt-site-main">
	<div class="bt-main-content">
		<div class="bt-main-product-ss">
			<div class="bt-container">

				<?php while (have_posts()): ?>
					<?php
                    the_post();
                    wc_get_template_part('content', 'single-product');
                    ?>

				<?php
endwhile;  // end of the loop.
?>

			</div>
		</div>
	</div>
</main>
<?php
get_footer('shop');

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
