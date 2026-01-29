<?php

/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined('ABSPATH') || exit;

// Check if on shop page or category page
$is_shop_page = is_shop();
$is_category_page = somnia_is_category_archive_page();

// If not shop or category page, force no sidebar
if (!$is_shop_page && !$is_category_page) {
	get_template_part('woocommerce/template','nosidebar-popup');
	return;
}

$archive_shop = get_field('archive_shop', 'options');
$sidebar_shop = isset($archive_shop['sidebar_shop']) ? $archive_shop['sidebar_shop'] : 'sidebar';
$filter_shop = isset($archive_shop['filter_shop']) ? $archive_shop['filter_shop'] : 'filter-dropdown';
if ($sidebar_shop == 'nosidebar') {
	if ($filter_shop == 'filter-popup') {
		get_template_part('woocommerce/template','nosidebar-popup');
	} else {
		get_template_part('woocommerce/template','nosidebar-dropdown');
	}
} else {
	get_template_part('woocommerce/template','sidebar');
}
