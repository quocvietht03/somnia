<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 */

defined('ABSPATH') || exit;
session_start();
$coupon = '';
if (isset($_SESSION['coupon'])) {
	$coupon = $_SESSION['coupon'];
}
do_action('woocommerce_before_cart');

?>
<div class="bt-cart-content" data-coupon="<?php echo esc_attr($coupon); ?>">
	<?php
	if (function_exists('get_field')) {
		$time_promotion = get_field('time_promotion', 'option');
	} else {
		$time_promotion = '';
	}
	$free_shipping_threshold = somnia_get_free_shipping_minimum_amount();
	$cart_total = WC()->cart->get_cart_contents_total();
	$currency_symbol = get_woocommerce_currency_symbol();
	if ($cart_total < $free_shipping_threshold) {
		$amount_left = $free_shipping_threshold - $cart_total;

		$percentage = ($cart_total / $free_shipping_threshold) * 100;
		$message = sprintf(
			__('<p class="bt-buy-more">Buy <span>%1$s%2$.2f</span> more to get <span>Free Shipping</span></p>', 'somnia'),
			$currency_symbol,
			$amount_left
		);
	} else {
		$percentage = 100;
		$message = __('<p class="bt-congratulation"> Congratulations! You have free shipping!</p>', 'somnia');
	}
	if ($time_promotion && $time_promotion['promotion'] === true) {
		$promotion_time = $time_promotion['time'];
		if (!empty($promotion_time)) {
			echo '<div class="bt-time-promotion">' . sprintf(
				__('Your cart will expire in <span id="countdown" data-time="%s">%s</span> minutes! Please checkout now before your items sell out!', 'somnia'),
				$promotion_time,
				$promotion_time
			) . '</div>';
		}
	}
	if ($free_shipping_threshold > 0) {
	?>
		<div class="bt-progress-content">
			<?php echo '<div id="bt-free-shipping-message">' . $message . '</div>'; ?>
			<div class="bt-progress-container-cart">
				<div class="bt-progress-bar" data-width="<?php echo esc_attr($percentage) ?>">
					<div class="bt-icon-shipping">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
							<g clip-path="url(#clip0_2134_37062)">
								<path d="M14.375 6.25H17.7016C17.8261 6.24994 17.9478 6.28709 18.0511 6.35669C18.1544 6.42629 18.2345 6.52517 18.2812 6.64063L19.375 9.375" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M1.875 11.25H14.375" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M15 16.875C16.0355 16.875 16.875 16.0355 16.875 15C16.875 13.9645 16.0355 13.125 15 13.125C13.9645 13.125 13.125 13.9645 13.125 15C13.125 16.0355 13.9645 16.875 15 16.875Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M6.25 16.875C7.28553 16.875 8.125 16.0355 8.125 15C8.125 13.9645 7.28553 13.125 6.25 13.125C5.21447 13.125 4.375 13.9645 4.375 15C4.375 16.0355 5.21447 16.875 6.25 16.875Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M13.125 15H8.125" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M14.375 9.375H19.375V14.375C19.375 14.5408 19.3092 14.6997 19.1919 14.8169C19.0747 14.9342 18.9158 15 18.75 15H16.875" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M4.375 15H2.5C2.33424 15 2.17527 14.9342 2.05806 14.8169C1.94085 14.6997 1.875 14.5408 1.875 14.375V5.625C1.875 5.45924 1.94085 5.30027 2.05806 5.18306C2.17527 5.06585 2.33424 5 2.5 5H14.375V13.232" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</g>
							<defs>
								<clipPath id="clip0_2134_37062">
									<rect width="20" height="20" fill="white" />
								</clipPath>
							</defs>
						</svg>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
	<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
		<?php do_action('woocommerce_before_cart_table'); ?>

		<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
			<thead>
				<tr>

					<th class="product-thumbnail"><?php esc_html_e('Products', 'somnia'); ?></th>
					<th class="product-name"></th>
					<th class="product-price"><?php esc_html_e('Price', 'somnia'); ?></th>
					<th class="product-quantity"><?php esc_html_e('Quantity', 'somnia'); ?></th>
					<th class="product-subtotal"><?php esc_html_e('Total Price', 'somnia'); ?></th>
					<th class="product-remove"><span class="screen-reader-text"><?php esc_html_e('Remove item', 'somnia'); ?></span></th>
				</tr>
			</thead>
			<tbody>
				<?php do_action('woocommerce_before_cart_contents'); ?>

				<?php
				foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
					$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
					$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
					$product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

					if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
						$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
				?>
						<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
							<td class="product-thumbnail">
								<?php
								$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

								if (! $product_permalink) {
									echo wp_kses_post($thumbnail);
								} else {
									printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
								}
								?>
							</td>

							<td class="product-name" data-title="<?php esc_attr_e('Product', 'somnia'); ?>">
								<?php
								if (! $product_permalink) {
									echo wp_kses_post($product_name . '&nbsp;');
								} else {
									/**
									 * This filter is documented above.
									 *
									 * @since 2.1.0
									 */
									echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
								}

								do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

								// Meta data.
								echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

								// Backorder notification.
								if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
									echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'somnia') . '</p>', $product_id));
								}
								?>
							</td>

							<td class="product-price" data-title="<?php esc_attr_e('Price', 'somnia'); ?>">
								<?php
								echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
								?>
							</td>

							<td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'somnia'); ?>">
								<?php
								if ($_product->is_sold_individually()) {
									$min_quantity = 1;
									$max_quantity = 1;
								} else {
									$min_quantity = 0;
									$max_quantity = $_product->get_max_purchase_quantity();
								}

								$product_quantity = woocommerce_quantity_input(
									array(
										'input_name'   => "cart[{$cart_item_key}][qty]",
										'input_value'  => $cart_item['quantity'],
										'max_value'    => $max_quantity,
										'min_value'    => $min_quantity,
										'product_name' => $product_name,
									),
									$_product,
									false
								);

								echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
								?>
							</td>

							<td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'somnia'); ?>">
								<?php
								echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
								?>
							</td>
							<td class="product-remove">
								<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 8 8" fill="none">
  <path d="M4.70591 4L7.84759 0.858326C7.89527 0.812273 7.9333 0.757185 7.95947 0.696277C7.98563 0.635369 7.99941 0.569859 7.99998 0.503571C8.00056 0.437283 7.98793 0.371545 7.96282 0.310191C7.93772 0.248837 7.90065 0.193096 7.85378 0.146222C7.8069 0.0993477 7.75116 0.0622781 7.68981 0.0371762C7.62845 0.0120743 7.56272 -0.000557174 7.49643 1.88494e-05C7.43014 0.000594873 7.36463 0.0143669 7.30372 0.0405312C7.24281 0.0666955 7.18773 0.104728 7.14167 0.15241L4 3.29409L0.858326 0.15241C0.764169 0.0614706 0.638062 0.0111508 0.507164 0.0122882C0.376267 0.0134257 0.251053 0.0659295 0.158491 0.158491C0.0659295 0.251053 0.0134257 0.376267 0.0122882 0.507165C0.0111508 0.638062 0.0614706 0.764169 0.15241 0.858326L3.29408 4L0.15241 7.14167C0.104728 7.18773 0.0666955 7.24282 0.0405312 7.30372C0.0143669 7.36463 0.000594873 7.43014 1.88494e-05 7.49643C-0.000557174 7.56272 0.0120743 7.62846 0.0371762 7.68981C0.0622781 7.75116 0.0993477 7.8069 0.146222 7.85378C0.193096 7.90065 0.248837 7.93772 0.310191 7.96283C0.371545 7.98793 0.437283 8.00056 0.503571 7.99998C0.569859 7.99941 0.635368 7.98563 0.696277 7.95947C0.757185 7.93331 0.812273 7.89527 0.858326 7.84759L4 4.70592L7.14167 7.84759C7.2113 7.91788 7.30028 7.96585 7.39728 7.98538C7.49427 8.00491 7.59488 7.99512 7.68629 7.95727C7.7777 7.91941 7.85576 7.85519 7.91054 7.7728C7.96532 7.69041 7.99433 7.59357 7.99387 7.49463C7.99388 7.42907 7.98097 7.36416 7.95587 7.30359C7.93077 7.24303 7.89397 7.18801 7.84759 7.14167L4.70591 4Z" fill="#F03E3E"/>
</svg></a>',
										esc_url(wc_get_cart_remove_url($cart_item_key)),
										/* translators: %s is the product name */
										esc_attr(sprintf(__('Remove %s from cart', 'somnia'), wp_strip_all_tags($product_name))),
										esc_attr($product_id),
										esc_attr($_product->get_sku())
									),
									$cart_item_key
								);
								?>
							</td>
						</tr>
				<?php
					}
				}
				?>

				<?php do_action('woocommerce_cart_contents'); ?>

				<tr>
					<td colspan="6" class="actions">

						<?php if (wc_coupons_enabled()) { ?>
							<div class="coupon">
								<label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'somnia'); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Add voucher discount', 'somnia'); ?>" /> <button type="submit" class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="apply_coupon" value="<?php esc_attr_e('Apply Code', 'somnia'); ?>"><?php esc_html_e('Apply Code', 'somnia'); ?></button>
								<?php do_action('woocommerce_cart_coupon'); ?>
							</div>
						<?php } ?>

						<button type="submit" class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="update_cart" value="<?php esc_attr_e('Update cart', 'somnia'); ?>"><?php esc_html_e('Update cart', 'somnia'); ?></button>

						<?php do_action('woocommerce_cart_actions'); ?>

						<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
					</td>
				</tr>

				<?php do_action('woocommerce_after_cart_contents'); ?>
			</tbody>
		</table>
		<?php do_action('woocommerce_after_cart_table'); ?>
	</form>
</div>
<?php do_action('woocommerce_before_cart_collaterals'); ?>


<div class="cart-collaterals">
	<?php
	/**
	 * Cart collaterals hook.

	 * @hooked woocommerce_cart_totals - 10
	 */
	do_action('woocommerce_cart_collaterals');
	?>
</div>
<?php
/* @hooked woocommerce_cross_sell_display */
do_action('somnia_woocommerce_template_cross_sell');
do_action('woocommerce_after_cart'); ?>