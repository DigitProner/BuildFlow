<?php
defined('ABSPATH') || exit;

function bf_register_batches_cpt() {
    $labels = [
        'name' => 'Material Batches',
        'singular_name' => 'Material Batch',
        'menu_name' => 'Batches',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Batch',
        'edit_item' => 'Edit Batch',
        'new_item' => 'New Batch',
        'view_item' => 'View Batch',
        'search_items' => 'Search Batches',
        'not_found' => 'No batches found',
        'not_found_in_trash' => 'No batches found in Trash',
    ];

    $args = [
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'menu_icon' => 'dashicons-clipboard',
        'supports' => ['title', 'editor'],
        'has_archive' => false,
        'show_in_rest' => false,
    ];

    register_post_type('bf_batch', $args);
}
add_action('init', 'bf_register_batches_cpt');
