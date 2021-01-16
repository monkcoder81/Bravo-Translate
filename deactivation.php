<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

function BRAVOTRAN__deactivation(){

global $wpdb;
$sql="DROP TABLE `".DB_NAME."`.`".$wpdb->base_prefix."bravo_translate`";
$wpdb->query($sql);
}

register_deactivation_hook(BRAVOTRAN_FILE,"BRAVOTRAN__deactivation");

?>