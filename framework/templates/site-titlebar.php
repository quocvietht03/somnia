<?php

/**
 * Site Titlebar
 *  
 */
if (function_exists('get_field')) {
  $background_color = get_field('title_bar_bg_color', 'options') ?: '';
  $background_image = get_field('title_bar_bg_images', 'options') ?: '';
  $title_bar_bg_overlay = get_field('title_bar_bg_overlay', 'options') ?: '';
} else {
  $background_color = '';
  $background_image = '';
  $title_bar_bg_overlay = '';
}
$style_attributes = '';
if ($background_color || $background_image) {
  $style_parts = [];
  if ($background_color) {
    $style_parts[] = 'background-color: ' . esc_attr($background_color) . ';';
  }
  if ($background_image) {
    $style_parts[] = 'background-image: url(' . esc_url($background_image['url']) . ');';
  }
  $style_attributes = implode(' ', $style_parts);
}
?>

<section class="bt-site-titlebar" <?php echo 'style="' . $style_attributes . '"'; ?>>
  <?php if ($title_bar_bg_overlay) {
    echo '<div class="bt-titlebar-overlay" style="background-color:' . esc_attr($title_bar_bg_overlay) . '"></div>';
  } ?>
  <div class="bt-container">
    <div class="bt-page-titlebar">
      <div class="bt-page-titlebar--breadcrumb">
        <?php
        $home_text = 'Home';
        $delimiter = '/';
        echo somnia_page_breadcrumb($home_text, $delimiter);
        ?>
      </div>
      <h1 class="bt-page-titlebar--title"><?php echo somnia_page_title(); ?></h1>
    </div>
  </div>
</section>