<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

function BRAVOTRAN__uninstall(){

//we delete table
global $wpdb;
$sql="DROP TABLE `".DB_NAME."`.`".$wpdb->base_prefix."bravo_translate`";
$wpdb->query($sql);
}

register_uninstall_hook(BRAVOTRAN_FILE,"BRAVOTRAN__uninstall");

?>