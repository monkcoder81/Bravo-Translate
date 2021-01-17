<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

function BRAVOTRAN_Translate($html) {
  $uriNoParams=$_SERVER['REQUEST_URI'];
  if(strpos($uriNoParams,"?")!=false) $uriNoParams=explode("?",$_SERVER['REQUEST_URI']);
  $uriNoParams=$uriNoParams[0];
  //if we are at BRAVOTAN admin page, we dont want the translation table to be translated 
  //i have putted in the admin.php file a marcage that know let me play with explodes so I can keep this part untranslated
  // I will glue again the skipped table at the end of the function if the $expeptionAdmin value is TRUE

  $exceptionAdmin=false;
  if (($_SERVER['REQUEST_URI']=="/wp-admin/admin.php?page=bravo-translate")OR($uriNoParams=="/wp-json/bravo-translate/BRAVOTRAN_delete")OR($uriNoParams=="/wp-json/bravo-translate/BRAVOTRAN_create")OR($uriNoParams=="/wp-json/bravo-translate/BRAVOTRAN_update")){
    
    $exceptionAdmin=true;
      $array_table1=explode('<!--begin of BRAVOTRANtablexss-->',$html);
      $array_table2=explode('<!--end of BRAVOTRANtablexss-->',$array_table1[1]);
      $intact=$array_table2[0];
  }
  //no mystery here, we take the translations from database
    $sql="SELECT * FROM `wp_bravo_translate`  ORDER BY CHAR_LENGTH(`searchFor`) DESC";
    global $wpdb;
    $results=$wpdb->get_results($sql);

    //I do not want to translate the header so I skip it
    if(strpos($html,"<body")!=false) 
      {
        $array=explode("<body",$html);
        $html=$array[1];
        
        //I will glue this later
        $prefix=$array[0]."<body";
      }
      //this is where each translation is analyzed and its ocurrences are eventually replaced
    foreach($results as $clave=>$tr){
   
        $html=BRAVOTRAN_Analyse_HTML($tr->searchFor,$tr->replaceBy,$html);
   
  }
 // I glue again the skipped table if we are at the admin page
    if($exceptionAdmin){
      $array_table1=explode('<!--begin of BRAVOTRANtablexss-->',$html);
      $array_table2=explode('<!--end of BRAVOTRANtablexss-->',$html);
      $html=$array_table1[0].'<!--begin of BRAVOTRANtablexss-->'.$intact.'<!--end of BRAVOTRANtablexss-->'.$array_table2[1];
    }
    //I return the processed html preceded by the header
    return $prefix.$html;
}

function BRAVOTRAN_Analyse_HTML($searchPattern,$replace,$html){
 
    //if the search pattern does not appear at least once we dont need further analyse and we return the html without replacing
    if(strpos($html,$searchPattern)==false) {
       $output=$html;
        }
        else{
          $output="";
  
        //we are going to analyze each piece of html before and after the ocurrence of the search pattern
        //for this we explode the html with the search pattern and then we will iterate the array
        
        $array=explode($searchPattern,$html);
        
        //we set the position of HTML to analyse to be the last character of the precedent piece of HTML
        //from this position, we are going to do a reverse reading to find out the context where the ocurrence appears
        $posicionHTML=strlen($array[0])-1;
    
        for($i=0;$i<(count($array)-1);$i++){
  
                $tag="";
                $InsideOrBetweenTag="";
                $atribute="";
                $quotesFound=0;
                $len=$posicionHTML;
                $hidden=false;
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
                    
                        //we isolate the piece of html from current '<' character to 
                        $cadeneta=substr($array[$i],-$e);
                        //here we see check if there is an hidden atribute
                        if(strpos($cadeneta,'"hidden"')!=false) $hidden=true;
                        //now the name tag is extracted exploding with blank and getting the first element of array
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
                        

                        //error_log("search pat:".$searchPattern."-tag:".$tag."-position:".$InsideOrBetweenTag."-insideword:".$insideWord."-quotes:".$quotesFound."-atribute:".$atribute);
                        //if the ocurrence of the search pattern is between allowed tags and it is not inside a word, we replace
                        if((strpos($tags,$tag)!=false) AND ($InsideOrBetweenTag=="between")AND !$insideWord){
                            $output=$output.$array[$i].$replace;
                        }
                        //if inside a tag but it is the value of placeholder or value attributes we replace 
                        else if(($InsideOrBetweenTag="inside")AND(($atribute=="placeholder")OR($atribute=="value"))AND(!$hidden)){
                            $output=$output.$array[$i].$replace;
                        }
                        // in this case we do not replace
                        else{
                            $output=$output.$array[$i].$searchPattern;
                        }
                        break;
                    }
  
                }
            
            $posicionHTML=$posicionHTML+strlen($searchPattern)+strlen($array[$i+1]);
        }
        $output=$output.$array[$i];
    } 
    return $output;
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
add_action('wp_loaded', 'BRAVOTRAN_start');
add_action('shutdown', 'BRAVOTRAN_end');

function BRAVOTRAN_start() { ob_start("BRAVOTRAN_Translate"); }
  
function BRAVOTRAN_end() { if (ob_get_length() > 0) { ob_end_flush(); }}
 ?>