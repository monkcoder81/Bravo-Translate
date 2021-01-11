<?php
/*
Plugin Name: Bravo Translate
Description: The easiest solution for translate foreign language from your themes or plugins.
Version: 1.0
Author: guelben
Author URI: http://www.guelbetech.com
License: MIT License
License URI: http://opensource.org/licenses/MIT
Text Domain: bravo-translate
Domain Path: /languages/
*/


//we define constants
define('BRAVOTRAN_FILE',__FILE__);
define('BRAVOTRAN_DIR_URL',plugin_dir_url(__FILE__));

//we laod modules
require_once( plugin_dir_path(__FILE__).'functions.php');
require_once( plugin_dir_path(__FILE__).'activation.php');

require_once( plugin_dir_path(__FILE__).'admin.php');
require_once( plugin_dir_path(__FILE__).'ajax.php');

//we load translations
add_action('after_setup_theme', 'bravo_translate_setup');
function bravo_translate_setup(){
load_plugin_textdomain('bravo-translate', false, dirname(plugin_basename( __FILE__ )) . '/languages/' );
}
?>