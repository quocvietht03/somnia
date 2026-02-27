<?php

/**
 * Block Name: Widget - Author Post
 * 
 **/


// Get the current post author
$author_id = get_post_field('post_author', get_the_ID());
if (empty($author_id)) {
    $current_user = wp_get_current_user();
    $author_id = $current_user->ID;
}
$author_name = get_the_author_meta('display_name', $author_id);
$author_bio = get_the_author_meta('description', $author_id);

// Get author custom fields from ACF
$author_avatar = get_field('avatar', 'user_' . $author_id);
$author_job = get_field('job', 'user_' . $author_id);
$author_socials = get_field('socials', 'user_' . $author_id);


// Fallback to WordPress avatar if no custom avatar
if (!$author_avatar) {
    $avatar_url = get_avatar_url($author_id, array('size' => 150));
} else {
    $avatar_url = $author_avatar['sizes']['thumbnail'] ?? $author_avatar['url'];
}

?>
<div id="<?php echo 'bt_block--' . $block['id']; ?>" class="widget widget-block bt-block-author-post">
    <div class="bt-author-profile">
        <div class="bt-author-avatar">
            <div class="bt-cover-image">
                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($author_name); ?>" />
            </div>
        </div>
        <div class="bt-author-header">
            <h3 class="bt-author-name"><?php echo esc_html($author_name); ?></h3>
            <?php if ($author_job) : ?>
                <div class="bt-author-job">
                    <?php echo esc_html($author_job); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="bt-author-info">
        <?php if ($author_bio) : ?>
            <div class="bt-author-bio">
                <p><?php echo esc_html($author_bio); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($author_socials && is_array($author_socials)) : ?>
            <div class="bt-author-socials">
                <?php foreach ($author_socials as $social) :
                    $social_type = $social['social'];
                    $social_link = $social['link'];

                    // Map social types to icons
                    $social_icons = array(
                        'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><g clip-path="url(#clip0_4099_5403)"><path d="M6.25 11.25L8.75 8.75L11.25 11.25L13.75 8.75" stroke="#183F91" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.24382 16.4912C7.81923 17.403 9.67248 17.7108 11.458 17.3571C13.2436 17.0034 14.8396 16.0124 15.9484 14.5689C17.0573 13.1253 17.6033 11.3278 17.4847 9.51146C17.3662 7.69508 16.5911 5.98381 15.304 4.69671C14.0169 3.4096 12.3056 2.63451 10.4892 2.51594C8.67284 2.39737 6.87533 2.94341 5.43182 4.05227C3.98831 5.16113 2.99733 6.75711 2.64363 8.54266C2.28993 10.3282 2.59766 12.1814 3.50944 13.7569L2.5321 16.6748C2.49538 16.785 2.49005 16.9031 2.51671 17.0161C2.54337 17.1291 2.60097 17.2324 2.68306 17.3145C2.76514 17.3966 2.86847 17.4542 2.98145 17.4808C3.09443 17.5075 3.2126 17.5022 3.32273 17.4655L6.24382 16.4912Z" stroke="#183F91" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_4099_5403"><rect width="20" height="20" fill="white"/></clipPath></defs></svg>',
                        'twitter'   => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><g clip-path="url(#clip0_4099_5408)"><path d="M3.75 3.125H7.5L16.25 16.875H12.5L3.75 3.125Z" stroke="#183F91" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.89687 11.2109L3.75 16.8727" stroke="#183F91" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.2484 3.125L11.1016 8.78672" stroke="#183F91" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_4099_5408"><rect width="20" height="20" fill="white"/></clipPath></defs></svg>',
                        'linkedin'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
                        'google'    => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>',
                        'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><g clip-path="url(#clip0_4099_5414)"><path d="M10 13.125C11.7259 13.125 13.125 11.7259 13.125 10C13.125 8.27411 11.7259 6.875 10 6.875C8.27411 6.875 6.875 8.27411 6.875 10C6.875 11.7259 8.27411 13.125 10 13.125Z" stroke="#183F91" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M13.75 2.5H6.25C4.17893 2.5 2.5 4.17893 2.5 6.25V13.75C2.5 15.8211 4.17893 17.5 6.25 17.5H13.75C15.8211 17.5 17.5 15.8211 17.5 13.75V6.25C17.5 4.17893 15.8211 2.5 13.75 2.5Z" stroke="#183F91" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M14.0625 6.71875C14.494 6.71875 14.8438 6.36897 14.8438 5.9375C14.8438 5.50603 14.494 5.15625 14.0625 5.15625C13.631 5.15625 13.2812 5.50603 13.2812 5.9375C13.2812 6.36897 13.631 6.71875 14.0625 6.71875Z" fill="#183F91"/></g><defs><clipPath id="clip0_4099_5414"><rect width="20" height="20" fill="white"/></clipPath></defs></svg>',
                        'telegram'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><g clip-path="url(#clip0_4099_5425)"><path d="M6.24939 10.5366L13.301 16.7186C13.3822 16.7903 13.4806 16.8396 13.5867 16.8618C13.6927 16.8839 13.8027 16.8781 13.9058 16.845C14.0089 16.8118 14.1016 16.7524 14.1749 16.6726C14.2481 16.5928 14.2994 16.4953 14.3236 16.3897L17.4994 2.59521C17.5025 2.58138 17.5018 2.56696 17.4973 2.55351C17.4928 2.54006 17.4848 2.52807 17.474 2.51884C17.4633 2.50961 17.4502 2.50348 17.4362 2.5011C17.4223 2.49873 17.4079 2.5002 17.3947 2.50537L1.56189 8.70146C1.4636 8.73929 1.38023 8.80798 1.3243 8.89722C1.26837 8.98646 1.2429 9.09143 1.2517 9.19638C1.26051 9.30133 1.30312 9.4006 1.37313 9.47927C1.44315 9.55794 1.5368 9.61178 1.64001 9.63271L6.24939 10.5366Z" stroke="#183F91" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.25 10.5375L17.4539 2.50781" stroke="#183F91" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.71641 13.5789L7.325 16.0602C7.23859 16.1498 7.12737 16.2116 7.00561 16.2376C6.88384 16.2636 6.75708 16.2527 6.64157 16.2062C6.52607 16.1597 6.42709 16.0797 6.35732 15.9766C6.28755 15.8735 6.25018 15.7519 6.25 15.6273V10.5391" stroke="#183F91" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_4099_5425"><rect width="20" height="20" fill="white"/></clipPath></defs></svg>'
                    );

                    $icon = $social_icons[$social_type] ?? '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>';
                ?>
                    <a href="<?php echo esc_url($social_link); ?>" target="_blank" rel="noopener noreferrer" class="bt-social-link bt-social-<?php echo esc_attr($social_type); ?>">
                        <?php echo $icon; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>