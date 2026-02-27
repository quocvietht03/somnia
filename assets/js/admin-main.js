!(function ($) {
	"use strict";
	// Product Extra Content - Admin Functions
	function somniaExtraContentHandlers() {
		// Create Extra Content
		$(document).on('click', '.somnia-create-extra-content', function (e) {
			e.preventDefault();

			var $button = $(this);
			var $box = $button.closest('.somnia-extra-content-box');
			var $loading = $box.find('.somnia-extra-loading');
			var productId = $button.data('product-id');

			if (!productId) {
				alert('Invalid Product ID!');
				return;
			}

			// Confirm before creating
			if (!confirm('Do you want to create Extra Content for this product?')) {
				return;
			}

			// Show loading
			$button.prop('disabled', true);
			$loading.show();

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'somnia_create_extra_content',
					product_id: productId,
					nonce: somniaExtraContent.nonce
				},
				success: function (response) {
					if (response.success) {
						// Update UI
						var newHTML = '<p class="somnia-extra-status">' +
							'<span class="dashicons dashicons-yes-alt"></span>' +
							'Extra Content Created' +
							'</p>' +
							'<p>' +
							'<a href="' + response.data.edit_link + '" ' +
							'class="button button-primary button-large somnia-edit-extra-content" ' +
							'target="_blank">' +
							'<span class="dashicons dashicons-edit"></span> ' +
							'Edit Extra Content' +
							'</a>' +
							'</p>' +
							'<p>' +
							'<button type="button" ' +
							'class="button button-link-delete somnia-delete-extra-content" ' +
							'data-product-id="' + productId + '" ' +
							'data-extra-id="' + response.data.extra_content_id + '">' +
							'<span class="dashicons dashicons-trash"></span> ' +
							'Delete Extra Content' +
							'</button>' +
							'</p>';

						$box.html(newHTML);

						// Update hidden field
						$('#somnia_extra_content_post_id').val(response.data.extra_content_id);

						// Show notification
						if (typeof wp !== 'undefined' && wp.data) {
							wp.data.dispatch('core/notices').createNotice(
								'success',
								response.data.message,
								{ isDismissible: true }
							);
						} else {
							alert(response.data.message);
						}
						// Open Elementor editor in new tab
						window.open(response.data.edit_link, '_blank');

					} else {
						alert(response.data.message || 'An error occurred!');
					}
				},
				error: function () {
					alert('Server connection error!');
				},
				complete: function () {
					$button.prop('disabled', false);
					$loading.hide();
				}
			});
		});

		// Delete Extra Content
		$(document).on('click', '.somnia-delete-extra-content', function (e) {
			e.preventDefault();

			var $button = $(this);
			var $box = $button.closest('.somnia-extra-content-box');
			var $loading = $box.find('.somnia-extra-loading');
			var productId = $button.data('product-id');
			var extraId = $button.data('extra-id');

			if (!productId || !extraId) {
				alert('Invalid ID!');
				return;
			}

			// Confirm before deleting
			if (!confirm('Are you sure you want to delete this Extra Content? This action cannot be undone!')) {
				return;
			}

			// Show loading
			$button.prop('disabled', true);
			$loading.show();

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'somnia_delete_extra_content',
					product_id: productId,
					extra_id: extraId,
					nonce: somniaExtraContent.nonce
				},
				success: function (response) {
					if (response.success) {
						// Update UI
						var newHTML = '<p class="somnia-extra-status">' +
							'<span class="dashicons dashicons-info"></span>' +
							'No Extra Content Yet' +
							'</p>' +
							'<p>' +
							'<button type="button" ' +
							'class="button button-primary button-large somnia-create-extra-content" ' +
							'data-product-id="' + productId + '">' +
							'<span class="dashicons dashicons-plus-alt"></span> ' +
							'Create Extra Content' +
							'</button>' +
							'</p>';

						$box.html(newHTML);

						// Update hidden field
						$('#somnia_extra_content_post_id').val('');

						// Show notification
						if (typeof wp !== 'undefined' && wp.data) {
							wp.data.dispatch('core/notices').createNotice(
								'success',
								response.data.message,
								{ isDismissible: true }
							);
						} else {
							alert(response.data.message);
						}

					} else {
						alert(response.data.message || 'An error occurred!');
					}
				},
				error: function () {
					alert('Server connection error!');
				},
				complete: function () {
					$button.prop('disabled', false);
					$loading.hide();
				}
			});
		});
	}
	// Handle variation gallery images
	function somniaVariationGalleryHandlers() {
		// Add gallery images
		$(document).on('click', '.add-variation-gallery-image', function (e) {
			e.preventDefault();
			var button = $(this);
			var wrapper = button.closest('.variation-gallery-wrapper');

			var frame = wp.media({
				title: 'Add Gallery Images',
				multiple: true,
				library: { type: 'image' }
			});

			frame.on('select', function () {
				var selection = frame.state().get('selection');
				var attachmentIds = wrapper.find('.variation-gallery-ids').val();
				attachmentIds = attachmentIds ? attachmentIds.split(',') : [];

				selection.map(function (attachment) {
					attachment = attachment.toJSON();
					if (attachmentIds.indexOf(attachment.id.toString()) === -1) {
						attachmentIds.push(attachment.id);
						wrapper.find('.variation-gallery-images').append(
							'<div class="image" data-id="' + attachment.id + '">' +
							'<img src="' + attachment.sizes.thumbnail.url + '" />' +
							'<a href="#" class="delete-variation-gallery-image">×</a>' +
							'</div>'
						);
					}
				});

				wrapper.find('.variation-gallery-ids').val(attachmentIds.join(','));
				wrapper.closest('.woocommerce_variation').addClass('variation-needs-update');
			});

			frame.open();
		});

		// Remove gallery image
		$(document).on('click', '.delete-variation-gallery-image', function (e) {
			e.preventDefault();
			var wrapper = $(this).closest('.variation-gallery-wrapper');
			var image = $(this).closest('.image');
			var imageId = image.data('id');

			var attachmentIds = wrapper.find('.variation-gallery-ids').val().split(',');
			var index = attachmentIds.indexOf(imageId.toString());
			if (index > -1) {
				attachmentIds.splice(index, 1);
			}

			wrapper.find('.variation-gallery-ids').val(attachmentIds.join(','));
			wrapper.closest('.woocommerce_variation').addClass('variation-needs-update');
			image.remove();
		});
	}
	// Handle 360 GLB file upload
	function somnia360GLBUploadHandlers() {
		var frame360;

		// Upload button click handler
		$('.upload_360_images_button').on('click', function (e) {
			e.preventDefault();

			if (frame360) {
				frame360.open();
				return;
			}

			frame360 = wp.media({
				title: 'Select GLB File for 360° View',
				button: {
					text: 'Use this file'
				},
				multiple: false,
				library: {
					type: ['model/gltf-binary', 'application/octet-stream']
				}
			});

			frame360.on('select', function () {
				var attachment = frame360.state().get('selection').first().toJSON();
				var fileId = attachment.id;
				var fileName = attachment.filename;
				var fileSize = attachment.filesizeHumanReadable;
				var fileUrl = attachment.url;

				// Update hidden input
				$('#_product_360_images').val(fileId);

				// Create preview HTML
				var previewHtml = '<div class="file-preview" style="display: flex; align-items: center; padding: 10px; background: #f5f5f5; border-radius: 4px; max-width: 400px;">';
				previewHtml += '<span class="dashicons dashicons-media-document" style="font-size: 24px; margin-right: 10px;"></span>';
				previewHtml += '<div style="flex: 1;">';
				previewHtml += '<strong>' + fileName + '</strong><br>';
				previewHtml += '<small style="color: #666;">' + fileSize + '</small>';
				previewHtml += '</div>';
				previewHtml += '<a href="' + fileUrl + '" target="_blank" class="button button-small" style="margin-left: 10px;">View</a>';
				previewHtml += '<button type="button" class="button button-small remove_360_file_button" style="margin-left: 5px; color: #b32d2e;">Remove</button>';
				previewHtml += '</div>';

				// Update or create preview container
				if ($('.product-360-preview').length) {
					$('.product-360-preview').html(previewHtml);
				} else {
					$('.product-360-fields').append('<div class="product-360-preview" style="margin-left: 150px;">' + previewHtml + '</div>');
				}
			});

			frame360.open();
		});

		// Remove button click handler
		$(document).on('click', '.remove_360_file_button', function (e) {
			e.preventDefault();
			$('#_product_360_images').val('');
			$('.product-360-preview').remove();
		});
	}
	// Handle product info fields
	function somniaToggleProductInfoFields() {
		var layoutValue = $('#_layout_product').val();
		var displayMode = $('#_product_info_display_mode').val();
		var $displayModeSelect = $('#_product_info_display_mode');
		var $displayModeField = $displayModeSelect.closest('p');
		var currentValue = $displayModeSelect.val();
		var thumbnailLayouts = ['bottom-thumbnail', 'left-thumbnail', 'right-thumbnail'];
		var isTabAllowed = thumbnailLayouts.indexOf(layoutValue) !== -1;

		// Handle Tab Position visibility
		$('.somnia_tab_position_field')[displayMode === 'tab' && isTabAllowed ? 'show' : 'hide']();

		// Handle Toggle State visibility  
		$('.somnia_toggle_state_field')[displayMode === 'toggle' ? 'show' : 'hide']();

		// If current value is 'tab' but layout doesn't support it, switch to toggle
		if (!isTabAllowed && currentValue === 'tab') {
			$displayModeSelect.val('toggle').trigger('change');
		}

		// Disable/enable Tab option based on layout
		$displayModeSelect.find('option[value="tab"]').prop('disabled', !isTabAllowed);

		// Update note
		$displayModeField.find('.somnia-tab-note').remove();
		if (!isTabAllowed) {
			$displayModeField.append('<span class="somnia-tab-note" style="color: #666; font-style: italic; font-size: 12px;">Note: Tab option is only available for Thumbnail layouts</span>');
		}
	}
	// Attribute types: image uploader + color picker (term edit/add forms)
	function somniaAttributeTypesHandlers() {
		if (!$('.somnia-term-image-wrapper').length && !$('.somnia-color-picker').length) {
			return;
		}
		// Image uploader
		$(document).on('click', '.somnia-upload-image-button', function (e) {
			e.preventDefault();
			var button = $(this);
			var wrapper = button.closest('.somnia-term-image-wrapper');
			var input = wrapper.find('input[type="hidden"]');
			var preview = wrapper.find('.somnia-term-image-preview');
			var removeBtn = wrapper.find('.somnia-remove-image-button');

			var frame = wp.media({
				title: 'Select Image',
				button: { text: 'Use this image' },
				multiple: false
			});

			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				input.val(attachment.id);
				preview.html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto; display: block;" />');
				removeBtn.show();
			});

			frame.open();
		});

		$(document).on('click', '.somnia-remove-image-button', function (e) {
			e.preventDefault();
			var button = $(this);
			var wrapper = button.closest('.somnia-term-image-wrapper');
			var input = wrapper.find('input[type="hidden"]');
			var preview = wrapper.find('.somnia-term-image-preview');
			input.val('');
			preview.html('');
			button.hide();
		});

		// Color picker init
		function initColorPicker() {
			if ($('.somnia-color-picker').length) {
				$('.somnia-color-picker').each(function () {
					if (!$(this).hasClass('wp-color-picker')) {
						$(this).wpColorPicker({
							change: function (event, ui) {
								var $input = $(this);
								$input.val(ui.color.toString());
								// Show input wrap when color has value
								$input.closest('.wp-picker-input-wrap').show();
							},
							clear: function () {
								var $input = $(this);
								if ($input.hasClass('wp-color-picker')) {
									$input.wpColorPicker('color', '');
								}
								// Hide input wrap when cleared
								$input.closest('.wp-picker-input-wrap').hide();
							}
						});
					}
				});
				// Initial state: hide input wrap when empty
				$('.somnia-color-picker').each(function () {
					var $input = $(this);
					var $wrap = $input.closest('.wp-picker-input-wrap');
					if ($wrap.length) {
						if ($.trim($input.val())) {
							$wrap.show();
						} else {
							$wrap.hide();
						}
					}
				});
			}
		}
		initColorPicker();

		function syncColorValues() {
			$('.somnia-color-picker').each(function () {
				if ($(this).hasClass('wp-color-picker')) {
					var colorValue = $(this).wpColorPicker('color');
					if (colorValue) {
						$(this).val(colorValue);
					}
				}
			});
		}

		$('#edittag, #addtag').on('submit', function () {
			syncColorValues();
		});

		$(document).ajaxSend(function (event, jqxhr, settings) {
			if (settings.data && (settings.data.indexOf('action=add-tag') !== -1 || settings.data.indexOf('action=inline-save-tax') !== -1)) {
				syncColorValues();
			}
		});

		$(document).ajaxComplete(function (event, xhr, settings) {
			if (settings.data && settings.data.indexOf('action=add-tag') !== -1 && xhr.status === 200) {
				setTimeout(function () {
					$('#addtag .somnia-remove-image-button').trigger('click');
					$('#addtag .wp-picker-clear').trigger('click');
					initColorPicker();
				}, 200);
			}
		});
	}

	// Mega Menu handlers
	function somniaMegamenuHandlers() {
		if (!$('.somnia-megamenu-fields').length) {
			return;
		}

		// Update edit link visibility and URL when dropdown changes
		function updateEditLink($select) {
			var blockId = $select.val(),
				$container = $select.closest('.somnia-megamenu-fields'),
				$editLink = $container.find('.somnia-megamenu-edit-link'),
				$metaSep = $container.find('.meta-sep'),
				baseUrl = $editLink.data('base-edit-url');

			if (blockId) {
				$editLink.attr('href', baseUrl.replace('%id%', blockId)).show();
				if ($metaSep.length === 0 && $editLink.length) {
					$editLink.after('<span class="meta-sep"> | </span>');
				}
				$metaSep.show();
			} else {
				$editLink.hide();
				$metaSep.hide();
			}
		}

		// Update Content Horizontal Position field visibility based on Content Width
		function updateHorizontalPositionVisibility($contentWidthSelect) {
			var contentWidth = $contentWidthSelect.val(),
				$container = $contentWidthSelect.closest('.somnia-megamenu-fields'),
				$horizontalPositionField = $container.find('.somnia-megamenu-horizontal-position-field');

			if (contentWidth === 'fit-to-content') {
				$horizontalPositionField.show();
			} else {
				$horizontalPositionField.hide();
			}
		}

		// Initialize edit link visibility on page load
		$('.somnia-megamenu-block-select').each(function () {
			updateEditLink($(this));
		});

		// Initialize Content Horizontal Position visibility on page load
		$('.somnia-megamenu-content-width-select').each(function () {
			updateHorizontalPositionVisibility($(this));
		});

		// Update when dropdown changes
		$(document).on('change', '.somnia-megamenu-block-select', function () {
			updateEditLink($(this));
		});

		// Update Content Horizontal Position visibility when Content Width changes
		$(document).on('change', '.somnia-megamenu-content-width-select', function () {
			updateHorizontalPositionVisibility($(this));
		});

		// Toggle visibility of dependent fields based on checkbox
		function toggleMegamenuFields($checkbox) {
			var $container = $checkbox.closest('.somnia-megamenu-fields'),
				$dependentFields = $container.find('.somnia-megamenu-dependent-fields');

			if ($checkbox.is(':checked')) {
				$dependentFields.slideDown(200);
			} else {
				$dependentFields.slideUp(200);
			}
		}

		// Initialize visibility on page load
		$('.somnia-megamenu-enable').each(function () {
			toggleMegamenuFields($(this));
		});

		// Update when checkbox changes
		$(document).on('change', '.somnia-megamenu-enable', function () {
			toggleMegamenuFields($(this));
		});

		// Handle menu item added via AJAX (WordPress menu)
		$(document).ajaxComplete(function (event, xhr, settings) {
			if (settings.data && typeof settings.data === 'string' && settings.data.indexOf('action=add-menu-item') !== -1) {
				setTimeout(function () {
					$('.somnia-megamenu-enable').each(function () {
						toggleMegamenuFields($(this));
					});
				}, 100);
			}
		});

		// Handle "Add megamenu block" click
		$(document).on('click', '.somnia-megamenu-add-link', function (e) {
			e.preventDefault();

			var $link = $(this),
				$container = $link.closest('.somnia-megamenu-fields'),
				$select = $container.find('.somnia-megamenu-block-select'),
				itemId = $link.data('item-id');

			// Show loading state
			var originalText = $link.text();
			$link.text(somniaMegamenu.i18n.creating || 'Creating...').prop('disabled', true);

			// AJAX request to create block
			$.ajax({
				url: somniaMegamenu.ajaxurl,
				type: 'POST',
				data: {
					action: 'somnia_create_megamenu_block',
					nonce: somniaMegamenu.nonce,
				},
				success: function (response) {
					if (response.success) {
						// Add new option to dropdown
						var option = $('<option>', {
							value: response.data.block_id,
							text: response.data.block_title || (somniaMegamenu.i18n.newBlock || 'New Mega Menu Block'),
							selected: true
						});
						$select.append(option);

						// Update edit link
						updateEditLink($select);

						// Open Elementor editor in new tab
						window.open(response.data.edit_link, '_blank');
					} else {
						alert(response.data.message || (somniaMegamenu.i18n.errorCreating || 'Error creating block'));
					}
				},
				error: function () {
					alert(somniaMegamenu.i18n.errorCreating || 'Error creating block');
				},
				complete: function () {
					$link.text(originalText).prop('disabled', false);
				}
			});
		});
	}

	jQuery(document).ready(function ($) {
		somniaExtraContentHandlers();
		somniaAttributeTypesHandlers();
		somniaMegamenuHandlers();
		if (!$('#woocommerce-product-data').length) {
			return;
		}
		somniaVariationGalleryHandlers();
		somnia360GLBUploadHandlers();
		somniaToggleProductInfoFields();
		$('#_layout_product, #_product_info_display_mode').on('change', somniaToggleProductInfoFields);
	});

	jQuery(window).on('resize', function () {

	});

	jQuery(window).on('scroll', function () {

	});
})(jQuery);
