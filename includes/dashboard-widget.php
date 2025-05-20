<?php
defined('ABSPATH') || exit;

add_action('wp_dashboard_setup', 'bf_register_dashboard_widget');

function bf_register_dashboard_widget() {
    wp_add_dashboard_widget(
        'buildflow_about_widget',
		'About BuildFlow',
        'bf_render_dashboard_widget'
    );
}

function bf_render_dashboard_widget() {
    echo '<p><strong>BuildFlow</strong> helps you manage materials, recipes, and batch production for WooCommerce products.</p>';
    echo '<ul style="margin-left: 1em;">';
    echo '<li>📦 <a href="' . admin_url('edit.php?post_type=product') . '">View Products</a></li>';
    echo '<li>🧪 <a href="' . admin_url('admin.php?page=buildflow-dashboard') . '">Recipe & Inventory Dashboard</a></li>';
    echo '<li>📁 <a href="' . admin_url('edit.php?post_type=bf_material') . '">Manage Materials</a></li>';
    echo '<li>⚙️ <a href="' . admin_url('admin.php?page=buildflow-settings') . '">Plugin Settings</a></li>';
    echo '</ul>';
    echo '<p style="font-size: 12px; color: #666;">BuildFlow v1.0.0 • Built for handmade and small-batch product makers.</p>';
}
