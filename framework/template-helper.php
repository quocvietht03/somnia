<?php
if (! isset($content_width)) $content_width = 900;
if (is_singular()) wp_enqueue_script("comment-reply");

if (! function_exists('somnia_setup')) {
	function somnia_setup()
	{
		/* Load textdomain */
		load_theme_textdomain('somnia', get_template_directory() . '/languages');

		/* Add custom logo */
		add_theme_support('custom-logo');

		/* Add RSS feed links to <head> for posts and comments. */
		add_theme_support('automatic-feed-links');

		/* Enable support for Post Thumbnails, and declare sizes. */
		add_theme_support('post-thumbnails');

		/* Enable support for Title Tag */
		add_theme_support("title-tag");

		/* This theme uses wp_nav_menu() in locations. */
		register_nav_menus(array(
			'primary_menu'   => esc_html__('Primary Menu', 'somnia'),

		));

		/* This theme styles the visual editor to resemble the theme style, specifically font, colors, icons, and column width. */
		add_editor_style('editor-style.css');

		/* Switch default core markup for search form, comment form, and comments to output valid HTML5. */
		add_theme_support('html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption'
		));

		add_theme_support('wp-block-styles');
		add_theme_support('responsive-embeds');
		add_theme_support('custom-header');
		add_theme_support('align-wide');

		/* This theme allows users to set a custom background. */
		add_theme_support('custom-background', apply_filters('somnia_custom_background_args', array(
			'default-color' => 'f5f5f5',
		)));

		/* Add support for featured content. */
		add_theme_support('featured-content', array(
			'featured_content_filter' => 'somnia_get_featured_posts',
			'max_posts' => 6,
		));

		/* This theme uses its own gallery styles. */
		add_filter('use_default_gallery_style', '__return_false');

		/* Add support woocommerce */
		add_theme_support( 'woocommerce', array(
			'thumbnail_image_width' => 600,
			'gallery_thumbnail_image_width' => 150,
			'single_image_width' => 1000,
		) );
	}
}
add_action('after_setup_theme', 'somnia_setup');

/* Logo */
if (!function_exists('somnia_logo')) {
	function somnia_logo($url = '', $height = 30)
	{
		if (!$url) {
			$url = get_template_directory_uri() . '/assets/images/site-logo.png';
		}
		echo '<a href="' . esc_url(home_url('/')) . '"><img class="logo" style="height: ' . esc_attr($height) . 'px; width: auto;" src="' . esc_url($url) . '" alt="' . esc_attr__('Logo', 'somnia') . '"/></a>';
	}
}

/* Nav Menu */
if (!function_exists('somnia_nav_menu')) {
	function somnia_nav_menu($menu_slug = '', $theme_location = '', $container_class = '')
	{
		if (has_nav_menu($theme_location) || $menu_slug) {
			wp_nav_menu(array(
				'menu'				=> $menu_slug,
				'container_class' 	=> $container_class,
				'items_wrap'      	=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'theme_location'  	=> $theme_location
			));
		} else {
			wp_page_menu(array(
				'menu_class'  => $container_class
			));
		}
	}
}
/* Page title */
if (!function_exists('somnia_page_title')) {
	function somnia_page_title()
	{
		ob_start();
		if (is_front_page()) {
			esc_html_e('Home', 'somnia');
		} elseif (is_home()) {
			esc_html_e('Blog', 'somnia');
		} elseif (is_search()) {
			esc_html_e('Search', 'somnia');
		} elseif (is_404()) {
			esc_html_e('404', 'somnia');
		} elseif (is_page()) {
			echo get_the_title();
		} elseif (is_archive()) {
			if (is_category()) {
				single_cat_title();
			} elseif (get_post_type() == 'product') {
				if (wc_get_page_id('shop')) {
					echo get_the_title(wc_get_page_id('shop'));
				} else {
					single_term_title();
				}
			} elseif (is_tag()) {
				single_tag_title();
			} elseif (is_author()) {
				printf(__('Author: %s', 'somnia'), '<span class="vcard">' . get_the_author() . '</span>');
			} elseif (is_day()) {
				printf(__('Day: %s', 'somnia'), '<span>' . get_the_date(get_option('date_format')) . '</span>');
			} elseif (is_month()) {
				printf(__('Month: %s', 'somnia'), '<span>' . get_the_date(get_option('date_format')) . '</span>');
			} elseif (is_year()) {
				printf(__('Year: %s', 'somnia'), '<span>' . get_the_date(get_option('date_format')) . '</span>');
			} elseif (is_tax('post_format', 'post-format-aside')) {
				esc_html_e('Aside', 'somnia');
			} elseif (is_tax('post_format', 'post-format-gallery')) {
				esc_html_e('Gallery', 'somnia');
			} elseif (is_tax('post_format', 'post-format-image')) {
				esc_html_e('Image', 'somnia');
			} elseif (is_tax('post_format', 'post-format-video')) {
				esc_html_e('Video', 'somnia');
			} elseif (is_tax('post_format', 'post-format-quote')) {
				esc_html_e('Quote', 'somnia');
			} elseif (is_tax('post_format', 'post-format-link')) {
				esc_html_e('Link', 'somnia');
			} elseif (is_tax('post_format', 'post-format-status')) {
				esc_html_e('Status', 'somnia');
			} elseif (is_tax('post_format', 'post-format-audio')) {
				esc_html_e('Audio', 'somnia');
			} elseif (is_tax('post_format', 'post-format-chat')) {
				esc_html_e('Chat', 'somnia');
			} else {
				echo get_the_title(wc_get_page_id('shop'));
			}
		} else {
			echo get_the_title();
		}

		return ob_get_clean();
	}
}

/* Page breadcrumb */
if (!function_exists('somnia_page_breadcrumb')) {
	function somnia_page_breadcrumb($home_text = 'Home', $delimiter = '-')
	{
		global $post;

		if (is_front_page()) {
			echo '<a class="bt-home" href="' . esc_url(home_url('/')) . '">' . $home_text . '</a> <span class="bt-deli first">' . $delimiter . '</span> ' . esc_html('Front Page', 'somnia');
		} elseif (is_home()) {
			echo  '<a class="bt-home" href="' . esc_url(home_url('/')) . '">' . $home_text . '</a> <span class="bt-deli first">' . $delimiter . '</span> ' . esc_html('Blog', 'somnia');
		} else {
			echo '<a class="bt-home" href="' . esc_url(home_url('/')) . '">' . $home_text . '</a> <span class="bt-deli first">' . $delimiter . '</span> ';
		}

		if (is_category()) {
			$thisCat = get_category(get_query_var('cat'), false);
			if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' <span class="bt-deli">' . $delimiter . '</span> ');
			echo '<span class="current">' . single_cat_title(esc_html__('Archive by category: ', 'somnia'), false) . '</span>';
		} elseif (is_tag()) {
			echo '<span class="current">' . single_tag_title(esc_html__('Posts tagged: ', 'somnia'), false) . '</span>';
		} elseif (is_post_type_archive()) {
			echo post_type_archive_title('<span class="current">', '</span>');
		} elseif (is_tax()) {
			echo '<span class="current">' . single_term_title(esc_html__('Archive by taxonomy: ', 'somnia'), false) . '</span>';
		} elseif (is_search()) {
			echo '<span class="current">' . esc_html__('Search results for: ', 'somnia') . get_search_query() . '</span>';
		} elseif (is_day()) {
			echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . ' ' . get_the_time('Y') . '</a> <span class="bt-deli">' . $delimiter . '</span> ';
			echo '<span class="current">' . get_the_time('d') . '</span>';
		} elseif (is_month()) {
			echo '<span class="current">' . get_the_time('F') . ' ' . get_the_time('Y') . '</span>';
		} elseif (is_single() && !is_attachment()) {
			if (get_post_type() != 'post') {
				if (get_post_type() == 'product') {
					$terms = get_the_terms(get_the_ID(), 'product_cat', '', '');
					if (!empty($terms) && !is_wp_error($terms)) {
						//the_terms(get_the_ID(), 'product_cat', '', ', ');
						$first_term = reset($terms); // Get first term
						$category_url = get_term_link($first_term->term_id, 'product_cat');
						if (!is_wp_error($category_url)) {
							echo '<a href="' . esc_url($category_url) . '">' . esc_html($first_term->name) . '</a>';
							echo ' <span class="bt-deli">' . $delimiter . '</span> ' . '<span class="current">' . get_the_title() . '</span>';
						} else {
							echo '<span class="current">' . get_the_title() . '</span>';
						}
					} else {
						echo '<span class="current">' . get_the_title() . '</span>';
					}
				} else {
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					if ($post_type->rewrite) {
						echo '<a href="' . esc_url(home_url('/')) . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>';
						echo ' <span class="bt-deli">' . $delimiter . '</span> ';
					}
					echo '<span class="current">' . get_the_title() . '</span>';
				}
			} else {
				$cat = get_the_category();
				$cat = $cat[0];
				$cats = get_category_parents($cat, TRUE, ' <span class="bt-deli">' . $delimiter . '</span> ');
				echo '' . $cats;
				echo '<span class="current">' . get_the_title() . '</span>';
			}
		} elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
			$post_type = get_post_type_object(get_post_type());
			if ($post_type) echo '<span class="current">' . $post_type->labels->name . '</span>';
		} elseif (is_attachment()) {
			$parent = get_post($post->post_parent);
			echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
			echo ' <span class="bt-deli">' . $delimiter . '</span> ' . '<span class="current">' . get_the_title() . '</span>';
		} elseif (is_page() && !is_front_page() && !$post->post_parent) {
			echo '<span class="current">' . get_the_title() . '</span>';
		} elseif (is_page() && !is_front_page() && $post->post_parent) {
			$parent_id  = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id = $page->post_parent;
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			for ($i = 0; $i < count($breadcrumbs); $i++) {
				echo '' . $breadcrumbs[$i];
				if ($i != count($breadcrumbs) - 1)
					echo ' <span class="bt-deli">' . $delimiter . '</span> ';
			}
			echo ' <span class="bt-deli">' . $delimiter . '</span> ' . '<span class="current">' . get_the_title() . '</span>';
		} elseif (is_author()) {
			global $author;
			$userdata = get_userdata($author);
			echo '<span class="current">' . esc_html__('Articles posted by ', 'somnia') . $userdata->display_name . '</span>';
		} elseif (is_404()) {
			echo '<span class="current">' . esc_html__('Error 404', 'somnia') . '</span>';
		}

		if (get_query_var('paged')) {
			if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) echo '<span class="bt-pages"> (';
			echo ' <span class="bt-deli">' . $delimiter . '</span> ' . esc_html__('Page', 'somnia') . ' ' . get_query_var('paged');
			if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) echo ')</span>';
		}
	}
}

/* Display navigation to next/previous post */
if (! function_exists('somnia_post_nav')) {
	function somnia_post_nav()
	{
		$previous = (is_attachment()) ? get_post(get_post()->post_parent) : get_adjacent_post(false, '', true);
		$next     = get_adjacent_post(false, '', false);
		if (! $next && ! $previous) {
			return;
		}
?>
		<nav class="bt-post-nav clearfix">
			<?php
			previous_post_link('<div class="bt-post-nav--item bt-prev"><span>' . esc_html__('Previous', 'somnia') . '</span><h3>%link</h3></div>');
			next_post_link('<div class="bt-post-nav--item bt-next"><span>' . esc_html__('Next', 'somnia') . '</span><h3>%link</h3></div>');
			?>
		</nav>
	<?php
	}
}

/* Display paginate links */
if (! function_exists('somnia_paginate_links')) {
	function somnia_paginate_links($wp_query)
	{
		if ($wp_query->max_num_pages <= 1) {
			return;
		}
	?>
		<nav class="bt-pagination" role="navigation">
			<?php
			$big = 999999999; // need an unlikely integer
			echo paginate_links(array(
				'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
				'format' => '?paged=%#%',
				'current' => max(1, get_query_var('paged')),
				'total' => $wp_query->max_num_pages,
				'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="13" viewBox="0 0 8 13" fill="none">
  <path d="M0.839282 12.4903C0.630446 12.2842 0.611461 11.9616 0.782327 11.7343L0.839282 11.6692L5.91327 6.6604L0.839282 1.65162C0.630446 1.44548 0.611461 1.1229 0.782327 0.895592L0.839282 0.830468C1.04812 0.624326 1.37491 0.605586 1.6052 0.774247L1.67117 0.830468L7.16137 6.24982C7.3702 6.45596 7.38919 6.77854 7.21832 7.00585L7.16137 7.07098L1.67117 12.4903C1.44145 12.7171 1.069 12.7171 0.839282 12.4903Z" fill="#212121"></path>
</svg>' . esc_html__('Prev', 'somnia'),
				'next_text' => esc_html__('Next', 'somnia') . '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="13" viewBox="0 0 8 13" fill="none">
  <path d="M0.839282 12.4903C0.630446 12.2842 0.611461 11.9616 0.782327 11.7343L0.839282 11.6692L5.91327 6.6604L0.839282 1.65162C0.630446 1.44548 0.611461 1.1229 0.782327 0.895592L0.839282 0.830468C1.04812 0.624326 1.37491 0.605586 1.6052 0.774247L1.67117 0.830468L7.16137 6.24982C7.3702 6.45596 7.38919 6.77854 7.21832 7.00585L7.16137 7.07098L1.67117 12.4903C1.44145 12.7171 1.069 12.7171 0.839282 12.4903Z" fill="#212121"></path>
</svg>',
			));
			?>
		</nav>
	<?php
	}
}

/* Display navigation to next/previous set of posts */
if (! function_exists('somnia_paging_nav')) {
	function somnia_paging_nav()
	{
		if ($GLOBALS['wp_query']->max_num_pages < 2) {
			return;
		}

		$paged        = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
		$pagenum_link = html_entity_decode(get_pagenum_link());
		$query_args   = array();
		$url_parts    = explode('?', $pagenum_link);

		if (isset($url_parts[1])) {
			wp_parse_str($url_parts[1], $query_args);
		}

		$pagenum_link = remove_query_arg(array_keys($query_args), $pagenum_link);
		$pagenum_link = trailingslashit($pagenum_link) . '%_%';

		$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
		$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit('page/%#%', 'paged') : '?paged=%#%';

	?>
		<nav class="bt-pagination" role="navigation">
			<?php
			echo paginate_links(array(
				'base'     => $pagenum_link,
				'format'   => $format,
				'total'    => $GLOBALS['wp_query']->max_num_pages,
				'current'  => $paged,
				'mid_size' => 1,
				'add_args' => array_map('urlencode', $query_args),
				'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="13" viewBox="0 0 8 13" fill="none">
  <path d="M0.839282 12.4903C0.630446 12.2842 0.611461 11.9616 0.782327 11.7343L0.839282 11.6692L5.91327 6.6604L0.839282 1.65162C0.630446 1.44548 0.611461 1.1229 0.782327 0.895592L0.839282 0.830468C1.04812 0.624326 1.37491 0.605586 1.6052 0.774247L1.67117 0.830468L7.16137 6.24982C7.3702 6.45596 7.38919 6.77854 7.21832 7.00585L7.16137 7.07098L1.67117 12.4903C1.44145 12.7171 1.069 12.7171 0.839282 12.4903Z" fill="#212121"/>
</svg>' . esc_html__('Prev', 'somnia'),
				'next_text' => esc_html__('Next', 'somnia') . '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="13" viewBox="0 0 8 13" fill="none">
  <path d="M0.839282 12.4903C0.630446 12.2842 0.611461 11.9616 0.782327 11.7343L0.839282 11.6692L5.91327 6.6604L0.839282 1.65162C0.630446 1.44548 0.611461 1.1229 0.782327 0.895592L0.839282 0.830468C1.04812 0.624326 1.37491 0.605586 1.6052 0.774247L1.67117 0.830468L7.16137 6.24982C7.3702 6.45596 7.38919 6.77854 7.21832 7.00585L7.16137 7.07098L1.67117 12.4903C1.44145 12.7171 1.069 12.7171 0.839282 12.4903Z" fill="#212121"/>
</svg>',
			));
			?>
		</nav>
	<?php
	}
}
/**
 * Back to top button
 * 
 * Adds a back to top button to the footer
 */
if (!function_exists('somnia_back_to_top')) {
	function somnia_back_to_top()
	{
	?>
		<a href="#" class="bt-back-to-top">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
				<path d="M1.00098 10.25L10.001 1.25L19.001 10.25H14.501V18.5C14.501 18.6989 14.422 18.8897 14.2813 19.0303C14.1407 19.171 13.9499 19.25 13.751 19.25H6.25098C6.05206 19.25 5.8613 19.171 5.72065 19.0303C5.57999 18.8897 5.50098 18.6989 5.50098 18.5V10.25H1.00098Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
			</svg>
		</a>
		<?php
	}
}
add_action('wp_footer', 'somnia_back_to_top', 99);

/**
 * popup newlsetter form
 * 
 * Adds a back to top button to the footer
 */
if (!function_exists('somnia_popup_newslleter_form')) {
	function somnia_popup_newslleter_form()
	{
		if (function_exists('get_field')) {
			$enable_newsletter_popup = get_field('enable_newsletter_popup', 'options');
			$newsletter_popup_setting = get_field('newsletter_popup_setting', 'options');
			$heading = !empty($newsletter_popup_setting['heading']) ? $newsletter_popup_setting['heading'] : esc_html__('Subscribe to get 10% OFF', 'somnia');
			$subheading = !empty($newsletter_popup_setting['sub_heading']) ? $newsletter_popup_setting['sub_heading'] : esc_html__('Subscribe to our newsletter and receive 10% off your first purchase', 'somnia');
			$note = !empty($newsletter_popup_setting['note_bottom']) ? $newsletter_popup_setting['note_bottom'] : '';
			$image_newsletter = !empty($newsletter_popup_setting['image_newsletter']['url']) ? $newsletter_popup_setting['image_newsletter']['url'] : '';
			$shortcode_form_newsletter = !empty($newsletter_popup_setting['shortcode_form_newsletter']) ? $newsletter_popup_setting['shortcode_form_newsletter'] : '';
		} else {
			$enable_newsletter_popup = false;
			$heading = esc_html__('Subscribe to get 10% OFF', 'somnia');
			$subheading = esc_html__('Subscribe to our newsletter and receive 10% off your first purchase', 'somnia');
			$note = '';
			$image_newsletter = '';
			$shortcode_form_newsletter = '';
		}
		if ($enable_newsletter_popup) {
		?>
			<div id="bt-newsletter-popup" class="bt-newsletter-popup">
				<div class="bt-newsletter-overlay"></div>
				<div class="bt-newsletter-popup-content">
					<span class="bt-close-popup"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
							<path d="M9.41183 8L15.6952 1.71665C15.7905 1.62455 15.8666 1.51437 15.9189 1.39255C15.9713 1.27074 15.9988 1.13972 16 1.00714C16.0011 0.874567 15.9759 0.743089 15.9256 0.620381C15.8754 0.497673 15.8013 0.386193 15.7076 0.292444C15.6138 0.198695 15.5023 0.124556 15.3796 0.0743523C15.2569 0.0241486 15.1254 -0.00111435 14.9929 3.76988e-05C14.8603 0.00118975 14.7293 0.0287337 14.6074 0.0810623C14.4856 0.133391 14.3755 0.209456 14.2833 0.30482L8 6.58817L1.71665 0.30482C1.52834 0.122941 1.27612 0.0223015 1.01433 0.0245764C0.752534 0.0268514 0.502106 0.131859 0.316983 0.316983C0.131859 0.502107 0.0268514 0.752534 0.0245764 1.01433C0.0223015 1.27612 0.122941 1.52834 0.30482 1.71665L6.58817 8L0.30482 14.2833C0.209456 14.3755 0.133391 14.4856 0.0810623 14.6074C0.0287337 14.7293 0.00118975 14.8603 3.76988e-05 14.9929C-0.00111435 15.1254 0.0241486 15.2569 0.0743523 15.3796C0.124556 15.5023 0.198695 15.6138 0.292444 15.7076C0.386193 15.8013 0.497673 15.8754 0.620381 15.9256C0.743089 15.9759 0.874567 16.0011 1.00714 16C1.13972 15.9988 1.27074 15.9713 1.39255 15.9189C1.51437 15.8666 1.62455 15.7905 1.71665 15.6952L8 9.41183L14.2833 15.6952C14.4226 15.8358 14.6006 15.9317 14.7945 15.9708C14.9885 16.0098 15.1898 15.9902 15.3726 15.9145C15.5554 15.8388 15.7115 15.7104 15.8211 15.5456C15.9306 15.3808 15.9886 15.1871 15.9877 14.9893C15.9878 14.8581 15.9619 14.7283 15.9117 14.6072C15.8615 14.4861 15.7879 14.376 15.6952 14.2833L9.41183 8Z" fill="#0C2C48" />
						</svg></span>
					<div class="bt-newsletter-popup-image">
						<?php if (!empty($image_newsletter)): ?>
							<img src="<?php echo esc_url($image_newsletter); ?>" alt="<?php echo esc_attr__('Newsletter', 'somnia'); ?>">
						<?php endif; ?>
					</div>
					<div class="bt-newsletter-popup-info">
						<h3 class="bt-title"><?php echo esc_html($heading); ?></h3>
						<p class="bt-subtitle"><?php echo esc_html($subheading); ?></p>
						<?php
						if (!empty($shortcode_form_newsletter)) {
							echo do_shortcode($shortcode_form_newsletter);
						} 
						?>
						<?php if (!empty($note)):
							echo '<div class="bt-newsletter-note">' . $note . '</div>';
						endif; ?>
					</div>
				</div>
			</div>
		<?php
		}
	}
	add_action('wp_footer', 'somnia_popup_newslleter_form');
}

/* Hook add Field Loop Caroucel Elementor */
add_action('elementor/element/loop-carousel/section_carousel_pagination/before_section_end', function ($element) {
	$element->add_control(
		'pagination_show_only_mobile',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__('Show Pagination Only Mobile?', 'somnia'),
			'default' => 'no',
			'prefix_class' => 'bt-',
			'label_on' => esc_html__('Yes', 'somnia'),
			'label_off' => esc_html__('No', 'somnia'),
			'return_value' => 'pagination-show-only-mobile',
			'separator' => 'before',
		]
	);
	$element->add_control(
		'pagination_use_theme_style',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__('Use Theme Style?', 'somnia'),
			'default' => 'no',
			'label_on' => esc_html__('Yes', 'somnia'),
			'label_off' => esc_html__('No', 'somnia'),
			'return_value' => 'yes',
			'selectors' => [
				'{{WRAPPER}} .swiper-pagination-bullet' => 'width: auto; height: auto; padding: 2px 10px; border-radius: 0; transition: all 0.3s ease;',
				'{{WRAPPER}} .swiper-pagination-bullet:hover,
				{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'padding: 2px 20px;',
			],
			'condition' => [
				'pagination' => 'bullets',
			],
		]
	);

	$element->add_responsive_control(
		'pagination_progress_width',
		[
			'label' => esc_html__('Width', 'somnia'),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'custom'],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 2000,
					'step' => 1,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .swiper-pagination-progressbar' => 'width: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'pagination' => 'progressbar',
			],
		]
	);
	$element->add_control(
		'pagination_progress_position_horizontal',
		[
			'label' => esc_html__('Position Horizontal', 'somnia'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'left',
			'options' => [
				'left' => esc_html__('Left', 'somnia'),
				'center' => esc_html__('Center', 'somnia'),
				'right' => esc_html__('Right', 'somnia'),
			],
			'prefix_class' => 'pagination-progress-position-horizontal-',
			'selectors' => [
				'{{WRAPPER}}.pagination-progress-position-horizontal-left .swiper-pagination-progressbar' => 'left: 0; margin-right: auto; transform: none;',
				'{{WRAPPER}}.pagination-progress-position-horizontal-center .swiper-pagination-progressbar' => 'left: 50%; transform: translateX(-50%); margin-left: auto; margin-right: auto;',
				'{{WRAPPER}}.pagination-progress-position-horizontal-right .swiper-pagination-progressbar' => 'right: 0; margin-left: auto; transform: none;',
			],
			'condition' => [
				'pagination' => 'progressbar',
			],
		]
	);
	$element->add_control(
		'pagination_progress_position_vertical',
		[
			'label' => esc_html__('Position Vertical', 'somnia'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'bottom',
			'options' => [
				'top' => esc_html__('Top', 'somnia'),
				'bottom' => esc_html__('Bottom', 'somnia'),
			],
			'condition' => [
				'pagination' => 'progressbar',
			],
		]
	);


	$element->add_responsive_control(
		'pagination_progress_top_position',
		[
			'label' => esc_html__('Top Position', 'somnia'),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'custom'],
			'range' => [
				'px' => [
					'min' => -100,
					'max' => 100,
				],
				'%' => [
					'min' => -100,
					'max' => 100,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .swiper-pagination-progressbar' => 'top: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'pagination' => 'progressbar',
				'pagination_progress_position_vertical' => 'top',
			],
		]
	);

	$element->add_responsive_control(
		'pagination_progress_bottom_position',
		[
			'label' => esc_html__('Bottom Position', 'somnia'),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'custom'],
			'range' => [
				'px' => [
					'min' => -100,
					'max' => 100,
				],
				'%' => [
					'min' => -100,
					'max' => 100,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .swiper-pagination-progressbar' => 'top: calc(100% + {{SIZE}}{{UNIT}});',
			],
			'condition' => [
				'pagination' => 'progressbar',
				'pagination_progress_position_vertical' => 'bottom',
			],
		]
	);
});
add_action('elementor/element/loop-carousel/section_navigation_settings/before_section_end', function ($element) {
	$element->add_control(
		'navigation_hidden_mobile',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__('Hidden Navigation Mobile?', 'somnia'),
			'default' => 'no',
			'prefix_class' => 'bt-',
			'label_on' => esc_html__('Yes', 'somnia'),
			'label_off' => esc_html__('No', 'somnia'),
			'return_value' => 'navigation-hidden-mobile',
			'separator' => 'before'
		]
	);
	$element->add_control(
		'navigation_use_theme_style',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__('Use Theme Style?', 'somnia'),
			'default' => 'no',
			'label_on' => esc_html__('Yes', 'somnia'),
			'label_off' => esc_html__('No', 'somnia'),
			'return_value' => 'yes',
			'selectors' => [
				'{{WRAPPER}} .elementor-swiper-button' => 'color: var(--arrow-normal-color, #181818); background: #FFFFFF; padding: 12px;',
				'{{WRAPPER}} .elementor-swiper-button svg' => 'width: var(--arrow-size, 20px); height: var(--arrow-size, 20px); fill: var(--arrow-normal-color, #181818);',
				'{{WRAPPER}} .elementor-swiper-button:hover' => 'color: var(--arrow-normal-color, #FFFFFF); background: #181818;',
				'{{WRAPPER}} .elementor-swiper-button:hover svg' => 'fill: var(--arrow-hover-color, #FFFFFF);',
			],
		]
	);
});
/* Hook add Field Caroucel Elementor */
add_action('elementor/element/nested-carousel/section_carousel_pagination/before_section_end', function ($element) {
	$element->add_control(
		'pagination_show_only_mobile',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__('Show Pagination Only Mobile?', 'somnia'),
			'default' => 'no',
			'prefix_class' => 'bt-',
			'label_on' => esc_html__('Yes', 'somnia'),
			'label_off' => esc_html__('No', 'somnia'),
			'return_value' => 'pagination-show-only-mobile',
			'separator' => 'before',
		]
	);

	$element->add_control(
		'pagination_use_theme_style',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__('Use Theme Style?', 'somnia'),
			'default' => 'no',
			'label_on' => esc_html__('Yes', 'somnia'),
			'label_off' => esc_html__('No', 'somnia'),
			'return_value' => 'yes',
			'selectors' => [
				'{{WRAPPER}} .swiper-pagination-bullet' => 'width: auto; height: auto; padding: 2px 10px; border-radius: 0; transition: all 0.3s ease;',
				'{{WRAPPER}} .swiper-pagination-bullet:hover,
				{{WRAPPER}} .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'padding: 2px 20px;',
			],
			'condition' => [
				'pagination' => 'bullets',
			],
		]
	);
	$element->add_responsive_control(
		'pagination_progress_width',
		[
			'label' => esc_html__('Width', 'somnia'),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'custom'],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 2000,
					'step' => 1,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .swiper-pagination-progressbar' => 'width: 100%; max-width: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'pagination' => 'progressbar',
			],
		]
	);
	$element->add_control(
		'pagination_progress_position_horizontal',
		[
			'label' => esc_html__('Position Horizontal', 'somnia'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'center',
			'options' => [
				'left' => esc_html__('Left', 'somnia'),
				'center' => esc_html__('Center', 'somnia'),
				'right' => esc_html__('Right', 'somnia'),
			],
			'prefix_class' => 'pagination-progress-position-horizontal-',
			'selectors' => [
				'{{WRAPPER}}.pagination-progress-position-horizontal-left .swiper-pagination-progressbar' => 'left: 0; margin-right: auto; transform: none;',
				'{{WRAPPER}}.pagination-progress-position-horizontal-center .swiper-pagination-progressbar' => 'left: 50%; transform: translateX(-50%); margin-left: auto; margin-right: auto;',
				'{{WRAPPER}}.pagination-progress-position-horizontal-right .swiper-pagination-progressbar' => 'right: 0; margin-left: auto; transform: none;',
			],
			'condition' => [
				'pagination' => 'progressbar',
			],
		]
	);
	$element->add_control(
		'pagination_progress_position_vertical',
		[
			'label' => esc_html__('Position Vertical', 'somnia'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'bottom',
			'options' => [
				'top' => esc_html__('Top', 'somnia'),
				'bottom' => esc_html__('Bottom', 'somnia'),
			],
			'condition' => [
				'pagination' => 'progressbar',
			],
		]
	);
	$element->add_responsive_control(
		'pagination_progress_top_position',
		[
			'label' => esc_html__('Top Position', 'somnia'),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'custom'],
			'range' => [
				'px' => [
					'min' => -100,
					'max' => 100,
				],
				'%' => [
					'min' => -100,
					'max' => 100,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 1,
				'unit' => 'px',
			],
			'selectors' => [
				'{{WRAPPER}} .swiper-pagination-progressbar' => 'top: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'pagination' => 'progressbar',
				'pagination_progress_position_vertical' => 'top',
			],
		]
	);

	$element->add_responsive_control(
		'pagination_progress_bottom_position',
		[
			'label' => esc_html__('Bottom Position', 'somnia'),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'custom'],
			'range' => [
				'px' => [
					'min' => -100,
					'max' => 100,
				],
				'%' => [
					'min' => -100,
					'max' => 100,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 1,
				'unit' => 'px',
			],
			'selectors' => [
				'{{WRAPPER}} .swiper-pagination-progressbar' => 'top: calc(100% + {{SIZE}}{{UNIT}});',
			],
			'condition' => [
				'pagination' => 'progressbar',
				'pagination_progress_position_vertical' => 'bottom',
			],
		]
	);
});
add_action('elementor/element/nested-carousel/section_navigation_settings/before_section_end', function ($element) {
	$element->add_control(
		'navigation_hidden_mobile',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__('Hidden Navigation Mobile?', 'somnia'),
			'default' => 'no',
			'prefix_class' => 'bt-',
			'label_on' => esc_html__('Yes', 'somnia'),
			'label_off' => esc_html__('No', 'somnia'),
			'return_value' => 'navigation-hidden-mobile',
			'separator' => 'before'
		]
	);
	$element->add_control(
		'navigation_use_theme_style',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => esc_html__('Use Theme Style?', 'somnia'),
			'default' => 'no',
			'label_on' => esc_html__('Yes', 'somnia'),
			'label_off' => esc_html__('No', 'somnia'),
			'return_value' => 'yes',
			'selectors' => [
				'{{WRAPPER}} .elementor-swiper-button' => 'color: var(--e-n-carousel-arrow-normal-color, #181818); background: #FFFFFF; padding: 12px;',
				'{{WRAPPER}} .elementor-swiper-button svg' => 'width: var(--e-n-carousel-arrow-size, 20px); height: var(--e-n-carousel-arrow-size, 20px); fill: var(--e-n-carousel-arrow-normal-color, #181818);',
				'{{WRAPPER}} .elementor-swiper-button:hover' => 'color: var(--e-n-carousel-arrow-hover-color, #FFFFFF); background: #181818;',
				'{{WRAPPER}} .elementor-swiper-button:hover svg' => 'fill: var(--e-n-carousel-arrow-hover-color, #FFFFFF);',
			],
		]
	);
});
/* Hook add Field Nested Tabs Elementor */
add_action('elementor/element/nested-tabs/section_title_style/before_section_end', function ($element) {
	$element->add_responsive_control(
		'heading_max_width',
		[
			'label' => esc_html__('Max Width', 'somnia'),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => ['px', '%', 'custom'],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 2000,
					'step' => 1,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .e-n-tabs-heading' => 'max-width: {{SIZE}}{{UNIT}};width: 100%;margin: 0 auto;',
			],
		]
	);
});

add_action('elementor/element/nested-tabs/section_title_style/before_section_end', function ($element) {
	$element->add_responsive_control(
		'heading_padding',
		[
			'label' => esc_html__('Padding', 'somnia'),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => ['px', 'em', '%'],
			'selectors' => [
				'{{WRAPPER}} .e-n-tabs-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'separator' => 'before',
		]
	);
});



// Slider Swiper Setting
function bt_elwg_get_slider_settings($settings, $breakpoints)
{
	// Helper: get the first non-empty value from a list of keys
	$get_first_nonempty = function ($keys, $default = 20) use ($settings) {
		foreach ($keys as $key) {
			if (isset($settings[$key]['size']) && $settings[$key]['size'] !== '') {
				return $settings[$key]['size'];
			}
		}
		return $default;
	};

	$slider_settings = [
		'autoplay' => isset($settings['slider_autoplay']) && $settings['slider_autoplay'] === 'yes',
		'loop' => isset($settings['slider_loop']) && $settings['slider_loop'] === 'yes',
		'speed' => (int) $settings['slider_speed'],
		'slidesPerView' => !empty($settings['slides_to_show_mobile']) ? (int)$settings['slides_to_show_mobile'] : 1,
		'spaceBetween' => $get_first_nonempty([
			'image_spacing_custom_mobile',
			'image_spacing_custom_mobile_extra',
			'image_spacing_custom_tablet',
			'image_spacing_custom_tablet_extra',
			'image_spacing_custom_laptop',
			'image_spacing_custom'
		], 20),
		'breakpoints' => []
	];

	foreach ($breakpoints as $key => $breakpoint) {
		$next_key = $key;
		$breakpoint_keys = array_keys($breakpoints);
		$current_index = array_search($key, $breakpoint_keys);

		if ($current_index !== false) {
			$preferred_next = match ($key) {
				'mobile' => 'mobile_extra',
				'mobile_extra' => 'tablet',
				'tablet' => 'tablet_extra',
				'tablet_extra' => 'laptop',
				'laptop' => 'desktop',
				default => $key
			};

			if (isset($breakpoints[$preferred_next])) {
				$next_key = $preferred_next;
			} else {
				$found_next = false;
				for ($i = $current_index + 1; $i < count($breakpoint_keys); $i++) {
					if (isset($breakpoints[$breakpoint_keys[$i]]) && $breakpoint_keys[$i] !== 'widescreen') {
						$next_key = $breakpoint_keys[$i];
						$found_next = true;
						break;
					}
				}
				if (!$found_next) {
					$next_key = 'desktop';
				}
			}
		}

		$slider_settings['breakpoints'][$breakpoint->get_value()] = ($next_key == 'desktop') ? [
			'slidesPerView' => !empty($settings['slides_to_show']) ? (int)$settings['slides_to_show'] : 5,
			'spaceBetween' => isset($settings['image_spacing_custom']['size']) ? $settings['image_spacing_custom']['size'] : 20
		] : [
			'slidesPerView' => !empty($settings["slides_to_show_{$next_key}"]) ? (int)$settings["slides_to_show_{$next_key}"] : (int)$settings['slides_to_show'],
			'spaceBetween' => isset($settings["image_spacing_custom_{$next_key}"]['size']) && $settings["image_spacing_custom_{$next_key}"]['size'] !== ''
				? $settings["image_spacing_custom_{$next_key}"]['size']
				: match ($next_key) {
					'desktop' => $settings['image_spacing_custom']['size'],
					'laptop' => $get_first_nonempty(['image_spacing_custom_desktop', 'image_spacing_custom'], 20),
					'tablet_extra' => $get_first_nonempty(['image_spacing_custom_laptop', 'image_spacing_custom_desktop', 'image_spacing_custom'], 20),
					'tablet' => $get_first_nonempty(['image_spacing_custom_tablet_extra', 'image_spacing_custom_laptop', 'image_spacing_custom_desktop', 'image_spacing_custom'], 20),
					'mobile_extra' => $get_first_nonempty(['image_spacing_custom_tablet', 'image_spacing_custom_tablet_extra', 'image_spacing_custom_laptop', 'image_spacing_custom_desktop', 'image_spacing_custom'], 20),
					default => $get_first_nonempty(['image_spacing_custom_mobile_extra', 'image_spacing_custom_tablet', 'image_spacing_custom_tablet_extra', 'image_spacing_custom_laptop', 'image_spacing_custom_desktop', 'image_spacing_custom'], 20)
				}
		];
	}
	return $slider_settings;
}

// Grid Area Positions for Flicker Collage
function bt_elwg_get_grid_area_positions($collage_items, $breakpoints)
{
	$result = [
		'breakpoints' => []
	];

	// Initialize breakpoints array with default
	$result['breakpoints']['default'] = [];

	// Initialize breakpoints for each breakpoint value
	foreach ($breakpoints as $key => $breakpoint) {
		$breakpoint_value = $breakpoint->get_value();
		$result['breakpoints'][$breakpoint_value] = [];
	}

	// Process each item
	foreach ($collage_items as $item) {
		// Helper: validate and get grid_area value
		$get_grid_area = function ($key, $default = '1 / 1 / 3 / 3') use ($item) {
			if (isset($item[$key]) && !empty($item[$key]) && trim($item[$key]) !== '') {
				$value = trim($item[$key]);
				// Validate format: should be "num / num / num / num"
				if (preg_match('/^\d+\s*\/\s*\d+\s*\/\s*\d+\s*\/\s*\d+$/', $value)) {
					return $value;
				}
			}
			return $default;
		};

		// Get default desktop grid area
		$default_grid_area = $get_grid_area('grid_area', '1 / 1 / 3 / 3');

		// Add default grid area
		$result['breakpoints']['default'][] = $default_grid_area;

		// Helper: check if grid_area value exists and is set
		$has_grid_area_value = function ($key) use ($item) {
			return isset($item[$key]) && !empty($item[$key]) && trim($item[$key]) !== '';
		};

		// Helper: get grid area with fallback chain (fallback to larger breakpoints)
		$get_grid_area_with_fallback = function ($key) use ($item, $get_grid_area, $default_grid_area, $has_grid_area_value) {
			// Get current breakpoint key first
			$current_key = match ($key) {
				'mobile' => 'grid_area_mobile',
				'mobile_extra' => 'grid_area_mobile_extra',
				'tablet' => 'grid_area_tablet',
				'tablet_extra' => 'grid_area_tablet_extra',
				'laptop' => 'grid_area_laptop',
				'desktop' => 'grid_area',
				default => 'grid_area'
			};

			// Check if current breakpoint has value
			if ($has_grid_area_value($current_key)) {
				return $get_grid_area($current_key, $default_grid_area);
			}

			// Fallback chain: fallback to larger breakpoints (larger to smaller)
			$fallback_chain = match ($key) {
				'mobile' => ['grid_area_tablet', 'grid_area_tablet_extra', 'grid_area_laptop', 'grid_area'],
				'mobile_extra' => ['grid_area_tablet', 'grid_area_tablet_extra', 'grid_area_laptop', 'grid_area'],
				'tablet' => ['grid_area_tablet_extra', 'grid_area_laptop', 'grid_area'],
				'tablet_extra' => ['grid_area_laptop', 'grid_area'],
				'laptop' => ['grid_area'],
				'desktop' => [], // Desktop already checked above
				default => ['grid_area']
			};

			foreach ($fallback_chain as $fallback_key) {
				if ($has_grid_area_value($fallback_key)) {
					return $get_grid_area($fallback_key, $default_grid_area);
				}
			}

			return $default_grid_area;
		};

		// Get grid area for each breakpoint
		foreach ($breakpoints as $key => $breakpoint) {
			$breakpoint_value = $breakpoint->get_value();

			// Get grid area for this breakpoint with proper fallback
			$grid_area = $get_grid_area_with_fallback($key);

			$result['breakpoints'][$breakpoint_value][] = $grid_area;
		}
	}

	return $result;
}

/**
 * Preloader HTML
 */
if (!function_exists('somnia_preloader_html')) {
	function somnia_preloader_html()
	{
		if (!function_exists('get_field')) {
			return;
		}

		$preloader_options = get_field('site_preloader', 'options');

		if (!$preloader_options || !isset($preloader_options['enable_preloader']) || !$preloader_options['enable_preloader']) {
			return;
		}

		$logo = isset($preloader_options['logo_preloader']) ? $preloader_options['logo_preloader'] : null;
		$background = isset($preloader_options['background_preloader']) ? $preloader_options['background_preloader'] : '#000000';

		// Fallback to site logo if no preloader logo is set
		if (!$logo) {
			$custom_logo_id = get_theme_mod('custom_logo');
			if ($custom_logo_id) {
				$logo = array(
					'ID' => $custom_logo_id,
					'url' => wp_get_attachment_image_url($custom_logo_id, 'full'),
					'alt' => get_bloginfo('name'),
					'mime_type' => get_post_mime_type($custom_logo_id)
				);
			}
		}

		// If still no logo, return
		if (!$logo || !isset($logo['url'])) {
			return;
		}

		// Check if logo is SVG
		$is_svg = false;
		$svg_content = '';
		$image_url = isset($logo['url']) ? $logo['url'] : '';

		// Check if the image is SVG by URL extension
		if ($image_url && pathinfo($image_url, PATHINFO_EXTENSION) === 'svg') {
			$is_svg = true;

			// Get SVG content via wp_safe_remote_get
			$response = wp_safe_remote_get($image_url, array(
				'timeout' => 20,
				'headers' => array(
					'User-Agent' => 'Mozilla/5.0 (compatible; WordPress)',
				),
			));

			if (!is_wp_error($response)) {
				$svg_content = wp_remote_retrieve_body($response);

				// Parse SVG and add classes to paths
				if ($svg_content) {
					// Add class to SVG tag
					$svg_content = preg_replace('/<svg/', '<svg class="bt-preloader-logo"', $svg_content, 1);

					// Add class and data-path to all path elements
					$path_index = 0;
					// Add class and data-path to all ellipse/circle elements
					$svg_content = preg_replace_callback('/<(ellipse|circle)/', function ($matches) use (&$path_index) {
						$result = '<' . $matches[1] . ' class="bt-preloader-path" data-path="' . $path_index . '"';
						$path_index++;
						return $result;
					}, $svg_content);
					$svg_content = preg_replace_callback('/<path/', function ($matches) use (&$path_index) {
						$result = '<path class="bt-preloader-path" data-path="' . $path_index . '"';
						$path_index++;
						return $result;
					}, $svg_content);
					// Add class and data-path to all polygon/polyline elements
					$svg_content = preg_replace_callback('/<(polygon|polyline)/', function ($matches) use (&$path_index) {
						$result = '<' . $matches[1] . ' class="bt-preloader-path" data-path="' . $path_index . '"';
						$path_index++;
						return $result;
					}, $svg_content);
				}
			}
		}

		?>
		<div class="bt-preloader" id="bt-preloader" style="background: <?php echo esc_attr($background); ?>;">
			<div class="bt-preloader-content">
				<?php if ($is_svg && $svg_content):
					echo '<div class="bt-svg-logo">' . $svg_content . '</div>';
				else: ?>
					<div class="bt-preloader-spinner">
						<img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr(isset($logo['alt']) ? $logo['alt'] : get_bloginfo('name')); ?>" class="bt-preloader-image">
					</div>
				<?php endif; ?>
			</div>
		</div>
<?php
	}
}

/**
 * Add preloader to wp_body_open hook
 */
if (!function_exists('somnia_add_preloader')) {
	function somnia_add_preloader()
	{
		// Don't show preloader in Elementor editor
		if (class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
			return;
		}

		// Don't show preloader in Elementor preview mode
		if (class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return;
		}

		// Only add preloader if enabled
		if (!function_exists('get_field')) {
			return;
		}

		$preloader_options = get_field('site_preloader', 'options');

		if ($preloader_options && isset($preloader_options['enable_preloader']) && $preloader_options['enable_preloader']) {
			somnia_preloader_html();
		}
	}
	add_action('wp_body_open', 'somnia_add_preloader', 1);
}
