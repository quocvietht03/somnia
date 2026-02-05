<?php
/* Register Sidebar */
if (!function_exists('somnia_register_sidebar')) {
	function somnia_register_sidebar()
	{
		register_sidebar(array(
			'name' => esc_html__('Main Sidebar', 'somnia'),
			'id' => 'main-sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="wg-title">',
			'after_title' => '</h4>',
		));
	}
	add_action('widgets_init', 'somnia_register_sidebar');
}

/* Add Support Upload Image Type SVG and 3D Models */
function somnia_mime_types($mimes)
{
	$mimes['svg'] = 'image/svg+xml';
	$mimes['glb'] = 'model/gltf-binary';
	$mimes['gltf'] = 'model/gltf+json';
	return $mimes;
}
add_filter('upload_mimes', 'somnia_mime_types');

/* Fix WordPress check filetype for GLB files */
function somnia_fix_mime_type_glb($data, $file, $filename, $mimes, $real_mime)
{
	if (!empty($data['ext']) && !empty($data['type'])) {
		return $data;
	}

	$wp_file_type = wp_check_filetype($filename, $mimes);

	// Check for GLB files
	if ($wp_file_type['ext'] === 'glb') {
		$data['ext'] = 'glb';
		$data['type'] = 'model/gltf-binary';
	}

	// Check for GLTF files
	if ($wp_file_type['ext'] === 'gltf') {
		$data['ext'] = 'gltf';
		$data['type'] = 'model/gltf+json';
	}

	return $data;
}
add_filter('wp_check_filetype_and_ext', 'somnia_fix_mime_type_glb', 10, 5);

/* Get icon SVG HTML */
function somnia_get_icon_svg_html($icon_file_name)
{
	if (empty($icon_file_name)) {
		return 'Error: Invalid file name or file name is missing.';
	}

	$icon_file_name = sanitize_file_name($icon_file_name);
	$file_path = get_template_directory() . '/assets/images/' . $icon_file_name . '.svg';

	if (! file_exists($file_path)) {
		return 'Error: File does not exist.';
	}

	$svg = file_get_contents($file_path);

	if (false === $svg) {
		return 'Error: Unable to read file.';
	}

	return $svg;
}

/* Enqueue Script */
if (!function_exists('somnia_enqueue_scripts')) {
	function somnia_enqueue_scripts()
	{
		wp_enqueue_style('somnia-fonts', get_template_directory_uri() . '/assets/css/fonts.css',  array(), false);

		if (is_archive('product')) {
			wp_enqueue_script('nouislider-script', get_template_directory_uri() . '/assets/libs/nouislider/nouislider.min.js', array('jquery'), '', true);
			wp_enqueue_style('nouislider-style', get_template_directory_uri() . '/assets/libs/nouislider/nouislider.min.css', array(), false);
		}
		if (class_exists('WooCommerce')) {
			wp_enqueue_script('wc-cart-fragments');
			wp_enqueue_script('wc-add-to-cart-variation');
			wp_enqueue_script('swiper-slider', get_template_directory_uri() . '/assets/libs/swiper/swiper.min.js', array('jquery'), '', true);
			wp_enqueue_style('swiper-slider', get_template_directory_uri() . '/assets/libs/swiper/swiper.min.css', array(), false);
			wp_enqueue_script('zoomable', get_template_directory_uri() . '/assets/libs/zoomable.js', array('jquery'), '', true);
			wp_enqueue_script('magnific-popup', get_template_directory_uri() . '/assets/libs/magnific-popup/jquery.magnific-popup.js', array('jquery'), '', true);
			wp_enqueue_style('magnific-popup', get_template_directory_uri() . '/assets/libs/magnific-popup/magnific-popup.css', array(), false);
		}

		if ((is_singular('post') && comments_open()) || (is_page() && !is_page_template()) || is_singular('product')) {
			wp_enqueue_script('jquery-validate', get_template_directory_uri() . '/assets/libs/jquery-validate/jquery.validate.min.js', array('jquery'), '', true);
		}
		wp_enqueue_script('select2', get_template_directory_uri() . '/assets/libs/select2/select2.min.js', array('jquery'), '', true);
		wp_enqueue_style('select2', get_template_directory_uri() . '/assets/libs/select2/select2.min.css', array(), false);

		wp_enqueue_style('somnia-main', get_template_directory_uri() . '/assets/css/main.css',  array(), false);
		wp_enqueue_style('somnia-style', get_template_directory_uri() . '/style.css',  array(), false);
		wp_enqueue_script('somnia-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '', true);
		if (function_exists('get_field')) {
			$dev_mode = get_field('dev_mode', 'options');
			/* Load custom style */
			$custom_style = '';

			$custom_style = get_field('custom_css_code', 'options');
			if ($dev_mode && !empty($custom_style)) {
				wp_add_inline_style('somnia-style', $custom_style);
			}

			/* Custom script */
			$custom_script = '';
			$custom_script = get_field('custom_js_code', 'options');
			if ($dev_mode && !empty($custom_script)) {
				wp_add_inline_script('somnia-main', $custom_script);
			}
		}
		// Get cart URL
		$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '';
		// Get shop URL
		$shop_url = home_url('/shop/');
		if (function_exists('wc_get_page_id')) {
			$shop_page_id = wc_get_page_id('shop');
			if ($shop_page_id > 0) {
				$shop_url = get_permalink($shop_page_id);
			}
		}
		$wishlist_toast = $compare_toast = $cart_toast = '';
		$show_cart_mini = '';
		$wishlist_toast_time = 3000;
		$compare_toast_time = 3000;
		$cart_toast_time = 3000;
		if (function_exists('get_field')) {
			$archive_shop = get_field('archive_shop', 'options');

			if (is_array($archive_shop)) {
				$wishlist_toast = isset($archive_shop['wishlist_toast']) ? $archive_shop['wishlist_toast'] : '';
				$wishlist_toast_time = isset($archive_shop['time_show_wishlist']) ? $archive_shop['time_show_wishlist'] : 3000;
				$compare_toast = isset($archive_shop['compare_toast']) ? $archive_shop['compare_toast'] : '';
				$compare_toast_time = isset($archive_shop['time_show_compare']) ? $archive_shop['time_show_compare'] : 3000;
				$cart_toast = isset($archive_shop['cart_toast']) ? $archive_shop['cart_toast'] : '';
				$cart_toast_time = isset($archive_shop['time_show_cart']) ? $archive_shop['time_show_cart'] : 3000;
				$show_cart_mini = isset($archive_shop['show_cart_mini']) ? $archive_shop['show_cart_mini'] : '';
			}
		}
		/* Options to script */
		$js_options = array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'shop' => $shop_url,
			'cart' => $cart_url,
			'wishlist_toast' => $wishlist_toast,
			'compare_toast' => $compare_toast,
			'cart_toast' => $cart_toast,
			'show_cart_mini' => $show_cart_mini,
			'wishlist_toast_time' => $wishlist_toast_time,
			'compare_toast_time' => $compare_toast_time,
			'cart_toast_time' => $cart_toast_time,
			'user_info' => wp_get_current_user(),
			'order_tracking_nonce' => wp_create_nonce('somnia_order_tracking_nonce'),
			'color_taxonomy' => function_exists('somnia_get_color_taxonomy') ? somnia_get_color_taxonomy() : 'pa_color',
		);

		wp_localize_script('somnia-main', 'AJ_Options', $js_options);

		// Create wc_cart_params object without enqueuing wc-cart script
		if (class_exists('WooCommerce') && class_exists('WC_AJAX')) {
			$wc_cart_params = array(
				'ajax_url'                     => WC()->ajax_url(),
				'wc_ajax_url'                  => WC_AJAX::get_endpoint('%%endpoint%%'),
				'update_shipping_method_nonce' => wp_create_nonce('update-shipping-method'),
				'apply_coupon_nonce'           => wp_create_nonce('apply-coupon'),
				'remove_coupon_nonce'          => wp_create_nonce('remove-coupon'),
			);
			wp_localize_script('somnia-main', 'wc_cart_params', $wc_cart_params);
		}

		wp_enqueue_script('somnia-main');
	}
	add_action('wp_enqueue_scripts', 'somnia_enqueue_scripts');
}
/* Add Stylesheet And Script Backend */
if (!function_exists('somnia_enqueue_admin_scripts')) {
	function somnia_enqueue_admin_scripts($hook)
	{
		$screen = get_current_screen();
		wp_enqueue_style('somnia-fonts', get_template_directory_uri() . '/assets/css/fonts.css', array(), false);
		wp_enqueue_style('somnia-admin-main', get_template_directory_uri() . '/assets/css/admin-main.css', array(), false);

		// Dependencies for admin-main.js
		$admin_main_deps = array('jquery');

		// On WooCommerce attribute term pages, add media + color picker (used by attribute types in admin-main.js)
		$is_attribute_term_page = ($hook === 'edit-tags.php' || $hook === 'term.php')
			&& $screen && isset($screen->taxonomy) && strpos($screen->taxonomy, 'pa_') === 0;
		if ($is_attribute_term_page) {
			wp_enqueue_media();
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_script('wp-color-picker');
			$admin_main_deps[] = 'wp-color-picker';
		}

		wp_enqueue_script(
			'somnia-admin-main',
			get_template_directory_uri() . '/assets/js/admin-main.js',
			$admin_main_deps,
			'',
			true
		);
		// Localize script for Product Extra Content (in admin-main.js)
		if ($screen && ($screen->post_type === 'product' || $screen->post_type === 'extra_content_prod')) {
			wp_localize_script('somnia-admin-main', 'somniaExtraContent', array(
				'nonce' => wp_create_nonce('somnia-extra-content-nonce'),
				'ajaxUrl' => admin_url('admin-ajax.php'),
			));
		}
	}
	add_action('admin_enqueue_scripts', 'somnia_enqueue_admin_scripts');
}

/* Plugin Requred */
require_once get_template_directory() . '/plugin-install/plugin-required.php';

/* ACF Options */
require_once get_template_directory() . '/framework/acf-options.php';

/* Template Functions */
require_once get_template_directory() . '/framework/template-helper.php';

/* Post Functions */
require_once get_template_directory() . '/framework/templates/post-helper.php';

/* Block Load */
require_once get_template_directory() . '/framework/block-load.php';

/* Widgets Load */
require_once get_template_directory() . '/framework/widget-load.php';

/* Cron Functions */
require_once get_template_directory() . '/framework/cron-helper.php';

/* Woocommerce Functions */
if (class_exists('Woocommerce')) {
	require_once get_template_directory() . '/woocommerce/attribute-types.php';
	require_once get_template_directory() . '/woocommerce/shop-helper.php';
	
}

/* Product Extra Content */
require_once get_template_directory() . '/framework/product-extra-content.php';

/* Custom search posts */
function bt_custom_search_filter($query)
{
	if ($query->is_search() && !is_admin()) {
		if (!is_post_type_archive('product') && !is_tax('product_cat') && !is_singular('product') && !is_page_template('woocommerce/template-nosidebar-dropdown.php') && !is_page_template('woocommerce/template-nosidebar-popup.php') && !is_page_template('woocommerce/template-sidebar.php')) {
			$query->set('post_type', 'post');
		}
	}
}
add_action('pre_get_posts', 'bt_custom_search_filter');
