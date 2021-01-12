<?php
//nos gustaría conseguir por lo menos:
//-para todo el texto stripped (aunque tb saliera dentro de no stripped)
//-los valores de value=" 
function currada($string,$reemplazar,$html){
  
  $html=" ".$html;
  
  if(strpos($html,$string)==false) {
     $output=$html;
      }
      else{
        $output="";
      $array=explode($string,$html);
     
      //para cada trozito de corte por el caracter
      $posicionHTML=strlen($array[0])-1;
      for($i=0;$i<(count($array)-1);$i++){
          
          //echo "posicion HTTML:".$html[$posicionHTML]."<br>";

              $etiqueta="";
              $position="";
              $atributo="";
              $comillas=0;
              //determinamos si es un valor de atributo value="
              $len=$posicionHTML;
              $char="";
              for($e=0;$char!="<";$e++){
                  
                  $char=$html[$len-$e];
                  //echo $char."<br>".$e;
                  if($char==">") {
                      if($position=="") $position="entre";
                  
                  }
                  if($char=='"') {
                      $comillas++;
                      if($comillas==1) {
                          if($html[$len-($e+1)]=="="){
                              for($u=$e+2;$html[$len-$u]!=" ";$u++)
                                  $atributo.=$html[$len-($u)];
                              }
                              $atributo=strrev($atributo);
                             
                          }
                        
                  }
                  if($char=="<"){
                      if($position=="") $position="dentro";
                      //aqui tenemos que sacar la etiquet0
                       //calculamos la posicion en la que esta el cursor:
                      
                      $df=$e-1;

                      $cadeneta=substr($html,0,$len);
                      $cadeneta=substr($cadeneta,-$df);
                      $cadeneta=explode(" ",$cadeneta);
                      $cadeneta=$cadeneta[0];
                      $etiqueta=$cadeneta;
                      //echo "position:".$position.",etiqueta:".$cadeneta."<br>";
                      //lets check if ocurrence of the string is inside a word
                      $prevChar=$html[$len];
                      $nextChar=$html[$len+strlen($string)+1];
                      $insideWord=insideWord($prevChar,$nextChar);
                  
                      if(($etiqueta!="script")AND($etiqueta!="style") AND ($position=="entre")AND !$insideWord){
                          $output=$output.$array[$i].$reemplazar;
                      }
                      else if(($position="dentro")AND($atributo=="placeholder")){
                          $output=$output.$array[$i].$reemplazar;
                      }
                      else{
                          $output=$output.$array[$i].$string;
                      }
                      break;
                  }

              }
              //echo "see".$posicionHTML."<br>";
          
          $imasuno=$i+1;
          $posicionHTML=$posicionHTML+strlen($string)+strlen($array[$imasuno]);
      }
      $output=$output.$array[$i];
  }
  //echo "<br>".$output;
  return $output;
}
add_filter( 'wp_lazy_loading_enabled', '__return_false' );
function isText($html,$string,$striped){
  // si ocurrencias en striped=ocurrencias de html entonces es texto
 if (mb_substr_count($html,$string)==mb_substr_count($striped,$string)) return true;
 }

 function isTExt2($html,$search,$replace){
  //ocurencias
  $count=mb_substr_count($html,$search);
  
  if($count>0){
    $array=explode($search,$html);
  $frank=$array[0];
      for ($i=0;$i<$count;$i++){
           if((substr($array[$i], -1)=="<") OR ((substr($array[$i], -1)==" ") AND ($array[$i+1][0]!="="))) $frank.=$array[$i].$replace; else $frank.=$array[$i].$search;
      }
      $frank=$frank.$array[$count];
      $html=$frank."ññ";
  }
  return $html."niiiii".$search."niiicount:".$count."-";
  }
function BRAVOTRAN_Replace($html) {
  error_log('time1:'.microtime()."nii");
   $uriNoParams=$_SERVER['REQUEST_URI'];
    if(strpos($uriNoParams,"?")!=false) $uriNoParams=explode("?",$_SERVER['REQUEST_URI']);
    $uriNoParams=$uriNoParams[0];
  if (($_SERVER['REQUEST_URI']=="/wp-admin/admin.php?page=bravo-translate")OR($uriNoParams=="/wp-json/wp/v2/BRAVOTRAN_delete")OR($uriNoParams=="/wp-json/wp/v2/BRAVOTRAN_create")OR($uriNoParams=="/wp-json/wp/v2/BRAVOTRAN_update")){
      $exceptionBravo=true;
      $array_table1=explode('<!--begin of BRAVOTRANtablexss-->',$html);
      $array_table2=explode('<!--end of BRAVOTRANtablexss-->',$array_table1[1]);
      $intact=$array_table2[0];
  }
    $sql="SELECT * FROM `wp_bravo_translate`";
    $striped=strip_tags($html);
    global $wpdb;
    $results=$wpdb->get_results($sql);
    $prefix="";
    if(strpos($html,"<body")!=false) 
      {
        $array=explode("<body",$html);
        $html=$array[1];
        $prefix=$array[0]."<body";
      }
    foreach($results as $clave=>$tr){
    
      //si solo esta en striped entonces sin problemas
    /*  if(mb_substr_count($html,$tr->searchFor)==mb_substr_count($striped,$tr->searchFor)){
        error_log("solo en stripped".$clave."-".$tr->searchFor);
        $html=str_replace($tr->searchFor,$tr->replaceBy,$html);
        //en este caso se supone que 1 o solo esta en tags o está tanto en tags como en stripped
        //si la cadena que buscamos tiene espacios en medio tampoco hay problema
      }else */ if(espaciosEnMedio($tr->searchFor)){
        $html=str_replace($tr->searchFor,$tr->replaceBy,$html);
      }
        //esto ya es más problemático porque el string está tanto en el stripped como en el código
      else{
        error_log("problematic".$clave."-".$tr->searchFor);
        //en el caso de que sea un boton o un select o algo así lo traducimos
      if(strpos($html,"value=".$tr->searchFor)!=false) $html=str_replace("value=".$tr->searchFor,"value=".$tr->replaceBy,$html);
      else if(strpos($html,"value='".$tr->searchFor)!=false) $html=str_replace("value='".$tr->searchFor,"value=".$tr->replaceBy,$html);
      
      else{
        $html=currada($tr->searchFor,$tr->replaceBy,$html);
      }
      //si prefijo es espacio o &npsp; o < o value='  o value="  
      //tenemos un problema tambien con el css
      //cuidado con js:en el caso de los sespacios en blancos hay que verificar que sufijo !0 = o de espacio.=
      //if((strpos($html," ".$tr->searchFor)!=false)AND(strpos($html,"=".$tr->searchFor)==false)AND(strpos($html," =".$tr->searchFor)==false)) $html=str_replace(" ".$tr->searchFor," ".$tr->replaceBy,$html);
      //if(strpos($html,"&nbsp;".$tr->searchFor)!=false) $html=str_replace("&nbsp;".$tr->searchFor,"&nbsp;".$tr->replaceBy,$html);
      //if(strpos($html,">".$tr->searchFor)!=false) $html=str_replace(">".$tr->searchFor,">".$tr->replaceBy,$html);
      
      //else $html=isTExt2($html,$tr->searchFor,$tr->replaceBy);
    }
  }
  //si hay más ocurrencias sin strip lo que se puede hacer es un explode y pegarles a todas un id. Después ver 
  //cuales son los ids buenos. Despues solo replace los id buenos.//aunque podría ser lento la verdad. 

   //queremos que no se afecte al html, css, o al javascript. Como hacerlo?

   // si ocurrencias en striped=ocurrencias de html entonces es texto
   // si ocurrencis en striped < ocurrencias en html entonces se puede liar
   //entonces podemos hacer lo siguiente:

   //para cada apricion identificamos la posicion. Si retrocediendo aparece un espacio o < entonces era texto
   //si aparece " pero luego aparece = entonces era tambien un texto a traducir

    // modify uffer here, and then return the updated code
    error_log('time2:'.microtime()."noo");
    if($exceptionBravo){
      $array_table1=explode('<!--begin of BRAVOTRANtablexss-->',$html);
      $array_table2=explode('<!--end of BRAVOTRANtablexss-->',$html);
      $html=$array_table1[0].'<!--begin of BRAVOTRANtablexss-->'.$intact.'<!--end of BRAVOTRANtablexss-->'.$array_table2[1];
    }
    return $prefix.$html;
}
  
  function BRAVOTRAN_buffer_start() { ob_start("BRAVOTRAN_Replace"); }
  
  function BRAVOTRAN_buffer_end() { if (ob_get_length() > 0) { ob_end_clean(); }}
  
 add_action('wp_loaded', 'BRAVOTRAN_buffer_start');
 add_action('shutdown', 'BRAVOTRAN_buffer_end');


 function BRAVOTRAN_create(WP_REST_Request $request){
$textTo=$request->get_param('textTo');
$yourTranslation=$request->get_param('yourTranslation');
$sql="INSERT INTO `wp_bravo_translate` (`ID`, `searchFor`, `replaceBy`) VALUES (NULL, '$textTo', '$yourTranslation');";

global $wpdb;
$results=$wpdb->get_results($sql);

$sql="SELECT * FROM `wp_bravo_translate` ORDER BY `wp_bravo_translate`.`ID` DESC";
$results=$wpdb->get_results($sql);
$response='<div id="message"  style="float:left;width:90%;margin-bottom:10px" class="updated notice is-dismissible">
<p>1 traducción añadida</p><button type="button" onclick="BRAVOTRANdismiss()" class="notice-dismiss">
<span class="screen-reader-text">Descartar este aviso.</span></button>
</div>
<!--begin of BRAVOTRANtablexss-->
<table class="wp-list-table widefat fixed striped table-view-list pages bravoTable"><tr><td class="bravoCell bravoCellHeader">TEXT TO TRANSLATE</td><td class="bravoCell bravoCellHeader">YOUR TRANSLATION</td> <td style="width:40px"></td></tr>';
if($wpdb->num_rows>0){
foreach($results as $result){
   $response.='<tr id="trID"'.$result->ID.'"><td id=forID'.$result->ID.' class="bravoCell">-'.$result->searchFor.'-</td><td id="toID'.$result->ID.'"'." class='bravoCell'>-".$result->replaceBy."-</td>
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
  <p>1 traducción editada</p><button type="button" onclick="BRAVOTRANdismiss()" class="notice-dismiss">
  <span class="screen-reader-text">Descartar este aviso.</span></button>
  </div>
  <!--begin of BRAVOTRANtablexss-->
  <table class="wp-list-table widefat fixed striped table-view-list pages bravoTable"><tr><td class="bravoCell bravoCellHeader">TEXT TO TRANSLATE</td><td class="bravoCell bravoCellHeader">YOUR TRANSLATION</td> <td style="width:40px"></td></tr>';
  if($wpdb->num_rows>0){
  foreach($results as $result){
     $response.='<tr id="trID"'.$result->ID.'"><td id=forID'.$result->ID.' class="bravoCell">-'.$result->searchFor.'-</td><td id="toID'.$result->ID.'"'." class='bravoCell'>-".$result->replaceBy."-</td>
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
    <p>1 traducción eliminada</p><button type="button" onclick="BRAVOTRANdismiss()" class="notice-dismiss">
    <span class="screen-reader-text">Descartar este aviso.</span></button>
    </div>
    <!--begin of BRAVOTRANtablexss-->
    <table class="wp-list-table widefat fixed striped table-view-list pages bravoTable"><tr><td class="bravoCell bravoCellHeader">TEXT TO TRANSLATE</td><td class="bravoCell bravoCellHeader">YOUR TRANSLATION</td> <td style="width:40px"></td></tr>';
    if($wpdb->num_rows>0){
    foreach($results as $result){
       $response.='<tr id="trID"'.$result->ID.'"><td id=forID'.$result->ID.' class="bravoCell">-'.$result->searchFor.'-</td><td id="toID'.$result->ID.'"'." class='bravoCell'>-".$result->replaceBy."-</td>
       <td style='width:40px'><span class='edit BRAVOTRANminiButton'><a onclick='BRAVOTRAN_edit(".$result->ID.")'>Edit</a> <br><span class='trash BRAVOTRANminiButton'><a onclick='BRAVOTRAN_delete(".$result->ID.")'> Delete</a></td></tr>";
    }
    }
    $response.="</table><!--end of BRAVOTRANtablexss-->";
    
    echo $response;
    }
    function espaciosEnMedio($cadeneta){
      while (substr($cadeneta, 0, 1)==" "){
          $long=strlen($cadeneta)-1;
          $cadeneta=substr($cadeneta, 1, $long);
          }
      while (substr($cadeneta, -1)==" "){
          $long=strlen($cadeneta)-1;
          $cadeneta=substr($cadeneta, 0 ,$long);
          }
      $array=$array=explode(" ",$cadeneta);
      if(count($array)<2) return false; else return true;
  }
  function insideWord($prevChar,$nextChar){
    echo $prevChar."<br>";
    echo $nextChar."<br>";
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
 ?>