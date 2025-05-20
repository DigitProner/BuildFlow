# BuildFlow – Inventory & Batch Production Plugin for WordPress

**BuildFlow** is a custom WordPress plugin for managing materials, inventory, batch creation, and WooCommerce product recipes.

---

## Features

- Manage raw materials:
  - Stock level, units, cost, supplier, location
  - Category tagging with defaults and clean UI
- Create recipes for WooCommerce products
- Check if materials are in stock for each batch
- Dashboard with:
  - Low stock warnings
  - Material usage counts
  - Quick action links
- Settings page to manage units and categories

---

## Directory Structure

```
buildflow/
├── buildflow.php
├── uninstall.php
├── includes/
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── add-material.php
│   │   ├── edit-material.php
│   │   ├── settings.php
│   └── woo/
│       └── woocommerce-recipe-info.php
├── assets/
│   ├── css/
│   └── js/
└── README.md
```

---

## Installation

1. Place the `buildflow/` folder in your `wp-content/plugins/` directory.
2. Activate the plugin in WordPress Admin.
3. Access the **BuildFlow** menu for materials, settings, and WooCommerce integration.

---

## Requirements

- WordPress 6.0 or newer
- PHP 8.0+
- WooCommerce (optional, but required for product integration)

---

## License

This plugin is for private use and not distributed publicly. Do not redistribute without permission.
