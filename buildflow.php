<?php
/**
 * Plugin Name: BuildFlow
 * Description: Manage materials, inventory, and batch production for handmade or small-batch goods.
 * Version: 1.0.0
 * Author: Reborn Core Interactive
 * License: GPL2+
 * Text Domain: buildflow
 */

defined('ABSPATH') || exit;

define('BF_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once plugin_dir_path(__FILE__) . 'includes/admin/menu.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-batches.php';
require_once BF_PLUGIN_DIR . 'includes/cpt-materials.php';
require_once plugin_dir_path(__FILE__) . 'includes/woocommerce-recipe-info.php';
require_once plugin_dir_path(__FILE__) . 'includes/dashboard-widget.php';
require_once BF_PLUGIN_DIR . 'includes/admin/edit-material.php';
require_once BF_PLUGIN_DIR . 'includes/helpers.php';

function bf_activate() {
    bf_register_materials_cpt();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'bf_activate');

function bf_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'bf_deactivate');
