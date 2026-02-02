<?php
/**
 * Attribute Types Management
 * 
 * Adds attribute type selection (select, button, image, color) to WooCommerce attributes
 * and creates metafields for image and color attributes
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Add attribute type field to add attribute form
 */
function somnia_add_attribute_type_field()
{
	$attribute_type = 'select'; // Default value
	?>
	<div class="form-field">
		<label for="somnia_attribute_type"><?php esc_html_e('Display Type', 'somnia'); ?></label>
		<select name="somnia_attribute_type" id="somnia_attribute_type">
			<option value="button" <?php selected($attribute_type, 'button'); ?>><?php esc_html_e('Button', 'somnia'); ?></option>
			<option value="select" <?php selected($attribute_type, 'select'); ?>><?php esc_html_e('Select', 'somnia'); ?></option>
			<option value="image" <?php selected($attribute_type, 'image'); ?>><?php esc_html_e('Image', 'somnia'); ?></option>
			<option value="color" <?php selected($attribute_type, 'color'); ?>><?php esc_html_e('Color', 'somnia'); ?></option>
		</select>
		<p class="description"><?php esc_html_e('Select how this attribute should be displayed on the frontend.', 'somnia'); ?></p>
	</div>
	<?php
}
add_action('woocommerce_after_add_attribute_fields', 'somnia_add_attribute_type_field');

/**
 * Add attribute type field to edit attribute form
 */
function somnia_edit_attribute_type_field()
{
	$edit = isset($_GET['edit']) ? absint($_GET['edit']) : 0;
	if (!$edit) {
		return;
	}

	$attribute_type = get_option('somnia_attribute_type_' . $edit, 'select');
	?>
	<tr class="form-field form-required">
		<th scope="row" valign="top">
			<label for="somnia_attribute_type"><?php esc_html_e('Display Type', 'somnia'); ?></label>
		</th>
		<td>
			<select name="somnia_attribute_type" id="somnia_attribute_type">
				<option value="select" <?php selected($attribute_type, 'select'); ?>><?php esc_html_e('Select', 'somnia'); ?></option>
				<option value="button" <?php selected($attribute_type, 'button'); ?>><?php esc_html_e('Button', 'somnia'); ?></option>
				<option value="image" <?php selected($attribute_type, 'image'); ?>><?php esc_html_e('Image', 'somnia'); ?></option>
				<option value="color" <?php selected($attribute_type, 'color'); ?>><?php esc_html_e('Color', 'somnia'); ?></option>
			</select>
			<p class="description"><?php esc_html_e('Select how this attribute should be displayed on the frontend.', 'somnia'); ?></p>
		</td>
	</tr>
	<?php
}
add_action('woocommerce_before_edit_attribute_fields', 'somnia_edit_attribute_type_field');

/**
 * Save attribute type when adding new attribute
 */
function somnia_save_attribute_type($attribute_id, $data = null)
{
	if (isset($_POST['somnia_attribute_type'])) {
		$attribute_type = sanitize_text_field($_POST['somnia_attribute_type']);
		update_option('somnia_attribute_type_' . $attribute_id, $attribute_type);

		// Get taxonomy name
		$attribute = wc_get_attribute($attribute_id);
		if ($attribute) {
			$taxonomy_name = wc_attribute_taxonomy_name($attribute->slug);

			// Add metafields to taxonomy terms if type is image or color
			if (in_array($attribute_type, ['image', 'color'])) {
				somnia_add_term_metafields($taxonomy_name, $attribute_type);
			}
		}
	}
}
add_action('woocommerce_attribute_added', 'somnia_save_attribute_type', 10, 2);

/**
 * Save attribute type when editing attribute
 */
function somnia_update_attribute_type($attribute_id, $data = null, $old_slug = null)
{
	if (isset($_POST['somnia_attribute_type'])) {
		$attribute_type = sanitize_text_field($_POST['somnia_attribute_type']);
		update_option('somnia_attribute_type_' . $attribute_id, $attribute_type);

		// Get taxonomy name
		$attribute = wc_get_attribute($attribute_id);
		if ($attribute) {
			$taxonomy_name = wc_attribute_taxonomy_name($attribute->slug);

			// Add or remove metafields based on type
			if (in_array($attribute_type, ['image', 'color'])) {
				somnia_add_term_metafields($taxonomy_name, $attribute_type);
			}
		}
	}
}
add_action('woocommerce_attribute_updated', 'somnia_update_attribute_type', 10, 3);

/**
 * Add metafields to taxonomy terms
 */
function somnia_add_term_metafields($taxonomy_name, $attribute_type)
{
	// Register meta for taxonomy terms
	if ($attribute_type === 'image') {
		register_term_meta($taxonomy_name, 'somnia_term_image', array(
			'type' => 'integer',
			'description' => 'Image ID for term',
			'single' => true,
			'show_in_rest' => true,
		));
	} elseif ($attribute_type === 'color') {
		register_term_meta($taxonomy_name, 'somnia_term_color', array(
			'type' => 'string',
			'description' => 'Color value for term',
			'single' => true,
			'show_in_rest' => true,
		));
	}
}

/**
 * Register metafields for existing attributes on init
 */
function somnia_register_existing_attribute_metafields()
{
	if (!function_exists('wc_get_attribute_taxonomies')) {
		return;
	}

	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if (empty($attribute_taxonomies)) {
		return;
	}

	foreach ($attribute_taxonomies as $attribute) {
		$attribute_type = get_option('somnia_attribute_type_' . $attribute->attribute_id, 'select');
		if (in_array($attribute_type, ['image', 'color'])) {
			$taxonomy_name = wc_attribute_taxonomy_name($attribute->attribute_name);
			somnia_add_term_metafields($taxonomy_name, $attribute_type);
		}
	}
}
add_action('init', 'somnia_register_existing_attribute_metafields', 100);

/**
 * Add image/color fields to term edit form (table row format)
 */
function somnia_add_term_metafield_fields($term, $taxonomy = '')
{
	if (empty($taxonomy)) {
		$taxonomy = is_object($term) && isset($term->taxonomy) ? $term->taxonomy : '';
	}
	
	// Ensure taxonomy is a string
	if (is_array($taxonomy)) {
		return;
	}
	
	$taxonomy = (string) $taxonomy;
	
	if (empty($taxonomy) || strpos($taxonomy, 'pa_') !== 0) {
		return;
	}

	// Get attribute ID from taxonomy
	$attribute_id = wc_attribute_taxonomy_id_by_name(str_replace('pa_', '', $taxonomy));
	if (!$attribute_id) {
		return;
	}

	$attribute_type = get_option('somnia_attribute_type_' . $attribute_id, 'select');
	
	$term_id = is_object($term) && isset($term->term_id) ? $term->term_id : 0;

	if ($attribute_type === 'image') {
		$image_id = $term_id ? get_term_meta($term_id, 'somnia_term_image', true) : 0;
		$image_url = '';
		if ($image_id) {
			$image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
		}
		?>
		<tr class="form-field term-image-wrap">
			<th scope="row">
				<label for="somnia_term_image"><?php esc_html_e('Image', 'somnia'); ?></label>
			</th>
			<td>
				<div class="somnia-term-image-wrapper">
					<div class="somnia-term-image-preview" style="margin-bottom: 10px;">
						<?php if ($image_url) : ?>
							<img src="<?php echo esc_url($image_url); ?>" style="max-width: 150px; height: auto; display: block;" />
						<?php endif; ?>
					</div>
					<input type="hidden" name="somnia_term_image" id="somnia_term_image" value="<?php echo esc_attr($image_id); ?>" />
					<button type="button" class="button somnia-upload-image-button"><?php esc_html_e('Select Image', 'somnia'); ?></button>
					<button type="button" class="button somnia-remove-image-button" style="<?php echo $image_id ? '' : 'display:none;'; ?>"><?php esc_html_e('Remove Image', 'somnia'); ?></button>
					<p class="description"><?php esc_html_e('Select an image for this term.', 'somnia'); ?></p>
				</div>
			</td>
		</tr>
		<?php
	} elseif ($attribute_type === 'color') {
		$color_value = $term_id ? get_term_meta($term_id, 'somnia_term_color', true) : '';
		?>
		<tr class="form-field term-color-wrap">
			<th scope="row">
				<label for="somnia_term_color"><?php esc_html_e('Color', 'somnia'); ?></label>
			</th>
			<td>
				<input type="text" name="somnia_term_color" id="somnia_term_color" value="<?php echo esc_attr($color_value); ?>" class="somnia-color-picker" />
				<p class="description"><?php esc_html_e('Select a color for this term.', 'somnia'); ?></p>
			</td>
		</tr>
		<?php
	}
}
// Hook into term edit/add forms dynamically for all product attribute taxonomies
function somnia_register_term_form_hooks()
{
	if (!function_exists('wc_get_attribute_taxonomies')) {
		return;
	}

	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if (empty($attribute_taxonomies)) {
		return;
	}

	foreach ($attribute_taxonomies as $attribute) {
		$taxonomy_name = wc_attribute_taxonomy_name($attribute->attribute_name);
		
		// Edit form (table row format)
		add_action($taxonomy_name . '_edit_form_fields', 'somnia_add_term_metafield_fields', 10, 2);
		
		// Add form (div format)
		add_action($taxonomy_name . '_add_form_fields', 'somnia_add_term_metafield_add_fields', 10, 1);
	}
}
add_action('init', 'somnia_register_term_form_hooks', 100);

/**
 * Add image/color fields to term add form (different format from edit)
 */
function somnia_add_term_metafield_add_fields($taxonomy)
{
	// Ensure taxonomy is a string
	if (is_array($taxonomy)) {
		return;
	}
	
	$taxonomy = (string) $taxonomy;
	
	if (empty($taxonomy) || strpos($taxonomy, 'pa_') !== 0) {
		return;
	}

	// Get attribute ID from taxonomy
	$attribute_id = wc_attribute_taxonomy_id_by_name(str_replace('pa_', '', $taxonomy));
	if (!$attribute_id) {
		return;
	}

	$attribute_type = get_option('somnia_attribute_type_' . $attribute_id, 'select');

	if ($attribute_type === 'image') {
		?>
		<div class="form-field term-image-wrap">
			<label for="somnia_term_image"><?php esc_html_e('Image', 'somnia'); ?></label>
			<div class="somnia-term-image-wrapper">
				<div class="somnia-term-image-preview" style="margin-bottom: 10px;"></div>
				<input type="hidden" name="somnia_term_image" id="somnia_term_image" value="" />
				<button type="button" class="button somnia-upload-image-button"><?php esc_html_e('Select Image', 'somnia'); ?></button>
				<button type="button" class="button somnia-remove-image-button" style="display:none;"><?php esc_html_e('Remove Image', 'somnia'); ?></button>
				<p class="description"><?php esc_html_e('Select an image for this term.', 'somnia'); ?></p>
			</div>
		</div>
		<?php
	} elseif ($attribute_type === 'color') {
		?>
		<div class="form-field term-color-wrap">
			<label for="somnia_term_color"><?php esc_html_e('Color', 'somnia'); ?></label>
			<input type="text" name="somnia_term_color" id="somnia_term_color" value="" class="somnia-color-picker" />
			<p class="description"><?php esc_html_e('Select a color for this term.', 'somnia'); ?></p>
		</div>
		<?php
	}
}

/**
 * Save term metafields
 */
function somnia_save_term_metafields($term_id, $tt_id = '', $taxonomy = '')
{
	// If taxonomy not passed in args, try to get from POST or from term
	if (empty($taxonomy)) {
		if (isset($_POST['taxonomy'])) {
			$taxonomy = sanitize_text_field($_POST['taxonomy']);
		} else {
			$term = get_term($term_id);
			if ($term && !is_wp_error($term)) {
				$taxonomy = $term->taxonomy;
			}
		}
	}
	
	// WordPress sometimes passes arrays - extract taxonomy string from array
	if (is_array($taxonomy)) {
		if (isset($taxonomy['taxonomy'])) {
			$taxonomy = $taxonomy['taxonomy'];
		} else {
			return;
		}
	}
	
	$taxonomy = (string) $taxonomy;
	
	if (empty($taxonomy) || strpos($taxonomy, 'pa_') !== 0) {
		return;
	}

	// Check if this is an autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}
	
	// Verify this is a POST request
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		return;
	}

	// Get attribute ID
	$attribute_id = wc_attribute_taxonomy_id_by_name(str_replace('pa_', '', $taxonomy));
	if (!$attribute_id) {
		return;
	}

	$attribute_type = get_option('somnia_attribute_type_' . $attribute_id, 'select');

	// Save image
	if ($attribute_type === 'image' && isset($_POST['somnia_term_image'])) {
		$image_id = absint($_POST['somnia_term_image']);
		update_term_meta($term_id, 'somnia_term_image', $image_id);
	}

	// Save color
	if ($attribute_type === 'color' && isset($_POST['somnia_term_color'])) {
		$color_value = sanitize_text_field($_POST['somnia_term_color']);
		update_term_meta($term_id, 'somnia_term_color', $color_value);
	}
}

/**
 * Register save hooks for all product attribute taxonomies
 */
function somnia_register_term_save_hooks()
{
	if (!function_exists('wc_get_attribute_taxonomies')) {
		return;
	}

	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if (empty($attribute_taxonomies)) {
		return;
	}

	foreach ($attribute_taxonomies as $attribute) {
		$taxonomy_name = wc_attribute_taxonomy_name($attribute->attribute_name);
		add_action('edited_' . $taxonomy_name, 'somnia_save_term_metafields', 10, 3);
		add_action('created_' . $taxonomy_name, 'somnia_save_term_metafields', 10, 3);
	}
}
add_action('init', 'somnia_register_term_save_hooks', 100);

/**
 * Enqueue admin scripts for image uploader and color picker
 */
function somnia_enqueue_attribute_admin_scripts($hook)
{
	// Only load on attribute term pages
	if ($hook !== 'edit-tags.php' && $hook !== 'term.php') {
		return;
	}

	// Check if we're on a product attribute taxonomy page
	$screen = get_current_screen();
	if (!$screen || !isset($screen->taxonomy) || strpos($screen->taxonomy, 'pa_') !== 0) {
		return;
	}

	// Enqueue WordPress media uploader
	wp_enqueue_media();

	// Enqueue color picker
	wp_enqueue_style('wp-color-picker');
	wp_enqueue_script('wp-color-picker');

	// Add custom script
	$script = "
	jQuery(document).ready(function($) {
		// Image uploader
		$(document).on('click', '.somnia-upload-image-button', function(e) {
			e.preventDefault();
			var button = $(this);
			var wrapper = button.closest('.somnia-term-image-wrapper');
			var input = wrapper.find('input[type=\"hidden\"]');
			var preview = wrapper.find('.somnia-term-image-preview');
			var removeBtn = wrapper.find('.somnia-remove-image-button');

			var frame = wp.media({
				title: 'Select Image',
				button: {
					text: 'Use this image'
				},
				multiple: false
			});

		frame.on('select', function() {
			var attachment = frame.state().get('selection').first().toJSON();
			input.val(attachment.id);
			preview.html('<img src=\"' + attachment.url + '\" style=\"max-width: 150px; height: auto; display: block;\" />');
			removeBtn.show();
		});

			frame.open();
		});

		// Remove image
		$(document).on('click', '.somnia-remove-image-button', function(e) {
			e.preventDefault();
			var button = $(this);
			var wrapper = button.closest('.somnia-term-image-wrapper');
			var input = wrapper.find('input[type=\"hidden\"]');
			var preview = wrapper.find('.somnia-term-image-preview');
			
			input.val('');
			preview.html('');
			button.hide();
		});

		// Initialize color picker
		function initColorPicker() {
			if ($('.somnia-color-picker').length) {
				$('.somnia-color-picker').each(function() {
					if (!$(this).hasClass('wp-color-picker')) {
						$(this).wpColorPicker({
							change: function(event, ui) {
								$(this).val(ui.color.toString());
							},
							clear: function() {
								$(this).val('');
							}
						});
					}
				});
			}
		}
		
		initColorPicker();
		
		// Function to sync color values
		function syncColorValues() {
			$('.somnia-color-picker').each(function() {
				if ($(this).hasClass('wp-color-picker')) {
					var colorValue = $(this).wpColorPicker('color');
					if (colorValue) {
						$(this).val(colorValue);
					}
				}
			});
		}
		
		// Sync before regular form submit
		$('#edittag, #addtag').on('submit', function(e) {
			syncColorValues();
		});
		
		// Hook into WordPress AJAX for term add/edit
		$(document).ajaxSend(function(event, jqxhr, settings) {
			if (settings.data && (settings.data.indexOf('action=add-tag') !== -1 || settings.data.indexOf('action=inline-save-tax') !== -1)) {
				syncColorValues();
			}
		});
		
		// Reset fields after AJAX term add
		$(document).ajaxComplete(function(event, xhr, settings) {
			if (settings.data && settings.data.indexOf('action=add-tag') !== -1 && xhr.status === 200) {
				setTimeout(function() {
					$('#addtag .somnia-remove-image-button').trigger('click');
					$('#addtag .wp-picker-clear').trigger('click');
					initColorPicker();
				}, 200);
			}
		});
	});
	";

	wp_add_inline_script('wp-color-picker', $script);
}
add_action('admin_enqueue_scripts', 'somnia_enqueue_attribute_admin_scripts');

/**
 * Get attribute type for a taxonomy
 */
function somnia_get_attribute_type($taxonomy_name)
{
	// Remove 'pa_' prefix if present
	$attribute_slug = str_replace('pa_', '', $taxonomy_name);
	$attribute_id = wc_attribute_taxonomy_id_by_name($attribute_slug);

	if ($attribute_id) {
		return get_option('somnia_attribute_type_' . $attribute_id, 'select');
	}

	return 'select';
}

/**
 * Get color taxonomy dynamically by checking attribute type
 */
if (!function_exists('somnia_get_color_taxonomy')) {
	function somnia_get_color_taxonomy()
	{
		static $color_taxonomy = null;

		if ($color_taxonomy !== null) {
			return $color_taxonomy;
		}

		$color_taxonomy = false;
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if (!empty($attribute_taxonomies)) {
			foreach ($attribute_taxonomies as $attribute) {
				$attribute_type = get_option('somnia_attribute_type_' . $attribute->attribute_id, 'select');
				if ($attribute_type === 'color') {
					$color_taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
					break;
				}
			}
		}

		return $color_taxonomy;
	}
}

/**
 * Get image taxonomy dynamically by checking attribute type
 */
if (!function_exists('somnia_get_image_taxonomy')) {
	function somnia_get_image_taxonomy()
	{
		static $image_taxonomy = null;

		if ($image_taxonomy !== null) {
			return $image_taxonomy;
		}

		$image_taxonomy = false;
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if (!empty($attribute_taxonomies)) {
			foreach ($attribute_taxonomies as $attribute) {
				$attribute_type = get_option('somnia_attribute_type_' . $attribute->attribute_id, 'select');
				if ($attribute_type === 'image') {
					$image_taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
					break;
				}
			}
		}

		return $image_taxonomy;
	}
}

/**
 * Check if attribute is select type
 */
if (!function_exists('somnia_is_select_attribute')) {
	function somnia_is_select_attribute($attribute_name)
	{
		// Remove 'pa_' prefix if present
		$attribute_slug = str_replace('pa_', '', $attribute_name);
		$attribute_id = wc_attribute_taxonomy_id_by_name($attribute_slug);

		if ($attribute_id) {
			$attribute_type = get_option('somnia_attribute_type_' . $attribute_id, 'select');
			return $attribute_type === 'select';
		}

		return false;
	}
}
