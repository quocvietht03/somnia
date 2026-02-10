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
	$enable_search = false; // Default value
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
	<div class="form-field">
		<label for="somnia_enable_search">
			<input name="somnia_enable_search" id="somnia_enable_search" type="checkbox" value="1" <?php checked($enable_search, true); ?> />
			<?php esc_html_e('Enable in Search', 'somnia'); ?>
		</label>
		<p class="description"><?php esc_html_e('Enable this attribute to be displayed in search/filter fields.', 'somnia'); ?></p>
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
	$enable_search = get_option('somnia_enable_search_' . $edit, false);
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
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="somnia_enable_search"><?php esc_html_e('Enable in Search', 'somnia'); ?></label>
		</th>
		<td>
			<label for="somnia_enable_search">
				<input name="somnia_enable_search" id="somnia_enable_search" type="checkbox" value="1" <?php checked($enable_search, true); ?> />
				<?php esc_html_e('Enable in Search', 'somnia'); ?>
			</label>
			<p class="description"><?php esc_html_e('Enable this attribute to be displayed in search/filter fields.', 'somnia'); ?></p>
		</td>
	</tr>
	<?php
}
add_action('woocommerce_after_edit_attribute_fields', 'somnia_edit_attribute_type_field');

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

	// Save enable search option
	$enable_search = isset($_POST['somnia_enable_search']) && $_POST['somnia_enable_search'] == '1' ? true : false;
	update_option('somnia_enable_search_' . $attribute_id, $enable_search);
	
	// Clear static cache for color taxonomies and enabled attributes
	if (function_exists('somnia_get_all_color_taxonomies')) {
		// Force refresh by clearing static cache
		$reflection = new ReflectionFunction('somnia_get_all_color_taxonomies');
		$static_vars = $reflection->getStaticVariables();
	}
	
	// Update ACF JSON file with enabled attributes
	somnia_update_acf_filter_choices();
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

	// Save enable search option
	$enable_search = isset($_POST['somnia_enable_search']) && $_POST['somnia_enable_search'] == '1' ? true : false;
	update_option('somnia_enable_search_' . $attribute_id, $enable_search);
	
	// Update ACF JSON file with enabled attributes
	somnia_update_acf_filter_choices();
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
 * Get all color taxonomies dynamically by checking attribute type
 * Only returns taxonomies if enabled in search
 */
if (!function_exists('somnia_get_all_color_taxonomies')) {
	function somnia_get_all_color_taxonomies()
	{
		static $color_taxonomies = null;

		if ($color_taxonomies !== null) {
			return $color_taxonomies;
		}

		$color_taxonomies = array();
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if (!empty($attribute_taxonomies)) {
			foreach ($attribute_taxonomies as $attribute) {
				$attribute_type = get_option('somnia_attribute_type_' . $attribute->attribute_id, 'select');
				$enable_search = get_option('somnia_enable_search_' . $attribute->attribute_id, false);
				if ($attribute_type === 'color' && $enable_search) {
					$color_taxonomies[] = wc_attribute_taxonomy_name($attribute->attribute_name);
				}
			}
		}

		return $color_taxonomies;
	}
}

/**
 * Get color taxonomy dynamically by checking attribute type
 * Only returns first taxonomy if enabled in search (for backward compatibility)
 */
if (!function_exists('somnia_get_color_taxonomy')) {
	function somnia_get_color_taxonomy()
	{
		static $color_taxonomy = null;

		if ($color_taxonomy !== null) {
			return $color_taxonomy;
		}

		$color_taxonomies = somnia_get_all_color_taxonomies();
		$color_taxonomy = !empty($color_taxonomies) ? $color_taxonomies[0] : false;

		return $color_taxonomy;
	}
}

/**
 * Get all enabled search attributes (excluding color)
 * Returns array of taxonomy names with their attribute types
 */
if (!function_exists('somnia_get_all_enabled_search_attributes')) {
	function somnia_get_all_enabled_search_attributes()
	{
		static $enabled_attributes = null;

		if ($enabled_attributes !== null) {
			return $enabled_attributes;
		}

		$enabled_attributes = array();
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if (!empty($attribute_taxonomies)) {
			foreach ($attribute_taxonomies as $attribute) {
				$attribute_type = get_option('somnia_attribute_type_' . $attribute->attribute_id, 'select');
				$enable_search = get_option('somnia_enable_search_' . $attribute->attribute_id, false);
				
				// Only include non-color attributes that are enabled in search
				if ($attribute_type !== 'color' && $enable_search) {
					$taxonomy_name = wc_attribute_taxonomy_name($attribute->attribute_name);
					$enabled_attributes[] = array(
						'taxonomy' => $taxonomy_name,
						'type' => $attribute_type,
						'attribute_id' => $attribute->attribute_id
					);
				}
			}
		}

		return $enabled_attributes;
	}
}

/**
 * Check if attribute is enabled in search
 */
if (!function_exists('somnia_is_attribute_enabled_in_search')) {
	function somnia_is_attribute_enabled_in_search($attribute_name_or_id)
	{
		$attribute_id = 0;
		
		// If it's a numeric ID, use it directly
		if (is_numeric($attribute_name_or_id)) {
			$attribute_id = absint($attribute_name_or_id);
		} else {
			// Otherwise, treat it as attribute name/slug
			$attribute_slug = str_replace('pa_', '', $attribute_name_or_id);
			$attribute_id = wc_attribute_taxonomy_id_by_name($attribute_slug);
		}

	if ($attribute_id) {
		return (bool) get_option('somnia_enable_search_' . $attribute_id, false);
	}

	return false;
	}
}

/**
 * Update ACF JSON file with enabled search attributes
 * Automatically adds/removes attributes from filter choices
 */
if (!function_exists('somnia_update_acf_filter_choices')) {
	function somnia_update_acf_filter_choices()
	{
		$acf_json_path = get_template_directory() . '/framework/acf-options/group_6530e5259c0aa.json';
		
		if (!file_exists($acf_json_path)) {
			return false;
		}
		
		// Read JSON file
		$json_content = file_get_contents($acf_json_path);
		if ($json_content === false) {
			return false;
		}
		
		$acf_data = json_decode($json_content, true);
		if (!$acf_data || !isset($acf_data['fields'])) {
			return false;
		}
		
		// Find the field with key "field_68f5e9b6ea2f3" (Item Filters)
		$field_found = false;
		somnia_find_and_update_field($acf_data['fields'], 'field_68f5e9b6ea2f3', $field_found);
		
		if (!$field_found) {
			return false;
		}
		
		// Save updated JSON
		$updated_json = json_encode($acf_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		if ($updated_json === false) {
			return false;
		}
		
		// Write back to file
		$result = file_put_contents($acf_json_path, $updated_json);
		
		return $result !== false;
	}
}

/**
 * Sync ACF JSON file on admin init to ensure all enabled attributes are included
 */
if (!function_exists('somnia_sync_acf_filter_choices_on_init')) {
	function somnia_sync_acf_filter_choices_on_init()
	{
		// Only run in admin and when saving/updating attributes
		if (!is_admin()) {
			return;
		}
		
		// Check if we're on the attributes page
		$screen = get_current_screen();
		if ($screen && $screen->id === 'product_page_product_attributes') {
			// Sync when on attributes page
			somnia_update_acf_filter_choices();
		}
	}
	add_action('admin_init', 'somnia_sync_acf_filter_choices_on_init');
}

/**
 * Recursively find and update field in ACF structure
 */
if (!function_exists('somnia_find_and_update_field')) {
	function somnia_find_and_update_field(&$fields, $target_key, &$found)
	{
		if ($found) {
			return;
		}
		
		foreach ($fields as &$field) {
			if (isset($field['key']) && $field['key'] === $target_key) {
				// Found the field, update choices
				$found = true;
				
				// Get base choices (static ones)
				$base_choices = array(
					'search_form' => 'Search Form',
					'categories' => 'Product Categories',
					'brand' => 'Brand',
					'comfort_scale' => 'Comfort Scale',
					'mattress_type' => 'Mattress Type',
					'price' => 'Price',
					'customer_rating' => 'Customer Rating',
					'text_editor' => 'Text Editor'
				);
				
				// Get all enabled WooCommerce attributes (including color)
				// Force fresh data by getting directly from options
				$enabled_attributes = array();
				$color_taxonomies = array();
				$attribute_taxonomies = wc_get_attribute_taxonomies();
				
				if (!empty($attribute_taxonomies)) {
					foreach ($attribute_taxonomies as $attribute) {
						$attribute_type = get_option('somnia_attribute_type_' . $attribute->attribute_id, 'select');
						$enable_search = get_option('somnia_enable_search_' . $attribute->attribute_id, false);
						
						if ($enable_search) {
							$taxonomy_name = wc_attribute_taxonomy_name($attribute->attribute_name);
							
							if ($attribute_type === 'color') {
								$color_taxonomies[] = $taxonomy_name;
							} else {
								$enabled_attributes[] = array(
									'taxonomy' => $taxonomy_name,
									'type' => $attribute_type,
									'attribute_id' => $attribute->attribute_id
								);
							}
						}
					}
				}
				
				$attribute_choices = array();
				
				// Add non-color attributes
				foreach ($enabled_attributes as $attr_data) {
					$taxonomy = $attr_data['taxonomy'];
					$attribute_id = $attr_data['attribute_id'];
					
					// Get attribute label
					$attribute_taxonomies = wc_get_attribute_taxonomies();
					$attribute_label = $taxonomy;
					
					if (!empty($attribute_taxonomies)) {
						foreach ($attribute_taxonomies as $attr) {
							if ($attr->attribute_id == $attribute_id && !empty($attr->attribute_label)) {
								$attribute_label = $attr->attribute_label;
								break;
							}
						}
					}
					
					// Use taxonomy name as key, label as value
					$attribute_choices[$taxonomy] = $attribute_label;
				}
				
				// Add color attributes
				if (!empty($color_taxonomies)) {
					foreach ($color_taxonomies as $color_taxonomy) {
						$attribute_id = wc_attribute_taxonomy_id_by_name(str_replace('pa_', '', $color_taxonomy));
						$attribute_taxonomies = wc_get_attribute_taxonomies();
						$attribute_label = function_exists('wc_attribute_label') ? wc_attribute_label($color_taxonomy) : $color_taxonomy;
						
						if ($attribute_id && !empty($attribute_taxonomies)) {
							foreach ($attribute_taxonomies as $attr) {
								if ($attr->attribute_id == $attribute_id && !empty($attr->attribute_label)) {
									$attribute_label = $attr->attribute_label;
									break;
								}
							}
						}
						
						// Use taxonomy name as key, label as value
						$attribute_choices[$color_taxonomy] = $attribute_label;
					}
				}
				
				// Merge base choices with attribute choices
				$field['choices'] = array_merge($base_choices, $attribute_choices);
				
				return;
			}
			
			// Check sub_fields if exists
			if (isset($field['sub_fields']) && is_array($field['sub_fields'])) {
				somnia_find_and_update_field($field['sub_fields'], $target_key, $found);
			}
			
			// Check fields if exists (for groups)
			if (isset($field['fields']) && is_array($field['fields'])) {
				somnia_find_and_update_field($field['fields'], $target_key, $found);
			}
		}
	}
}

/**
 * Render BT attributes wrap content for variable product (image/color/select/button swatches).
 * Hook: somnia_bt_attributes_wrap — called from variable.php with $attributes, $product, $available_variations.
 */
if (!function_exists('somnia_woocommerce_custom_attributes')) {
	function somnia_woocommerce_custom_attributes($attributes, $product, $available_variations)
	{
		// Helper to check if an option is available based on selected attributes
		$check_option_availability = function ($option_value, $current_attribute_name, $all_attributes, $selected_attributes, $available_variations) {
			if (!is_array($available_variations) || empty($available_variations)) {
				return true;
			}
			$test_attributes = $selected_attributes;
			unset($test_attributes[$current_attribute_name]);
			$test_attributes[$current_attribute_name] = $option_value;
			$current_attr_key = 'attribute_' . sanitize_title($current_attribute_name);

			foreach ($available_variations as $variation) {
				if (!isset($variation['attributes'])) {
					continue;
				}
				$variation_attrs = $variation['attributes'];
				$matches = true;
				$variation_current_value = isset($variation_attrs[$current_attr_key]) ? $variation_attrs[$current_attr_key] : '';
				if ($variation_current_value !== '' && $variation_current_value !== $option_value) {
					continue;
				}
				foreach ($test_attributes as $attr_name => $test_value) {
					if ($attr_name === $current_attribute_name || $test_value === '') {
						continue;
					}
					$attr_key = 'attribute_' . sanitize_title($attr_name);
					$variation_value = isset($variation_attrs[$attr_key]) ? $variation_attrs[$attr_key] : '';
					if ($variation_value !== '' && $variation_value !== $test_value) {
						$matches = false;
						break;
					}
				}
				if ($matches) {
					return true;
				}
			}
			return false;
		};

		$selected_attributes = array();
		foreach ($attributes as $attr_name => $attr_options) {
			$attr_slug = sanitize_title($attr_name);
			$selected_attr = isset($_REQUEST['attribute_' . $attr_slug])
				? wc_clean(wp_unslash($_REQUEST['attribute_' . $attr_slug]))
				: $product->get_variation_default_attribute($attr_name);
			if ($selected_attr) {
				$selected_attributes[$attr_name] = $selected_attr;
			}
		}
		?>
		<div class="bt-attributes-wrap">
		<?php
		foreach ($attributes as $attribute_name => $options) {
			$data_attribute = strtolower($attribute_name);
			$data_attribute_slug = sanitize_title($attribute_name);
			$selected_value = isset($_REQUEST['attribute_' . $data_attribute_slug]) ? wc_clean(wp_unslash($_REQUEST['attribute_' . $data_attribute_slug])) : $product->get_variation_default_attribute($attribute_name);
			$is_size_attr = (strpos(strtolower($attribute_name), 'size') !== false);
			$attr_type = function_exists('somnia_get_attribute_type') ? somnia_get_attribute_type($attribute_name) : 'select';
			$is_image_attr = ($attr_type === 'image');
			$is_color_attr = ($attr_type === 'color');
			$is_select_attr = ($attr_type === 'select');
			$attr_class = in_array($attr_type, array('image', 'color', 'select'), true) ? ' bt-is-' . $attr_type . '-attribute' : '';
			?>
			<div class="bt-attributes--item<?php echo esc_attr($attr_class); ?>" data-attribute-name="<?php echo esc_attr($data_attribute_slug); ?>">
				<div class="bt-attributes--name">
					<div class="bt-name"><?php echo wc_attribute_label($attribute_name) . ':'; ?></div>
					<div class="bt-result"></div>
					<?php
					if ($is_size_attr && is_product()) {
						$enable_size_guide = get_post_meta($product->get_id(), '_enable_size_guide', true);
						$size_guide = get_field('size_guide', 'option');
						if ($enable_size_guide === 'yes' && !empty($size_guide)) {
					?>
							<div class="bt-size-guide-wrapper bt-inline-position">
								<a href="#bt-size-guide-popup" class="bt-size-guide-button bt-js-open-popup-link">
									<?php echo esc_html__('Size Guide', 'somnia'); ?>
								</a>
							</div>
					<?php
						}
					}
					?>
				</div>
				<?php
				$ordered_options = $options;
				if ($product && taxonomy_exists($attribute_name)) {
					$terms = wc_get_product_terms(
						$product->get_id(),
						$attribute_name,
						array('fields' => 'all')
					);
					$ordered_options = array();
					foreach ($terms as $term) {
						if (in_array($term->slug, $options, true)) {
							$ordered_options[] = $term->slug;
						}
					}
				}

				if ($is_image_attr) { ?>
					<div class="bt-attributes--value bt-value-image">
						<?php
						foreach ($ordered_options as $option) :
							$term = get_term_by('slug', $option, $attribute_name);
							$term_id = $term ? $term->term_id : '';
							$image_id = $term_id ? get_term_meta($term_id, 'somnia_term_image', true) : 0;
							$image_id = absint($image_id);
							$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
							$is_selected = ($selected_value === $option);
							$class_active = $is_selected ? ' active' : '';
							$is_available = $check_option_availability($option, $attribute_name, $attributes, $selected_attributes, $available_variations);
							$class_disabled = !$is_available ? ' disabled' : '';
						?>
							<div class="bt-js-item bt-item-image<?php echo esc_attr($class_active . $class_disabled); ?>" data-value="<?php echo esc_attr($option); ?>">
								<div class="bt-image">
									<?php if ($image_url) : ?>
										<span style="background-image: url('<?php echo esc_url($image_url); ?>');">
											<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
												<path d="M16 3C13.4288 3 10.9154 3.76244 8.77759 5.19089C6.63975 6.61935 4.97351 8.64968 3.98957 11.0251C3.00563 13.4006 2.74819 16.0144 3.2498 18.5362C3.75141 21.0579 4.98953 23.3743 6.80762 25.1924C8.6257 27.0105 10.9421 28.2486 13.4638 28.7502C15.9856 29.2518 18.5994 28.9944 20.9749 28.0104C23.3503 27.0265 25.3807 25.3603 26.8091 23.2224C28.2376 21.0846 29 18.5712 29 16C28.9964 12.5533 27.6256 9.24882 25.1884 6.81163C22.7512 4.37445 19.4467 3.00364 16 3ZM21.7075 13.7075L14.7075 20.7075C14.6146 20.8005 14.5043 20.8742 14.3829 20.9246C14.2615 20.9749 14.1314 21.0008 14 21.0008C13.8686 21.0008 13.7385 20.9749 13.6171 20.9246C13.4957 20.8742 13.3854 20.8005 13.2925 20.7075L10.2925 17.7075C10.1049 17.5199 9.99945 17.2654 9.99945 17C9.99945 16.7346 10.1049 16.4801 10.2925 16.2925C10.4801 16.1049 10.7346 15.9994 11 15.9994C11.2654 15.9994 11.5199 16.1049 11.7075 16.2925L14 18.5862L20.2925 12.2925C20.3854 12.1996 20.4957 12.1259 20.6171 12.0756C20.7385 12.0253 20.8686 11.9994 21 11.9994C21.1314 11.9994 21.2615 12.0253 21.3829 12.0756C21.5043 12.1259 21.6146 12.1996 21.7075 12.2925C21.8004 12.3854 21.8741 12.4957 21.9244 12.6171C21.9747 12.7385 22.0006 12.8686 22.0006 13C22.0006 13.1314 21.9747 13.2615 21.9244 13.3829C21.8741 13.5043 21.8004 13.6146 21.7075 13.7075Z" fill="white" />
											</svg>
										</span>
									<?php else : ?>
										<span style="background-color: #e5e7eb;">
											<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
												<path d="M16 3C13.4288 3 10.9154 3.76244 8.77759 5.19089C6.63975 6.61935 4.97351 8.64968 3.98957 11.0251C3.00563 13.4006 2.74819 16.0144 3.2498 18.5362C3.75141 21.0579 4.98953 23.3743 6.80762 25.1924C8.6257 27.0105 10.9421 28.2486 13.4638 28.7502C15.9856 29.2518 18.5994 28.9944 20.9749 28.0104C23.3503 27.0265 25.3807 25.3603 26.8091 23.2224C28.2376 21.0846 29 18.5712 29 16C28.9964 12.5533 27.6256 9.24882 25.1884 6.81163C22.7512 4.37445 19.4467 3.00364 16 3ZM21.7075 13.7075L14.7075 20.7075C14.6146 20.8005 14.5043 20.8742 14.3829 20.9246C14.2615 20.9749 14.1314 21.0008 14 21.0008C13.8686 21.0008 13.7385 20.9749 13.6171 20.9246C13.4957 20.8742 13.3854 20.8005 13.2925 20.7075L10.2925 17.7075C10.1049 17.5199 9.99945 17.2654 9.99945 17C9.99945 16.7346 10.1049 16.4801 10.2925 16.2925C10.4801 16.1049 10.7346 15.9994 11 15.9994C11.2654 15.9994 11.5199 16.1049 11.7075 16.2925L14 18.5862L20.2925 12.2925C20.3854 12.1996 20.4957 12.1259 20.6171 12.0756C20.7385 12.0253 20.8686 11.9994 21 11.9994C21.1314 11.9994 21.2615 12.0253 21.3829 12.0756C21.5043 12.1259 21.6146 12.1996 21.7075 12.2925C21.8004 12.3854 21.8741 12.4957 21.9244 12.6171C21.9747 12.7385 22.0006 12.8686 22.0006 13C22.0006 13.1314 21.9747 13.2615 21.9244 13.3829C21.8741 13.5043 21.8004 13.6146 21.7075 13.7075Z" fill="white" />
											</svg>
										</span>
									<?php endif; ?>
								</div>
								<label><?php echo esc_html($term ? $term->name : $option); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				<?php } elseif ($is_color_attr) { ?>
					<div class="bt-attributes--value bt-value-color">
						<?php
						foreach ($ordered_options as $option) :
							$term = get_term_by('slug', $option, $attribute_name);
							$term_id = $term ? $term->term_id : '';
							$color = $term_id ? get_term_meta($term_id, 'somnia_term_color', true) : '';
							if (!$color) {
								$color = $option;
							}
							$is_selected = ($selected_value === $option);
							$class_active = $is_selected ? ' active' : '';
							$is_available = $check_option_availability($option, $attribute_name, $attributes, $selected_attributes, $available_variations);
							$class_disabled = !$is_available ? ' disabled' : '';
						?>
							<div class="bt-js-item bt-item-color<?php echo esc_attr($class_active . $class_disabled); ?>" data-value="<?php echo esc_attr($option); ?>">
								<div class="bt-color">
									<span style="background-color: <?php echo esc_attr($color); ?>;">
										<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
											<path d="M16 3C13.4288 3 10.9154 3.76244 8.77759 5.19089C6.63975 6.61935 4.97351 8.64968 3.98957 11.0251C3.00563 13.4006 2.74819 16.0144 3.2498 18.5362C3.75141 21.0579 4.98953 23.3743 6.80762 25.1924C8.6257 27.0105 10.9421 28.2486 13.4638 28.7502C15.9856 29.2518 18.5994 28.9944 20.9749 28.0104C23.3503 27.0265 25.3807 25.3603 26.8091 23.2224C28.2376 21.0846 29 18.5712 29 16C28.9964 12.5533 27.6256 9.24882 25.1884 6.81163C22.7512 4.37445 19.4467 3.00364 16 3ZM21.7075 13.7075L14.7075 20.7075C14.6146 20.8005 14.5043 20.8742 14.3829 20.9246C14.2615 20.9749 14.1314 21.0008 14 21.0008C13.8686 21.0008 13.7385 20.9749 13.6171 20.9246C13.4957 20.8742 13.3854 20.8005 13.2925 20.7075L10.2925 17.7075C10.1049 17.5199 9.99945 17.2654 9.99945 17C9.99945 16.7346 10.1049 16.4801 10.2925 16.2925C10.4801 16.1049 10.7346 15.9994 11 15.9994C11.2654 15.9994 11.5199 16.1049 11.7075 16.2925L14 18.5862L20.2925 12.2925C20.3854 12.1996 20.4957 12.1259 20.6171 12.0756C20.7385 12.0253 20.8686 11.9994 21 11.9994C21.1314 11.9994 21.2615 12.0253 21.3829 12.0756C21.5043 12.1259 21.6146 12.1996 21.7075 12.2925C21.8004 12.3854 21.8741 12.4957 21.9244 12.6171C21.9747 12.7385 22.0006 12.8686 22.0006 13C22.0006 13.1314 21.9747 13.2615 21.9244 13.3829C21.8741 13.5043 21.8004 13.6146 21.7075 13.7075Z" fill="white" />
										</svg>
									</span>
								</div>
								<label><?php echo esc_html($term ? $term->name : $option); ?></label>
							</div>
						<?php endforeach; ?>
					</div>
				<?php } elseif ($is_select_attr) { ?>
					<div class="bt-attributes--value bt-value-select">
						<select class="bt-js-select" data-attribute="<?php echo esc_attr($data_attribute_slug); ?>">
							<option value=""><?php echo esc_html__('Choose an option', 'somnia'); ?></option>
							<?php
							foreach ($ordered_options as $option) :
								$term = get_term_by('slug', $option, $attribute_name);
								$display_name = $term ? $term->name : $option;
								$display_desc = $term ? $term->description : '';
								$is_selected = ($selected_value === $option);
								$is_available = $check_option_availability($option, $attribute_name, $attributes, $selected_attributes, $available_variations);
							?>
								<option
									value="<?php echo esc_attr($option); ?>"
									<?php selected($is_selected, true); ?>
									<?php disabled(!$is_available, true); ?>
								>
									<?php echo esc_html($display_name); ?>
									<?php if (!empty($display_desc)) echo '<span class="bt-item-desc">' . esc_html($display_desc) . '</span>'; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php } else { ?>
					<div class="bt-attributes--value">
						<?php foreach ($ordered_options as $option) : ?>
							<?php
							$term = get_term_by('slug', $option, $attribute_name);
							$display_name = $term ? $term->name : $option;
							$display_desc = $term ? $term->description : '';
							$is_selected = ($selected_value === $option);
							$class_active = $is_selected ? ' active' : '';
							$is_available = $check_option_availability($option, $attribute_name, $attributes, $selected_attributes, $available_variations);
							$class_disabled = !$is_available ? ' disabled' : '';
							?>
							<span class="bt-js-item bt-item-value<?php echo esc_attr($class_active . $class_disabled); ?>" data-value="<?php echo esc_attr($option); ?>">
								<?php
								echo esc_html($display_name);
								if (!empty($display_desc)) echo '<span class="bt-item-desc">' . esc_html($display_desc) . '</span>';
								?>
							</span>
						<?php endforeach; ?>
					</div>
				<?php } ?>
			</div>
		<?php
		}
		?>
		</div>
		<?php
	}
	add_action('somnia_woocommerce_custom_attributes', 'somnia_woocommerce_custom_attributes', 10, 3);	
}

