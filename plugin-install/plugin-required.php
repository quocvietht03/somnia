<?php
/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.6.1
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 *
 * Depending on your implementation, you may want to change the include call:
 *
 * Parent Theme:
 * require_once get_template_directory() . '/path/to/class-tgm-plugin-activation.php';
 *
 * Child Theme:
 * require_once get_stylesheet_directory() . '/path/to/class-tgm-plugin-activation.php';
 */

require_once get_template_directory() . '/plugin-install/class-tgm-plugin-activation.php';

function somnia_register_required_plugins() {
	if ( isset( $_GET['page'] ) && $_GET['page'] === 'verifytheme_settings' ) {
		return;
	} 

	$pathfile = 'https://download.beplusthemes.com/';

	$plugin_includes = array(
		array(
			'name'     		=> __( 'Elementor Website Builder', 'somnia' ),
			'slug'     		=> 'elementor',
			'required'     	=> true,
		),
		array(
			'name'          => __( 'Elementor Pro', 'somnia' ),
			'slug'          => 'elementor-pro',
			'source'        => $pathfile . 'elementor-pro.zip',
			'required'      => true,
		),
		array(
			'name'          => __( 'Smart Slider 3 Pro', 'somnia' ),
			'slug'          => 'nextend-smart-slider3-pro',
			'source'        => $pathfile . 'nextend-smart-slider3-pro.zip',
			'required'      => true,
		),
		array(
			'name'          => __( 'Advanced Custom Fields PRO', 'somnia' ),
			'slug'          => 'advanced-custom-fields-pro',
			'source'        => $pathfile . 'advanced-custom-fields-pro.zip',
			'required'      => true,
		),
		array(
			'name'          => __( 'Gravity Forms', 'somnia' ),
			'slug'          => 'gravityforms',
			'source'        => $pathfile . 'gravityforms.zip',
			'required'      => true,
		),
		array(
			'name'          => __( 'WooCommerce', 'somnia' ),
			'slug'          => 'woocommerce',
			'required'      => false,
		),
		array(
			'name'          => __( 'Worry Proof Backup', 'somnia' ),
			'slug'          => 'worry-proof-backup',
			'required'      => false,
		),

	);

	$config = array(
		'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => false,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => true,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.

	);

	tgmpa( $plugin_includes, $config );
}
add_action( 'tgmpa_register', 'somnia_register_required_plugins' );

/**
 * Dummy Demo Data
 */
function somnia_are_required_plugins_active() {
	$required_plugins = array(
		'elementor/elementor.php',
		'elementor-pro/elementor-pro.php',
		'nextend-smart-slider3-pro/nextend-smart-slider3-pro.php',
		'advanced-custom-fields-pro/acf.php',
		'gravityforms/gravityforms.php',
		'woocommerce/woocommerce.php',
	);
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    foreach ( $required_plugins as $plugin ) {
        if ( ! is_plugin_active( $plugin ) ) {
            return false;
        }
    }

    return true;
}

if ( somnia_are_required_plugins_active() ) {
	define('WORRPRBA_DUMMY_PACK_CENTER_SUPPORTED', true);
	define('WORRPRBA_DUMMY_PACK_CENTER_ENDPOINT', 'https://wpb-dummy-pack-center-neon.vercel.app/api/');
	define('WORRPRBA_DUMMY_PACK_CENTER_THEME_SLUG', 'somnia');

	add_filter( 'worrprba_dummy_pack_center_license_key', function( $purchase_code ) {
		$verifytheme = get_option( '_verifytheme_settings' );
		if($verifytheme) {
			$verifytheme_ob = json_decode($verifytheme);
			$purchase_code = $verifytheme_ob->purchase_code;

			return $purchase_code;
		}
		return '';
	} );

	add_filter( 'worrprba_dummy_pack_center_submenu_args', function ($submenu_args) {
		return $submenu_args = array(
			'parent_slug' => 'themes.php',
			'page_title'  => __('Demo Import', 'somnia'),
			'menu_title'  => __('Demo Import', 'somnia'),
			'capability'  => 'manage_options',
			'menu_slug'   => 'dummy-pack-center',
			'callback'    => 'worrprba_dummy_pack_center_page'
		);
	} );

	add_filter('worrprba_dummy_pack_skip_install_proccess', function($skip) {
		return ['restore_plugins']; // support: restore_uploads, restore_plugins, restore_database
	}, 10);

	add_action('worry-proof-backup:after_install_dummy_pack_done', function($payload) {
		if ( did_action( 'elementor/loaded' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		if ( function_exists( 'wc_delete_product_transients' ) ) {
			wc_delete_product_transients();
		}

		flush_rewrite_rules();

		return $payload;
	}, 10, 1 );
} else {
	function somnia_import_plugins_notice() {
		if ( isset( $_GET['page'] ) && $_GET['page'] === 'tgmpa-install-plugins' ) {
			return;
		} 

		if ( somnia_are_required_plugins_active() ) {
			return;
		}

		echo '<div class="notice notice-warning settings-error is-dismissible">';
		echo '<p><strong>' . esc_html__( 'Demo import is available after activating Envato license and all required plugins.', 'somnia' ) . '</strong></p>';
		echo '<p><a href="' . esc_url( admin_url( 'themes.php?page=tgmpa-install-plugins' ) ) . '">' . esc_html__( 'Install Required Plugins', 'somnia' ) . '</a></p>';
		echo '</div>';
	}
	add_action( 'admin_notices', 'somnia_import_plugins_notice' );
}
 
/**
 * Verify purchase code
 */
require get_template_directory() . '/license-manager/VerifyTheme.php';

// Initialize the admin UI and AJAX handlers
add_action( 'after_setup_theme', function() {
    if ( class_exists( 'VerifyTheme_Admin' ) ) {
        VerifyTheme_Admin::init();
    }
} );
