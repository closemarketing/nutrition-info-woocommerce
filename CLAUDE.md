# Nutrition Info for WooCommerce — CLAUDE.md

## Project overview

WordPress plugin that displays nutritional information and allergen icons on WooCommerce product pages. Developed and maintained by Closemarketing.

- **Namespace:** `CLOSE\NutritionInfo`
- **Text domain:** `nutrition-info-woocommerce`
- **Constants:** `NIW_BUNDLE_VERSION`, `NIW_PLUGIN_PATH`, `NIW_PLUGIN_URL`

## Architecture

### Entry point
`nutrition-info-woocommerce.php` — defines constants, loads Composer autoload, and boots the plugin on `plugins_loaded`:
- `WooSettings::init()` — registers WooCommerce settings tab
- `new MetaProducts()` — registers product data panels and saves meta
- `new Hooks()` — registers frontend hooks, allergen icons, position handling

### Autoloaded classes (PSR-4 via Composer)
All in `includes/` → namespace `CLOSE\NutritionInfo\`:

| File | Class | Purpose |
|---|---|---|
| `class-hooks.php` | `Hooks` | Frontend hooks, allergen icon rendering, tab position logic |
| `class-woo-settings.php` | `WooSettings` | WooCommerce settings tab (nutrients config) |
| `class-woo-metaproducts.php` | `MetaProducts` | Product edit panels (nutritional fields, allergens, ingredients) |
| `allergens.php` | `Allergens` | Allergen data (keys, labels, SVG icons loaded from `includes/assets/allergens/`) |

### Autoloaded function files (Composer `files`)
Also in namespace `CLOSE\NutritionInfo\`:

| File | Functions |
|---|---|
| `template.php` | `niw_nutrition_info()`, `niw_composition_info()` |
| `product-tab.php` | `niw_composition_content_tab()`, `niw_nutritional_content_tab()` and their content callbacks |
| `shortcode.php` | `niw_shortcode_func()` — shortcode `[nutritiontable]` |

### SVG assets
Allergen SVG icons are stored in `includes/assets/allergens/{key}.svg`.
The `Allergens` class loads them via `file_get_contents()`.

### CSS
Frontend stylesheet at `includes/assets/css/styles.css`, enqueued by `Hooks::enqueue_styles()`.

## Common commands

```bash
# Regenerate Composer autoload after adding/moving files
composer dump-autoload

# Code style check
composer lint

# Auto-fix code style
composer format

# Static analysis
composer phpstan
```

## Key conventions

- All hook registration uses namespaced function strings: `__NAMESPACE__ . '\\function_name'`
- `WooSettings` uses static methods + `WooSettings::init()` called from the plugin entry point
- `MetaProducts` and `Hooks` are instantiated with `new` in the `plugins_loaded` callback
- SVG allergen icons: never hardcode SVG in PHP — add a new `.svg` file to `includes/assets/allergens/`
- Product meta keys follow the pattern `niw_{field}` (e.g. `niw_energy`, `niw_all_gluten`)

## Settings

Stored in WooCommerce options:

| Option | Values |
|---|---|
| `wc_nutrients_settings_tab_position` | `tab`, `in_description_tab`, `after_price`, `after_excerpt`, `after_add_to_cart`, `after_meta`, `hidden` |
| `wc_nutrients_settings_tab_title` | String |
| `wc_nutrients_settings_tab_per_volume_text` | String |
| `wc_nutrients_settings_tab_styling` | `yes` / empty |

## Distribution

The `.distignore` excludes dev files from plugin zips: `composer.json`, `composer.lock`, `tests/`, `phpstan.neon.dist`, `phpcs.xml.dist`, `CLAUDE.md`, `.github/`, etc.
