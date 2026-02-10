<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$enable_recently_viewed = false;
$related_description   = '';
$related_heading       = __( 'Related Products', 'somnia' );
if ( function_exists( 'get_field' ) ) {
	$product_related_posts = get_field( 'product_related_posts', 'options' );
	if ( $product_related_posts ) {
		$enable_recently_viewed = ! empty( $product_related_posts['enable_recently_viewed'] );
		if ( ! empty( $product_related_posts['heading'] ) ) {
			$related_heading = $product_related_posts['heading'];
		}
		if ( ! $enable_recently_viewed && ! empty( $product_related_posts['description'] ) ) {
			$related_description = $product_related_posts['description'];
		}
	}
}

$show_section = $related_products || $enable_recently_viewed;
if ( $show_section ) :
	// Set global flag to indicate we're in related products section
	// This helps improve image quality in related products
	global $somnia_is_related_products;
	$somnia_is_related_products = true;

	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}
?>
	<section class="related products <?php echo empty( $enable_recently_viewed ) ? 'related-products-only' : ''; ?>">
		<div class="bt-related-tab-heading">
			<div class="bt-tab-nav">
				<?php if ( $related_products ) : ?>
					<h2 class="bt-main-text bt-tab-title active related" data-tab="related"><?php echo esc_html( $related_heading ); ?></h2>
					<?php if ( $related_description ) : ?>
					<div class="related-products-description"><?php echo wp_kses_post( wpautop( $related_description ) ); ?></div>
				<?php endif; ?>
				<?php endif; ?>
				<?php if ( $enable_recently_viewed ) : ?>
					<h2 class="bt-main-text bt-tab-title recently-viewed<?php echo empty( $related_products ) ? ' active' : ''; ?>" data-tab="recently-viewed"><?php echo esc_html__( 'Recently Viewed', 'somnia' ); ?></h2>
				<?php endif; ?>
			</div>
		</div>

		<div class="bt-tab-content">
			<?php if ( $related_products ) : ?>
			<div class="bt-tab-pane<?php echo ' active'; ?>" data-tab-content="related">
				<?php woocommerce_product_loop_start(); ?>
					<?php foreach ( $related_products as $related_product ) : ?>
						<?php
						$post_object = get_post( $related_product->get_id() );
						setup_postdata( $GLOBALS['post'] = &$post_object );
						wc_get_template_part( 'content', 'product' );
						?>
					<?php endforeach; ?>
				<?php woocommerce_product_loop_end(); ?>
			</div>
			<?php endif; ?>

			<?php if ( $enable_recently_viewed ) : ?>
			<div class="bt-tab-pane<?php echo empty( $related_products ) ? ' active' : ''; ?>" data-tab-content="recently-viewed">
				<div class="recently-viewed-products">
					<?php if ( ! empty( $recently_viewed_products ) ) : ?>
						<?php woocommerce_product_loop_start(); ?>
						<?php foreach ( $recently_viewed_products as $recent_product ) : ?>
							<?php
							$post_object = get_post( $recent_product->get_id() );
							setup_postdata( $GLOBALS['post'] = &$post_object );
							wc_get_template_part( 'content', 'product' );
							?>
						<?php endforeach; ?>
						<?php woocommerce_product_loop_end(); ?>
					<?php else : ?>
						<p class="no-products"><?php esc_html_e( 'No recently viewed products.', 'somnia' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
	// Reset global flag
	$somnia_is_related_products = false;
endif;

wp_reset_postdata();
