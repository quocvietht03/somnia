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
