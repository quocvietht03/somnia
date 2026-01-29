<?php
$post_id = get_the_ID();
$category = get_the_terms($post_id, 'category');
?>
<article <?php post_class('bt-post'); ?>>
  <div class="bt-post--inner">
  <?php echo somnia_post_cover_featured_render('medium_large'); ?>
    <div class="bt-post--content">
      <div class="bt-post--category">
        <?php
        if (!empty($category) && is_array($category)) {
          $first_category = reset($category);
          echo '<a href="'.esc_url(get_category_link($first_category->term_id)).'">'.esc_html($first_category->name).'</a>';
        }
        ?>
      </div>

      <?php echo somnia_post_title_render(); ?>
    </div>
    <div class="bt-post--info">
        <?php
        echo somnia_post_publish_render();
        echo somnia_author_icon_render();
        ?>
      </div>
  </div>
</article>