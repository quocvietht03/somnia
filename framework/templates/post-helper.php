<?php
/* Count post view. */
if (!function_exists('somnia_set_count_view')) {
  function somnia_set_count_view()
  {
    $post_id = get_the_ID();

    if (is_single() && !empty($post_id) && !isset($_COOKIE['somnia_post_view_' . $post_id])) {
      $views = get_post_meta($post_id, '_post_count_views', true);
      $views = $views ? $views : 0;
      $views++;

      update_post_meta($post_id, '_post_count_views', $views);

      /* set cookie. */
      setcookie('somnia_post_view_' . $post_id, $post_id, time() * 20, '/');
    }
  }
}
add_action('wp', 'somnia_set_count_view');

/* Post count view */
if (!function_exists('somnia_get_count_view')) {
  function somnia_get_count_view()
  {
    $post_id = get_the_ID();
    $views = get_post_meta($post_id, '_post_count_views', true);

    $views = $views ? $views : 0;
    $label = $views > 1 ? esc_html__('Views', 'somnia') : esc_html__('View', 'somnia');
    return $views . ' ' . $label;
  }
}

/* Post Reading */
if (!function_exists('somnia_reading_time_render')) {
  function somnia_reading_time_render()
  {
    $content = get_the_content();
    $word_count = str_word_count(strip_tags($content));
    $readingtime = ceil($word_count / 200);

    return '<div class="bt-reading-time">' . $readingtime . ' min read' . '</div>';
  }
}

/* Single Post Title */
if (!function_exists('somnia_single_post_title_render')) {
  function somnia_single_post_title_render()
  {
    ob_start();
?>
    <h3 class="bt-post--title">
      <?php the_title(); ?>
    </h3>
  <?php

    return ob_get_clean();
  }
}

/* Post Title */
if (!function_exists('somnia_post_title_render')) {
  function somnia_post_title_render()
  {
    ob_start();
  ?>
    <h3 class="bt-post--title">
      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>
    <?php

    return ob_get_clean();
  }
}

/* Post Featured */
if (!function_exists('somnia_post_featured_render')) {
  function somnia_post_featured_render($image_size = 'full')
  {
    ob_start();

    if (is_single()) {
    ?>
      <div class="bt-post--featured">
        <div class="bt-cover-image">
          <?php if (has_post_thumbnail()) {
            the_post_thumbnail($image_size);
          } ?>
        </div>
      </div>
    <?php
    } else {
    ?>
      <div class="bt-post--featured">
        <a href="<?php the_permalink(); ?>">
          <div class="bt-cover-image">
            <?php if (has_post_thumbnail()) {
              the_post_thumbnail($image_size);
            } ?>
          </div>
        </a>
      </div>
    <?php

    }

    return ob_get_clean();
  }
}

/* Post Cover Featured */
if (!function_exists('somnia_post_cover_featured_render')) {
  function somnia_post_cover_featured_render($image_size = 'full')
  {
    ob_start();
    ?>
    <div class="bt-post--featured">
      <a href="<?php the_permalink(); ?>">
        <div class="bt-cover-image">
          <?php
          if (has_post_thumbnail()) {
            the_post_thumbnail($image_size);
          }
          ?>
        </div>
      </a>
    </div>
  <?php

    return ob_get_clean();
  }
}

/* Post Publish */
if (!function_exists('somnia_post_publish_render')) {
  function somnia_post_publish_render($format = null)
  {
    ob_start();
    if ($format) {
      $date = get_the_date($format);
    } else {
      $date = get_the_date(get_option('date_format'));
    }
  ?>
    <div class="bt-post--publish">
      <?php echo '<span>' . $date . '</span>'; ?>
    </div>
  <?php

    return ob_get_clean();
  }
}

/* Post Short Meta */
if (!function_exists('somnia_post_short_meta_render')) {
  function somnia_post_short_meta_render()
  {
    ob_start();

  ?>
    <div class="bt-post--meta">
      <?php
      the_terms(get_the_ID(), 'category', '<div class="bt-post-cat">', ', ', '</div>');
      echo somnia_reading_time_render();
      ?>
    </div>
  <?php

    return ob_get_clean();
  }
}

/* Post Meta */
if (!function_exists('somnia_post_meta_render')) {
  function somnia_post_meta_render()
  {
    ob_start();
    $post_id = get_the_ID();
    $category = get_the_terms($post_id, 'category');
  ?>
    <div class="bt-post--meta">
      <div class="bt-post--publish">
        <?php echo get_the_date(get_option('date_format')); ?>
      </div>
      <?php if (!empty($category) && is_array($category)) {
        $first_category = reset($category); ?>
        <div class="bt-post--category">
          <a href="<?php echo esc_url(get_category_link($first_category->term_id)); ?>">
            <?php echo esc_html($first_category->name); ?>
          </a>
        </div>
      <?php } ?>
    </div>
  <?php
    return ob_get_clean();
  }
}
/* Post Meta Single Post */
if (!function_exists('somnia_post_meta_single_render')) {
  function somnia_post_meta_single_render()
  {
    ob_start();

  ?>
    <ul class="bt-post--meta">
      <li class="bt-meta bt-meta--publish">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M16.25 2.5H14.375V1.875C14.375 1.70924 14.3092 1.55027 14.1919 1.43306C14.0747 1.31585 13.9158 1.25 13.75 1.25C13.5842 1.25 13.4253 1.31585 13.3081 1.43306C13.1908 1.55027 13.125 1.70924 13.125 1.875V2.5H6.875V1.875C6.875 1.70924 6.80915 1.55027 6.69194 1.43306C6.57473 1.31585 6.41576 1.25 6.25 1.25C6.08424 1.25 5.92527 1.31585 5.80806 1.43306C5.69085 1.55027 5.625 1.70924 5.625 1.875V2.5H3.75C3.41848 2.5 3.10054 2.6317 2.86612 2.86612C2.6317 3.10054 2.5 3.41848 2.5 3.75V16.25C2.5 16.5815 2.6317 16.8995 2.86612 17.1339C3.10054 17.3683 3.41848 17.5 3.75 17.5H16.25C16.5815 17.5 16.8995 17.3683 17.1339 17.1339C17.3683 16.8995 17.5 16.5815 17.5 16.25V3.75C17.5 3.41848 17.3683 3.10054 17.1339 2.86612C16.8995 2.6317 16.5815 2.5 16.25 2.5ZM5.625 3.75V4.375C5.625 4.54076 5.69085 4.69973 5.80806 4.81694C5.92527 4.93415 6.08424 5 6.25 5C6.41576 5 6.57473 4.93415 6.69194 4.81694C6.80915 4.69973 6.875 4.54076 6.875 4.375V3.75H13.125V4.375C13.125 4.54076 13.1908 4.69973 13.3081 4.81694C13.4253 4.93415 13.5842 5 13.75 5C13.9158 5 14.0747 4.93415 14.1919 4.81694C14.3092 4.69973 14.375 4.54076 14.375 4.375V3.75H16.25V6.25H3.75V3.75H5.625ZM16.25 16.25H3.75V7.5H16.25V16.25Z" fill="currentColor" />
        </svg>
        <?php echo get_the_date(get_option('date_format')); ?>
      </li>
      <li class="bt-meta bt-meta--author">
        <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M18.0407 16.5624C16.8508 14.5054 15.0172 13.0304 12.8774 12.3312C13.9358 11.7011 14.7582 10.7409 15.2182 9.59821C15.6781 8.45548 15.7503 7.19337 15.4235 6.00568C15.0968 4.81798 14.3892 3.77039 13.4094 3.02378C12.4296 2.27716 11.2318 1.8728 10 1.8728C8.76821 1.8728 7.57044 2.27716 6.59067 3.02378C5.6109 3.77039 4.90331 4.81798 4.57654 6.00568C4.24978 7.19337 4.32193 8.45548 4.78189 9.59821C5.24186 10.7409 6.06422 11.7011 7.12268 12.3312C4.98284 13.0296 3.14925 14.5046 1.9594 16.5624C1.91577 16.6336 1.88683 16.7127 1.87429 16.7953C1.86174 16.8778 1.86585 16.962 1.88638 17.0429C1.9069 17.1238 1.94341 17.1997 1.99377 17.2663C2.04413 17.3328 2.10731 17.3886 2.17958 17.4304C2.25185 17.4721 2.33175 17.499 2.41457 17.5093C2.49738 17.5197 2.58143 17.5134 2.66176 17.4907C2.74209 17.4681 2.81708 17.4296 2.88228 17.3775C2.94749 17.3254 3.00161 17.2608 3.04143 17.1874C4.51331 14.6437 7.11487 13.1249 10 13.1249C12.8852 13.1249 15.4867 14.6437 16.9586 17.1874C16.9985 17.2608 17.0526 17.3254 17.1178 17.3775C17.183 17.4296 17.258 17.4681 17.3383 17.4907C17.4186 17.5134 17.5027 17.5197 17.5855 17.5093C17.6683 17.499 17.7482 17.4721 17.8205 17.4304C17.8927 17.3886 17.9559 17.3328 18.0063 17.2663C18.0566 17.1997 18.0932 17.1238 18.1137 17.0429C18.1342 16.962 18.1383 16.8778 18.1258 16.7953C18.1132 16.7127 18.0843 16.6336 18.0407 16.5624ZM5.62503 7.49993C5.62503 6.63464 5.88162 5.78877 6.36235 5.06931C6.84308 4.34984 7.52636 3.78909 8.32579 3.45796C9.12522 3.12682 10.0049 3.04018 10.8535 3.20899C11.7022 3.3778 12.4818 3.79448 13.0936 4.40634C13.7055 5.01819 14.1222 5.79774 14.291 6.64641C14.4598 7.49508 14.3731 8.37474 14.042 9.17417C13.7109 9.9736 13.1501 10.6569 12.4306 11.1376C11.7112 11.6183 10.8653 11.8749 10 11.8749C8.84009 11.8737 7.72801 11.4124 6.90781 10.5922C6.0876 9.77195 5.62627 8.65987 5.62503 7.49993Z" fill="currentColor" />
          </svg>
          <?php echo esc_html__('By', 'somnia') . ' ' . get_the_author(); ?>
        </a>
      </li>
    </ul>
    <?php
    return ob_get_clean();
  }
}
/* Post Category */
if (!function_exists('somnia_post_category_render')) {
  function somnia_post_category_render()
  {
    $post_id = get_the_ID();
    $categorys = get_the_terms($post_id, 'category');
    if ($categorys && !is_wp_error($categorys)) {
    ?>
      <div class="bt-post--category">
        <?php
        $category_links = array();
        foreach ($categorys as $category) {
          $category_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
        }
        echo implode(' ', $category_links);
        ?>
      </div>
    <?php
    }
  }
}
/* Post Excerpt */
if (!function_exists('somnia_post_excerpt_render')) {
  function somnia_post_excerpt_render()
  {
    ob_start();
    ?>
    <div class="bt-post--excerpt"><?php echo get_the_excerpt(); ?></div>
    <?php
    return ob_get_clean();
  }
}

/* Post Content */
if (!function_exists('somnia_post_content_render')) {
  function somnia_post_content_render()
  {
    ob_start();
    if (is_single()) {
    ?>
      <div class="bt-post--content">
        <?php
        the_content();
        wp_link_pages(array(
          'before' => '<div class="page-links">' . esc_html__('Pages:', 'somnia'),
          'after' => '</div>',
        ));
        ?>
      </div>
    <?php
    } else {
    ?>
      <div class="bt-post--excerpt"><?php echo get_the_excerpt(); ?></div>
    <?php
    }

    return ob_get_clean();
  }
}

/* Post tag */
if (!function_exists('somnia_tags_render')) {
  function somnia_tags_render()
  {
    ob_start();
    if (has_tag()) {
    ?>
      <div class="bt-post-tags">
        <span><?php esc_html_e('Tag:', 'somnia') ?></span>
        <?php
        if (has_tag()) {
          the_tags('', '', '');
        }
        ?>
      </div>
    <?php
    }
    return ob_get_clean();
  }
}

/* Post share */
if (!function_exists('somnia_share_render')) {
  function somnia_share_render()
  {

    $social_item = array();

    $social_item[] = '<li>
                        <a target="_blank" data-btIcon="fa fa-facebook" data-toggle="tooltip" title="' . esc_attr__('Facebook', 'somnia') . '" href="https://www.facebook.com/sharer/sharer.php?u=' . get_the_permalink() . '">
                       <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                          <path d="M18.125 10.001C18.1224 11.9868 17.3938 13.9031 16.0764 15.389C14.7591 16.8749 12.9438 17.8278 10.9726 18.0682C10.9287 18.0732 10.8843 18.0688 10.8422 18.0553C10.8001 18.0419 10.7614 18.0196 10.7286 17.9901C10.6957 17.9606 10.6695 17.9244 10.6516 17.884C10.6338 17.8436 10.6247 17.7999 10.625 17.7557V11.876H12.5C12.5856 11.8762 12.6704 11.8588 12.7491 11.8248C12.8278 11.7908 12.8986 11.7411 12.9572 11.6786C13.0158 11.6161 13.061 11.5422 13.0898 11.4615C13.1187 11.3808 13.1306 11.2951 13.125 11.2096C13.1112 11.0489 13.037 10.8994 12.9174 10.7911C12.7979 10.6828 12.6417 10.6238 12.4804 10.626H10.625V8.75102C10.625 8.41949 10.7567 8.10155 10.9911 7.86713C11.2255 7.63271 11.5434 7.50102 11.875 7.50102H13.125C13.2106 7.5012 13.2954 7.48377 13.3741 7.44981C13.4528 7.41584 13.5236 7.36606 13.5822 7.30357C13.6408 7.24107 13.686 7.16719 13.7148 7.08652C13.7437 7.00585 13.7556 6.9201 13.75 6.83461C13.7361 6.67362 13.6618 6.52386 13.5419 6.41556C13.422 6.30725 13.2654 6.24845 13.1039 6.25102H11.875C11.2119 6.25102 10.576 6.51441 10.1072 6.98325C9.63836 7.45209 9.37496 8.08797 9.37496 8.75102V10.626H7.49996C7.41429 10.6258 7.32948 10.6433 7.25082 10.6772C7.17216 10.7112 7.10133 10.761 7.04271 10.8235C6.9841 10.886 6.93897 10.9598 6.91011 11.0405C6.88125 11.1212 6.86929 11.2069 6.87497 11.2924C6.88879 11.4534 6.96316 11.6032 7.08306 11.7115C7.20297 11.8198 7.3595 11.8786 7.52106 11.876H9.37496V17.7573C9.37523 17.8014 9.36616 17.845 9.34836 17.8854C9.33055 17.9257 9.3044 17.9618 9.27164 17.9913C9.23887 18.0208 9.20023 18.0431 9.15826 18.0566C9.11628 18.0701 9.07192 18.0746 9.02809 18.0698C7.00415 17.8233 5.1465 16.8259 3.82283 15.2751C2.49917 13.7243 1.80597 11.7331 1.88043 9.69555C2.03668 5.4768 5.45387 2.04711 9.67575 1.88305C10.7688 1.84071 11.8591 2.01926 12.8816 2.40802C13.904 2.79678 14.8376 3.38777 15.6263 4.14562C16.4151 4.90348 17.0429 5.81264 17.4723 6.81873C17.9016 7.82482 18.1236 8.90716 18.125 10.001Z" fill="currentColor"/>
                        </svg>
                        </a>
                      </li>';
    $social_item[] = '<li>
                      <a target="_blank" data-btIcon="fa fa-twitter" data-toggle="tooltip" title="' . esc_attr__('Twitter', 'somnia') . '" href="https://twitter.com/share?url=' . get_the_permalink() . '">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M16.7972 17.1758C16.7434 17.2738 16.6642 17.3556 16.568 17.4126C16.4719 17.4696 16.3621 17.4998 16.2503 17.5H12.5003C12.3951 17.5 12.2916 17.4734 12.1995 17.4227C12.1073 17.3721 12.0294 17.2989 11.973 17.2102L8.80968 12.2391L4.2128 17.2953C4.10074 17.4157 3.94575 17.487 3.78143 17.4939C3.61712 17.5008 3.45671 17.4426 3.335 17.332C3.21329 17.2214 3.14008 17.0673 3.13125 16.903C3.12241 16.7388 3.17866 16.5777 3.2878 16.4547L8.11359 11.1422L3.22296 3.46094C3.16273 3.36644 3.12901 3.25749 3.12534 3.14548C3.12166 3.03348 3.14817 2.92255 3.20208 2.82431C3.256 2.72607 3.33533 2.64413 3.43178 2.58708C3.52823 2.53002 3.63824 2.49995 3.7503 2.5H7.5003C7.60549 2.50003 7.70897 2.52661 7.80115 2.57728C7.89334 2.62795 7.97124 2.70106 8.02765 2.78984L11.1909 7.76094L15.7878 2.70469C15.8999 2.58431 16.0549 2.51296 16.2192 2.50609C16.3835 2.49923 16.5439 2.5574 16.6656 2.66801C16.7873 2.77862 16.8605 2.93275 16.8694 3.09697C16.8782 3.2612 16.8219 3.42228 16.7128 3.54531L11.887 8.85391L16.7776 16.5398C16.8375 16.6344 16.871 16.7433 16.8744 16.8551C16.8778 16.967 16.8512 17.0777 16.7972 17.1758Z" fill="currentColor"/>
                      </svg>
                      </a>
                    </li>';
    $social_item[] = '<li>
                    <a target="_blank" data-btIcon="fa fa-instagram" data-toggle="tooltip" title="' . esc_attr__('Instagram', 'somnia') . '" href="https://www.instagram.com/sharer.php?u=' . get_the_permalink() . '">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                          <path d="M13.75 1.875H6.25C5.09006 1.87624 3.97798 2.33758 3.15778 3.15778C2.33758 3.97798 1.87624 5.09006 1.875 6.25V13.75C1.87624 14.9099 2.33758 16.022 3.15778 16.8422C3.97798 17.6624 5.09006 18.1238 6.25 18.125H13.75C14.9099 18.1238 16.022 17.6624 16.8422 16.8422C17.6624 16.022 18.1238 14.9099 18.125 13.75V6.25C18.1238 5.09006 17.6624 3.97798 16.8422 3.15778C16.022 2.33758 14.9099 1.87624 13.75 1.875ZM10 13.75C9.25832 13.75 8.5333 13.5301 7.91661 13.118C7.29993 12.706 6.81928 12.1203 6.53545 11.4351C6.25162 10.7498 6.17736 9.99584 6.32206 9.26841C6.46675 8.54098 6.8239 7.8728 7.34835 7.34835C7.8728 6.8239 8.54098 6.46675 9.26841 6.32206C9.99584 6.17736 10.7498 6.25162 11.4351 6.53545C12.1203 6.81928 12.706 7.29993 13.118 7.91661C13.5301 8.5333 13.75 9.25832 13.75 10C13.749 10.9942 13.3535 11.9475 12.6505 12.6505C11.9475 13.3535 10.9942 13.749 10 13.75ZM14.6875 6.25C14.5021 6.25 14.3208 6.19502 14.1667 6.092C14.0125 5.98899 13.8923 5.84257 13.8214 5.67127C13.7504 5.49996 13.7318 5.31146 13.768 5.1296C13.8042 4.94775 13.8935 4.7807 14.0246 4.64959C14.1557 4.51848 14.3227 4.42919 14.5046 4.39301C14.6865 4.35684 14.875 4.37541 15.0463 4.44636C15.2176 4.51732 15.364 4.63748 15.467 4.79165C15.57 4.94582 15.625 5.12708 15.625 5.3125C15.625 5.56114 15.5262 5.7996 15.3504 5.97541C15.1746 6.15123 14.9361 6.25 14.6875 6.25ZM12.5 10C12.5 10.4945 12.3534 10.9778 12.0787 11.3889C11.804 11.8 11.4135 12.1205 10.9567 12.3097C10.4999 12.4989 9.99723 12.5484 9.51227 12.452C9.02732 12.3555 8.58186 12.1174 8.23223 11.7678C7.8826 11.4181 7.6445 10.9727 7.54804 10.4877C7.45157 10.0028 7.50108 9.50011 7.6903 9.04329C7.87952 8.58648 8.19995 8.19603 8.61107 7.92133C9.0222 7.64662 9.50555 7.5 10 7.5C10.663 7.5 11.2989 7.76339 11.7678 8.23223C12.2366 8.70107 12.5 9.33696 12.5 10Z" fill="currentColor"/>
                      </svg>
                    </a>
                  </li>';
    $social_item[] = '<li>
                        <a target="_blank" data-btIcon="fa fa-pinterest" data-toggle="tooltip" title="' . esc_attr__('Pinterest', 'somnia') . '" href="https://pinterest.com/pin/create/button/?url=' . get_the_permalink() . '&media=' . wp_get_attachment_url(get_post_thumbnail_id()) . '&description=' . get_the_title() . '">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M18.7505 10.0551C18.7208 14.4683 15.1208 18.0809 10.7083 18.1246C10.0256 18.1318 9.34474 18.0531 8.68173 17.8902C8.64184 17.8803 8.60431 17.8625 8.57128 17.8381C8.53826 17.8136 8.51039 17.7828 8.48927 17.7476C8.46815 17.7123 8.4542 17.6732 8.44821 17.6325C8.44223 17.5919 8.44432 17.5504 8.45439 17.5105L9.12939 14.8113C9.78776 15.1413 10.5141 15.313 11.2505 15.3129C14.1411 15.3129 16.4614 12.6996 16.2356 9.58163C16.1744 8.77235 15.9388 7.98591 15.5449 7.2763C15.151 6.56668 14.6083 5.9507 13.9539 5.47064C13.2995 4.99058 12.549 4.65781 11.7539 4.49518C10.9587 4.33254 10.1378 4.3439 9.34749 4.52846C8.55717 4.71303 7.81614 5.06643 7.17528 5.5644C6.53442 6.06238 6.00891 6.69314 5.63483 7.41338C5.26075 8.13361 5.04695 8.92627 5.00812 9.73693C4.96928 10.5476 5.10633 11.3571 5.40985 12.1098C5.44139 12.1883 5.48854 12.2596 5.54844 12.3193C5.60835 12.3791 5.67977 12.4261 5.75837 12.4574C5.83697 12.4887 5.92112 12.5038 6.00571 12.5017C6.0903 12.4995 6.17357 12.4802 6.25048 12.4449C6.39716 12.3738 6.51117 12.2494 6.56924 12.0971C6.62732 11.9448 6.62508 11.7761 6.56298 11.6254C6.32938 11.0396 6.2252 10.4102 6.25755 9.78039C6.28991 9.15057 6.45805 8.53519 6.75045 7.97642C7.04285 7.41765 7.45259 6.9287 7.95161 6.54307C8.45062 6.15745 9.02711 5.88426 9.64156 5.74224C10.256 5.60022 10.8939 5.59272 11.5115 5.72026C12.1291 5.84779 12.7119 6.10734 13.2198 6.48113C13.7278 6.85491 14.1489 7.3341 14.4544 7.88584C14.7598 8.43758 14.9424 9.04884 14.9895 9.67772C15.1567 12.0629 13.4169 14.0629 11.2505 14.0629C10.6134 14.0626 9.98835 13.8886 9.44267 13.5598L10.6067 8.90194C10.6438 8.74227 10.6166 8.57445 10.531 8.43463C10.4455 8.29481 10.3085 8.19418 10.1495 8.15443C9.99046 8.11468 9.82219 8.13898 9.68093 8.2221C9.53966 8.30522 9.4367 8.44051 9.39423 8.59882L7.29579 16.9934C7.2844 17.0391 7.26282 17.0816 7.23266 17.1178C7.20251 17.154 7.16456 17.1829 7.12166 17.2024C7.07876 17.2218 7.03201 17.2313 6.98492 17.2302C6.93782 17.229 6.8916 17.2172 6.8497 17.1957C5.52425 16.5002 4.4167 15.452 3.64924 14.1669C2.88177 12.8818 2.48423 11.4096 2.50048 9.91288C2.54735 5.50975 6.14657 1.916 10.5474 1.87538C11.6255 1.86499 12.695 2.06929 13.6934 2.47639C14.6918 2.88348 15.5992 3.48522 16.3627 4.24654C17.1262 5.00787 17.7305 5.91354 18.1405 6.91078C18.5504 7.90802 18.7578 8.97688 18.7505 10.0551Z" fill="currentColor"/>
                          </svg>
                        </a>
                      </li>';

    ob_start();
    if (is_singular('post') && has_tag()) { ?>
      <div class="bt-post-share">
        <?php if (!empty($social_item)) {
          echo '<span>' . esc_html__('Share this post: ', 'somnia') . '</span><ul>' . implode(' ', $social_item) . '</ul>';
        } ?>
      </div>

    <?php } elseif (!empty($social_item)) { ?>

      <div class="bt-post-share">
        <span><?php echo esc_html__('Share: ', 'somnia'); ?></span>
        <ul><?php echo implode(' ', $social_item); ?></ul>
      </div>
    <?php }

    return ob_get_clean();
  }
}

/* Post Button */
if (!function_exists('somnia_post_button_render')) {
  function somnia_post_button_render($text)
  { ?>
    <div class="bt-post--button">
      <a href="<?php echo esc_url(get_permalink()) ?>">
        <span> <?php echo esc_html($text) ?> </span>
      </a>
    </div>
  <?php }
}
/* Author Icon */
if (!function_exists('somnia_author_icon_render')) {
  function somnia_author_icon_render()
  { ?>
    <div class="bt-post-author-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
        <path d="M6.66634 5.83333C6.66634 7.67428 8.15876 9.16667 9.99967 9.16667C11.8406 9.16667 13.333 7.67428 13.333 5.83333C13.333 3.99238 11.8406 2.5 9.99967 2.5C8.15876 2.5 6.66634 3.99238 6.66634 5.83333Z" stroke="#C2A74E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M9.99967 11.6667C13.2213 11.6667 15.833 14.2784 15.833 17.5001H4.16634C4.16634 14.2784 6.77801 11.6667 9.99967 11.6667Z" stroke="#C2A74E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
      <h4 class="bt-post-author-icon--name"> <?php echo esc_html__('By', 'somnia') . ' ' . get_the_author(); ?> </h4>
    </div>
  <?php }
}
/* Author with avatar */
if (!function_exists('somnia_author_w_avatar')) {
  function somnia_author_w_avatar()
  {
    $author_id = get_the_author_meta('ID');
    if (function_exists('get_field')) {
      $avatar = get_field('avatar', 'user_' . $author_id);
    } else {
      $avatar = array();
    }
  ?>
    <div class="bt-post-author-w-avatar">
      <div class="bt-post-author-w-avatar--thumbnail">
        <?php
        if (!empty($avatar)) {
          echo '<img src="' . esc_url($avatar['url']) . '" alt="' . esc_attr($avatar['title']) . '" />';
        } else {
          echo get_avatar($author_id, 150);
        }
        ?>
      </div>

      <h4 class="bt-post-author-w-avatar--name"> <span><?php echo esc_html__('By ', 'somnia') ?></span>
        <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>">
          <?php the_author(); ?>
        </a>
      </h4>
    </div>
  <?php }
}
/* Author */
if (!function_exists('somnia_author_render')) {
  function somnia_author_render()
  {
    $author_id = get_the_author_meta('ID');
    $desc = get_the_author_meta('description');

    if (function_exists('get_field')) {
      $avatar = get_field('avatar', 'user_' . $author_id);
      $job = get_field('job', 'user_' . $author_id);
      $socials = get_field('socials', 'user_' . $author_id);
    } else {
      $avatar = array();
      $job = '';
      $socials = array();
    }

    ob_start();
  ?>
    <div class="bt-post-author">
      <div class="bt-post-author--avatar">
        <?php
        if (!empty($avatar)) {
          echo '<img src="' . esc_url($avatar['url']) . '" alt="' . esc_attr($avatar['title']) . '" />';
        } else {
          echo get_avatar($author_id, 150);
        }
        ?>
      </div>
      <div class="bt-post-author--info">
        <h4 class="bt-post-author--name">
          <span class="bt-name">
            <?php the_author(); ?>
          </span>
          <?php
          if (!empty($job)) {
            echo '<span class="bt-label">' . $job . '</span>';
          }
          ?>
        </h4>
        <?php
        if (!empty($desc)) {
          echo '<div class="bt-post-author--desc">' . $desc . '</div>';
        }

        if (!empty($socials)) {
        ?>
          <div class="bt-post-author--socials">
            <?php
            foreach ($socials as $item) {
              if ($item['social'] == 'facebook') {
                echo '<a class="bt-' . esc_attr($item['social']) . '" href="' . esc_url($item['link']) . '" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">
                          <path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/>
                        </svg>
                      </a>';
              }

              if ($item['social'] == 'linkedin') {
                echo '<a class="bt-' . esc_attr($item['social']) . '" href="' . esc_url($item['link']) . '" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
                          <path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/>
                        </svg>
                      </a>';
              }

              if ($item['social'] == 'twitter') {
                echo '<a class="bt-' . esc_attr($item['social']) . '" href="' . esc_url($item['link']) . '" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                          <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/>
                        </svg>
                      </a>';
              }

              if ($item['social'] == 'google') {
                echo '<a class="bt-' . esc_attr($item['social']) . '" href="' . esc_url($item['link']) . '" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 640 512">
                          <path d="M386.061 228.496c1.834 9.692 3.143 19.384 3.143 31.956C389.204 370.205 315.599 448 204.8 448c-106.084 0-192-85.915-192-192s85.916-192 192-192c51.864 0 95.083 18.859 128.611 50.292l-52.126 50.03c-14.145-13.621-39.028-29.599-76.485-29.599-65.484 0-118.92 54.221-118.92 121.277 0 67.056 53.436 121.277 118.92 121.277 75.961 0 104.513-54.745 108.965-82.773H204.8v-66.009h181.261zm185.406 6.437V179.2h-56.001v55.733h-55.733v56.001h55.733v55.733h56.001v-55.733H627.2v-56.001h-55.733z"/>
                        </svg>
                      </a>';
              }
            }
            ?>
          </div>
        <?php
        }
        ?>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }
}


/* Related posts */
if (!function_exists('somnia_related_posts')) {
  function somnia_related_posts()
  {
    if (function_exists('get_field')) {
      $enable_related_posts = get_field('enable_related_posts', 'options');
      $related_posts = get_field('related_posts', 'options');
    } else {
      $enable_related_posts = true;
      $related_posts = array(
        'heading' => __('Related Articles', 'somnia'),
        'description' => __('Discover the Hottest Fashion News and Trends Straight from the Runway', 'somnia'),
        'number_posts' => 3,
      );
    }
    if ($enable_related_posts) {

      $post_id = get_the_ID();
      $cat_ids = array();
      $categories = get_the_category($post_id);

      if (!empty($categories) && !is_wp_error($categories)) {
        foreach ($categories as $category) {
          array_push($cat_ids, $category->term_id);
        }
      }

      $current_post_type = get_post_type($post_id);

      $query_args = array(
        'category__in'   => $cat_ids,
        'post_type'      => $current_post_type,
        'post__not_in'    => array($post_id),
        'posts_per_page'  => !empty($related_posts['number_posts']) ? $related_posts['number_posts'] : 3,
      );

      $list_posts = new WP_Query($query_args);

      ob_start();

      if ($list_posts->have_posts()) {
    ?>
        <div class="bt-related-posts">
          <div class="bt-container">
            <div class="bt-related-posts--heading">
              <?php if (!empty($related_posts['heading'])): ?>
                <h4 class="bt-head"><?php echo esc_html($related_posts['heading']); ?></h4>
              <?php endif; ?>
              <?php if (!empty($related_posts['description'])): ?>
                <p class="bt-sub"><?php echo esc_html($related_posts['description']); ?></p>
              <?php endif; ?>
            </div>
            <div class="bt-related-posts--list">
              <?php
              while ($list_posts->have_posts()) : $list_posts->the_post();
                get_template_part('framework/templates/post', 'style', array('image-size' => "large"));
              endwhile;
              wp_reset_postdata();
              ?>
            </div>
          </div>
        </div>
    <?php
      }
      return ob_get_clean();
    }
  }
}

//Comment Field Order
function somnia_comment_fields_custom_order($fields)
{
  $comment_field = $fields['comment'];
  $author_field = $fields['author'];
  $email_field = $fields['email'];
  $cookies_field = $fields['cookies'];
  unset($fields['comment']);
  unset($fields['author']);
  unset($fields['email']);
  unset($fields['url']);
  unset($fields['cookies']);
  // the order of fields is the order below, change it as needed:
  $fields['author'] = $author_field;
  $fields['email'] = $email_field;
  $fields['comment'] = $comment_field;
  $fields['cookies'] = $cookies_field;
  // done ordering, now return the fields:
  return $fields;
}
add_filter('comment_form_fields', 'somnia_comment_fields_custom_order');

/* Custom comment list */
if (!function_exists('somnia_custom_comment')) {
  function somnia_custom_comment($comment, $args, $depth)
  {
    $GLOBALS['comment'] = $comment;
    extract($args, EXTR_SKIP);

    if ('div' == $args['style']) {
      $tag = 'div';
      $add_below = 'comment';
    } else {
      $tag = 'li';
      $add_below = 'div-comment';
    }
    ?>
    <<?php echo esc_html($tag); ?> <?php comment_class(empty($args['has_children']) ? 'bt-comment-item clearfix' : 'bt-comment-item parent clearfix') ?> id="comment-<?php comment_ID() ?>">
      <div class="bt-comment">
        <div class="bt-avatar">
          <?php
          if (function_exists('get_field')) {
            $avatar = get_field('avatar', 'user_' . $comment->user_id);
          } else {
            $avatar = array();
          }
          if (!empty($avatar)) {
            echo '<img src="' . esc_url($avatar['url']) . '" alt="' . esc_attr($avatar['title']) . '" />';
          } else {
            if ($args['avatar_size'] != 0) echo get_avatar($comment, $args['avatar_size']);
          }


          ?>
        </div>
        <div class="bt-author">
          <h5 class="bt-name">
            <?php echo get_comment_author(get_comment_ID()); ?>
          </h5>
          <div class="bt-date">
            <?php echo get_comment_date(); ?>
          </div>
          <?php if ($comment->comment_approved == '0') : ?>
            <em class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'somnia'); ?></em>
          <?php endif; ?>
          <div class="bt-content">
            <div class="bt-text">
              <?php comment_text(); ?>
            </div>
            <?php comment_reply_link(array_merge($args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
          </div>
        </div>
      </div>
  <?php
  }
}
