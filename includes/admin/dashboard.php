<?php
defined('ABSPATH') || exit;

function bf_render_dashboard() {
    $materials = get_posts(['post_type' => 'bf_material', 'numberposts' => -1]);
    $products = get_posts(['post_type' => 'product', 'numberposts' => -1]);

    $usage_count = [];
    foreach ($products as $product) {
        $recipe = get_post_meta($product->ID, '_bf_recipe', true);
		if (is_array($recipe)) {
			foreach ($recipe as $item) {
				$mid = $item['material'] ?? null;
				if ($mid) {
					$usage_count[$mid] = ($usage_count[$mid] ?? 0) + 1;
				}
			}
		}	
    }

    $low_stock = [];
    foreach ($materials as $mat) {
        $stock = get_post_meta($mat->ID, '_bf_stock_level', true) ?: 0;
        $min = get_post_meta($mat->ID, '_bf_min_stock', true) ?: 0;
        if ($stock < $min) {
            $low_stock[] = ['name' => $mat->post_title, 'stock' => $stock];
        }
    }

    $recent = array_slice($materials, 0, 5);

    echo '<div class="wrap">';
    echo '<div class="bf-actions" style="margin: 20px 0;">';
    $buttons = [
        ['Add Material', 'admin.php?page=buildflow-add-material', 'button-primary'],
        ['View Materials', 'edit.php?post_type=bf_material', ''],
        ['Add Product', 'post-new.php?post_type=product', 'button-primary'],
        ['View Products', 'edit.php?post_type=product', ''],
        ['Settings', 'admin.php?page=buildflow-settings', '']
    ];
    foreach ($buttons as [$label, $url, $class]) {
        echo '<a href="' . admin_url($url) . '" class="button ' . $class . '" style="margin-right: 10px;">' . esc_html($label) . '</a>';
    }
    echo '</div>';

    echo '<h1>📊 BuildFlow Dashboard</h1>';
    echo '<style>
        .bf-cards { display: flex; gap: 20px; margin: 20px 0; }
        .bf-card { flex: 1; padding: 20px; background: #fff; border-left: 4px solid #0073aa; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .bf-section { margin-top: 30px; }
        .bf-section h2 { border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .bf-list li { margin: 5px 0; }
        .bf-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .bf-table th, .bf-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>';

    echo '<div class="bf-cards">';
    echo '<div class="bf-card"><strong>Total Materials</strong><br><span style="font-size: 24px;">' . count($materials) . '</span></div>';
    echo '<div class="bf-card"><strong>Total Products</strong><br><span style="font-size: 24px;">' . count($products) . '</span></div>';
    echo '<div class="bf-card"><strong>Low Stock Materials</strong><br><span style="font-size: 24px; color: red;">' . count($low_stock) . '</span></div>';
    echo '</div>';

    echo '<div class="bf-section"><h2>⚠️ Low Stock Materials</h2>';
    if (count($low_stock)) {
        echo '<table class="bf-table"><thead><tr><th>Material</th><th>Stock Level</th></tr></thead><tbody>';
        foreach ($low_stock as $item) {
            echo '<tr><td>' . esc_html($item['name']) . '</td><td>' . esc_html($item['stock']) . '</td></tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>All materials are above minimum stock levels ✅</p>';
    }
    echo '</div>';

    echo '<div class="bf-section"><h2>⭐ Most Used Materials</h2>';
    if (!empty($usage_count)) {
        arsort($usage_count);
        echo '<ul class="bf-list">';
        foreach (array_slice($usage_count, 0, 5, true) as $mid => $count) {
            echo '<li>' . esc_html(get_the_title($mid)) . ' — used in ' . $count . ' product' . ($count > 1 ? 's' : '') . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No recipes found yet.</p>';
    }
    echo '</div>';

    echo '<div class="bf-section"><h2>📥 Recently Added Materials</h2>';
    if (count($recent)) {
        echo '<ul class="bf-list">';
        foreach ($recent as $mat) {
            $date = get_the_date('M j, Y', $mat);
            echo '<li>' . esc_html($mat->post_title) . ' — ' . esc_html($date) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No materials added yet.</p>';
    }

    echo '<div class="bf-section"><h2>🔎 About BuildFlow</h2>';
    echo '<p><strong>BuildFlow</strong> helps you manage materials, recipes, and batch production for your WooCommerce products.</p>';
    echo '<ul class="bf-list">';
    echo '<li>📦 Products via WooCommerce</li>';
    echo '<li>🧪 Recipes linked to products</li>';
    echo '<li>📁 <a href="' . admin_url('edit.php?post_type=bf_material') . '">Manage Materials</a></li>';
    echo '<li>⚙️ <a href="' . admin_url('admin.php?page=buildflow-settings') . '">Settings</a></li>';
    echo '</ul>';
    echo '<p style="font-size: 12px; color: #666;">Version 1.0.0 • Built for handmade and small-batch makers.</p>';
    echo '</div>';

    echo '</div>';
}
