<?php
/**
 * Plugin Name: BuildFlow
 * Plugin URI: https://reborncore.com
 * Description: Inventory and production management system for WooCommerce with recipe, material, and batch tracking.
 * Version: 1.0.0
 * Author: Reborn Core Interactive
 * Author URI: https://reborncore.com
 * License: Proprietary
 * License URI: https://reborncore.com/license
 * Text Domain: buildflow
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

define('BUILDFLOW_PRO', true); // Toggle to false if Pro features should be disabled

// Admin Pages
require_once plugin_dir_path(__FILE__) . 'includes/admin/menu.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/dashboard.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/add-material.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/edit-material.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-batches.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-materials.php';


// WooCommerce Integration
require_once plugin_dir_path(__FILE__) . 'includes/woo/woocommerce-recipe-info.php';

// Load Pro Features if enabled
if (defined('BUILDFLOW_PRO') && BUILDFLOW_PRO) {
    $pro_file = plugin_dir_path(__FILE__) . 'includes/pro/pro-batch-reports.php';
    if (file_exists($pro_file)) {
        require_once $pro_file;
    }
}
