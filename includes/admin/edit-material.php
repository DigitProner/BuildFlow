<?php
defined('ABSPATH') || exit;

function bf_render_edit_material_page() {
    $units = get_option('buildflow_units', []);
    $categories = get_option('buildflow_categories', []);
    $material_id = isset($_GET['material_id']) ? intval($_GET['material_id']) : 0;

    if (!$material_id || get_post_type($material_id) !== 'bf_material') {
        echo '<div class="notice notice-error"><p>Invalid material ID.</p></div>';
        return;
    }

    $material = get_post($material_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bf_edit_material_nonce']) && wp_verify_nonce($_POST['bf_edit_material_nonce'], 'bf_edit_material')) {
        wp_update_post([
            'ID' => $material_id,
            'post_title' => sanitize_text_field($_POST['material_name']),
        ]);

        update_post_meta($material_id, '_bf_stock_level', sanitize_text_field($_POST['stock_level']));
        update_post_meta($material_id, '_bf_min_stock', sanitize_text_field($_POST['min_stock']));
        update_post_meta($material_id, '_bf_cost_per_unit', sanitize_text_field($_POST['cost_per_unit']));
        update_post_meta($material_id, '_bf_supplier', sanitize_text_field($_POST['supplier']));
        update_post_meta($material_id, '_bf_unit', sanitize_text_field($_POST['unit']));
        update_post_meta($material_id, '_bf_sku', sanitize_text_field($_POST['sku']));
        update_post_meta($material_id, '_bf_category', sanitize_text_field($_POST['category']));
        update_post_meta($material_id, '_bf_location', sanitize_text_field($_POST['location']));
        update_post_meta($material_id, '_bf_safety_stock', sanitize_text_field($_POST['safety_stock']));
        update_post_meta($material_id, '_bf_notes', sanitize_textarea_field($_POST['notes']));

        echo '<div class="notice notice-success"><p>Material updated successfully!</p></div>';
    }

    $stock = get_post_meta($material_id, '_bf_stock_level', true);
    $min_stock = get_post_meta($material_id, '_bf_min_stock', true);
    $cost = get_post_meta($material_id, '_bf_cost_per_unit', true);
    $supplier = get_post_meta($material_id, '_bf_supplier', true);
    $unit = get_post_meta($material_id, '_bf_unit', true);
    $sku = get_post_meta($material_id, '_bf_sku', true);
    $category = get_post_meta($material_id, '_bf_category', true);
    $location = get_post_meta($material_id, '_bf_location', true);
    $safety_stock = get_post_meta($material_id, '_bf_safety_stock', true);
    $notes = get_post_meta($material_id, '_bf_notes', true);

    if ($category && !in_array($category, $categories)) {
        $categories[] = $category;
    }
?>
<div class="wrap">
    <h1>Edit Material: <?php echo esc_html($material->post_title); ?></h1>
    <form method="post">
        <?php wp_nonce_field('bf_edit_material', 'bf_edit_material_nonce'); ?>

        <style>
        .bf-tabs { margin-bottom: 20px; }
        .bf-tab-buttons button { margin-right: 10px; }
        .bf-tab-content { display: none; margin-top: 20px; }
        .bf-tab-content.active { display: block; }
        </style>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.bf-tab-buttons button');
            const contents = document.querySelectorAll('.bf-tab-content');
            buttons.forEach(btn => {
                btn.addEventListener('click', function () {
                    buttons.forEach(b => b.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(this.dataset.tab).classList.add('active');
                });
            });
            if (buttons[0]) buttons[0].click();
        });
        </script>

        <div class="bf-tabs">
            <div class="bf-tab-buttons">
                <button type="button" data-tab="tab-basic">Basic Info</button>
                <button type="button" data-tab="tab-inventory">Inventory</button>
                <button type="button" data-tab="tab-cost">Cost & Supplier</button>
                <button type="button" data-tab="tab-notes">Notes</button>
            </div>
        </div>

        <div id="tab-basic" class="bf-tab-content">
            <table class="form-table"><tbody>
                <tr><th><label for="material_name">Name</label></th>
                    <td><input name="material_name" type="text" class="regular-text" value="<?php echo esc_attr($material->post_title); ?>" required></td></tr>
                <tr><th><label for="sku">SKU / Code</label></th>
                    <td><input name="sku" type="text" value="<?php echo esc_attr($sku); ?>"></td></tr>
                <tr><th><label for="category">Category</label></th>
                    <td>
                        <select name="category">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo esc_attr($cat); ?>" <?php selected($category, $cat); ?>><?php echo esc_html($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td></tr>
            </tbody></table>
        </div>

        <div id="tab-inventory" class="bf-tab-content">
            <table class="form-table"><tbody>
                <tr><th><label for="stock_level">Stock Level</label></th>
                    <td><input name="stock_level" type="number" step="any" value="<?php echo esc_attr($stock); ?>"></td></tr>
                <tr><th><label for="min_stock">Minimum Stock</label></th>
                    <td><input name="min_stock" type="number" step="any" value="<?php echo esc_attr($min_stock); ?>"></td></tr>
                <tr><th><label for="safety_stock">Safety Stock Level</label></th>
                    <td><input name="safety_stock" type="number" step="any" value="<?php echo esc_attr($safety_stock); ?>"></td></tr>
                <tr><th><label for="unit">Unit</label></th>
                    <td><select name="unit"><?php foreach ($units as $group => $opts): ?><optgroup label="<?php echo esc_attr($group); ?>"><?php foreach ($opts as $opt): ?><option value="<?php echo esc_attr($opt); ?>" <?php selected($unit, $opt); ?>><?php echo esc_html($opt); ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select></td></tr>
                <tr><th><label for="location">Storage Location</label></th>
                    <td><input name="location" type="text" value="<?php echo esc_attr($location); ?>"></td></tr>
            </tbody></table>
        </div>

        <div id="tab-cost" class="bf-tab-content">
            <table class="form-table"><tbody>
                <tr><th><label for="cost_per_unit">Cost Per Unit</label></th>
                    <td><input name="cost_per_unit" type="text" value="<?php echo esc_attr($cost); ?>"></td></tr>
                <tr><th><label for="supplier">Supplier</label></th>
                    <td><input name="supplier" type="text" value="<?php echo esc_attr($supplier); ?>"></td></tr>
            </tbody></table>
        </div>

        <div id="tab-notes" class="bf-tab-content">
            <table class="form-table"><tbody>
                <tr><th><label for="notes">Material Notes</label></th>
                    <td><textarea name="notes" rows="3" class="large-text"><?php echo esc_textarea($notes); ?></textarea></td></tr>
            </tbody></table>
        </div>

        <p class="submit"><input type="submit" class="button-primary" value="Update Material"></p>
    </form>
</div>
<?php } ?>
