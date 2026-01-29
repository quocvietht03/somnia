<?php

/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.6.0
 */

defined('ABSPATH') || exit;

global $product;

$attribute_keys  = array_keys($attributes);
$variations_json = wp_json_encode($available_variations);
$variations_attr = function_exists('wc_esc_json') ? wc_esc_json($variations_json) : _wp_specialchars($variations_json, ENT_QUOTES, 'UTF-8', true);


do_action('woocommerce_before_add_to_cart_form'); ?>
<form class="variations_form cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo esc_attr(absint($product->get_id())); ?>" <?php echo 'data-product_variations="' . $variations_attr . '"'; ?>>
	<?php do_action('woocommerce_before_variations_form'); ?>

	<?php if (empty($available_variations) && false !== $available_variations) : ?>
		<p class="stock out-of-stock"><?php echo esc_html(apply_filters('woocommerce_out_of_stock_message', __('This product is currently out of stock and unavailable.', 'somnia'))); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0" role="presentation">
			<tbody>
				<?php foreach ($attributes as $attribute_name => $options) : 
					
					?>
					<tr>
						<th class="label"><label for="<?php echo esc_attr(sanitize_title($attribute_name)); ?>"><?php echo wc_attribute_label($attribute_name); // WPCS: XSS ok. 
																												?></label></th>
						<td class="value">
							<?php
							wc_dropdown_variation_attribute_options(
								array(
									'options'   => $options,
									'attribute' => $attribute_name,
									'product'   => $product,
								)
							);
							/**
							 * Filters the reset variation button.
							 *
							 * @since 2.5.0
							 *
							 * @param string  $button The reset variation button HTML.
							 */
							echo end($attribute_keys) === $attribute_name ? wp_kses_post(apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#" aria-label="' . esc_attr__('Clear options', 'somnia') . '">' . esc_html__('Clear', 'somnia') . '</a>')) : '';
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="bt-attributes-wrap">
			<?php
			// Helper function to check if an option is available based on selected attributes
			$check_option_availability = function ($option_value, $current_attribute_name, $all_attributes, $selected_attributes, $available_variations) {
				// Build test attributes array - exclude current attribute and add the test option
				$test_attributes = $selected_attributes;
				unset($test_attributes[$current_attribute_name]); // Remove current attribute from test
				$test_attributes[$current_attribute_name] = $option_value; // Add the option we're testing

				// Get the attribute key format used in variations
				$current_attr_key = 'attribute_' . sanitize_title($current_attribute_name);

				// Check if any variation matches these attributes
				foreach ($available_variations as $variation) {
					if (!isset($variation['attributes'])) {
						continue;
					}

					$variation_attrs = $variation['attributes'];
					$matches = true;

					// First, check that the current attribute matches (if specified in variation)
					$variation_current_value = isset($variation_attrs[$current_attr_key]) ? $variation_attrs[$current_attr_key] : '';
					// Variation can have empty value (any) or must match exactly
					if ($variation_current_value !== '' && $variation_current_value !== $option_value) {
						continue; // Skip this variation, doesn't match current attribute
					}

					// Then check each selected attribute (excluding current)
					foreach ($test_attributes as $attr_name => $test_value) {
						if ($attr_name === $current_attribute_name || $test_value === '') {
							continue; // Skip current attribute and empty values
						}

						$attr_key = 'attribute_' . sanitize_title($attr_name);
						$variation_value = isset($variation_attrs[$attr_key]) ? $variation_attrs[$attr_key] : '';

						// Variation can have empty value (any), or must match exactly
						if ($variation_value !== '' && $variation_value !== $test_value) {
							$matches = false;
							break;
						}
					}

					if ($matches) {
						return true; // Found at least one matching variation
					}
				}

				return false; // No matching variation found
			};

			// Build selected attributes array from defaults
			$selected_attributes = array();
			foreach ($attributes as $attr_name => $attr_options) {
				$attr_slug = sanitize_title($attr_name);
				$selected_attr = isset($_REQUEST['attribute_' . $attr_slug])
					? wc_clean(wp_unslash($_REQUEST['attribute_' . $attr_slug]))
					: $product->get_variation_default_attribute($attr_name);

				if ($selected_attr) {
					$selected_attributes[$attr_name] = $selected_attr;
				}
			}

			foreach ($attributes as $attribute_name => $options) :
				$data_attribute = strtolower($attribute_name);
				$data_attribute_slug = sanitize_title($attribute_name);
				$selected_value = isset($_REQUEST['attribute_' . $data_attribute_slug]) ? wc_clean(wp_unslash($_REQUEST['attribute_' . $data_attribute_slug])) : $product->get_variation_default_attribute($attribute_name);

				// Check if this is size attribute for Size Guide button
				$is_size_attr = (strpos(strtolower($attribute_name), 'size') !== false);

				// Check if this is color or image attribute and add dynamic class
				$color_taxonomy = function_exists('somnia_get_color_taxonomy') ? somnia_get_color_taxonomy() : false;
				$image_taxonomy = function_exists('somnia_get_image_taxonomy') ? somnia_get_image_taxonomy() : false;

				// Prioritize image over color
				$is_image_attr = $image_taxonomy && ($attribute_name === $image_taxonomy);
				$is_color_attr = !$is_image_attr && $color_taxonomy && ($attribute_name === $color_taxonomy);

				$attr_class = $is_image_attr ? ' bt-is-image-attribute' : ($is_color_attr ? ' bt-is-color-attribute' : '');
			?>
				<div class="bt-attributes--item<?php echo esc_attr($attr_class); ?>" data-attribute-name="<?php echo esc_attr($data_attribute_slug); ?>">
					<div class="bt-attributes--name">
						<div class="bt-name"><?php echo wc_attribute_label($attribute_name) . ':'; ?></div>
						<div class="bt-result"></div>
						<?php
						// Display Size Guide button inline with Size label - only on single product pages
						if ($is_size_attr && is_product()) {
							// Check if size guide is enabled for this product
							$enable_size_guide = get_post_meta($product->get_id(), '_enable_size_guide', true);
							$size_guide = get_field('size_guide', 'option');

							if ($enable_size_guide === 'yes' && !empty($size_guide)) {
						?>
								<div class="bt-size-guide-wrapper bt-inline-position">
									<a href="#bt-size-guide-popup" class="bt-size-guide-button bt-js-open-popup-link">
										<?php echo esc_html__('Size Guide', 'somnia'); ?>
									</a>
								</div>
						<?php
							}
						}
						?>
					</div>
					<?php
					// Check if this is image attribute
					// Get ordered options - same logic as wc_dropdown_variation_attribute_options
					$ordered_options = $options;
					if ($product && taxonomy_exists($attribute_name)) {
						$terms = wc_get_product_terms(
							$product->get_id(),
							$attribute_name,
							array('fields' => 'all')
						);
						$ordered_options = array();
						foreach ($terms as $term) {
							if (in_array($term->slug, $options, true)) {
								$ordered_options[] = $term->slug;
							}
						}
					}
				
					if ($is_image_attr) { ?>
						<div class="bt-attributes--value bt-value-image">
							<?php
							foreach ($ordered_options as $option) :
								$term = get_term_by('slug', $option, $attribute_name);
								$term_id = $term ? $term->term_id : '';
								$image_data = $term_id ? get_field('image_tax_attributes', $attribute_name . '_' . $term_id) : '';

								// Get image ID from ACF field
								$image_id = 0;
								if ($image_data) {
									if (is_array($image_data) && isset($image_data['ID'])) {
										$image_id = $image_data['ID'];
									} elseif (is_numeric($image_data)) {
										$image_id = $image_data;
									}
								}

								// Get image URL
								$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
								$is_selected = ($selected_value === $option);
								$class_active = $is_selected ? ' active' : '';

								// Check if option is available based on selected attributes
								$is_available = $check_option_availability($option, $attribute_name, $attributes, $selected_attributes, $available_variations);
								$class_disabled = !$is_available ? ' disabled' : '';
							?>
								<div class="bt-js-item bt-item-image<?php echo esc_attr($class_active . $class_disabled); ?>" data-value="<?php echo esc_attr($option); ?>">
									<div class="bt-image">
										<?php if ($image_url) : ?>
											<span style="background-image: url('<?php echo esc_url($image_url); ?>');">
												<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
													<path d="M16 3C13.4288 3 10.9154 3.76244 8.77759 5.19089C6.63975 6.61935 4.97351 8.64968 3.98957 11.0251C3.00563 13.4006 2.74819 16.0144 3.2498 18.5362C3.75141 21.0579 4.98953 23.3743 6.80762 25.1924C8.6257 27.0105 10.9421 28.2486 13.4638 28.7502C15.9856 29.2518 18.5994 28.9944 20.9749 28.0104C23.3503 27.0265 25.3807 25.3603 26.8091 23.2224C28.2376 21.0846 29 18.5712 29 16C28.9964 12.5533 27.6256 9.24882 25.1884 6.81163C22.7512 4.37445 19.4467 3.00364 16 3ZM21.7075 13.7075L14.7075 20.7075C14.6146 20.8005 14.5043 20.8742 14.3829 20.9246C14.2615 20.9749 14.1314 21.0008 14 21.0008C13.8686 21.0008 13.7385 20.9749 13.6171 20.9246C13.4957 20.8742 13.3854 20.8005 13.2925 20.7075L10.2925 17.7075C10.1049 17.5199 9.99945 17.2654 9.99945 17C9.99945 16.7346 10.1049 16.4801 10.2925 16.2925C10.4801 16.1049 10.7346 15.9994 11 15.9994C11.2654 15.9994 11.5199 16.1049 11.7075 16.2925L14 18.5862L20.2925 12.2925C20.3854 12.1996 20.4957 12.1259 20.6171 12.0756C20.7385 12.0253 20.8686 11.9994 21 11.9994C21.1314 11.9994 21.2615 12.0253 21.3829 12.0756C21.5043 12.1259 21.6146 12.1996 21.7075 12.2925C21.8004 12.3854 21.8741 12.4957 21.9244 12.6171C21.9747 12.7385 22.0006 12.8686 22.0006 13C22.0006 13.1314 21.9747 13.2615 21.9244 13.3829C21.8741 13.5043 21.8004 13.6146 21.7075 13.7075Z" fill="white" />
												</svg>
											</span>
										<?php else : ?>
											<span style="background-color: #e5e7eb;">
												<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
													<path d="M16 3C13.4288 3 10.9154 3.76244 8.77759 5.19089C6.63975 6.61935 4.97351 8.64968 3.98957 11.0251C3.00563 13.4006 2.74819 16.0144 3.2498 18.5362C3.75141 21.0579 4.98953 23.3743 6.80762 25.1924C8.6257 27.0105 10.9421 28.2486 13.4638 28.7502C15.9856 29.2518 18.5994 28.9944 20.9749 28.0104C23.3503 27.0265 25.3807 25.3603 26.8091 23.2224C28.2376 21.0846 29 18.5712 29 16C28.9964 12.5533 27.6256 9.24882 25.1884 6.81163C22.7512 4.37445 19.4467 3.00364 16 3ZM21.7075 13.7075L14.7075 20.7075C14.6146 20.8005 14.5043 20.8742 14.3829 20.9246C14.2615 20.9749 14.1314 21.0008 14 21.0008C13.8686 21.0008 13.7385 20.9749 13.6171 20.9246C13.4957 20.8742 13.3854 20.8005 13.2925 20.7075L10.2925 17.7075C10.1049 17.5199 9.99945 17.2654 9.99945 17C9.99945 16.7346 10.1049 16.4801 10.2925 16.2925C10.4801 16.1049 10.7346 15.9994 11 15.9994C11.2654 15.9994 11.5199 16.1049 11.7075 16.2925L14 18.5862L20.2925 12.2925C20.3854 12.1996 20.4957 12.1259 20.6171 12.0756C20.7385 12.0253 20.8686 11.9994 21 11.9994C21.1314 11.9994 21.2615 12.0253 21.3829 12.0756C21.5043 12.1259 21.6146 12.1996 21.7075 12.2925C21.8004 12.3854 21.8741 12.4957 21.9244 12.6171C21.9747 12.7385 22.0006 12.8686 22.0006 13C22.0006 13.1314 21.9747 13.2615 21.9244 13.3829C21.8741 13.5043 21.8004 13.6146 21.7075 13.7075Z" fill="white" />
												</svg>
											</span>
										<?php endif; ?>
									</div>
									<label><?php echo esc_html($term ? $term->name : $option); ?></label>
								</div>
							<?php endforeach; ?>
						</div>
					<?php } elseif ($is_color_attr) { ?>
						<div class="bt-attributes--value bt-value-color">
							<?php
							foreach ($ordered_options as $option) :
								$term = get_term_by('slug', $option, $attribute_name);
								$term_id = $term ? $term->term_id : '';
								$color = $term_id ? get_field('color_tax_attributes', $attribute_name . '_' . $term_id) : '';
								if (!$color) {
									$color = $option;
								}
								$is_selected = ($selected_value === $option);
								$class_active = $is_selected ? ' active' : '';

								// Check if option is available based on selected attributes
								$is_available = $check_option_availability($option, $attribute_name, $attributes, $selected_attributes, $available_variations);
								$class_disabled = !$is_available ? ' disabled' : '';
							?>
								<div class="bt-js-item bt-item-color<?php echo esc_attr($class_active . $class_disabled); ?>" data-value="<?php echo esc_attr($option); ?>">
									<div class="bt-color">
										<span style="background-color: <?php echo esc_attr($color); ?>;">
											<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
												<path d="M16 3C13.4288 3 10.9154 3.76244 8.77759 5.19089C6.63975 6.61935 4.97351 8.64968 3.98957 11.0251C3.00563 13.4006 2.74819 16.0144 3.2498 18.5362C3.75141 21.0579 4.98953 23.3743 6.80762 25.1924C8.6257 27.0105 10.9421 28.2486 13.4638 28.7502C15.9856 29.2518 18.5994 28.9944 20.9749 28.0104C23.3503 27.0265 25.3807 25.3603 26.8091 23.2224C28.2376 21.0846 29 18.5712 29 16C28.9964 12.5533 27.6256 9.24882 25.1884 6.81163C22.7512 4.37445 19.4467 3.00364 16 3ZM21.7075 13.7075L14.7075 20.7075C14.6146 20.8005 14.5043 20.8742 14.3829 20.9246C14.2615 20.9749 14.1314 21.0008 14 21.0008C13.8686 21.0008 13.7385 20.9749 13.6171 20.9246C13.4957 20.8742 13.3854 20.8005 13.2925 20.7075L10.2925 17.7075C10.1049 17.5199 9.99945 17.2654 9.99945 17C9.99945 16.7346 10.1049 16.4801 10.2925 16.2925C10.4801 16.1049 10.7346 15.9994 11 15.9994C11.2654 15.9994 11.5199 16.1049 11.7075 16.2925L14 18.5862L20.2925 12.2925C20.3854 12.1996 20.4957 12.1259 20.6171 12.0756C20.7385 12.0253 20.8686 11.9994 21 11.9994C21.1314 11.9994 21.2615 12.0253 21.3829 12.0756C21.5043 12.1259 21.6146 12.1996 21.7075 12.2925C21.8004 12.3854 21.8741 12.4957 21.9244 12.6171C21.9747 12.7385 22.0006 12.8686 22.0006 13C22.0006 13.1314 21.9747 13.2615 21.9244 13.3829C21.8741 13.5043 21.8004 13.6146 21.7075 13.7075Z" fill="white" />
											</svg>
										</span>
									</div>
									<label><?php echo esc_html($term ? $term->name : $option); ?></label>
								</div>
							<?php endforeach; ?>
						</div>
					<?php } else { ?>
						<div class="bt-attributes--value">
							<?php foreach ($ordered_options as $option) : ?>
								<?php
								$term = get_term_by('slug', $option, $attribute_name);
								$display_name = $term ? $term->name : $option;
								$display_desc = $term ? $term->description : '';
								$is_selected = ($selected_value === $option);
								$class_active = $is_selected ? ' active' : '';
								
								// Check if option is available based on selected attributes
								$is_available = $check_option_availability($option, $attribute_name, $attributes, $selected_attributes, $available_variations);
								$class_disabled = !$is_available ? ' disabled' : '';
								?>
								<span class="bt-js-item bt-item-value<?php echo esc_attr($class_active . $class_disabled); ?>" data-value="<?php echo esc_attr($option); ?>">
									<?php 
										echo esc_html($display_name); 
										if(!empty($display_desc)) echo '<br>' . esc_html($display_desc);
									?>
								</span>
							<?php endforeach; ?>
						</div>
					<?php } ?>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="reset_variations_alert screen-reader-text" role="alert" aria-live="polite" aria-relevant="all"></div>
		<?php do_action('woocommerce_after_variations_table'); ?>
		<div class="single_variation_wrap">
			<?php
			/**
			 * Hook: woocommerce_before_single_variation.
			 */
			do_action('woocommerce_before_single_variation');

			/**
			 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
			 *
			 * @since 2.4.0
			 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
			 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
			 */
			do_action('woocommerce_single_variation');

			/**
			 * Hook: woocommerce_after_single_variation.
			 */
			do_action('woocommerce_after_single_variation');

			?>
		</div>
	<?php endif; ?>

	<?php do_action('woocommerce_after_variations_form'); ?>
</form>

<?php do_action('somnia_woocommerce_template_single_out_of_stock'); ?>

<?php
do_action('woocommerce_after_add_to_cart_form');
