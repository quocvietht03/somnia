<?php

namespace SomniaElementorWidgets;

/**
 * Class ElementorWidgets
 *
 * Main ElementorWidgets class
 * @since 1.0.0
 */
class ElementorWidgets
{

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var ElementorWidgets The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return ElementorWidgets An instance of the class.
	 */
	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public $widgets = array();

	public function widgets_list()
	{

		$this->widgets = array(
			'site-information',
			'site-information-style-1',
			'site-social',
			'site-copyright',
			'site-notification',
			'instagram-posts',
			'recent-posts',
			'banner-product-slider',
			'page-breadcrumb',
			'mobile-menu',
			'post-loop-item',
			'post-loop-item-style-1',
			'post-loop-item-style-2',
			'post-loop-item-style-3',
			'post-grid',
			'product-loop-item',
			'product-loop-item-style-1',
			'product-loop-item-style-2',
			'product-category-item',
			'product-wishlist',
			'product-compare',
			'highlighted-heading',
			'mini-cart',
			'list-faq',
			'bt-accordion',
			'account-login',
			'search-product',
			'search-product-style-1',
			'brand-slider',
			'product-item',
			'product-showcase',
			'product-showcase-style-1',
			'product-showcase-style-2',
			'product-showcase-vertical',
			'heading-animation',
			'mini-wishlist',
			'mini-compare',
			'product-tooltip-hotspot',
			'product-list-hotspot',
			'product-overlay-hotspot',
			'product-overlay-hotspot-style-1',
			'product-slider-bottom-hotspot',
			'product-popup-hotspot',
			'product-testimonial',
			'product-testimonial-slider',
			'the-story',
			'testimonial-slider',
			'testimonials-staggered-slider',
			'countdown',
			'our-teams',
			'our-store',
			'currency-switcher',
			'language-switcher',
			'accordion-with-product-slider',
			'title-nav-with-slider',
			'collection-banner',
			'site-icon-payment',
			'text-slider',
			'store-locations-slider',
			'product-nav-image',
			'vertical-banner-slider',
			'offers-slider',
			'bundle-save',
			'order-tracking',
			'list-text-image-hover',
			'flicker-collage',
			'product-banner-scroll-hotspot'
		);

		return $this->widgets;
	}

	/**
	 * backend_styles
	 *
	 * Load required core files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function backend_styles()
	{
		wp_enqueue_style('somnia-backend-editor', get_template_directory_uri() . '/framework/widgets/backend.css', array(), false);
	}

	/**
	 * widget_styles
	 *
	 * Load required core files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_styles()
	{
		wp_enqueue_style('swiper-slider', get_template_directory_uri() . '/assets/libs/swiper/swiper.min.css', array(), false);
		wp_enqueue_style('magnific-popup', get_template_directory_uri() . '/assets/libs/magnific-popup/magnific-popup.css', array(), false);
	}

	/**
	 * widget_scripts
	 *
	 * Load required core files.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_scripts()
	{
		wp_register_script('swiper-slider', get_template_directory_uri() . '/assets/libs/swiper/swiper.min.js', array('jquery'), '', true);
		wp_register_script('elementor-widgets',  get_template_directory_uri() . '/framework/widgets/frontend.js', ['jquery'], '', true);
		wp_register_script('magnific-popup', get_template_directory_uri() . '/assets/libs/magnific-popup/jquery.magnific-popup.js', array('jquery'), '', true);
	}

	/**
	 * Check if Elementor Pro is active
	 *
	 * @since 1.0.0
	 * @access public
	 * @return bool
	 */
	public static function is_elementor_pro_active()
	{
		// Check if Elementor Pro class exists
		if (class_exists('ElementorPro\Plugin')) {
			return true;
		}

		// Fallback: Check if plugin is active
		if (!function_exists('is_plugin_active')) {
			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		return is_plugin_active('elementor-pro/elementor-pro.php');
	}
	
	/**
	 * Get list of widgets that require Elementor Pro
	 *
	 * @since 1.0.0
	 * @access private
	 * @return array
	 */
	private function get_elementor_pro_widgets()
	{
		return array(
			'instagram-posts',
			'banner-product-slider',
			'offers-slider',
			'brand-slider',
			'testimonial-slider',
			'product-testimonial-slider',
			'the-story',
			'testimonials-staggered-slider',
			'store-locations-slider',
		);
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function include_widgets_files()
	{
		$elementor_pro_widgets = $this->get_elementor_pro_widgets();
		$is_elementor_pro_active = self::is_elementor_pro_active();

		foreach ($this->widgets_list() as $widget) {
			// Skip widgets that require Elementor Pro if Elementor Pro is not active
			if (in_array($widget, $elementor_pro_widgets) && ! $is_elementor_pro_active) {
				continue;
			}

			$widget_file = get_template_directory() . '/framework/widgets/' . $widget . '/widget.php';

			// Only require if file exists and class is not already declared
			if (file_exists($widget_file) && ! class_exists($widget)) {
				require_once $widget_file;
			}

			// Include skins safely
			$skins_path = get_template_directory() . '/framework/widgets/' . $widget . '/skins/';
			if (is_dir($skins_path)) {
				foreach (glob($skins_path . '*.php') as $filepath) {
					if (file_exists($filepath)) {
						include $filepath;
					}
				}
			}
		}
	}

	/**
	 * Register widget with Elementor Pro check
	 *
	 * @since 1.0.0
	 * @access private
	 * @param string $class_name Full class name
	 * @param string $widget_name Widget name for placeholder
	 * @param string $widget_title Widget title for placeholder
	 */
	private function register_widget_with_pro_check($class_name, $widget_name, $widget_title)
	{
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

		if (class_exists($class_name)) {
			$widgets_manager->register_widget_type(new $class_name());
		} elseif (class_exists('Elementor\Modules\Promotions\Widgets\Pro_Widget_Promotion')) {
			// Create wrapper class to add e-widget-pro-promotion class
			$placeholder = new class([], [
				'widget_name' => $widget_name,
				'widget_title' => $widget_title,
			]) extends \Elementor\Modules\Promotions\Widgets\Pro_Widget_Promotion {
				protected function get_html_wrapper_class()
				{
					return parent::get_html_wrapper_class() . ' e-widget-pro-promotion';
				}
			};
			$widgets_manager->register_widget_type($placeholder);
		}
	}

	/**
	 * Register categories
	 *
	 * Register new Elementor category.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_categories($elements_manager)
	{

		$elements_manager->add_category(
			'somnia',
			[
				'title' => esc_html__('Somnia', 'somnia')
			]
		);
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widgets()
	{
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SiteInformation\Widget_SiteInformation());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SiteInformationStyle1\Widget_SiteInformationStyle1());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SiteNotification\Widget_SiteNotification());

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SiteSocial\Widget_SiteSocial());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SiteCopyright\Widget_SiteCopyright());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\RecentPosts\Widget_RecentPosts());


		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PageBreadcrumb\Widget_PageBreadcrumb());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\MobileMenu\Widget_MobileMenu());

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PostLoopItem\Widget_PostLoopItem());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PostLoopItemStyle1\Widget_PostLoopItemStyle1());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PostLoopItemStyle2\Widget_PostLoopItemStyle2());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PostLoopItemStyle3\Widget_PostLoopItemStyle3());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PostGrid\Widget_PostGrid());

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductLoopItem\Widget_ProductLoopItem());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductLoopItemStyle1\Widget_ProductLoopItemStyle1());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductLoopItemStyle2\Widget_ProductLoopItemStyle2());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductCategoryItem\Widget_ProductCategoryItem());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductWishlist\Widget_ProductWishlist());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductCompare\Widget_ProductCompare());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\AccountLogin\Widget_AccountLogin());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SearchProduct\Widget_SearchProduct());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\SearchProductStyle1\Widget_SearchProductStyle1());


		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductItem\Widget_ProductItem());

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\HighlightedHeading\Widget_HighlightedHeading());

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\MiniCart\Widget_MiniCart());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ListFaq\Widget_ListFaq());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\BtAccordion\Widget_BtAccordion());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\HeadingAnimation\Widget_HeadingAnimation());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\MiniWishlist\Widget_MiniWishlist());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\MiniCompare\Widget_MiniCompare());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductTooltipHotspot\Widget_ProductTooltipHotspot());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductListHotspot\Widget_ProductListHotspot());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductSliderBottomHotspot\Widget_ProductSliderBottomHotspot());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductPopupHotspot\Widget_ProductPopupHotspot());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductTestimonial\Widget_ProductTestimonial());


		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\CountDown\Widget_CountDown());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\OurTeams\Widget_OurTeams());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\OurStore\Widget_OurStore());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\CurrencySwitcher\Widget_CurrencySwitcher());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\LanguageSwitcher\Widget_LanguageSwitcher());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\AccordionWithProductSlider\Widget_AccordionWithProductSlider());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\TitleNavWithSlider\Widget_TitleNavWithSlider());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\CollectionBanner\Widget_CollectionBanner());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductOverlayHotspot\Widget_ProductOverlayHotspot());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductOverlayHotspotStyle1\Widget_ProductOverlayHotspotStyle1());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\IconPayment\Widget_IconPayment());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\TextSlider\Somnia_TextSlider());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductShowcase\Widget_ProductShowcase());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductShowcaseStyle1\Widget_ProductShowcaseStyle1());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductShowcaseStyle2\Widget_ProductShowcaseStyle2());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductShowcaseVertical\Widget_ProductShowcaseVertical());


		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductNavImage\Widget_ProductNavImage());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\VerticalBannerSlider\Somnia_VerticalBannerSlider());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\BundleSave\Widget_BundleSave());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\OrderTracking\Widget_OrderTracking());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ListTextImageHover\Widget_ListTextImageHover());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\FlickerCollage\Widget_FlickerCollage());

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\ProductBannerScrollHotspot\Widget_ProductBannerScrollHotspot());
		// Register widgets that require Elementor Pro
		$this->register_widget_with_pro_check(
			'SomniaElementorWidgets\Widgets\InstagramPosts\Widget_InstagramPosts',
			'bt-instagram-posts',
			__('Instagram Posts', 'somnia')
		);
		$this->register_widget_with_pro_check(
			'SomniaElementorWidgets\Widgets\BannerProductSlider\Widget_BannerProductSlider',
			'bt-banner-product-slider',
			__('Banner Product Slider', 'somnia')
		);

		$this->register_widget_with_pro_check(
			'SomniaElementorWidgets\Widgets\OffersSlider\Widget_OffersSlider',
			'bt-offers-slider',
			__('Offers Slider', 'somnia')
		);
		$this->register_widget_with_pro_check(
			'SomniaElementorWidgets\Widgets\BrandSlider\Widget_BrandSlider',
			'bt-brand-slider',
			__('Brand Slider', 'somnia')
		);
		$this->register_widget_with_pro_check(
			'SomniaElementorWidgets\Widgets\ProductTestimonialSlider\Widget_ProductTestimonialSlider',
			'bt-product-testimonial-slider',
			__('Product Testimonial Slider', 'somnia')
		);

		$this->register_widget_with_pro_check(
			'SomniaElementorWidgets\Widgets\TheStory\Widget_TheStory',
			'bt-the-story',
			__('The Story', 'somnia')
		);

		$this->register_widget_with_pro_check(
			'SomniaElementorWidgets\Widgets\TestimonialSlider\Widget_TestimonialSlider',
			'bt-testimonial-slider',
			__('Testimonial Slider', 'somnia')
		);

		$this->register_widget_with_pro_check(
			'SomniaElementorWidgets\Widgets\TestimonialsStaggeredSlider\Widget_TestimonialsStaggeredSlider',
			'bt-testimonials-staggered-slider',
			__('Testimonials Staggered Slider', 'somnia')
		);
		$this->register_widget_with_pro_check(
			'SomniaElementorWidgets\Widgets\StoreLocationsSlider\Widget_StoreLocationsSlider',
			'bt-store-locations-slider',
			__('Store Locations Slider', 'somnia')
		);
	}

	/**
	 *  ElementorWidgets class constructor
	 *
	 * Register action hooks and filters
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct()
	{
		// Enqueue backend editor styles
		add_action('elementor/editor/after_enqueue_styles', [$this, 'backend_styles']);

		// Register widget styles
		add_action('elementor/frontend/after_register_styles', [$this, 'widget_styles']);

		// Register widget scripts
		add_action('elementor/frontend/after_register_scripts', [$this, 'widget_scripts']);

		// Register categories
		add_action('elementor/elements/categories_registered', [$this, 'register_categories']);

		// Register widgets
		add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
	}
}

// Instantiate ElementorWidgets Class
ElementorWidgets::instance();
