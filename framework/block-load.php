<?php
function somnia_register_layout_category($categories)
{

    $categories[] = array(
        'slug'  => 'bt-custom-block',
        'title' => 'Custom Block'
    );

    return $categories;
}
add_filter('block_categories_all', 'somnia_register_layout_category');

function somnia_acf_init()
{

    // check function exists
    if (function_exists('acf_register_block')) {
        acf_register_block(array(
            'name'              => 'widget-recent-posts',
            'title'             => __('Widget - Recent Posts', 'somnia'),
            'description'       => __('Widget - Recent Posts block.', 'somnia'),
            'render_callback'   => 'somnia_acf_block_render_callback',
            // 'enqueue_assets' => 'somnia_acf_block_assets_callback',
            'category'          => 'bt-custom-block',
            'icon'              => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0V0z" /><path d="M19 13H5v-2h14v2z" /></svg>',
            'keywords'          => array('Recent Posts', 'Posts'),
        ));
        acf_register_block(array(
            'name'              => 'widget-instagram-posts',
            'title'             => __('Widget - Instagram Posts', 'somnia'),
            'description'       => __('Widget - Instagram Posts block.', 'somnia'),
            'render_callback'   => 'somnia_acf_block_render_callback',
            // 'enqueue_assets' => 'somnia_acf_block_assets_callback',
            'category'          => 'bt-custom-block',
            'icon'              => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0V0z" /><path d="M19 13H5v-2h14v2z" /></svg>',
            'keywords'          => array('Instagram Posts', 'Instagram'),
        ));
        acf_register_block(array(
            'name'              => 'widget-author-post',
            'title'             => __('Widget - Author Post', 'somnia'),
            'description'       => __('Widget - Author Post block for single posts.', 'somnia'),
            'render_callback'   => 'somnia_acf_block_render_callback',
            // 'enqueue_assets' => 'somnia_acf_block_assets_callback',
            'category'          => 'bt-custom-block',
            'icon'              => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0V0z" /><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" /></svg>',
            'keywords'          => array('Author', 'Post', 'Profile'),
            'className'         => 'widget-block bt-block-author-post',
        ));
    }
}
add_action('acf/init', 'somnia_acf_init');

function somnia_acf_block_render_callback($block)
{
    // convert name ("acf/testimonial") into path friendly slug ("testimonial")
    $slug = str_replace('acf/', '', $block['name']);

    // include a template part from within the "block-parts/block" folder
    if (file_exists(get_template_directory() . "/framework/block-parts/{$slug}.php")) {
        include get_template_directory() . "/framework/block-parts/{$slug}.php";
    }
}

function somnia_acf_block_assets_callback($block)
{

    // convert name ("acf/block-name") into path friendly slug ("block-name")
    $slug = str_replace('acf/', '', $block['name']);

    // include a template part from within the "block-parts/block" folder
    if (file_exists(get_template_directory() . "/framework/block-parts/{$slug}.css")) {
        wp_enqueue_style("block-{$slug}", get_template_directory_uri() . "/framework/block-parts/{$slug}.css");
    }
    if (file_exists(get_template_directory() . "/framework/block-parts/{$slug}.js")) {
        wp_enqueue_script("block-{$slug}", get_template_directory_uri() . "/framework/block-parts/{$slug}.js", array('jquery'), '', true);
    }
}
