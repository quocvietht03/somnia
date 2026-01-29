<?php

/**
 * Extra Content Product - Custom Post Type & Meta Box
 * Create CPT for Extra Content and buttons in Product admin
 */

// Register Custom Post Type
function somnia_register_extra_content_post_type()
{
    $labels = array(
        'name'               => __('Product Extra Content', 'somnia'),
        'singular_name'      => __('Product Extra Content', 'somnia'),
        'menu_name'          => __('Product Extra Content', 'somnia'),
        'add_new'            => __('Add New', 'somnia'),
        'add_new_item'       => __('Add New Extra Content', 'somnia'),
        'edit_item'          => __('Edit Extra Content', 'somnia'),
        'new_item'           => __('New Extra Content', 'somnia'),
        'view_item'          => __('View Extra Content', 'somnia'),
        'search_items'       => __('Search Extra Content', 'somnia'),
        'not_found'          => __('No Extra Content found', 'somnia'),
        'not_found_in_trash' => __('No Extra Content found in Trash', 'somnia'),
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => false,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'extra-content-product'),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 56,
        'menu_icon'           => 'dashicons-editor-kitchensink',
        'supports'            => array('title', 'editor', 'thumbnail', 'elementor'),
        'show_in_rest'        => true, // CRITICAL: Required for Elementor REST API
        'exclude_from_search' => true,
    );

    register_post_type('extra_content_prod', $args);
}
add_action('init', 'somnia_register_extra_content_post_type');

// Enable Elementor support for this post type
function somnia_add_elementor_support_to_extra_content($post_types)
{
    $post_types[] = 'extra_content_prod';
    return $post_types;
}
add_filter('elementor/documents/register/post_types', 'somnia_add_elementor_support_to_extra_content');

// Add Meta Box to Product Edit Screen (below short description)
function somnia_add_extra_content_meta_box()
{
    add_meta_box(
        'somnia_extra_content_box',
        __('Extra Content Settings', 'somnia'),
        'somnia_extra_content_meta_box_callback',
        'product',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'somnia_add_extra_content_meta_box');

// Move Extra Content meta box below excerpt
function somnia_reorder_extra_content_meta_box() {
    global $wp_meta_boxes;

    if (!isset($wp_meta_boxes['product']['normal']['default']['somnia_extra_content_box'])) {
        return;
    }

    $extra_content_box = $wp_meta_boxes['product']['normal']['default']['somnia_extra_content_box'];
    unset($wp_meta_boxes['product']['normal']['default']['somnia_extra_content_box']);

    $new_order = array();
    $inserted  = false;

    foreach ($wp_meta_boxes['product']['normal']['default'] as $key => $box) {
        $new_order[$key] = $box;

        if ($key === 'postexcerpt') {
            $new_order['somnia_extra_content_box'] = $extra_content_box;
            $inserted = true;
        }
    }

    // If postexcerpt not found, add to end of list
    if (!$inserted) {
        $new_order['somnia_extra_content_box'] = $extra_content_box;
    }

    $wp_meta_boxes['product']['normal']['default'] = $new_order;
}
add_action('add_meta_boxes', 'somnia_reorder_extra_content_meta_box', 100);

// Meta Box callback display
function somnia_extra_content_meta_box_callback($post)
{
    wp_nonce_field('somnia_extra_content_nonce', 'somnia_extra_content_nonce');

    // Get linked Extra Content post ID
    $extra_content_id = get_post_meta($post->ID, '_extra_content_post_id', true);

?>
    <div class="somnia-extra-content-box">
        <?php if ($extra_content_id && get_post_status($extra_content_id) !== false):
            $edit_link = admin_url('post.php?post=' . $extra_content_id . '&action=elementor');
        ?>
            <p class="somnia-extra-status">
                <span class="dashicons dashicons-yes-alt"></span>
                <?php _e('Extra Content Created', 'somnia'); ?>
            </p>

            <p>
                <a href="<?php echo esc_url($edit_link); ?>"
                    class="button button-primary button-large somnia-edit-extra-content"
                    target="_blank">
                    <span class="dashicons dashicons-edit"></span>
                    <?php _e('Edit Extra Content', 'somnia'); ?>
                </a>
            </p>

            <p>
                <button type="button"
                    class="button button-link-delete somnia-delete-extra-content"
                    data-product-id="<?php echo esc_attr($post->ID); ?>"
                    data-extra-id="<?php echo esc_attr($extra_content_id); ?>">
                    <span class="dashicons dashicons-trash"></span>
                    <?php _e('Delete Extra Content', 'somnia'); ?>
                </button>
            </p>

        <?php else: ?>
            <p class="somnia-extra-status">
                <span class="dashicons dashicons-info"></span>
                <?php _e('No Extra Content Yet', 'somnia'); ?>
            </p>

            <p>
                <button type="button"
                    class="button button-primary button-large somnia-create-extra-content"
                    data-product-id="<?php echo esc_attr($post->ID); ?>">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php _e('Create Extra Content', 'somnia'); ?>
                </button>
            </p>
        <?php endif; ?>

        <input type="hidden"
            id="somnia_extra_content_post_id"
            name="somnia_extra_content_post_id"
            value="<?php echo esc_attr($extra_content_id); ?>" />

        <div class="somnia-extra-loading" style="display:none;">
            <span class="spinner is-active"></span>
            <p><?php _e('Processing...', 'somnia'); ?></p>
        </div>
    </div>
    <?php
}

// Save meta when saving product
function somnia_save_extra_content_meta($post_id)
{
    // Check nonce
    if (
        !isset($_POST['somnia_extra_content_nonce']) ||
        !wp_verify_nonce($_POST['somnia_extra_content_nonce'], 'somnia_extra_content_nonce')
    ) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save extra content post ID if exists
    if (isset($_POST['somnia_extra_content_post_id'])) {
        update_post_meta($post_id, '_extra_content_post_id', sanitize_text_field($_POST['somnia_extra_content_post_id']));
    }
}
add_action('save_post_product', 'somnia_save_extra_content_meta');

// AJAX: Create Extra Content Post
function somnia_ajax_create_extra_content()
{
    check_ajax_referer('somnia-extra-content-nonce', 'nonce');

    if (!current_user_can('edit_products')) {
        wp_send_json_error(array('message' => __('Permission denied', 'somnia')));
        return;
    }

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if (!$product_id || get_post_type($product_id) !== 'product') {
        wp_send_json_error(array('message' => __('Invalid Product ID', 'somnia')));
        return;
    }

    // Check if Extra Content already exists
    $existing_extra = get_post_meta($product_id, '_extra_content_post_id', true);
    if ($existing_extra && get_post_status($existing_extra) !== false) {
        wp_send_json_error(array('message' => __('Extra Content already exists for this product', 'somnia')));
        return;
    }

    $product = wc_get_product($product_id);
    $product_title = $product ? $product->get_name() : 'Product #' . $product_id;

    // Create Extra Content Post
    $extra_post = array(
        'post_title'   => sprintf(__('Extra Content - %s', 'somnia'), $product_title),
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'extra_content_prod',
        'post_author'  => get_current_user_id(),
    );

    $extra_content_id = wp_insert_post($extra_post);

    if (is_wp_error($extra_content_id)) {
        wp_send_json_error(array('message' => __('Failed to create Extra Content', 'somnia')));
        return;
    }

    // Save relationship between Product and Extra Content
    update_post_meta($product_id, '_extra_content_post_id', $extra_content_id);
    update_post_meta($extra_content_id, '_parent_product_id', $product_id);

    // Enable Elementor for this post with full width template
    update_post_meta($extra_content_id, '_elementor_edit_mode', 'builder');
    update_post_meta($extra_content_id, '_elementor_template_type', 'wp-post');

    // Initialize empty Elementor data FIRST
    update_post_meta($extra_content_id, '_elementor_data', '[]');
    
    // Check if Elementor constant exists
    if (defined('ELEMENTOR_VERSION')) {
        update_post_meta($extra_content_id, '_elementor_version', ELEMENTOR_VERSION);
    }
    update_post_meta($extra_content_id, '_elementor_css', '');

    // IMPORTANT: _elementor_page_settings must be an array, not JSON string
    $page_settings = array(
        'post_status' => 'publish',
        'template' => 'elementor_canvas', // Full width template
        'hide_title' => 'yes',
    );
    update_post_meta($extra_content_id, '_elementor_page_settings', $page_settings);

    // Ensure WordPress template is set
    update_post_meta($extra_content_id, '_wp_page_template', 'elementor_canvas');

    $edit_link = admin_url('post.php?post=' . $extra_content_id . '&action=elementor');

    wp_send_json_success(array(
        'message' => __('Extra Content created successfully!', 'somnia'),
        'extra_content_id' => $extra_content_id,
        'edit_link' => $edit_link,
    ));
}
add_action('wp_ajax_somnia_create_extra_content', 'somnia_ajax_create_extra_content');

// AJAX: Delete Extra Content Post
function somnia_ajax_delete_extra_content()
{
    check_ajax_referer('somnia-extra-content-nonce', 'nonce');

    if (!current_user_can('delete_products')) {
        wp_send_json_error(array('message' => __('Permission denied', 'somnia')));
        return;
    }

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $extra_id = isset($_POST['extra_id']) ? intval($_POST['extra_id']) : 0;

    if (!$product_id || !$extra_id) {
        wp_send_json_error(array('message' => __('Invalid ID', 'somnia')));
        return;
    }

    // Verify Extra Content belongs to Product
    $linked_product = get_post_meta($extra_id, '_parent_product_id', true);
    if ($linked_product != $product_id) {
        wp_send_json_error(array('message' => __('Extra Content does not belong to this Product', 'somnia')));
        return;
    }

    // Verify the extra content post exists
    if (get_post_type($extra_id) !== 'extra_content_prod') {
        wp_send_json_error(array('message' => __('Invalid Extra Content', 'somnia')));
        return;
    }

    // Delete Extra Content Post permanently
    $deleted = wp_delete_post($extra_id, true);

    if (!$deleted) {
        wp_send_json_error(array('message' => __('Failed to delete Extra Content', 'somnia')));
        return;
    }

    // Remove meta from Product
    delete_post_meta($product_id, '_extra_content_post_id');

    wp_send_json_success(array(
        'message' => __('Extra Content deleted successfully!', 'somnia'),
    ));
}
add_action('wp_ajax_somnia_delete_extra_content', 'somnia_ajax_delete_extra_content');

// Add column in Product list table
function somnia_add_extra_content_column($columns)
{
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'product_tag') {
            $new_columns['extra_content'] = __('Extra Content', 'somnia');
        }
    }
    return $new_columns;
}
add_filter('manage_product_posts_columns', 'somnia_add_extra_content_column');

// Display column content
function somnia_extra_content_column_content($column, $post_id)
{
    if ($column === 'extra_content') {
        $extra_content_id = get_post_meta($post_id, '_extra_content_post_id', true);
        if ($extra_content_id && get_post_status($extra_content_id) !== false) {
            $edit_link = admin_url('post.php?post=' . intval($extra_content_id) . '&action=elementor');
            echo '<a href="' . esc_url($edit_link) . '" target="_blank" class="button button-small">';
            echo '<span class="dashicons dashicons-yes-alt" style="color: green;"></span> ';
            echo esc_html__('Edit', 'somnia');
            echo '</a>';
        } else {
            echo '<span class="dashicons dashicons-minus" style="color: #ccc;"></span>';
        }
    }
}
add_action('manage_product_posts_custom_column', 'somnia_extra_content_column_content', 10, 2);

/**
 * Helper function: Display Extra Content in single product
 * Use in template to simplify code
 * 
 * @param int $product_id Product ID (optional, defaults to current post ID)
 * @return void
 */
function somnia_display_product_extra_content($product_id = null)
{
    if (!$product_id) {
        $product_id = get_the_ID();
    }

    $extra_content_id = get_post_meta($product_id, '_extra_content_post_id', true);

    if (!$extra_content_id || get_post_status($extra_content_id) !== 'publish') {
        return;
    }

    // Check if Elementor is active
    if (class_exists('\Elementor\Plugin')) {
        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($extra_content_id);  
    } else {
        // Fallback if Elementor is not available
        $extra_post = get_post($extra_content_id);
        if ($extra_post) {
            echo apply_filters('the_content', $extra_post->post_content);
        }
    }
}

/**
 * Output Extra Content before Related Products via hook
 * Hooked into woocommerce_after_single_product_summary (priority 18)
 * and somnia_woocommerce_template_related_products (priority 18)
 */
function somnia_output_product_extra_content()
{
    if (!is_product()) {
        return;
    }

    $extra_content_id = get_post_meta(get_the_ID(), '_extra_content_post_id', true);

    if (!$extra_content_id || get_post_status($extra_content_id) !== 'publish') {
        return;
    }

    echo '<div class="bt-product-extra-content">';
    somnia_display_product_extra_content();
    echo '</div>';
}
add_action('woocommerce_after_single_product_summary', 'somnia_output_product_extra_content', 18);
add_action('somnia_woocommerce_template_related_products', 'somnia_output_product_extra_content', 18);

// Display notice in Extra Content editor about linked product
function somnia_extra_content_admin_notice()
{
    $screen = get_current_screen();

    if ($screen && $screen->post_type === 'extra_content_prod' && $screen->base === 'post') {
        global $post;
        if ($post) {
            $parent_product_id = get_post_meta($post->ID, '_parent_product_id', true);
            if ($parent_product_id && get_post_status($parent_product_id) !== false) {
                $product = wc_get_product($parent_product_id);
                $product_title = $product ? $product->get_name() : 'Product #' . $parent_product_id;
                $product_edit_link = admin_url('post.php?post=' . intval($parent_product_id) . '&action=edit');
                $product_view_link = get_permalink($parent_product_id);

    ?>
                <div class="notice notice-info extra_content_prod-info-notice">
                    <p><strong><?php esc_html_e('📦 Linked to Product:', 'somnia'); ?></strong></p>
                    <p>
                        <?php esc_html_e('This Extra Content will be displayed in:', 'somnia'); ?>
                        <a href="<?php echo esc_url($product_edit_link); ?>" target="_blank">
                            <strong><?php echo esc_html($product_title); ?></strong>
                        </a>
                    </p>
                    <p>
                        <a href="<?php echo esc_url($product_view_link); ?>" class="button button-small" target="_blank">
                            <?php esc_html_e('View Product', 'somnia'); ?>
                        </a>
                        <a href="<?php echo esc_url($product_edit_link); ?>" class="button button-small" target="_blank">
                            <?php esc_html_e('Edit Product', 'somnia'); ?>
                        </a>
                    </p>
                </div>
            <?php
            }
        }
    }
}
add_action('admin_notices', 'somnia_extra_content_admin_notice');

// Hide Extra Content from main menu (optional - uncomment to hide)
function somnia_hide_extra_content_from_menu()
{
    // Uncomment the line below to hide from menu
    // remove_menu_page('edit.php?post_type=extra_content_prod');
}
add_action('admin_menu', 'somnia_hide_extra_content_from_menu', 999);

// Ensure Elementor settings are preserved when publishing
function somnia_ensure_elementor_settings_on_publish($new_status, $old_status, $post)
{
    // Only for extra_content_prod post type
    if ($post->post_type !== 'extra_content_prod') {
        return;
    }

    // When transitioning to publish
    if ($new_status === 'publish' && $old_status !== 'publish') {
        // Ensure page settings is an array
        $page_settings = get_post_meta($post->ID, '_elementor_page_settings', true);

        if (empty($page_settings) || !is_array($page_settings)) {
            $page_settings = array(
                'post_status' => 'publish',
                'template' => 'elementor_canvas',
                'hide_title' => 'yes',
            );
            update_post_meta($post->ID, '_elementor_page_settings', $page_settings);
        }

        // Ensure template is set
        $template = get_post_meta($post->ID, '_wp_page_template', true);
        if (empty($template)) {
            update_post_meta($post->ID, '_wp_page_template', 'elementor_canvas');
        }

        // Ensure edit mode is builder
        $edit_mode = get_post_meta($post->ID, '_elementor_edit_mode', true);
        if (empty($edit_mode)) {
            update_post_meta($post->ID, '_elementor_edit_mode', 'builder');
        }

        // Clear Elementor cache
        if (class_exists('\Elementor\Plugin')) {
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }
    }
}
add_action('transition_post_status', 'somnia_ensure_elementor_settings_on_publish', 10, 3);

// Admin action to flush rewrite rules manually
function somnia_extra_content_flush_rules_action()
{
    if (isset($_GET['somnia_flush_extra_content']) && current_user_can('manage_options')) {
        check_admin_referer('somnia_flush_extra_content');

        flush_rewrite_rules();

        wp_redirect(admin_url('edit.php?post_type=extra_content_prod&flushed=1'));
        exit;
    }
}
add_action('admin_init', 'somnia_extra_content_flush_rules_action');

// Show admin notice with flush button if needed
function somnia_extra_content_flush_notice()
{
    $screen = get_current_screen();

    if ($screen && $screen->post_type === 'extra_content_prod') {
        if (isset($_GET['flushed']) && $_GET['flushed'] === '1') {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong><?php esc_html_e('Rewrite rules flushed successfully!', 'somnia'); ?></strong></p>
            </div>
        <?php
        } else {
            $flush_url = wp_nonce_url(
                admin_url('edit.php?post_type=extra_content_prod&somnia_flush_extra_content=1'),
                'somnia_flush_extra_content'
            );
        ?>
            <div class="notice notice-info">
                <p>
                    <?php esc_html_e('If you experience issues with Extra Content posts:', 'somnia'); ?>
                    <a href="<?php echo esc_url($flush_url); ?>" class="button button-small" style="margin-left: 10px;">
                        <?php esc_html_e('Flush Rewrite Rules', 'somnia'); ?>
                    </a>
                </p>
            </div>
<?php
        }
    }
}
add_action('admin_notices', 'somnia_extra_content_flush_notice');
