<?php

/**
 * Site Header
 *
 */

?>

<header class="bt-site-header">
  <div class="bt-container">
    <div class="bt-header-row">
      <div class="bt-logo-col">
        <div class="bt-site-logo">
          <?php
          if (has_custom_logo()) {
            the_custom_logo();
          } else {
          ?>
            <a href="<?php echo esc_url(home_url('/')); ?>">
              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/site-logo.png'); ?>" alt="Somnia Logo" />
            </a>
          <?php
          }
          ?>
        </div>
      </div>
      <div class="bt-primary-menu-col">
        <div class="bt-primary-menu">
          <?php
          if (has_nav_menu('primary_menu')) {
            wp_nav_menu(
              array(
                'theme_location'  => 'primary_menu',
                'menu_class'      => 'bt-primary-menu-wrapper',
                'container_class' => 'bt-primary-menu-container',
                'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'fallback_cb'     => false,
                'link_before'          => '<span>',
		            'link_after'           => '</span>',
              )
            );
          } else {
            wp_page_menu(array(
              'menu_class'  => 'bt-page-menu-wrap'
            ));
          }
          ?>
          <div class="bt-menu-toggle bt-menu-close">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0.660037 17.3226C0.00915417 16.6718 0.00915417 15.6165 0.660037 14.9656L6.63527 8.99041L0.660037 3.01507C0.00915417 2.36424 0.00915417 1.30891 0.660037 0.658073C1.3109 0.0072403 2.36619 0.0072403 3.01705 0.658073L8.99227 6.63341L14.9676 0.658073C15.6184 0.0072403 16.6738 0.0072403 17.3246 0.658073C17.9754 1.30891 17.9754 2.36424 17.3246 3.01507L11.3493 8.99041L17.3246 14.9656C17.9754 15.6165 17.9754 16.6717 17.3246 17.3226C16.6736 17.9735 15.6184 17.9735 14.9676 17.3226L8.99227 11.3474L3.01705 17.3226C2.36619 17.9735 1.3109 17.9735 0.660037 17.3226Z" fill="#222222" />
            </svg>
          </div>
        </div>

        <div class="bt-menu-toggle bt-menu-open">
          <svg width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20.332 2H1.66537M20.332 9H1.66537M20.332 16H1.66537" stroke="#222222" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>
      </div>
    </div>
  </div>
</header>