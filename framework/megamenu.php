<?php

/**
 * Mega Menu - Custom Post Type & Menu Item Options
 * Create CPT for Mega Menu blocks (like extra_content_prod) and add menu item options
 */

// Register Custom Post Type
function somnia_register_megamenu_block_post_type()
{
    $labels = array(
        'name'               => __('Mega Menu Block', 'somnia'),
        'singular_name'      => __('Mega Menu Block', 'somnia'),
        'menu_name'          => __('Mega Menu', 'somnia'),
        'add_new'            => __('Add New', 'somnia'),
        'add_new_item'       => __('Add New Mega Menu Block', 'somnia'),
        'edit_item'          => __('Edit Mega Menu Block', 'somnia'),
        'new_item'           => __('New Mega Menu Block', 'somnia'),
        'view_item'          => __('View Mega Menu Block', 'somnia'),
        'search_items'       => __('Search Mega Menu Block', 'somnia'),
        'not_found'          => __('No Mega Menu Block found', 'somnia'),
        'not_found_in_trash' => __('No Mega Menu Block found in Trash', 'somnia'),
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'megamenu-block'),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 57,
        'menu_icon'           => 'dashicons-grid-view',
        'supports'            => array('title', 'editor', 'thumbnail', 'elementor'),
        'show_in_rest'        => true,
        'exclude_from_search' => true,
        'show_in_nav_menus'   => false,
    );

    register_post_type('megamenu_block', $args);
}
add_action('init', 'somnia_register_megamenu_block_post_type');

// Enable Elementor support for megamenu_block
function somnia_add_elementor_support_to_megamenu_block($post_types)
{
    $post_types[] = 'megamenu_block';
    return $post_types;
}
add_filter('elementor/documents/register/post_types', 'somnia_add_elementor_support_to_megamenu_block');

// Add Mega Menu options to menu item (only for main menu / depth 0)
function somnia_megamenu_nav_menu_item_custom_fields($item_id, $item, $depth, $args, $id = '')
{
    // Only show for main menu items (depth 0)
    if ($depth !== 0) {
        return;
    }

    $megamenu_enabled = get_post_meta($item_id, '_somnia_megamenu_enabled', true);
    $megamenu_block_id = get_post_meta($item_id, '_somnia_megamenu_block_id', true);
    $megamenu_content_width = get_post_meta($item_id, '_somnia_megamenu_content_width', true);
    if (empty($megamenu_content_width)) {
        $megamenu_content_width = 'full-width'; // Default value
    }
    $megamenu_horizontal_position = get_post_meta($item_id, '_somnia_megamenu_horizontal_position', true);
    if (empty($megamenu_horizontal_position)) {
        $megamenu_horizontal_position = 'default'; // Default value
    }

    // Get all published megamenu blocks for dropdown
    $megamenu_blocks = get_posts(array(
        'post_type'      => 'megamenu_block',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));

    $has_selected_block = $megamenu_block_id && get_post_status($megamenu_block_id) !== false;
    $edit_block_url = $has_selected_block
        ? admin_url('post.php?post=' . intval($megamenu_block_id) . '&action=elementor')
        : '';
?>
    <div class="somnia-megamenu-fields description-wide" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #ddd;">
        <p class="field-megamenu-enable description">
            <label for="edit-megamenu-enable-<?php echo esc_attr($item_id); ?>">
                <input type="checkbox"
                    id="edit-megamenu-enable-<?php echo esc_attr($item_id); ?>"
                    name="menu-item[<?php echo esc_attr($item_id); ?>][_somnia_megamenu_enabled]"
                    value="1"
                    <?php checked($megamenu_enabled, '1'); ?>
                    class="somnia-megamenu-enable" />
                <?php esc_html_e('Enable Mega Menu (only for main menu)', 'somnia'); ?>
            </label>
        </p>
        <p class="field-megamenu-block description description-wide">
            <label for="edit-megamenu-block-<?php echo esc_attr($item_id); ?>">
                <?php esc_html_e('Select block', 'somnia'); ?><br />
                <select id="edit-megamenu-block-<?php echo esc_attr($item_id); ?>"
                    name="menu-item[<?php echo esc_attr($item_id); ?>][_somnia_megamenu_block_id]"
                    class="widefat somnia-megamenu-block-select"
                    data-item-id="<?php echo esc_attr($item_id); ?>">
                    <option value=""><?php esc_html_e('— Select —', 'somnia'); ?></option>
                    <?php foreach ($megamenu_blocks as $block) : ?>
                        <option value="<?php echo esc_attr($block->ID); ?>"
                            <?php selected($megamenu_block_id, $block->ID); ?>>
                            <?php echo esc_html($block->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </p>
        <p class="field-megamenu-content-width description description-wide">
            <label for="edit-megamenu-content-width-<?php echo esc_attr($item_id); ?>">
                <?php esc_html_e('Content Width', 'somnia'); ?><br />
                <select id="edit-megamenu-content-width-<?php echo esc_attr($item_id); ?>"
                    name="menu-item[<?php echo esc_attr($item_id); ?>][_somnia_megamenu_content_width]"
                    class="widefat somnia-megamenu-content-width-select"
                    data-item-id="<?php echo esc_attr($item_id); ?>">
                    <option value="full-width" <?php selected($megamenu_content_width, 'full-width'); ?>>
                        <?php esc_html_e('Full Width', 'somnia'); ?>
                    </option>
                    <option value="fit-to-content" <?php selected($megamenu_content_width, 'fit-to-content'); ?>>
                        <?php esc_html_e('Fit to Content', 'somnia'); ?>
                    </option>
                </select>
            </label>
        </p>
        <p class="field-megamenu-horizontal-position description description-wide somnia-megamenu-horizontal-position-field"
            style="<?php echo $megamenu_content_width === 'fit-to-content' ? '' : 'display: none;'; ?>">
            <label for="edit-megamenu-horizontal-position-<?php echo esc_attr($item_id); ?>">
                <?php esc_html_e('Content Horizontal Position', 'somnia'); ?><br />
                <select id="edit-megamenu-horizontal-position-<?php echo esc_attr($item_id); ?>"
                    name="menu-item[<?php echo esc_attr($item_id); ?>][_somnia_megamenu_horizontal_position]"
                    class="widefat somnia-megamenu-horizontal-position-select">
                    <option value="default" <?php selected($megamenu_horizontal_position, 'default'); ?>>
                        <?php esc_html_e('Default', 'somnia'); ?>
                    </option>
                    <option value="left" <?php selected($megamenu_horizontal_position, 'left'); ?>>
                        <?php esc_html_e('Left', 'somnia'); ?>
                    </option>
                    <option value="center" <?php selected($megamenu_horizontal_position, 'center'); ?>>
                        <?php esc_html_e('Center', 'somnia'); ?>
                    </option>
                    <option value="right" <?php selected($megamenu_horizontal_position, 'right'); ?>>
                        <?php esc_html_e('Right', 'somnia'); ?>
                    </option>
                </select>
            </label>
        </p>
        <p class="field-megamenu-links description">
            <a href="<?php echo esc_url($edit_block_url ?: '#'); ?>"
                class="somnia-megamenu-edit-link"
                data-base-edit-url="<?php echo esc_attr(admin_url('post.php?post=%id%&action=elementor')); ?>"
                target="_blank"
                style="<?php echo $has_selected_block ? '' : 'display: none;'; ?>">
                <?php esc_html_e('Edit megamenu block', 'somnia'); ?>
            </a>
            <span class="meta-sep" style="<?php echo $has_selected_block ? '' : 'display: none;'; ?>"> | </span>
            <a href="#"
                class="somnia-megamenu-add-link"
                data-item-id="<?php echo esc_attr($item_id); ?>">
                <?php esc_html_e('Add megamenu block', 'somnia'); ?>
            </a>
        </p>
    </div>
<?php
}
add_action('wp_nav_menu_item_custom_fields', 'somnia_megamenu_nav_menu_item_custom_fields', 10, 5);

// Save mega menu options when menu is saved
function somnia_megamenu_save_nav_menu_item($menu_id, $menu_item_db_id, $args)
{
    if (!isset($_POST['menu-item'][$menu_item_db_id])) {
        return;
    }

    $menu_item_data = $_POST['menu-item'][$menu_item_db_id];

    // Save mega menu enabled
    $megamenu_enabled = isset($menu_item_data['_somnia_megamenu_enabled']) && $menu_item_data['_somnia_megamenu_enabled'] === '1' ? '1' : '0';
    update_post_meta($menu_item_db_id, '_somnia_megamenu_enabled', $megamenu_enabled);

    // Save megamenu block ID
    $megamenu_block_id = isset($menu_item_data['_somnia_megamenu_block_id']) ? absint($menu_item_data['_somnia_megamenu_block_id']) : 0;
    if ($megamenu_block_id && get_post_type($megamenu_block_id) === 'megamenu_block') {
        update_post_meta($menu_item_db_id, '_somnia_megamenu_block_id', $megamenu_block_id);
    } else {
        update_post_meta($menu_item_db_id, '_somnia_megamenu_block_id', '');
    }

    // Save megamenu content width
    $megamenu_content_width = isset($menu_item_data['_somnia_megamenu_content_width']) 
        ? sanitize_text_field($menu_item_data['_somnia_megamenu_content_width']) 
        : 'full-width';
    if (in_array($megamenu_content_width, array('full-width', 'fit-to-content'), true)) {
        update_post_meta($menu_item_db_id, '_somnia_megamenu_content_width', $megamenu_content_width);
    } else {
        update_post_meta($menu_item_db_id, '_somnia_megamenu_content_width', 'full-width');
    }

    // Save megamenu horizontal position
    $megamenu_horizontal_position = isset($menu_item_data['_somnia_megamenu_horizontal_position']) 
        ? sanitize_text_field($menu_item_data['_somnia_megamenu_horizontal_position']) 
        : 'default';
    if (in_array($megamenu_horizontal_position, array('default', 'left', 'center', 'right'), true)) {
        update_post_meta($menu_item_db_id, '_somnia_megamenu_horizontal_position', $megamenu_horizontal_position);
    } else {
        update_post_meta($menu_item_db_id, '_somnia_megamenu_horizontal_position', 'default');
    }
}
add_action('wp_update_nav_menu_item', 'somnia_megamenu_save_nav_menu_item', 10, 3);

// AJAX: Create Mega Menu Block
function somnia_ajax_create_megamenu_block()
{
    check_ajax_referer('somnia-megamenu-nonce', 'nonce');

    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error(array('message' => __('Permission denied', 'somnia')));
        return;
    }

    // Get next sequential number for block title
    $existing_blocks = get_posts(array(
        'post_type'      => 'megamenu_block',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ));
    $next_number = count($existing_blocks) + 1;

    // Create Mega Menu Block Post
    $block_post = array(
        'post_title'   => sprintf(__('Mega Menu Block %d', 'somnia'), $next_number),
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'megamenu_block',
        'post_author'  => get_current_user_id(),
    );

    $block_id = wp_insert_post($block_post);

    if (is_wp_error($block_id)) {
        wp_send_json_error(array('message' => __('Failed to create Mega Menu Block', 'somnia')));
        return;
    }

    // Enable Elementor for this post with full width template
    update_post_meta($block_id, '_elementor_edit_mode', 'builder');
    update_post_meta($block_id, '_elementor_template_type', 'wp-post');

    // Initialize empty Elementor data
    update_post_meta($block_id, '_elementor_data', '[]');
    
    if (defined('ELEMENTOR_VERSION')) {
        update_post_meta($block_id, '_elementor_version', ELEMENTOR_VERSION);
    }
    update_post_meta($block_id, '_elementor_css', '');

    $page_settings = array(
        'post_status' => 'publish',
        'template' => 'elementor_canvas',
        'hide_title' => 'yes',
    );
    update_post_meta($block_id, '_elementor_page_settings', $page_settings);
    update_post_meta($block_id, '_wp_page_template', 'elementor_canvas');

    $edit_link = admin_url('post.php?post=' . $block_id . '&action=elementor');
    $block_title = get_the_title($block_id);

    wp_send_json_success(array(
        'message' => __('Mega Menu Block created successfully!', 'somnia'),
        'block_id' => $block_id,
        'block_title' => $block_title,
        'edit_link' => $edit_link,
    ));
}
add_action('wp_ajax_somnia_create_megamenu_block', 'somnia_ajax_create_megamenu_block');

// Ensure Elementor settings on publish (like extra_content_prod)
function somnia_megamenu_ensure_elementor_settings_on_publish($new_status, $old_status, $post)
{
    if ($post->post_type !== 'megamenu_block') {
        return;
    }

    if ($new_status === 'publish' && $old_status !== 'publish') {
        $page_settings = get_post_meta($post->ID, '_elementor_page_settings', true);

        if (empty($page_settings) || !is_array($page_settings)) {
            $page_settings = array(
                'post_status' => 'publish',
                'template' => 'elementor_canvas',
                'hide_title' => 'yes',
            );
            update_post_meta($post->ID, '_elementor_page_settings', $page_settings);
        }

        $template = get_post_meta($post->ID, '_wp_page_template', true);
        if (empty($template)) {
            update_post_meta($post->ID, '_wp_page_template', 'elementor_canvas');
        }

        $edit_mode = get_post_meta($post->ID, '_elementor_edit_mode', true);
        if (empty($edit_mode)) {
            update_post_meta($post->ID, '_elementor_edit_mode', 'builder');
        }

        if (class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }
    }
}
add_action('transition_post_status', 'somnia_megamenu_ensure_elementor_settings_on_publish', 10, 3);

/**
 * Helper: Get mega menu block ID for a menu item
 *
 * @param int $menu_item_id Menu item post ID
 * @return int|false Mega menu block ID or false
 */
function somnia_get_megamenu_block_id($menu_item_id)
{
    $enabled = get_post_meta($menu_item_id, '_somnia_megamenu_enabled', true);
    if ($enabled !== '1') {
        return false;
    }

    $block_id = get_post_meta($menu_item_id, '_somnia_megamenu_block_id', true);
    if (!$block_id || get_post_status($block_id) !== 'publish' || get_post_type($block_id) !== 'megamenu_block') {
        return false;
    }

    return (int) $block_id;
}

/**
 * Helper: Display mega menu block content (for use in walker or template)
 *
 * @param int $block_id Mega menu block post ID
 * @return void
 */
function somnia_display_megamenu_block($block_id)
{
    if (!$block_id || get_post_status($block_id) !== 'publish') {
        return;
    }

    if (class_exists('\Elementor\Plugin')) {
        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($block_id);
    } else {
        $post = get_post($block_id);
        if ($post) {
            echo apply_filters('the_content', $post->post_content);
        }
    }
}
