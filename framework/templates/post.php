<?php
$layout = !empty($args['layout']) ? $args['layout'] : 'layout-default';
?>
<article <?php post_class('bt-post'); ?>>
    <?php if ($layout != 'layout-02') : ?>
        <div class="bt-post--infor">
            <?php
            echo somnia_post_category_render();
            echo is_single() ? somnia_single_post_title_render() : somnia_post_title_render();
            echo somnia_post_meta_single_render();
            ?>
        </div>
    <?php endif; ?>

    <?php
    if ($layout === 'layout-default') {
        echo somnia_post_featured_render();
    }
    echo somnia_post_content_render();
    ?>
</article>