<?php

/*
Plugin Name: Custom Woocommerce Checkout Linked
Plugin URI: https://github.com/AhmedHany2021
Description: This plugin change checkout page based on user behavior
Version: 1.0
Author: Ahmed Hany
Author URI: https://github.com/AhmedHany2021
*/

namespace CUSTOMWOO;

if (!defined('ABSPATH'))
{
    die();
}
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    die();
}

/* Add the main global variables */

if(!defined("CWOO_BASEDIR")) { define("CWOO_BASEDIR",__DIR__ . '/'); }
if(!defined("CWOO_INC")) { define("CWOO_INC",CWOO_BASEDIR.'includes' . '/'); }
if(!defined("CWOO_TEMPLATES")) { define("CWOO_TEMPLATES",CWOO_BASEDIR.'templates' . '/'); }
if(!defined("CWOO_URI")) { define("CWOO_URI",plugin_dir_url(__FILE__) ); }
if(!defined("CWOO_ASSETS")) { define("CWOO_ASSETS", CWOO_URI.'assets' . '/'); }

/* Add the autoload class */
require_once CWOO_INC.'autoload.php';
use CUSTOMWOO\Includes\autoload;
autoload::fire();

use CUSTOMWOO\Includes\RelatedProductsClass;
$relatedProducts = new RelatedProductsClass();

use CUSTOMWOO\Includes\CheckoutCustomFieldsClass;
$checkoutcustomfields = new CheckoutCustomFieldsClass();