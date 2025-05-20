<?php
defined('ABSPATH') || exit;

add_action('add_meta_boxes', 'bf_add_wc_product_recipe_status');
function bf_add_wc_product_recipe_status() {
    add_meta_box(
        'bf_wc_recipe_status',
        'BuildFlow Recipe & Stock Status',
        'bf_render_wc_recipe_status_box',
        'product',
        'side',
        'high'
    );
}

function bf_render_wc_recipe_status_box($post) {
    $recipe = get_post_meta($post->ID, '_bf_recipe', true);

    if (empty($recipe)) {
        echo '<p><strong>No BuildFlow recipe found.</strong></p>';
        echo '<p><a href="#bf_recipe_meta">Scroll down to create one ↓</a></p>';
        return;
    }

    echo '<p><strong>Recipe Materials:</strong></p>';
    echo '<table class="widefat"><thead><tr><th>Material</th><th>Need</th><th>Stock</th><th>Status</th></tr></thead><tbody>';

    foreach ($recipe as $item) {
        $mat_id = $item['material'] ?? null;
        $qty_needed = floatval($item['qty'] ?? 0);
        $unit = $item['unit'] ?? '';
        $material = get_post($mat_id);

        if (!$material) continue;

        $stock = floatval(get_post_meta($mat_id, '_bf_stock_level', true));

        $status = $stock >= $qty_needed
            ? '<span style="color:green;">✔</span>'
            : '<span style="color:red;">✖</span>';

        echo '<tr>';
        echo '<td>' . esc_html($material->post_title) . '</td>';
        echo '<td>' . esc_html($qty_needed) . ' ' . esc_html($unit) . '</td>';
        echo '<td>' . esc_html($stock) . '</td>';
        echo '<td>' . $status . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}
