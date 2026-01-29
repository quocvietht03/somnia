<?php

/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.0.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_mini_cart'); ?>

<?php if (WC()->cart && ! WC()->cart->is_empty()) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr($args['list_class']); ?>">
		<?php
		do_action('woocommerce_before_mini_cart_contents');

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
			$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

			if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {
				/**
				 * This filter is documented in woocommerce/templates/cart/cart.php.
				 *
				 * @since 2.1.0
				 */
				$product_name      = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
				$thumbnail         = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
				$product_price     = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
				$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
		?>
				<li class="woocommerce-mini-cart-item <?php echo esc_attr(apply_filters('woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key)); ?>">
					<?php
					echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s">&times;</a>',
							esc_url(wc_get_cart_remove_url($cart_item_key)),
							/* translators: %s is the product name */
							esc_attr(sprintf(__('Remove %s from cart', 'somnia'), wp_strip_all_tags($product_name))),
							esc_attr($product_id),
							esc_attr($cart_item_key),
							esc_attr($_product->get_sku()),
							/* translators: %s is the product name */
							esc_attr(sprintf(__('&ldquo;%s&rdquo; has been removed from your cart', 'somnia'), wp_strip_all_tags($product_name)))
						),
						$cart_item_key
					);
					?>
					<?php echo empty($product_permalink) ? $thumbnail : sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
					?>
					<div class="bt-cart-mini-infor">
						<h4><?php echo empty($product_permalink) ? wp_kses_post($product_name) : sprintf('<a href="%s">%s</a>', esc_url($product_permalink), wp_kses_post($product_name)); ?></h4>
						<?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
						?>
						<?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf('%s &times; %s', $cart_item['quantity'], $product_price) . '</span>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
						?>
					</div>
				</li>
		<?php
			}
		}

		do_action('woocommerce_mini_cart_contents');
		?>
	</ul>
	<div class="bt-bottom-mini-cart">
		<div class="bt-bottom-mini-cart-actions">
			<button type="button" class="bt-mini-cart-action-btn bt-mini-cart-note-btn" data-action="note">
				<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256">
					<path d="M229.66,58.34l-32-32a8,8,0,0,0-11.32,0l-96,96A8,8,0,0,0,88,128v32a8,8,0,0,0,8,8h32a8,8,0,0,0,5.66-2.34l96-96A8,8,0,0,0,229.66,58.34ZM124.69,152H104V131.31l64-64L188.69,88ZM200,76.69,179.31,56,192,43.31,212.69,64ZM224,128v80a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32h80a8,8,0,0,1,0,16H48V208H208V128a8,8,0,0,1,16,0Z"></path>
				</svg>
				<span><?php esc_html_e('Note', 'somnia'); ?></span>
			</button>
			<button type="button" class="bt-mini-cart-action-btn bt-mini-cart-coupon-btn" data-action="coupon">
				<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256">
					<path d="M243.31,136,144,36.69A15.86,15.86,0,0,0,132.69,32H40a8,8,0,0,0-8,8v92.69A15.86,15.86,0,0,0,36.69,144L136,243.31a16,16,0,0,0,22.63,0l84.68-84.68a16,16,0,0,0,0-22.63Zm-96,96L48,132.69V48h84.69L232,147.31ZM96,84A12,12,0,1,1,84,72,12,12,0,0,1,96,84Z"></path>
				</svg>
				<span><?php esc_html_e('Coupon', 'somnia'); ?></span>
			</button>
		</div>

		<!-- Note Popup -->
		<div class="bt-mini-cart-popup bt-mini-cart-note-popup" data-popup="note">
			<div class="bt-mini-cart-popup-header">
				<h4><?php esc_html_e('Note', 'somnia'); ?></h4>
				<button type="button" class="bt-mini-cart-popup-close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
						<path stroke="#181818" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
					</svg>
				</button>
			</div>
			<div class="bt-mini-cart-popup-content">
				<textarea id="bt-mini-cart-note-text" class="bt-mini-cart-note-textarea" placeholder="<?php esc_attr_e('Add a note to your order...', 'somnia'); ?>"></textarea>
			</div>
			<div class="bt-mini-cart-popup-footer">
				<button type="button" class="bt-mini-cart-popup-btn bt-mini-cart-popup-save"><?php esc_html_e('SAVE', 'somnia'); ?></button>
			</div>
		</div>

		<!-- Coupon Popup -->
		<div class="bt-mini-cart-popup bt-mini-cart-coupon-popup" data-popup="coupon">
			<div class="bt-mini-cart-popup-header">
				<h4><?php esc_html_e('Coupon', 'somnia'); ?></h4>
				<button type="button" class="bt-mini-cart-popup-close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
						<path stroke="#181818" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
					</svg>
				</button>
			</div>
			<div class="bt-mini-cart-popup-content">
				<div class="bt-mini-cart-coupon-form">
					<input type="text" id="bt-mini-cart-coupon-code" class="bt-mini-cart-coupon-input" placeholder="<?php esc_attr_e('Enter coupon code', 'somnia'); ?>" />
					<div class="bt-mini-cart-coupon-messages"></div>
					<?php if (WC()->cart->get_applied_coupons()) : ?>
						<div class="bt-mini-cart-applied-coupons">
							<p><?php esc_html_e('Applied coupons:', 'somnia'); ?></p>
							<ul>
								<?php foreach (WC()->cart->get_applied_coupons() as $coupon_code) : ?>
									<li>
										<span><?php echo esc_html($coupon_code); ?></span>
										<button type="button" class="bt-mini-cart-remove-coupon" data-coupon="<?php echo esc_attr($coupon_code); ?>">&times;</button>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="bt-mini-cart-popup-footer">
				<button type="button" class="bt-mini-cart-popup-btn bt-mini-cart-popup-apply"><?php esc_html_e('APPLY', 'somnia'); ?></button>
			</div>
		</div>
		<p class="woocommerce-mini-cart__total total">
			<?php
			/**
			 * Hook: woocommerce_widget_shopping_cart_total.
			 *
			 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
			 */
			do_action('woocommerce_cart_collaterals');
			?>
		</p>

		<?php do_action('woocommerce_widget_shopping_cart_before_buttons'); ?>

		<p class="woocommerce-mini-cart__buttons buttons"><?php do_action('woocommerce_widget_shopping_cart_buttons'); ?></p>

		<?php do_action('woocommerce_widget_shopping_cart_after_buttons'); ?>
		<?php
		if (function_exists('get_field')) {
			$site_infor = get_field('site_information', 'options');
		} else {
			$site_infor = array();
		}
		if (!empty($site_infor['payment_icons'])) { ?>
			<div class="bt-icon-payment--mini-cart">
				<?php foreach ($site_infor['payment_icons'] as $image) : 
					if (empty($image['url'])) continue;
					
					if (!empty($image['id'])) {
						echo wp_get_attachment_image($image['id'], 'thumbnail');
					} else {
						echo '<img src="' . esc_url('#') . '" alt="' . esc_html__('Awaiting payment icon', 'somnia') . '">';
					}
				endforeach; ?>
			</div>
		<?php }
		
		if (!empty($site_infor['commitment'])) { ?>
			<div class="bt-mini-cart-commitment">
				<p><?php echo esc_html($site_infor['commitment']); ?></p>
			</div>
		<?php } ?>
	</div>
<?php else : ?>
	<div class="bt-cart-empty">
		<svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M6.01 16.136L4.141 4H3a1 1 0 0 1 0-2h1.985a.993.993 0 0 1 .66.235.997.997 0 0 1 .346.627L6.319 5H14v2H6.627l1.23 8h9.399l1.5-5h2.088l-1.886 6.287A1 1 0 0 1 18 17H7.016a.993.993 0 0 1-.675-.248.999.999 0 0 1-.332-.616zM10 20a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm9 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0-18a1 1 0 0 1 1 1v1h1a1 1 0 1 1 0 2h-1v1a1 1 0 1 1-2 0V6h-1a1 1 0 1 1 0-2h1V3a1 1 0 0 1 1-1z" fill="#212121"></path>
		</svg>
		<p class="woocommerce-mini-cart__empty-message"><?php esc_html_e('No products in the cart.', 'somnia'); ?></p>
	</div>
<?php endif; ?>

<?php do_action('woocommerce_after_mini_cart'); ?>