<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Plugin_Name
 *
 * @wordpress-plugin
 * Plugin Name:       Wathlist-letizo
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Your Name or Your Company
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-name
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUGIN_NAME_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_plugin_name()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-plugin-name-activator.php';
    Plugin_Name_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_plugin_name()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-plugin-name-deactivator.php';
    Plugin_Name_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_plugin_name');
register_deactivation_hook(__FILE__, 'deactivate_plugin_name');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-plugin-name.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name()
{

    $plugin = new Plugin_Name();
    $plugin->run();
}
run_plugin_name();



function get_api_config()
{
    return array(
        'auth' => array(
            'crumb' => 'Fpzh2QPx82b',
            'cookie' => 'd=AQABBLBbhGQCEGiyQXDnrRd9f4RJL1-0Hw8FEgEBAQGthWSOZFkeyyMA_eMAAA&S=AQAAAvNs5-0hNumW6l1ASUuHtQE',
        ),
        'assets' => array()
    );
}


function format_stock_data($stock_data)
{
    require_once(ABSPATH . 'wp-content/plugins/massive-stock-widgets/includes/shortcodes.php');

    $shortcodes = new \MassiveStockWidgets\Shortcodes($config);

    $formatted_data = [];
    $fields = [
        "symbol",
        "company_name",
        "type",
        "exchange",
        "exchange_name",
        "price",
        "currency",
        "open",
        "close",
        "low",
        "high",
        "previous_close",
        "change",
        "change_percent",
        "volume",
        "market_cap",
        "52_week_low",
        "52_week_low_change",
        "52_week_low_change_percent",
        "52_week_high",
        "52_week_high_change",
        "52_week_high_change_percent",
        "shares_outstanding",
        "eps_ttm",
        "dividend_yield_ta",
        "dividend_rate_ta",
        "last_update",
        "logo",
        "category",
    ];

    foreach ($stock_data as $symbol => $data) {
        $formatted_item = [];
        $skip_stock = false;

        foreach ($fields as $field) {
            if ($field === "price") {

                if (!isset($data["quote"][$field]) || $data["quote"][$field] === null || $data["quote"][$field] === 0) {
                    $skip_stock = true;
                    break;
                }
            }

            if ($field === "logo") {
                $formatted_item[$field] = $shortcodes->get_logo($data);
            } else {
                $formatted_item[$field] = $data["quote"][$field];
            }
        }

        if (!$skip_stock) {
            $formatted_data[] = $formatted_item;
        }
    }

    return $formatted_data;
}





function render_stock_assets_json($args)
{
    require_once(ABSPATH . 'wp-content/plugins/massive-stock-widgets/includes/api.php');

    $symbols_string = $args["assets"];
    $symbols = explode(",", $symbols_string);

    $config = get_api_config();

    $api = new MassiveStockWidgets\API($config);
    $api->auth_check();

    $stock_data = $api->batch_request($symbols);

    $formatted_data = format_stock_data($stock_data);

    return json_encode($formatted_data);
}


add_shortcode('stock-data', 'render_stock_assets_json');


function letizo_get_stocks_data()
{

    $config = get_api_config();
    $api = new MassiveStockWidgets\API($config);
    $api->auth_check();
    $symbols_string = '';

    if (isset($_REQUEST['symbols_string'])) {
        $symbols_string = $_REQUEST['symbols_string'];
    } elseif (isset($_REQUEST['user_id'])) {
        $user_id = $_REQUEST['user_id'];
        $symbols_string = get_user_meta($user_id, 'letizo_user_watchlist_symbols_string', true);

        if ($symbols_string === null || !metadata_exists('user', $user_id, 'letizo_user_watchlist_symbols_string')) {
            $symbols_string = "AAPL,TSLA,NVDA,BTC-USD";
        }
    }



    $symbols = explode(",", $symbols_string);
    $stock_data = $api->batch_request($symbols);
    $formatted_data = [
        'stocks_data' => format_stock_data($stock_data),
    ];
    echo json_encode($formatted_data);


    die();
}



add_action('wp_ajax_watchlist_letizo_get_stocks_data', 'letizo_get_stocks_data');
add_action('wp_ajax_nopriv_watchlist_letizo_get_stocks_data', 'letizo_get_stocks_data');

function get_default_stocks_data()
{
    $symbols_string = "AAPL,TSLA,NVDA,BTC-USD";
    $_REQUEST['symbols_string'] = $symbols_string;

    ob_start(); 
    letizo_get_stocks_data();
    $result = ob_get_clean();
    echo $result;
}

add_action('wp_ajax_watchlist_get_default_stocks_data', 'get_default_stocks_data');
add_action('wp_ajax_nopriv_watchlist_get_default_stocks_data', 'get_default_stocks_data');


function letizo_save_stocks_data_by_user_id()
{
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null;
    $symbols_string = isset($_REQUEST['symbols_string']) ? $_REQUEST['symbols_string'] : null;


    if (isset($symbols_string) && isset($user_id)) {
        
        update_user_meta($user_id, 'letizo_user_watchlist_symbols_string', $symbols_string);
        $result = letizo_get_stocks_data();
        echo $result;

    } else {
        echo json_encode(['success' => false, 'error' => 'symbols_string is missing']);
    }

    die();
}

add_action('wp_ajax_watchlist_letizo_save_stocks_data_by_user_id', 'letizo_save_stocks_data_by_user_id');
add_action('wp_ajax_nopriv_watchlist_letizo_save_stocks_data_by_user_id', 'letizo_save_stocks_data_by_user_id');


function get_sidebar_stocks()
{
    $config = get_api_config();
    $api = new MassiveStockWidgets\API($config);
    $api->auth_check();

    $symbols_categories = [
        'indices' => ["PG", "MRK", "HD", "TRV", "WBA", "MMM"],
        'bonds' => ["INTC", "V", "IBM", "BA", "WMT", "CSCO"],
        'forex' => ["NKE", "DIS", "JNJ", "XOM", "JPM", "BRK.B"],
        'commodity' => ["PFE", "UNH", "VZ", "CVX", "KO", "GS"],
        'stocks' => ["AAPL", "TSLA", "GOOGL", "MSFT", "AMZN", "FB"],
    ];

    $result = [];

    foreach ($symbols_categories as $category => $symbols) {
        $stock_data = $api->batch_request($symbols);
        $formatted_data = format_stock_data($stock_data);

        foreach ($formatted_data as &$item) {
            $item['category'] = $category;
        }

        $result = array_merge($result, $formatted_data);
    }

    $result = array_values($result);

    wp_send_json(['stocks_data' => $result]);
    die();
}

add_action('wp_ajax_watchlist_get_sidebar_stocks', 'get_sidebar_stocks');
add_action('wp_ajax_nopriv_watchlist_get_sidebar_stocks', 'get_sidebar_stocks');



function render_letizo_watchlist_shortcode($atts, $content = null)
{

    $element_id =  'app';

    $script_src = plugin_dir_url(__FILE__) . 'watchlist-vue/dist/index.js';
    $css_src = plugin_dir_url(__FILE__) . 'watchlist-vue/dist/index.css';
    $html_tag = '<div id="' . $element_id . '"></div> ';
    $script_tag = '<script type="module" src="' . $script_src . '"></script> ';
    $css_tag = '<link rel="stylesheet" href="' . $css_src . '"> ';


    return $html_tag . $script_tag . $css_tag;
}
add_shortcode('letizo-watchlist', 'render_letizo_watchlist_shortcode');
