<?php
defined('ABSPATH') || exit;

require_once __DIR__ . '/dashboard.php';
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/add-material.php';
require_once __DIR__ . '/edit-material.php';

function bf_register_admin_menu() {
    add_menu_page(
        'BuildFlow',
        'BuildFlow',
        'manage_options',
        'buildflow-dashboard',
        'bf_render_dashboard',
        'dashicons-hammer',
        25
    );

    add_submenu_page(
        'buildflow-dashboard',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'buildflow-dashboard',
        'bf_render_dashboard'
    );

    add_submenu_page(
        'buildflow-dashboard',
        'Materials',
        'Materials',
        'edit_posts',
        'edit.php?post_type=bf_material'
    );

    add_submenu_page(
        'buildflow-dashboard',
        'Add Material',
        'Add Material',
        'manage_options',
        'buildflow-add-material',
        'bf_render_add_material_page'
    );

    add_submenu_page(
        'buildflow-dashboard',
        'Settings',
        'Settings',
        'manage_options',
        'buildflow-settings',
        'bf_render_settings_page'
    );

    // Hidden custom edit page for materials
    add_submenu_page(
        null,
        'Edit Material',
        'Edit Material',
        'manage_options',
        'buildflow-edit-material',
        'bf_render_edit_material_page'
    );
}
add_action('admin_menu', 'bf_register_admin_menu');
