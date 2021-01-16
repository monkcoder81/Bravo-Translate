<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

function BRAVOTRAN_Translate($html) {

  //if we are at BRAVOTAN admin page, we dont want the translation table to be translated
  //i have putted in the admin.php file a marcage that know let me play around with explodes so I can keep this part untranslated
  // I will glue again the skipped table at the end of the function if the $expeptionAdmin value is TRUE
  if (($_SERVER['REQUEST_URI']=="/wp-admin/admin.php?page=bravo-translate")OR($uriNoParams=="/wp-json/wp/v2/BRAVOTRAN_delete")OR($uriNoParams=="/wp-json/wp/v2/BRAVOTRAN_create")OR($uriNoParams=="/wp-json/wp/v2/BRAVOTRAN_update")){
      $exceptionAdmin=true;
      $array_table1=explode('<!--begin of BRAVOTRANtablexss-->',$html);
      $array_table2=explode('<!--end of BRAVOTRANtablexss-->',$array_table1[1]);
      $intact=$array_table2[0];
  }
  //no mystery here, we take the translations from database
    $sql="SELECT * FROM `wp_bravo_translate`";
    global $wpdb;
    $results=$wpdb->get_results($sql);

    //I do not want to translate the header so I skip it
    if(strpos($html,"<body")!=false) 
      {
        $array=explode("<body",$html);
        $html=$array[1];
        $prefix=$array[0]."<body";
      }
      //this is where the translations are analyzed and their ocurrences are eventually replaced
    foreach($results as $clave=>$tr){
   
        $html=BRAVOTRAN_Analyse_HTML($tr->searchFor,$tr->replaceBy,$html);
   
  }
 // I glue again the skipped table if we are at the admin page
    if($exceptionAdmin){
      $array_table1=explode('<!--begin of BRAVOTRANtablexss-->',$html);
      $array_table2=explode('<!--end of BRAVOTRANtablexss-->',$html);
      $html=$array_table1[0].'<!--begin of BRAVOTRANtablexss-->'.$intact.'<!--end of BRAVOTRANtablexss-->'.$array_table2[1];
    }
    return $prefix.$html;
}

add_action('wp_loaded', 'BRAVOTRAN_buffer_start');
add_action('shutdown', 'BRAVOTRAN_buffer_end');

function BRAVOTRAN_buffer_start() { ob_start("BRAVOTRAN_Translate"); }
  
function BRAVOTRAN_buffer_end() { if (ob_get_length() > 0) { ob_end_flush(); }}

function BRAVOTRAN_Analyse_HTML($searchPattern,$replace,$html){
    //if the search pattern does not appear at least once we dont need further analyse and we return the html without replacing
    if(strpos($html,$searchPattern)==false) {
       $output=$html;
        }
        else{
          $output="";
  
        //we are agoing to analyze each piece of html before and after the ocurrence of the search pattern
        //for this we explode the html with the search pattern and then we will iterate the array
        
        $array=explode($searchPattern,$html);
        
        //we set the position of HTML to analyse to be the last character of the precedent piece of HTML
        //from this position, we are going to do a reverse reading to find out if the ocurrence is a text to tranlate or not
        $posicionHTML=strlen($array[0])-1;
    
        for($i=0;$i<(count($array)-1);$i++){
  
                $tag="";
                $InsideOrBetweenTag="";
                $atribute="";
                $quotesFound=0;
                $len=$posicionHTML;
                $char="";
                //we go forward till we find start of a tag
                for($e=0;$char!="<";$e++){
                    
                    $char=$html[$len-$e];
  
                    //in this case the ocurrence of the string is between an openening and closing tag, or no ending tags like <br> <hr>..
                    //so probably it is a text to translate as long it is between the allowed tags (we will see afterwards)
                    if($char==">") {
                        if($InsideOrBetweenTag=="") $InsideOrBetweenTag="between";
                    
                    }
  
                    //if the first double quote we find is precedded by =, the ocurrence of the string is an attribute value
                  
                    if($char=='"') {
                        $quotesFound++;
                        if($quotesFound==1) {
                            if($html[$len-($e+1)]=="="){
  
                              //we extract the atribute name for further decisions
                                for($u=$e+2;$html[$len-$u]!=" ";$u++)
                                    $atribute.=$html[$len-($u)];
                                }
                                $atribute=strrev($atribute);
                               
                            }
                          
                    }
                    if($char=="<"){
                        if($InsideOrBetweenTag=="") $InsideOrBetweenTag="inside";
                        //from this position we will extract the name of the tag
                        $df=$e-1;
                        //we isolate the piece of html from current '<' character to 
                        $cadeneta=substr($html,0,$len);
                        $cadeneta=substr($cadeneta,-$df);
                        //now the name tag is extracted explofing with blank and getting the first element of array
                        //in case the tag was of type: <example> we substitue > by blank
                        $cadeneta=str_replace(">"," ",$cadeneta);
                        $cadeneta=explode(" ",$cadeneta);
                        $cadeneta=$cadeneta[0];
                        $tag=$cadeneta;
  
                        //lets check if ocurrence of the string is inside a word (in that case we do not replace)
                        $prevChar=$html[$len];
                        $nextChar=$html[$len+strlen($searchPattern)+1];
                        $insideWord=insideWord($prevChar,$nextChar);
                        //we retrive the allowed tags
                        $tags=allowedTagsBetween();
                        
                        //if the ocurrence of the search pattern is between allowed tags and it is not inside a word, we replace
                        if((strpos($tags,$tag)!=false) AND ($InsideOrBetweenTag=="between")AND !$insideWord){
                            $output=$output.$array[$i].$replace;
                        }
                        //if inside a tag but it is the value of placeholder or value attributes we replace 
                        else if(($InsideOrBetweenTag="inside")AND(($atribute=="placeholder")OR($atribute=="value"))){
                            $output=$output.$array[$i].$replace;
                        }
                        // in this case we do not replace
                        else{
                            $output=$output.$array[$i].$searchPattern;
                        }
                        break;
                    }
  
                }
            
            $imasuno=$i+1;
            $posicionHTML=$posicionHTML+strlen($searchPattern)+strlen($array[$imasuno]);
        }
        $output=$output.$array[$i];
    } 
    return $output;
  }



 function BRAVOTRAN_create(WP_REST_Request $request){
      $textTo=$request->get_param('textTo');
      $yourTranslation=$request->get_param('yourTranslation');
      $sql="INSERT INTO `wp_bravo_translate` (`ID`, `searchFor`, `replaceBy`) VALUES (NULL, '$textTo', '$yourTranslation');";

      global $wpdb;
      $results=$wpdb->get_results($sql);

      $sql="SELECT * FROM `wp_bravo_translate` ORDER BY `wp_bravo_translate`.`ID` DESC";
      $results=$wpdb->get_results($sql);
      $response='<div id="message"  style="float:left;width:90%;margin-bottom:10px" class="updated notice is-dismissible">
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
        $textTo=$request->get_param('textTo');
        $yourTranslation=$request->get_param('yourTranslation');
        $id=$request->get_param('id');
        global $wpdb;
        $sql="UPDATE `wp_bravo_translate` SET `searchFor` = '".$textTo."', `replaceBy` = '".$yourTranslation."' WHERE `wp_bravo_translate`.`ID` = ".$id.";";
        $results=$wpdb->get_results($sql);
        $sql="SELECT * FROM `wp_bravo_translate` ORDER BY `wp_bravo_translate`.`ID` DESC";
        $results=$wpdb->get_results($sql);
        $response='<div id="message"  style="float:left;width:90%;margin-bottom:10px" class="updated notice is-dismissible">
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
        $id=$request->get_param('ID');
        global $wpdb;
        $sql="DELETE FROM `wp_bravo_translate` WHERE `wp_bravo_translate`.`ID` = $id";
        $results=$wpdb->get_results($sql);
        $sql="SELECT * FROM `wp_bravo_translate` ORDER BY `wp_bravo_translate`.`ID` DESC";
        $results=$wpdb->get_results($sql);
        $response='<div id="message"  style="float:left;width:90%;margin-bottom:10px" class="updated notice is-dismissible">
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

function insideWord($prevChar,$nextChar){
        $prev=false;
        $next=false;
        $alphas = array_merge(range('A', 'Z'), range('a', 'z'));
        foreach($alphas as $letter){
        if($letter==$prevChar) $prev=true;
        if($letter==$nextChar) $next=true; 
        }
        if($prev OR  $next) return true;
        else return false;
 }
    function allowedTagsBetween(){
      $tags="-a-abbr-address-article-aside-audio-b-blockquote-body-br-button-caption-cite-data-div-dt-dd-em-figcaption-footer-form-h1-h2-h3-h4-h5-h6-hr-html-i-img-input-del-ins-kbd-label-legend-li-main-mark-noscript-option-p-pre-q-s-samp-section-select-small-source-span-strong-sub-summary-sup-table-tbody-td-template-textarea-tfoot-th-time-thead-title-tr-u-ul-video-";    
      return $tags;
}
 ?>