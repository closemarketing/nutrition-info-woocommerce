=== Nutrition Info for WooCommerce ===
Contributors: closemarketing, davidperez, matiasquero, alexcm13
Tags: nutrition, allergens, woocommerce, food, ingredients
Donate link: https://www.closemarketing.es/go/donate/
Requires at least: 5.8
Requires PHP: 7.4
Requires plugins: woocommerce
WC requires at least: 5.0
WC tested up to: 9.0
Tested up to: 7.0
Stable tag: 1.0.1
Version: 1.0.1
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display nutritional information and allergen icons on your WooCommerce product pages.

== Description ==

**Nutrition Info for WooCommerce** lets you add complete nutritional data, ingredients, and allergen information to each WooCommerce product. All data is entered directly from the product edit screen and displayed on the frontend with a clean, accessible design.

= Features =

**Nutritional information table**

Enter values per 100 g for all standard nutritional fields:

* Energy (KJ/kcal)
* Fat, saturated, monounsaturated and polyunsaturated fatty acids
* Carbohydrate, sugars, polyols and starch
* Dietary fiber
* Protein
* Salt
* Vitamins and minerals

The table is rendered as a collapsible element so it takes no space by default.

**Ingredients**

A free-text ingredients field is displayed in a separate collapsible section below the nutritional table.

**Allergen icons**

Choose which allergens apply to each product. Supported allergens (each with its own SVG icon):

Alcohol, Almonds, Celery, Corn, Crustaceans, Egg, Fish, Gluten, Honey, Lupins, Milk, Mollusks, Mushrooms, Mustard, Nuts, Organic, Peanuts, Sesame, Soy, Spices, Sugar, Sulfates, Vegetables — plus a **Vegan** badge.

Icons are shown in the product loop (shop/archive pages) and on the single product page.

**Flexible placement**

From WooCommerce → Settings → Nutrients you can choose where the nutritional table and ingredients appear on the single product page:

* Separate tab
* Inside the description tab
* After price
* After excerpt
* After "Add to Cart" button
* After product metadata
* Manual placement (hidden — use the `[nutritiontable]` shortcode)

**Shortcode**

Use `[nutritiontable]` to render the nutritional table anywhere on the page.

= Requirements =

* WordPress 5.8+
* WooCommerce 5.0+
* PHP 7.4+

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install it through the WordPress plugin screen.
2. Activate the plugin through the **Plugins** screen.
3. Go to **WooCommerce → Settings → Nutrients** to configure the display position and styling options.
4. Edit any product and fill in the **Nutritional Info** and **Composition & Allergens** tabs.

== Frequently Asked Questions ==

= Where do I enter the nutritional data? =

In the product edit screen, two new tabs appear in the Product Data metabox: **Nutritional Info** and **Composition & Allergens**.

= Can I place the table manually? =

Yes. Set the position to "Manual placement (hidden)" in the settings and use the `[nutritiontable]` shortcode wherever you need it.

= Are the allergen icons customisable? =

The icons are SVG files stored in `includes/assets/allergens/`. You can replace any file with your own SVG keeping the same filename.

== Screenshots ==

1. Nutritional Info tab in the product edit screen.
2. Composition & Allergens tab in the product edit screen.
3. Nutritional table displayed on the single product page.
4. Allergen icons displayed in the product loop.
5. Plugin settings under WooCommerce → Settings → Nutrients.

== Changelog ==
= 1.0.1 =
* Fixed: Strings were not in English.

= 1.0.0 =
* Refactor: migrated to `CLOSE\NutritionInfo` namespace with Composer autoload (classmap).
* New: `Hooks` class centralises all frontend hook registration.
* New: `Allergens` class loads SVG icons from `includes/assets/allergens/` — no more inline SVG in PHP.
* New: vegan badge shown separately before the product thumbnail in the shop loop.
* New: position setting supports "inside description tab" option that appends nutrition and ingredients to the existing tab content.
* Improvement: nutritional table and ingredients rendered as accessible collapsible elements.
* Improvement: allergen icons include a label shown on hover.
* Fix: Composer autoload switched from PSR-4 to classmap to support WordPress `class-*.php` filename convention.
* Removed: `sass/` source folder (CSS is distributed as a compiled file).
* Refactor: improved code structure, sanitization and escaping across all files.
* New: PHPStan and PHP_CodeSniffer tooling added.
* New: GitHub Actions workflow for linting and deployment.
* Fix: variable sanitization and nonce verification on product meta save.

= 0.1.0 =
* Initial release.

== Links ==
* [Closemarketing](https://close.marketing)
* [Plugin page](https://www.closemarketing.net/plugin/nutrition-info-woocommerce)
