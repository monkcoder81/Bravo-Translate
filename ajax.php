<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 


//this endopoints only are avalaible for admin 


    add_action('rest_api_init', function () {
        //para ver si el usuario ya existe 
        register_rest_route('bravo-translate', '/BRAVOTRAN_create', [
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => 'BRAVOTRAN_create',
            'permission_callback' => '__return_true'
        ]);
        register_rest_route('bravo-translate', '/BRAVOTRAN_update', [
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => 'BRAVOTRAN_update',
            'permission_callback' => '__return_true'
        ]);
        register_rest_route('bravo-translate', '/BRAVOTRAN_delete', [
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => 'BRAVOTRAN_delete',
            'permission_callback' => '__return_true'
        ]);
    });


function BRAVOTRAN_create(WP_REST_Request $request){

    if (!BRAVOTRAN_IsAllowedAjaxContext()) return;
    
            $textTo=$request->get_param('textTo');
            $yourTranslation=$request->get_param('yourTranslation');
            $sql="INSERT INTO `wp_bravo_translate` (`ID`, `searchFor`, `replaceBy`) VALUES (NULL, '$textTo', '$yourTranslation');";
    
            global $wpdb;
            $results=$wpdb->get_results($sql);
    
            $sql="SELECT * FROM `wp_bravo_translate` ORDER BY `wp_bravo_translate`.`ID` DESC";
            $results=$wpdb->get_results($sql);
            $response='<div id="message"  style="width:96%;max-width:800px;margin:10px auto" class="updated notice is-dismissible">
            <p>'.__('1 translation added','bravo-transalte').'</p><button type="button" onclick="BRAVOTRANdismiss()" class="notice-dismiss">
            <span class="screen-reader-text">'.__('Dismiss.','bravo_translate').'</span></button>
            </div>
            <!--begin of BRAVOTRANtablexss-->
            <table class="wp-list-table widefat fixed striped table-view-list pages bravoTable"><tr><td class="bravoCell bravoCellHeader">TEXT TO TRANSLATE</td><td class="bravoCell bravoCellHeader">YOUR TRANSLATION</td> <td style="width:40px"></td></tr>';
            if($wpdb->num_rows>0){
            foreach($results as $result){
            $response.='<tr id="trID"'.$result->ID.'"><td id=forID'.$result->ID.' class="bravoCell">'.$result->searchFor.'</td><td id="toID'.$result->ID.'"'." class='bravoCell'>".$result->replaceBy."</td>
            <td style='width:40px'><span class='edit BRAVOTRANminiButton'><a onclick='BRAVOTRAN_edit(".$result->ID.")'>Edit</a> <br><span class='trash BRAVOTRANminiButton'><a onclick='BRAVOTRAN_delete(".$result->ID.")'> Delete</a></td></tr>";
            }
            }
            $response.="</table><!--end of BRAVOTRANtablexss-->";
    
            echo $response;
    
    }


function BRAVOTRAN_update(WP_REST_Request $request){

if (!BRAVOTRAN_IsAllowedAjaxContext()) return;
    $textTo=$request->get_param('textTo');
    $yourTranslation=$request->get_param('yourTranslation');
    $id=$request->get_param('id');
    global $wpdb;
    $sql="UPDATE `wp_bravo_translate` SET `searchFor` = '".$textTo."', `replaceBy` = '".$yourTranslation."' WHERE `wp_bravo_translate`.`ID` = ".$id.";";
    $results=$wpdb->get_results($sql);
    $sql="SELECT * FROM `wp_bravo_translate` ORDER BY `wp_bravo_translate`.`ID` DESC";
    $results=$wpdb->get_results($sql);
    $response='<div id="message"  style="width:96%;max-width:800px;margin:10px auto" class="updated notice is-dismissible">
    <p>'.__('1 translation edited','bravo-transalte').'</p><button type="button" onclick="BRAVOTRANdismiss()" class="notice-dismiss">
    <span class="screen-reader-text">'.__('Dismiss.','bravo_translate').'</span></button>
    </div>
    <!--begin of BRAVOTRANtablexss-->
    <table class="wp-list-table widefat fixed striped table-view-list pages bravoTable"><tr><td class="bravoCell bravoCellHeader">TEXT TO TRANSLATE</td><td class="bravoCell bravoCellHeader">YOUR TRANSLATION</td> <td style="width:40px"></td></tr>';
    if($wpdb->num_rows>0){
    foreach($results as $result){
        $response.='<tr id="trID"'.$result->ID.'"><td id=forID'.$result->ID.' class="bravoCell">'.$result->searchFor.'</td><td id="toID'.$result->ID.'"'." class='bravoCell'>".$result->replaceBy."</td>
        <td style='width:40px'><span class='edit BRAVOTRANminiButton'><a onclick='BRAVOTRAN_edit(".$result->ID.")'>Edit</a> <br><span class='trash BRAVOTRANminiButton'><a onclick='BRAVOTRAN_delete(".$result->ID.")'> Delete</a></td></tr>";
    }
    }
    $response.="</table><!--end of BRAVOTRANtablexss-->";
    
    echo $response;
}



function BRAVOTRAN_delete(WP_REST_Request $request){

if (!BRAVOTRAN_IsAllowedAjaxContext()) return;

    $id=$request->get_param('ID');
    global $wpdb;
    $sql="DELETE FROM `wp_bravo_translate` WHERE `wp_bravo_translate`.`ID` = $id";
    $results=$wpdb->get_results($sql);
    $sql="SELECT * FROM `wp_bravo_translate` ORDER BY `wp_bravo_translate`.`ID` DESC";
    $results=$wpdb->get_results($sql);
    $response='<div id="message"  style="width:96%;max-width:800px;margin:10px auto" class="updated notice is-dismissible">
    <p>'.__('1 translation deleted','bravo-translate').'</p><button type="button" onclick="BRAVOTRANdismiss()" class="notice-dismiss">
    <span class="screen-reader-text">'.__('Dismiss.','bravo_translate').'</span></button>
    </div>
    <!--begin of BRAVOTRANtablexss-->
    <table class="wp-list-table widefat fixed striped table-view-list pages bravoTable"><tr><td class="bravoCell bravoCellHeader">TEXT TO TRANSLATE</td><td class="bravoCell bravoCellHeader">YOUR TRANSLATION</td> <td style="width:40px"></td></tr>';
    if($wpdb->num_rows>0){
    foreach($results as $result){
        $response.='<tr id="trID"'.$result->ID.'"><td id=forID'.$result->ID.' class="bravoCell">'.$result->searchFor.'</td><td id="toID'.$result->ID.'"'." class='bravoCell'>".$result->replaceBy."</td>
        <td style='width:40px'><span class='edit BRAVOTRANminiButton'><a onclick='BRAVOTRAN_edit(".$result->ID.")'>Edit</a> <br><span class='trash BRAVOTRANminiButton'><a onclick='BRAVOTRAN_delete(".$result->ID.")'> Delete</a></td></tr>";
    }
    }
    $response.="</table><!--end of BRAVOTRANtablexss-->";
    
    echo $response;
}

        
function BRAVOTRAN_IsAllowedAjaxContext(){
    $user_id = wp_validate_auth_cookie( $_COOKIE[LOGGED_IN_COOKIE], 'logged_in' );
    if(user_can($user_id,'activate_plugins')){
        return true;
    }
        else{
        return false;
    }    
}       
?>