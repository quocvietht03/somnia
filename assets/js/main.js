!(function ($) {
	"use strict";

	/* Toggle submenu align */
	function SomniaSubmenuAuto() {
		if ($('.bt-site-header .bt-container').length > 0) {
			var container = $('.bt-site-header .bt-container'),
				containerInfo = { left: container.offset().left, width: container.innerWidth() },
				contLeftPos = containerInfo.left,
				contRightPos = containerInfo.left + containerInfo.width;

			$('.children, .sub-menu').each(function () {
				var submenuInfo = { left: $(this).offset().left, width: $(this).innerWidth() },
					smLeftPos = submenuInfo.left,
					smRightPos = submenuInfo.left + submenuInfo.width;

				if (smLeftPos <= contLeftPos) {
					$(this).addClass('bt-align-left');
				}

				if (smRightPos >= contRightPos) {
					$(this).addClass('bt-align-right');
				}

			});
		}
	}

	/* Toggle menu mobile */
	function SomniaToggleMenuMobile() {
		$('.bt-site-header .bt-menu-toggle').on('click', function (e) {
			e.preventDefault();

			if ($(this).hasClass('bt-menu-open')) {
				$(this).addClass('bt-is-hidden');
				$('.bt-site-header .bt-primary-menu').addClass('bt-is-active');
			} else {
				$('.bt-menu-open').removeClass('bt-is-hidden');
				$('.bt-site-header .bt-primary-menu').removeClass('bt-is-active');
			}
		});
	}

	/* Toggle sub menu mobile */
	function SomniaToggleSubMenuMobile() {
		var hasChildren = $('.bt-site-header .page_item_has_children, .bt-site-header .menu-item-has-children');

		hasChildren.each(function () {
			var $btnToggle = $('<div class="bt-toggle-icon"></div>');

			$(this).append($btnToggle);

			$btnToggle.on('click', function (e) {
				e.preventDefault();
				$(this).toggleClass('bt-is-active');
				$(this).parent().children('ul').toggle();
			});
		});
	}
	/* Shop */
	function SomniaImageZoomable() {
		if ($('.bt-gallery-zoomable').length == 0 || window.innerWidth < 1025) {
			return
		}

		const zoomables = $('.bt-gallery-zoomable .zoomable');
		for (const el of zoomables) {
			new Zoomable(el);
		}
	}

	function SomniaGalleryLightbox() {
		if ($('.bt-gallery-lightbox').length == 0) {
			return
		}

		$('.bt-gallery-lightbox').magnificPopup({
			delegate: 'img',
			type: 'image',
			removalDelay: 500,
			callbacks: {
				beforeOpen: function () {
					this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
					this.st.mainClass = 'mfp-with-zoom mfp-img-mobile';
				},
				elementParse: function (item) { item.src = item.el.attr('src'); },
			},
			image: {
				verticalFit: true
			},
			gallery: {
				enabled: true,
				navigateByImgClick: true,
			},
			zoom: {
				enabled: true,
				duration: 300,
				easing: 'ease-in-out',
				opener: function (openerElement) {
					return openerElement.is('img') ? openerElement : openerElement.find('img');
				}
			}
		});

		$('.bt-show-gallery-lightbox').on('click', function () {
			$('.bt-gallery-lightbox').magnificPopup('open');
		});

	}

	function SomniaSliderThumbs(container) {
		// If container is provided, scope within it; otherwise use global
		var $context = container ? $(container) : $(document);

		if ($context.find('.woocommerce-product-gallery__slider').length == 0) {
			return
		}

		var thumbDirection = 'horizontal';
		if ($context.find('.bt-left-thumbnail').length > 0 || $context.find('.bt-right-thumbnail').length > 0) {
			thumbDirection = 'vertical';
		}

		// Get specific elements within context
		var thumbsElement = $context.find('.woocommerce-product-gallery__slider-thumbs')[0];
		var sliderElement = $context.find('.woocommerce-product-gallery__slider')[0];

		if (!thumbsElement || !sliderElement) {
			return;
		}

		var galleryThumbs = new Swiper(thumbsElement, {
			direction: thumbDirection,
			spaceBetween: 10,
			autoHeight: true,
			loop: false,
			freeMode: true,
			loopedSlides: 5,
			watchSlidesVisibility: true,
			watchSlidesProgress: true,
			breakpoints: {
				0: {
					slidesPerView: 'vertical' == thumbDirection ? 'auto' : 3,
				},
				480: {
					slidesPerView: 'vertical' == thumbDirection ? 'auto' : 4,
				},
				768: {
					slidesPerView: 'vertical' == thumbDirection ? 'auto' : 5,
				},
				992: {
					slidesPerView: 'vertical' == thumbDirection ? 'auto' : 4,
				},
				1200: {
					slidesPerView: 'vertical' == thumbDirection ? 'auto' : 5,
				}
			}
		});
		var galleryTop = new Swiper(sliderElement, {
			spaceBetween: 20,
			loop: false,
			loopedSlides: 5,
			navigation: {
				nextEl: $context.find('.swiper-button-next')[0],
				prevEl: $context.find('.swiper-button-prev')[0],
			},
			thumbs: {
				swiper: galleryThumbs,
			},
		});
	}

	function SomniaGallerySlider() {
		if ($('.bt-gallery-slider-product').length == 0) {
			return
		}

		var gallerySlider = new Swiper('.bt-gallery-slider-product', {
			spaceBetween: 20,
			loop: false,
			loopedSlides: 5,
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			pagination: {
				el: '.swiper-pagination',
				clickable: true,
			},
			breakpoints: {
				0: {
					slidesPerView: 1,
				},
				992: {
					slidesPerView: 3,
				}
			}
		});
	}

	function SomniaShop() {
		if ($('.single-product div.images').length > 0) {
			SomniaImageZoomable();
			SomniaGalleryLightbox();
			SomniaSliderThumbs();
			SomniaGallerySlider();
			if ($('.bt-gallery-grid-products').length > 0) {
				var items = $('.bt-gallery-grid-products').data('items'),
					shown = $('.bt-gallery-grid-products').data('shown');
				$('.bt-gallery-grid-product__item:lt(' + shown + ')').addClass('show');
				if (shown < items) {
					$('.bt-gallery-grid-products .bt-show-more').show();
				} else {
					$('.bt-gallery-grid-products .bt-show-more').hide();
				}
				$('.bt-gallery-grid-products .bt-show-more').on('click', function () {
					items = $('.bt-gallery-grid-products').data('items');
					shown = $('.bt-gallery-grid-product__item.show').length + 2;
					if (shown < items) {
						$('.bt-gallery-grid-product__item:lt(' + shown + ')').addClass('show');
					} else {
						$('.bt-gallery-grid-product__item:lt(' + items + ')').addClass('show');
						$('.bt-gallery-grid-products .bt-show-more').hide();
					}
				});
			}
		}

		if ($('.quantity input').length > 0) {
			/* Plus Qty - Exclude quickview */
			$(document).off('click.quantity', '.qty-plus').on('click.quantity', '.qty-plus', function () {
				// Skip if inside quickview popup
				if ($(this).closest('.bt-popup-quick-view').length > 0) {
					return;
				}
				var parent = $(this).parent();
				var $input = $('input.qty', parent);
				var currentVal = parseFloat($input.val()) || 0;
				var step = parseFloat($input.attr('step')) || 1;
				var max = $input.attr('max') ? parseFloat($input.attr('max')) : null;
				var newVal = currentVal + step;

				if (max !== null && newVal > max) {
					newVal = max;
				}

				$input.val(newVal);
				$input.trigger('change');
			});
			/* Minus Qty - Exclude quickview */
			$(document).off('click.quantity', '.qty-minus').on('click.quantity', '.qty-minus', function () {
				// Skip if inside quickview popup
				if ($(this).closest('.bt-popup-quick-view').length > 0) {
					return;
				}
				var parent = $(this).parent();
				var $input = $('input.qty', parent);
				var currentVal = parseFloat($input.val()) || 0;
				var step = parseFloat($input.attr('step')) || 1;
				var min = parseFloat($input.attr('min')) || 1;
				var newVal = currentVal - step;

				if (newVal < min) {
					newVal = min;
				}

				$input.val(newVal);
				$input.trigger('change');
			});
		}



		if ($('.bt-js-open-popup-link').length > 0) {
			$('.bt-js-open-popup-link').magnificPopup({
				type: 'inline',
				midClick: true,
				mainClass: 'mfp-fade',
				callbacks: {
					open: function () {
						// Check if this is product video popup
						if (this.content.hasClass('bt-product-video__popup')) {
							const videoPopup = this.content.find('.bt-video-embed');
							const videoElement = videoPopup.find('video');
							const iframeElement = videoPopup.find('iframe');

							if (videoElement.length > 0) {
								// Handle MP4 video - autoplay
								videoElement[0].play();
							} else if (iframeElement.length > 0) {
								// Handle iframe video (YouTube, Vimeo, etc.)
								const src = iframeElement.attr('src');
								if (src && !src.includes('autoplay=1')) {
									// Add autoplay parameter to iframe
									const separator = src.includes('?') ? '&' : '?';
									iframeElement.attr('src', src + separator + 'autoplay=1');
								}
							}
						}
					},
					close: function () {
						// Check if this is product video popup
						if (this.content.hasClass('bt-product-video__popup')) {
							const videoElement = this.content.find('video');
							const iframeElement = this.content.find('iframe');

							if (videoElement.length > 0) {
								// Pause and reset MP4 video
								videoElement[0].pause();
								videoElement[0].currentTime = 0;
							} else if (iframeElement.length > 0) {
								// Stop iframe video by reloading without autoplay
								const src = iframeElement.attr('src');
								if (src) {
									// Remove autoplay parameter and reload iframe to stop video
									const newSrc = src.replace(/[?&]autoplay=1/g, '');
									iframeElement.attr('src', newSrc);
								}
							}
						}
					}
				}
			});
		}

		$('.bt-copy-btn').on('click', function (e) {
			e.preventDefault();
			var $button = $(this),
				$buttonurl = $(this).closest('form').find('#bt-product-share-url');
			if (navigator.clipboard) {
				navigator.clipboard.writeText($buttonurl.val()).then(() => {
					$buttonurl.select();
					$button.text($button.data('copied'));
					setTimeout(function () {
						$button.text($button.data('copy'))
					}, 1000);
				}, () => {
					return prompt("Copy to clipboard: Ctrl+C, Enter", $buttonurl.value);
				});
			} else {
				$buttonurl.select();
				document.execCommand('copy');
				$button.text($button.data('copied'));
				setTimeout(function () {
					$button.text($button.data('copy'))
				}, 1000);
			}
		});
	}
	function SomniaProductVariationHandler() {
		if ($('.variations_form').length > 0) {
			$(document).on('click', '.bt-attributes-wrap .bt-js-item', function () {
				var valueItem = $(this).data('value');
				var attributesItem = $(this).closest('.bt-attributes--item');
				var attributeName = attributesItem.data('attribute-name');
				attributesItem.find('.bt-js-item').removeClass('active'); // Remove active class only from items in the same attribute group
				$(this).addClass('active'); // Add active class to clicked item
				var colorTaxonomy = AJ_Options.color_taxonomy;
				var nameItem = (attributeName == colorTaxonomy) ? $(this).find('label').html() : $(this).html();
				attributesItem.find('.bt-result').html(nameItem);
				$(this).closest('.variations_form').find('select#' + attributeName).val(valueItem).trigger('change');
				var gallerylayout = '';
				var $productContainer = $(this).closest('.bt-product-inner, .bt-quickview-product');
				
				// Check if we're in quick view
				if ($productContainer.closest('.bt-popup-quick-view').length > 0 || $productContainer.hasClass('bt-quickview-product')) {
					gallerylayout = 'quickview-slider';
				} else if ($('.bt-gallery-slider-container').length > 0 || $('.bt-gallery-slider-fullwidth').length > 0) {
					gallerylayout = 'gallery-slider';
				} else if ($('.bt-gallery-one-column').length > 0 || $('.bt-gallery-two-columns').length > 0 || $('.bt-gallery-three-columns').length > 0 ||
					$('.bt-gallery-four-columns').length > 0 || $('.bt-gallery-grid-fullwidth').length > 0 || $('.bt-gallery-stacked').length > 0) {
					gallerylayout = 'gallery-grid';
				} else {
					gallerylayout = 'slider-thumb';
				}


				// update js variations_form woo (only in product loop)
				var $currentForm = $(this).closest('.variations_form');
				var $productLoop = $(this).closest('.woocommerce-loop-product');
				var isInProductLoop = $productLoop.length > 0;
				var showVariationTriggered = false;
				$(this).closest('.variations_form').off('show_variation.somnia').on('show_variation.somnia', function (event, variation) {
					showVariationTriggered = true;
					var variationId = variation.variation_id;
					if (variationId && variationId !== '0') {
						if (!variation.is_in_stock) {
							$(this).closest('.variations_form').find('.bt-button-buy-now a').addClass('disabled').removeAttr('data-variation');
						} else {
							$(this).closest('.variations_form').find('.bt-button-buy-now a').removeClass('disabled').attr('data-variation', variationId);
						}

						if ($('.bt-product-add-to-cart-variable').length > 0 && variation.is_in_stock) {
							var $addToCartBtn = $(this).closest('.bt-product-add-to-cart-variable').find('.bt-js-add-to-cart-variable');

							$addToCartBtn.removeClass('disabled').attr('data-variation', variationId);

							// Handle quantity controls - remove old handler first
							$(this).closest('.variations_form').find('.quantity .qty').off('change.updateQuantity').on('change.updateQuantity', function () {
								var newQuantity = parseInt($(this).val()) || 1;
								$addToCartBtn.attr('data-product-quantity', newQuantity);
							});
						}
						
					// Check if variation has custom image before loading gallery
					var hasCustomImage = false;
					if (variation.image && variation.image.src) {
						// Get parent product image source from data attribute (more reliable than selector)
						var parentImageSrc = $productContainer.data('parent-image-src');
						var variationImageSrc = variation.image.src;
						
						if (parentImageSrc) {
							// Compare image sources (remove query strings and size suffixes for comparison)
							// WordPress adds size suffixes like -300x300, -150x150, etc.
							var cleanParentSrc = parentImageSrc.split('?')[0].replace(/-\d+x\d+\.(jpg|jpeg|png|gif|webp)$/i, '.$1');
							var cleanVariationSrc = variationImageSrc.split('?')[0].replace(/-\d+x\d+\.(jpg|jpeg|png|gif|webp)$/i, '.$1');
							// Check if images are different and variation image is not placeholder
							hasCustomImage = cleanVariationSrc !== cleanParentSrc && 
											 variationImageSrc.indexOf('woocommerce-placeholder') === -1 &&
											 variationImageSrc.indexOf('placeholder.png') === -1;
						}
					}

						// Only load gallery if variation has custom image
						if (!hasCustomImage) {
							return;
						}
						
						// Load gallery
						var param_ajax = {
							action: 'somnia_load_product_gallery',
							gallery_layout: gallerylayout,
							variation_id: variationId
						};

						$.ajax({
							type: 'POST',
							dataType: 'json',
							url: AJ_Options.ajax_url,
							data: param_ajax,
							beforeSend: function () {
								// Show loading skeleton
								let skeletonHtml = '';
								if (gallerylayout == 'quickview-slider' || gallerylayout == 'gallery-slider') {
									skeletonHtml = `
									<div class="bt-skeleton-gallery">
										<div class="bt-skeleton-main-image">
											<div class="bt-skeleton-thumb"></div>
										</div>
										<div class="bt-skeleton-main-image">
											<div class="bt-skeleton-thumb"></div>
										</div>
										<div class="bt-skeleton-main-image">
											<div class="bt-skeleton-thumb"></div>
										</div>
									</div>`;
								} else if (gallerylayout == 'gallery-grid') {
									skeletonHtml = `
									<div class="bt-skeleton-gallery">
										<div class="bt-skeleton-main-image">
											<div class="bt-skeleton-thumb"></div>
										</div>
										<div class="bt-skeleton-main-image">
											<div class="bt-skeleton-thumb"></div>
										</div>
										<div class="bt-skeleton-main-image">
											<div class="bt-skeleton-thumb"></div>
										</div>
										<div class="bt-skeleton-main-image">
											<div class="bt-skeleton-thumb"></div>
										</div>
										<div class="bt-skeleton-main-image">
											<div class="bt-skeleton-thumb"></div>
										</div>
										<div class="bt-skeleton-main-image">
											<div class="bt-skeleton-thumb"></div>
										</div>
									</div>`;
								} else {
									skeletonHtml = `
									<div class="bt-skeleton-gallery">
											<div class="bt-skeleton-main-image">
												<div class="bt-skeleton-thumb"></div>
											</div>
											<div class="bt-skeleton-thumbnails">
												<div class="bt-skeleton-thumbnails--inner">
													<div class="bt-skeleton-thumb"></div>
													<div class="bt-skeleton-thumb"></div>
													<div class="bt-skeleton-thumb"></div>
													<div class="bt-skeleton-thumb"></div>
													<div class="bt-skeleton-thumb"></div>
													<div class="bt-skeleton-thumb"></div>
												</div>
											</div>
									</div>`;
								}
								// Remove existing gallery 
								$productContainer.find('.woocommerce-product-gallery, .bt-gallery-grid-products, .bt-gallery-slider-products').addClass('loading');
								$productContainer.find('.woocommerce-product-gallery, .bt-gallery-grid-products, .bt-gallery-slider-products').prepend(skeletonHtml);

								$productContainer.find('.bt-attributes-wrap .bt-js-item').addClass('disable');
							},
							success: function (response) {
								if (response.success) {
									if ($productContainer.length > 0) {
										if (gallerylayout == 'quickview-slider') {
											// Destroy existing swiper if any
											var existingSwiper = $('.bt-popup-quick-view .bt-gallery-slider-product')[0]?.swiper;
											if (existingSwiper) {
												existingSwiper.destroy(true, true);
											}
											
											$productContainer.find('.bt-gallery-slider-products').html(response.data['gallery-slider']);
											
											// Re-init quickview slider
											setTimeout(function() {
												if ($('.bt-popup-quick-view .bt-gallery-slider-product').length > 0) {
													var $quickviewSlider = $('.bt-popup-quick-view .bt-gallery-slider-product');
													var $wrapper = $quickviewSlider.find('.swiper-wrapper');
													var $slides = $wrapper.find('.swiper-slide');
													var slideCount = $slides.length;
													
													// Duplicate slides to ensure minimum 3 slides
													if (slideCount > 0 && slideCount < 3) {
														var slidesToAdd = 3 - slideCount;
														for (var i = 0; i < slidesToAdd; i++) {
															var $clonedSlide = $slides.eq(i % slideCount).clone();
															$wrapper.append($clonedSlide);
														}
													}
													
													var quickviewSlider = new Swiper('.bt-popup-quick-view .bt-gallery-slider-product', {
														spaceBetween: 30,
														loop: true,
														loopedSlides: 3,
														pagination: {
															el: '.bt-popup-quick-view .swiper-pagination',
															type: 'progressbar',
														},
														slidesPerView: 1,
														allowTouchMove: true,
														centeredSlides: false,
													});
												}
												
												$productContainer.find('.bt-skeleton-gallery').remove();
												$productContainer.find('.bt-gallery-slider-products').removeClass('loading');
											}, 100);
										} else if (gallerylayout == 'gallery-slider') {
											$productContainer.find('.bt-gallery-slider-products').html(response.data['gallery-slider']);
											SomniaImageZoomable();
											SomniaGalleryLightbox();
											SomniaGallerySlider();

											setTimeout(function () {
												$productContainer.find('.bt-skeleton-gallery').remove();
												$productContainer.find('.bt-gallery-slider-products').removeClass('loading');
											}, 200);
										} else if (gallerylayout == 'gallery-grid') {
											$productContainer.find('.bt-gallery-grid-products').data('items', response.data['itemgallery']);
											$productContainer.find('.bt-gallery-grid-product').html(response.data['gallery-grid']);

											SomniaImageZoomable();

											var items = $productContainer.find('.bt-gallery-grid-products').data('items'),
												shown = $productContainer.find('.bt-gallery-grid-products').data('shown');
											$productContainer.find('.bt-gallery-grid-product__item:lt(' + shown + ')').addClass('show');
											if (shown < items) {
												$productContainer.find('.bt-gallery-grid-products .bt-show-more').show();
											} else {
												$productContainer.find('.bt-gallery-grid-products .bt-show-more').hide();
											}

											setTimeout(function () {
												$productContainer.find('.bt-skeleton-gallery').remove();
												$productContainer.find('.bt-gallery-grid-products').removeClass('loading');
											}, 200);
										} else {
											if (response.data['itemgallery'] > 1) {
												$productContainer.find('.woocommerce-product-gallery__wrapper').addClass('bt-has-slide-thumbs');
												$productContainer.find('.bt-skeleton-gallery .bt-skeleton-thumbnails').show();
											} else {
												$productContainer.find('.woocommerce-product-gallery__wrapper').removeClass('bt-has-slide-thumbs');
												$productContainer.find('.bt-skeleton-gallery .bt-skeleton-thumbnails').hide();
											}
											$productContainer.find('.woocommerce-product-gallery__wrapper').html(response.data['slider-thumb']);
											SomniaImageZoomable();
											SomniaGalleryLightbox();
											SomniaSliderThumbs($productContainer);

											setTimeout(function () {
												$productContainer.find('.woocommerce-product-gallery').removeClass('loading');
												$productContainer.find('.bt-skeleton-gallery').remove();
											}, 200);
									}
									
									// Update parent image src for next comparison
									// Store the full variation image URL (not cleaned) for accurate comparison
									if (variation.image && variation.image.src) {
										$productContainer.data('parent-image-src', variation.image.src);
									}
								}
								$productContainer.find('.bt-attributes-wrap .bt-js-item').removeClass('disable');

							}
						},
						error: function (xhr, status, error) {
								console.log('Error loading gallery:', error);
								$productContainer.find('.woocommerce-product-gallery').removeClass('loading');
								$productContainer.find('.bt-attributes-wrap .bt-js-item').removeClass('disable');
							}
						});
						//Get variation price from data-product_variations
						var $form = $(this).closest('.variations_form');
						var variations = $form.data('product_variations');
						if (variations) {
							// Find matching variation by ID
							var variation = variations.find(function (v) {
								return v.variation_id === variationId;
							});
							if (variation && variation.price_html) {
								// Format price with currency symbol
								var formattedPrice = '<span class="bt-price-add-cart"> - ' +
									variation.price_html + '</span>';
								// Update add to cart button text
								$form.find(".single_add_to_cart_button")
									.html("Add to cart" + formattedPrice);
							}
						}
					} else {
						$(this).closest('.variations_form').find('.bt-button-buy-now a').addClass('disabled').removeAttr('data-variation');
						if ($('.bt-product-add-to-cart-variable').length > 0) {
							$(this).closest('.bt-product-add-to-cart-variable').find('.bt-js-add-to-cart-variable').addClass('disabled').removeAttr('data-variation');
						}
					}
				});

				// Check if show_variation.somnia event is triggered (only in product loop)
				if (typeof $.fn.wc_variation_form !== 'undefined' && isInProductLoop) {
					// Set timeout to check if show_variation.somnia event is triggered after setting value
					setTimeout(function () {
						if (!showVariationTriggered) {
							$currentForm.wc_variation_form();
						}
					}, 300);
				}

				// Function to update available options based on current selections
				var updateAvailableOptions = function () {
					$currentForm.find('.bt-attributes-wrap .bt-js-item').each(function () {
						var $item = $(this);
						var valueItem = $item.data('value');

						// Skip if valueItem is missing
						if (!valueItem) {
							$item.addClass('disabled');
							return;
						}

						var $attributesItem = $item.closest('.bt-attributes--item');
						var attributeName = $attributesItem.data('attribute-name');

						// Skip if attributeName is missing
						if (!attributeName) {
							$item.addClass('disabled');
							return;
						}

						// Find the select element for this attribute
						var $select = $currentForm.find('select#' + attributeName);

						// Check if select exists
						if ($select.length === 0) {
							$item.addClass('disabled');
							return;
						}

						// Find the option with matching value
						var $option = $select.find('option[value="' + valueItem.replace(/"/g, '\\"') + '"]');

						// Check if option exists and is NOT disabled
						// WooCommerce disables unavailable options, so we check for !disabled
						var isAvailable = $option.length > 0 && !$option.prop('disabled');

						// Toggle disabled class
						$item.toggleClass('disabled', !isAvailable);
					});
				};

				// Listen to WooCommerce variation update event to update immediately
				$currentForm.off('woocommerce_update_variation_values.updateOptions').on('woocommerce_update_variation_values.updateOptions', function () {
					updateAvailableOptions();
				});

				// Initial update for single product page
				updateAvailableOptions();
			});
		}
	}
	/* load Shop Quick View */
	function SomniaLoadShopQuickView() {
		if ($('.bt-quickview-product').length > 0) {
			SomniaSliderThumbs('.bt-quickview-product');
			// Init gallery slider for quickview (no zoom, no lightbox)
			if ($('.bt-popup-quick-view .bt-gallery-slider-product').length > 0) {
				var $quickviewSlider = $('.bt-popup-quick-view .bt-gallery-slider-product');
				var $wrapper = $quickviewSlider.find('.swiper-wrapper');
				var $slides = $wrapper.find('.swiper-slide');
				var slideCount = $slides.length;
				
				// Duplicate slides to ensure minimum 3 slides
				if (slideCount > 0 && slideCount < 3) {
					var slidesToAdd = 3 - slideCount;
					for (var i = 0; i < slidesToAdd; i++) {
						var $clonedSlide = $slides.eq(i % slideCount).clone();
						$wrapper.append($clonedSlide);
					}
				}
				
				var quickviewSlider = new Swiper('.bt-popup-quick-view .bt-gallery-slider-product', {
					spaceBetween: 30,
					loop: true,
					loopedSlides: 3,
					pagination: {
						el: '.bt-popup-quick-view .swiper-pagination',
						type: 'progressbar',
					},
					slidesPerView: 1,
					allowTouchMove: true,
					centeredSlides: false,
				});
			}
		}
		// check button add to cart 
		if ($('.bt-quickview-product .grouped_form').length > 0) {
			const $addToCartBtn = $('.bt-quickview-product .grouped_form .single_add_to_cart_button');
			$addToCartBtn.addClass('disabled');
		}
		/* Plus Qty - Quick view only */
		$(document).off('click.quickview', '.bt-popup-quick-view .qty-plus').on('click.quickview', '.bt-popup-quick-view .qty-plus', function () {
			var parent = $(this).parent();
			var $input = $('input.qty', parent);
			var currentVal = parseFloat($input.val()) || 0;
			var step = parseFloat($input.attr('step')) || 1;
			var max = $input.attr('max') ? parseFloat($input.attr('max')) : null;
			var newVal = currentVal + step;

			if (max !== null && newVal > max) {
				newVal = max;
			}

			$input.val(newVal);
			$input.trigger('change');
		});
		/* Minus Qty - Quick view only */
		$(document).off('click.quickview', '.bt-popup-quick-view .qty-minus').on('click.quickview', '.bt-popup-quick-view .qty-minus', function () {
			var parent = $(this).parent();
			var $input = $('input.qty', parent);
			var currentVal = parseFloat($input.val()) || 0;
			var step = parseFloat($input.attr('step')) || 1;
			var min = parseFloat($input.attr('min')) || 1;
			var newVal = currentVal - step;

			if (newVal < min) {
				newVal = min;
			}

			$input.val(newVal);
			$input.trigger('change');
		});
	}

	/* Validation form comment */
	function SomniaCommentValidation() {
		if ($('#bt_comment_form').length) {
			jQuery('#bt_comment_form').validate({
				rules: {
					author: {
						required: true,
						minlength: 2
					},
					email: {
						required: true,
						email: true
					},
					comment: {
						required: true,
						minlength: 20
					}
				},
				errorElement: "div",
				errorPlacement: function (error, element) {
					element.after(error);
				}
			});
		}
		// Check if the form reviews product
		if ($('#commentform').length) {
			jQuery('#commentform').validate({
				rules: {
					author: {
						required: true,
						minlength: 2
					},
					email: {
						required: true,
						email: true
					},
					comment: {
						required: true,
						minlength: 20
					}
				},
				errorElement: "div",
				errorPlacement: function (error, element) {
					element.after(error);
				}
			});
		}
	}

	/* Product wishlist */
	function SomniaProductWishlist() {
		if ($('.bt-product-wishlist-btn').length > 0) {
			$(document).on('click', '.bt-product-wishlist-btn', function (e) {
				e.preventDefault();

				var post_id = $(this).data('id').toString(),
					wishlist_local = window.localStorage.getItem('productwishlistlocal');
				if (!wishlist_local) {
					window.localStorage.setItem('productwishlistlocal', post_id);
					wishlist_local = window.localStorage.getItem('productwishlistlocal');
					var wishlist_count = wishlist_local ? wishlist_local.split(',').length : 0;
					$('.bt-mini-wishlist .wishlist_total').html(wishlist_count);
					$(this).addClass('loading');
					$(this).removeClass('no-added');
					setTimeout(function () {
						$('.bt-product-wishlist-btn[data-id="' + post_id + '"]').addClass('added');
						$('.bt-product-wishlist-btn[data-id="' + post_id + '"]').removeClass('loading');
						$('.bt-product-wishlist-btn[data-id="' + post_id + '"] .tooltip').text("Remove Wishlist");
					}, 200);
					if (AJ_Options.wishlist_toast) {
						SomniashowToast(post_id, 'wishlist', 'add');
					}

				} else {
					var wishlist_arr = wishlist_local.split(',');

					if (wishlist_arr.includes(post_id)) {
						var index = wishlist_arr.indexOf(post_id);
						if (index > -1) {
							wishlist_arr.splice(index, 1);
						}
						window.localStorage.setItem('productwishlistlocal', wishlist_arr);
						wishlist_local = window.localStorage.getItem('productwishlistlocal');
						var wishlist_count = wishlist_local ? wishlist_local.split(',').length : 0;
						$('.bt-mini-wishlist .wishlist_total').html(wishlist_count);
						$(this).addClass('loading');
						$(this).removeClass('added');
						setTimeout(function () {
							$('.bt-product-wishlist-btn[data-id="' + post_id + '"]').addClass('no-added');
							$('.bt-product-wishlist-btn[data-id="' + post_id + '"]').removeClass('loading');
							$('.bt-product-wishlist-btn[data-id="' + post_id + '"] .tooltip').text("Add To Wishlist");
						}, 200);
						if (AJ_Options.wishlist_toast) {
							SomniashowToast(post_id, 'wishlist', 'remove');
						}
					} else {
						window.localStorage.setItem('productwishlistlocal', wishlist_local + ',' + post_id);
						wishlist_local = window.localStorage.getItem('productwishlistlocal');
						var wishlist_count = wishlist_local ? wishlist_local.split(',').length : 0;
						$('.bt-mini-wishlist .wishlist_total').html(wishlist_count);
						$(this).addClass('loading');
						$(this).removeClass('no-added');
						setTimeout(function () {
							$('.bt-product-wishlist-btn[data-id="' + post_id + '"]').addClass('added');
							$('.bt-product-wishlist-btn[data-id="' + post_id + '"]').removeClass('loading');
							$('.bt-product-wishlist-btn[data-id="' + post_id + '"] .tooltip').text("Remove Wishlist");
						}, 200);
						if (AJ_Options.wishlist_toast) {
							SomniashowToast(post_id, 'wishlist', 'add');
						}
					}
				}
			});
		}

		if ($('.elementor-widget-bt-product-wishlist').length > 0) {
			$(document).on('click', '.bt-product-remove-wishlist', function (e) {
				e.preventDefault();
				$(this).addClass('deleting');
				var product_id = $(this).data('id').toString(),
					wishlist_str = $('.bt-productwishlistlocal').val(),
					wishlist_arr = wishlist_str.split(','),
					index = wishlist_arr.indexOf(product_id);

				if (index > -1) {
					wishlist_arr.splice(index, 1);
				}

				wishlist_str = wishlist_arr.toString();
				$('.bt-productwishlistlocal').val(wishlist_str);
				window.localStorage.setItem('productwishlistlocal', wishlist_str);
				SomniaShareLocalStorage(wishlist_str);

				$(this).closest('.bt-product-item').remove();
				let currentCount = $('.bt-product-item').length;
				$('.bt-mini-wishlist .wishlist_total').html(currentCount);
				if (currentCount == 0) {
					$('.bt-products-wishlist-form').submit();
				}
			});

			// Ajax wishlist
			$('.bt-products-wishlist-form').submit(function () {

				var param_ajax = {
					action: 'somnia_products_wishlist',
					productwishlist_data: $('.bt-productwishlistlocal').val()
				};

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: AJ_Options.ajax_url,
					data: param_ajax,
					context: this,
					beforeSend: function () {
					},
					success: function (response) {
						if (response.success) {
							$('.bt-product-list').html(response.data['items']).fadeIn('slow');
							$('.bt-mini-wishlist .wishlist_total').html(response.data['count']);
						} else {
							console.log('error');
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log('The following error occured: ' + textStatus, errorThrown);
					}
				});

				return false;
			});
		}
	}
	/* Product Wishlist Load */
	function SomniaProductWishlistLoad() {
		var wishlist_local = window.localStorage.getItem('productwishlistlocal');
		if (!wishlist_local) {
			return;
		}
		if ($('.elementor-widget-bt-product-wishlist').length > 0) {
			SomniaShareLocalStorage(wishlist_local);
			$('.bt-productwishlistlocal').val(wishlist_local);
			var param_ajax = {
				action: 'somnia_products_wishlist',
				productwishlist_data: wishlist_local
			};

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: AJ_Options.ajax_url,
				data: param_ajax,
				context: this,
				beforeSend: function () {
					//$('.bt-table--body').addClass('loading');
					let skeletonHtml = '';
					for (let i = 0; i < 5; i++) {
						skeletonHtml += `
							<div class="bt-table--row bt-product-item bt-skeleton-item">
								<div class="bt-table--col bt-product-remove">
									<div class="bt-skeleton-circle"></div>
								</div>
								<div class="bt-table--col bt-product-thumb">
									<div class="bt-skeleton-image"></div>
								</div>
								<div class="bt-table--col bt-product-title">
									<div class="bt-skeleton-text"></div>
								</div>
								<div class="bt-table--col bt-product-price">
									<div class="bt-skeleton-text"></div>
								</div>
								<div class="bt-table--col bt-product-stock">
									<div class="bt-skeleton-text"></div>
								</div>
								<div class="bt-table--col bt-product-add-to-cart">
									<div class="bt-skeleton-button"></div>
								</div>
							</div>`;
					}
					$('.bt-product-list').html(skeletonHtml);
				},
				success: function (response) {
					if (response.success) {
						$('.bt-product-list').html(response.data['items']).fadeIn('slow');
						$('.bt-mini-wishlist .wishlist_total').html(response.data['count']);
					} else {
						console.log('error');
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('The following error occured: ' + textStatus, errorThrown);
				}
			});
		}
	}
	/* Product content compare scroll  */
	function SomniaCompareContentScroll() {
		const $compareTable = $('.bt-table--body');
		if ($compareTable.length) {
			let isDown = false;
			let startX, scrollLeft;

			$($compareTable).on("mousedown", function (e) {
				isDown = true;
				startX = e.pageX - $(this).offset().left;
				scrollLeft = $(this).scrollLeft();
				$(this).css({
					"cursor": "grabbing",
				});
			});

			$(document).on("mouseup", function () {
				isDown = false;
				$($compareTable).css({
					"cursor": "grab",
				});
			});

			$($compareTable).on("mousemove", function (e) {
				if (!isDown) return;
				e.preventDefault();
				let x = e.pageX - $(this).offset().left;
				let walk = (x - startX) * 1.5;
				$(this).scrollLeft(scrollLeft - walk);
			});

			// Add momentum scrolling
			$($compareTable).on("mouseleave", function () {
				if (isDown && typeof walk !== 'undefined') {
					let velocity = walk;
					let deceleration = 0.95;
					let momentum = setInterval(function () {
						velocity *= deceleration;
						$($compareTable).scrollLeft($($compareTable).scrollLeft() + velocity);
						if (Math.abs(velocity) < 0.5) {
							clearInterval(momentum);
						}
					}, 10);
				}
			});

			// Prevent text selection on mouseup outside the element
			$(document).on("selectstart", function (e) {
				if (isDown) {
					e.preventDefault();
					return false;
				}
			});
		}
	}
	/* Product compare */
	function SomniaProductCompare() {
		function showComparePopup() {
			$('.bt-popup-compare').addClass('active');
			$('.bt-compare-body').addClass('show');
			$('body').css({
				'overflow': 'hidden',
				'padding-right': `${window.innerWidth - $(window).width()}px` // Prevent layout shift
			}); // Disable body scroll
		}
		function removeComparePopup() {
			$('.bt-compare-body').removeClass('show');
			setTimeout(function () {
				$('.bt-popup-compare').removeClass('active');
			}, 300);
			$('body').css({
				'overflow': 'auto', // Restore body scroll
				'padding-right': '0' // Reset padding-right
			});
		}
		if ($('.bt-product-compare-btn').length > 0) {
			$('body').append('<div class="bt-popup-compare"><div class="bt-compare-overlay"></div><div class="bt-compare-close"></div><div class="bt-compare-body"><div class="bt-loading-wave"></div><div class="bt-compare-load"></div></div></div>').fadeIn('slow');

			$(document).on('click', '.bt-product-compare-btn', function (e) {
				e.preventDefault();
				$(this).find('.tooltip').remove();
				var post_id = $(this).data('id').toString(),
					compare_local = window.localStorage.getItem('productcomparelocal');
				if (!compare_local) {
					window.localStorage.setItem('productcomparelocal', post_id);
					compare_local = window.localStorage.getItem('productcomparelocal');
					$(this).addClass('loading');
					$(this).removeClass('no-added');
					if (AJ_Options.compare_toast) {
						SomniashowToast(post_id, 'compare', 'add');
					}
				} else {
					var compare_arr = compare_local.split(',');
					if (!compare_arr.includes(post_id)) {
						window.localStorage.setItem('productcomparelocal', compare_local + ',' + post_id);
						compare_local = window.localStorage.getItem('productcomparelocal');
						$(this).addClass('loading');
						$(this).removeClass('no-added');
						if (AJ_Options.compare_toast) {
							SomniashowToast(post_id, 'compare', 'add');
						}
					}
				}
				var param_ajax = {
					action: 'somnia_products_compare',
					compare_data: compare_local,
				};
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: AJ_Options.ajax_url,
					data: param_ajax,
					beforeSend: function () {

					},
					success: function (response) {
						if (response.success) {
							$('.bt-product-compare-btn[data-id="' + post_id + '"]').removeClass('loading');
							$('.bt-product-compare-btn[data-id="' + post_id + '"]').addClass('added');
							showComparePopup();
							$('.bt-popup-compare .bt-compare-load').html(response.data['product']).fadeIn('slow');
							SomniaCompareContentScroll();
							// Update mini compare count
							var compare_count = compare_local ? compare_local.split(',').filter(function (item) { return item !== ''; }).length : 0;
							$('.bt-mini-compare .compare_total').html(compare_count);
							// Trigger custom event for widget update
							$(document).trigger('somnia_compare_updated');
							// close popup quick view
							if ($('.bt-popup-quick-view').hasClass('active')) {
								$('.bt-quick-view-body').removeClass('show');
								setTimeout(function () {
									$('.bt-popup-quick-view').removeClass('active');
								}, 300);
							}
						} else {
							console.log('error');
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.log('The following error occured: ' + textStatus, errorThrown);
					}
				});
			});
			// Prevent all click events from bubbling to Elementor popup
			$(document).on('click', '.bt-popup-compare .bt-compare-overlay', function (e) {
				e.stopPropagation();
				removeComparePopup();
			});
			$(document).on('click', '.bt-popup-compare .bt-compare-close', function (e) {
				e.stopPropagation();
				if (!$('.bt-popup-compare').hasClass('bt-compare-elwwg')) {
					removeComparePopup();
				}
			});
			$(document).on('keydown', function (e) {
				if (e.key === 'Escape' && $('.bt-popup-compare').hasClass('active')) {
					e.stopPropagation();
					removeComparePopup();
				}
			});
		}
		$(document).on('click', '.bt-product-add-compare .bt-cover-image', function () {
			$('.bt-compare-body').removeClass('show');
			setTimeout(function () {
				if (!$('.bt-popup-compare').hasClass('bt-compare-elwwg')) {
					$('.bt-popup-compare').removeClass('active');
					$('body').css({
						'overflow': 'auto', // Restore body scroll
						'padding-right': '0' // Reset padding-right
					});
				} else {
					window.location.href = AJ_Options.shop;
				}
			}, 300);
		});

		$(document).on('click', '.bt-remove-item', function (e) {
			e.preventDefault();
			var compare_local = window.localStorage.getItem('productcomparelocal');
			var product_id = $(this).data('id').toString(),
				compare_arr = compare_local.split(','),
				index = compare_arr.indexOf(product_id);

			if (index > -1) {
				compare_arr.splice(index, 1);
			}
			window.localStorage.setItem('productcomparelocal', compare_arr);
			compare_local = window.localStorage.getItem('productcomparelocal');
			if ($('.bt-popup-compare').hasClass('bt-compare-elwwg')) {
				SomniaShareLocalStorage(compare_local);
			}
			$('.bt-product-compare-btn[data-id="' + product_id + '"]').addClass('no-added');
			$('.bt-product-compare-btn[data-id="' + product_id + '"]').removeClass('added');
			// Update mini compare count
			var compare_count = compare_local && compare_local !== '' ? compare_local.split(',').filter(function (item) { return item !== ''; }).length : 0;
			$('.bt-mini-compare .compare_total').html(compare_count);
			// Trigger custom event for widget update
			$(document).trigger('somnia_compare_updated');
			if (!$('.bt-popup-compare').hasClass('bt-compare-elwwg')) {
				if (!compare_local || compare_local === '') {
					removeComparePopup();
					$('.bt-compare-body').removeClass('loading');
					return;
				}
			}
			// Get the specific table before removing the row
			var tableCompare = $(this).closest('.bt-table-compare');
			$(this).closest('.bt-table--row').remove();

			// Count rows only in the specific table
			let itemCompareCount = tableCompare.find('.bt-table--row').length;
			if (itemCompareCount == 5) {
				tableCompare.find('.bt-table--row.bt-product-add-compare').first().addClass('active');
			} else if (itemCompareCount == 4) {
				tableCompare.find('.bt-table--row.bt-product-add-compare').slice(0, 2).addClass('active');
			} else if (itemCompareCount == 3) {
				tableCompare.find('.bt-table--row.bt-product-add-compare').slice(0, 3).addClass('active');
			}
		});
	}
	/* Product Compare Load */
	function SomniaProductCompareLoad() {
		var compare_local = window.localStorage.getItem('productcomparelocal');
		if (!compare_local) {
			return;
		}
		if ($('.elementor-widget-bt-product-compare').length > 0) {
			SomniaShareLocalStorage(compare_local);
			var param_ajax = {
				action: 'somnia_products_compare',
				compare_data: compare_local
			};
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: AJ_Options.ajax_url,
				data: param_ajax,
				beforeSend: function () {
					$('.bt-compare-body').addClass('loading');
				},
				success: function (response) {
					if (response.success) {
						$('.bt-popup-compare .bt-compare-load').html(response.data['product']).fadeIn('slow');
						SomniaCompareContentScroll();
						$('.bt-compare-body').removeClass('loading');
					} else {
						console.log('error');
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('The following error occured: ' + textStatus, errorThrown);
				}
			});
		}
	}
	/* Product Quick View */
	function SomniaProductQuickView() {
		var hasQuickViewTrigger = $('.bt-product-quick-view-btn').length > 0 || $('.bt-loop-add-to-cart-btn').length > 0;
		if (hasQuickViewTrigger) {
			if ($('.bt-popup-quick-view').length === 0) {
				$('body').append('<div class="bt-popup-quick-view"><div class="bt-quick-view-overlay"></div><div class="bt-quick-view-body"><div class="bt-quick-view-load"></div></div></div>');
			}
			function showQuickViewPopup() {
				$('.bt-popup-quick-view').addClass('active');
				$('.bt-quick-view-body').addClass('show');
				$('body').css({
					'overflow': 'hidden',
					'padding-right': `${window.innerWidth - $(window).width()}px` // Prevent layout shift
				}); // Disable body scroll
			}
			function removeQuickViewPopup() {
				$('.bt-quick-view-body').removeClass('show');
				setTimeout(function () {
					$('.bt-popup-quick-view').removeClass('active');
				}, 300);
				$('body').css({
					'overflow': 'auto', // Restore body scroll
					'padding-right': '0' // Reset padding-right
				});
			}
			function openQuickViewByProductId($btn, productid, source) {
				if (!productid) return;
				$btn.find('.tooltip').remove();
				$btn.addClass('loading');
				var param_ajax = {
					action: 'somnia_products_quick_view',
					productid: productid,
					source: source || 'quickview'
				};
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: AJ_Options.ajax_url,
					data: param_ajax,
					success: function (response) {
						$('.bt-product-quick-view-btn, .bt-loop-add-to-cart-btn').removeClass('loading');
						if (response.success) {
							showQuickViewPopup();
							$('.bt-popup-quick-view .bt-quick-view-load').html(response.data['product']).fadeIn('slow');
							SomniaLoadShopQuickView();
							SomniaProductButtonStatus();
							SomniaLoadDefaultActiveVariations('.bt-popup-quick-view');
							if (typeof $.fn.wc_variation_form !== 'undefined') {
								$('.bt-quickview-product .variations_form').wc_variation_form();
							}
							SomniaProductVariationHandler();
						} else {
							console.log('error');
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						$('.bt-product-quick-view-btn, .bt-loop-add-to-cart-btn').removeClass('loading');
						console.log('The following error occured: ' + textStatus, errorThrown);
					}
				});
			}
			$(document).on('click', '.bt-product-quick-view-btn', function (e) {
				e.preventDefault();
				var productid = $(this).data('id');
				openQuickViewByProductId($(this), productid, 'quickview');
			});
			/* .bt-loop-add-to-cart-btn: open quick view when not inside popup (e.g. product loop) */
			$(document).on('click', '.bt-loop-add-to-cart-btn', function (e) {
				if ($(this).closest('.bt-popup-quick-view, .bt-quickview-product').length > 0) return; // inside quick view: keep add-to-cart
				var productid = $(this).data('product_id') || $(this).data('productId') || $(this).data('id');
				if (!productid) return;
				e.preventDefault();
				openQuickViewByProductId($(this), productid, 'add-to-cart');
			});
			$(document).on('click', '.bt-popup-quick-view .bt-quick-view-overlay', function () {
				removeQuickViewPopup();
			});
			$(document).on('click', '.bt-popup-quick-view .bt-quick-view-close', function () {
				removeQuickViewPopup();
			});
			$(document).on('keydown', function (e) {
				if (e.key === 'Escape') {
					removeQuickViewPopup();
				}
			});
		}

	}
	/* Product Toast */
	function LoadSomniaProductToast() {
		if (AJ_Options.wishlist_toast || AJ_Options.compare_toast || AJ_Options.cart_toast) {
			$('body').append('<div class="bt-toast"></div>');
		}
	}
	/* Helper function to handle cart toast vs cart mini logic */
	function SomniaHandleCartAction(productId) {
		// Check if page has bt-loop-add-show-cart class - if yes, always show mini cart
		if ($('.bt-loop-add-show-minicart').length > 0) {
			SomniaOpenMiniCart();
			return;
		}

		var cart_toast = AJ_Options.cart_toast || false;
		var show_cart_mini = AJ_Options.show_cart_mini || false;
		var isMobile = $(window).width() <= 1023;

		// Logic: If both are enabled, prioritize cart_toast (desktop: toast, mobile: mini cart)
		// If only show_cart_mini is enabled: show mini cart on both desktop and mobile
		// If only cart_toast is enabled: desktop show toast, mobile show mini cart
		// If both are disabled: do nothing

		if (cart_toast) {
			// cart_toast is enabled
			if (!isMobile) {
				// Desktop: show toast
				SomniashowToast(productId, 'cart', 'add');
			} else {
				// Mobile: show mini cart
				SomniaOpenMiniCart();
			}
		} else if (show_cart_mini) {
			// Only show_cart_mini is enabled: show mini cart on both desktop and mobile
			SomniaOpenMiniCart();
		}
		// If both are false, do nothing
	}

	/* Helper function to open mini cart sidebar */
	function SomniaOpenMiniCart() {
		if ($('.bt-mini-cart-sidebar').length > 0) {
			const $sidebar = $('.bt-mini-cart-sidebar');
			$sidebar.addClass('active');
			const scrollbarWidth = window.innerWidth - $(window).width();
			$('body').css({
				'overflow': 'hidden',
				'padding-right': scrollbarWidth + 'px'
			});
			// Update bottom cart padding
			setTimeout(function () {
				const $bottomCart = $sidebar.find('.bt-bottom-mini-cart');
				const $sidebarBody = $sidebar.find('.bt-mini-cart-sidebar-body');
				if ($bottomCart.length && $sidebarBody.length) {
					const height = $bottomCart.outerHeight(true);
					$sidebarBody.css('--padding-bottom', height + 'px');
				}
			}, 100);
		}
	}
	function SomniashowToast(idproduct, tools = 'wishlist', status = 'add') {
		// ajax load product toast
		var toastTimeshow;
		if (tools === 'wishlist' && AJ_Options.wishlist_toast_time) {
			toastTimeshow = AJ_Options.wishlist_toast_time;
		} else if (tools === 'compare' && AJ_Options.compare_toast_time) {
			toastTimeshow = AJ_Options.compare_toast_time;
		} else if (tools === 'cart' && AJ_Options.cart_toast_time) {
			toastTimeshow = AJ_Options.cart_toast_time;
		} else {
			toastTimeshow = 3000; // Default fallback time
		}
		var param_ajax = {
			action: 'somnia_load_product_toast',
			idproduct: idproduct,
			status: status,
			tools: tools
		};
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: AJ_Options.ajax_url,
			data: param_ajax,
			beforeSend: function () {
			},
			success: function (response) {
				if (response.success) {
					// Append and show new toast
					$('.bt-toast').append(response.data['toast']);
					const $newToast = $('.bt-toast .bt-product-toast').last();
					setTimeout(() => {
						$newToast.addClass('show');
					}, 100);
					// Handle close button click
					$newToast.find('.bt-product-toast--close').on('click', function () {
						removeToast($newToast);
					});
					let toastTimeout;

					function startRemovalTimer($toast) {
						toastTimeout = setTimeout(() => {
							removeToast($toast);
						}, toastTimeshow);
					}

					// Handle hover events
					$newToast.hover(
						function () {
							// On mouse enter, clear the timeout
							clearTimeout(toastTimeout);
						},
						function () {
							// On mouse leave, start a new timeout
							startRemovalTimer($(this));
						}
					);

					// Start initial removal timer
					startRemovalTimer($newToast);

					function removeToast($toast) {
						$toast.addClass('remove-visibility');

						// Remove toast element after animation
						setTimeout(() => {
							$toast.addClass('remove-height');
							setTimeout(() => {
								$toast.remove();
							}, 300);
						}, 300);
					}
				}
			},
			error: function (xhr, status, error) {
				console.error('Ajax request failed:', {
					status: status,
					error: error,
					response: xhr.responseText
				});
			}
		});

	}
	/* load Show filter Tag */
	function SomniaLoadFilterTagProduct() {
		if ($('body').hasClass('archive') && $('body').hasClass('post-type-archive-product')) {
			const url = new URL(window.location.href);
			const params = new URLSearchParams(url.search);

			// Check if formsearch=true exists in URL before deleting it
			var hasFormSearch = params.has('formsearch') && params.get('formsearch') === 'true';

			// Store product_cat value IMMEDIATELY before any deletion operations
			var productCatValue = params.get('product_cat');

			params.delete('current_page');
			params.delete('sort_order');
			params.delete('view_type');
			params.delete('search_keyword');
			params.delete('content_width');
			params.delete('sidebar_position');
			params.delete('layout-pagination');
			params.delete('layout-titlebar');
			params.delete('layout-bottom-titlebar');
			params.delete('layout-shop');
			params.delete('formsearch');
			params.delete('excluded_product_cat');
			// Clean up URL params by removing empty values
			for (const [key, value] of params.entries()) {
				//	console.log(key, value);
				if (!value) {
					params.delete(key);
				}
				if (key.startsWith('customize_')) {
					params.delete(key);
				}
			}

			// Check if we're on a category page or taxonomy page and get info from data attributes
			var $sidebar = $('.bt-product-sidebar');
			var isCategoryPage = $sidebar.data('is-category-page') === 1;
			var isTaxonomyPage = $sidebar.data('is-taxonomy-page') === 1;
			var taxonomyType = $sidebar.data('taxonomy-type') || '';

			// Remove product_cat from params when on category page 
			if (isCategoryPage) {
				params.delete('product_cat');
			}

			// Remove taxonomy from params when on taxonomy page
			if (isTaxonomyPage && taxonomyType) {
				params.delete(taxonomyType);
			}

			// When formsearch=true, handle product_cat based on slug count
			if (hasFormSearch && productCatValue) {
				const slugCount = productCatValue.split(',').filter(slug => slug.trim() !== '').length;
				if (slugCount >= 2) {
					params.delete('product_cat');
				} else {
					params.set('product_cat', productCatValue);
				}
			}

			const hasValidParams = params.size > 0;
			if (hasValidParams) {
				const tagsContainer = $('.bt-list-tag-filter').addClass('active');
				tagsContainer.find('.bt-reset-filter-product-btn').removeClass('disable');
				tagsContainer.children().not('.bt-reset-filter-product-btn').remove();

				let minPrice = '', maxPrice = '';
				const svgElement = `<svg class="bt-close" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
					<path d="M12 4L4 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M4 4L12 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
				  </svg>`;
				const svgStar = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
				  <path d="M14.6431 7.17815L11.8306 9.60502L12.6875 13.2344C12.7347 13.4314 12.7226 13.638 12.6525 13.8281C12.5824 14.0182 12.4575 14.1833 12.2937 14.3025C12.1298 14.4217 11.9343 14.4896 11.7319 14.4977C11.5294 14.5059 11.3291 14.4538 11.1562 14.3481L7.99996 12.4056L4.84184 14.3481C4.66898 14.4532 4.4689 14.5048 4.2668 14.4963C4.06469 14.4879 3.8696 14.4199 3.70609 14.3008C3.54257 14.1817 3.41795 14.0169 3.3479 13.8272C3.27786 13.6374 3.26553 13.4312 3.31246 13.2344L4.17246 9.60502L1.35996 7.17815C1.20702 7.04597 1.09641 6.87166 1.04195 6.67699C0.987486 6.48232 0.99158 6.27592 1.05372 6.08356C1.11586 5.89121 1.23329 5.72142 1.39135 5.59541C1.54941 5.4694 1.7411 5.39274 1.94246 5.37502L5.62996 5.07752L7.05246 1.63502C7.12946 1.44741 7.26051 1.28693 7.42894 1.17398C7.59738 1.06104 7.7956 1.00073 7.9984 1.00073C8.2012 1.00073 8.39942 1.06104 8.56785 1.17398C8.73629 1.28693 8.86734 1.44741 8.94434 1.63502L10.3662 5.07752L14.0537 5.37502C14.2555 5.39209 14.4477 5.46831 14.6064 5.59415C14.765 5.71999 14.883 5.88984 14.9455 6.08243C15.008 6.27502 15.0123 6.48178 14.9579 6.6768C14.9034 6.87183 14.7926 7.04644 14.6393 7.17877L14.6431 7.17815Z" fill="#FDCC0D"></path>
				</svg>`;

				params.forEach((value, key) => {
					// Skip taxonomy if on taxonomy page
					if (isTaxonomyPage && key === taxonomyType) {
						return;
					}

					const tags = value.split(/[,; ]+/); // Split value by comma, semicolon, or space
					tags.forEach(tag => {
						const tagElement = $(`<span class="bt-filter-tag" data-name="${key}" data-slug="${tag.trim()}"></span>`); // Updated to use tag.trim() for data-slug

						if (key == 'product_cat' && tag != '') {
							const matchingLink = $(`.bt-form-field[data-name="${key}"] label`).filter(function () {
								return $(this).data('slug') === tag.trim();
							});
							if (matchingLink.length) {
								const nameTag = matchingLink.text().trim();
								tagElement.text(nameTag).append(svgElement);
							} else {
								const nameTag = tag.split('-')
									.map(word => word.charAt(0).toUpperCase() + word.slice(1))
									.join(' ');
								tagElement.text(nameTag).append(svgElement);
							}
							tagsContainer.append(tagElement);
						} else if (key == 'min_price') {
							minPrice = tag.trim();
						} else if (key == 'max_price') {
							maxPrice = tag.trim();
						} else if (key == 'product_rating') {
							const product_rating = tag.trim();
							//	console.log(product_rating);
							tagElement.text(product_rating).append(svgStar).append(svgElement).addClass('bt-rating-tag');;
							tagsContainer.append(tagElement);
						} else {
							const matchingLink = $(`.bt-form-field[data-name="${key}"] .bt-field-list a`).filter(function () {
								return $(this).data('slug') === tag.trim();
							});
							if (matchingLink.length) {
								const colorTaxonomy = AJ_Options.color_taxonomy;
								console.log(colorTaxonomy);
								if (key == colorTaxonomy) {
									const colortag = matchingLink.prop('outerHTML');
									//	console.log(colortag);
									tagElement.html(colortag).append(svgElement).addClass('bt-color-tag');
								} else {
									const textTag = matchingLink.text().trim();
									const nameTag = textTag.replace(/\(\d+\)$/g, '').trim();
									tagElement.text(nameTag).append(svgElement);
								}
								tagsContainer.append(tagElement);
							} else {
								if (tag != '') {
									const titleCase = tag.split('-')
										.map(word => word.charAt(0).toUpperCase() + word.slice(1))
										.join(' ');
									tagElement.text(titleCase).append(svgElement);
									tagsContainer.append(tagElement);
								}
							}
						}
					});
				});

				if (minPrice && maxPrice) {
					if (minPrice && maxPrice) {
						const currency = $('.bt-field-price .bt-field-min-price .bt-currency').text().trim();
						const priceElement = $(`<span class="bt-filter-tag bt-tag-price" data-name="product_price">${currency}${minPrice} - ${currency}${maxPrice}</span>`).append(svgElement);
						tagsContainer.append(priceElement);
					}
				}
			} else {
				// No params - hide filter tags
				$('.bt-list-tag-filter').removeClass('active');
				$('.bt-list-tag-filter').children().not('.bt-reset-filter-product-btn').remove();
			}
		}
	}
	/* Generate Skeleton HTML Helper */
	function SomniaGenerateSkeletonHTML(count = 12) {
		let skeletonHtml = '';
		for (let i = 0; i < count; i++) {
			skeletonHtml += `
				<div class="bt-product-skeleton product">
					<div class="bt-skeleton-thumbnail"></div>
					<div class="bt-skeleton-content">
						<div class="bt-skeleton-title"></div>
						<div class="bt-skeleton-price"></div>
						<div class="bt-skeleton-rating"></div>
						<div class="bt-skeleton-description"></div>
						<div class="bt-skeleton-action"></div>
					</div>
				</div>
			`;
		}
		return skeletonHtml;
	}
	/* Product Filter */
	function SomniaProductsFilter() {
		if (!$('body').hasClass('post-type-archive-product')) {
			return;
		}
		// Hide topbar if not-found-products exists on page load
		if ($('.not-found-products').length > 0) {
			$('.bt-products-topbar').addClass('bt-hide');
		}
		// Search by keywords
		$('.bt-product-filter-form .bt-field-type-search input').on('keyup', function (e) {
			if (e.key === 'Enter' || e.keyCode === 13) {
				$('.bt-product-filter-form .bt-product-current-page').val('');
				$('.bt-product-filter-form').submit();
			}
		});

		$('.bt-product-filter-form  .bt-field-type-search a').on('click', function (e) {
			e.preventDefault();
			$('.bt-product-filter-form .bt-product-current-page').val('');
			$('.bt-product-filter-form').submit();
		});

		// View type
		$(document).on('click', '.bt-view-type', function (e) {
			e.preventDefault();

			var view_type = $(this).data('view');

			if ('grid-3' == view_type) {
				$('.bt-product-filter-form .bt-product-view-type').val('');
				$('.bt-product-layout').attr('data-view', '');
			} else {
				$('.bt-product-filter-form .bt-product-view-type').val(view_type);
				$('.bt-product-layout').attr('data-view', view_type);
			}

			$('.bt-view-type').removeClass('active');
			$(this).addClass('active');
			//	$('.bt-product-filter-form').submit();

			// Get current URL params
			var urlParams = new URLSearchParams(window.location.search);

			// Get pagination type
			var paginationType = $('.bt-product-layout').data('pagination-type');

			// Get form params
			var param_in = $('.bt-product-filter-form').serialize().split('&');

			param_in.forEach(function (param) {
				var param_key = param.split('=')[0],
					param_val = param.split('=')[1];

				// Skip current_page param for infinite scroll and load more button
				if ((paginationType === 'infinite-scrolling' || paginationType === 'button-load-more') && param_key === 'current_page') {
					return;
				}

				// Update or add form params to URL params
				if ('' !== param_val) {
					urlParams.set(param_key, decodeURIComponent(param_val));
				} else {
					// Remove empty params from URL
					urlParams.delete(param_key);
				}
			});

			var param_str = urlParams.toString();

			if ('' !== param_str) {
				window.history.replaceState(null, null, `?${param_str}`);
				$(this).find('.bt-reset-filter-product-btn').removeClass('disable');
			} else {
				window.history.replaceState(null, null, window.location.pathname);
				$(this).find('.bt-reset-filter-product-btn').addClass('disable');
			}
		});

		//Sort order
		$('.bt-product-sort-block select').select2({
			dropdownParent: $('.bt-product-sort-block'),
			minimumResultsForSearch: Infinity
		});
		$('.bt-product-sort-block select').on('change', function () {
			var sort_val = $(this).val();

			$('.bt-product-filter-form .bt-product-sort-order').val(sort_val);
			$('.bt-product-filter-form').submit();
		});


		// Pagination
		$('.bt-product-pagination-wrap').on('click', '.bt-product-pagination a', function (e) {
			e.preventDefault();

			var current_page = $(this).data('page');

			if (1 < current_page) {
				$('.bt-product-filter-form .bt-product-current-page').val(current_page);
			} else {
				$('.bt-product-filter-form .bt-product-current-page').val('');
			}

			$('.bt-product-filter-form').submit();
		});

		// Filter Slider
		if ($('#bt-price-slider').length > 0) {
			const priceSlider = document.getElementById('bt-price-slider');

			var rangeMin = $('#bt-price-slider').data('range-min'),
				rangeMax = $('#bt-price-slider').data('range-max'),
				startMin = $('#bt-price-slider').data('start-min'),
				startMax = $('#bt-price-slider').data('start-max');
			noUiSlider.create(priceSlider, {
				start: [startMin, startMax],
				connect: true,
				range: {
					'min': rangeMin,
					'max': rangeMax
				},
				step: 1
			});

			const minPriceValue = $('#bt-min-price');
			const maxPriceValue = $('#bt-max-price');
			let timeout;
			priceSlider.noUiSlider.on('change', function (values, handle) {
				minPriceValue.val(parseInt(values[0]));
				maxPriceValue.val(parseInt(values[1]));
				$('.bt-product-filter-form .bt-product-current-page').val('');
				$('.bt-product-filter-form').submit();
			});
			minPriceValue.on('input', function () {
				clearTimeout(timeout);
				timeout = setTimeout(function () {
					let minValue = parseInt(minPriceValue.val());
					const maxValue = parseInt(maxPriceValue.val());

					if (!isNaN(minValue)) {
						if (minValue > maxValue) {
							minValue = maxValue;
							minPriceValue.val(minValue);
						} else if (minValue < rangeMin) {
							minValue = rangeMin;
							minPriceValue.val(minValue);
						}
						priceSlider.noUiSlider.set([minValue, null]);
						if (!isNaN(maxValue)) {
							$('.bt-product-filter-form .bt-product-current-page').val('');
							$('.bt-product-filter-form').submit();
						} else {
							maxPriceValue.val(rangeMax);
							$('.bt-product-filter-form .bt-product-current-page').val('');
							$('.bt-product-filter-form').submit();
						}
					}
				}, 500);
			});

			maxPriceValue.on('input', function () {
				clearTimeout(timeout);
				timeout = setTimeout(function () {
					const minValue = parseInt(minPriceValue.val());
					let maxValue = parseInt(maxPriceValue.val());

					if (!isNaN(maxValue)) {
						if (maxValue < minValue) {
							maxValue = minValue;
							maxPriceValue.val(maxValue);
						} else if (maxValue > rangeMax) {
							maxValue = rangeMax;
							maxPriceValue.val(maxValue);
						}
						priceSlider.noUiSlider.set([null, maxValue]);
						if (!isNaN(minValue)) {
							$('.bt-product-filter-form .bt-product-current-page').val('');
							$('.bt-product-filter-form').submit();
						} else {
							minPriceValue.val(rangeMin);
							$('.bt-product-filter-form .bt-product-current-page').val('');
							$('.bt-product-filter-form').submit();
						}
					}
				}, 500);
			});
		}

		//Filter single tax
		if ($('.bt-field-type-radio').length > 0) {
			$('.bt-field-type-radio input').on('change', function () {
				// If clicking on product_cat filter, remove excluded_product_cat from URL
				if ($(this).attr('name') === 'product_cat') {
					var urlParams = new URLSearchParams(window.location.search);
					urlParams.delete('excluded_product_cat');
					var param_str = urlParams.toString();
					if ('' !== param_str) {
						window.history.replaceState(null, null, `?${param_str}`);
					} else {
						window.history.replaceState(null, null, window.location.pathname);
					}
				}

				var url = $(this).closest('.item-radio').data('url');
				if (url) {
					window.location.href = url;
				} else {
					$('.bt-product-filter-form .bt-product-current-page').val('');
					$('.bt-product-filter-form').submit();
				}
			});

			// Toggle subcategories
			$('.bt-toggle-children').on('click', function (e) {
				e.preventDefault();
				e.stopPropagation();

				var $parent = $(this).closest('.item-radio');
				// Only get direct children, not nested ones
				var $children = $parent.children('.bt-children-categories').first();

				if ($parent.hasClass('open')) {
					$parent.removeClass('open');
					$children.slideUp(300);
					// Close all nested children when closing parent
					$children.find('.item-radio.has-children').removeClass('open');
					$children.find('.bt-children-categories').slideUp(300);
				} else {
					$parent.addClass('open');
					$children.slideDown(300);
				}
			});

			// Auto open subcategories if a child is selected (recursively open all parent levels)
			$('.bt-field-type-radio input:checked').each(function () {
				var $checkedItem = $(this).closest('.item-radio');
				// If this is a child item, open all parent categories to show the selected item
				if ($checkedItem.hasClass('item-radio-child')) {
					// Find all parent .bt-children-categories containers and open them
					// This opens all parent levels to show the selected item
					$checkedItem.parents('.bt-children-categories').each(function () {
						var $parent = $(this).closest('.item-radio.has-children');
						if ($parent.length) {
							$parent.addClass('open');
							$(this).show();
						}
					});
				}
			});
		}

		//Filter multiple tax
		if ($('.bt-field-type-multi').length > 0) {
			$('.bt-field-type-multi a').on('click', function (e) {
				e.preventDefault();

				if ($(this).parent().hasClass('checked')) {
					$(this).parent().removeClass('checked');
				} else {
					$(this).parent().addClass('checked');
				}

				var value_arr = [];

				$(this).parents('.bt-form-field').find('.bt-field-item').each(function () {
					if ($(this).hasClass('checked')) {
						value_arr.push($(this).children().data('slug'));
					}
				});
				$(this).parents('.bt-form-field').find('input').val(value_arr.toString());
				$('.bt-product-filter-form .bt-product-current-page').val('');
				$('.bt-product-filter-form').submit();
			});
		}
		//Filter rating
		if ($('.bt-field-type-rating ').length > 0) {
			$('.bt-field-type-rating input').on('change', function () {
				$('.bt-product-filter-form .bt-product-current-page').val('');
				$('.bt-product-filter-form').submit();
			});
		}
		// Filter field toggle (expand/collapse)
		if ($('.bt-field-title').length > 0) {
			$('.bt-field-title').on('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				var $formField = $(this).closest('.bt-form-field');
				$formField.toggleClass('bt-field-collapsed');
			});
		}
		// Filter Product Tag remove
		$(document).on('click', '.bt-filter-tag .bt-close', function (e) {
			e.preventDefault();
			var tagSlug = $(this).parent().data('slug');
			var tagName = $(this).parent().data('name');

			// Check if the field has radio buttons dynamically
			var hasRadioButtons = $(`.bt-product-filter-form .bt-form-field[data-name="${tagName}"] input[type="radio"]`).length > 0;

			if (hasRadioButtons) {
				// For radio button fields, uncheck all radio buttons
				$(`.bt-product-filter-form .bt-form-field[data-name="${tagName}"] input[type="radio"]`).prop('checked', false);
			} else {
				// For other field types (checkboxes, multi-select, etc.)
				if (typeof tagSlug !== 'undefined' && tagSlug !== null && tagSlug !== '') {
					var currentValue = $(`.bt-product-filter-form .bt-form-field[data-name="${tagName}"] input`).val();
					var valuesArray = currentValue.split(',');
					var updatedValues = valuesArray.filter(value => value !== tagSlug);
					$(`.bt-product-filter-form .bt-form-field[data-name="${tagName}"] input`).val(updatedValues.join(','));
					$(`.bt-product-filter-form .bt-form-field[data-name="${tagName}"] .bt-field-item a[data-slug="${tagSlug}"]`).parent().removeClass('checked');
				} else {
					$(`.bt-product-filter-form .bt-form-field[data-name="${tagName}"] input`).val('');
					$(`.bt-product-filter-form .bt-form-field[data-name="${tagName}"] .bt-field-item`).removeClass('checked');
				}
			}
			$('.bt-product-filter-form').submit();
		});
		// Filter reset
		var $sidebar = $('.bt-product-sidebar');
		var isCategoryPage = $sidebar.data('is-category-page') === 1;
		var isTaxonomyPage = $sidebar.data('is-taxonomy-page') === 1;
		if (window.location.href.includes('?') || isCategoryPage || isTaxonomyPage) {
			$('.bt-product-filter-form .bt-reset-filter-product-btn').removeClass('disable');
		}

		// Use event delegation to handle dynamically created reset buttons
		$(document).on('click', '.bt-reset-filter-product-btn', function (e) {
			e.preventDefault();

			if ($(this).hasClass('disable')) {
				return;
			}
			$('.bt-list-tag-filter').removeClass('active');
			$('.bt-list-tag-filter').children().not('.bt-reset-filter-product-btn').remove();

			// Get current URL params
			var urlParams = new URLSearchParams(window.location.search);

			// Get all form field names to remove only form-related params
			var formParams = [];
			$('.bt-product-filter-form').serializeArray().forEach(function (item) {
				if (!formParams.includes(item.name)) {
					formParams.push(item.name);
				}
			});

			// Remove only form-related params from URL
			formParams.forEach(function (paramName) {
				urlParams.delete(paramName);
			});

			// Remove formsearch=true when resetting filters
			urlParams.delete('formsearch');

			var param_str = urlParams.toString();
			if ('' !== param_str) {
				window.history.replaceState(null, null, `?${param_str}`);
			} else {
				window.history.replaceState(null, null, window.location.pathname);
			}

			// Check if we're on a category page
			var $sidebar = $('.bt-product-sidebar');
			var isCategoryPage = $sidebar.data('is-category-page') === 1;

			if (isCategoryPage) {
				// Reset all inputs except product_cat on category page
				$('.bt-product-filter-form input').not('[type="radio"]').not('[name="product_cat"]').val('');
				$('.bt-product-filter-form input[type="radio"]').not('[name="product_cat"]').prop('checked', false);
				$('.bt-product-filter-form input[type="checkbox"]').not('[name="product_cat"]').prop('checked', false);
				$('.bt-product-filter-form .bt-field-item').not('.bt-form-field[data-name="product_cat"] .bt-field-item').removeClass('checked');
			} else {
				// Reset all inputs including product_cat on other pages
				$('.bt-product-filter-form input').not('[type="radio"]').val('');
				$('.bt-product-filter-form input[type="radio"]').prop('checked', false);
				$('.bt-product-filter-form input[type="checkbox"]').prop('checked', false);
				$('.bt-product-filter-form .bt-field-item').removeClass('checked');
			}

			$('.bt-product-filter-form select').select2().val('').trigger('change');
			$(this).addClass('disable')

			$('.bt-product-filter-form').submit();
		});
		// Ajax filter
		$('.bt-product-filter-form').submit(function () {
			// Check if we're on a category page or taxonomy page
			var $sidebar = $('.bt-product-sidebar');
			var isCategoryPage = $sidebar.data('is-category-page') === 1;
			var isTaxonomyPage = $sidebar.data('is-taxonomy-page') === 1;
			var taxonomyType = $sidebar.data('taxonomy-type') || '';
			var taxonomySlug = $sidebar.data('taxonomy-slug') || '';

			// Get current URL params
			var urlParams = new URLSearchParams(window.location.search);

			// Check if formsearch=true exists in URL
			var hasFormSearch = urlParams.has('formsearch') && urlParams.get('formsearch') === 'true';

			// Store product_cat and formsearch from URL if formsearch=true exists
			var preservedProductCat = '';
			if (hasFormSearch && urlParams.has('product_cat')) {
				preservedProductCat = urlParams.get('product_cat');
			}

			// Remove product_cat from URL params if on category page (but preserve if formsearch=true)
			if (isCategoryPage && !hasFormSearch) {
				urlParams.delete('product_cat');
			}

			// Remove taxonomy from URL params if on taxonomy page
			if (isTaxonomyPage && taxonomyType) {
				urlParams.delete(taxonomyType);
			}

			// NOTE: jQuery serialize() omits unchecked radio groups entirely.
			// To ensure radio group names always exist (even when nothing is checked),
			// build params from serializeArray() and then append empty entries for
			// any radio groups with no selection.
			var dataArr = $(this).serializeArray();

			// Get product_cat from form BEFORE removing it (for both ajax and URL update)
			var formProductCat = '';
			var productCatItem = dataArr.find(function (item) {
				return item.name === 'product_cat';
			});
			if (productCatItem && productCatItem.value) {
				formProductCat = productCatItem.value;
			} else {
				// Fallback: get from checked radio input
				var productCatInput = $(this).find('input[name="product_cat"]:checked');
				if (productCatInput.length > 0) {
					formProductCat = productCatInput.val();
				}
			}

			// Get category slug from form before removing it (for ajax request)
			var categorySlug = '';
			if (isCategoryPage) {
				// Try to get from data attribute first (most reliable)
				categorySlug = $sidebar.data('category-slug') || '';
				if (!categorySlug) {
					// Use formProductCat if available
					if (formProductCat) {
						categorySlug = formProductCat;
					}
				}
			}

			// Get taxonomy slug from data attribute (for ajax request)
			if (isTaxonomyPage && !taxonomySlug) {
				// Fallback: try to get from serializeArray
				var taxonomyItem = dataArr.find(function (item) {
					return item.name === taxonomyType;
				});
				if (taxonomyItem && taxonomyItem.value) {
					taxonomySlug = taxonomyItem.value;
				}
			}

			// Remove product_cat from form data if on category page
			if (isCategoryPage) {
				dataArr = dataArr.filter(function (item) {
					return item.name !== 'product_cat';
				});
			}

			// Remove taxonomy from form data if on taxonomy page
			if (isTaxonomyPage && taxonomyType) {
				dataArr = dataArr.filter(function (item) {
					return item.name !== taxonomyType;
				});
			}

			// Add empty values for unchecked radio groups
			$(this).find('input[type="radio"][name]').each(function () {
				var radioName = this.name;
				// Skip product_cat if on category page
				if (isCategoryPage && radioName === 'product_cat') {
					return;
				}
				// Skip taxonomy if on taxonomy page
				if (isTaxonomyPage && radioName === taxonomyType) {
					return;
				}
				var hasValue = dataArr.some(item => item.name === radioName);
				if (!hasValue) {
					dataArr.push({ name: radioName, value: '' });
				}
			});

			// Convert to param strings
			var param_in = dataArr.map(p =>
				encodeURIComponent(p.name) + '=' + encodeURIComponent(p.value)
			);
			var param_ajax = {
				action: 'somnia_products_filter',
			};

			// Add category slug to ajax params
			// Priority: form value > preserved value (when formsearch=true) > categorySlug
			if (formProductCat) {
				// User clicked on filter, use form value
				param_ajax['product_cat'] = formProductCat.replace(/%2C/g, ',');
				// Remove excluded_product_cat when clicking on product_cat filter
				urlParams.delete('excluded_product_cat');
				// Also remove from form data array
				dataArr = dataArr.filter(function (item) {
					return item.name !== 'excluded_product_cat';
				});
			} else if (hasFormSearch && preservedProductCat) {
				// No form value, but formsearch=true exists, use preserved value
				param_ajax['product_cat'] = preservedProductCat.replace(/%2C/g, ',');
			} else if (isCategoryPage && categorySlug) {
				// Normal category page, use categorySlug from data attribute
				param_ajax['product_cat'] = categorySlug.replace(/%2C/g, ',');
			}

			// Add excluded_product_cat to ajax params if exists (from form or URL)
			var excludedProductCat = '';
			var excludedCatItem = dataArr.find(function (item) {
				return item.name === 'excluded_product_cat';
			});
			if (excludedCatItem && excludedCatItem.value) {
				excludedProductCat = excludedCatItem.value;
			} else if (urlParams.has('excluded_product_cat')) {
				excludedProductCat = urlParams.get('excluded_product_cat');
			}
			if (excludedProductCat) {
				param_ajax['excluded_product_cat'] = excludedProductCat.replace(/%2C/g, ',');
			}

			// Add taxonomy slug to ajax params if on taxonomy page (but not to URL)
			if (isTaxonomyPage && taxonomyType && taxonomySlug) {
				param_ajax[taxonomyType] = taxonomySlug.replace(/%2C/g, ',');
			}

			// Add layout parameters from URL if present (for demo/customize mode)
			const currentUrlParams = new URLSearchParams(window.location.search);
			if (currentUrlParams.has('layout-pagination')) {
				param_ajax['layout-pagination'] = currentUrlParams.get('layout-pagination');
			}

			// Get pagination type
			var paginationType = $('.bt-product-layout').data('pagination-type');
			param_in.forEach(function (param) {
				var param_key = param.split('=')[0],
					param_val = param.split('=')[1];

				// Skip current_page param for infinite scroll and load more button
				if ((paginationType === 'infinite-scrolling' || paginationType === 'button-load-more') && param_key === 'current_page') {
					return;
				}

				if ('' !== param_val) {
					var decodedVal = decodeURIComponent(param_val);
					urlParams.set(param_key, decodedVal);
					param_ajax[param_key] = decodedVal.replace(/%2C/g, ',');
				} else {
					// Remove empty params from URL
					urlParams.delete(param_key);
				}
			});

			// Restore product_cat and formsearch if formsearch=true was in original URL
			// Use form value if user clicked on filter, otherwise use preserved value
			if (hasFormSearch) {
				if (formProductCat) {
					// User clicked on filter, use form value
					urlParams.set('product_cat', formProductCat);
					// Ensure excluded_product_cat is removed when clicking product_cat filter
					urlParams.delete('excluded_product_cat');
				} else if (preservedProductCat) {
					// No form value, use preserved value
					urlParams.set('product_cat', preservedProductCat);
				}
				urlParams.set('formsearch', 'true');
			}

			var param_str = urlParams.toString();
			console.log(param_ajax);

			if ('' !== param_str) {
				window.history.replaceState(null, null, `?${param_str}`);
				$(this).find('.bt-reset-filter-product-btn').removeClass('disable');
			} else {
				window.history.replaceState(null, null, window.location.pathname);
				$(this).find('.bt-reset-filter-product-btn').addClass('disable');
			}
			SomniaLoadFilterTagProduct();
			// console.log(param_ajax);

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: AJ_Options.ajax_url,
				data: param_ajax,
				context: this,
				beforeSend: function () {
					document.querySelector('.bt-filter-scroll-pos').scrollIntoView({
						behavior: 'smooth'
					});

					// Show loading skeleton
					$('.bt-product-layout .woocommerce-loop-products').html(SomniaGenerateSkeletonHTML(12)).fadeIn('fast');
					$('.bt-product-pagination-wrap').fadeOut('fast');
				},
				success: function (response) {
					//console.log(response);
					if (response.success) {

						// Update category title and description
						var $title = $('.bt-shop-titlebar .bt-page-titlebar--title');
						var $description = $('.bt-shop-titlebar .bt-page-titlebar--description');

						if ($title.length > 0) {
							if (response.data['category_title']) {
								// Set category title
								// Decode HTML entities (e.g., &amp; to &)
								var decodedCategoryTitle = $('<textarea>').html(response.data['category_title']).text();
								$title.text(decodedCategoryTitle);
							} else {
								// Restore original title
								var originalTitle = $title.attr('data-original-title');
								if (originalTitle) {
									// Decode HTML entities (e.g., &amp; to &)
									var decodedTitle = $('<textarea>').html(originalTitle).text();
									$title.text(decodedTitle);
								}
							}
						}

						if ($description.length > 0) {
							// Check if filtering by category
							if (response.data['has_category_filter']) {
								// Filtering by category: use category description (may be empty)
								$description.html(response.data['category_description'] || '');
							} else {
								// Not filtering by category: restore original description
								var originalDescription = $description.attr('data-original-description');
								$description.html(originalDescription || '');
							}
						}

						setTimeout(function () {
							$('.bt-results-count').html(response.data['results']).fadeIn('slow');
							$('.bt-product-results-btn').html(response.data['button-results']).fadeIn('slow');
							$('.bt-product-layout .woocommerce-loop-products').html(response.data['items']).fadeIn('slow');
							$('.bt-product-pagination-wrap').html(response.data['pagination']).fadeIn('slow');

							// Update pagination type data attribute
							if (response.data.pagination_meta) {
								$('.bt-product-pagination-wrap').attr('data-pagination-type', response.data.pagination_meta.pagination_type);
							}

							// Hide topbar if not-found-products exists
							var hasNotFoundProducts = $('.not-found-products').length > 0;
							if (hasNotFoundProducts) {
								$('.bt-products-topbar').addClass('bt-hide');
							} else {
								$('.bt-products-topbar').removeClass('bt-hide');
							}

							SomniaProductButtonStatus();
							SomniaProductVariationHandler();
							SomniaLoadDefaultActiveVariations();
							SomniaCountdownProductSale();
							SomniaProductAttributeVariationSwitch();
							// Trigger event for infinite scroll to re-initialize
							$(document).trigger('filter-products-complete');
						}, 500);
					} else {
						console.log('error');
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('The following error occured: ' + textStatus, errorThrown);
				}
			});

			return false;
		});
	}
	/* Load More Button Handler */
	function SomniaLoadMoreButton() {
		if (!$('body').hasClass('post-type-archive-product')) {
			return;
		}

		$(document).on('click', '.bt-load-more-btn', function (e) {
			e.preventDefault();
			const $button = $(this);
			const nextPage = parseInt($button.data('page'));
			const totalPages = parseInt($button.data('total'));

			if ($button.hasClass('loading')) {
				return;
			}

			// Update current page in filter form
			$('.bt-product-filter-form .bt-product-current-page').val(nextPage);

			// Check if we're on a category page or taxonomy page
			var $sidebar = $('.bt-product-sidebar');
			var isCategoryPage = $sidebar.data('is-category-page') === 1;
			var isTaxonomyPage = $sidebar.data('is-taxonomy-page') === 1;
			var taxonomyType = $sidebar.data('taxonomy-type') || '';
			var taxonomySlug = $sidebar.data('taxonomy-slug') || '';

			// Get current URL params
			var urlParams = new URLSearchParams(window.location.search);

			// Check if formsearch=true exists in URL
			var hasFormSearch = urlParams.has('formsearch') && urlParams.get('formsearch') === 'true';

			// Store product_cat from URL if formsearch=true exists
			var preservedProductCat = '';
			if (hasFormSearch && urlParams.has('product_cat')) {
				preservedProductCat = urlParams.get('product_cat');
			}

			// Get form data
			var dataArr = $('.bt-product-filter-form').serializeArray();

			// Get product_cat from form
			var formProductCat = '';
			var productCatItem = dataArr.find(function (item) {
				return item.name === 'product_cat';
			});
			if (productCatItem && productCatItem.value) {
				formProductCat = productCatItem.value;
			} else {
				var productCatInput = $('.bt-product-filter-form').find('input[name="product_cat"]:checked');
				if (productCatInput.length > 0) {
					formProductCat = productCatInput.val();
				}
			}

			// Get category slug
			var categorySlug = '';
			if (isCategoryPage) {
				categorySlug = $sidebar.data('category-slug') || '';
				if (!categorySlug && formProductCat) {
					categorySlug = formProductCat;
				}
			}

			// Get taxonomy slug
			if (isTaxonomyPage && !taxonomySlug) {
				var taxonomyItem = dataArr.find(function (item) {
					return item.name === taxonomyType;
				});
				if (taxonomyItem && taxonomyItem.value) {
					taxonomySlug = taxonomyItem.value;
				}
			}

			// Remove product_cat from form data if on category page
			if (isCategoryPage) {
				dataArr = dataArr.filter(function (item) {
					return item.name !== 'product_cat';
				});
			}

			// Remove taxonomy from form data if on taxonomy page
			if (isTaxonomyPage && taxonomyType) {
				dataArr = dataArr.filter(function (item) {
					return item.name !== taxonomyType;
				});
			}

			// Get all form parameters
			var param_ajax = {
				action: 'somnia_products_filter',
			};

			// Add category slug to ajax params
			// Priority: form value > preserved value (when formsearch=true) > categorySlug
			if (formProductCat) {
				param_ajax['product_cat'] = formProductCat.replace(/%2C/g, ',');
			} else if (hasFormSearch && preservedProductCat) {
				param_ajax['product_cat'] = preservedProductCat.replace(/%2C/g, ',');
			} else if (isCategoryPage && categorySlug) {
				param_ajax['product_cat'] = categorySlug.replace(/%2C/g, ',');
			}

			// Add taxonomy slug to ajax params if on taxonomy page
			if (isTaxonomyPage && taxonomyType && taxonomySlug) {
				param_ajax[taxonomyType] = taxonomySlug.replace(/%2C/g, ',');
			}

			// Add excluded_product_cat to ajax params if exists
			var excludedProductCat = '';
			var excludedCatItem = dataArr.find(function (item) {
				return item.name === 'excluded_product_cat';
			});
			if (excludedCatItem && excludedCatItem.value) {
				excludedProductCat = excludedCatItem.value;
			} else if (urlParams.has('excluded_product_cat')) {
				excludedProductCat = urlParams.get('excluded_product_cat');
			}
			if (excludedProductCat) {
				param_ajax['excluded_product_cat'] = excludedProductCat.replace(/%2C/g, ',');
			}

			// Add other form params
			dataArr.forEach(function (item) {
				if (item.value) {
					param_ajax[item.name] = item.value.replace(/%2C/g, ',');
				}
			});

			// Add layout parameters from URL if present (for demo/customize mode)
			const currentUrlParams = new URLSearchParams(window.location.search);
			if (currentUrlParams.has('layout-pagination')) {
				param_ajax['layout-pagination'] = currentUrlParams.get('layout-pagination');
			}

			// AJAX call
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: AJ_Options.ajax_url,
				data: param_ajax,
				beforeSend: function () {
					$button.addClass('loading');
					$button.find('.bt-btn-text').hide();
					$button.find('.bt-btn-loading').show();

					// Show loading skeleton
					$('.bt-product-layout .woocommerce-loop-products').append(SomniaGenerateSkeletonHTML(12));
				},
				success: function (response) {
					if (response.success && response.data) {
						// Remove skeleton loading
						$('.bt-product-layout .woocommerce-loop-products .bt-product-skeleton').remove();

						// Append new products
						const $newProducts = $(response.data['items']);
						$('.bt-product-layout .woocommerce-loop-products').append($newProducts);

						// Update or remove load more button
						if (response.data.pagination_meta && response.data.pagination_meta.has_more) {
							$button.data('page', response.data.pagination_meta.current_page + 1);
							$button.removeClass('loading');
							$button.find('.bt-btn-text').show();
							$button.find('.bt-btn-loading').hide();
						} else {
							$('.bt-load-more-button-wrap').fadeOut('slow', function () {
								$(this).remove();
							});
						}

						// Re-initialize product functionality
						SomniaProductButtonStatus();
						SomniaProductVariationHandler();
						SomniaLoadDefaultActiveVariations();
						SomniaProductAttributeVariationSwitch();
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('Load more error: ' + textStatus, errorThrown);
					// Remove skeleton loading on error
					$('.bt-product-layout .woocommerce-loop-products .bt-product-skeleton').remove();
					$button.removeClass('loading');
					$button.find('.bt-btn-text').show();
					$button.find('.bt-btn-loading').hide();
				}
			});
		});
	}

	/* Infinite Scroll Handler */
	function SomniaInfiniteScroll() {
		if (!$('body').hasClass('post-type-archive-product')) {
			return;
		}

		let isLoading = false;
		let observer = null;

		// Create intersection observer
		function initInfiniteScroll() {
			const trigger = document.querySelector('.bt-infinite-scroll-trigger');

			if (!trigger) {
				return;
			}

			if (observer) {
				observer.disconnect();
			}

			observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting && !isLoading) {
						loadMoreProducts();
					}
				});
			}, {
				rootMargin: '200px'
			});

			observer.observe(trigger);
		}

		function loadMoreProducts() {
			const $trigger = $('.bt-infinite-scroll-trigger');

			if (!$trigger.length || isLoading) {
				return;
			}

			const nextPage = parseInt($trigger.data('page'));
			const totalPages = parseInt($trigger.data('total'));

			if (nextPage > totalPages) {
				return;
			}

			isLoading = true;

			// Update current page in filter form
			$('.bt-product-filter-form .bt-product-current-page').val(nextPage);

			// Check if we're on a category page or taxonomy page
			var $sidebar = $('.bt-product-sidebar');
			var isCategoryPage = $sidebar.data('is-category-page') === 1;
			var isTaxonomyPage = $sidebar.data('is-taxonomy-page') === 1;
			var taxonomyType = $sidebar.data('taxonomy-type') || '';
			var taxonomySlug = $sidebar.data('taxonomy-slug') || '';

			// Get current URL params
			var urlParams = new URLSearchParams(window.location.search);

			// Check if formsearch=true exists in URL
			var hasFormSearch = urlParams.has('formsearch') && urlParams.get('formsearch') === 'true';

			// Store product_cat from URL if formsearch=true exists
			var preservedProductCat = '';
			if (hasFormSearch && urlParams.has('product_cat')) {
				preservedProductCat = urlParams.get('product_cat');
			}

			// Get form data
			var dataArr = $('.bt-product-filter-form').serializeArray();

			// Get product_cat from form
			var formProductCat = '';
			var productCatItem = dataArr.find(function (item) {
				return item.name === 'product_cat';
			});
			if (productCatItem && productCatItem.value) {
				formProductCat = productCatItem.value;
			} else {
				var productCatInput = $('.bt-product-filter-form').find('input[name="product_cat"]:checked');
				if (productCatInput.length > 0) {
					formProductCat = productCatInput.val();
				}
			}

			// Get category slug
			var categorySlug = '';
			if (isCategoryPage) {
				categorySlug = $sidebar.data('category-slug') || '';
				if (!categorySlug && formProductCat) {
					categorySlug = formProductCat;
				}
			}

			// Get taxonomy slug
			if (isTaxonomyPage && !taxonomySlug) {
				var taxonomyItem = dataArr.find(function (item) {
					return item.name === taxonomyType;
				});
				if (taxonomyItem && taxonomyItem.value) {
					taxonomySlug = taxonomyItem.value;
				}
			}

			// Remove product_cat from form data if on category page
			if (isCategoryPage) {
				dataArr = dataArr.filter(function (item) {
					return item.name !== 'product_cat';
				});
			}

			// Remove taxonomy from form data if on taxonomy page
			if (isTaxonomyPage && taxonomyType) {
				dataArr = dataArr.filter(function (item) {
					return item.name !== taxonomyType;
				});
			}

			// Get all form parameters
			var param_ajax = {
				action: 'somnia_products_filter',
			};

			// Add category slug to ajax params
			// Priority: form value > preserved value (when formsearch=true) > categorySlug
			if (formProductCat) {
				param_ajax['product_cat'] = formProductCat.replace(/%2C/g, ',');
			} else if (hasFormSearch && preservedProductCat) {
				param_ajax['product_cat'] = preservedProductCat.replace(/%2C/g, ',');
			} else if (isCategoryPage && categorySlug) {
				param_ajax['product_cat'] = categorySlug.replace(/%2C/g, ',');
			}

			// Add taxonomy slug to ajax params if on taxonomy page
			if (isTaxonomyPage && taxonomyType && taxonomySlug) {
				param_ajax[taxonomyType] = taxonomySlug.replace(/%2C/g, ',');
			}

			// Add excluded_product_cat to ajax params if exists
			var excludedProductCat = '';
			var excludedCatItem = dataArr.find(function (item) {
				return item.name === 'excluded_product_cat';
			});
			if (excludedCatItem && excludedCatItem.value) {
				excludedProductCat = excludedCatItem.value;
			} else if (urlParams.has('excluded_product_cat')) {
				excludedProductCat = urlParams.get('excluded_product_cat');
			}
			if (excludedProductCat) {
				param_ajax['excluded_product_cat'] = excludedProductCat.replace(/%2C/g, ',');
			}

			// Add other form params
			dataArr.forEach(function (item) {
				if (item.value) {
					param_ajax[item.name] = item.value.replace(/%2C/g, ',');
				}
			});

			// Add layout parameters from URL if present (for demo/customize mode)
			const currentUrlParams = new URLSearchParams(window.location.search);
			if (currentUrlParams.has('layout-pagination')) {
				param_ajax['layout-pagination'] = currentUrlParams.get('layout-pagination');
			}

			// AJAX call
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: AJ_Options.ajax_url,
				data: param_ajax,
				beforeSend: function () {
					// Show loading indicator
					$trigger.find('.bt-loading-spinner').fadeIn();
					// Show loading skeleton
					$('.bt-product-layout .woocommerce-loop-products').append(SomniaGenerateSkeletonHTML(12));
				},
				success: function (response) {
					if (response.success && response.data) {
						// Remove skeleton loading
						$('.bt-product-layout .woocommerce-loop-products .bt-product-skeleton').remove();

						// Append new products
						const $newProducts = $(response.data['items']);
						$('.bt-product-layout .woocommerce-loop-products').append($newProducts);

						// Update or remove trigger
						if (response.data.pagination_meta && response.data.pagination_meta.has_more) {
							$trigger.data('page', response.data.pagination_meta.current_page + 1);
							$trigger.find('.bt-loading-spinner').fadeOut();
							isLoading = false;

							// Re-observe for next load
							setTimeout(function () {
								initInfiniteScroll();
							}, 100);
						} else {
							// No more products
							$trigger.fadeOut('slow', function () {
								$(this).remove();
							});
							if (observer) {
								observer.disconnect();
							}
						}

						// Re-initialize product functionality
						SomniaProductButtonStatus();
						SomniaProductVariationHandler();
						SomniaLoadDefaultActiveVariations();
						SomniaProductAttributeVariationSwitch();
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('Infinite scroll error: ' + textStatus, errorThrown);
					// Remove skeleton loading on error
					$('.bt-product-layout .woocommerce-loop-products .bt-product-skeleton').remove();
					$trigger.find('.bt-loading-spinner').fadeOut();
					isLoading = false;
				}
			});
		}

		// Initialize on page load
		initInfiniteScroll();

		// Re-initialize after filter changes (modify existing filter submit to handle this)
		$(document).on('filter-products-complete', function () {
			isLoading = false;
			setTimeout(function () {
				initInfiniteScroll();
			}, 100);
		});
	}

	/* Product Button toggle Filter*/
	function SomniaProductFilterToggle() {
		if ($('.bt-product-filter-toggle').length > 0) {
			$('.bt-product-filter-toggle').on('click', function () {
				const $mainContent = $(this).parents('.bt-main-content');
				$mainContent.find('.bt-products-sidebar').addClass('active');
				$mainContent.find('.bt-products-dropdown').toggleClass('active');
				$mainContent.find('.bt-template-nosidebar-dropdown').addClass('active');
			});
			$('.bt-popup-overlay, .bt-form-button .bt-close-btn, .bt-form-button-results .bt-product-results-btn').on('click', function (e) {
				e.preventDefault();
				const $mainContent = $(this).parents('.bt-main-content');
				$mainContent.find('.bt-products-sidebar, .bt-products-dropdown, .bt-template-nosidebar-dropdown').removeClass('active');
			});
		}
		if ($('.bt-elwg-filter-toggle').length > 0) {
			$(document).on('click', '.bt-elwg-filter-toggle', function () {

				// Check if bt-product-filter-toggle exists on the page
				if ($('.bt-product-filter-toggle').length > 0) {
					const $mainContent = $('.bt-product-filter-toggle').closest('.bt-main-content');
					$mainContent.find('.bt-products-sidebar').addClass('active');
					$mainContent.find('.bt-products-dropdown').toggleClass('active');
					$mainContent.find('.bt-template-nosidebar-dropdown').addClass('active');

				}
			});
		}
	}
	function SomniaAttachTooltip(targetSelector, tooltipText) {
		var timeout;
		var mediaQuery = window.matchMedia('(min-width: 767px)');

		function hideTooltip(element) {
			timeout = setTimeout(function () {
				element.find('.tooltip').fadeOut(200, function () {
					$(this).remove();
				});
			}, 200);
		}

		$(document).on('mouseenter', targetSelector, function () {
			// Only show tooltip on screens 767px and above
			if (!mediaQuery.matches) {
				return;
			}

			clearTimeout(timeout);
			if (!$(this).find('.tooltip').length) {
				var tooltip = $('<span class="tooltip"></span>').text(tooltipText);
				$(this).append(tooltip);
				tooltip.fadeIn(200);
			}
		});

		$(document).on('mouseleave', targetSelector, function () {
			hideTooltip($(this));
		});

		// Automatically remove tooltips when resizing to mobile
		mediaQuery.addListener(function (e) {
			if (!e.matches) {
				$('.tooltip').fadeOut(200, function () {
					$(this).remove();
				});
			}
		});
	}
	function SomniaAttachTooltips() {
		SomniaAttachTooltip('.bt-product-wishlist-btn.no-added', 'Add to Wishlist');
		SomniaAttachTooltip('.bt-product-wishlist-btn.added', 'Remove Wishlist');
		SomniaAttachTooltip('.bt-product-compare-btn.no-added', 'Add to Compare');
		SomniaAttachTooltip('.bt-product-compare-btn.added', 'View Compare');
		SomniaAttachTooltip('.bt-product-quick-view-btn', 'Quick View');
		SomniaAttachTooltip('.bt-product-icon-btn .bt-loop-add-to-cart-btn', 'Choose options');
		SomniaAttachTooltip('.bt-product-icon-btn .product_type_simple', 'Add to Cart');
		SomniaAttachTooltip('.bt-product-icon-btn .wc-forward', 'View Cart');
	}
	function SomniaUpdateMiniCart() {
		var timeout;
		$(document.body).on('change input', 'input.qty', function () {
			if (timeout !== undefined) clearTimeout(timeout);
			timeout = setTimeout(function () {
				$('[name=update_cart]').trigger('click');
			}, 500);
		});
		if (typeof wc_cart_fragments_params !== 'undefined') {
			$('form.woocommerce-cart-form').on('submit', function (event) {
				event.preventDefault();
				var $form = $(this);
				$.ajax({
					url: $form.attr('action'),
					type: 'POST',
					data: $form.serialize(),
					success: function () {
						$.ajax({
							url: wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_fragments'),
							type: 'POST',
							success: function (response) {
								if (response && response.fragments) {
									$.each(response.fragments, function (key, value) {
										$(key).replaceWith(value);
									});
								}
							},
							error: function () {
								console.error('Failed to update mini cart.');
							}
						});
					},
					error: function () {
						console.error('Failed to submit the cart form.');
					}
				});
			});
		}
	}
	function SomniaProgressCart() {
		if ($('.bt-progress-container-cart').length > 0) {
			let targetWidth = $(".bt-progress-bar").data("width");
			let currentWidth = 0;
			var interval = setInterval(function () {
				if (currentWidth >= targetWidth) {
					clearInterval(interval);
				} else {
					currentWidth++;
					$(".bt-progress-bar").css("width", currentWidth + "%");
				}
			}, 30);
		}
	}
	function SomniaFreeShippingMessage() {
		$.ajax({
			url: AJ_Options.ajax_url,
			type: 'POST',
			data: {
				action: 'somnia_get_free_shipping',
			},
			success: function (response) {
				if (response.success) {
					$(".bt-progress-bar").css("width", response.data['percentage'] + "%");
					$('#bt-free-shipping-message').html(response.data['message']);
				}
			},
		});
	}
	function SomniaCountdownCart() {
		if ($('.bt-time-promotion').length > 0) {
			var countdownElement = $('#countdown');
			var originalTime = countdownElement.data('time');
			var time = originalTime.split(':');
			var minutes = parseInt(time[0], 10);
			var seconds = parseInt(time[1], 10);
			var interval = setInterval(function () {
				if (seconds === 0 && minutes === 0) {
					countdownElement.text("00:00");
					countdownElement.data('time', originalTime);
					time = originalTime.split(':');
					minutes = parseInt(time[0], 10);
					seconds = parseInt(time[1], 10);
				} else {
					if (seconds === 0) {
						minutes--;
						seconds = 59;
					} else {
						seconds--;
					}
					countdownElement.text(formatTime(minutes, seconds));
					countdownElement.data('time', formatTime(minutes, seconds));
				}
			}, 1000);

			function formatTime(minutes, seconds) {
				return (minutes < 10 ? '0' + minutes : minutes) + ':' + (seconds < 10 ? '0' + seconds : seconds);
			}
		}
	}
	function SomniaBuyNow() {
		$(document).on('click', '.bt-button-buy-now a', function (e) {
			e.preventDefault();

			if ($(this).hasClass('disabled')) {
				return;
			}

			var product_id_grouped = $(this).data('grouped');
			if (product_id_grouped) {
				product_id_grouped = product_id_grouped.toString();
				var param_ajax = {
					action: 'somnia_products_buy_now',
					product_id_grouped: product_id_grouped
				};
			} else {
				var product_id = $(this).data('id').toString();
				var variation_id = $(this).data('variation');
				var quantity = $('input[name="quantity"]').val();
				var param_ajax = {
					action: 'somnia_products_buy_now',
					product_id: product_id,
					quantity: quantity
				};

				// Add variation_id to AJAX data if it exists
				if (variation_id) {
					param_ajax.variation_id = variation_id;
				}
			}
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: AJ_Options.ajax_url,
				data: param_ajax,
				beforeSend: function () {

				},
				success: function (response) {
					if (response.success) {
						window.location.href = response.data['redirect_url'];
					} else {
						console.log('error');
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('The following error occured: ' + textStatus, errorThrown);
				}
			});
		});
	}
	function SomniaReviewPopup() {
		$(document).on('click', '.bt-action-review', function (e) {
			e.preventDefault();
			$('.bt-form-review-popup').addClass('active');
		});

		$(document).on('click', '.bt-form-review-popup .bt-review-overlay, .bt-form-review-popup .bt-review-close', function () {
			$('.bt-form-review-popup').removeClass('active');
		});

		$(document).on('keydown', function (e) {
			if (e.key === 'Escape') {
				$('.bt-form-review-popup').removeClass('active');
			}
		});
	}
	function SomniaAddSelect2GravityForm() {
		$('.gform_wrapper').each(function () {
			const $self = $(this);
			$($self).find('select').each(function () {
				const placeholder = $(this).find('option.gf_placeholder').text();
				const select2Options = {
					dropdownParent: $($self),
					minimumResultsForSearch: Infinity,
				};
				if (placeholder) {
					select2Options.placeholder = placeholder;
				}
				$(this).select2(select2Options);
			});
		});
	}
	function SomniaHookGravityFormEvents() {
		$(document).on('submit', '.gform_wrapper form', function (e) {
			let $form = $(this);
			let $submitButton = $form.find('input[type="submit"], button[type="submit"]');
			$form.addClass('loading');
			$submitButton.prop('disabled', true).addClass('loading');
		});
		$(document).on('gform_post_render', function (event, formId) {
			SomniaAddSelect2GravityForm();
		});
	}
	function SomniaProductButtonStatus() {
		var productCompare = localStorage.getItem('productcomparelocal');
		var productCompareArray = productCompare ? productCompare.split(',') : [];
		var productWishlist = localStorage.getItem('productwishlistlocal');
		var productWishlistArray = productWishlist ? productWishlist.split(',') : [];
		var wishlist_count = productWishlist ? productWishlist.split(',').length : 0;
		$('.bt-mini-wishlist .wishlist_total').html(wishlist_count);
		$('.bt-product-compare-btn').each(function () {
			var productId = $(this).data('id');
			if (productCompareArray.includes(productId.toString())) {
				$(this).addClass('added').removeClass('no-added');
			} else {
				$(this).addClass('no-added').removeClass('added');
			}
		});
		$('.bt-product-wishlist-btn').each(function () {
			var productId = $(this).data('id');
			if (productWishlistArray.includes(productId.toString())) {
				$(this).addClass('added').removeClass('no-added');
			} else {
				$(this).addClass('no-added').removeClass('added');
			}
		});
	}
	/* Copyright Current Year */
	function SomniaCopyrightCurrentYear() {
		var searchTerm = '{Year}',
			replaceWith = new Date().getFullYear();

		$('.bt-elwg-site-copyright').each(function () {
			this.innerHTML = this.innerHTML.replace(searchTerm, replaceWith);
		});
	}
	/* share button wishlist page and compare page */
	function SomniaShareLocalStorage(datashare = []) {
		if (!datashare) {
			const url = new URL(window.location.href);
			url.searchParams.delete('datashare');
			window.history.pushState(null, '', url.toString());
			return;
		}
		const url = new URL(window.location.href);
		url.searchParams.set('datashare', datashare);
		window.history.pushState(null, '', url.toString());
		$('.bt-post-share a').each(function () {
			var currentHref = $(this).attr('href');
			// Handle both Facebook and Pinterest share links
			if (currentHref.includes('facebook.com/sharer')) {
				var newHref = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href);
			} else if (currentHref.includes('pinterest.com/pin')) {
				var newHref = 'https://pinterest.com/pin/create/button/?url=' + encodeURIComponent(window.location.href);
			} else {
				var newHref = currentHref.replace(/url=[^&]+/, 'url=' + encodeURIComponent(window.location.href));
			}
			$(this).attr('href', newHref);
		});
	}
	/* backtotop */
	function SomniaBackToTop() {
		const $backToTop = $('.bt-back-to-top');
		if ($backToTop.length > 0) {
			$(window).on('scroll', function () {
				if ($(this).scrollTop() > 300) {
					$backToTop.addClass('show');
				} else {
					$backToTop.removeClass('show');
				}
			});

			$backToTop.on('click', function (e) {
				e.preventDefault();
				$('html, body').animate({ scrollTop: 0 }, 500);
			});
		}
	}
	/* addToRecentlyViewed */
	function SomniaAddToRecentlyViewed(productId) {
		// Check if not single product page, return early
		if (!$('body').hasClass('single-product')) {
			return;
		}
		const MAX_PRODUCTS = 8;
		// Get product ID from URL
		const bodyClasses = document.body.className;
		const match = bodyClasses.match(/postid-(\d+)/);

		if (match && match[1]) {
			var productId = parseInt(match[1]);
		}
		if (productId) {
			var recentlyViewed = localStorage.getItem('recentlyViewed') || '[]';
			recentlyViewed = JSON.parse(recentlyViewed);
			// Remove the product if it's already in the list
			recentlyViewed = recentlyViewed.filter(id => id !== productId);
			// Add the product to the beginning of the list
			recentlyViewed.unshift(productId);
			// Limit the list to the last 5 products
			if (recentlyViewed.length > MAX_PRODUCTS) {
				recentlyViewed = recentlyViewed.slice(0, MAX_PRODUCTS);
			}
			localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));
		}
	}
	/* loadRecentlyViewedProducts */
	function SomniaLoadRecentlyViewedProducts() {
		const recentlyViewed = localStorage.getItem('recentlyViewed') || '[]';
		const parsedRecentlyViewed = JSON.parse(recentlyViewed);
		$('.bt-related-tab-heading .bt-tab-title').addClass('bt-heading-related');
		if (parsedRecentlyViewed.length > 1) {
			$('.bt-related-tab-heading .bt-tab-title').removeClass('bt-heading-related');
			$('.bt-related-tab-heading .bt-tab-title.recently-viewed').show();

			$.ajax({
				url: AJ_Options.ajax_url,
				type: 'POST',
				data: {
					action: 'load_recently_viewed',
					recently_viewed: parsedRecentlyViewed
				},
				success: function (response) {
					if (response.success) {
						$('.recently-viewed-products').html(response.data);
						SomniaProductAttributeVariationSwitch();
						SomniaLoadDefaultActiveVariations();
					}
				}
			})
			const $tabTitles = $('.bt-tab-title');
			const $tabPanes = $('.bt-tab-pane');

			// Tab switching functionality
			$tabTitles.on('click', function () {
				const $this = $(this);
				const tabId = $this.data('tab');

				// Update active states
				$tabTitles.removeClass('active');
				$this.addClass('active');

				$tabPanes.removeClass('active');
				$(`[data-tab-content="${tabId}"]`).addClass('active');
			});

		}
	}
	/* Helper function to sync countdown values to cloned slides */
	function syncCountdownToClones(productId, values) {
		const $clonedCountdowns = $(`.swiper-slide-duplicate .bt-countdown-product-js[data-idproduct="${productId}"]`);

		if ($clonedCountdowns.length === 0) return;

		if (typeof values === 'string') {
			$clonedCountdowns.html(values);
		} else {
			$clonedCountdowns.each(function () {
				$(this).find('.bt-countdown-days').text(values.days);
				$(this).find('.bt-countdown-hours').text(values.hours);
				$(this).find('.bt-countdown-mins').text(values.mins);
				$(this).find('.bt-countdown-secs').text(values.secs);
			});
		}
	}

	/* Countdown product sale */
	function SomniaCountdownProductSale($container) {
		const $searchContext = $container || $(document);

		// Find countdown timers, exclude slider clones
		const $countdowns = $searchContext.find('.bt-countdown-product-js')
			.not('.bt-countdown-initialized')
			.filter(function () {
				return !$(this).closest('.swiper-slide-duplicate').length;
			});

		if ($countdowns.length === 0) return;

		$countdowns.each(function () {
			const $countdown = $(this);
			const countDownDate = new Date($countdown.data('time')).getTime();
			const productId = $countdown.data('idproduct');
			const serverCurrentTime = $countdown.data('current-time');

			$countdown.addClass('bt-countdown-initialized');

			if (isNaN(countDownDate)) {
				console.error('Invalid countdown date for product:', productId);
				return;
			}

			// Use server current time as baseline and track elapsed time
			const serverInitTime = serverCurrentTime ? new Date(serverCurrentTime).getTime() : new Date().getTime();
			const clientInitTime = Date.now();

			// Create individual timer for each countdown
			const timer = setInterval(() => {
				// Calculate current server time: initial server time + elapsed time since initialization
				const elapsed = Date.now() - clientInitTime;
				const now = serverInitTime + elapsed;

				const distance = countDownDate - now;

				if (distance < 0) {
					clearInterval(timer);
					$countdown.html('<div class="bt-countdown-expired">EXPIRED</div>');
					syncCountdownToClones(productId, '<div class="bt-countdown-expired">EXPIRED</div>');

					if ($('body').hasClass('single-product')) {
						$countdown.closest('.bt-countdown-product-sale').fadeOut(300);
						$('.bt-product-percentage-sold').fadeOut(300);
					} else {
						$countdown.closest('.bt-product-countdown-timer').fadeOut(300);
						$(`.swiper-slide-duplicate .bt-countdown-product-js[data-idproduct="${productId}"]`)
							.closest('.bt-product-countdown-timer').fadeOut(300);
					}
					return;
				}

				const days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
				const hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
				const mins = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
				const secs = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');

				$countdown.find('.bt-countdown-days').text(days);
				$countdown.find('.bt-countdown-hours').text(hours);
				$countdown.find('.bt-countdown-mins').text(mins);
				$countdown.find('.bt-countdown-secs').text(secs);

				// Sync to cloned slides
				syncCountdownToClones(productId, { days, hours, mins, secs });
			}, 1000);
		});

		// Progress bar countdown product sale (only for single product)
		if ($('body').hasClass('single-product') && $('.bt-progress-bar-sold').length > 0) {
			const $progressBar = $('.bt-progress-bar-sold');
			if (!$progressBar.hasClass('bt-progress-initialized')) {
				$progressBar.addClass('bt-progress-initialized');
				let progressWidth = $progressBar.data("width");
				let currentWidth = 0;
				var interval = setInterval(function () {
					if (currentWidth >= progressWidth) {
						clearInterval(interval);
					} else {
						currentWidth++;
						$progressBar.css("width", currentWidth + "%");
					}
				}, 30);
			}
		}
	}

	// Expose function to global scope for use in other scripts
	window.SomniaCountdownProductSale = SomniaCountdownProductSale;

	/**
	 * Product Info Display - Toggle Handler
	 * Handle toggle/accordion for product information
	 */
	function SomniaCustomizeProductToggle() {
		if ($('.bt-product-toggle-js').length === 0) {
			return;
		}

		$('.bt-product-toggle-js .bt-item-title').on('click', function (e) {
			e.preventDefault();

			var $clickedTitle = $(this);
			var $clickedContent = $clickedTitle.parent().find('.bt-item-content');
			var $toggleContainer = $clickedTitle.closest('.bt-product-toggle-js');
			var toggleState = $toggleContainer.data('toggle-state');

			if ($clickedTitle.hasClass('active')) {
				// Close clicked toggle
				$clickedContent.slideUp(300);
				$clickedTitle.removeClass('active');
			} else {
				// If toggle-state is 'first', close all others
				// If toggle-state is 'all', keep others open
				if (toggleState === 'first') {
					$toggleContainer.find('.bt-item-content').slideUp(300);
					$toggleContainer.find('.bt-item-title').removeClass('active');
				}

				// Open clicked toggle
				$clickedContent.slideDown(300, function () {
					// Scroll to the toggle container after opening
					$('html, body').animate({
						scrollTop: $toggleContainer.offset().top - 100
					}, 500);
				});
				$clickedTitle.addClass('active');
			}
		});
	}
	// checkbox customize grouped product
	function SomniaCustomizeGroupedProduct() {
		// reset input quantity value
		$('.bt-product-grouped-js .quantity input[type="number"]').val(0);
		// check button add to cart 
		const $addToCartBtn = $('.grouped_form .single_add_to_cart_button');
		$addToCartBtn.addClass('disabled');

		$(document).on('click', '.grouped_form .single_add_to_cart_button.disabled', function (e) {
			e.preventDefault();
			return;
		});
		// Check if the checkbox is checked
		$(document).on('change', '.bt-product-grouped-js input[type="checkbox"]', function () {
			var parent = $(this).parents('.woocommerce-grouped-product-list-item');
			var quantityInput = parent.find('.quantity input');
			var currentQuantity = parseInt(quantityInput.val()) || 0;
			if ($(this).is(':checked')) {
				if (currentQuantity === 0) {
					quantityInput.val(1).trigger('change');
				}
			} else {
				quantityInput.val(0).trigger('change');
			}
		});
		$(document).on('change', '.bt-product-grouped-js .quantity input', function () {
			var parent = $(this).parents('.woocommerce-grouped-product-list-item');
			var checkbox = parent.find('input[type="checkbox"]');
			var quantity = parseInt($(this).val()) || 0;

			if (quantity > 0) {
				checkbox.prop('checked', true);
			} else {
				checkbox.prop('checked', false);
			}
			// Get all checked checkboxes and store their values
			// Get checked products and their quantities
			var productGrouped = [];
			let totalPrice = 0;
			let regularTotalPrice = 0;
			let currencySymbol = parent.data('currency') || '$';
			let thousandSeparator = parent.data('thousand-separator') || ',';
			let decimalSeparator = parent.data('decimal-separator') || '.';
			$('.bt-product-grouped-js input[type="checkbox"]:checked').each(function () {
				const $checkbox = $(this);
				const $item = $checkbox.closest('.woocommerce-grouped-product-list-item');
				const $quantity = $item.find('.quantity input');

				// Add product to grouped array
				productGrouped.push($checkbox.val() + ':' + $quantity.val());

				// Calculate total price of checked products
				currencySymbol = $item.data('currency') || '$';
				const $priceElement = $item.find('.woocommerce-Price-amount');
				let price;

				// Get regular price
				const regularPriceText = $priceElement.first().text();
				const regularPrice = parseFloat(
					regularPriceText
						.replace(new RegExp('[^0-9' + thousandSeparator + decimalSeparator + ']+', 'g'), '') // Remove all non-numeric except separators
						.replace(new RegExp('\\' + thousandSeparator, 'g'), '') // Remove thousand separator
						.replace(new RegExp('\\' + decimalSeparator, 'g'), '.') // Replace decimal separator with dot for parseFloat
				);

				// Get sale price if exists
				if ($item.find('ins').length) {
					const salePriceText = $item.find('ins .woocommerce-Price-amount').text();
					price = parseFloat(
						salePriceText
							.replace(new RegExp('[^0-9' + thousandSeparator + decimalSeparator + ']+', 'g'), '') // Remove all non-numeric except separators
							.replace(new RegExp('\\' + thousandSeparator, 'g'), '') // Remove thousand separator
							.replace(new RegExp('\\' + decimalSeparator, 'g'), '.') // Replace decimal separator with dot for parseFloat
					);
				} else {
					price = regularPrice;
				}

				const quantity = parseInt($quantity.val()) || 0;
				totalPrice += price * quantity;
				regularTotalPrice += regularPrice * quantity;
			});

			// Helper function to format price with proper separators
			function formatPrice(price) {
				const parts = price.toFixed(2).split('.');
				// Format integer part with thousand separator
				parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
				// Join with decimal separator
				return parts.join(decimalSeparator);
			}

			// Update price display
			if (totalPrice < regularTotalPrice) {
				$('.bt-price').html(`<del>${currencySymbol}${formatPrice(regularTotalPrice)}</del> ${currencySymbol}${formatPrice(totalPrice)}`);
			} else {
				$('.bt-price').text(currencySymbol + formatPrice(totalPrice));
			}
			// Update buy now button state

			const $buyNowBtn = $('.bt-button-buy-now a');

			if (productGrouped.length) {
				$buyNowBtn
					.attr('data-grouped', productGrouped.join(','))
					.removeClass('disabled');
				$addToCartBtn.removeClass('disabled');
				$('.bt-quickview-product .grouped_form .single_add_to_cart_button').removeClass('disabled');
				$('.bt-total-price ').addClass('active');
			} else {
				$buyNowBtn.addClass('disabled');
				$addToCartBtn.addClass('disabled');
				$('.bt-quickview-product .grouped_form .single_add_to_cart_button').addClass('disabled');
				$('.bt-total-price ').removeClass('active');

			}
		});
	}

	/* popup newsletter */
	function SomniaPopupNewsletter() {
		// Check if newsletter popup exists
		if ($('#bt-newsletter-popup').length > 0) {
			if ($('.bt-thankyou-newsletter').length) {
				// Check if URL has confirmed parameter
				const urlParams = new URLSearchParams(window.location.search);
				if (urlParams.get('nm') === 'confirmed') {
					localStorage.setItem('newsletterpopup', 'true');
				}
				return;
			}
			const newsletter_local = window.localStorage.getItem('newsletterpopup');

			if (!newsletter_local) {
				const popup = $('#bt-newsletter-popup');
				let hasShown = false;

				// Show popup when scrolling down 300px
				$(window).scroll(function () {
					if (!hasShown && $(this).scrollTop() > 300) {
						hasShown = true;
						const scrollbarWidth = window.innerWidth - $(window).width();

						popup.fadeIn();
						$('body').css({
							'overflow': 'hidden',
							'padding-right': scrollbarWidth + 'px' // Prevent layout shift
						});
					}
				});

				// Helper function to close popup
				const closePopup = () => {
					popup.fadeOut();
					localStorage.setItem('newsletterpopup', 'true');
					$('body').css({
						'overflow': 'auto',
						'padding-right': '0'
					});
				};

				// Close popup events
				$(document).on('click', '#bt-newsletter-popup .bt-close-popup', closePopup);
				$(document).on('click', '#bt-newsletter-popup .bt-newsletter-overlay', closePopup);
				$(document).on('keydown', function (e) {
					if (e.key === 'Escape') {
						closePopup();
					}
				});
			}
		}
	}
	function SomniaProductAttributeVariationSwitch() {
		if ($('.bt-product-add-to-cart-variable').length > 0) {
			// Handle both color and image attribute clicks to switch product images
			$(document).on('click', '.bt-product-add-to-cart-variable .bt-value-color .bt-item-color, .bt-product-add-to-cart-variable .bt-value-image .bt-item-image', function (e) {
				var attributeValue = $(this).data('value');

				const productContainer = $(this).closest('.woocommerce-loop-product');
				const attributeVariationsContainer = $(this).closest('.bt-product-add-to-cart-variable');
				const attributeVariations = attributeVariationsContainer.data('attribute-variations');

				// Check if attribute variations exists and is an object
				if (attributeVariations && typeof attributeVariations === 'object') {
					// Find the matching attribute variation
					const matchingAttribute = Object.keys(attributeVariations).find(slug => {
						return slug === attributeValue;
					});

					if (matchingAttribute && attributeVariations[matchingAttribute]) {
						const attributeData = attributeVariations[matchingAttribute];
						productContainer.find('.bt-product-images-wrapper').html(attributeData.variable_image_html);
					} else {
						console.log('No matching attribute found for:', attributeValue);
					}
				} else {
					console.log('Attribute variations data not found or invalid');
				}
			});
		}
	}
	/* Get width body */
	function SomniaUpdateBodyWidthVariable() {
		var widthBody = $(window).width();
		if ($('.bt-col-container-left').length) {
			$('.bt-col-container-left').css('--width-body', widthBody + 'px');
		}
		if ($('.bt-col-container-right').length) {
			$('.bt-col-container-right').css('--width-body', widthBody + 'px');
		}
		if ($('.bt-product-extra-content').length) {
			$('.bt-product-extra-content').css('--width-body', widthBody + 'px');
		}
		if ($('.bt-carousel-full-width').length) {
			$('.bt-carousel-full-width .elementor-loop-container').css('--width-body', widthBody + 'px');
		}
	}
	/* add to cart ajax product variable */
	function SomniaAddToCartVariable() {
		$('.woocommerce-product-sale-label').each(function () {
			if ($(this).html().trim() === '') {
				$(this).addClass('hidden').hide();
			} else {
				$(this).removeClass('hidden').show();
			}
		});

		$(document).on('click', '.bt-js-add-to-cart-variable', function (e) {
			e.preventDefault();

			var $button = $(this);
			var $form = $button.closest('.variations_form');

			if ($button.hasClass('disabled')) {
				return;
			}

			$button.addClass('loading');

			// Get the latest values from the variations form
			var product_id = $button.data('product-id').toString();
			var variation_id = $form.find('input.variation_id').val();
			var quantity = $form.find('.quantity .qty').val() || 1;

			var param_ajax = {
				action: 'somnia_products_add_to_cart_variable',
				product_id: product_id,
				quantity: quantity
			};

			// Add variation_id to AJAX data if it exists
			if (variation_id) {
				param_ajax.variation_id = variation_id;
			}
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: AJ_Options.ajax_url,
				data: param_ajax,
				beforeSend: function () {

				},
				success: function (response) {
					if (response.success) {
						$('.bt-js-add-to-cart-variable').removeClass('loading');
						var productId = variation_id || product_id;
				
					// Update mini cart after successful add to cart
					$.ajax({
						url: wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_fragments'),
						type: 'POST',
						success: function (response) {
							if (response && response.fragments) {
								$.each(response.fragments, function (key, value) {
									$(key).replaceWith(value);
								});
								const cartCount = parseInt($('.bt-mini-cart .cart_total').text());
								if (cartCount === 0) {
									$(".bt-mini-cart-sidebar .bt-progress-content").addClass("bt-hide");
								} else {
									$(".bt-mini-cart-sidebar .bt-progress-content").removeClass("bt-hide");
								}
								// Trigger fragments refreshed event to update note button class
								$(document.body).trigger('wc_fragments_refreshed');
								
								// Open mini cart after fragments are loaded
								if (productId) {
									SomniaHandleCartAction(productId);
								}
								// Free shipping message
								SomniaFreeShippingMessage();
							}
						},
						error: function () {
							console.error('Failed to update mini cart.');
						}
					});
					} else {
						console.log('error');
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('The following error occured: ' + textStatus, errorThrown);
				}
			});
		});
	}
	/* add to cart ajax product simple */
	function SomniaAddToCartSimple() {
		$(document).on('click', '.bt-js-add-to-cart-simple', function (e) {
			e.preventDefault();

			var $button = $(this);
			var $form = $button.closest('form.cart');

			if ($button.hasClass('disabled')) {
				return;
			}

			$button.addClass('loading');

			// Get the latest values from the form
			var product_id = $button.data('product-id').toString();
			var quantity = $form.find('.quantity .qty').val() || 1;

			var param_ajax = {
				action: 'somnia_products_add_to_cart_simple',
				product_id: product_id,
				quantity: quantity
			};

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: AJ_Options.ajax_url,
				data: param_ajax,
				beforeSend: function () {

				},
				success: function (response) {
					if (response.success) {
						$('.bt-js-add-to-cart-simple').removeClass('loading');
						

					// Update mini cart after successful add to cart
					$.ajax({
						url: wc_cart_fragments_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_refreshed_fragments'),
						type: 'POST',
						success: function (response) {
							if (response && response.fragments) {
								$.each(response.fragments, function (key, value) {
									$(key).replaceWith(value);
								});
								const cartCount = parseInt($('.bt-mini-cart .cart_total').text());
								if (cartCount === 0) {
									$(".bt-mini-cart-sidebar .bt-progress-content").addClass("bt-hide");
								} else {
									$(".bt-mini-cart-sidebar .bt-progress-content").removeClass("bt-hide");
								}
								// Trigger fragments refreshed event to update note button class
								$(document.body).trigger('wc_fragments_refreshed');
								
								// Open mini cart after fragments are loaded
								if (product_id) {
									SomniaHandleCartAction(product_id);
								}
								// Free shipping message
								SomniaFreeShippingMessage();
							}
						},
						error: function () {
							console.error('Failed to update mini cart.');
						}
					});
					} else {
						console.log('error');
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log('The following error occured: ' + textStatus, errorThrown);
				}
			});
		});
	}
	/* Load data for default active variation items */
	function SomniaLoadDefaultActiveVariations(context) {
		// If context is provided, search within that context, otherwise search globally
		var $context = context ? $(context) : $(document);
		var $variationForms = $context.find('.variations_form').not('.bt-product-inner .variations_form');
		var $singleProductForms = $context.find('.bt-product-inner .variations_form');

		// Handle single product variation form inside .bt-product-inner (only out-of-stock logic)
		if ($singleProductForms.length > 0) {
			var $form = $singleProductForms;
			$form.off('show_variation.somniasetdefault').on('show_variation.somniasetdefault', function (event, variation) {
				if (!variation) return;
				if (!variation.is_in_stock && $form.parent().find('.bt-notification-form').length > 0) {
					$form.addClass('out-of-stock');
				} else {
					$form.removeClass('out-of-stock');
				}
			});
		}

		// Handle other variation forms (full logic)
		if ($variationForms.length > 0) {

			$variationForms.each(function () {
				var $form = $(this);
				$form.off('show_variation.somniasetdefault').on('show_variation.somniasetdefault', function (event, variation) {
					if (!variation) return;
					if (!variation.is_in_stock && $form.parent().find('.bt-notification-form').length > 0) {
						$form.addClass('out-of-stock');
					} else {
						$form.removeClass('out-of-stock');
					}
					$form.find('.bt-attributes-wrap .bt-js-item.active').each(function () {
						var $item = $(this);
						var $attrItem = $item.closest('.bt-attributes--item');
						var attrName = $attrItem.data('attribute-name');
						var colorTaxonomy = AJ_Options.color_taxonomy;
						var name = (attrName == colorTaxonomy) ? $item.find('label').html() : $item.html();
						$attrItem.find('.bt-result').html(name);
					});
				});

				var $activeItems = $form.find('.bt-attributes-wrap .bt-js-item.active');
				if ($activeItems.length > 0 && !$form.find('.bt-attributes--item').filter(function () {
					return !$(this).find('.bt-js-item.active').length;
				}).length) {
					$activeItems.first().trigger('click');
				}
			});

		}
	}

	/* Auto switch grid on smaller screens and hide/show grid buttons */
	function SomniaHandleGridViewResize() {
		var currentActiveView = $('.bt-view-type.active').data('view');
		var windowWidth = $(window).width();

		if (windowWidth < 651) {
			// Hide all except grid-2
			$('.bt-view-type.bt-view-list').hide();
			$('.bt-view-type.bt-view-grid-3').hide();
			$('.bt-view-type.bt-view-grid-4').hide();

			// Force all views to grid-2
			if (currentActiveView !== 'grid-2') {
				$('.bt-view-type.bt-view-grid-2').trigger('click');
			}
		} else if (windowWidth < 768) {
			// Show list and grid-2, hide grid-3 and grid-4
			$('.bt-view-type.bt-view-list').show();
			$('.bt-view-type.bt-view-grid-3').hide();
			$('.bt-view-type.bt-view-grid-4').hide();

			// Auto-switch if currently on grid-3 or grid-4
			if (currentActiveView === 'grid-3' || currentActiveView === 'grid-4') {
				$('.bt-view-type.bt-view-grid-2').trigger('click');
			}
		} else if (windowWidth < 1200) {
			// Show list, grid-2, grid-3, hide grid-4
			$('.bt-view-type.bt-view-list').show();
			$('.bt-view-type.bt-view-grid-3').show();
			$('.bt-view-type.bt-view-grid-4').hide();

			// Auto-switch if currently on grid-4
			if (currentActiveView === 'grid-4') {
				$('.bt-view-type.bt-view-grid-3').trigger('click');
			}
		} else {
			// Show all grid buttons
			$('.bt-view-type.bt-view-list').show();
			$('.bt-view-type.bt-view-grid-3').show();
			$('.bt-view-type.bt-view-grid-4').show();
		}
	}
	// Frequently Bought Together Handler
	function SomniaFrequentlyBoughtTogether() {
		const $fbtSection = $('.somnia-frequently-bought-together');
		if ($fbtSection.length === 0) return;

		const $productsList = $fbtSection.find('.fbt-products-list');
		const $totalAmount = $fbtSection.find('.fbt-total-amount');
		const $addToCartBtn = $fbtSection.find('.fbt-add-to-cart-btn');
		const $currentProduct = $productsList.find('.fbt-current-product');
		const isVariable = $currentProduct.data('is-variable') === 1;

		// Get currency settings
		const currencySymbol = $productsList.data('currency') || '$';
		const decimalSeparator = $productsList.data('decimal-separator') || '.';
		const thousandSeparator = $productsList.data('thousand-separator') || ',';

		let currentVariationId = null;
		let currentVariationSelected = !isVariable; // If not variable, consider as selected

		// Function to parse price from HTML
		function parsePrice(priceText) {
			return parseFloat(
				priceText
					.replace(new RegExp('[^0-9' + thousandSeparator + decimalSeparator + ']+', 'g'), '')
					.replace(new RegExp('\\' + thousandSeparator, 'g'), '')
					.replace(new RegExp('\\' + decimalSeparator, 'g'), '.')
			) || 0;
		}

		// Function to format price
		function formatPrice(price) {
			const parts = price.toFixed(2).split('.');
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
			return currencySymbol + parts.join(decimalSeparator);
		}

		// Function to calculate total
		function calculateTotal() {
			let totalPrice = 0;
			let regularTotalPrice = 0;
			let checkedCount = 0;

			$productsList.find('.fbt-product-item').each(function () {
				const $item = $(this);
				const $checkbox = $item.find('input[type="checkbox"]');

				if ($checkbox.is(':checked')) {
					const price = parseFloat($item.data('price')) || 0;
					const regularPrice = parseFloat($item.data('regular-price')) || price;

					totalPrice += price;
					regularTotalPrice += regularPrice;

					// Count only non-disabled checkboxes
					if (!$checkbox.is(':disabled')) {
						checkedCount++;
					}
				}
			});

			// Update total display
			if (regularTotalPrice > totalPrice) {
				$totalAmount.html('<del>' + formatPrice(regularTotalPrice) + '</del> ' + formatPrice(totalPrice));
			} else {
				$totalAmount.text(formatPrice(totalPrice));
			}

			// Enable/disable button
			// Disabled if: variable product without selection OR no other products checked
			if (!currentVariationSelected || checkedCount === 0) {
				$addToCartBtn.prop('disabled', true).addClass('disabled');
			} else {
				$addToCartBtn.prop('disabled', false).removeClass('disabled');
			}
		}

		// Handle checkbox change
		$productsList.on('change', 'input[type="checkbox"]:not(:disabled)', function () {
			calculateTotal();
		});

		// Listen to WooCommerce variation changes
		if (isVariable) {
			$('.variations_form').on('found_variation', function (event, variation) {
				currentVariationId = variation.variation_id;
				currentVariationSelected = true;

				// Update current product data
				$currentProduct.data('product-id', variation.variation_id);
				$currentProduct.attr('data-product-id', variation.variation_id);
				$currentProduct.data('price', variation.display_price);
				$currentProduct.data('regular-price', variation.display_regular_price || variation.display_price);

				// Update checkbox value
				$currentProduct.find('input[type="checkbox"]').val(variation.variation_id);

				// Update price display
				const $priceDiv = $currentProduct.find('.fbt-product-price');
				if (variation.price_html) {
					$priceDiv.html(variation.price_html);
				}

				// Update variation text
				let variationText = '';
				if (variation.attributes) {
					const attrValues = [];
					for (let key in variation.attributes) {
						if (variation.attributes.hasOwnProperty(key)) {
							let value = variation.attributes[key];
							// Capitalize first letter
							value = value.charAt(0).toUpperCase() + value.slice(1);
							attrValues.push(value);
						}
					}
					if (attrValues.length > 0) {
						variationText = ' - ' + attrValues.join('/');
					}
				}
				$currentProduct.find('.fbt-variation-text').text(variationText);

				// Recalculate total
				calculateTotal();
			});

			$('.variations_form').on('reset_data', function () {
				currentVariationId = null;
				currentVariationSelected = false;

				// Reset variation text
				$currentProduct.find('.fbt-variation-text').text('');

				// Disable button
				calculateTotal();
			});
		}

		// Handle add to cart button click
		$addToCartBtn.on('click', function (e) {
			e.preventDefault();

			if ($(this).prop('disabled')) {
				return;
			}
			if ($(this).hasClass('bt-view-cart')) {
				window.location.href = AJ_Options.cart;
				return;
			}
			const productIds = [];
			$productsList.find('.fbt-product-item input[type="checkbox"]:checked').each(function () {
				const productId = $(this).val();
				// For current product, use variation ID if selected
				if ($(this).closest('.fbt-current-product').length && currentVariationId) {
					productIds.push(currentVariationId);
				} else {
					productIds.push(productId);
				}
			});

			if (productIds.length === 0) {
				return;
			}

			// Disable button and show loading

			$addToCartBtn.prop('disabled', true).addClass('loading');

			$.ajax({
				url: AJ_Options.ajax_url,
				type: 'POST',
				data: {
					action: 'somnia_add_fbt_to_cart',
					product_ids: productIds
				},
				success: function (response) {
					if (response.success) {
						// Handle cart action for each added product with sequential delay
						if (response.data.added && response.data.added.length > 0) {
							response.data.added.forEach((productId, idx) => {
								setTimeout(() => {
									SomniaHandleCartAction(productId);
								}, idx * 300);
							});
						}

						// Trigger WooCommerce added_to_cart event
						$(document.body).trigger('wc_fragment_refresh');
						$addToCartBtn.text('View Cart').prop('disabled', false).addClass('bt-view-cart');
					}

					// Reset button
					$addToCartBtn.prop('disabled', false).removeClass('loading');
					calculateTotal(); // Recheck state
				},
				error: function () {
					$addToCartBtn.prop('disabled', false).removeClass('loading');
					calculateTotal(); // Recheck state
				}
			});
		});

		// Calculate initial total on page load
		calculateTotal();
	}

	/* Elementor Slider Control - Button click to trigger slider arrows */
	function SomniaElementorSliderControl() {
		// Handle click on buttons with class bt-click-right-{name-slider} or bt-click-left-{name-slider}
		$(document).on('click', '[class*="bt-click-right-"], [class*="bt-click-left-"]', function (e) {
			e.preventDefault();

			var $button = $(this);
			var buttonClasses = $button.attr('class').split(/\s+/);
			var sliderName = '';
			var direction = '';

			// Find the class that contains bt-click-right- or bt-click-left-
			buttonClasses.forEach(function (className) {
				if (className.indexOf('bt-click-right-') === 0) {
					sliderName = className.replace('bt-click-right-', '');
					direction = 'next';
				} else if (className.indexOf('bt-click-left-') === 0) {
					sliderName = className.replace('bt-click-left-', '');
					direction = 'prev';
				}
			});

			// If we found a slider name and direction
			if (sliderName && direction) {
				// Find the slider with class {name-slider}
				var $slider = $('.' + sliderName);

				if ($slider.length > 0) {
					// Try to find Swiper instance on the slider
					var sliderElement = $slider[0];

					// Check if it's a Swiper slider
					if (sliderElement.swiper) {
						// Use Swiper API to navigate
						if (direction === 'next') {
							sliderElement.swiper.slideNext();
						} else {
							sliderElement.swiper.slidePrev();
						}
					} else {
						// Fallback: try to find and click the arrow buttons
						var arrowSelector = direction === 'next'
							? '.swiper-button-next, .elementor-swiper-button-next, .slick-next, [class*="arrow-next"], [class*="button-next"]'
							: '.swiper-button-prev, .elementor-swiper-button-prev, .slick-prev, [class*="arrow-prev"], [class*="button-prev"]';

						var $arrow = $slider.find(arrowSelector).first();

						if ($arrow.length > 0) {
							$arrow.trigger('click');
						} else {
							// Try to find arrow buttons within the slider container
							$arrow = $slider.closest('.elementor-widget-container, .elementor-container').find(arrowSelector).first();
							if ($arrow.length > 0) {
								$arrow.trigger('click');
							}
						}
					}
				}
			}
		});
	}
	/* Mini Cart Note Handler */
	function SomniaMiniCartNoteHandler() {
		// Fill order_comments field with note from localStorage
		function fillOrderComments() {
			try {
				const savedNote = localStorage.getItem('bt_cart_note');
				if (savedNote) {
					const $orderComments = $('#order_comments');
					if ($orderComments.length && !$orderComments.val()) {
						$orderComments.val(savedNote);
						// Trigger change event to ensure WooCommerce picks it up
						$orderComments.trigger('change');
					}
				}
			} catch (error) {
				console.error('Error loading note from localStorage:', error);
			}
		}

		// Clear localStorage after successful checkout
		function clearCartNote() {
			try {
				localStorage.removeItem('bt_cart_note');
			} catch (error) {
				console.error('Error clearing note from localStorage:', error);
			}
		}

		// Fill order_comments on checkout page
		if ($('body').hasClass('woocommerce-checkout')) {
			// Fill on page load
			fillOrderComments();

			// Also fill after checkout form updates (for AJAX updates)
			$('body').on('updated_checkout', function () {
				fillOrderComments();
			});
		}

		// Clear localStorage after successful order
		$('body').on('woocommerce_thankyou', function (event, orderId) {
			clearCartNote();
		});

		// Also clear on order received page
		if ($('body').hasClass('woocommerce-order-received')) {
			clearCartNote();
		}
	}
	/* Preloader */
	function SomniaPreloader() {
		var $preloader = $('#bt-preloader');
		if ($preloader.length === 0) return;

		// Function to hide preloader
		function hidePreloader() {
			// Fade out the entire preloader
			$preloader.css('opacity', '0');

			// Remove from DOM after fade out
			setTimeout(function () {
				$preloader.remove();
			}, 400);
		}

		// Hide preloader when page is fully loaded
		$(window).on('load', function () {
			hidePreloader();
		});

		// Fallback: hide preloader after maximum wait time (3 seconds) if load event doesn't fire
		setTimeout(function () {
			if ($preloader.length && $preloader.css('opacity') !== '0') {
				hidePreloader();
			}
		}, 3000);
	}
	jQuery(document).ready(function ($) {
		SomniaSubmenuAuto();
		SomniaToggleMenuMobile();
		SomniaToggleSubMenuMobile();
		SomniaShop();
		SomniaProductVariationHandler();
		SomniaCommentValidation();
		SomniaProductCompare();
		SomniaProductCompareLoad();
		SomniaProductWishlist();
		SomniaProductWishlistLoad();
		SomniaProductQuickView();
		LoadSomniaProductToast();
		SomniaProductsFilter();
		SomniaLoadMoreButton();
		SomniaInfiniteScroll();
		SomniaProductFilterToggle();
		SomniaAttachTooltips();
		SomniaUpdateMiniCart();
		SomniaProgressCart();
		SomniaCountdownCart();
		SomniaBuyNow();
		SomniaReviewPopup();
		SomniaHandleGridViewResize();
		SomniaHookGravityFormEvents();
		SomniaProductButtonStatus();
		SomniaCopyrightCurrentYear();
		SomniaCompareContentScroll();
		SomniaBackToTop();
		SomniaLoadFilterTagProduct();
		SomniaAddToRecentlyViewed();
		SomniaLoadRecentlyViewedProducts();
		SomniaCountdownProductSale();
		SomniaCustomizeProductToggle();
		SomniaCustomizeGroupedProduct();
		SomniaPopupNewsletter();
		SomniaProductAttributeVariationSwitch();
		SomniaUpdateBodyWidthVariable();
		SomniaAddToCartVariable();
		SomniaAddToCartSimple();
		SomniaLoadDefaultActiveVariations(); // Load data for default active variations
		SomniaFrequentlyBoughtTogether();
		SomniaElementorSliderControl(); // Elementor slider control via button clicks
		SomniaMiniCartNoteHandler();	// Initialize Mini Cart Note Handler
		SomniaPreloader(); // Initialize Preloader
	});
	// Block WooCommerce from changing images
	jQuery(function ($) {
		$.fn.wc_variations_image_update = function (variation) {
			return this;
		};
	});

	$(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
		// Only show toast if not in Elementor editor
		if (!$('body').hasClass('elementor-editor-active')) {
			// Get product ID from button that triggered the event
			var productId = null;
			if ($button && $button.data('product_id')) {
				productId = $button.data('product_id');
			}
			
			// Apply fragments if available
			if (fragments) {
				$.each(fragments, function (key, value) {
					$(key).replaceWith(value);
				});
			}
			
			// Wait a bit to ensure DOM is updated before opening mini cart
			setTimeout(function() {
				SomniaFreeShippingMessage();
				if (productId) {
					SomniaHandleCartAction(productId);
				}
			}, 100);
		}
	});
	$(document).on('removed_from_cart', function () {
		SomniaFreeShippingMessage();
	});

	jQuery(window).on('resize', function () {
		SomniaSubmenuAuto();
		SomniaUpdateBodyWidthVariable();
		SomniaHandleGridViewResize();
	});
	$(document.body).on('updated_cart_totals', function () {
		SomniaFreeShippingMessage();
	});
	/* Check if cart is empty */
	$(document.body).on('wc_fragments_loaded wc_fragments_refreshed', function () {
		const cartCount = parseInt($('.bt-mini-cart .cart_total').text());
		if (cartCount === 0) {
			$(".bt-mini-cart-sidebar .bt-progress-content").addClass("bt-hide");
		} else {
			$(".bt-mini-cart-sidebar .bt-progress-content").removeClass("bt-hide");
		}
	});
	// Handle search-product-style-1 widget in Elementor popup
	jQuery(window).on('elementor/popup/show', function (event, id, instance) {

		// Try to modify dialog settings and override hide function
		if (instance && instance.getModal) {
			setTimeout(function () {
				try {
					var modal = instance.getModal();
					if (modal) {
						// Modify settings to add quickview/compare to ignore list
						if (modal.getSettings) {
							var settings = modal.getSettings();
							if (settings && settings.hide) {
								var currentIgnore = settings.hide.ignore || '';
								var ignoreList = currentIgnore ? currentIgnore.split(',').map(function (s) { return s.trim(); }) : [];
								if (ignoreList.indexOf('.bt-popup-quick-view') === -1) {
									ignoreList.push('.bt-popup-quick-view');
								}
								if (ignoreList.indexOf('.bt-popup-compare') === -1) {
									ignoreList.push('.bt-popup-compare');
								}
								settings.hide.ignore = ignoreList.join(', ');
								if (modal.setSettings) {
									modal.setSettings(settings);
								}
							}
						}

					}
				} catch (e) {
					console.log('Could not modify Elementor popup:', e);
				}
			}, 100);
		}

		var $popup = jQuery('#elementor-popup-modal-' + id);
		if (!$popup.length) {
			return;
		}

		// Only proceed if search-product-style-1 widget exists in popup
		var $searchWidget = $popup.find('.bt-elwg-search-product-style-1');
		if (!$searchWidget.length) {
			return;
		}

		var $productsDisplay = $searchWidget.find('.bt-products-display-section');
		var $wrapperInner = $searchWidget.find('.bt-products-wrapper-inner');

		if (!$productsDisplay.length || !$wrapperInner.length) {
			return;
		}

		// Function to check and hide bt-products-wrapper-inner if no products
		function checkAndHideProductsWrapper() {
			var productsSource = $productsDisplay.data('source');
			var shouldHide = false;

			// Check based on products_source
			if (productsSource === 'recent_viewed') {
				// Check if recentlyViewed is empty or doesn't exist
				var recentlyViewed = localStorage.getItem('recentlyViewed');
				if (!recentlyViewed || recentlyViewed === '[]' || recentlyViewed === '') {
					shouldHide = true;
				} else {
					try {
						var parsedRecentlyViewed = JSON.parse(recentlyViewed);
						if (!parsedRecentlyViewed || parsedRecentlyViewed.length === 0) {
							shouldHide = true;
						}
					} catch (e) {
						shouldHide = true;
					}
				}
			} else {
				// For other sources, check data-has-products attribute
				var hasProductsAttr = $wrapperInner.attr('data-has-products');
				if (hasProductsAttr === 'false') {
					shouldHide = true;
				} else if (hasProductsAttr !== 'true') {
					// Data attribute not set yet (products still loading)
					return false;
				}
			}

			if (shouldHide) {
				$wrapperInner.hide();
				return true;
			}

			return false;
		}

		// Try to check and hide immediately
		if (!checkAndHideProductsWrapper()) {
			// Wait a bit for DOM/AJAX to complete
			setTimeout(function () {
				if (!checkAndHideProductsWrapper()) {
					// Watch for data-has-products attribute changes
					var observer = new MutationObserver(function (mutations) {
						if (checkAndHideProductsWrapper()) {
							observer.disconnect();
						}
					});
					observer.observe($wrapperInner[0], {
						attributes: true,
						attributeFilter: ['data-has-products']
					});
					// Stop observing after 3 seconds
					setTimeout(function () {
						observer.disconnect();
					}, 3000);
				}
			}, 100);
		}
	});

	jQuery(window).on('scroll', function () {

	});



})(jQuery);
