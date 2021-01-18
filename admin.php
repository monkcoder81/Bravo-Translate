<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

function BRAVOTRAN_add_the_admin(){
    add_menu_page('Bravo Translate', 'Bravo Translate', 'activate_plugins', 'bravo-translate', 'BRAVOTRAN_admin', 'dashicons-translation');
    }
   
 add_action('admin_menu', 'BRAVOTRAN_add_the_admin');

    function BRAVOTRAN_admin(){
    ?>
<link rel="stylesheet" href='<?PHP echo BRAVOTRAN_DIR_URL ?>css/style.css'>
<script src='<?PHP echo BRAVOTRAN_DIR_URL ?>js/admin.js'></script>



<div id="wp-content">
<input type="hidden" id="BRAVOTRAN_min_char_message" value='<?php _e('The text to be translated must have a minimum length of 2 characters.','bravo-translate')?>'>
<input type="hidden" id="BRAVOTRAN_edit_hidden" value="0">

<table style="position:relative" class="wp-list-table widefat fixed striped table-view-list pages bravoTable">
<tr>
    <td class="bravoCell bravoCellHeader"><?php _e('Text to translate','bravo-translate')?></td>
    <td class="bravoCell bravoCellHeader"><?php _e('Your translation','bravo-translate')?></td>
</tr>
<tr>
    <td class="bravoCell"><input id="textToId" type="text" style="width:100%"></td>
    <td class="bravoCell"><input id="YourTrId" type="text" style="width:100%"></td>
   
</tr>



</table>


<div style="text-align:center;height:80px">
<button type="button" id="BRAVOTRANbutton" onclick="BRAVOTRAN_create()" class="button button-primary"><?php _e('Add Translation','bravo-translate') ?></button>
<button type="button" id="BRAVOTRANbutton_edit" style="display:none" onclick="BRAVOTRAN_edit_ajax()" class="button button-primary"><?php _e('Edit Translation','bravo-translate') ?></button>
<?php echo '<img src="https://media.giphy.com/media/swhRkVYLJDrCE/giphy.gif" id="BRAVOTRANgif" style="display:none;width:80px;">'; ?>
</div> 

<div id="BRAVOTRAN_table_container">
 <!--begin of BRAVOTRANtablexss-->
    <table class="wp-list-table widefat fixed striped table-view-list pages bravoTable"> 
    <tr>
        <td class="bravoCell bravoCellHeader"><?php _e('Text to translate','bravo-translate')?></td>
        <td class="bravoCell bravoCellHeader"><?php _e('Your translation','bravo-translate') ?></td>
        <td style="width:40px"></td>
    </tr>
   
    <?php
     
     global $wpdb;
     $sql="SELECT * FROM `".$wpdb->base_prefix."bravo_translate` ORDER BY `wp_bravo_translate`.`ID` DESC";
     $results=$wpdb->get_results($sql);
     if($wpdb->num_rows>0){
    foreach($results as $result){
       echo  '<tr id="trID"'.$result->ID.'"><td id=forID'.$result->ID.' class="bravoCell">'.$result->searchFor.'</td>
       <td id="toID'.$result->ID.'"'." class='bravoCell'>".$result->replaceBy."</td>
       <td style='width:40px'><span class='edit BRAVOTRANminiButton'><a onclick='BRAVOTRAN_edit(".$result->ID.")'>".__('Edit','bravo-translate')."</a> <br><span class='trash BRAVOTRANminiButton'><a onclick='BRAVOTRAN_delete(".$result->ID.")'>".__('Delete','bravo-translate')."</a></td></tr>";
    }
}
else
echo'<tr><td class="bravoCell" colspan="2">'.__('No translations so far.','bravo-translate').'</td></tr>';
    ?>
    
    </table>
     <!--end of BRAVOTRANtablexss-->
    </div>
   
    </div>
    <?php
}    



