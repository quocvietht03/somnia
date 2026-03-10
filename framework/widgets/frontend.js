(function ($) {
	/**
	   * @param $scope The Widget wrapper element as a jQuery element
	 * @param $ The jQuery alias
	**/

	/* Submenu toggle */
	const SubmenuToggleHandler = function ($scope, $) {
		var hasChildren = $scope.find('.menu-item-has-children');

		hasChildren.each(function () {
			var $btnToggle = $('<span class="bt-toggle-icon"></span>');

			$(this).append($btnToggle);

			$btnToggle.on('click', function (e) {
				e.preventDefault();

				if ($(this).parent().hasClass('bt-is-active')) {
					$(this).parent().removeClass('bt-is-active');
					$(this).parent().children('ul').slideUp();
				} else {
					$(this).parent().addClass('bt-is-active');
					$(this).parent().children('ul').slideDown();
					$(this).parent().siblings().removeClass('bt-is-active').children('ul').slideUp();
					$(this).parent().siblings().find('li').removeClass('bt-is-active').children('ul').slideUp();
				}
			});
		});
	}

	const FaqHandler = function ($scope, $) {
		const $titleFaq = $scope.find('.bt-item-title');
		if ($titleFaq.length > 0) {
			$titleFaq.on('click', function (e) {
				e.preventDefault();
				if ($(this).hasClass('active')) {
					$(this).parent().find('.bt-item-content').slideUp();
					$(this).removeClass('active');
				} else {
					$(this).parent().find('.bt-item-content').slideDown();
					$(this).addClass('active');
				}
			});
		}
	};
	const BtAccordionHandler = function ($scope, $) {
		const $accordionTitle = $scope.find('.bt-accordion-title');
		if ($accordionTitle.length > 0) {
			$accordionTitle.on('click', function (e) {
				e.preventDefault();
				const $currentItem = $(this);
				const $content = $currentItem.parent().find('.bt-accordion-content');

				if ($currentItem.hasClass('active')) {
					$content.slideUp();
					$currentItem.removeClass('active');
				} else {
					// Close other accordion items (single accordion behavior)
					$scope.find('.bt-accordion-title.active').removeClass('active');
					$scope.find('.bt-accordion-content').slideUp();

					// Open current item
					$content.slideDown();
					$currentItem.addClass('active');
				}
			});
		}
	};
	// Helper function to generate correct URL based on widget category settings
	const generateCategoryUrl = function ($form) {
		// Get widget category slugs from bt-widget-category-include (now contains slugs, not IDs)
		const $widgetCategoryInclude = $form.find('.bt-widget-category-include');
		const widgetCategorySlugs = $widgetCategoryInclude.length ? $widgetCategoryInclude.val() : '';
		const widgetCategoryCount = widgetCategorySlugs ? widgetCategorySlugs.split(',').filter(function (slug) { return slug.trim() !== ''; }).length : 0;

		// Get widget category exclude slugs (now contains slugs, not IDs)
		const $widgetCategoryExclude = $form.find('.bt-widget-category-exclude');
		const widgetCategoryExcludeSlugs = $widgetCategoryExclude.length ? $widgetCategoryExclude.val() : '';

		const widgetSingleCategoryUrl = $form.data('widget-single-category-url') || '';
		const selectedCategoryUrl = $form.data('category-url');
		const shopUrl = $form.attr('action') || '';

		// Get selected category slug to check if "All Categories" is selected
		const $catProductInput = $form.find('input[name="product_cat"]');
		const selectedCatSlug = $catProductInput.length ? $catProductInput.val() : '';

		let url = '';
		let isShopUrl = false; // Flag to check if URL is shop URL (not category page)

		// Rule: If has include, ignore exclude. Only use exclude when there's no include.
		const hasInclude = widgetCategoryCount > 0;
		const shouldUseExclude = !hasInclude && widgetCategoryExcludeSlugs;

		// Priority 1: If user selected a specific category from dropdown (not "All Categories"), use that
		if (selectedCategoryUrl && selectedCatSlug && selectedCatSlug !== '') {
			url = selectedCategoryUrl;
			// This is a category page, not shop URL
			isShopUrl = false;
			// Don't add excluded_product_cat for category pages
		}
		// Priority 2: If "All Categories" is selected (selectedCatSlug is empty), check widget include categories
		// If widget has 2+ categories included, build shop URL with product_cat parameter
		else if (widgetCategoryCount >= 2 && widgetCategorySlugs) {
			// Get base shop URL (remove any existing query parameters)
			let baseShopUrl = shopUrl.split('?')[0];
			// Add product_cat parameter
			url = baseShopUrl;
			const separator = url.indexOf('?') !== -1 ? '&' : '?';
			url += separator + 'product_cat=' + encodeURIComponent(widgetCategorySlugs);
			// Don't add excluded_product_cat because we have include
			// This is shop URL
			isShopUrl = true;
		}
		// If widget has 1 category included, use that category page URL
		else if (widgetCategoryCount === 1 && widgetSingleCategoryUrl) {
			url = widgetSingleCategoryUrl;
			// This is a category page, not shop URL
			isShopUrl = false;
			// Don't add excluded_product_cat for category pages or when we have include
		}
		// If "All Categories" is selected (selectedCatSlug is empty) and no widget include categories
		// Use shop URL and check for exclude
		else if (!selectedCatSlug || selectedCatSlug === '') {
			url = shopUrl;
			// This is shop URL
			isShopUrl = true;
			// Add excluded_product_cat only if we have exclude and no include, and it's shop URL
			if (shouldUseExclude) {
				url = addUrlParameter(url, 'excluded_product_cat', widgetCategoryExcludeSlugs);
			}
		}
		// If no widget categories and selected category URL exists (fallback)
		else if (selectedCategoryUrl) {
			url = selectedCategoryUrl;
			// This is a category page, not shop URL
			isShopUrl = false;
			// Don't add excluded_product_cat for category pages
		}
		// Default: use shop URL
		else {
			url = shopUrl;
			// This is shop URL
			isShopUrl = true;
			// Add excluded_product_cat only if we have exclude and no include, and it's shop URL
			if (shouldUseExclude) {
				url = addUrlParameter(url, 'excluded_product_cat', widgetCategoryExcludeSlugs);
			}
		}

		// Add formsearch=true to shop URLs only if URL has product_cat or excluded_product_cat
		if (isShopUrl && (url.indexOf('product_cat=') !== -1 || url.indexOf('excluded_product_cat=') !== -1)) {
			url = addUrlParameter(url, 'formsearch', 'true');
		}

		return url;
	};

	// Helper function to add URL parameter
	const addUrlParameter = function (url, paramName, paramValue) {
		// Remove existing parameter if exists
		url = url.replace(new RegExp('([?&])' + paramName + '=[^&]*'), '');
		// Clean up trailing ? or &
		url = url.replace(/[?&]$/, '');
		// Add parameter
		const separator = url.indexOf('?') !== -1 ? '&' : '?';
		return url + separator + paramName + '=' + encodeURIComponent(paramValue);
	};

	/* ===== KEYWORD SUGGESTION HANDLER ===== */
	const KeywordSuggestionHandler = function ($scope, $) {
		const $keywordInputs = $scope.find('.bt-keyword-suggest');

		if ($keywordInputs.length === 0) {
			return;
		}

		$keywordInputs.each(function () {
			const $input = $(this);
			const $searchWrap = $input.closest('.bt-search-wrap');
			const $ghost = $searchWrap.find('.bt-keyword-ghost');

			if ($ghost.length === 0 || $searchWrap.length === 0) {
				return;
			}

			// Get word pool from data-suggest attribute on bt-search-wrap div
			const suggestData = $searchWrap.data('suggest');
			let wordPool = [];

			if (suggestData && Array.isArray(suggestData)) {
				wordPool = suggestData;
			} else if (typeof suggestData === 'string') {
				// Try to parse JSON if it's a string
				try {
					wordPool = JSON.parse(suggestData);
				} catch (e) {
					console.warn('Failed to parse keyword suggestions:', e);
				}
			}

			if (wordPool.length === 0) {
				return;
			}

			// Input event handler
			$input.on('input', function () {
				const value = $input.val();
				$ghost.val('');

				const parts = value.split(' ');
				const lastWordOriginal = parts[parts.length - 1];
				const lastWord = lastWordOriginal.toLowerCase();

				if (!lastWord) {
					return;
				}

				// Check if input is valid case pattern (not mixed case)
				const isAllUppercase = lastWordOriginal === lastWordOriginal.toUpperCase() && lastWordOriginal.length > 1;
				const isAllLowercase = lastWordOriginal === lastWordOriginal.toLowerCase();
				const isTitleCase = lastWordOriginal.length > 0 &&
					lastWordOriginal[0] === lastWordOriginal[0].toUpperCase() &&
					lastWordOriginal.slice(1) === lastWordOriginal.slice(1).toLowerCase();

				// Only show suggestion for valid case patterns
				if (!isAllUppercase && !isAllLowercase && !isTitleCase) {
					return; // Hide suggestion for mixed case
				}

				const match = wordPool.find(function (w) {
					return w.startsWith(lastWord) && w !== lastWord;
				});

				if (match) {
					// Preserve the case of the user's input
					let suggestedWord = match;

					if (isAllUppercase) {
						// All uppercase: convert match to uppercase
						suggestedWord = match.toUpperCase();
					} else if (isTitleCase || (lastWordOriginal.length > 0 && lastWordOriginal[0] === lastWordOriginal[0].toUpperCase())) {
						// First letter uppercase: convert to title case
						suggestedWord = match.charAt(0).toUpperCase() + match.slice(1);
					}
					// Otherwise keep lowercase (isAllLowercase)

					parts[parts.length - 1] = suggestedWord;
					$ghost.val(parts.join(' '));
				}
			});

			// Tab key handler to accept suggestion
			$input.on('keydown', function (e) {
				if (e.key === 'Tab' && $ghost.val()) {
					e.preventDefault();
					$input.val($ghost.val());
					$ghost.val('');
					// Trigger input event to run AJAX live search
					$input.trigger('input');
				}
			});
		});
	};
	const SearchProductHandler = function ($scope, $) {
		const $searchProduct = $scope.find('.bt-elwg-search-product');
		if ($searchProduct.length) {
			const $selectedCategory = $searchProduct.find('.bt-selected-category');
			const $categoryList = $searchProduct.find('.bt-category-list');
			const $categoryItems = $searchProduct.find('.bt-category-item');
			const $catProductInput = $searchProduct.find('input[name="product_cat"]');

			$selectedCategory.on('click', function (e) {
				e.stopPropagation();
				$categoryList.toggle();
			});

			// Handle category selection
			$categoryItems.on('click', function (e) {
				e.preventDefault();
				const $this = $(this);
				const selectedText = $this.text();
				const catSlug = $this.data('cat-slug');
				const catUrl = $this.data('cat-url');

				// Update selected category
				$selectedCategory.find('span').text(selectedText);
				$catProductInput.val(catSlug);

				// Store category URL in form data attribute
				$searchProduct.find('.bt-search--form').data('category-url', catUrl);

				// Update active class
				$categoryItems.removeClass('active');
				$this.addClass('active');

				// Hide dropdown
				$categoryList.hide();
			});

			// Handle form submit - update action URL based on category
			$searchProduct.find('.bt-search--form').on('submit', function (e) {
				e.preventDefault();
				const $form = $(this);
				const $searchInput = $form.find('input[name="search_keyword"]');
				const searchKeyword = $searchInput.length ? $searchInput.val().trim() : '';
				const shopUrl = $form.attr('action') || '';

				// Generate correct URL based on widget settings
				let finalUrl = generateCategoryUrl($form);

				// Only add search_keyword to URL if it's not empty
				if (searchKeyword) {
					// Remove existing search_keyword and formsearch parameters if exists
					finalUrl = finalUrl.replace(/([?&])search_keyword=[^&]*/g, '');
					finalUrl = finalUrl.replace(/([?&])formsearch=[^&]*/g, '');
					// Clean up trailing ? or &
					finalUrl = finalUrl.replace(/[?&]$/, '');
					// Add search_keyword parameter
					const separator = finalUrl.indexOf('?') !== -1 ? '&' : '?';
					finalUrl += separator + 'search_keyword=' + encodeURIComponent(searchKeyword);
					// Add formsearch=true at the end only if URL has product_cat or excluded_product_cat
					if (finalUrl.indexOf('product_cat=') !== -1 || finalUrl.indexOf('excluded_product_cat=') !== -1) {
						finalUrl += '&formsearch=true';
					}
				} else {
					// Ensure formsearch=true is at the end only if URL has product_cat or excluded_product_cat
					if (finalUrl.indexOf('product_cat=') !== -1 || finalUrl.indexOf('excluded_product_cat=') !== -1) {
						finalUrl = finalUrl.replace(/([?&])formsearch=[^&]*/g, '');
						finalUrl = finalUrl.replace(/[?&]$/, '');
						finalUrl += (finalUrl.indexOf('?') !== -1 ? '&' : '?') + 'formsearch=true';
					} else {
						// Remove formsearch if URL doesn't have product_cat or excluded_product_cat
						finalUrl = finalUrl.replace(/([?&])formsearch=[^&]*/g, '');
						finalUrl = finalUrl.replace(/[?&]$/, '');
					}
				}

				// Redirect to the correct URL
				window.location.href = finalUrl;
			});

			// Close dropdown when clicking outside
			$(document).on('click', function () {
				$categoryList.hide();
			});
			// scroll window remove class active live search results
			$(window).scroll(function () {
				$liveSearchResults.removeClass('active');
			});
			const $liveSearch = $searchProduct.find('.bt-live-search');
			const $liveSearchResults = $searchProduct.find('.bt-live-search-results');
			const $dataSearch = $searchProduct.find('.bt-live-search-results .bt-load-data');
			let typingTimer;
			const doneTypingInterval = 500; // 0.5 second delay after typing stops

			const performSearch = function () {
				const searchTerm = $liveSearch.val().trim();
				if (searchTerm.length >= 2) {
					// Get widget settings for category filtering
					const categoryInclude = $searchProduct.find('.bt-widget-category-include').val();
					const categoryExclude = $searchProduct.find('.bt-widget-category-exclude').val();
					const autocompleteLimit = $searchProduct.find('.bt-autocomplete-limit').val() || 5;

					var param_ajax = {
						action: 'somnia_search_live',
						search_term: searchTerm,
						category_slug: $catProductInput.val(),
						widget_category_include: categoryInclude,
						widget_category_exclude: categoryExclude,
						autocomplete_limit: autocompleteLimit
					};
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: AJ_Options.ajax_url,
						data: param_ajax,
						context: this,
						beforeSend: function () {
							$liveSearchResults.addClass('active');
							//	$liveSearchResults.addClass('loading');
							// Show loading skeleton for 3 product items
							let skeletonHtml = '';
							for (let i = 0; i < 3; i++) {
								skeletonHtml += `
							<div class="bt-product-skeleton product">
								<div class="bt-skeleton-thumb">
									<div class="bt-skeleton-image"></div>
									<div class="bt-skeleton-content">
										<div class="bt-skeleton-title"></div>
										<div class="bt-skeleton-price"></div>
									</div>
								</div>
								<div class="bt-skeleton-add-to-cart"></div>
							</div>
						`;
							}
							$dataSearch.html(skeletonHtml);
						},
						success: function (response) {
							if (response.success) {
								setTimeout(function () {
									$dataSearch.html(response.data['items']);

									// Handle "View All" button visibility and link
									const $viewAllButton = $searchProduct.find('.bt-view-all-button');
									const $viewAllResults = $searchProduct.find('.bt-view-all-results');

									if ($viewAllButton.length) {
										const hasProducts = response.data['has_products'];
										const isCustomLink = $viewAllButton.data('custom-link');

										// Always show button
										$viewAllResults.show();

										// Update link if not using custom link
										if (!isCustomLink) {
											const $form = $searchProduct.find('.bt-search--form');
											// Generate correct URL based on widget category settings
											let buttonUrl = generateCategoryUrl($form);

											if (hasProducts) {
												// Has products: use "has results" text and add search_keyword
												const textHasResults = $viewAllButton.data('text-has-results');
												if (textHasResults) {
													$viewAllButton.text(textHasResults);
												}

												// Remove existing search_keyword and formsearch parameters if exists
												buttonUrl = buttonUrl.replace(/([?&])search_keyword=[^&]*/g, '');
												buttonUrl = buttonUrl.replace(/([?&])formsearch=[^&]*/g, '');
												// Clean up trailing ? or &
												buttonUrl = buttonUrl.replace(/[?&]$/, '');

												// Add search_keyword parameter
												const separator = buttonUrl.indexOf('?') !== -1 ? '&' : '?';
												buttonUrl += separator + 'search_keyword=' + encodeURIComponent(searchTerm);
												// Add formsearch=true at the end only if URL has product_cat or excluded_product_cat
												if (buttonUrl.indexOf('product_cat=') !== -1 || buttonUrl.indexOf('excluded_product_cat=') !== -1) {
													buttonUrl += '&formsearch=true';
												}
											} else {
												// No products: use "no results" text and remove search_keyword
												const textNoResults = $viewAllButton.data('text-no-results');
												if (textNoResults) {
													$viewAllButton.text(textNoResults);
												}

												// Remove search_keyword parameter
												buttonUrl = buttonUrl.replace(/([?&])search_keyword=[^&]*/g, '');
												buttonUrl = buttonUrl.replace(/[?&]$/, '');
												// Ensure formsearch=true is at the end only if URL has product_cat or excluded_product_cat
												if (buttonUrl.indexOf('product_cat=') !== -1 || buttonUrl.indexOf('excluded_product_cat=') !== -1) {
													buttonUrl = buttonUrl.replace(/([?&])formsearch=[^&]*/g, '');
													buttonUrl = buttonUrl.replace(/[?&]$/, '');
													buttonUrl += (buttonUrl.indexOf('?') !== -1 ? '&' : '?') + 'formsearch=true';
												} else {
													// Remove formsearch if URL doesn't have product_cat or excluded_product_cat
													buttonUrl = buttonUrl.replace(/([?&])formsearch=[^&]*/g, '');
													buttonUrl = buttonUrl.replace(/[?&]$/, '');
												}
											}

											$viewAllButton.attr('href', buttonUrl);
										}
									}
								}, 300);
							} else {
								console.log('error');
							}
						},
						error: function (jqXHR, textStatus, errorThrown) {
							console.log('The following error occured: ' + textStatus, errorThrown);
						}
					});
					return false;
				} else {
					$dataSearch.empty();
				}
			};

			$liveSearch.on('input', function () {
				clearTimeout(typingTimer);
				const searchTerm = $(this).val().trim();
				if (searchTerm.length >= 2) {
					typingTimer = setTimeout(performSearch, doneTypingInterval);
				} else {
					$dataSearch.empty();
					$liveSearchResults.removeClass('active');
				}
			});
			$liveSearch.on('click', function () {
				const searchTerm = $(this).val().trim();
				if (searchTerm.length >= 2) {
					$liveSearchResults.addClass('active');
					if (window.location.href.includes('search_keyword')) {
						performSearch();
					}
				}
			});
			$categoryItems.on('click', function (e) {
				e.preventDefault();
				const searchTerm = $liveSearch.val().trim();
				// If search term exists, perform search
				if (searchTerm.length >= 2) {
					performSearch();
				} else {
					$liveSearchResults.removeClass('active');
				}
			});

			// Hide results when clicking outside
			$(document).on('click', function (e) {
				if (!$(e.target).closest('.bt-live-search-results').length &&
					!$(e.target).is('.bt-live-search') &&
					!$(e.target).closest('.bt-category-item').length) {
					$liveSearchResults.removeClass('active');
				}
			});

			// Initialize keyword suggestion if enabled
			KeywordSuggestionHandler($searchProduct, $);
		}
	};

	// Search Product Style 1 Handler
	const SearchProductStyle1Handler = function ($scope, $) {
		const $searchProduct = $scope.find('.bt-elwg-search-product-style-1');
		if ($searchProduct.length) {
			const $productsDisplay = $searchProduct.find('.bt-products-display-section');
			const $productsContainer = $productsDisplay.find('.bt-products-container');
			const $liveSearch = $searchProduct.find('.bt-live-search');
			const $liveSearchResults = $searchProduct.find('.bt-live-search-results');
			const $dataSearch = $liveSearchResults.find('.bt-load-data');
			const $catProductInput = $searchProduct.find('.bt-cat-product');
			const $form = $searchProduct.find('.bt-search--form');

			const source = $productsDisplay.data('source');
			const limit = parseInt($productsDisplay.data('limit')) || 8;
			const customProducts = $productsDisplay.data('products');
			let typingTimer;
			// Get typing interval based on device - mobile needs longer delay due to slower typing speed
			const getTypingInterval = function () {
				const windowWidth = $(window).width();
				const isMobile = windowWidth <= 570;
				return isMobile ? 800 : 500; // Mobile: 800ms, Desktop: 500ms
			};
			let resizeTimer;
			let previousIsMobile = $(window).width() <= 570;

			// Load products display section
			const loadProductsDisplay = function () {
				const recentlyViewed = localStorage.getItem('recentlyViewed') || '[]';
				const parsedRecentlyViewed = JSON.parse(recentlyViewed);
				const windowWidth = $(window).width();
				const isMobile = windowWidth <= 570;
				previousIsMobile = isMobile; // Update previous state

				$.ajax({
					url: AJ_Options.ajax_url,
					type: 'POST',
					data: {
						action: 'load_products_display',
						source: source,
						limit: limit,
						products: customProducts,
						recently_viewed: source === 'recent_viewed' ? parsedRecentlyViewed.slice(0, limit) : [],
						is_mobile: isMobile
					},
					success: function (response) {
						if (response.success) {
							$productsContainer.html(response.data['content']);

							// Set data attribute for has_products to make it easier to check
							const $wrapperInner = $productsDisplay.find('.bt-products-wrapper-inner');
							if ($wrapperInner.length) {
								const hasProducts = response.data['has_products'] || false;
								$wrapperInner.attr('data-has-products', hasProducts ? 'true' : 'false');

								// Hide/show wrapper-inner based on has_products
								if (!hasProducts) {
									$wrapperInner.hide();
								} else {
									$wrapperInner.show();
								}
							}

							// Initialize countdown timers for newly loaded products
							if (typeof window.SomniaCountdownProductSale === 'function') {
								window.SomniaCountdownProductSale($productsContainer);
							}

						}
					}
				});
			};

			// Perform search with grid layout
			const performSearch = function () {
				const searchTerm = $liveSearch.val().trim();
				if (searchTerm.length >= 2) {
					const categoryInclude = $searchProduct.find('.bt-widget-category-include').val();
					const categoryExclude = $searchProduct.find('.bt-widget-category-exclude').val();
					const autocompleteLimit = $searchProduct.find('.bt-autocomplete-limit').val() || 8;

					// Detect screen width to determine mobile layout
					const windowWidth = $(window).width();
					const isMobile = windowWidth <= 570;
					previousIsMobile = isMobile; // Update previous state

					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: AJ_Options.ajax_url,
						data: {
							action: 'somnia_search_live_style1',
							search_term: searchTerm,
							category_slug: $catProductInput.val(),
							widget_category_include: categoryInclude,
							widget_category_exclude: categoryExclude,
							autocomplete_limit: autocompleteLimit,
							is_mobile: isMobile
						},
						beforeSend: function () {
							$liveSearchResults.addClass('active loading');
							$productsDisplay.addClass('hidden');


							// Show loading skeleton
							let skeletonHtml = '';
							const skeletonCount = isMobile ? 4 : 4;

							if (isMobile) {
								// Mobile skeleton bt-product-item structure
								for (let i = 0; i < skeletonCount; i++) {
									skeletonHtml += `
							<div class="bt-product-item bt-product-skeleton">
								<div class="bt-product-thumb">
									<div class="bt-skeleton-thumbnail"></div>
									<div class="bt-product-title">
										<div class="bt-skeleton-title"></div>
										<div class="bt-skeleton-price"></div>
									</div>
								</div>
								<div class="bt-product-add-to-cart">
									<div class="bt-skeleton-button"></div>
								</div>
							</div>
						`;
								}
							} else {
								// Desktop skeleton WooCommerce structure
								for (let i = 0; i < skeletonCount; i++) {
									skeletonHtml += `
							<div class="bt-product-skeleton product">
								<div class="bt-skeleton-thumbnail"></div>
								<div class="bt-skeleton-content">
									<div class="bt-skeleton-title"></div>
									<div class="bt-skeleton-price"></div>
									<div class="bt-skeleton-rating"></div>
								</div>
							</div>
						`;
								}
							}
							$dataSearch.html(skeletonHtml);
						},
						success: function (response) {
							if (response.success) {
								setTimeout(function () {
									$dataSearch.html(response.data['items']);

									// Remove loading class from title
									$liveSearchResults.removeClass('loading');

									// Update title
									const $searchTitle = $searchProduct.find('.bt-search-results-title');
									const titleTemplate = $searchTitle.data('template');
									if (titleTemplate) {
										$searchTitle.text(titleTemplate.replace('%s', searchTerm));
									}

									// Initialize countdown timers for newly loaded products
									if (typeof window.SomniaCountdownProductSale === 'function') {
										window.SomniaCountdownProductSale($dataSearch);
									}

									// Handle "View All" button
									const $viewAllButton = $searchProduct.find('.bt-view-all-button');
									const $viewAllResults = $searchProduct.find('.bt-view-all-results');

									if ($viewAllButton.length) {
										const hasProducts = response.data['has_products'];
										const isCustomLink = $viewAllButton.data('custom-link');

										$viewAllResults.show();

										if (!isCustomLink) {
											// Generate correct URL based on widget category settings
											let buttonUrl = generateCategoryUrl($form);

											if (hasProducts) {
												const textHasResults = $viewAllButton.data('text-has-results');
												if (textHasResults) {
													$viewAllButton.text(textHasResults);
												}

												// Remove existing search_keyword and formsearch parameters if exists
												buttonUrl = buttonUrl.replace(/([?&])search_keyword=[^&]*/g, '');
												buttonUrl = buttonUrl.replace(/([?&])formsearch=[^&]*/g, '');
												// Clean up trailing ? or &
												buttonUrl = buttonUrl.replace(/[?&]$/, '');

												// Add search_keyword parameter
												const separator = buttonUrl.indexOf('?') !== -1 ? '&' : '?';
												buttonUrl += separator + 'search_keyword=' + encodeURIComponent(searchTerm);
												// Add formsearch=true at the end only if URL has product_cat or excluded_product_cat
												if (buttonUrl.indexOf('product_cat=') !== -1 || buttonUrl.indexOf('excluded_product_cat=') !== -1) {
													buttonUrl += '&formsearch=true';
												}
											} else {
												const textNoResults = $viewAllButton.data('text-no-results');
												if (textNoResults) {
													$viewAllButton.text(textNoResults);
												}

												// Remove search_keyword parameter
												buttonUrl = buttonUrl.replace(/([?&])search_keyword=[^&]*/g, '');
												buttonUrl = buttonUrl.replace(/[?&]$/, '');
												// Ensure formsearch=true is at the end only if URL has product_cat or excluded_product_cat
												if (buttonUrl.indexOf('product_cat=') !== -1 || buttonUrl.indexOf('excluded_product_cat=') !== -1) {
													buttonUrl = buttonUrl.replace(/([?&])formsearch=[^&]*/g, '');
													buttonUrl = buttonUrl.replace(/[?&]$/, '');
													buttonUrl += (buttonUrl.indexOf('?') !== -1 ? '&' : '?') + 'formsearch=true';
												} else {
													// Remove formsearch if URL doesn't have product_cat or excluded_product_cat
													buttonUrl = buttonUrl.replace(/([?&])formsearch=[^&]*/g, '');
													buttonUrl = buttonUrl.replace(/[?&]$/, '');
												}
											}

											$viewAllButton.attr('href', buttonUrl);
										}
									}

									// Keep focus on input so user can continue typing
									$liveSearch.focus();
								}, 300);
								setTimeout(function () {
									$liveSearch.focus();
								}, 1000);
							}
						},
						error: function () {
							$dataSearch.html('<p>Error loading results</p>');
						}
					});
				}
			};

			// Load on init
			loadProductsDisplay();

			// Search on keyup - only on desktop, mobile will use submit
			$liveSearch.on('keyup', function () {
				const searchValue = $(this).val().trim();
				const windowWidth = $(window).width();
				const isMobile = windowWidth <= 570;

				// On mobile, don't perform search while typing - only on submit
				if (isMobile) {
					if (searchValue.length < 2) {
						$productsDisplay.removeClass('hidden');
						$liveSearchResults.removeClass('active');
					}
					return;
				}

				// Desktop: perform search while typing
				clearTimeout(typingTimer);

				if (searchValue.length >= 2) {
					const typingInterval = getTypingInterval();
					typingTimer = setTimeout(performSearch, typingInterval);
				} else {
					$productsDisplay.removeClass('hidden');
					$liveSearchResults.removeClass('active');
				}
			});

			// Clear search input
			$liveSearch.on('search', function () {
				if ($(this).val() === '') {
					$productsDisplay.removeClass('hidden');
					$liveSearchResults.removeClass('active');
				}
			});

			// Handle category dropdown
			const $categoryDropdown = $searchProduct.find('.bt-category-dropdown');
			const $selectedCategory = $categoryDropdown.find('.bt-selected-category');
			const $categoryList = $categoryDropdown.find('.bt-category-list');
			const $selectedCategorySvg = $selectedCategory.find('svg');

			$selectedCategory.on('click', function (e) {
				e.stopPropagation();
				const isVisible = $categoryList.is(':visible');

				if (isVisible) {
					$categoryList.hide();
					$selectedCategorySvg.css('transform', 'rotate(0deg)');
				} else {
					$categoryList.show();
					$selectedCategorySvg.css('transform', 'rotate(180deg)');
				}
			});

			$categoryList.find('.bt-category-item').on('click', function (e) {
				e.stopPropagation();
				const categoryName = $(this).data('name');
				const categorySlug = $(this).data('slug');
				const categoryUrl = $(this).data('url');

				$selectedCategory.find('span').text(categoryName);
				$catProductInput.val(categorySlug);
				$form.data('category-url', categoryUrl);
				$categoryList.find('.bt-category-item').removeClass('active');
				$(this).addClass('active');
				$categoryList.hide();
				$selectedCategorySvg.css('transform', 'rotate(0deg)');

				if ($liveSearch.val().trim().length >= 2) {
					performSearch();
				}
			});

			$(document).on('click', function (e) {
				if (!$categoryDropdown.is(e.target) && $categoryDropdown.has(e.target).length === 0) {
					$categoryList.hide();
					$selectedCategorySvg.css('transform', 'rotate(0deg)');
				}
			});

			// Form submit handler
			$form.on('submit', function (e) {
				e.preventDefault();
				const $form = $(this);
				const $searchInput = $form.find('input[name="search_keyword"]');
				const searchKeyword = $searchInput.length ? $searchInput.val().trim() : '';
				const shopUrl = $form.attr('action') || '';
				const windowWidth = $(window).width();
				const isMobile = windowWidth <= 570;

				// On mobile, perform search first before redirecting
				if (isMobile && searchKeyword.length >= 2) {
					// Perform search to show results
					performSearch();
					// Don't redirect, let user see the results
					return false;
				}

				// Desktop or empty search: redirect normally
				// Generate correct URL based on widget settings
				let finalUrl = generateCategoryUrl($form);

				// Only add search_keyword to URL if it's not empty
				if (searchKeyword) {
					// Remove existing search_keyword and formsearch parameters if exists
					finalUrl = finalUrl.replace(/([?&])search_keyword=[^&]*/g, '');
					finalUrl = finalUrl.replace(/([?&])formsearch=[^&]*/g, '');
					// Clean up trailing ? or &
					finalUrl = finalUrl.replace(/[?&]$/, '');
					// Add search_keyword parameter
					const separator = finalUrl.indexOf('?') !== -1 ? '&' : '?';
					finalUrl += separator + 'search_keyword=' + encodeURIComponent(searchKeyword);
					// Add formsearch=true at the end only if URL has product_cat or excluded_product_cat
					if (finalUrl.indexOf('product_cat=') !== -1 || finalUrl.indexOf('excluded_product_cat=') !== -1) {
						finalUrl += '&formsearch=true';
					}
				} else {
					// Ensure formsearch=true is at the end only if URL has product_cat or excluded_product_cat
					if (finalUrl.indexOf('product_cat=') !== -1 || finalUrl.indexOf('excluded_product_cat=') !== -1) {
						finalUrl = finalUrl.replace(/([?&])formsearch=[^&]*/g, '');
						finalUrl = finalUrl.replace(/[?&]$/, '');
						finalUrl += (finalUrl.indexOf('?') !== -1 ? '&' : '?') + 'formsearch=true';
					} else {
						// Remove formsearch if URL doesn't have product_cat or excluded_product_cat
						finalUrl = finalUrl.replace(/([?&])formsearch=[^&]*/g, '');
						finalUrl = finalUrl.replace(/[?&]$/, '');
					}
				}

				// Redirect to the correct URL
				window.location.href = finalUrl;
			});

			// Handle trending keyword clicks
			$searchProduct.find('.bt-trending-keyword').on('click', function () {
				const keyword = $(this).data('keyword');
				const windowWidth = $(window).width();
				const isMobile = windowWidth <= 570;

				$liveSearch.val(keyword);

				// On mobile, perform search directly; on desktop, trigger keyup (which will perform search)
				if (isMobile && keyword.length >= 2) {
					performSearch();
				} else {
					$liveSearch.trigger('keyup');
				}
			});

			// Handle window resize to reload AJAX with correct layout
			$(window).on('resize', function () {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(function () {
					const windowWidth = $(window).width();
					const currentIsMobile = windowWidth <= 570;

					// Only reload if mobile state changed
					if (currentIsMobile !== previousIsMobile) {
						previousIsMobile = currentIsMobile;

						const searchTerm = $liveSearch.val().trim();

						// If user is searching, reload search results
						if (searchTerm.length >= 2) {
							performSearch();
						} else {
							// Otherwise reload products display
							loadProductsDisplay();
						}
					}
				}, 250); // Debounce resize events
			});

			// Initialize keyword suggestion if enabled
			KeywordSuggestionHandler($searchProduct, $);
		}
	};

	function SomniaAnimateText(selector, delayFactor = 50) {
		const $text = $(selector);
		const textContent = $text.text();
		$text.empty();

		let letterIndex = 0;

		textContent.split(" ").forEach((word) => {
			const $wordSpan = $("<span>").addClass("bt-word");

			word.split("").forEach((char) => {
				const $charSpan = $("<span>").addClass("bt-letter").text(char);
				$charSpan.css("animation-delay", `${letterIndex * delayFactor}ms`);
				$wordSpan.append($charSpan);
				letterIndex++;
			});

			$text.append($wordSpan).append(" ");
		});
	}
	function headingAnimationHandler($scope) {
		var headingAnimationContainer = $scope.find('.bt-elwg-heading-animation');
		var animationElement = headingAnimationContainer.find('.bt-heading-animation-js');
		var animationClass = headingAnimationContainer.data('animation');
		var animationDelay = headingAnimationContainer.data('delay');

		if (animationClass === 'none') {
			return;
		}
		function checkIfElementInView() {
			const windowHeight = $(window).height();
			const elementOffsetTop = animationElement.offset().top;
			const elementOffsetBottom = elementOffsetTop + animationElement.outerHeight();

			const isElementInView =
				elementOffsetTop < $(window).scrollTop() + windowHeight &&
				elementOffsetBottom > $(window).scrollTop();

			if (isElementInView) {
				if (!animationElement.hasClass('bt-animated')) {
					animationElement
						.addClass('bt-animated')
						.addClass(animationClass);
					SomniaAnimateText(animationElement, animationDelay);
				}
			}
		}
		jQuery(window).on('scroll', function () {
			checkIfElementInView();
		});
		jQuery(document).ready(function () {
			checkIfElementInView();
		});
	}

	/* Helper function to handle cart toast vs cart mini logic */
	function SomniaHandleCartAction(productId) {
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

	function SomniashowToast(idproduct, tools = 'cart', status = 'add') {
		if ($(window).width() > 1024) { // Only run for screens wider than 1024px
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
	const ProductTestimonialHandler = function ($scope) {
		const $ProductTestimonial = $scope.find('.bt-elwg-product-testimonial--default');
		const $testimonialContent = $ProductTestimonial.find('.js-testimonial-content');
		const $testimonialImages = $ProductTestimonial.find('.js-testimonial-images');

		if ($testimonialContent.length > 0 && $testimonialImages.length > 0) {
			const $sliderSettings = $ProductTestimonial.data('slider-settings');
			const sliderSpeed = $sliderSettings.speed || 1000;
			const autoplay = $sliderSettings.autoplay || false;
			const autoplayDelay = $sliderSettings.autoplay_delay || 3000;
			// Initialize the testimonial content slider
			const testimonialContentSwiper = new Swiper($testimonialContent[0], {
				slidesPerView: 1,
				spaceBetween: 10,
				loop: true,
				speed: sliderSpeed,
				autoplay: autoplay ? {
					delay: autoplayDelay,
					disableOnInteraction: false
				} : false,
				navigation: {
					nextEl: $ProductTestimonial.find('.bt-button-next')[0],
					prevEl: $ProductTestimonial.find('.bt-button-prev')[0],
				},
				pagination: {
					el: $ProductTestimonial.find('.bt-swiper-pagination')[0],
					clickable: true,
					type: 'bullets',
					renderBullet: function (index, className) {
						return '<span class="' + className + '"></span>';
					},
				},

			});
			// Initialize the testimonial images slider
			const testimonialImagesSwiper = new Swiper($testimonialImages[0], {
				slidesPerView: 1,
				loop: true,
				speed: sliderSpeed,
				effect: 'fade',
				fadeEffect: {
					crossFade: true
				},
				allowTouchMove: false,
			});

			// Sync both sliders
			testimonialContentSwiper.controller.control = testimonialImagesSwiper;
			testimonialImagesSwiper.controller.control = testimonialContentSwiper;

			// Pause autoplay on hover if autoplay is enabled
			if (autoplay) {
				$testimonialContent[0].addEventListener('mouseenter', () => {
					testimonialContentSwiper.autoplay.stop();
				});

				$testimonialContent[0].addEventListener('mouseleave', () => {
					testimonialContentSwiper.autoplay.start();
				});
			}
		}
	};
	const TestimonialSliderHandler = function ($scope) {
		const $TestimonialSlider = $scope.find('.js-data-testimonial-slider');
		if ($TestimonialSlider.length > 0) {
			const $sliderSettings = $TestimonialSlider.data('slider-settings') || {};
			const swiper = new Swiper($TestimonialSlider.find('.js-testimonial-slider')[0], {
				slidesPerView: $sliderSettings.slidesPerView,
				spaceBetween: $sliderSettings.spaceBetween,
				loop: $sliderSettings.loop,
				speed: $sliderSettings.speed,
				autoplay: $sliderSettings.autoplay ? {
					delay: $sliderSettings.autoplay_delay,
					disableOnInteraction: false
				} : false,
				navigation: {
					nextEl: $scope.find('.bt-button-next')[0],
					prevEl: $scope.find('.bt-button-prev')[0],
				},
				pagination: {
					el: $scope.find('.bt-swiper-pagination')[0],
					clickable: true,
					type: 'bullets',
					renderBullet: function (index, className) {
						return '<span class="' + className + '"></span>';
					},
				},
				breakpoints: $sliderSettings.breakpoints,
			});

			if ($sliderSettings.autoplay) {
				$TestimonialSlider.find('.js-testimonial-slider')[0].addEventListener('mouseenter', () => {
					swiper.autoplay.stop();
				});
				$TestimonialSlider.find('.js-testimonial-slider')[0].addEventListener('mouseleave', () => {
					swiper.autoplay.start();
				});
			}
		}
	};

	const countDownHandler = function ($scope) {
		const countDown = $scope.find('.bt-countdown-js');

		function initCountdown() {
			const countDownDate = new Date(countDown.data('time')).getTime();
			const serverCurrentTime = countDown.data('current-time');

			if (isNaN(countDownDate)) {
				console.error('Invalid countdown date');
				return;
			}

			// Use server current time as baseline and track elapsed time
			const serverInitTime = serverCurrentTime ? new Date(serverCurrentTime).getTime() : new Date().getTime();
			const clientInitTime = Date.now();

			const timer = setInterval(() => {
				// Calculate current server time: initial server time + elapsed time since initialization
				const elapsed = Date.now() - clientInitTime;
				const now = serverInitTime + elapsed;
				const distance = countDownDate - now;

				if (distance < 0) {
					clearInterval(timer);
					// Check if infinity mode is enabled
					const isInfinity = countDown.data('infinity') === 'yes';
					if (isInfinity) {
						// Reload countdown data and restart for infinity mode
						setTimeout(() => {
							initCountdown();
						}, 1000);
					} else {
						// Stop and show expired message for normal countdown
						countDown.html('<div class="bt-countdown-expired">EXPIRED</div>');
					}
					return;
				}

				const days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
				const hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
				const mins = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
				const secs = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');

				countDown.find('.bt-countdown-days').text(days);
				countDown.find('.bt-countdown-hours').text(hours);
				countDown.find('.bt-countdown-mins').text(mins);
				countDown.find('.bt-countdown-secs').text(secs);
			}, 1000);
		}

		initCountdown();
	}
	const NotificationSliderHandler = function ($scope) {
		const $notificationWrapper = $scope.find('.bt-elwg-site-notification--default ');
		const $notificationContent = $notificationWrapper.find('.js-notification-content');

		if ($notificationContent.length > 0) {
			const $sliderSettings = $notificationWrapper.data('slider-settings');
			const sliderSpeed = $sliderSettings.speed || 1000;
			const autoplay = $sliderSettings.autoplay || false;
			const autoplayDelay = $sliderSettings.autoplay_delay || 3000;
			// Initialize the notification content slider
			const notificationContentSwiper = new Swiper($notificationContent[0], {
				slidesPerView: 1,
				loop: true,
				speed: sliderSpeed,
				effect: 'fade',
				fadeEffect: {
					crossFade: true
				},
				autoplay: autoplay ? {
					delay: autoplayDelay,
					disableOnInteraction: false
				} : false,
				navigation: {
					nextEl: $notificationWrapper.find('.bt-site-notification--next')[0],
					prevEl: $notificationWrapper.find('.bt-site-notification--prev')[0],
				},
			});

			// Pause autoplay on hover if autoplay is enabled
			if (autoplay) {
				$notificationContent[0].addEventListener('mouseenter', () => {
					notificationContentSwiper.autoplay.stop();
				});

				$notificationContent[0].addEventListener('mouseleave', () => {
					notificationContentSwiper.autoplay.start();
				});
			}
		}
	};
	// mini cart
	const MiniCartHandler = function ($scope) {
		const $miniCart = $scope.find('.bt-elwg-mini-cart--default');
		const $sidebar = $('.bt-mini-cart-sidebar');

		// Toggle mini cart
		$miniCart.find('.js-cart-sidebar').on('click', function (e) {
			e.preventDefault();
			$sidebar.addClass('active'); // Add active class to sidebar
			const scrollbarWidth = window.innerWidth - $(window).width();
			$('body').css({
				'overflow': 'hidden',
				'padding-right': scrollbarWidth + 'px' // Prevent layout shift
			});
		});

		// Close mini cart when clicking overlay or close button
		$sidebar.find('.bt-mini-cart-sidebar-overlay, .bt-mini-cart-close').on('click', function () {
			closeMiniCart();
		});

		// Close mini cart when pressing ESC key
		$(document).keyup(function (e) {
			if (e.key === "Escape") {
				closeMiniCart();
			}
		});

		// Helper function to close mini cart
		function closeMiniCart() {
			$sidebar.removeClass('active');
			$('body').css({
				'overflow': 'auto', // Restore body scroll
				'padding-right': '0' // Reset padding-right
			});
		}

		// Mini Cart Note and Coupon Popups
		initMiniCartPopups();
	}

	// Initialize mini cart popups
	function initMiniCartPopups() {
		const $sidebar = $('.bt-mini-cart-sidebar');

		// Open popup handlers
		$sidebar.on('click', '.bt-mini-cart-action-btn:not(.bt-mini-cart-note-btn)', function (e) {
			e.preventDefault();
			openPopup($(this).data('action'));
		});

		// Note button handler - load saved note
		$sidebar.on('click', '.bt-mini-cart-note-btn', function (e) {
			e.preventDefault();
			const $popup = openPopup($(this).data('action'));

			// Load saved note from localStorage
			try {
				const savedNote = localStorage.getItem('bt_cart_note');
				if (savedNote) {
					$popup.find('#bt-mini-cart-note-text').val(savedNote);
				}
			} catch (error) {
				console.error('Error loading note:', error);
			}
		});

		// Close popup handlers
		$sidebar.on('click', '.bt-mini-cart-popup-close', function (e) {
			e.preventDefault();
			closePopup($(this).closest('.bt-mini-cart-popup'));
		});

		// Save note to localStorage
		$sidebar.on('click', '.bt-mini-cart-popup-save', function (e) {
			e.preventDefault();
			const $popup = $(this).closest('.bt-mini-cart-note-popup');
			const noteText = $popup.find('#bt-mini-cart-note-text').val();
			const $noteBtn = $sidebar.find('.bt-mini-cart-note-btn');

			// Save note to localStorage
			try {
				if (noteText && noteText.trim() !== '') {
					localStorage.setItem('bt_cart_note', noteText);
					$noteBtn.addClass('have-notes');
				} else {
					localStorage.removeItem('bt_cart_note');
					$noteBtn.removeClass('have-notes');
				}
				closePopup($popup);
				// Trigger cart update to refresh mini cart
				$('body').trigger('wc_fragment_refresh');
			} catch (error) {
				console.error('Error saving note to localStorage:', error);
				alert('Error saving note. Please try again.');
			}
		});

		// Apply coupon handler
		$sidebar.on('click', '.bt-mini-cart-popup-apply', function (e) {
			e.preventDefault();
			const $popup = $(this).closest('.bt-mini-cart-coupon-popup');
			const couponCode = $popup.find('#bt-mini-cart-coupon-code').val().trim();
			const $messages = $popup.find('.bt-mini-cart-coupon-messages');

			if (!couponCode) {
				$messages.html('<div class="woocommerce-error">Please enter a coupon code.</div>');
				return;
			}

			if (typeof wc_cart_params === 'undefined') {
				$messages.html('<div class="woocommerce-error">Cart parameters not loaded. Please refresh the page.</div>');
				return;
			}

			const ajaxUrl = wc_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon');

			$.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					security: wc_cart_params.apply_coupon_nonce,
					coupon_code: couponCode
				},
				dataType: 'html',
				success: function (response) {
					$messages.html(response);

					// If success, clear input and refresh cart
					if (response.indexOf('woocommerce-error') === -1 && response.indexOf('is-error') === -1) {
						$popup.find('#bt-mini-cart-coupon-code').val('');
						$('body').trigger('wc_fragment_refresh');
						setTimeout(() => closePopup($popup), 1000);
					}
				},
				error: function () {
					$messages.html('<div class="woocommerce-error">Error applying coupon. Please try again.</div>');
				}
			});
		});

		// Remove coupon handler
		$sidebar.on('click', '.bt-mini-cart-remove-coupon', function (e) {
			e.preventDefault();
			const couponCode = $(this).data('coupon');

			if (!couponCode || typeof wc_cart_params === 'undefined') {
				alert('Error: Missing coupon code or cart parameters.');
				return;
			}

			const ajaxUrl = wc_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_coupon');

			$.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					security: wc_cart_params.remove_coupon_nonce,
					coupon: couponCode
				},
				dataType: 'html',
				success: function () {
					$('.woocommerce-error, .woocommerce-message, .woocommerce-info').remove();
					$('body').trigger('wc_fragment_refresh');
					$('body').trigger('removed_coupon', [couponCode]);
				},
				error: function () {
					alert('Error removing coupon. Please try again.');
				}
			});
		});

		// Utility functions
		function openPopup(action) {
			// Close any other open popup first
			const $activePopup = $sidebar.find('.bt-mini-cart-popup.active');
			if ($activePopup.length) {
				closePopup($activePopup);
			}

			const $popup = $sidebar.find('.bt-mini-cart-popup[data-popup="' + action + '"]');
			if ($popup.length) {
				$popup.addClass('active');
				$sidebar.addClass('popup-active');
				updateBottomCartPadding();
			}
			return $popup;
		}

		// Close popup function
		function closePopup($popup) {
			$popup.removeClass('active');
			if (!$sidebar.find('.bt-mini-cart-popup.active').length) {
				$sidebar.removeClass('popup-active');
			}
			updateBottomCartPadding();
		}

		// Update padding-bottom based on bt-bottom-mini-cart height
		function updateBottomCartPadding() {
			const $bottomCart = $sidebar.find('.bt-bottom-mini-cart');
			const $sidebarBody = $sidebar.find('.bt-mini-cart-sidebar-body');
			if ($bottomCart.length && $sidebarBody.length) {
				const height = $bottomCart.outerHeight(true); // Include margin
				$sidebarBody.css('--padding-bottom', height + 'px');
			}
		}
		// Function to update note button class based on localStorage
		function updateNoteButtonClass() {
			const $noteBtn = $sidebar.find('.bt-mini-cart-note-btn');
			if (!$noteBtn.length) return; // Button not found, skip

			try {
				const savedNote = localStorage.getItem('bt_cart_note');
				if (savedNote && savedNote.trim() !== '') {
					$noteBtn.addClass('have-notes');
				} else {
					$noteBtn.removeClass('have-notes');
				}
			} catch (error) {
				console.error('Error checking note:', error);
			}
		}

		// Update note button class on init
		updateNoteButtonClass();

		// Update note button class on cart events
		$('body').on('added_to_cart removed_from_cart wc_fragments_refreshed', function () {
			setTimeout(updateNoteButtonClass, 100);
		});

		// Update padding when cart is updated
		$('body').on('wc_fragment_refresh updated_wc_div wc_fragments_refreshed', function () {
			// Use setTimeout to ensure DOM is updated
			setTimeout(function () {
				updateBottomCartPadding();
			}, 100);
		});

		// Initial update after a short delay to ensure DOM is ready
		setTimeout(function () {
			updateBottomCartPadding();
		}, 200);

		// Update on window resize
		$(window).on('resize', function () {
			updateBottomCartPadding();
		});

		// Close popup with ESC key
		$(document).on('keyup', function (e) {
			if (e.key === "Escape" && $sidebar.hasClass('popup-active')) {
				const $activePopup = $sidebar.find('.bt-mini-cart-popup.active');
				if ($activePopup.length) {
					closePopup($activePopup);
				}
			}
		});

		// Close popup when clicking outside
		$sidebar.on('click', '.bt-mini-cart-popup', function (e) {
			if ($(e.target).hasClass('bt-mini-cart-popup')) {
				closePopup($(this));
			}
		});
	}
	const SwitcherHandler = function ($scope, $) {
		const $switcher = $scope.find('.js-switcher-dropdown');
		if ($switcher.length) {
			const $currentItem = $switcher.find('.bt-current-item .bt-current-item-text');
			const $dropdownItems = $switcher.find('.bt-item');
			const $dropdown = $switcher.find('.bt-has-dropdown');

			// Toggle dropdown on click
			$currentItem.parent().on('click', function (e) {
				e.preventDefault();
				$dropdown.toggleClass('active');
			});

			// Handle dropdown item click
			$dropdownItems.on('click', function (e) {
				e.preventDefault();
				const selectedText = $(this).html();

				console.log(selectedText);

				// Remove active class from all items
				$dropdownItems.removeClass('active');

				// Add active class to clicked item
				$(this).addClass('active');

				// Update current item text
				$currentItem.html(selectedText);

				// Close dropdown
				$dropdown.removeClass('active');
			});

			// Close dropdown when clicking outside
			$(document).on('click', function (e) {
				if (!$switcher.is(e.target) && $switcher.has(e.target).length === 0) {
					$dropdown.removeClass('active');
				}
			});
		}
	};

	const InstagramPostsHandler = function ($scope) {
		const $instagramPosts = $scope.find('.bt-elwg-instagram-posts');

		if ($instagramPosts.length > 0 && $instagramPosts.hasClass('bt-elwg-instagram-posts--slider')) {
			const $sliderSettings = $instagramPosts.data('slider-settings');
			const swiperOptions = {
				slidesPerView: $sliderSettings.slidesPerView,
				loop: $sliderSettings.loop,
				spaceBetween: $sliderSettings.spaceBetween,
				speed: $sliderSettings.speed,
				autoplay: $sliderSettings.autoplay ? {
					delay: 3000,
					disableOnInteraction: false
				} : false,
				navigation: {
					nextEl: $instagramPosts.find('.bt-button-next')[0],
					prevEl: $instagramPosts.find('.bt-button-prev')[0],
				},
				pagination: {
					el: $instagramPosts.find('.bt-swiper-pagination')[0],
					clickable: true,
					type: 'bullets',
					renderBullet: function (index, className) {
						return '<span class="' + className + '"></span>';
					},
				},
				breakpoints: $sliderSettings.breakpoints
			};

			const swiper = new Swiper($instagramPosts.find('.swiper')[0], swiperOptions);

			if ($sliderSettings.autoplay) {
				$instagramPosts.find('.swiper')[0].addEventListener('mouseenter', () => {
					swiper.autoplay.stop();
				});

				$instagramPosts.find('.swiper')[0].addEventListener('mouseleave', () => {
					swiper.autoplay.start();
				});
			}
		}
	};

	const VideoAutoPlayHoverHandler = function ($scope) {
		const $item = $scope.find('.bt-video-hover-enabled');
		const $video = $scope.find('.bt-hover-video');
		const $coverImage = $scope.find('.bt-cover-image img');

		if ($video.length) {
			$item.on('mouseenter', function () {
				$coverImage.css('opacity', '0');
				$video.css('opacity', '1');
				$video[0].play();
			});

			$item.on('mouseleave', function () {
				$coverImage.css('opacity', '1');
				$video.css('opacity', '0');
				$video[0].pause();
			});

			$item.on('click', function () {
				if ($video[0].paused) {
					$video[0].play();
				} else {
					$video[0].pause();
				}
			});
		}
	};

	const BannerProductSliderHandler = function ($scope) {
		const $bannerProductSlider = $scope.find('.bt-elwg-banner-product-slider');

		if ($bannerProductSlider.length > 0) {
			const $sliderSettings = $bannerProductSlider.data('slider-settings');
			const swiperOptions = {
				slidesPerView: $sliderSettings.slidesPerView,
				loop: $sliderSettings.loop,
				spaceBetween: $sliderSettings.spaceBetween,
				speed: $sliderSettings.speed,
				autoplay: $sliderSettings.autoplay ? {
					delay: $sliderSettings.autoplay_delay,
					disableOnInteraction: false
				} : false,
				navigation: {
					nextEl: $bannerProductSlider.find('.bt-button-next')[0],
					prevEl: $bannerProductSlider.find('.bt-button-prev')[0],
				},
				pagination: {
					el: $bannerProductSlider.find('.bt-swiper-pagination')[0],
					clickable: true,
					type: 'bullets',
					renderBullet: function (index, className) {
						return '<span class="' + className + '"></span>';
					},
				},
				breakpoints: $sliderSettings.breakpoints
			};

			const swiper = new Swiper($bannerProductSlider.find('.swiper')[0], swiperOptions);

			if ($sliderSettings.autoplay) {
				$bannerProductSlider.find('.swiper')[0].addEventListener('mouseenter', () => {
					swiper.autoplay.stop();
				});

				$bannerProductSlider.find('.swiper')[0].addEventListener('mouseleave', () => {
					swiper.autoplay.start();
				});
			}
			// video hover
			$bannerProductSlider.find('.bt-banner-product-slider--item').each(function () {
				const $item = $(this);
				const $video = $item.find('.bt-hover-video');
				const $coverImage = $item.find('.bt-cover-image img');

				if ($item.hasClass('bt-video-hover-enable') && $video.length) {
					$item.on('mouseenter', function () {
						$coverImage.css('opacity', '0');
						$video[0].play();
					});

					$item.on('mouseleave', function () {
						$coverImage.css('opacity', '1');
						$video[0].pause();
					});

					$item.on('click', function () {
						if ($video[0].paused) {
							$video[0].play();
						} else {
							$video[0].pause();
						}
					});
				}
			});
		}
	};

	// product showcase
	const ProductShowcaseHandler = function ($scope) {
		const $productShowcase = $scope.find('.js-product-showcase');
		if ($productShowcase.length > 0) {
			$productShowcase.find('.js-check-bg-color').each(function () {
				let $el = $(this);
				let bg = $el.css("background-color");
				let rgb = bg.match(/\d+/g);
				if (!rgb) return;

				let r = parseInt(rgb[0]),
					g = parseInt(rgb[1]),
					b = parseInt(rgb[2]);

				let yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;

				$el.removeClass("bt-bg-light bt-bg-dark")
					.addClass(yiq >= 128 ? "bt-bg-light" : "bt-bg-dark");
			});
		}
	}
	const ProductShowcaseStyle2Handler = function ($scope) {
		// Initialize Gallery Slider for Layout 01
		const $layoutWidget = $scope.find('.bt-layout-layout-01');
		if ($layoutWidget.length > 0) {
			const $gallerySlider = $layoutWidget.find('.woocommerce-product-gallery__slider');
			if ($gallerySlider.length > 0) {
				// Determine thumb direction based on layout class
				var thumbDirection = 'horizontal';
				if ($layoutWidget.find('.bt-left-thumbnail').length > 0 || $layoutWidget.find('.bt-right-thumbnail').length > 0) {
					thumbDirection = 'vertical';
				}

				// Initialize thumbnail slider
				var galleryThumbs = new Swiper($layoutWidget.find('.woocommerce-product-gallery__slider-thumbs')[0], {
					direction: thumbDirection,
					spaceBetween: 12,
					autoHeight: true,
					loop: false,
					freeMode: true,
					loopedSlides: 5,
					watchSlidesVisibility: true,
					watchSlidesProgress: true,
					threshold: 5,
					breakpoints: {
						0: {
							slidesPerView: 'vertical' == thumbDirection ? 'auto' : 4,
						},
						480: {
							slidesPerView: 'vertical' == thumbDirection ? 'auto' : 5,
						},
						768: {
							slidesPerView: 'vertical' == thumbDirection ? 'auto' : 4,
						},
						1024: {
							slidesPerView: 'vertical' == thumbDirection ? 'auto' : 5,
						}
					}
				});

				// Initialize main gallery slider
				var galleryTop = new Swiper($gallerySlider[0], {
					spaceBetween: 20,
					loop: false,
					loopedSlides: 5,
					navigation: {
						nextEl: $layoutWidget.find('.swiper-button-next')[0],
						prevEl: $layoutWidget.find('.swiper-button-prev')[0],
					},
					thumbs: {
						swiper: galleryThumbs,
					},
				});
			}
		}
		const $progressBar = $layoutWidget.find('.bt-progress-bar-sold');
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
	function SomniaProductHotspotAddSetCart($container) {
		const $variationForm = $container.find('.variations_form');
		const $productItems = $container.find('.bt-hotspot-product-list__item');
		if ($variationForm.length > 0) {
			$variationForm.find('.bt-attributes--item').each(function () {
				$(this).closest('.variations_form').off('show_variation.somnialoaditem').on('show_variation.somnialoaditem', function (event, variation) {
					var variationId = variation.variation_id;
					if (variationId && variationId !== '0') {
						var $ItemProduct = $(this).closest('.bt-hotspot-product-list__item');
						var $form = $(this).closest('.variations_form');
						var variations = $form.data('product_variations');
						if (variations) {
							// Find matching variation by ID
							var variation = variations.find(function (v) {
								return v.variation_id === variationId;
							});

							if (variation && variation.price_html) {
								// Update price display
								if ($ItemProduct.find(".bt-price").length > 0) {
									$ItemProduct.find(".bt-price").html(variation.price_html);
								} else {
									$ItemProduct.find(".woocommerce-loop-product__infor .price").html(variation.price_html);
								}
							}
						}
					}
				});
			});
		}
		// Function to update price product
		function updateHotspotProductPrice(productItem, variationId, $container) {

			const $productItem = productItem;
			const $productId = $productItem.data('product-id');
			const $product_currencySymbol = $productItem.data('product-currency');
			const $product_html_price_default = $productItem.attr('data-product-html-price-default');
			if ($product_html_price_default) {
				$productItem.find(".woocommerce-loop-product__infor .price").html($product_html_price_default);
			}
			const $variationForm = $productItem.find('.variations_form');
			if (typeof variationId === 'undefined' || !variationId || typeof variationId === 'object') {
				variationId = parseInt($variationForm.find('input.variation_id').val(), 10) || 0;
			}
			const $addSetToCartBtn = $container.find('.bt-button-add-set-to-cart');
			if ($addSetToCartBtn.length) {
				let idsData = $addSetToCartBtn.attr('data-ids');
				let idsArr = [];
				try {
					idsArr = JSON.parse(idsData);
				} catch (e) {
					console.error('Invalid data-ids JSON', e);
				}
				let updated = false;
				let totalPrice = 0;
				let hasInvalidVariation = false;
				let filteredIdsArr = [];

				idsArr.forEach(item => {
					// Update variation_id for current product being changed
					if (item.product_id == $productId) {
						if (item.variation_id != variationId && variationId !== 0) {
							item.variation_id = variationId;
							updated = true;
						}
					}

					// Get product item element
					const $productItemId = $container.find(`.bt-hotspot-product-list__item[data-product-id="${item.product_id}"]`);

					// Check product stock status
					let isInStock = true;

					// Check if this is a variable product
					const $form = $container.find(`.variations_form[data-product_id="${item.product_id}"]`);

					if ($form.length) {
						// Variable product - check variation stock
						if (!item.variation_id || item.variation_id === 0 || item.variation_id === null) {
							// No variation selected yet
							hasInvalidVariation = true;
							isInStock = false;
							//	console.log('Product', item.product_id, '- No variation selected');
						} else {
							// Check variation stock from variation data
							const variations = $form.data('product_variations');
							if (variations) {
								// Convert to number for comparison
								const currentVariationId = parseInt(item.variation_id, 10);
								const variation = variations.find(v => parseInt(v.variation_id, 10) === currentVariationId);
								if (variation) {
									isInStock = variation.is_in_stock ? true : false;
								} else {
									isInStock = false;
								}
							}
						}
					} else {
						// Simple product - check data-in-stock attribute
						if ($productItemId.length) {
							const inStockAttr = $productItemId.attr('data-in-stock');
							if (inStockAttr === '0' || inStockAttr === 0) {
								isInStock = false;
							}
						}
					}

					// Only include in-stock products in calculations and data-ids
					if (isInStock) {
						// Get price for each product
						if ($form.length) {
							// Variable product - get price from variation
							const variations = $form.data('product_variations');
							if (variations) {
								const currentVariationId = parseInt(item.variation_id, 10);
								const variation = variations.find(v => parseInt(v.variation_id, 10) === currentVariationId);
								if (variation && variation.display_price) {
									totalPrice += parseFloat(variation.display_price);
								}
							}
						} else {
							// Simple product - get price from data attribute
							if ($productItemId.length) {
								const simplePrice = $productItemId.attr('data-product-default-price');
								if (simplePrice) {
									totalPrice += parseFloat(simplePrice);
								}
							}
						}
						// Add to filtered array only if in stock
						filteredIdsArr.push(item);
					}
				});

				// Update button state based on variations
				if (hasInvalidVariation) {
					$addSetToCartBtn.addClass('disabled');
				} else {
					$addSetToCartBtn.removeClass('disabled');
				}

				// Always update data-ids to reflect current stock status
				$addSetToCartBtn.attr('data-ids', JSON.stringify(idsArr));

				// update total price
				totalPrice = totalPrice.toFixed(2);
				$addSetToCartBtn.find('.bt-btn-price').html(' - ' + $product_currencySymbol + totalPrice);
			}
		}
		window.updateHotspotProductPrice = updateHotspotProductPrice;


		// Initial update on load
		$productItems.each(function () {
			updateHotspotProductPrice($(this), null, $container);
		});
		// Update on variation change
		$variationForm.find('select').on('change', function () {
			var $form = $(this).closest('.variations_form')
			$form.off('show_variation.somniachangeitem').on('show_variation.somniachangeitem', function (event, variation) {
				var variationId = variation.variation_id;
				if (variationId && variationId !== '0') {
					var $ItemProduct = $form.closest('.bt-hotspot-product-list__item');
					if (!variation.is_in_stock) {
						$ItemProduct.addClass('out-of-stock');
						$ItemProduct.attr('data-in-stock', '0');
					} else {
						$ItemProduct.removeClass('out-of-stock');
						$ItemProduct.attr('data-in-stock', '1');
					}
					updateHotspotProductPrice($ItemProduct, variationId, $container);
					var variations = $form.data('product_variations');
					if (variations) {
						var variation = variations.find(function (v) {
							return v.variation_id === variationId;
						});
						if (variation && variation.price_html) {
							// Update price display
							if ($ItemProduct.find(".bt-price").length > 0) {
								$ItemProduct.find(".bt-price").html(variation.price_html);
							} else {
								$ItemProduct.find(".woocommerce-loop-product__infor .price").html(variation.price_html);
							}
						}
					}
				}

			});
		});
		/* Function to update price product in quickview */
		$productItems.on('click', '.bt-loop-add-to-cart-btn', function (e) {
			e.preventDefault();
			// Find the nearest .elementor-widget-bt-product-tooltip-hotspot and get data-id
			var widgetId = $(this).closest('.elementor-widget-bt-product-tooltip-hotspot').data('id');
			var defaultAttributesData = $(this).closest('.bt-hotspot-product-list__item').attr('data-product-default-attributes');

			// Store data for use when quick view is loaded
			if (widgetId && defaultAttributesData) {
				window.pendingQuickViewData = {
					widgetId: widgetId,
					defaultAttributesData: defaultAttributesData
				};
			}
		});

		// Listen for quick view loaded event
		$(document).on('somniaQuickViewLoaded', function () {
			if (window.pendingQuickViewData) {
				var widgetId = window.pendingQuickViewData.widgetId;
				var defaultAttributesData = window.pendingQuickViewData.defaultAttributesData;
				var $quickviewWrap = $('.bt-popup-quick-view .bt-quick-view-load');

				if ($quickviewWrap.length && widgetId) {
					var $productContainer = $quickviewWrap.find('.product');
					if ($productContainer.length) {
						$productContainer.attr('data-widget-id', widgetId);
						if (defaultAttributesData) {
							try {
								var attributes = JSON.parse(defaultAttributesData);

								// Clean attributes by removing 'attribute_' prefix if present
								var cleanAttributes = {};
								$.each(attributes, function (attributeName, attributeValue) {
									var cleanName = attributeName.replace(/^attribute_/, '');
									cleanAttributes[cleanName] = attributeValue;
								});

								var $attributesWrap = $productContainer.find('.bt-attributes-wrap');
								var tableAttributes = $productContainer.find('table.variations');
								if ($attributesWrap.length && typeof cleanAttributes === 'object' && cleanAttributes !== null) {
									// Loop through each attribute to re-add active class for the corresponding option
									$.each(cleanAttributes, function (attributeName, attributeValue) {
										var $group = $attributesWrap.find('[data-attribute-name="' + attributeName + '"]');
										$group.find('.bt-js-item').removeClass('active');
										var $optionBtn = $group.find('[data-value="' + attributeValue + '"]');
										if ($optionBtn.length) {
											$optionBtn.addClass('active').attr('aria-checked', 'true');
										}
										// WooCommerce uses select[name^="attribute_"]
										var $select = tableAttributes.find('select[name="attribute_' + attributeName + '"]');
										if ($select.length) {
											$select.val(attributeValue).trigger('change'); // set value and trigger events
										}
									});
								}
							} catch (e) {
								console.error('Cannot parse data-product-default-attributes', e);
							}
						}
					}
				}

				// Clear pending data after processing
				window.pendingQuickViewData = null;
			}
		});
		/* ajax add to cart */
		$container.find('.bt-button-add-set-to-cart').on('click', function (e) {
			e.preventDefault();
			const $this = $(this);
			if ($this.hasClass('disabled')) {
				return;
			}
			if ($this.hasClass('bt-view-cart')) {
				window.location.href = AJ_Options.cart;
				return;
			}
			let productIds = $this.data('ids');
			// Ensure productIds is an array of objects (for variable products)
			if (typeof productIds === 'string') {
				try {
					productIds = JSON.parse(productIds);
				} catch (e) {
					console.error('Invalid data-ids JSON', e);
					productIds = [];
				}
			}
			if (!Array.isArray(productIds)) {
				productIds = [];
			}

			// Filter out out-of-stock products before adding to cart
			productIds = productIds.filter(item => {
				const $productItem = $container.find(`.bt-hotspot-product-list__item[data-product-id="${item.product_id}"]`);
				if ($productItem.length) {
					// Check if product has out-of-stock class
					if ($productItem.hasClass('out-of-stock')) {
						return false;
					}
					// Check data-in-stock attribute
					const inStockAttr = $productItem.data('in-stock');
					if (inStockAttr === 0 || inStockAttr === '0') {
						return false;
					}
				}
				return true;
			});

			if (productIds.length > 0) {
				$.ajax({
					type: 'POST',
					url: AJ_Options.ajax_url,
					data: {
						action: 'somnia_add_multiple_to_cart_variable',
						product_ids: productIds
					},
					beforeSend: function () {
						$this.addClass('loading');
					},
					success: function (response) {
						$this.removeClass('loading');
						if (response.success) {
							// Update cart count and trigger cart refresh
							$(document.body).trigger('updated_wc_div');
							SomniaFreeShippingMessage();
							$this.html('View Cart');
							$this.addClass('bt-view-cart');
							// Handle cart action for each product (with delay)
							productIds.forEach((item, idx) => {
								const productId = item.variation_id && item.variation_id !== 0 ? item.variation_id : item.product_id;
								setTimeout(() => {
									SomniaHandleCartAction(productId);
								}, idx * 300);
							});
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						$this.removeClass('loading');
						console.log('Error adding products to cart:', textStatus, errorThrown);
					}
				});
			}
		});
	}

	const ProductTooltipHotspotHandler = function ($scope) {
		const $HotspotProduct = $scope.find('.bt-elwg-product-tooltip-hotspot--default');
		if ($HotspotProduct.length > 0) {
			function getPositionPoint($point) {
				const pointLeft = $point.position().left;
				const pointTop = $point.position().top;
				const pointRight = $point.parent().width() - (pointLeft + $point.outerWidth());
				const pointBottom = $point.parent().height() - (pointTop + $point.outerHeight());
				const $info = $point.find('.bt-hotspot-product-info');
				const infoWidth = $info.outerWidth();
				const halfWidth = infoWidth / 2 - (window.innerWidth <= 600 ? 6 : 10);
				const infoHeight = $info.outerHeight();
				const halfHeight = infoHeight / 2 - (window.innerWidth <= 600 ? 6 : 10);
				const maxPoint = Math.max(pointLeft, pointTop, pointRight, pointBottom);
				$HotspotProduct.toggleClass('bt-hotspot-product-mobile', $point.parent().width() < 600);
				if (maxPoint === pointRight) {
					if (infoWidth > pointRight) {
						const maxHigh = Math.max(pointTop, pointBottom);
						if (maxHigh === pointTop) {
							return 'topcenter';
						} else {
							return 'bottomcenter';
						}
					} else {
						if (halfHeight < pointTop && halfHeight < pointBottom) {
							return 'rightcenter';
						} else if (infoHeight < pointTop) {
							return 'righttop';
						} else {
							return 'rightbottom';
						}
					}
				} else if (maxPoint === pointLeft) {
					if (infoWidth > pointLeft) {
						const maxHigh = Math.max(pointTop, pointBottom);
						if (maxHigh === pointTop) {
							return 'topcenter';
						} else {
							return 'bottomcenter';
						}
					} else {
						if (halfHeight < pointTop && halfHeight < pointBottom) {
							return 'leftcenter';
						} else if (infoHeight < pointTop) {
							return 'lefttop';
						} else {
							return 'leftbottom';
						}
					}
				} else if (maxPoint === pointTop) {
					if (halfWidth < pointLeft && halfWidth < pointRight) {
						return 'topcenter';
					} else if (infoWidth < pointLeft) {
						return 'topleft';
					} else {
						return 'topright';
					}
				} else if (maxPoint === pointBottom) {
					if (halfWidth < pointLeft && halfWidth < pointRight) {
						return 'bottomcenter';
					} else if (infoWidth < pointLeft) {
						return 'bottomleft';
					} else {
						return 'bottomright';
					}
				}
			}
			function hotspotPoint() {
				$HotspotProduct.find('.bt-hotspot-point').each(function () {
					const $point = $(this);
					const $positionPoin = getPositionPoint($point);
					const $info = $point.find('.bt-hotspot-product-info');
					const containerWidth = $point.parent().width();
					let smallOffset = 5;
					let largeOffset = 15;
					if (containerWidth < 700) {
						smallOffset = 2;
						largeOffset = 8;
					}
					if ($positionPoin == 'rightcenter') {
						$info.css({
							'inset': 'auto auto auto 100%',
							'transform': `translateX(${largeOffset}px)`
						});
					} else if ($positionPoin == 'righttop') {
						$info.css({
							'inset': 'auto auto 100% 100%',
							'transform': `translate(0, -${smallOffset}px)`
						});
					} else if ($positionPoin == 'rightbottom') {
						$info.css({
							'inset': '100% auto auto 100%',
							'transform': `translate(0, ${smallOffset}px)`
						});
					} else if ($positionPoin == 'leftcenter') {
						$info.css({
							'inset': 'auto 100% auto auto',
							'transform': `translateX(-${largeOffset}px)`
						});
					} else if ($positionPoin == 'lefttop') {
						$info.css({
							'inset': 'auto 100% 100% auto',
							'transform': `translate(0, -${smallOffset}px)`
						});
					} else if ($positionPoin == 'leftbottom') {
						$info.css({
							'inset': '100% 100% auto auto',
							'transform': `translate(0, ${smallOffset}px)`
						});
					} else if ($positionPoin == 'topcenter') {
						$info.css({
							'inset': 'auto auto 100% auto',
							'transform': `translateY(-${largeOffset}px)`
						});
					} else if ($positionPoin == 'topleft') {
						$info.css({
							'inset': 'auto 100% 100% auto',
							'transform': `translate(0, -${smallOffset}px)`
						});
					} else if ($positionPoin == 'topright') {
						$info.css({
							'inset': 'auto auto 100% 100%',
							'transform': `translate(0, -${smallOffset}px)`
						});
					} else if ($positionPoin == 'bottomcenter') {
						$info.css({
							'inset': '100% auto auto auto',
							'transform': `translateY(${largeOffset}px)`
						});
					} else if ($positionPoin == 'bottomleft') {
						$info.css({
							'inset': '100% 100% auto auto',
							'transform': `translate(0, ${smallOffset}px)`
						});
					} else if ($positionPoin == 'bottomright') {
						$info.css({
							'inset': '100% auto auto 100%',
							'transform': `translate(0, ${smallOffset}px)`
						});
					}
				});
			}
			hotspotPoint();
			$(window).on('resize', function () {
				hotspotPoint();
			});
		}
		// slider hotspot
		const $Hotspotslider = $HotspotProduct.find('.bt-hotspot-slider');

		if ($Hotspotslider.length > 0) {
			const $sliderSettings = $Hotspotslider.data('slider-settings');
			const $Hotspotwrap = $Hotspotslider.find('.bt-hotspot-slider--inner');
			const $swiper = new Swiper($Hotspotwrap[0], {
				slidesPerView: 1,
				loop: false,
				spaceBetween: $sliderSettings.spaceBetween.mobile,
				speed: $sliderSettings.speed,
				pagination: {
					el: $Hotspotslider.find('.bt-swiper-pagination')[0],
					clickable: true,
					type: 'bullets',
					renderBullet: function (index, className) {
						return '<span class="' + className + '"></span>';
					},
				},
				autoplay: $sliderSettings.autoplay ? {
					delay: 3000,
					disableOnInteraction: false
				} : false,
				breakpoints: {
					1581: {
						slidesPerView: 3,
						spaceBetween: $sliderSettings.spaceBetween.desktop
					},
					1200: {
						slidesPerView: 2,
						spaceBetween: $sliderSettings.spaceBetween.desktop
					},
					1025: {
						slidesPerView: 1,
						spaceBetween: $sliderSettings.spaceBetween.desktop
					},
					767: {
						slidesPerView: 3,
						spaceBetween: $sliderSettings.spaceBetween.tablet
					},
					500: {
						slidesPerView: 2,
						spaceBetween: $sliderSettings.spaceBetween.tablet
					},
				},
			});

			if ($sliderSettings.autoplay) {
				$Hotspotwrap[0].addEventListener('mouseenter', () => {
					$swiper.autoplay.stop();
				});
				$Hotspotwrap[0].addEventListener('mouseleave', () => {
					$swiper.autoplay.start();
				});
			}
		}
		SomniaProductHotspotAddSetCart($HotspotProduct);

	};
	const ProductHotspotOverlayHandler = function ($scope) {
		const $productHotspotOverlay = $scope.find('.bt-elwg-product-overlay-hotspot--default');

		if ($productHotspotOverlay.length > 0) {
			const $hotspotPoints = $productHotspotOverlay.find('.bt-hotspot-point');
			const $productItems = $productHotspotOverlay.find('.bt-product-item-minimal');

			// Handle hotspot point clicks
			$hotspotPoints.on('click', function (e) {
				e.preventDefault();
				const $this = $(this);
				const productId = $this.data('product-id');

				// Remove active state from all points and products
				$hotspotPoints.removeClass('active');
				$productItems.removeClass('active');

				// Add active state to clicked point
				$this.addClass('active');

				// Show corresponding product
				$productItems.filter(`[data-product-id="${productId}"]`).addClass('active');
			});
		}
	};

	const BrandSliderHandler = function ($scope) {
		const $brandSlider = $scope.find('.bt-elwg-brand-slider--default');

		if ($brandSlider.length > 0 && $brandSlider.hasClass('bt-elwg-brand-slider--slider')) {
			const $sliderSettings = $brandSlider.data('slider-settings');
			const $sliderContinuous = $brandSlider.data('slider-continuous');
			if ($sliderContinuous) {
				$sliderSettings.speed = $sliderContinuous.speed;
				$sliderSettings.direction = $sliderContinuous.direction;
				var $swiper = new Swiper($brandSlider.find('.swiper')[0], {
					slidesPerView: 'auto',
					loop: true,
					spaceBetween: 0,
					speed: $sliderContinuous.speed,
					freeMode: true,
					allowTouchMove: true,
					autoplay:
					{
						delay: 0,
						reverseDirection: $sliderContinuous.direction == 'rtl' ? true : false,
						disableOnInteraction: false,
					}
				});
			} else {
				const swiperOptions = {
					slidesPerView: $sliderSettings.slidesPerView,
					loop: $sliderSettings.loop,
					spaceBetween: $sliderSettings.spaceBetween,
					speed: $sliderSettings.speed,
					autoplay: $sliderSettings.autoplay ? {
						delay: 3000,
						disableOnInteraction: false
					} : false,
					navigation: {
						nextEl: $brandSlider.find('.bt-button-next')[0],
						prevEl: $brandSlider.find('.bt-button-prev')[0],
					},
					pagination: {
						el: $brandSlider.find('.bt-swiper-pagination')[0],
						clickable: true,
						type: 'bullets',
						renderBullet: function (index, className) {
							return '<span class="' + className + '"></span>';
						},
					},
					breakpoints: $sliderSettings.breakpoints
				};

				const swiper = new Swiper($brandSlider.find('.swiper')[0], swiperOptions);

				if ($sliderSettings.autoplay) {
					$brandSlider.find('.swiper')[0].addEventListener('mouseenter', () => {
						swiper.autoplay.stop();
					});

					$brandSlider.find('.swiper')[0].addEventListener('mouseleave', () => {
						swiper.autoplay.start();
					});
				}
			}
		}
	};
	const VerticalBannerSliderHandler = function ($scope) {
		const $verticalBannerSlider = $scope.find('.bt-vertical-banner-slider');
		if ($verticalBannerSlider.length > 0) {
			const $backgrounds = $verticalBannerSlider.find('.bt-banner-background');
			const $headings = $verticalBannerSlider.find('.bt-banner-heading');
			const $autoplay = $verticalBannerSlider.data('autoplay');
			const $autoplaySpeed = $verticalBannerSlider.data('autoplay-speed');
			const $autoplayOnlyMobile = $verticalBannerSlider.data('autoplay-only-mobile');
			let currentActive = 0;

			function setActiveItem(index) {
				// Remove active class from all backgrounds and headings
				$backgrounds.removeClass('active');
				$headings.removeClass('active');

				// Add active class to target items
				$backgrounds.eq(index).addClass('active');
				$headings.eq(index).addClass('active');
				currentActive = index;
			}

			// Hover event on headings to change active banner
			$headings.on('mouseenter', function () {
				const index = $(this).index();
				setActiveItem(index);
			});

			// Set first item as active by default
			setActiveItem(0);
			// Auto rotate for mobile screens
			function autoRotateBanners() {
				const totalBanners = $backgrounds.length;
				setActiveItem((currentActive + 1) % totalBanners);
			}

			// Check screen width and start/stop auto rotation 
			function handleAutoRotation() {
				// Clear any existing interval
				const existingInterval = $verticalBannerSlider.data('autoRotateInterval');
				if (existingInterval) {
					clearInterval(existingInterval);
					$verticalBannerSlider.data('autoRotateInterval', null);
				}

				// Only run autoplay if enabled
				if ($autoplay === 'yes') {
					const isMobile = window.innerWidth <= 767;
					const shouldAutoplay = $autoplayOnlyMobile === 'yes' ? isMobile : true;

					if (shouldAutoplay) {
						// Start auto rotation with configured speed
						const autoRotateInterval = setInterval(autoRotateBanners, $autoplaySpeed);
						$verticalBannerSlider.data('autoRotateInterval', autoRotateInterval);

						// Stop autoplay on heading hover
						$headings.off('mouseenter.autoplay').on('mouseenter.autoplay', function () {
							const currentInterval = $verticalBannerSlider.data('autoRotateInterval');
							if (currentInterval) {
								clearInterval(currentInterval);
								$verticalBannerSlider.data('autoRotateInterval', null);
							}
						});

						// Resume autoplay when mouse leaves headings
						$headings.off('mouseleave.autoplay').on('mouseleave.autoplay', function () {
							const newInterval = setInterval(autoRotateBanners, $autoplaySpeed);
							$verticalBannerSlider.data('autoRotateInterval', newInterval);
						});
					}
				}
			}

			// Initial check
			handleAutoRotation();

			// Check on resize
			$(window).off('resize.bannerSlider').on('resize.bannerSlider', handleAutoRotation);
		}
	}
	const OrderTrackingHandler = function ($scope, $) {
		const $form = $scope.find('.bt-order-tracking-form');
		const $message = $scope.find('.bt-order-tracking-message');
		const $result = $scope.find('.bt-order-tracking-result');

		if ($form.length) {
			$form.on('submit', function (e) {
				e.preventDefault();

				const orderId = $form.find('input[name="order_id"]').val();
				const billingEmail = $form.find('input[name="billing_email"]').val();
				const $button = $form.find('button[type="submit"]');

				// Reset messages
				$message.html('').removeClass('error success');
				$result.hide();

				// Disable button
				$button.prop('disabled', true).text($button.data('loading-text') || 'Loading...');

				// AJAX request
				$.ajax({
					url: AJ_Options.ajax_url,
					type: 'POST',
					data: {
						action: 'somnia_track_order',
						order_id: orderId,
						billing_email: billingEmail,
						nonce: AJ_Options.order_tracking_nonce
					},
					success: function (response) {
						$button.prop('disabled', false).text($button.data('original-text') || 'Track');

						if (response.success) {
							$message.html(response.data.message).addClass('success');
							if (response.data.html) {
								$result.html(response.data.html).slideDown(400, function () {
									// Smooth scroll to result
									$('html, body').animate({
										scrollTop: $result.offset().top - 100
									}, 600);
								});

								// Initialize tabs
								initOrderTrackingTabs($result);
							}
						} else {
							$message.html(response.data.message).addClass('error');
						}
					},
					error: function () {
						$button.prop('disabled', false).text($button.data('original-text') || 'Track');
						$message.html('An error occurred. Please try again.').addClass('error');
					}
				});
			});

			// Store original button text
			const $button = $form.find('button[type="submit"]');
			$button.data('original-text', $button.text());
			$button.data('loading-text', 'Loading...');
		}

		// Initialize tabs functionality
		function initOrderTrackingTabs($container) {
			const $tabBtns = $container.find('.bt-tab-btn');
			const $tabContents = $container.find('.bt-tab-content');

			$tabBtns.on('click', function () {
				const targetTab = $(this).data('tab');

				// Update buttons
				$tabBtns.removeClass('active');
				$(this).addClass('active');

				// Update content
				$tabContents.removeClass('active');
				$container.find('#' + targetTab + '-tab').addClass('active');
			});
		}
	};

	const ProductTestimonialSliderHandler = function ($scope) {
		const $ProductTestimonialSlider = $scope.find('.js-data-product-testimonial-slider');
		if ($ProductTestimonialSlider.length > 0) {
			const $sliderSettings = $ProductTestimonialSlider.data('slider-settings') || {};
			// Initialize the testimonial slider
			const testimonialSlider = new Swiper($ProductTestimonialSlider.find('.js-testimonial-slider')[0], {
				slidesPerView: $sliderSettings.slidesPerView,
				spaceBetween: $sliderSettings.spaceBetween,
				loop: $sliderSettings.loop,
				speed: $sliderSettings.speed,
				autoplay: $sliderSettings.autoplay ? {
					delay: $sliderSettings.autoplay_delay,
					disableOnInteraction: false
				} : false,
				navigation: {
					nextEl: $ProductTestimonialSlider.find('.bt-button-next')[0],
					prevEl: $ProductTestimonialSlider.find('.bt-button-prev')[0],
				},
				pagination: {
					el: $ProductTestimonialSlider.find('.bt-swiper-pagination')[0],
					clickable: true,
					type: 'bullets',
					renderBullet: function (index, className) {
						return '<span class="' + className + '"></span>';
					},
				},
				breakpoints: $sliderSettings.breakpoints,
			});

			// Pause autoplay on hover if autoplay is enabled
			if ($sliderSettings.autoplay) {
				$ProductTestimonialSlider.find('.js-testimonial-slider')[0].addEventListener('mouseenter', () => {
					testimonialSlider.autoplay.stop();
				});

				$ProductTestimonialSlider.find('.js-testimonial-slider')[0].addEventListener('mouseleave', () => {
					testimonialSlider.autoplay.start();
				});
			}
		}
	};

	const ProductPopupHotspotHandler = function ($scope) {
		const $HotspotProduct = $scope.find('.bt-elwg-product-popup-hotspot--default');

		if ($HotspotProduct.length === 0) {
			return;
		}

		// Check if mobile tooltip style is manually enabled
		const isMobileStyleEnabled = $HotspotProduct.hasClass('bt-hotspot-product-mobile');

		// Position hotspot tooltips
		function getPositionPoint($point) {
			const pointLeft = $point.position().left;
			const pointTop = $point.position().top;
			const pointRight = $point.parent().width() - (pointLeft + $point.outerWidth());
			const pointBottom = $point.parent().height() - (pointTop + $point.outerHeight());
			const $info = $point.find('.bt-hotspot-product-info');
			const infoWidth = $info.outerWidth();
			const halfWidth = infoWidth / 2 - (window.innerWidth <= 600 ? 6 : 10);
			const infoHeight = $info.outerHeight();
			const halfHeight = infoHeight / 2 - (window.innerWidth <= 600 ? 6 : 10);
			const maxPoint = Math.max(pointLeft, pointTop, pointRight, pointBottom);

			// Only toggle mobile class automatically if it's not manually enabled
			if (!isMobileStyleEnabled) {
				$HotspotProduct.toggleClass('bt-hotspot-product-mobile', $point.parent().width() < 600);
			}

			if (maxPoint === pointRight) {
				if (infoWidth > pointRight) {
					const maxHigh = Math.max(pointTop, pointBottom);
					if (maxHigh === pointTop) {
						return 'topcenter';
					} else {
						return 'bottomcenter';
					}
				} else {
					if (halfHeight < pointTop && halfHeight < pointBottom) {
						return 'rightcenter';
					} else if (infoHeight < pointTop) {
						return 'righttop';
					} else {
						return 'rightbottom';
					}
				}
			} else if (maxPoint === pointLeft) {
				if (infoWidth > pointLeft) {
					const maxHigh = Math.max(pointTop, pointBottom);
					if (maxHigh === pointTop) {
						return 'topcenter';
					} else {
						return 'bottomcenter';
					}
				} else {
					if (halfHeight < pointTop && halfHeight < pointBottom) {
						return 'leftcenter';
					} else if (infoHeight < pointTop) {
						return 'lefttop';
					} else {
						return 'leftbottom';
					}
				}
			} else if (maxPoint === pointTop) {
				if (halfWidth < pointLeft && halfWidth < pointRight) {
					return 'topcenter';
				} else if (infoWidth < pointLeft) {
					return 'topleft';
				} else {
					return 'topright';
				}
			} else if (maxPoint === pointBottom) {
				if (halfWidth < pointLeft && halfWidth < pointRight) {
					return 'bottomcenter';
				} else if (infoWidth < pointLeft) {
					return 'bottomleft';
				} else {
					return 'bottomright';
				}
			}
		}

		function hotspotPoint() {
			$HotspotProduct.find('.bt-hotspot-point').each(function () {
				const $point = $(this);
				const $positionPoin = getPositionPoint($point);
				const $info = $point.find('.bt-hotspot-product-info');
				const containerWidth = $point.parent().width();
				let smallOffset = 5;
				let largeOffset = 15;
				if (containerWidth < 700) {
					smallOffset = 2;
					largeOffset = 8;
				}
				if ($positionPoin == 'rightcenter') {
					$info.css({
						'inset': 'auto auto auto 100%',
						'transform': `translateX(${largeOffset}px)`
					});
				} else if ($positionPoin == 'righttop') {
					$info.css({
						'inset': 'auto auto 100% 100%',
						'transform': `translate(0, -${smallOffset}px)`
					});
				} else if ($positionPoin == 'rightbottom') {
					$info.css({
						'inset': '100% auto auto 100%',
						'transform': `translate(0, ${smallOffset}px)`
					});
				} else if ($positionPoin == 'leftcenter') {
					$info.css({
						'inset': 'auto 100% auto auto',
						'transform': `translateX(-${largeOffset}px)`
					});
				} else if ($positionPoin == 'lefttop') {
					$info.css({
						'inset': 'auto 100% 100% auto',
						'transform': `translate(0, -${smallOffset}px)`
					});
				} else if ($positionPoin == 'leftbottom') {
					$info.css({
						'inset': '100% 100% auto auto',
						'transform': `translate(0, ${smallOffset}px)`
					});
				} else if ($positionPoin == 'topcenter') {
					$info.css({
						'inset': 'auto auto 100% auto',
						'transform': `translateY(-${largeOffset}px)`
					});
				} else if ($positionPoin == 'topleft') {
					$info.css({
						'inset': 'auto 100% 100% auto',
						'transform': `translate(0, -${smallOffset}px)`
					});
				} else if ($positionPoin == 'topright') {
					$info.css({
						'inset': 'auto auto 100% 100%',
						'transform': `translate(0, -${smallOffset}px)`
					});
				} else if ($positionPoin == 'bottomcenter') {
					$info.css({
						'inset': '100% auto auto auto',
						'transform': `translateY(${largeOffset}px)`
					});
				} else if ($positionPoin == 'bottomleft') {
					$info.css({
						'inset': '100% 100% auto auto',
						'transform': `translate(0, ${smallOffset}px)`
					});
				} else if ($positionPoin == 'bottomright') {
					$info.css({
						'inset': '100% auto auto 100%',
						'transform': `translate(0, ${smallOffset}px)`
					});
				}
			});
		}

		hotspotPoint();
		$(window).on('resize', function () {
			hotspotPoint();
		});

		// Initialize magnificPopup
		const $openBtn = $HotspotProduct.find('.bt-js-open-popup-link');
		if ($openBtn.length > 0) {
			$openBtn.magnificPopup({
				type: 'inline',
				midClick: true,
				mainClass: 'mfp-fade mfp-product-popup-hotspot',
				removalDelay: 300
			});
		}
	};

	// Accordion hotspot
	const AccordionHotspotHandler = function ($scope) {
		const $accordionHotspot = $scope.find('.bt-elwg-accordion-hotspot');
		if ($accordionHotspot.length > 0) {

			// Handle hotspot point clicks
			const $hotspotPoints = $accordionHotspot.find('.bt-hotspot-point');
			const $accordionItems = $accordionHotspot.find('.bt-accordion-hotspot__item');
			const $accordionTitle = $accordionItems.find('.bt-accordion-hotspot__item--title');
			const $accordionDesc = $accordionItems.find('.bt-accordion-hotspot__item--desc');

			$accordionTitle.on('click', function (e) {
				e.preventDefault();
				const $titleCurent = $(this);
				const $descCurent = $titleCurent.next();
				const $itemCurrent = $titleCurent.parent();
				const itemIndex = $itemCurrent.data('index');

				if ($itemCurrent.hasClass('__is_active')) {
					$descCurent.slideUp();
					$itemCurrent.removeClass('__is_active');
					$hotspotPoints.removeClass('__is_active');
				} else {
					$accordionItems.removeClass('__is_active');
					$accordionDesc.slideUp();
					$hotspotPoints.removeClass('__is_active');

					$descCurent.slideDown();
					$itemCurrent.addClass('__is_active');

					$hotspotPoints.filter(`[data-index="${itemIndex}"]`).addClass('__is_active');
				}
			});

			$hotspotPoints.on('click', function (e) {
				e.preventDefault();
				const $pointCurrent = $(this);
				const itemIndex = $pointCurrent.data('index');

				if ($pointCurrent.hasClass('__is_active')) {
					$hotspotPoints.removeClass('__is_active');
					$accordionItems.removeClass('__is_active');
					$accordionDesc.slideUp();
				} else {
					$hotspotPoints.removeClass('__is_active');
					$accordionItems.removeClass('__is_active');
					$accordionDesc.slideUp();

					$pointCurrent.addClass('__is_active');

					$accordionItems.filter(`[data-index="${itemIndex}"]`).addClass('__is_active');
					$accordionItems.filter(`[data-index="${itemIndex}"]`).find('.bt-accordion-hotspot__item--desc').slideDown();
				}

				if ($(window).width() <= 767) {
					const $accordionList = $accordionHotspot.find('.bt-accordion-hotspot__list');
					$('html, body').animate({
						scrollTop: $accordionList.offset().top - 100
					}, 500);
				}
			});
		}
	}

	const MegaMenuHandler = function ($scope, $) {
		const $megamenuWrapper = $scope.find('.bt-elwg-megamenu--default');
		const $megamenuToggle = $megamenuWrapper.find('.bt-megamenu-toggle');
		const $megamenuContainer = $megamenuWrapper.find('.bt-megamenu-wrapper');
		const $megamenu = $megamenuWrapper.find('.bt-megamenu');
		const $megamenuDropdowns = $scope.find('.bt-megamenu-dropdown');

		// Set menu wrapper position below toggle button
		function setMenuWrapperPosition() {
			if (window.innerWidth <= 1024 && $megamenuToggle.length && $megamenuContainer.length) {
				var toggleRect = $megamenuToggle[0].getBoundingClientRect();
				$megamenuContainer.css('--top-mega-mobile', toggleRect.bottom + 'px');
			}
		}

		// Icon bar toggle handler
		if ($megamenuToggle.length) {
			$megamenuToggle.on('click', function (e) {
				e.preventDefault();
				var $toggle = $(this);
				var isExpanded = $toggle.attr('aria-expanded') === 'true';

				setMenuWrapperPosition();
				$toggle.attr('aria-expanded', !isExpanded);
				$toggle.toggleClass('bt-is-active', !isExpanded);
				$megamenuContainer.toggleClass('bt-is-active', !isExpanded);
			});
		}

		// Accordion toggle for menu items with children (mobile)
		function initAccordionMenu() {
			var $hasChildren = $megamenu.find('.menu-item-has-children, .menu-item-has-megamenu');

			$hasChildren.each(function () {
				var $item = $(this);
				var $toggleIcon = $item.find('> .bt-toggle-icon');

				// Create toggle icon if not exists
				if ($toggleIcon.length === 0) {
					$toggleIcon = $('<span class="bt-toggle-icon"></span>');
					$item.append($toggleIcon);
				}

				// Toggle icon click handler
				$toggleIcon.off('click.megamenu').on('click.megamenu', function (e) {
					e.preventDefault();
					e.stopPropagation();

					var $subMenu = $item.find('> .sub-menu');
					var $megamenuDropdown = $item.find('> .bt-megamenu-dropdown');
					var $target = $subMenu.length ? $subMenu : $megamenuDropdown;
					if (!$target.length) return;

					if ($item.hasClass('bt-is-active')) {
						$item.removeClass('bt-is-active');
						$target.slideUp(300);
					} else {
						// Close siblings
						$item.siblings('.bt-is-active').removeClass('bt-is-active').find('> .sub-menu, > .bt-megamenu-dropdown').slideUp(300);
						$item.siblings().find('.bt-is-active').removeClass('bt-is-active').closest('li').find('> .sub-menu, > .bt-megamenu-dropdown').slideUp(300);

						// Open current
						$item.addClass('bt-is-active');
						$target.slideDown(300);
					}
				});
			});
		}

		// Set --fullwidth-mega for menu wrapper and dropdowns
		function setMegaMenuDropdownVariables() {
			var windowWidth = $(window).width();
			$megamenuWrapper.css('--fullwidth-mega', windowWidth + 'px');

			// Desktop: --left-mega-full, --left-mega-center per dropdown
			$megamenuDropdowns.each(function () {
				var $dropdown = $(this);
				var dropdownWidth = $dropdown.outerWidth();
				var $container = $dropdown.closest('.bt-megamenu-wrapper');
				var containerLeft = $container.length ? $container[0].getBoundingClientRect().left : 0;
				if ($dropdown.hasClass('bt-megamenu-full-width')) {
					var offsetParent = $dropdown[0].offsetParent;
					var positioningContextLeft = offsetParent ? offsetParent.getBoundingClientRect().left : 0;
					$dropdown.css('--left-mega-full', positioningContextLeft + 'px');
				}
				var leftCenter = (windowWidth - dropdownWidth) / 2 - containerLeft;
				$dropdown.css('--left-mega-center', leftCenter + 'px');
			});
		}

		// Initialize
		setMegaMenuDropdownVariables();
		setMenuWrapperPosition();
		if (window.innerWidth <= 1024) {
			initAccordionMenu();
		}

		// JS ready: allow hover (variables are set)
		$megamenuWrapper.removeClass('bt-megamenu-js-pending');

		// Handle resize and scroll
		var resizeTimeout;
		$(window).on('resize scroll', function () {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(function () {
				setMegaMenuDropdownVariables();
				setMenuWrapperPosition();
				if (window.innerWidth <= 1024) {
					initAccordionMenu();
				}
			}, 100);
		});
	};

	const LocationListHandler = function ($scope, $) {
		const $locationFinder = $scope.find('.bt-elwg-location-list--finder');

		if ($locationFinder.length === 0) return;

		const $locationItems = $locationFinder.find('.bt-location-item');
		const $searchInput = $locationFinder.find('#location-search');
		const $searchButton = $locationFinder.find('.bt-search-button');
		const $mapContainer = $locationFinder.find('.bt-location-finder--map .bt-map-container');
		const $mapInfoCard = $locationFinder.find('#map-info-card');

		// Create locations JSON data
		let locationsData = [];
		$locationItems.each(function (index) {
			const $item = $(this);
			locationsData.push({
				index: index,
				title: $item.find('.bt-location-title').text().trim().toLowerCase(),
				address: $item.find('.bt-location-address').text().trim().toLowerCase(),
				phone: $item.find('.bt-location-phone').text().trim().toLowerCase(),
				mapAddress: $item.data('address'),
				zoom: $item.data('zoom') || 15,
				element: $item
			});
		});

		// Create all maps and hide them initially
		function createAllMaps() {
			$mapContainer.empty();

			locationsData.forEach(function (location, index) {
				if (location.mapAddress) {
					const mapSrc = 'https://maps.google.com/maps?q=' + encodeURIComponent(location.mapAddress) +
						'&t=m&z=' + location.zoom + '&output=embed&iwloc=near';

					const $iframe = $('<iframe>', {
						id: 'location-map-' + index,
						class: 'location-map-iframe',
						src: mapSrc,
						loading: 'lazy',
						title: location.mapAddress,
						'aria-label': location.mapAddress,
						style: index === 0 ? 'display: block;' : 'display: none;'
					});

					$mapContainer.append($iframe);
				}
			});
		}

		// Initialize maps
		createAllMaps();

		// Location click handler
		$locationItems.on('click', function (e) {
			// Allow links (a tags) inside location item to work normally
			if ($(e.target).closest('a').length) {
				return;
			}
			e.preventDefault();

			const $this = $(this);
			const locationIndex = $this.data('location-index');
			const locationData = locationsData[locationIndex];

			if (!locationData) return;

			// Update active state
			$locationItems.removeClass('active');
			$this.addClass('active');

			// Show corresponding map
			$mapContainer.find('.location-map-iframe').hide();
			$mapContainer.find('#location-map-' + locationIndex).show();

			// Update info card
			$mapInfoCard.find('.bt-map-info-title').text($this.find('.bt-location-title').text());
			$mapInfoCard.find('.bt-map-info-address').text($this.find('.bt-location-address').text());
			$mapInfoCard.find('.bt-map-info-location').text($this.find('.bt-location-address').text());

			// Scroll to map on mobile
			if ($(window).width() <= 1024) {
				$('html, body').animate({
					scrollTop: $locationFinder.find('.bt-location-finder--map').offset().top - 20
				}, 500);
			}
		});

		// Enhanced search functionality using JSON data
		function performSearch() {
			const searchTerm = $searchInput.val().toLowerCase().trim();
			let visibleCount = 0;

			// Search through JSON data
			locationsData.forEach(function (location) {
				const isMatch = searchTerm === '' ||
					location.title.includes(searchTerm) ||
					location.address.includes(searchTerm) ||
					location.phone.includes(searchTerm);

				if (isMatch) {
					location.element.removeClass('bt-hidden').addClass('bt-visible');
					visibleCount++;
				} else {
					location.element.removeClass('bt-visible').addClass('bt-hidden');
				}
			});

			// Show no results message
			let $noResults = $locationFinder.find('.bt-no-results');
			if (visibleCount === 0 && searchTerm !== '') {
				if ($noResults.length === 0) {
					$noResults = $('<div class="bt-no-results">No locations found matching your search.</div>');
					$locationFinder.find('.bt-location-list').after($noResults);
				}
				$noResults.addClass('show');
			} else {
				$noResults.removeClass('show');
			}

			// Auto-select first visible item
			if (searchTerm !== '' && visibleCount > 0) {
				const $firstVisible = $locationItems.filter(':visible').first();
				if (!$firstVisible.hasClass('active')) {
					$firstVisible.trigger('click');
				}
			}
		}

		// Search input handler with debounce
		let searchTimeout;
		$searchInput.on('input', function () {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(performSearch, 300);
		});

		// Search on Enter key
		$searchInput.on('keypress', function (e) {
			if (e.which === 13) {
				e.preventDefault();
				clearTimeout(searchTimeout);
				performSearch();
			}
		});

		// Search button click
		$searchButton.on('click', function (e) {
			e.preventDefault();
			clearTimeout(searchTimeout);
			performSearch();
		});

		// Keyboard navigation
		$searchInput.on('keydown', function (e) {
			const $visibleItems = $locationItems.filter(':visible');
			const $activeItem = $visibleItems.filter('.active');
			const currentIndex = $visibleItems.index($activeItem);

			switch (e.which) {
				case 38: // Up arrow
					e.preventDefault();
					if (currentIndex > 0) {
						$visibleItems.eq(currentIndex - 1).trigger('click');
					}
					break;
				case 40: // Down arrow
					e.preventDefault();
					if (currentIndex < $visibleItems.length - 1) {
						$visibleItems.eq(currentIndex + 1).trigger('click');
					} else if (currentIndex === -1 && $visibleItems.length > 0) {
						$visibleItems.eq(0).trigger('click');
					}
					break;
			}
		});

		// Initialize: Select first item by default
		if ($locationItems.length > 0) {
			$locationItems.first().addClass('active');
		}

	};

	// Account Login Handler
	const AccountLoginHandler = function ($scope, $) {
		const $loginPopup = $scope.find('.bt-js-open-popup-link');
		const $loginForm = $scope.find('.bt-login-form');
		const $registerForm = $scope.find('.bt-register-form');
		const $togglePassword = $scope.find('.bt-toggle-password');
		const $passwordInput = $scope.find('input[type="password"]');
		const $loginMessagesContainer = $scope.find('.bt-login-messages');
		const $registerMessagesContainer = $scope.find('.bt-register-messages');
		const $tabBtns = $scope.find('.bt-tab-btn');
		const $tabContents = $scope.find('.bt-tab-content');


		// Initialize magnificPopup for login popup
		if ($loginPopup.length > 0) {
			$loginPopup.magnificPopup({
				type: 'inline',
				midClick: true,
				mainClass: 'mfp-fade mfp-login-popup',
				removalDelay: 300,
				callbacks: {
					open: function () {
						// Focus on username field when popup opens
						setTimeout(function () {
							const $activeTab = $scope.find('.bt-tab-content.active');
							$activeTab.find('input[name="username"]').focus();
						}, 100);
					},
					close: function () {
						// Clear forms and messages when popup closes
						if ($loginForm.length) $loginForm[0].reset();
						if ($registerForm.length) $registerForm[0].reset();
						$loginMessagesContainer.empty().removeClass('bt-success bt-error');
						$registerMessagesContainer.empty().removeClass('bt-success bt-error');

						// Reset to login tab
						$tabBtns.removeClass('active');
						$tabBtns.filter('[data-tab="login"]').addClass('active');
						$tabContents.removeClass('active');
						$tabContents.filter('[data-tab="login"]').addClass('active');
					}
				}
			});
		}

		// Handle tab switching
		$tabBtns.on('click', function (e) {
			e.preventDefault();
			const targetTab = $(this).data('tab');

			// Update tab buttons
			$tabBtns.removeClass('active');
			$(this).addClass('active');

			// Update tab content
			$tabContents.removeClass('active');
			$tabContents.filter('[data-tab="' + targetTab + '"]').addClass('active');

			// Clear messages
			$loginMessagesContainer.empty().removeClass('bt-success bt-error');
			$registerMessagesContainer.empty().removeClass('bt-success bt-error');

			// Focus on first input
			setTimeout(function () {
				const $activeContent = $tabContents.filter('.active');
				$activeContent.find('input[name="username"]').focus();
			}, 100);
		});

		// Handle switch to register link
		$scope.find('.bt-switch-to-register').on('click', function (e) {
			e.preventDefault();
			$tabBtns.filter('[data-tab="register"]').trigger('click');
		});

		// Handle switch to login link
		$scope.find('.bt-switch-to-login').on('click', function (e) {
			e.preventDefault();
			$tabBtns.filter('[data-tab="login"]').trigger('click');
		});

		// Toggle password visibility
		$togglePassword.on('click', function (e) {
			e.preventDefault();
			const $btn = $(this);
			const $eyeIcon = $btn.find('.bt-eye-icon');
			const $eyeOffIcon = $btn.find('.bt-eye-off-icon');
			const $targetInput = $btn.siblings('input[type="password"], input[type="text"]');

			if ($targetInput.attr('type') === 'password') {
				$targetInput.attr('type', 'text');
				$eyeIcon.hide();
				$eyeOffIcon.show();
				$btn.attr('aria-label', $btn.attr('aria-label').replace('Show', 'Hide'));
			} else {
				$targetInput.attr('type', 'password');
				$eyeIcon.show();
				$eyeOffIcon.hide();
				$btn.attr('aria-label', $btn.attr('aria-label').replace('Hide', 'Show'));
			}
		});

		// Handle login form submission
		$loginForm.on('submit', function (e) {
			e.preventDefault();

			// Check if AJ_Options is defined
			if (typeof AJ_Options === 'undefined') {
				$loginMessagesContainer.addClass('bt-error').html('<p>Login system is not properly loaded. Please refresh the page.</p>');
				return;
			}

			const $submitBtn = $(this).find('.bt-login-btn');
			const formData = new FormData(this);

			// Add AJAX data
			formData.append('action', 'bt_login_user');
			formData.append('current_url', window.location.href);
			
			// Disable submit button and show loading
			$submitBtn.prop('disabled', true).addClass('bt-loading');
			$loginMessagesContainer.empty().removeClass('bt-success bt-error');

			// Send AJAX request
			$.ajax({
				url: AJ_Options.ajax_url,
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function (response) {
					if (response.success) {
						// Redirect after successful login
						if (response.data.redirect_url) {
							window.location.href = response.data.redirect_url;
						} else {
							window.location.reload();
						}
					} else {
						// Show specific error message from server
						var errorMessage = response.data && response.data.message ? response.data.message : 'Login failed. Please try again.';
						$loginMessagesContainer.addClass('bt-error').html('<p>' + errorMessage + '</p>');
						$submitBtn.prop('disabled', false).removeClass('bt-loading');
					}
				},
				error: function (xhr, status, error) {
					// More detailed error handling
					var errorMessage = 'Login failed. ';

					if (xhr.status === 0) {
						errorMessage += 'Please check your internet connection.';
					} else if (xhr.status === 404) {
						errorMessage += 'Login service not found.';
					} else if (xhr.status === 500) {
						errorMessage += 'Server error. Please try again later.';
					} else if (status === 'timeout') {
						errorMessage += 'Request timed out. Please try again.';
					} else {
						errorMessage += 'Please try again or contact support if the problem persists.';
					}

					$loginMessagesContainer.addClass('bt-error').html('<p>' + errorMessage + '</p>');
					$submitBtn.prop('disabled', false).removeClass('bt-loading');
				}
			});
		});

		// Handle register form submission
		$registerForm.on('submit', function (e) {
			e.preventDefault();

			// Check if AJ_Options is defined
			if (typeof AJ_Options === 'undefined') {
				$registerMessagesContainer.addClass('bt-error').html('<p>Registration system is not properly loaded. Please refresh the page.</p>');
				return;
			}

			const $submitBtn = $(this).find('.bt-register-btn');
			const originalText = $submitBtn.text();
			const formData = new FormData(this);

			// Add AJAX data
			formData.append('action', 'bt_register_user');

			// Disable submit button and show loading
			$submitBtn.prop('disabled', true).addClass('bt-loading');
			$registerMessagesContainer.empty().removeClass('bt-success bt-error');

			// Send AJAX request
			$.ajax({
				url: AJ_Options.ajax_url,
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				success: function (response) {
					if (response.success) {
						if (response.data.redirect_url) {
							window.location.href = response.data.redirect_url;
						} else {
							window.location.reload();
						}
					} else {
						// Show specific error message from server
						var errorMessage = response.data && response.data.message ? response.data.message : 'Registration failed. Please try again.';
						$registerMessagesContainer.addClass('bt-error').html('<p>' + errorMessage + '</p>');
						$submitBtn.prop('disabled', false).removeClass('bt-loading');
					}
				},
				error: function (xhr, status, error) {
					// More detailed error handling
					var errorMessage = 'Registration failed. ';

					if (xhr.status === 0) {
						errorMessage += 'Please check your internet connection.';
					} else if (xhr.status === 404) {
						errorMessage += 'Registration service not found.';
					} else if (xhr.status === 500) {
						errorMessage += 'Server error. Please try again later.';
					} else if (status === 'timeout') {
						errorMessage += 'Request timed out. Please try again.';
					} else {
						errorMessage += 'Please try again or contact support if the problem persists.';
					}

					$registerMessagesContainer.addClass('bt-error').html('<p>' + errorMessage + '</p>');
					$submitBtn.prop('disabled', false).removeClass('bt-loading');
				}
			});
		});


	};

	// Make sure you run this code under Elementor.
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-mobile-menu.default', SubmenuToggleHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-list-faq.default', FaqHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-accordion.default', BtAccordionHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-search-product.default', SearchProductHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-search-product-style-1.default', SearchProductStyle1Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-heading-animation.default', headingAnimationHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-instagram-posts.default', InstagramPostsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-banner-product-slider.default', BannerProductSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-tooltip-hotspot.default', ProductTooltipHotspotHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-testimonial.default', ProductTestimonialHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-testimonial-slider.default', ProductTestimonialSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-testimonial-slider.default', TestimonialSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-countdown.default', countDownHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-site-notification.default', NotificationSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-mini-cart.default', MiniCartHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-currency-switcher.default', SwitcherHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-language-switcher.default', SwitcherHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-overlay-hotspot.default', ProductHotspotOverlayHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-showcase.default', ProductShowcaseHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-showcase-style-1.default', ProductShowcaseHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-showcase-style-2.default', ProductShowcaseHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-showcase-style-2.default', ProductShowcaseStyle2Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-brand-slider.default', BrandSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-vertical-banner-slider.default', VerticalBannerSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-order-tracking.default', OrderTrackingHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-popup-hotspot.default', ProductPopupHotspotHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-spotlight-item.default', VideoAutoPlayHoverHandler);


		elementorFrontend.hooks.addAction('frontend/element_ready/bt-accordion-hotspot.default', AccordionHotspotHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-megamenu.default', MegaMenuHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-location-list.default', LocationListHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-account-login.default', AccountLoginHandler);
	});

})(jQuery);
