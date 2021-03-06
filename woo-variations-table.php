<?php
/*
Plugin Name: Woo Variations Table
Plugin URI: https://wordpress.org/plugins/woo-variations-table/
Description: Show WooCommerce variable products variations as table with filters and sorting instead of normal dropdowns.
Author: Alaa Rihan
Author URI: https://lb.linkedin.com/in/alaa-rihan-6971b686
Text Domain: woo-variations-table
Domain Path: /languages/
Version: 2.1.4
Requires at least: 4.0.0
Requires PHP: 5.6.20
WC requires at least: 3.0.0
WC tested up to: 3.9.1
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define("WOO_VARIATIONS_TABLE_VERSION", '2.1.4');

// Check if WooCommerce is enabled
add_action('plugins_loaded', 'check_woocommerce_enabled', 1);
function check_woocommerce_enabled()
{
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'woocommerce_disabled_notice');
        return;
    }
}

// Display WC disabled notice
function woocommerce_disabled_notice()
{
    echo '<div class="error"><p><strong>' . __('Woo Variations Table', 'woo-variations-table') . '</strong> ' . sprintf(__('requires %sWooCommerce%s to be installed & activated!', 'woo-variations-table'), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>') . '</p></div>';
}

// Settings menu item
add_action('admin_menu', 'woo_variations_table_settings_menu', 99);
function woo_variations_table_settings_menu()
{
    add_submenu_page('woocommerce', __('Woo Variations Table', 'woo-variations-table'), __('Woo Variations Table', 'woo-variations-table'), 'manage_options', 'woo_variations_table', 'woo_variations_table_settings_page_callback');
}

// Register our settings
add_action('admin_init', 'woo_variations_table_register_settings');
function woo_variations_table_register_settings()
{
    register_setting('woo_variations_table_settings', 'woo_variations_table_columns');
    register_setting('woo_variations_table_settings', 'woo_variations_table_show_attributes');
    register_setting('woo_variations_table_settings', 'woo_variations_table_show_filters');
    register_setting('woo_variations_table_settings', 'woo_variations_table_show_spinner');
    register_setting('woo_variations_table_settings', 'woo_variations_table_place');
    register_setting('woo_variations_table_settings', 'woo_variations_table_show_available_options_btn');
}

// Settings page callback function
function woo_variations_table_settings_page_callback()
{
    $default_columns = array(
        'image_link' => 1,
        'sku' => 1,
        'variation_description' => 1,
        'dimensions' => 0,
        'weight_html' => 0,
        'stock' => 1,
        'price_html' => 1,
        'quantity' => 1,
    );
    $columns_labels = array(
        'image_link' => __('Thumbnail', 'woo-variations-table'),
        'sku' => __('SKU', 'woo-variations-table'),
        'variation_description' => __('Description', 'woo-variations-table'),
        'dimensions' => __('Dimensions', 'woo-variations-table'),
        'weight_html' => __('Weight', 'woo-variations-table'),
        'stock' => __('Stock', 'woo-variations-table'),
        'price_html' => __('Price', 'woo-variations-table'),
        'quantity' => __('Quantity', 'woo-variations-table'),
    );
    $columns = get_option('woo_variations_table_columns', $default_columns);
    $showAttributes = get_option('woo_variations_table_show_attributes', '');
    $showFilters = get_option('woo_variations_table_show_filters', 'on');
    $showSpinner = get_option('woo_variations_table_show_spinner', 'on');
    $place = get_option('woo_variations_table_place', 'woocommerce_after_single_product_summary_9');
    $showAvailableOptionBtn = get_option('woo_variations_table_show_available_options_btn', 'on');?>
<div class="wrap">
  <h1><?php echo __('Woo Variations Table Settings', 'woo-variations-table'); ?></h1>
  <form method="post" action="options.php">
      <?php settings_fields('woo_variations_table_settings');?>
      <?php do_settings_sections('woo_variations_table_settings');?>
      <table class="form-table">
          <tr valign="top">
            <th scope="row"><?php echo __('Where to Place Variations Table', 'woo-variations-table'); ?></th>
            <td>
                <select name='woo_variations_table_place' >
                    <option value="woocommerce_after_single_product_summary_9" <?php echo $place == 'woocommerce_after_single_product_summary_9' ? "selected" : ''; ?>>After product summary ( before description )</option>
                    <option value="woocommerce_single_product_summary_11" <?php echo $place == 'woocommerce_single_product_summary_11' ? "selected" : ''; ?>>After product price</option>
                    <option value="woocommerce_single_product_summary_41" <?php echo $place == 'woocommerce_single_product_summary_41' ? "selected" : ''; ?>>After product short description</option>
                    <option value="woocommerce_after_single_product_summary_11" <?php echo $place == 'woocommerce_after_single_product_summary_11' ? "selected" : ''; ?>>After product description</option>
                </select>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php echo __('Select Columns to Show in the Variations Table', 'woo-variations-table'); ?></th>
            <td><?php woo_variations_table_create_multi_select_options('woo-variations-table-columns', $default_columns, $columns, $columns_labels);?></td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php echo __('Show Attributes', 'woo-variations-table'); ?></th>
            <td>
                <label><input type='checkbox' name='woo_variations_table_show_attributes' <?php echo $showAttributes ? "checked='checked'" : ''; ?> /> <?php echo __('Show product attributes as columns', 'woo-variations-table'); ?></label>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php echo __('Show Filters', 'woo-variations-table'); ?></th>
            <td>
                <label><input type='checkbox' name='woo_variations_table_show_filters' <?php echo $showFilters ? "checked='checked'" : ''; ?> /> <?php echo __('Show filters and search box', 'woo-variations-table'); ?></label>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php echo __('Available Options Button', 'woo-variations-table'); ?></th>
            <td>
                <label><input type='checkbox' name='woo_variations_table_show_available_options_btn' <?php echo $showAvailableOptionBtn ? "checked='checked'" : ''; ?> /> <?php echo __('Show "Available Options" button to scroll to the variations table when clicked', 'woo-variations-table'); ?></label>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php echo __('Add to Cart Loader Icon', 'woo-variations-table'); ?></th>
            <td>
                <label><input type='checkbox' name='woo_variations_table_show_spinner' <?php echo $showSpinner ? "checked='checked'" : ''; ?> /> <?php echo __('Show animated loader icon when click add to cart and tick icon when variation added to cart', 'woo-variations-table'); ?></label>
            </td>
          </tr>
      </table>

      <?php submit_button();?>

  </form>
</div>
<?php
}

function woo_variations_table_create_multi_select_options($id, $columns, $values, $labels)
{
    echo "<ul style='margin-top: 5px;' class='mnt-checklist sortable' id='$id' >" . "\n";
    foreach ($columns as $key => $value) {
        $checked = " ";
        if (isset($values[$key]) && $values[$key]) {
            $checked = " checked='checked' ";
        }
        echo "<li class='ui-state-default'>\n";
        echo "<label><input type='checkbox' name='woo_variations_table_columns[$key]' $checked />" . $labels[$key] . "</label>\n";
        echo "</li>\n";
    }
    echo "</ul>\n";
}

// Remove default variable product add to cart
add_action('plugins_loaded', 'remove_variable_product_add_to_cart');
function remove_variable_product_add_to_cart()
{
    remove_action('woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30);
}

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', 'variations_table_scripts');
function variations_table_scripts()
{
    if (is_product()) {
        wp_enqueue_script('woo-variations-table-scripts', plugins_url('js/woo-variations-table-scripts.js', __FILE__), array('jquery'), WOO_VARIATIONS_TABLE_VERSION, true);
        wp_enqueue_script('woo-variations-table-app', plugins_url('ui/public/build/woo-variations-table-app.js', __FILE__), array('wc-add-to-cart'), WOO_VARIATIONS_TABLE_VERSION, true);
        wp_enqueue_style('woo-variations-table-style', plugins_url('ui/public/woo-variations-table.css', __FILE__), array(), WOO_VARIATIONS_TABLE_VERSION);
        wp_enqueue_style('woo-variations-table-app-style', plugins_url('ui/public/build/woo-variations-table-app.css', __FILE__), array(), WOO_VARIATIONS_TABLE_VERSION);
    }
}

// Update database
add_action('admin_init', 'variations_table_database_update');
function variations_table_database_update()
{
    $plugin_db_version = get_option('woo_variations_table_db_version', WOO_VARIATIONS_TABLE_VERSION);
    if (in_array($plugin_db_version, array('1.1', '1.0', '0.9.0', '0.8.1'))) {
        $activeColumns = get_option('woo_variations_table_columns');
        if (isset($activeColumns['weight'])) {
            $activeColumns['weight_html'] = $activeColumns['weight'];
            unset($activeColumns['weight']);
            update_option('woo_variations_table_columns', $activeColumns);
        }
        update_option('woo_variations_table_show_attributes', '');
    }
    if ($plugin_db_version != WOO_VARIATIONS_TABLE_VERSION) {
        update_option('woo_variations_table_db_version', WOO_VARIATIONS_TABLE_VERSION);
    }
}

add_action('init', 'woo_variations_table_init');
function woo_variations_table_init()
{
    $place = get_option('woo_variations_table_place', 'woocommerce_after_single_product_summary_9');
    $priority = strrchr($place, "_");
    $place = str_replace($priority, "", $place);
    $priority = str_replace("_", "", $priority);
    add_action($place, 'woo_variations_table_print_table', $priority);

    $showAvailableOptionBtn = get_option('woo_variations_table_show_available_options_btn', 'on');
    if ($showAvailableOptionBtn == 'on') {
        add_action('woocommerce_single_product_summary', 'woo_variations_table_available_options_btn', 11);
    }
}

function woo_variations_table_available_options_btn()
{
    global $product;
    if (!$product->is_type('variable')) {
        return;
    }?>
  <div class="available-options-btn">
    <button scrollto="#variations-table" type="button" class="single_add_to_cart_button button alt"><?php echo apply_filters('woo_variations_table_available_options_btn_text', __('Available options', 'woo-variations-table')); ?></button>
  </div>
  <?php
}

// Print variations table
function woo_variations_table_print_table()
{
    global $product;
    if ($product->is_type('variable')) {
        $thumb_name = apply_filters('woo_variations_table_thumb_name', 'shop_single');
        $productImageURL = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), $thumb_name);
        if (is_array($productImageURL) && count($productImageURL)) {
            $productImageURL = $productImageURL[0];
        }
        $variations = $product->get_available_variations();

        // Image link is no longer exist in WooCommerce 3.x so do this work around
        foreach ($variations as $key => $variation) {
            if (!isset($variation['image_link']) && isset($variation['image'])) {
                $variations[$key]['image_link'] = $variation['image']['src'];
            }
            // price_html is empty if all variations have the same price in WooCommerce 3.x so do this work around
            if (empty($variation['price_html'])) {
                $variations[$key]['price_html'] = $product->get_price_html();
            }
        }

        $product_attributes = $product->get_attributes();
        $variation_attributes = $product->get_variation_attributes();
        $attrs = array();
        foreach ($variation_attributes as $key => $name) {
            $correctkey = wc_sanitize_taxonomy_name(stripslashes($key));
            $options = array();
            for ($i = 0; count($name) > $i; $i++) {
                $terms = array_values($name);
                $term = get_term_by('slug', $terms[$i], $key);
                $slug = array_values($name);
                $slug = $slug[$i];
                if ($term) {
                    array_push($options, array('name' => $term->name, 'slug' => $slug));
                } else {
                    array_push($options, array('name' => $slug, 'slug' => $slug));
                }
            }
            array_push($attrs, array(
                "key" => $correctkey,
                "name" => wc_attribute_label($key),
                "visible" => $product_attributes[$correctkey]->get_visible(),
                "options" => $options,
            ));
        }
        $default_columns = array(
            'image_link' => 'on',
            'sku' => 'on',
            'variation_description' => 'on',
            'dimensions' => '',
            'weight_html' => '',
            'stock' => '',
            'price_html' => 'on',
            'quantity' => 'on',
        );
        $activeColumns = get_option('woo_variations_table_columns', $default_columns);
        $showAttributes = get_option('woo_variations_table_show_attributes', '');
        $showFilters = get_option('woo_variations_table_show_filters', 'on');
        $showSpinner = get_option('woo_variations_table_show_spinner', 'on');
        $columnsText = array(
            'sku' => __('SKU', 'woo-variations-table'),
            'variation_description' => __('Description', 'woo-variations-table'),
            'weight_html' => __('Weight', 'woo-variations-table'),
            'dimensions' => __('Dimensions', 'woo-variations-table'),
            'price_html' => __('Price', 'woo-variations-table'),
            'stock' => __('Stock', 'woo-variations-table'),
            'quantity' => __('Quantity', 'woo-variations-table'),
        );

        $woo_variations_table_data = array(
            "variations" => $variations,
            "attributes" => $attrs,
            "showAttributes" => $showAttributes,
            "showFilters" => $showFilters,
            "activeColumns" => $activeColumns,
            "imageURL" => $productImageURL,
            "ajaxURL" => admin_url('admin-ajax.php?add_variation_to_cart=1'),
            "showSpinner" => $showSpinner,
            "textVars" => array(
                "columnsText" => $columnsText,
                "addToCartText" => __("Add to Cart", 'woo-variations-table'),
                "anyText" => __("Any", 'woo-variations-table'),
                "searchPlaceholderText" => __("Keywords", 'woo-variations-table'),
                "noResultsText" => __("No results!", 'woo-variations-table'),
            ),
        );

        wp_localize_script('woo-variations-table-app', 'wooVariationsTableData', $woo_variations_table_data);?>
        <div id='variations-table' class="variations-table">
          <h3 class="available-title"><?php echo esc_html_e('Available Options:', 'woo-variations-table'); ?></h3>
          <div id="woo-variations-table-component"></div>
        </div>
        <?php
} else {
        wp_dequeue_script('woo-variations-table-scripts');
        wp_dequeue_script('woo-variations-table-app');
    }
}
