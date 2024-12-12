<?php

namespace CUSTOMWOO\Includes;

class RelatedProductsClass
{
    public function __construct()
    {
        /* it will be added automatically to the woocommerce checkout page and in case of custom layout it provided by short code */
        add_action('woocommerce_after_checkout_form', [$this,'RelatedProductsRender'] ,  10 );
        add_shortcode('cwoo_recommended_products', [$this,'RelatedProductsRender']);
        add_action('wp_enqueue_scripts', [$this,'RegisetAssets']);
    }

    public function RegisetAssets()
    {
        wp_enqueue_style('cwoo_mainstyle', CWOO_ASSETS . 'css/main.css');

        if (is_checkout()) {
            wp_enqueue_script(
                'cwoo_mainscript',
                CWOO_ASSETS . 'js/main.js',
                array('jquery'),
                '1.0',
                true
            );

            wp_localize_script('cwoo_mainscript', 'wcAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('add_to_cart_nonce'),
            ));
        }
    }

    public function RelatedProductsRender()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $user_id = get_current_user_id();
        $recommended_products = $this->getRecomendedProducts($user_id);

        if (empty($recommended_products)) {
            return;
        }

        include CWOO_TEMPLATES . 'relatedproducts.php';

    }

    public function getRecomendedProducts($customer_id)
    {
        $customer_orders = wc_get_orders(array(
            'customer_id' => $customer_id,
            'status'      => array('completed', 'processing'),
            'return'      => 'ids',
        ));

        if (empty($customer_orders)) {
            return array();
        }

        $purchased_products = array();
        foreach ($customer_orders as $order_id) {
            $order = wc_get_order($order_id);
            foreach ($order->get_items() as $item) {
                $purchased_products[] = $item->get_product_id();
            }
        }
        $purchased_products = array_unique($purchased_products);

        $purchased_categories = array();
        foreach ($purchased_products as $product_id) {
            $categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
            $purchased_categories = array_merge($purchased_categories, $categories);
        }
        $purchased_categories = array_unique($purchased_categories);


        $cart_product_ids = array();
        foreach (WC()->cart->get_cart() as $cart_item) {
            $cart_product_ids[] = $cart_item['product_id'];
        }


        $cart_categories = array();
        foreach ($cart_product_ids as $cart_product_id) {
            $categories = wp_get_post_terms($cart_product_id, 'product_cat', array('fields' => 'ids'));
            $cart_categories = array_merge($cart_categories, $categories);
        }
        $cart_categories = array_unique($cart_categories);


        $common_categories = array_intersect($purchased_categories, $cart_categories);


        if (empty($common_categories)) {
            return array();
        }


        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 5,
            'post_status'    => 'publish',
            'fields'         => 'ids',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $common_categories,
                ),
            ),
            'post__not_in'   => $cart_product_ids,
        );

        $recommended_products = get_posts($args);

        return $recommended_products;

    }

}