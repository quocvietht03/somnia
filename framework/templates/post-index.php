<?php
$post_id = get_the_ID();
$category = get_the_terms($post_id, 'category');
?>
<article <?php post_class('bt-post'); ?>>
<div class="bt-post--inner">
    <?php echo somnia_post_cover_featured_render($args['image-size']); ?>
    <div class="bt-post--content">
      <?php 
      echo somnia_post_meta_render();
      echo somnia_post_title_render();
      echo somnia_post_excerpt_render();
      echo somnia_post_button_render('Read More');
      ?>
    </div>
  </div>
</article>