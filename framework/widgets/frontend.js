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
		console.log("trong");
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
	const TheStoryHandler = function ($scope) {
		const $TheStory = $scope.find('.bt-elwg-the-story--default');
		const $itemsSlider = $TheStory.find('.js-story-items-slider');
		const $imageSlider = $TheStory.find('.js-story-image-slider');
		const $items = $TheStory.find('.bt-the-story--item');

		if ($itemsSlider.length > 0 && $imageSlider.length > 0) {
			const $sliderSettings = $TheStory.data('slider-settings');
			const sliderSpeed = $sliderSettings.speed || 1000;
			const autoplay = $sliderSettings.autoplay || false;
			const autoplayDelay = $sliderSettings.autoplay_delay || 5000;

			// Initialize the items slider (thumbs)
			var itemsSwiper = new Swiper($itemsSlider[0], {
				spaceBetween: $sliderSettings.spaceBetween || 30,
				slidesPerView: $sliderSettings.slidesPerView || 4,
				freeMode: true,
				watchSlidesProgress: true,
				loop: false,
				speed: sliderSpeed,
				breakpoints: $sliderSettings.breakpoints || {}
			});

			// Initialize the image slider (main)
			var imageSwiper = new Swiper($imageSlider[0], {
				spaceBetween: 0,
				loop: false,
				speed: sliderSpeed,
				allowTouchMove: true,
				centeredSlides: false,
				// Disable Swiper autoplay - we'll control it with progress animation
				autoplay: false,
				thumbs: {
					swiper: itemsSwiper,
				},
			});

			// Progress animation manager
			const ProgressManager = {
				animation: null,
				startTime: null,
				currentItem: null,
				swiperInstance: null,

				// Set swiper instance for triggering slide change
				setSwiper(swiper) {
					this.swiperInstance = swiper;
				},

				// Reset progress line to 0%
				reset(item) {
					if (!item) return;
					const progressLine = item.querySelector('.bt-the-story--progress-line');
					if (progressLine) {
						progressLine.style.width = '0%';
						progressLine.style.transition = 'none';
					}
				},

				// Stop current animation
				stop() {
					if (this.animation) {
						cancelAnimationFrame(this.animation);
						this.animation = null;
					}
					this.startTime = null;
					this.currentItem = null;
				},

				// Set progress line to 100% (when autoplay is off)
				setFull(item) {
					if (!item) return;
					const progressLine = item.querySelector('.bt-the-story--progress-line');
					if (progressLine) {
						progressLine.style.width = '100%';
						progressLine.style.transition = 'none';
					}
				},

				// Start progress animation or set full (based on autoplay)
				start(item) {
					if (!item) return;

					this.stop(); // Stop any existing animation

					const progressLine = item.querySelector('.bt-the-story--progress-line');
					if (!progressLine) return;

					// If autoplay is off, just set progress line to 100% (color handled by CSS)
					if (!autoplay) {
						this.setFull(item);
						return;
					}

					// If autoplay is on, start animation
					// Setup progress line (color handled by CSS)
					this.reset(item);

					this.currentItem = item;
					this.startTime = performance.now();

					// Animation loop
					const animate = (currentTime) => {
						if (!this.startTime) this.startTime = currentTime;

						const elapsed = currentTime - this.startTime;
						const progress = Math.min((elapsed / autoplayDelay) * 100, 100);

						if (progressLine && progressLine.parentElement) {
							progressLine.style.width = progress + '%';
						}

						if (progress < 100) {
							this.animation = requestAnimationFrame(animate);
						} else {
							this.stop();
							// Trigger slide next when progress reaches 100%
							if (this.swiperInstance) {
								// If not at end, go to next slide, otherwise go to first slide
								if (!this.swiperInstance.isEnd) {
									this.swiperInstance.slideNext();
								} else {
									// If at end, go to first slide
									this.swiperInstance.slideTo(0);
								}
							}
						}
					};

					this.animation = requestAnimationFrame(animate);
				}
			};

			// Set swiper instance to ProgressManager
			ProgressManager.setSwiper(imageSwiper);

			// Find active item by slide index
			const findActiveItem = (slideIndex) => {
				let activeItem = null;

				$items.each(function () {
					const itemIndex = parseInt(this.getAttribute('data-slide-index'), 10);
					if (itemIndex === slideIndex) {
						activeItem = this;
						return false; // break
					}
				});

				return activeItem || $items.eq(slideIndex)[0];
			};

			// Handle slide change
			imageSwiper.on('slideChange', () => {
				const activeItem = findActiveItem(imageSwiper.realIndex);
				if (activeItem) {
					// Reset all progress lines
					$items.each(function () {
						ProgressManager.reset(this);
					});

					// Start progress for active item (or set full if autoplay is off)
					ProgressManager.start(activeItem);
				}
			});

			// No need for autoplay events since we control it with progress animation

			// Initialize progress for first slide on load
			const firstActiveItem = findActiveItem(imageSwiper.realIndex);
			if (firstActiveItem) {
				// Reset all progress lines first
				$items.each(function () {
					ProgressManager.reset(this);
				});
				// Start progress for first active item (or set full if autoplay is off)
				ProgressManager.start(firstActiveItem);
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

	const TestimonialsStaggeredSliderHandler = function ($scope) {
		const $TestimonialsStaggeredSlider = $scope.find('.js-data-testimonials-staggered-slider');
		if ($TestimonialsStaggeredSlider.length > 0) {
			const $sliderSettings = $TestimonialsStaggeredSlider.data('slider-settings') || {};
			// Initialize the testimonials staggered slider
			const staggeredSlider = new Swiper($TestimonialsStaggeredSlider.find('.js-testimonials-staggered-slider')[0], {
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

			// Pause autoplay on hover if autoplay is enabled
			if ($sliderSettings.autoplay) {
				$TestimonialsStaggeredSlider.find('.js-testimonials-staggered-slider')[0].addEventListener('mouseenter', () => {
					staggeredSlider.autoplay.stop();
				});

				$TestimonialsStaggeredSlider.find('.js-testimonials-staggered-slider')[0].addEventListener('mouseleave', () => {
					staggeredSlider.autoplay.start();
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

	const AccordionWithProductSliderHandler = function ($scope) {
		const $AccordionWithProductSlider = $scope.find('.bt-elwg-accordion-with-product-slider--default');
		const $accordionProducts = $AccordionWithProductSlider.find('.js-accordion-products');

		if ($accordionProducts.length > 0) {
			const $sliderSettings = $AccordionWithProductSlider.data('slider-settings');
			const sliderSpeed = $sliderSettings.speed || 1000;
			const autoplay = $sliderSettings.autoplay || false;
			const autoplayDelay = $sliderSettings.autoplay_delay || 3000;

			// Clone first slide and append to end for smooth loop effect
			const $swiperWrapper = $accordionProducts.find('.swiper-wrapper');
			const $firstSlide = $swiperWrapper.find('.swiper-slide').first();

			if ($firstSlide.length > 0) {
				// Clone first two slides and append to end for smooth loop effect
				const $secondSlide = $swiperWrapper.find('.swiper-slide').eq(1);

				const $clonedFirstSlide = $firstSlide.clone();
				$clonedFirstSlide.addClass('swiper-slide-duplicate-end'); // Add identifier class
				$swiperWrapper.append($clonedFirstSlide);

				if ($secondSlide.length > 0) {
					const $clonedSecondSlide = $secondSlide.clone();
					$clonedSecondSlide.addClass('swiper-slide-duplicate-end'); // Add identifier class
					$swiperWrapper.append($clonedSecondSlide);
				}
			}

			// Initialize the accordion products slider
			const accordionProductsSwiper = new Swiper($accordionProducts[0], {
				slidesPerView: 1,
				spaceBetween: 20,
				loop: false,
				speed: sliderSpeed,
				centeredSlides: false,
				autoplay: autoplay ? {
					delay: autoplayDelay,
					disableOnInteraction: false
				} : false,
				allowTouchMove: true,
				breakpoints: {
					767: {
						slidesPerView: 1,
						centeredSlides: false,
						spaceBetween: 20
					},
					1024: {
						slidesPerView: 1.9,
						centeredSlides: true,
						spaceBetween: 30
					}
				},
			});

			// Handle accordion navigation item click
			$AccordionWithProductSlider.find('.bt-accordion-nav-item').on('click', function () {
				const clickedIndex = parseInt($(this).data('index'));

				// Update active accordion nav item
				$AccordionWithProductSlider.find('.bt-accordion-nav-item').removeClass('active');
				$(this).addClass('active');

				// Slide to corresponding products
				accordionProductsSwiper.slideTo(clickedIndex);
			});

			// Get total number of original slides (not including cloned)
			const totalOriginalSlides = $AccordionWithProductSlider.find('.bt-accordion-nav-item').length;

			// Update accordion nav when slider changes (via navigation arrows or pagination)
			accordionProductsSwiper.on('slideChange', function () {
				const activeIndex = this.activeIndex;

				// If we're on the cloned slide (last slide), jump to first slide without stopping autoplay
				if (activeIndex >= totalOriginalSlides) {
					// Store autoplay state before reset
					const wasAutoplayRunning = this.autoplay && this.autoplay.running;

					setTimeout(() => {
						this.slideTo(0, 0); // Slide to first slide with 0 speed (no animation)

						// Restart autoplay if it was running before reset
						if (wasAutoplayRunning && autoplay) {
							setTimeout(() => {
								this.autoplay.start();
							}, 50); // Small delay to ensure slide transition is complete
						}
					}, sliderSpeed); // Wait for current transition to complete
				}

				// Update accordion nav (use modulo to handle cloned slide)
				const navIndex = activeIndex % totalOriginalSlides;
				$AccordionWithProductSlider.find('.bt-accordion-nav-item').removeClass('active');
				$AccordionWithProductSlider.find('.bt-accordion-nav-item[data-index="' + navIndex + '"]').addClass('active');
			});

			// Pause autoplay on hover if autoplay is enabled
			if (autoplay) {
				$accordionProducts[0].addEventListener('mouseenter', () => {
					if (accordionProductsSwiper.autoplay) {
						accordionProductsSwiper.autoplay.stop();
					}
				});

				$accordionProducts[0].addEventListener('mouseleave', () => {
					if (accordionProductsSwiper.autoplay) {
						accordionProductsSwiper.autoplay.start();
					}
				});
			}
		}
	};

	const TitleNavWithSliderHandler = function ($scope) {
		const $TitleNavWithSlider = $scope.find('.bt-elwg-title-nav-with-slider--default');
		const $navContent = $TitleNavWithSlider.find('.js-title-nav-content');

		if ($navContent.length > 0) {
			const $sliderSettings = $TitleNavWithSlider.data('slider-settings');
			const sliderSpeed = $sliderSettings.speed || 1000;
			const autoplay = $sliderSettings.autoplay || false;
			const autoplayDelay = $sliderSettings.autoplay_delay || 3000;

			let titleNavContentSwiper;

			function initSlider() {
				if (titleNavContentSwiper) {
					titleNavContentSwiper.destroy();
				}

				titleNavContentSwiper = new Swiper($navContent[0], {
					direction: 'horizontal',
					slidesPerView: 1,
					spaceBetween: 15,
					loop: true,
					speed: sliderSpeed,
					centeredSlides: false,
					autoplay: autoplay ? {
						delay: autoplayDelay,
						disableOnInteraction: false
					} : false,
					allowTouchMove: true,
					effect: 'slide',
					mousewheel: {
						enabled: true,
						forceToAxis: true,
					},
					breakpoints: {
						768: {
							spaceBetween: 20,
							loop: true,
						},
						1025: {
							direction: 'vertical',
							spaceBetween: 20,
							loop: false,
						},
						1367: {
							direction: 'vertical',
							spaceBetween: 30,
							loop: false,
						}
					}
				});

				// Handle navigation item click
				$TitleNavWithSlider.find('.bt-nav-item').on('click', function () {
					const clickedIndex = parseInt($(this).data('index'));

					// Update active navigation item
					$TitleNavWithSlider.find('.bt-nav-item').removeClass('active');
					$(this).addClass('active');

					// Slide to corresponding content
					titleNavContentSwiper.slideTo(clickedIndex);
				});

				// Update navigation when slider changes
				titleNavContentSwiper.on('slideChange', function () {
					const activeIndex = this.activeIndex;

					// Update navigation
					$TitleNavWithSlider.find('.bt-nav-item').removeClass('active');
					$TitleNavWithSlider.find('.bt-nav-item[data-index="' + activeIndex + '"]').addClass('active');
				});

				// Pause autoplay on hover if autoplay is enabled
				if (autoplay) {
					$navContent[0].addEventListener('mouseenter', () => {
						if (titleNavContentSwiper.autoplay) {
							titleNavContentSwiper.autoplay.stop();
						}
					});

					$navContent[0].addEventListener('mouseleave', () => {
						if (titleNavContentSwiper.autoplay) {
							titleNavContentSwiper.autoplay.start();
						}
					});
				}
				// Trigger click on second nav item programmatically for desktop
				if (window.innerWidth > 1024) {
					setTimeout(() => {
						$TitleNavWithSlider.find('.bt-nav-item').eq(1).trigger('click');
					}, 100);
				}
			}

			// Initialize slider
			initSlider();

			// Reinitialize on window resize
			let resizeTimer;
			window.addEventListener('resize', () => {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(() => {
					initSlider();
				}, 250);
			});
		}
	};

	const CollectionBannerHandler = function ($scope, $) {
		var $collectionBanner = $scope.find('.bt-collection-banner');

		if ($collectionBanner.length) {
			// Store the index of the default active item on page load
			var $defaultActiveItem = $collectionBanner.find('.collection-item.active').first();
			var defaultActiveIndex = $defaultActiveItem.length ? $defaultActiveItem.data('index') : null;

			// Handle hover events
			$collectionBanner.find('.collection-item').on('mouseenter', function () {
				var $this = $(this);
				var $container = $this.closest('.bt-collection-banner');

				// Remove active class from all items
				$container.find('.collection-item').removeClass('active');

				// Add active class to hovered item
				$this.addClass('active');
			});

			// Optional: Reset to default active item when mouse leaves container
			$collectionBanner.on('mouseleave', function () {
				var $container = $(this);

				// Remove active class from all items
				$container.find('.collection-item').removeClass('active');

				// If we have a default active item, restore it
				if (defaultActiveIndex !== null) {
					$container.find('.collection-item[data-index="' + defaultActiveIndex + '"]').addClass('active');
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

	const OffersSliderHandler = function ($scope) {
		const $offersSlider = $scope.find('.bt-elwg-offers-slider');

		if ($offersSlider.length > 0) {
			const $sliderSettings = $offersSlider.data('slider-settings');
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
					nextEl: $offersSlider.find('.bt-button-next')[0],
					prevEl: $offersSlider.find('.bt-button-prev')[0],
				},
				pagination: {
					el: $offersSlider.find('.bt-swiper-pagination')[0],
					clickable: true,
					type: 'bullets',
					renderBullet: function (index, className) {
						return '<span class="' + className + '"></span>';
					},
				},
				breakpoints: $sliderSettings.breakpoints
			};

			const swiper = new Swiper($offersSlider.find('.swiper')[0], swiperOptions);

			if ($sliderSettings.autoplay) {
				$offersSlider.find('.swiper')[0].addEventListener('mouseenter', () => {
					swiper.autoplay.stop();
				});

				$offersSlider.find('.swiper')[0].addEventListener('mouseleave', () => {
					swiper.autoplay.start();
				});
			}
		}
	};

	const TextSliderHandler = function ($scope, $) {
		var $textslider = $scope.find('.bt-elwg-text-slider--default');
		if ($textslider.length > 0) {
			var $direction = $textslider.data('direction');
			var $speed = $textslider.data('speed');
			var $spaceBetween = $textslider.data('spacebetween');

			var $swiper = new Swiper($textslider[0], {
				slidesPerView: 'auto',
				loop: true,
				spaceBetween: $spaceBetween,
				speed: $speed,
				freeMode: true,
				allowTouchMove: true,
				autoplay:
				{
					delay: 0,
					reverseDirection: $direction == 'rtl' ? true : false,
					disableOnInteraction: false,
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
		// Function to update variation_id in data-ids for a given product
		function updateHotspotProductVariationId(productItem, variationId, $container) {

			const $productItem = productItem;
			const $productId = $productItem.data('product-id');

			const $product_currencySymbol = $productItem.data('product-currency');
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
								const simplePrice = $productItemId.data('product-single-price');
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

		// Initial update on load
		$productItems.each(function () {
			updateHotspotProductVariationId($(this), null, $container);
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
					updateHotspotProductVariationId($ItemProduct, variationId, $container);
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
	// Product list hotspot
	const ProductListHotspotHandler = function ($scope) {
		const $hotspotProductNormal = $scope.find('.bt-elwg-product-list-hotspot--default');
		if ($hotspotProductNormal.length > 0) {
			// Handle hotspot point clicks
			const $hotspotPoints = $hotspotProductNormal.find('.bt-hotspot-point');
			const $productItems = $hotspotProductNormal.find('.bt-hotspot-product-list__item');

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

				// Scroll to product list on mobile/tablet (<=991px)
				if ($(window).width() <= 991) {
					const $productList = $hotspotProductNormal.find('.bt-hotspot-product-list');
					if ($productList.length > 0) {
						$('html, body').animate({
							scrollTop: $productList.offset().top - 100
						}, 500);
					}
				}
			});
			SomniaProductHotspotAddSetCart($hotspotProductNormal);
		}
	}
	const ProductSliderBottomHotspotHandler = function ($scope) {
		const $productSliderBottomHotspot = $scope.find('.bt-elwg-product-slider-bottom-hotspot--default');
		if ($productSliderBottomHotspot.length > 0) {
			// Get width of container
			const $itemdesktop = $productSliderBottomHotspot.width() < 1560 ? 3 : 4;
			// Get slider direction from data attribute
			const sliderDirection = $productSliderBottomHotspot.data('slider-direction') || 'ltr';
			const isRTL = sliderDirection === 'rtl';

			const swiperOptions = {
				slidesPerView: 1,
				loop: false,
				spaceBetween: 30,
				speed: 1000,
				autoplay: false,
				rtl: isRTL,
				navigation: {
					nextEl: $productSliderBottomHotspot.find('.bt-button-next')[0],
					prevEl: $productSliderBottomHotspot.find('.bt-button-prev')[0],
				},
				pagination: {
					el: $productSliderBottomHotspot.find('.bt-swiper-pagination')[0],
					clickable: true,
					type: 'bullets',
					renderBullet: function (index, className) {
						return '<span class="' + className + '"></span>';
					},
				},
				breakpoints: {
					1560: {
						slidesPerView: $itemdesktop,
						spaceBetween: 30
					},
					1200: {
						slidesPerView: 3,
						spaceBetween: 20
					},
					800: {
						slidesPerView: 2,
						spaceBetween: 20
					}
				}
			};

			const swiper = new Swiper($productSliderBottomHotspot.find('.swiper')[0], swiperOptions);

			// Handle hotspot point clicks
			const $hotspotPoints = $productSliderBottomHotspot.find('.bt-hotspot-point');
			const $productItems = $productSliderBottomHotspot.find('.bt-hotspot-product-list__item');

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
				const $targetProduct = $productItems.filter(`[data-product-id="${productId}"]`);
				$targetProduct.addClass('active');

				// Find the slide index containing the target product
				const $targetSlide = $targetProduct.closest('.swiper-slide');
				const slideIndex = $targetSlide.index();
				// Get number of visible items
				const visibleItemsCount = swiper.params.slidesPerView;
				// Scroll to the slide if it exists
				if (slideIndex >= 0) {
					// Only scroll if target slide is outside visible range
					const currentIndex = swiper.activeIndex;
					const lastVisibleIndex = currentIndex + visibleItemsCount - 1;

					if (slideIndex < currentIndex || slideIndex > lastVisibleIndex) {
						swiper.slideTo(slideIndex);
					}
				}

				// Scroll page so slider bottom is 20px from bottom of screen
				setTimeout(function () {
					const sliderBottom = $productSliderBottomHotspot.offset().top + $productSliderBottomHotspot.outerHeight();
					const windowHeight = $(window).height();
					const currentScroll = $(window).scrollTop();
					const targetScroll = sliderBottom - windowHeight + 20;

					if (targetScroll > currentScroll) {
						$('html, body').animate({
							scrollTop: targetScroll
						}, 600);
					}
				}, 100);
			});
			SomniaProductHotspotAddSetCart($productSliderBottomHotspot);
		}
	};
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
					1560: {
						slidesPerView: 3,
						spaceBetween: $sliderSettings.spaceBetween.desktop
					},
					1025: {
						slidesPerView: 2,
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

	const ProductHotspotOverlayStyle1Handler = function ($scope) {
		const $productHotspotOverlayStyle1 = $scope.find('.bt-elwg-product-overlay-hotspot-style-1--default');

		if ($productHotspotOverlayStyle1.length > 0) {
			const $shopPanel = $productHotspotOverlayStyle1.find('.bt-shop-the-look-panel');
			const $shopPanelHeader = $shopPanel.find('.bt-shop-panel-header');
			const $shopPanelContent = $shopPanel.find('.bt-shop-panel-content');
			const $shopPanelClose = $shopPanelHeader.find('.bt-shop-panel-close');
			const $hotspotPoints = $productHotspotOverlayStyle1.find('.bt-hotspot-point');

			// Function to check and update responsive class
			const checkResponsive = function () {
				const panelWidth = $shopPanel.outerWidth();
				if (panelWidth < 350) {
					$shopPanel.addClass('bt-responsive-panel');
				} else {
					$shopPanel.removeClass('bt-responsive-panel');
				}
			};

			// Check on load and resize
			checkResponsive();
			$(window).on('resize', function () {
				checkResponsive();
			});

			// Function to open panel
			const openPanel = function () {
				if (!$shopPanel.hasClass('bt-panel-open')) {
					$shopPanel.addClass('bt-panel-open');
					$shopPanelContent.slideDown(300);
					// Recheck responsive after opening
					setTimeout(checkResponsive, 300);
				}
			};

			// Function to close panel
			const closePanel = function () {
				$shopPanel.removeClass('bt-panel-open');
				$shopPanelContent.slideUp(300);
			};

			// Handle click on "SHOP THE LOOK" header to toggle panel
			$shopPanelHeader.on('click', function (e) {
				e.preventDefault();
				// Don't toggle if clicking the close button
				if ($(e.target).closest('.bt-shop-panel-close').length) {
					return;
				}
				const isOpen = $shopPanel.hasClass('bt-panel-open');
				if (isOpen) {
					closePanel();
				} else {
					openPanel();
				}
			});

			// Handle click on hotspot points to open panel
			$hotspotPoints.on('click', function (e) {
				e.preventDefault();
				openPanel();
			});

			// Handle click on close button in header
			$shopPanelClose.on('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				closePanel();
			});

			// Initialize add to cart functionality
			SomniaProductHotspotAddSetCart($productHotspotOverlayStyle1);
		}
	};

	const StoreLocationsHandler = function ($scope) {
		const $storeLocationsSlider = $scope.find('.bt-elwg-store-locations-slider');

		if ($storeLocationsSlider.length > 0) {
			const $sliderSettings = $storeLocationsSlider.data('slider-settings');
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
					nextEl: $storeLocationsSlider.find('.bt-button-next')[0],
					prevEl: $storeLocationsSlider.find('.bt-button-prev')[0],
				},
				pagination: {
					el: $storeLocationsSlider.find('.bt-swiper-pagination')[0],
					clickable: true,
					type: 'bullets',
					renderBullet: function (index, className) {
						return '<span class="' + className + '"></span>';
					},
				},
				breakpoints: $sliderSettings.breakpoints
			};

			const swiper = new Swiper($storeLocationsSlider.find('.swiper')[0], swiperOptions);

			if ($sliderSettings.autoplay) {
				$storeLocationsSlider.find('.swiper')[0].addEventListener('mouseenter', () => {
					swiper.autoplay.stop();
				});

				$storeLocationsSlider.find('.swiper')[0].addEventListener('mouseleave', () => {
					swiper.autoplay.start();
				});
			}
		}
	};

	const ProductNavImageHandler = function ($scope) {
		const $productNavImage = $scope.find('.bt-elwg-product-nav-image--default');
		if ($productNavImage.length > 0) {
			const $productNavImageTabs = $productNavImage.find('.bt-product-nav-image--tabs');
			const $productNavImageThumb = $productNavImage.find('.bt-product-nav-image--thumb');
			$productNavImageTabs.find('.bt-product-nav-image--tab-item').on('click', function () {
				const $this = $(this);
				const index = $this.data('index');
				$productNavImageTabs.find('.bt-product-nav-image--tab-item').removeClass('active');
				$this.addClass('active');
				$productNavImageThumb.find('.bt-product-nav-image--thumb-item').removeClass('active');
				$productNavImageThumb.find('.bt-product-nav-image--thumb-item').eq(index).addClass('active');
			});
		}
	}
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
	/* Bundle Save Handler */
	const BundleSaveHandler = function ($scope) {
		const $bundleSave = $scope.find('.bt-bundle-save');
		if ($bundleSave.length === 0) return;

		// Update data-ids and subtotal on load
		updateBundleDataIds($bundleSave);

		// Add More Button - Show modal
		$bundleSave.find('.bt-bundle-save--add-more-btn').on('click', function (e) {
			e.preventDefault();
			showModal($bundleSave);
		});

		// Modal Close
		$bundleSave.find('.bt-modal-close, .bt-modal-overlay').on('click', function (e) {
			e.preventDefault();
			$bundleSave.find('.bt-bundle-save--modal').fadeOut();
		});

		// Add product from modal
		$bundleSave.on('click', '.bt-modal-add-product', function (e) {
			e.preventDefault();
			const $button = $(this);
			const productId = $button.data('product-id');
			addProductToBundle($bundleSave, productId, $button);
		});

		// Remove product
		$bundleSave.on('click', '.bt-product-remove', function (e) {
			e.preventDefault();
			const $item = $(this).closest('.bt-bundle-product--item');
			$item.fadeOut(300, function () {
				$(this).remove();
				updateBundleDataIds($bundleSave);
			});
		});

		// Add all to cart
		$bundleSave.find('.bt-bundle-save--add-cart-btn').on('click', function (e) {
			e.preventDefault();
			addBundleToCart($bundleSave, $(this));
		});

		function showModal($widget) {
			const $modal = $widget.find('.bt-bundle-save--modal');
			const $modalBody = $modal.find('.bt-modal-body');
			const $productsContainer = $widget.find('.bt-bundle-save--products');

			const bundleProducts = $productsContainer.data('bundle-products') || [];
			const currentProducts = [];

			// Collect all current product/variation IDs
			$widget.find('.bt-bundle-product--item').each(function () {
				const variationId = parseInt($(this).data('variation-id'));
				const productId = parseInt($(this).data('product-id'));

				// If it's a variation, use variation ID, otherwise use product ID
				const itemId = variationId > 0 ? variationId : productId;
				currentProducts.push(itemId);
			});

			// Filter out products that are already in the bundle
			const availableProducts = bundleProducts.filter(function (id) {
				return currentProducts.indexOf(parseInt(id)) === -1;
			});

			if (availableProducts.length === 0) {
				$modalBody.html('<p class="bt-no-products">All products are already added to the bundle.</p>');
			} else {
				loadAvailableProducts($widget, availableProducts, $modalBody);
			}

			$modal.fadeIn();
		}

		function loadAvailableProducts($widget, productIds, $container) {
			$container.html('<p class="bt-loading">Loading products...</p>');

			$.ajax({
				url: AJ_Options.ajax_url,
				type: 'POST',
				data: {
					action: 'somnia_get_bundle_products',
					product_ids: productIds
				},
				success: function (response) {
					if (response.success && response.data.html) {
						$container.html(response.data.html);
					} else {
						$container.html('<p class="bt-error">Failed to load products.</p>');
					}
				},
				error: function () {
					$container.html('<p class="bt-error">Failed to load products.</p>');
				}
			});
		}

		function addProductToBundle($widget, productId, $button) {
			const $productsContainer = $widget.find('.bt-bundle-save--products');
			$button.prop('disabled', true).text('Adding...');

			$.ajax({
				url: AJ_Options.ajax_url,
				type: 'POST',
				data: {
					action: 'somnia_get_bundle_product_item',
					product_id: productId
				},
				success: function (response) {
					if (response.success && response.data.html) {
						$productsContainer.append(response.data.html);
						updateBundleDataIds($widget);

						// Remove this product item from modal
						$button.closest('.bt-modal-product--item').fadeOut(300, function () {
							$(this).remove();

							// Check if any products left in modal
							const $modal = $widget.find('.bt-bundle-save--modal');
							const $modalBody = $modal.find('.bt-modal-body');
							const remainingProducts = $modalBody.find('.bt-modal-product--item').length;

							if (remainingProducts === 0) {
								$modalBody.html('<p class="bt-no-products">All products are already added to the bundle.</p>');
								setTimeout(function () {
									$modal.fadeOut();
								}, 1000);
							}
						});
					} else {
						$button.prop('disabled', false).text('Add');
					}
				},
				error: function () {
					$button.prop('disabled', false).text('Add');
				}
			});
		}

		function updateBundleDataIds($widget) {
			const $productsContainer = $widget.find('.bt-bundle-save--products');
			const $addCartBtn = $widget.find('.bt-bundle-save--add-cart-btn');
			const idsArr = [];
			let subtotal = 0;
			let regularTotal = 0;
			let productCount = 0;

			$widget.find('.bt-bundle-product--item').each(function () {
				const $item = $(this);
				const productId = parseInt($item.data('product-id'));
				const variationId = parseInt($item.data('variation-id')) || 0;
				const price = parseFloat($item.data('price')) || 0;
				const regularPrice = parseFloat($item.data('regular-price')) || price;

				idsArr.push({
					product_id: productId,
					variation_id: variationId
				});

				subtotal += price;
				regularTotal += regularPrice;
				productCount++;
			});

			// Calculate savings
			const savings = regularTotal - subtotal;
			const savingsPercent = regularTotal > 0 ? ((savings / regularTotal) * 100) : 0;

			// Update progress bar
			const $progressBar = $widget.find('.bt-progress-fill');
			const $discountText = $widget.find('.bt-discount-text');

			if ($progressBar.length) {
				$progressBar.css('width', savingsPercent.toFixed(0) + '%');
			}

			if ($discountText.length) {
				let template = $discountText.data('template');
				if (!template) {
					return;
				}
				let text = template;

				// Replace placeholders with styled spans
				text = text.replace(/\{count\}/g, '<span>' + productCount + '</span>');
				text = text.replace(/\{discount\}/g, '<span>' + savingsPercent.toFixed(0) + '%</span>');

				$discountText.html(text);
			}

			// Update button data-ids
			$addCartBtn.attr('data-ids', JSON.stringify(idsArr));
			// Check if no products 
			if (idsArr.length === 0) {
				$addCartBtn.prop('disabled', true);
			} else {
				$addCartBtn.prop('disabled', false);
			}

			// Update subtotal display
			const currencySymbol = AJ_Options.currency_symbol || '$';
			const formattedSubtotal = currencySymbol + subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
			$widget.find('.bt-subtotal-amount').text(formattedSubtotal);
		}

		function addBundleToCart($widget, $button) {
			let productIds = $button.data('ids');

			if (typeof productIds === 'string') {
				try {
					productIds = JSON.parse(productIds);
				} catch (e) {
					console.error('Invalid data-ids JSON', e);
					productIds = [];
				}
			}
			if ($button.hasClass('bt-view-cart')) {
				window.location.href = AJ_Options.cart;
				return;
			}
			if (!Array.isArray(productIds) || productIds.length === 0) {
				alert('Please add products to the bundle first.');
				return;
			}

			const originalText = $button.text();
			$button.prop('disabled', true).addClass('loading');


			$.ajax({
				url: AJ_Options.ajax_url,
				type: 'POST',
				data: {
					action: 'somnia_add_multiple_to_cart_variable',
					product_ids: productIds
				},
				success: function (response) {
					$button.removeClass('loading');
					if (response.success) {
						$(document.body).trigger('updated_wc_div');
						SomniaFreeShippingMessage();
						$button.text('View Cart').prop('disabled', false).addClass('bt-view-cart');
						// Handle cart action for each product
						productIds.forEach((item, idx) => {
							const productId = item.variation_id && item.variation_id !== 0 ? item.variation_id : item.product_id;
							setTimeout(() => {
								SomniaHandleCartAction(productId);
							}, idx * 300);
						});
					} else {
						alert('Failed to add products to cart.');
						$button.prop('disabled', false).text(originalText);
					}
				},
				error: function () {
					$button.removeClass('loading');
					alert('Failed to add products to cart.');
					$button.prop('disabled', false).text(originalText);
				}
			});
		}
	};

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

	const FlickerCollageHandler = function ($scope, $) {
		const $widget = $scope.find('.bt-elwg-flicker-collage');
		if (!$widget.length) return;

		// Don't run animation in Elementor editor
		const isEditor = $widget.data('elementor-editor') === 'true' ||
			$('body').hasClass('elementor-editor-active') ||
			(typeof elementorFrontend !== 'undefined' && elementorFrontend.isEditMode && elementorFrontend.isEditMode());

		if (isEditor) {
			return;
		}

		const $block = $widget.find('.bt-flicker-collage--list-images');
		if (!$block.length) return;

		const $items = $block.find('.bt-flicker-collage_item');
		if (!$items.length) return;

		const items = $items.toArray();
		let currentIndex = 0;
		let animationInterval;

		function getPositions() {
			const currentWidth = window.innerWidth;

			// Get grid-positions data from block
			const gridPositionsData = $block.data('grid-positions');

			if (!gridPositionsData || !gridPositionsData.breakpoints) {
				return [];
			}

			let gridAreas = [];

			// Get all breakpoint values (excluding 'default')
			const breakpointValues = Object.keys(gridPositionsData.breakpoints)
				.map(Number)
				.filter(function (val) { return !isNaN(val); })
				.sort(function (a, b) { return a - b; }); // Sort ascending (767, 1024, 1200, 1366)

			// If currentWidth > largest breakpoint (1366), use default
			if (breakpointValues.length > 0 && currentWidth > breakpointValues[breakpointValues.length - 1]) {
				gridAreas = gridPositionsData.breakpoints.default || [];
			} else {
				// Find the smallest breakpoint that is >= currentWidth
				let selectedBreakpoint = null;
				for (let i = 0; i < breakpointValues.length; i++) {
					if (currentWidth <= breakpointValues[i]) {
						selectedBreakpoint = breakpointValues[i];
						break;
					}
				}

				// Use selected breakpoint or fallback to default
				if (selectedBreakpoint !== null && gridPositionsData.breakpoints[selectedBreakpoint]) {
					gridAreas = gridPositionsData.breakpoints[selectedBreakpoint];
				} else {
					gridAreas = gridPositionsData.breakpoints.default || [];
				}
			}

			// Convert array of grid_area strings to array of objects with gridArea property
			const positions = gridAreas.map(function (gridArea) {
				// Trim whitespace from gridArea string
				return { gridArea: String(gridArea).trim() };
			});

			return positions;
		}

		// Get random position from all items' positions
		function getRandomPosition() {
			const positions = getPositions();
			return positions[Math.floor(Math.random() * positions.length)];
		}

		// Check if position overlaps with visible items
		function isOverlapping(newPosition) {
			return items.some(function (item) {
				if (item.classList.contains('visible')) {
					return window.getComputedStyle(item).gridArea === newPosition.gridArea;
				}
				return false;
			});
		}

		// Get unique position that doesn't overlap
		function getUniquePosition() {
			let newPosition;
			let tries = 0;
			const maxTries = 10;

			do {
				newPosition = getRandomPosition();
				tries++;
			} while (isOverlapping(newPosition) && tries < maxTries);

			return newPosition;
		}

		// Update visibility and positions
		function updateVisibility() {
			// Remove visible class from all items
			items.forEach(function (item) {
				item.classList.remove('visible');
			});

			// Add visible class to 2 items (current and next)
			for (let i = 0; i < 2; i++) {
				const index = (currentIndex + i) % items.length;
				items[index].classList.add('visible');
			}

			// Move disappearing item to new position after delay
			const disappearingIndex = (currentIndex + 3) % items.length;
			const disappearingItem = items[disappearingIndex];

			setTimeout(function () {
				const newPosition = getUniquePosition();
				disappearingItem.style.gridArea = newPosition.gridArea;
				disappearingItem.classList.add('visible');
			}, 1500);

			// Update current index
			currentIndex = (currentIndex + 1) % items.length;
		}

		// Start animation
		function startAnimation() {
			if (animationInterval) {
				clearInterval(animationInterval);
			}
			animationInterval = setInterval(updateVisibility, 1500);
		}

		// Initialize
		startAnimation();

		// Handle window resize
		let resizeTimer;
		$(window).on('resize', function () {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function () {
				// Reset visibility on resize
				items.forEach(function (item) {
					item.classList.remove('visible');
				});
				// Restart animation
				startAnimation();
			}, 250);
		});
	};

	const ImageListWidgetHandler = function ($scope, $) {
		const $widget = $scope.find('.bt-elwg-list-text-image-hover--default');
		if (!$widget.length) return;

		const $list = $widget.find('.list-text-image-hover--list');
		const $items = $widget.find('.list-text-image-hover--item');
		const $images = $widget.find('.list-text-image-hover--image');
		const defaultActive = parseInt($widget.data('default-active')) || 0;

		// Set active item by index
		const setActive = (index) => {
			$items.removeClass('active');
			$images.removeClass('active');
			$items.filter('[data-index="' + index + '"]').addClass('active');
			$images.filter('[data-image-index="' + index + '"]').addClass('active');
		};

		// Get index of item being hovered
		const getHoveredIndex = () => {
			let index = null;
			$items.each(function () {
				const $item = $(this);
				if ($item.is(':hover') || $item.find(':hover').length) {
					index = parseInt($item.data('index'));
					return false;
				}
			});
			return index;
		};

		// Initialize: check hover first, then default
		const init = () => {
			const hovered = getHoveredIndex();
			setActive(hovered !== null ? hovered : defaultActive);
		};

		// Initialize on load
		init();
		setTimeout(init, 100);

		// Hover events
		$items.on('mouseenter', function () {
			setActive(parseInt($(this).data('index')));
		});

		$list.on('mouseleave', () => {
			setActive(defaultActive);
		});
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
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-offers-slider.default', OffersSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-tooltip-hotspot.default', ProductTooltipHotspotHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-testimonial.default', ProductTestimonialHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-testimonial-slider.default', ProductTestimonialSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-the-story.default', TheStoryHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-testimonial-slider.default', TestimonialSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-testimonials-staggered-slider.default', TestimonialsStaggeredSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-countdown.default', countDownHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-site-notification.default', NotificationSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-mini-cart.default', MiniCartHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-currency-switcher.default', SwitcherHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-language-switcher.default', SwitcherHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-accordion-with-product-slider.default', AccordionWithProductSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-title-nav-with-slider.default', TitleNavWithSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-collection-banner.default', CollectionBannerHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-overlay-hotspot.default', ProductHotspotOverlayHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-overlay-hotspot-style-1.default', ProductHotspotOverlayStyle1Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-text-slider.default', TextSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-showcase.default', ProductShowcaseHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-showcase-style-1.default', ProductShowcaseHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-showcase-style-2.default', ProductShowcaseHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-showcase-style-2.default', ProductShowcaseStyle2Handler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-list-hotspot.default', ProductListHotspotHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-store-locations-slider.default', StoreLocationsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-slider-bottom-hotspot.default', ProductSliderBottomHotspotHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-nav-image.default', ProductNavImageHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-brand-slider.default', BrandSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-vertical-banner-slider.default', VerticalBannerSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-bundle-save.default', BundleSaveHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-order-tracking.default', OrderTrackingHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/list-text-image-hover.default', ImageListWidgetHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/flicker-collage.default', FlickerCollageHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-product-popup-hotspot.default', ProductPopupHotspotHandler);


		elementorFrontend.hooks.addAction('frontend/element_ready/bt-accordion-hotspot.default', AccordionHotspotHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/bt-megamenu.default', MegaMenuHandler);
	});

})(jQuery);
