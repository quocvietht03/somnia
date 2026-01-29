<?php

/**
 * Shop Titlebar
 * 
 */

if (!function_exists('get_field')) {
    return;
}

// Get shop title bar options
$shop_title_bar = get_field('shop_title_bar', 'options') ?: [];

// Override with URL parameters if provided (for demo purposes)
if (isset($_GET['layout-titlebar'])) {
    $layout_titlebar = sanitize_text_field($_GET['layout-titlebar']);
    if ($layout_titlebar === 'no-titlebar') {
        $shop_title_bar['select_titlebar'] = 'no-titlebar';
    }
    if ($layout_titlebar === 'left') {
        $shop_title_bar['breadcrumb_position'] = 'left';
    }
    if ($layout_titlebar === 'right') {
        $shop_title_bar['breadcrumb_position'] = 'right';
    }
    if ($layout_titlebar === 'center') {
        $shop_title_bar['breadcrumb_position'] = 'center';
    }
    if ($layout_titlebar === 'description') {
        $shop_title_bar['enable_description_titlebarshop'] = true;
        $shop_title_bar['breadcrumb_position'] = 'center';
    }
    if ($layout_titlebar === 'background') {
        $shop_title_bar['enable_background_titlebarshop'] = true;
        $shop_title_bar['breadcrumb_position'] = 'center';
    }
}

if (isset($_GET['layout-bottom-titlebar'])) {
    $layout_bottom_titlebar = absint($_GET['layout-bottom-titlebar']);
    $shop_title_bar['enable_section_bottom_titlebarshop'] = true;
    $section_post = get_post($layout_bottom_titlebar);
    if ($section_post) {
        $shop_title_bar['select_section_bottom_title_bar'] = $section_post;
    }
}

// Get select titlebar type (default or no-titlebar)
$select_titlebar = isset($shop_title_bar['select_titlebar']) ? $shop_title_bar['select_titlebar'] : 'default';

// If no titlebar selected, check if we still need section bottom
if ($select_titlebar === 'no-titlebar') {
    // Check enable section bottom
    $enable_section_bottom = isset($shop_title_bar['enable_section_bottom_titlebarshop']) ? $shop_title_bar['enable_section_bottom_titlebarshop'] : false;

    if ($enable_section_bottom) {
        $section_bottom = isset($shop_title_bar['select_section_bottom_title_bar']) ? $shop_title_bar['select_section_bottom_title_bar'] : null;

        if ($section_bottom && !empty($section_bottom->ID)) {
            echo '<div class="bt-shop-titlebar-section-bottom">';
            echo do_shortcode('[elementor-template id="' . $section_bottom->ID . '"]');
            echo '</div>';
        }
    }
    return;
}

// Get all options for default titlebar
$breadcrumb_position = isset($shop_title_bar['breadcrumb_position']) ? $shop_title_bar['breadcrumb_position'] : 'left';
$enable_description = isset($shop_title_bar['enable_description_titlebarshop']) ? $shop_title_bar['enable_description_titlebarshop'] : false;
$enable_background = isset($shop_title_bar['enable_background_titlebarshop']) ? $shop_title_bar['enable_background_titlebarshop'] : false;
$enable_section_bottom = isset($shop_title_bar['enable_section_bottom_titlebarshop']) ? $shop_title_bar['enable_section_bottom_titlebarshop'] : false;

// Get description
$description_shop = '';
if ($enable_description) {
    $description_shop = isset($shop_title_bar['description_shop']) ? $shop_title_bar['description_shop'] : '';
}

// Check if product_cat is set in URL or we're on category archive page - override title and description
$page_title = somnia_page_title();
$category_term = null;

// Check if we're on a category page (either via URL param or category archive)
$is_category_page = isset($_GET['product_cat']) || somnia_is_category_archive_page();

if ($is_category_page) {
    // Get category term
    if (isset($_GET['product_cat']) && !empty($_GET['product_cat'])) {
        $cat_slug = sanitize_text_field($_GET['product_cat']);
        $category_term = get_term_by('slug', $cat_slug, 'product_cat');
    } elseif (somnia_is_category_archive_page()) {
        $category_term = get_queried_object();
    }

    if ($category_term && !is_wp_error($category_term)) {
        // Override title with category name
        $page_title = $category_term->name;

        // Override description with category description if enable_description is on
        if ($enable_description) {
            $cat_description = term_description($category_term->term_id, 'product_cat');
            $description_shop = $cat_description;
        }
    }
}

// Get background settings
$background_color = '';
$background_image = '';
$background_overlay = '';
$style_attributes = '';

if ($enable_background) {
    $background_color = isset($shop_title_bar['background_color_titlebarshop']) ? $shop_title_bar['background_color_titlebarshop'] : '';
    $background_image = isset($shop_title_bar['background_images_titlebarshop']) ? $shop_title_bar['background_images_titlebarshop'] : '';
    $background_overlay = isset($shop_title_bar['background_overlay_titlebarshop']) ? $shop_title_bar['background_overlay_titlebarshop'] : '';

    if ($background_color || $background_image) {
        $style_parts = [];
        if ($background_color) {
            $style_parts[] = 'background-color: ' . esc_attr($background_color) . ';';
        }
        if ($background_image && is_array($background_image) && isset($background_image['url'])) {
            $style_parts[] = 'background-image: url(' . esc_url($background_image['url']) . ');';
        }
        $style_attributes = implode(' ', $style_parts);
    }
}

// Get section bottom
$section_bottom = null;
if ($enable_section_bottom) {
    $section_bottom = isset($shop_title_bar['select_section_bottom_title_bar']) ? $shop_title_bar['select_section_bottom_title_bar'] : null;
}

?>

<section class="bt-shop-titlebar bt-breadcrumb-<?php echo esc_attr($breadcrumb_position); ?>" <?php if ($style_attributes) echo 'style="' . $style_attributes . '"'; ?>>
    <?php if ($enable_background && $background_overlay) { ?>
        <div class="bt-titlebar-overlay" style="background-color:<?php echo esc_attr($background_overlay); ?>"></div>
    <?php } ?>

    <div class="bt-container">
        <div class="bt-page-titlebar">
            <div class="bt-page-titlebar--breadcrumb">
                <?php
                $home_text = 'Home';
                $delimiter = '/';
                echo somnia_page_breadcrumb($home_text, $delimiter);
                ?>
            </div>

            <div class="bt-page-titlebar--content">
                <h1 class="bt-page-titlebar--title" data-original-title="<?php echo esc_attr(somnia_page_title()); ?>"><?php echo esc_html($page_title); ?></h1>

                <?php if ($enable_description) { ?>
                    <div class="bt-page-titlebar--description" data-original-description="<?php echo esc_attr(isset($shop_title_bar['description_shop']) ? $shop_title_bar['description_shop'] : ''); ?>">
                        <?php if (!empty($description_shop)) echo wp_kses_post($description_shop); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<?php
// Display section bottom if enabled
if ($enable_section_bottom && $section_bottom && !empty($section_bottom->ID)) {
    echo '<div class="bt-shop-titlebar-section-bottom">';
    echo do_shortcode('[elementor-template id="' . $section_bottom->ID . '"]');
    echo '</div>';
}
?>