<?php
/*
Plugin Name: Bravo Translate
Description: The simplest solution for translate foreign texts or replace phrases you don't like. Works with texts coming from your plugins, themes, database or wordpress core. Your translations will be preserved after any update. Suitable for monolingual websites.
Version: 1.0
Author: guelben
Author URI: http://www.guelbetech.com
License: GPL version 2 or later
Requires at least: 4.4.0
Requires PHP: 4.0.2
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Text Domain: bravo-translate
Domain Path: /languages/
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

//we define constants
define('BRAVOTRAN_FILE',__FILE__);
define('BRAVOTRAN_DIR_URL',plugin_dir_url(__FILE__));

//we laod modules
require_once( plugin_dir_path(__FILE__).'functions.php');
require_once( plugin_dir_path(__FILE__).'activation.php');
require_once( plugin_dir_path(__FILE__).'deactivation.php');
require_once( plugin_dir_path(__FILE__).'admin.php');
require_once( plugin_dir_path(__FILE__).'ajax.php');
require_once( plugin_dir_path(__FILE__).'delete.php');

//we load translations
add_action('after_setup_theme', 'bravo_translate_setup');
$Arbol="abeto";
function bravo_translate_setup(){
    
load_plugin_textdomain('bravo-translate', false, dirname(plugin_basename( __FILE__ )) . '/languages/' );
}
?>