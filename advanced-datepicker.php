<?php
/**
 * Plugin Name:       Advanced Datepicker
 * Plugin URI:        https://github.com/rav89lodz/wordpress-advanced-datepicker
 * Description:       Dedykowane pole z wyborem daty
 * Version:           1.0.0
 * Requires at least: 6.6.2
 * Requires PHP:      7.2
 * Author:            Rafał Chęciński
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/rav89lodz/wordpress-advanced-datepicker
 * Text Domain:       advanced-datepicker
 */

if (!defined('ABSPATH')) exit;

$wp_form_id = prepare_option_data('product_datepicker_wp_forms_id_form');
$wp_form_field_id = prepare_option_data('product_datepicker_wp_forms_id_field');

$forminator_form_id = prepare_option_data('product_datepicker_forminator_id_form');
$forminator_form_field_id = prepare_option_data('product_datepicker_forminator_id_field');

/**
 * Dodanie pickera do wpforms
 */
add_action( 'wpforms_display_field_after', function($field, $form_data) {
    global $wp_form_id, $wp_form_field_id;

    if(count($wp_form_id) > 0 && count($wp_form_field_id) > 0) {
        if (absint($wp_form_id[0]) === absint($form_data['id']) && absint($wp_form_field_id[0]) === absint($field['id'])) {
            echo do_shortcode('[advanced_datepicker]');
        }
    }
}, 16, 2 );

/**
 * Zapis daty po wysłaniu formularza przez wpforms
 */
add_action( 'wpforms_process_filter', function($fields, $entry, $form_data) {
    global $wp_form_id, $wp_form_field_id;

    if(count($wp_form_id) > 0 && count($wp_form_field_id) > 0) {
        if(absint($form_data['id']) !== absint($wp_form_id[0])) {
            return $fields;
        }
        prepare_option_data('product_datepicker_excluded_days', $fields[$wp_form_field_id[0]]["value"]);
    }

    return $fields;
}, 10, 3 );


function wdp_shortcode() {
    ob_start();
    include( plugin_dir_path( __FILE__ ) . '/template.php' );
    return ob_get_clean();
}

add_shortcode('advanced_datepicker', 'wdp_shortcode');

$allowed_ids = prepare_option_data('product_datepicker_product_ids');

/**
 * Dodaj pole na stronie produktu (tylko dla wybranych ID)
 */
add_action('woocommerce_before_add_to_cart_button', function() {
    global $product, $allowed_ids;

    if (!in_array($product->get_id(), $allowed_ids)) return;

    echo do_shortcode('[advanced_datepicker]');
});

/**
 * Dodanie pickera do forminatora
 */
add_action( 'forminator_before_form_render', function($id) {
    global $forminator_form_id;

    if(count($forminator_form_id) > 0 && absint($forminator_form_id[0]) === absint($id)) {
        echo do_shortcode('[advanced_datepicker]');
    }
}, 10, 1);

/**
 * Zapis daty po wysłaniu formularza przez forminator
 */
add_action('forminator_custom_form_mail_after_send_mail', function($custom_form, $data, $entry) {
    global $forminator_form_id, $forminator_form_field_id;

    if(count($forminator_form_id) > 0 && count($forminator_form_field_id) > 0) {
        if(absint($forminator_form_id[0]) === absint($entry['form_id']) && array_key_exists($forminator_form_field_id[0], $entry)) {
            prepare_option_data('product_datepicker_excluded_days', $entry[$forminator_form_field_id[0]]);
        }
    }
}, 10, 3);

/**
 * Walidacja przed dodaniem do koszyka
 */
add_filter('woocommerce_add_to_cart_validation', function($result, $product_id, $quantity) {
    global $allowed_ids;
    if (!in_array($product_id, $allowed_ids)) return $result;

    if (empty($_POST['datePickerField'])) {
        wc_add_notice('Wybierz datę przed dodaniem do koszyka.', 'error');
        return false;
    }
    return $result;
}, 10, 3);

/**
 * Zapis do koszyka jako customowa dana
 */
add_filter('woocommerce_add_cart_item_data', function($cart_item_data, $product_id, $variation_id) {
    global $allowed_ids;
    if (!in_array($product_id, $allowed_ids)) return $cart_item_data;

    $cart_item_data['workday_date'] = sanitize_text_field($_POST['datePickerField']);

    return $cart_item_data;
}, 10, 3);

/**
 * Wyświetl w koszyku
 */
add_filter('woocommerce_get_item_data', function($item_data, $cart_item) {
    if (!empty($cart_item['workday_date'])) {
        $item_data[] = [
            'name' => 'Wybrana data',
            'value' => $cart_item['workday_date']
        ];
    }
    return $item_data;
}, 10, 2);

/**
 * Zapis do zamówienia
 */
add_action('woocommerce_checkout_create_order_line_item', function($item, $cart_item_key, $values, $order) {
    if (!empty($values['workday_date'])) {
        $item->add_meta_data('Wybrana data', $values['workday_date']);
    }
}, 10, 4);

/**
 * Wyświetl w panelu admina i mailach
 */
add_filter('woocommerce_email_order_meta_fields', function($fields, $sent_to_admin, $order) {
    foreach ($order->get_items() as $item) {
        $date = $item->get_meta('Wybrana data');
        if ($date) {
            // $fields['workday_date_' . $item->get_id()] = [
            //     'label' => 'Wybrana data (' . $item->get_name() . ')',
            //     'value' => $date,
            // ];
            prepare_option_data('product_datepicker_excluded_days', $date);
        }
    }
    return $fields;
}, 10, 3);

/**
 * Dodanie opcji w menu admina w ustawieniach
 */
add_action('admin_menu', function() {
    add_options_page(
        'Advanced Datepicker',
        'Advanced Datepicker',
        'manage_options',
        'product-datepicker-main-menu',
        'product_datepicker_main_menu'
    );
});

function product_datepicker_register_settings() {
    register_setting( 'product_datepicker_settings_group', 'product_datepicker_excluded_days' );
    register_setting( 'product_datepicker_settings_group', 'product_datepicker_product_ids' );
    register_setting( 'product_datepicker_settings_group', 'product_datepicker_wp_forms_id_form' );
    register_setting( 'product_datepicker_settings_group', 'product_datepicker_wp_forms_id_field' );
    register_setting( 'product_datepicker_settings_group', 'product_datepicker_forminator_id_form' );
    register_setting( 'product_datepicker_settings_group', 'product_datepicker_forminator_id_field' );
}

/**
 * Rejestracja opcji
 */
add_action( 'admin_init', 'product_datepicker_register_settings' );

function product_datepicker_main_menu() {
    include( plugin_dir_path( __FILE__ ) . '/admin-template.php' );
}

/**
 * Prepare option data
 * 
 * @param string option
 * @param string|null to_add
 * @return array
 */
function prepare_option_data($option, $to_add = null) {
    $options_data = get_option($option, null);

    if($options_data === null) {
        return [];
    }

    $to_return = [];
    $options_data = explode(',', $options_data);

    switch($option) {
        case 'product_datepicker_forminator_id_field':
            $to_return = prepare_other_options($options_data, $option, false);
            break;
        case 'product_datepicker_product_ids':
        case 'product_datepicker_wp_forms_id_form':
        case 'product_datepicker_wp_forms_id_field':
        case 'product_datepicker_forminator_id_form':
            $to_return = prepare_other_options($options_data, $option);
            break;
        case 'product_datepicker_excluded_days':
        default:
            $to_return = prepare_excluded_days($options_data, $option);
            break;
    }

    if($to_add === null) {
        return $to_return;
    }

    if(validate_date_format($to_add) === false) {
        return [];
    }

    $to_return[] = $to_add;
    $to_return = array_unique($to_return);
    update_option($option, implode(',', $to_return));
}

/**
 * Prepare other options
 * 
 * @param array options_data
 * @param string option
 * @param bool is_int
 * @return array
 */
function prepare_other_options($options_data, $option, $is_int = true) {
    $to_return = [];

    foreach($options_data as $key => $value) {
        $temp = trim($value);
        if($is_int === false) {
            $to_return[] = $temp;
            continue;
        }
        if(is_valid_number($temp)) {
            $to_return[] = $temp;
        }
    }

    update_option($option, implode(',', $to_return));

    return $to_return;
}

/**
 * Prepare excluded days
 * 
 * @param array options_data
 * @param string option
 * @return array
 */
function prepare_excluded_days($options_data, $option) {
    $to_return = [];

    foreach($options_data as $key => $value) {
        $temp = trim($value);
        if(validate_date_format($temp)) {
            $to_return[] = $temp;
        }
    }

    $to_return = remove_outdated_dates($to_return);
    update_option($option, implode(',', $to_return));

    return $to_return;
}

/**
 * Validate number format
 * 
 * @param mixed number
 * @return bool
 */
function is_valid_number($number) {
    if($number !== null && is_numeric($number)) {
        return true;
    }
    return false;
}

/**
 * Validate date format
 * 
 * @param mixed date
 * @return bool
 */
function validate_date_format($date) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return false;
    }

    [$year, $month, $day] = explode('-', $date);
    return checkdate((int)$month, (int)$day, (int)$year);
}

/**
 * Remove outdated dates
 * 
 * @param array dates
 * @return array
 */
function remove_outdated_dates(array $dates) {
    $today = date('Y-m-d');
    $dates = array_unique($dates);
    return array_values(array_filter($dates, fn($date) => $date >= $today));
}

wp_enqueue_script('pd-render-calendar-js', plugin_dir_url( __FILE__ ) . 'calendar.js');
wp_enqueue_style('pd-render-calendar-css', plugin_dir_url( __FILE__ ) . 'calendar.css');

add_filter( 'plugin_action_links', function( $actions, $plugin_file ) {
    static $plugin;

    if (!isset($plugin)) {
        $plugin = plugin_basename(__FILE__);
    }

    if ($plugin == $plugin_file) {
        $actions = array_merge(['settings' => '<a href="options-general.php?page=product-datepicker-main-menu">' . __('Ustawienia', 'General') . '</a>'], $actions);
    }

    return $actions;
}, 10, 5 );