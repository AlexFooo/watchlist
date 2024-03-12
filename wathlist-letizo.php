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
        "price_alert"
        
        
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


function letizo_get_stocks_data() {
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

    
    synchronize_watchlist_stock_alerts($user_id, $symbols_string);

    

    $stock_data = $api->batch_request(explode(",", $symbols_string));
    $formatted_data = format_stock_data($stock_data);

    foreach ($formatted_data as &$stock) {
        $symbol = $stock["symbol"];
        $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : get_current_user_id();
        $user_alerts_json = get_user_meta($user_id, 'letizo_watchlist_stock_alerts', true);
        $user_alerts = json_decode($user_alerts_json, true);
        
        $price_alert = null;
        foreach ($user_alerts as $alert) {
            if ($alert['symbol'] === $symbol) {
                
                $current_price = floatval($alert['current_price']);
                $desired_price = floatval($alert['desired_price']);
        
                
                $price_alert = [
                    "current_price" => $current_price,
                    "desired_price" => $desired_price
                ];
                break;
            }
        }
        
        $stock["price_alert"] =  $price_alert;
    }

    $response = [
        'stocks_data' => $formatted_data,
    ];
    echo json_encode($response);

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


function calculate_price_direction($current_price, $desired_price) {
    if ($current_price < $desired_price) {
        return "higher";
    } elseif ($current_price > $desired_price) {
        return "lower";
    } else {
        return "unchanged";
    }
}

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
        'stocks' => ["AAPL", "TSLA", "GOOGL", "MSFT", "AMZN", "NVDA"],
        'indices' => ["^GSPC", "^NDX", "^DJI", "^N225", "^GDAXI", "^FTSE"],
        'bonds' => ["ZB=F", "UB=F"],
        'forex' => ["EURUSD=X", "GBPUSD=X", "CHF=X", "AUDUSD=X", "INR=X", "KRW=X"],
        'commodity' => ["GC=F", "CL=F", "NG=F"],
    ];

    $result = [];

    foreach ($symbols_categories as $category => $symbols) {
        $stock_data = $api->batch_request($symbols);
        $formatted_data = format_stock_data($stock_data);

        foreach ($formatted_data as &$item) {

            $item['category'] = $category;

            $item['slug'] = 'stocks';
            
        }

        $result = array_merge($result, $formatted_data);
    }

    $result = array_values($result);

    wp_send_json(['stocks_data' => $result]);
    die();
}

add_action('wp_ajax_watchlist_get_sidebar_stocks', 'get_sidebar_stocks');
add_action('wp_ajax_nopriv_watchlist_get_sidebar_stocks', 'get_sidebar_stocks');








function save_user_stock_alert_data() {
    if (isset($_REQUEST['symbol'], $_REQUEST['current_price'], $_REQUEST['desired_price'])) {
        $user_id = get_current_user_id() ? get_current_user_id() : ( isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null );
        $symbol = $_REQUEST['symbol'];
        $current_price = $_REQUEST['current_price'];
        $desired_price = $_REQUEST['desired_price'];
        $user_meta_json = get_user_meta($user_id, 'letizo_watchlist_stock_alerts', true);

        if (empty($user_meta_json)) {
            $user_meta = [];
        } else {
            $user_meta = json_decode($user_meta_json, true);
        }

        $symbol_updated = false;

        foreach ($user_meta as &$stock_alert) {
            if ($stock_alert['symbol'] === $symbol) {
                $stock_alert['current_price'] = $current_price;
                $stock_alert['desired_price'] = $desired_price;

                
                $direction = calculate_price_direction($current_price, $desired_price);
                $stock_alert['price_direction'] = $direction;

                $symbol_updated = true;
                break;
            }
        }

        if (!$symbol_updated) {
            $new_stock_alert = [
                'current_price' => $current_price,
                'desired_price' => $desired_price,
                'symbol' => $symbol
            ];

            
            $direction = calculate_price_direction($current_price, $desired_price);
            $new_stock_alert['price_direction'] = $direction;

            $user_meta[] = $new_stock_alert;
        }

        $user_meta_json = json_encode($user_meta);

        update_user_meta($user_id, 'letizo_watchlist_stock_alerts', $user_meta_json);

        wp_send_json($user_meta);
    } else {
        wp_send_json(['error' => 'Missing data']);
    }

    wp_die();
}



add_action('wp_ajax_watchlist_save_user_stock_alert_data', 'save_user_stock_alert_data');
add_action('wp_ajax_nopriv_watchlist_save_user_stock_alert_data', 'save_user_stock_alert_data');






function delete_user_stock_alert_data() {
    
    if (isset($_REQUEST['symbol'])) {
        $user_id = get_current_user_id() ? get_current_user_id() : ( isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : null );
        $symbol = $_REQUEST['symbol'];
        $user_meta_json = get_user_meta($user_id, 'letizo_watchlist_stock_alerts', true);

        if (empty($user_meta_json)) {
            wp_send_json(['error' => 'User data not found']);
        }

        $user_meta = json_decode($user_meta_json, true);

        $updated_user_meta = [];

        
        foreach ($user_meta as $stock_alert) {
            
            if ($stock_alert['symbol'] === $symbol) {
                continue;
            }
            
            $updated_user_meta[] = $stock_alert;
        }

        
        $updated_user_meta_json = json_encode($updated_user_meta);

        
        update_user_meta($user_id, 'letizo_watchlist_stock_alerts', $updated_user_meta_json);

        
        wp_send_json($updated_user_meta);
    } else {
        wp_send_json(['error' => 'Missing data']);
    }

    wp_die();
}

add_action('wp_ajax_watchlist_delete_user_stock_alert_data', 'delete_user_stock_alert_data');
add_action('wp_ajax_nopriv_watchlist_delete_user_stock_alert_data', 'delete_user_stock_alert_data');





function synchronize_watchlist_stock_alerts($user_id, $symbols_string) {
    $user_alerts_json = get_user_meta($user_id, 'letizo_watchlist_stock_alerts', true);
    $user_alerts = json_decode($user_alerts_json, true);

    
    if (!empty($user_alerts)) {
        
        $symbols = explode(",", $symbols_string);

        
        foreach ($user_alerts as $key => $alert) {
            
            if (!in_array($alert['symbol'], $symbols)) {
                
                if (isset($user_alerts[$key]['price_alert'])) {
                    $user_alerts[$key]['price_alert'] = null;
                }
                
                unset($user_alerts[$key]);
            }
        }

        
        $user_alerts_json = json_encode(array_values($user_alerts)); 
        update_user_meta($user_id, 'letizo_watchlist_stock_alerts', $user_alerts_json);
    }
}



function schedule_check_alerts_desired_price()
{
    if (!wp_next_scheduled('check_alerts_desired_price_event')) {
        wp_schedule_event(time(), 'scrape_4 Hours', 'check_alerts_desired_price_event');
    }
}
add_action('wp', 'schedule_check_alerts_desired_price   ');


function publish_check_alerts_desired_price_event_handler()
{
    check_alerts_desired_price();
}
add_action('check_alerts_desired_price_event', 'publish_check_alerts_desired_price_event_handler');


function check_alerts_desired_price() {
    $notifications_to_update = array(); 

    $users_with_alerts = get_users(array(
        'meta_query' => array(
            array(
                'key' => 'letizo_watchlist_stock_alerts',
                'compare' => 'EXISTS', 
            ),
        ),
    ));

    foreach ($users_with_alerts as $user) {
        $user_notifications = array(); 

        $user_id = $user->ID;
        $symbols_string = get_user_meta($user_id, 'letizo_user_watchlist_symbols_string', true);
        $symbols_array = explode(',', $symbols_string); 
        
        
        $response = wp_remote_post(admin_url('admin-ajax.php'), array(
            'method' => 'POST',
            'body' => array(
                'action' => 'watchlist_letizo_get_stocks_data',
                'symbols_string' => implode(',', $symbols_array)
            )
        ));
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $stocks_data = json_decode(wp_remote_retrieve_body($response), true);

            foreach ($stocks_data['stocks_data'] as $stock) {
                $symbol = $stock['symbol'];
                $current_price = $stock['price'];
                $logo = $stock['logo'];
                $company_name = $stock['company_name'];

                $user_alerts_json = get_user_meta($user_id, 'letizo_watchlist_stock_alerts', true);
                $user_alerts = json_decode($user_alerts_json, true);

                foreach ($user_alerts as $key => $alert) {
                    if ($alert['symbol'] === $symbol) {
                        $desired_price = $alert['desired_price'];
                        $price_direction = $alert['price_direction'];

                        if (($price_direction === 'higher' && $current_price >= $desired_price) ||
                            ($price_direction === 'lower' && $current_price <= $desired_price)) {
                            $notification_id = substr(uniqid(), -6);
                            $notification = array(
                                'symbol' => $symbol,
                                'company_name' => $company_name,
                                'logo' => $logo,
                                'id' => $notification_id,
                                'desired_price' => $desired_price,
                                'created_at' => current_time('mysql'),
                                'updated_at' => current_time('mysql'),
                                'read' => false
                            );

                            $user_notifications[] = $notification; 
                            
                            unset($user_alerts[$key]);
                        }
                    }
                }

                update_user_meta($user_id, 'letizo_watchlist_stock_alerts', json_encode($user_alerts));
            }
        }
        
        
        $existing_notifications = get_user_meta($user_id, 'watchlist_alerts_notification', true);
        
       
        if (is_array($existing_notifications)) {
            $user_notifications = array_merge($existing_notifications, $user_notifications);
        }
        
       
        update_user_meta($user_id, 'watchlist_alerts_notification', $user_notifications);
        
        
        $notifications_to_update[$user_id] = $user_notifications;
    }

    wp_send_json($notifications_to_update);
}

add_action('wp_ajax_watchlist_check_alerts_desired_price', 'check_alerts_desired_price');
add_action('wp_ajax_nopriv_watchlist_check_alerts_desired_price', 'check_alerts_desired_price');


















function get_watchlist_user_alert_notifications() {
    $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : get_current_user_id();
    
    
    $watchlist_alerts_notification = get_user_meta($user_id, 'watchlist_alerts_notification', true);
    
    
    wp_send_json($watchlist_alerts_notification);
}

add_action('wp_ajax_get_watchlist_user_alert_notifications', 'get_watchlist_user_alert_notifications');
add_action('wp_ajax_nopriv_get_watchlist_user_alert_notifications', 'get_watchlist_user_alert_notifications');





function clear_watchlist_alerts_notification() {
    
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : get_current_user_id();
    
    
    if ($user_id) {
        
        $deleted = delete_user_meta($user_id, 'watchlist_alerts_notification');
        if ($deleted) {
            wp_send_json_success("true");
        } else {
            wp_send_json_error("false");
        }
    } else {
        wp_send_json_error('false');
    }
}


add_action('wp_ajax_clear_watchlist_alerts_notification', 'clear_watchlist_alerts_notification');
add_action('wp_ajax_nopriv_clear_watchlist_alerts_notification', 'clear_watchlist_alerts_notification');


function mark_all_notifications_as_read() {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : get_current_user_id();
    
    if ($user_id) {
        $existing_notifications = get_user_meta($user_id, 'watchlist_alerts_notification', true);
        
        if (is_array($existing_notifications)) {
            foreach ($existing_notifications as &$notification) {
                $notification['read'] = true;
            }
            
            update_user_meta($user_id, 'watchlist_alerts_notification', $existing_notifications);
            
            wp_send_json_success();
        } else {
            wp_send_json_error('No notifications found for the user.');
        }
    } else {
        wp_send_json_error('User ID not provided or invalid.');
    }
}

add_action('wp_ajax_mark_all_notifications_as_read', 'mark_all_notifications_as_read');
add_action('wp_ajax_nopriv_mark_all_notifications_as_read', 'mark_all_notifications_as_read');








function render_letizo_watchlist_shortcode($atts, $content = null)
{


    $script_src = plugin_dir_url(__FILE__) . 'watchlist-vue/dist/index.js';
    $css_src = plugin_dir_url(__FILE__) . 'watchlist-vue/dist/index.css';
    $html_tag = '<div class="letizo-vue-app" data-type="watchlist"></div> ';
    $script_tag = '<script type="module" src="' . $script_src . '"></script> ';
    $css_tag = '<link rel="stylesheet" href="' . $css_src . '"> ';


    return $html_tag . $script_tag . $css_tag;
}
add_shortcode('letizo-watchlist', 'render_letizo_watchlist_shortcode');

function render_letizo_sidebar_stocks_shortcode($atts, $content = null)
{

    $script_src = plugin_dir_url(__FILE__) . 'watchlist-vue/dist/index.js';
    $css_src = plugin_dir_url(__FILE__) . 'watchlist-vue/dist/index.css';
    $html_tag = '<div class="letizo-vue-app" data-type="sidebar"></div> ';
    $script_tag = '<script type="module" src="' . $script_src . '"></script> ';
    $css_tag = '<link rel="stylesheet" href="' . $css_src . '"> ';


    return $html_tag . $script_tag . $css_tag;
}
add_shortcode('letizo-sidebar-stocks', 'render_letizo_sidebar_stocks_shortcode');


function render_letizo_add_to_watchlist_shortcode($atts, $content = null)
{
    $stockSymbol = $atts['symbol'];

    $script_src = plugin_dir_url(__FILE__) . 'watchlist-vue/dist/index.js';
    $css_src = plugin_dir_url(__FILE__) . 'watchlist-vue/dist/index.css';
    $html_tag = '<div class="letizo-vue-app" data-type="add-to-watchlist-button" data-symbol="' . $stockSymbol . '"></div>';
    $script_tag = '<script type="module" src="' . $script_src . '"></script> ';
    $css_tag = '<link rel="stylesheet" href="' . $css_src . '"> ';


    return $html_tag . $script_tag . $css_tag;
}
add_shortcode('letizo-add-stock-to-watchlist-button', 'render_letizo_add_to_watchlist_shortcode');











