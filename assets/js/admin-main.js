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
	jQuery(document).ready(function ($) {
		somniaExtraContentHandlers();
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
