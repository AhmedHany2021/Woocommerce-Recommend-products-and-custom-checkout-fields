<?php

namespace CUSTOMWOO\Includes;

class CheckoutCustomFieldsClass
{

    public function __construct()
    {
        add_action('woocommerce_product_options_general_product_data', [$this,'add_checkout_custom_fields']);
        add_action('woocommerce_process_product_meta', [$this,'save_checkout_custom_fields']);

        add_action('woocommerce_after_order_notes', [$this,'add_custom_checkout_fields']);
        add_action('woocommerce_checkout_process', [$this,'validate_product_custom_fields']);
        add_action('woocommerce_checkout_update_order_meta', [$this,'save_product_custom_fields_to_order']);
        add_action('woocommerce_admin_order_data_after_order_details', [$this,'display_product_custom_fields_in_admin']);

    }

    public function add_checkout_custom_fields()
    {
        woocommerce_wp_text_input([
            'id' => '_checkout_fields',
            'label' => __('Custom Checkout Fields', 'woocommerce'),
            'description' => __('Enter the fields in checkout form using | separator eg "operating system|size"', 'woocommerce'),
            'desc_tip' => true,
            'type' => 'text',
        ]);
    }

    public function save_checkout_custom_fields($post_id) {
        if (isset($_POST['_checkout_fields'])) {
            $checkoutFields = sanitize_text_field($_POST['_checkout_fields']);
            update_post_meta($post_id, '_checkout_fields', $checkoutFields);
        }
    }

    function add_custom_checkout_fields($checkout) {
        echo '<div id="product_custom_checkout_fields"><h3>' . __('Product Custom Fields') . '</h3>';

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $checkout_fields = get_post_meta($product_id, '_checkout_fields', true);
            if (!empty($checkout_fields)) {
                echo '<div class="product-custom-field">';
                echo '<h4>' . get_the_title($product_id) . '</h4>'; // Display product title.

                $field_names = explode('|', $checkout_fields); // Split fields by `|`
                foreach ($field_names as $field_name) {
                    $field_key = sanitize_key($field_name); // Create a key for the field.
                    woocommerce_form_field("custom_field_{$cart_item_key}_{$field_key}", array(
                        'type'        => 'text',
                        'class'       => array('form-row-wide'),
                        'label'       => esc_html($field_name),
                        'placeholder' => esc_html__("Enter {$field_name}"),
                        'required'    => true,
                    ), $checkout->get_value("custom_field_{$cart_item_key}_{$field_key}"));
                }

                echo '</div>';
            }
        }

        echo '</div>';
    }

    public function validate_product_custom_fields()
    {
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $checkout_fields = get_post_meta($product_id, '_checkout_fields', true);

            if (!empty($checkout_fields)) {
                $field_names = explode('|', $checkout_fields); // Split fields by `|`
                foreach ($field_names as $field_name) {
                    $field_key = sanitize_key($field_name);
                    $field_input_name = "custom_field_{$cart_item_key}_{$field_key}";

                    if (empty($_POST[$field_input_name])) {
                        wc_add_notice(sprintf(__('Please fill in the %s field for %s.', 'woocommerce'), esc_html($field_name), get_the_title($product_id)), 'error');
                    }
                }
            }
        }
    }

    public function save_product_custom_fields_to_order($order_id)
    {
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $checkout_fields = get_post_meta($product_id, '_checkout_fields', true);

            if (!empty($checkout_fields)) {
                $field_names = explode('|', $checkout_fields); // Split fields by `|`
                foreach ($field_names as $field_name) {
                    $field_key = sanitize_key($field_name);
                    $field_input_name = "custom_field_{$cart_item_key}_{$field_key}";

                    if (!empty($_POST[$field_input_name])) {
                        $field_value = sanitize_text_field($_POST[$field_input_name]);
                        add_post_meta($order_id, "_product_{$product_id}_{$field_key}", $field_value);
                    }
                }
            }
        }
    }

    public function display_product_custom_fields_in_admin($order)
    {
        $order_id = $order->get_id();
        $order_meta = get_post_meta($order_id); // Fetch all order meta.

        echo '<h3>' . __('Product Custom Fields', 'woocommerce') . '</h3>';
        echo '<div class="product-custom-field-admin">';
        echo "<pre>";
        foreach ($order_meta as $meta_key => $meta_value) {
            if (preg_match('/^_product_(\d+)_(.+)$/', $meta_key, $matches)) {
                $product_id = $matches[1];
                $field_label = $matches[2];
                $field_value = maybe_unserialize($meta_value[0]); // Unserialize if necessary.

                echo '<p><strong>' . esc_html($field_label) . ' (' . get_the_title($product_id) . '):</strong> ' . esc_html($field_value) . '</p>';
            }
        }

        echo '</div>';
    }


}