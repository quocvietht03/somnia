<div class="woocommerce-loop-product product product-placeholder">
	<div class="woocommerce-loop-product__thumbnail">
		<a href="#" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
            <div class="bt-product-images-wrapper">
                <div class="woocommerce-product-gallery__image ">
                    <?php echo '<img src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_html__('Awaiting product image', 'somnia') . '" class="wp-post-image" />'; ?>
                </div>
            </div>
        </a>
	</div>

	<div class="woocommerce-loop-product__infor">
		<a href="http://somnia.local/product/cap/" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
            <h2 class="woocommerce-loop-product__title"><?php esc_html_e('Product Name', 'somnia') ?></h2>
        </a>
        <span class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>&nbsp;20,00</bdi></span></span>
        <div class="bt-product-rating woocommerce"></div>				    
    </div>
</div>