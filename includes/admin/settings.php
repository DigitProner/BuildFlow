<?php
defined('ABSPATH') || exit;

function bf_render_settings_page() {
    $categories_reset = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('bf_save_settings')) {
        if (isset($_POST['units']) && is_array($_POST['units'])) {
            update_option('buildflow_units', $_POST['units']);
        }

        if (isset($_POST['categories'])) {
            $new_categories = array_filter(array_map('sanitize_text_field', $_POST['categories'] ?? []));
            if (empty($new_categories)) {
                $new_categories = ['Default'];
                $categories_reset = true;
            }

            update_option('buildflow_categories', $new_categories);

            // Reassign deleted categories to Default
            $all_materials = get_posts(['post_type' => 'bf_material', 'numberposts' => -1]);
            foreach ($all_materials as $mat) {
                $cat = get_post_meta($mat->ID, '_bf_category', true);
                if (!in_array($cat, $new_categories)) {
                    update_post_meta($mat->ID, '_bf_category', 'Default');
                }
            }
        }

        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }

    $units = get_option('buildflow_units', []);
    $categories = get_option('buildflow_categories', []);
    if (empty($categories)) {
        $categories = ['Default'];
        update_option('buildflow_categories', $categories);
        $categories_reset = true;
    }
?>
<div class="wrap">
    <h1>BuildFlow Settings</h1>
    <?php if ($categories_reset): ?>
        <div class="notice notice-warning"><p>No categories were defined, so a <strong>"Default"</strong> category has been created and applied.</p></div>
    <?php endif; ?>

    <form method="post" id="buildflow-settings-form">
        <?php wp_nonce_field('bf_save_settings'); ?>

        <h2>Stock Units</h2>
        <?php foreach ($units as $group => $opts): ?>
            <h3><?php echo esc_html($group); ?></h3>
            <ul id="unit-group-<?php echo esc_attr($group); ?>">
                <?php foreach ($opts as $unit): ?>
                    <li>
                        <input type="text" name="units[<?php echo esc_attr($group); ?>][]" value="<?php echo esc_attr($unit); ?>">
                        <button class="remove-unit" type="button">Remove</button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button class="add-unit" type="button" data-group="<?php echo esc_attr($group); ?>">Add Unit</button>
        <?php endforeach; ?>

        <h2>Material Categories</h2>
        <ul id="category-list">
            <?php foreach ($categories as $cat): ?>
                <li>
                    <input type="text" name="categories[]" value="<?php echo esc_attr($cat); ?>">
                    <button class="remove-cat" type="button">Remove</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <button id="add-category" type="button">Add Category</button>

        <p class="submit"><input type="submit" class="button-primary" value="Save Settings"></p>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function attachRemoveHandlers() {
        document.querySelectorAll('.remove-cat').forEach(btn => {
            btn.onclick = function () {
                this.closest('li').remove();
                logCategories();
            };
        });

        document.querySelectorAll('.remove-unit').forEach(btn => {
            btn.onclick = function () {
                this.closest('li').remove();
            };
        });
    }

    function logCategories() {
        const cats = Array.from(document.querySelectorAll('#category-list input')).map(el => el.value.trim()).filter(Boolean);
        console.log('[BuildFlow] Categories to be saved:', cats);
    }

    document.querySelectorAll('.add-unit').forEach(btn => {
        btn.onclick = function () {
            const group = this.dataset.group;
            const ul = document.getElementById('unit-group-' + group);
            const li = document.createElement('li');
            li.innerHTML = '<input type="text" name="units[' + group + '][]" value=""><button class="remove-unit" type="button">Remove</button>';
            ul.appendChild(li);
            attachRemoveHandlers();
        };
    });

    document.getElementById('add-category').onclick = function () {
        const li = document.createElement('li');
        li.innerHTML = '<input type="text" name="categories[]" value=""><button class="remove-cat" type="button">Remove</button>';
        document.getElementById('category-list').appendChild(li);
        attachRemoveHandlers();
    };

    attachRemoveHandlers();

    document.getElementById('buildflow-settings-form').addEventListener('submit', function () {
        logCategories();
        const remaining = document.querySelectorAll('#category-list input');
        if (remaining.length === 0) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'categories[]';
            input.value = '';
            this.appendChild(input);
        }
    });
});
</script>
<?php } ?>
