<?php
defined('ABSPATH') || exit;

function bf_render_add_material_page() {
    $units = get_option('buildflow_units', []);
    $categories = get_option('buildflow_categories', []);
    $current = $_POST['category'] ?? '';
    if ($current && !in_array($current, $categories)) {
        $categories[] = $current;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bf_add_material_nonce']) && wp_verify_nonce($_POST['bf_add_material_nonce'], 'bf_add_material')) {
        $post_id = wp_insert_post([
            'post_type' => 'bf_material',
            'post_title' => sanitize_text_field($_POST['material_name']),
            'post_status' => 'publish',
        ]);

        if ($post_id) {
            update_post_meta($post_id, '_bf_stock_level', sanitize_text_field($_POST['stock_level']));
            update_post_meta($post_id, '_bf_min_stock', sanitize_text_field($_POST['min_stock']));
            update_post_meta($post_id, '_bf_cost_per_unit', sanitize_text_field($_POST['cost_per_unit']));
            update_post_meta($post_id, '_bf_supplier', sanitize_text_field($_POST['supplier']));
            update_post_meta($post_id, '_bf_unit', sanitize_text_field($_POST['unit']));
            update_post_meta($post_id, '_bf_sku', sanitize_text_field($_POST['sku']));
            update_post_meta($post_id, '_bf_category', sanitize_text_field($_POST['category']));
            update_post_meta($post_id, '_bf_location', sanitize_text_field($_POST['location']));
            update_post_meta($post_id, '_bf_safety_stock', sanitize_text_field($_POST['safety_stock']));
            update_post_meta($post_id, '_bf_notes', sanitize_textarea_field($_POST['notes']));

            wp_redirect(admin_url('admin.php?page=buildflow-edit-material&material_id=' . $post_id));
            exit;
        } else {
            echo '<div class="notice notice-error"><p>Something went wrong.</p></div>';
        }
    }
?>
<div class="wrap">
    <h1>Add New Material</h1>
    <form method="post">
        <?php wp_nonce_field('bf_add_material', 'bf_add_material_nonce'); ?>

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
                    <td><input name="material_name" type="text" class="regular-text" required></td></tr>
                <tr><th><label for="sku">SKU / Code</label></th>
                    <td><input name="sku" type="text"></td></tr>
                <tr><th><label for="category">Category</label></th>
                    <td>
                        <select name="category">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo esc_attr($cat); ?>" <?php selected($current, $cat); ?>><?php echo esc_html($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td></tr>
            </tbody></table>
        </div>

        <div id="tab-inventory" class="bf-tab-content">
            <table class="form-table"><tbody>
                <tr><th><label for="stock_level">Stock Level</label></th>
                    <td><input name="stock_level" type="number" step="any"></td></tr>
                <tr><th><label for="min_stock">Minimum Stock</label></th>
                    <td><input name="min_stock" type="number" step="any"></td></tr>
                <tr><th><label for="safety_stock">Safety Stock Level</label></th>
                    <td><input name="safety_stock" type="number" step="any"></td></tr>
                <tr><th><label for="unit">Unit</label></th>
                    <td><select name="unit"><?php foreach ($units as $group => $opts): ?><optgroup label="<?php echo esc_attr($group); ?>"><?php foreach ($opts as $opt): ?><option value="<?php echo esc_attr($opt); ?>"><?php echo esc_html($opt); ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select></td></tr>
                <tr><th><label for="location">Storage Location</label></th>
                    <td><input name="location" type="text"></td></tr>
            </tbody></table>
        </div>

        <div id="tab-cost" class="bf-tab-content">
            <table class="form-table"><tbody>
                <tr><th><label for="cost_per_unit">Cost Per Unit</label></th>
                    <td><input name="cost_per_unit" type="text"></td></tr>
                <tr><th><label for="supplier">Supplier</label></th>
                    <td><input name="supplier" type="text"></td></tr>
            </tbody></table>
        </div>

        <div id="tab-notes" class="bf-tab-content">
            <table class="form-table"><tbody>
                <tr><th><label for="notes">Material Notes</label></th>
                    <td><textarea name="notes" rows="3" class="large-text"></textarea></td></tr>
            </tbody></table>
        </div>

        <p class="submit"><input type="submit" class="button-primary" value="Create Material"></p>
    </form>
</div>
<?php } ?>
