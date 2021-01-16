<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

add_action('rest_api_init', function () {
	//para ver si el usuario ya existe 
    register_rest_route('bravotran', '/BRAVOTRAN_create', [
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => 'BRAVOTRAN_create',
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('bravotran', '/BRAVOTRAN_update', [
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => 'BRAVOTRAN_update',
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('bravotran', '/BRAVOTRAN_delete', [
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => 'BRAVOTRAN_delete',
        'permission_callback' => '__return_true'
    ]);
});


?>