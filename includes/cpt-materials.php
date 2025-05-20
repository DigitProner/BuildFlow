<?php
defined('ABSPATH') || exit;

function bf_register_materials_cpt() {
    $labels = [
        'name' => 'Materials',
        'singular_name' => 'Material',
        'menu_name' => 'Materials',
        'add_new' => 'Add Material',
        'add_new_item' => 'Add New Material',
        'edit_item' => 'Edit Material',
        'new_item' => 'New Material',
        'view_item' => 'View Material',
        'search_items' => 'Search Materials',
        'not_found' => 'No materials found.',
        'not_found_in_trash' => 'No materials found in Trash.',
        'all_items' => 'All Materials',
    ];

    $args = [
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'supports' => ['title'],
        'has_archive' => false,
        'show_in_rest' => false,
    ];

    register_post_type('bf_material', $args);
}
add_action('init', 'bf_register_materials_cpt');

// Add admin columns
add_filter('manage_bf_material_posts_columns', 'bf_material_columns');
function bf_material_columns($columns) {
    return [
        'cb'         => '<input type="checkbox" />',
        'title'      => 'Name',
        'stock'      => 'Stock',
        'min_stock'  => 'Min Stock',
        'unit'       => 'Unit',
        'cost'       => 'Cost / Unit',
        'supplier'   => 'Supplier',
        'category'   => 'Category',
        'location'   => 'Location',
        'date'       => 'Date',
    ];
}

add_action('manage_bf_material_posts_custom_column', 'bf_material_column_content', 10, 2);
function bf_material_column_content($column, $post_id) {
    switch ($column) {
        case 'stock':
            $stock = get_post_meta($post_id, '_bf_stock_level', true);
            $min   = get_post_meta($post_id, '_bf_min_stock', true);
            $style = ($stock < $min) ? 'color:red; font-weight:bold;' : '';
            echo '<span style="' . esc_attr($style) . '">' . esc_html($stock) . '</span>';
            break;
        case 'min_stock':
            echo esc_html(get_post_meta($post_id, '_bf_min_stock', true));
            break;
        case 'unit':
            echo esc_html(get_post_meta($post_id, '_bf_unit', true));
            break;
        case 'cost':
            echo '$' . esc_html(get_post_meta($post_id, '_bf_cost_per_unit', true));
            break;
        case 'supplier':
            echo esc_html(get_post_meta($post_id, '_bf_supplier', true));
            break;
        case 'category':
            echo esc_html(get_post_meta($post_id, '_bf_category', true));
            break;
        case 'location':
            echo esc_html(get_post_meta($post_id, '_bf_location', true));
            break;
    }
}

// Make stock and cost sortable
add_filter('manage_edit-bf_material_sortable_columns', 'bf_material_sortable_columns');
function bf_material_sortable_columns($columns) {
    $columns['stock'] = 'stock';
    $columns['cost'] = 'cost';
    return $columns;
}

add_action('pre_get_posts', 'bf_material_sort_by_meta');
function bf_material_sort_by_meta($query) {
    if (!is_admin() || !$query->is_main_query()) return;

    if ($query->get('orderby') === 'stock') {
        $query->set('meta_key', '_bf_stock_level');
        $query->set('orderby', 'meta_value_num');
    }

    if ($query->get('orderby') === 'cost') {
        $query->set('meta_key', '_bf_cost_per_unit');
        $query->set('orderby', 'meta_value_num');
    }
}

// Dropdown filter for Supplier and Category
add_action('restrict_manage_posts', 'bf_material_filters');
function bf_material_filters() {
    global $typenow;
    if ($typenow !== 'bf_material') return;

    $supplier_filter = isset($_GET['bf_supplier']) ? $_GET['bf_supplier'] : '';
    $category_filter = isset($_GET['bf_category']) ? $_GET['bf_category'] : '';

    $suppliers = bf_get_unique_meta_values('_bf_supplier');
    $categories = bf_get_unique_meta_values('_bf_category');

    echo '<select name="bf_supplier">';
    echo '<option value="">All Suppliers</option>';
    foreach ($suppliers as $supplier) {
        echo '<option value="' . esc_attr($supplier) . '" ' . selected($supplier, $supplier_filter, false) . '>' . esc_html($supplier) . '</option>';
    }
    echo '</select>';

    echo '<select name="bf_category">';
    echo '<option value="">All Categories</option>';
    foreach ($categories as $category) {
        echo '<option value="' . esc_attr($category) . '" ' . selected($category, $category_filter, false) . '>' . esc_html($category) . '</option>';
    }
    echo '</select>';
}

add_filter('parse_query', 'bf_material_filter_query');
function bf_material_filter_query($query) {
    global $pagenow;
    if (!is_admin() || $pagenow !== 'edit.php' || $query->get('post_type') !== 'bf_material') return;

    if (!empty($_GET['bf_supplier'])) {
        $query->set('meta_query', array_merge($query->get('meta_query', []), [
            ['key' => '_bf_supplier', 'value' => sanitize_text_field($_GET['bf_supplier'])]
        ]));
    }

    if (!empty($_GET['bf_category'])) {
        $query->set('meta_query', array_merge($query->get('meta_query', []), [
            ['key' => '_bf_category', 'value' => sanitize_text_field($_GET['bf_category'])]
        ]));
    }
}

add_filter('post_row_actions', 'bf_override_edit_link_for_materials', 10, 2);
function bf_override_edit_link_for_materials($actions, $post) {
    if ($post->post_type === 'bf_material') {
        $custom_url = admin_url('admin.php?page=buildflow-edit-material&material_id=' . $post->ID);
        $actions['edit'] = '<a href="' . esc_url($custom_url) . '">Edit</a>';
    }
    return $actions;
}


// Helper to get unique values for filters
function bf_get_unique_meta_values($key) {
    global $wpdb;
    $results = $wpdb->get_col($wpdb->prepare("
        SELECT DISTINCT meta_value FROM {$wpdb->postmeta}
        WHERE meta_key = %s AND meta_value != ''
        ORDER BY meta_value ASC
    ", $key));
    return array_filter($results);
}

// Fix Add New button redirect
add_action('admin_head', function () {
    global $typenow;
    if ($typenow === 'bf_material') {
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {
                const addNewButton = document.querySelector('.page-title-action');
                if (addNewButton) {
                    addNewButton.setAttribute('href', '<?php echo admin_url('admin.php?page=buildflow-add-material'); ?>');
                }
            });
        </script>
        <?php
    }
});
