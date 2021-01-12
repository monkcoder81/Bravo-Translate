<?php
//reglas si está entre una etiqueta que no es script ni style entonces reemplazar
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
$html='<div id="main-content">
<div class="container">
<div id="content-area" class="clearfix"> <input type="text" placeholder="lo que a mi me de la gana">
    <div id="left-area">
                                    <article id="post-1" class="et_pb_post post-1 post type-post status-publish format-standard hentry category-sin-categoria">
                                    <div class="et_post_meta_wrapper">
                    <h1 class="entry-title">¡Hola, class mundo!</h1>';
//echo strip_tags($string);
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
echo currada("lo que a mi me de la gana","jili",$html);

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
/*
//echo isText($html,"post");
function isText($html,$string){
 // si ocurrencias en striped=ocurrencias de html entonces es texto
if (mb_substr_count($html,$string)==mb_substr_count(strip_tags($html),$string)) return "si";
}


function isTExt2($html,$search,$replace){
//ocurencias
$count=mb_substr_count($html,$search);
$array=explode($search,$html);
$frank=$array[0];
if($count>0){
    for ($i=0;$i<$count;$i++){
         if((substr($array[$i], -1)=="<") OR ((substr($array[$i], -1)==" ") AND ($array[$i+1][0]!="="))) $frank.=$array[$i].$replace; else $frank.=$array[$i].$search;
    }
    $html=$frank.$array[$count];
    
}
return $html;
}
//$output=isTExt2($html,"classee","po");
//echo $output;
$string="    -ca ca-     ";
$cadeneta=$string;
while (substr($cadeneta, 0, 1)==" "){
$long=strlen($cadeneta)-1;
$cadeneta=substr($cadeneta, 1, $long);
}
while (substr($cadeneta, -1)==" "){
    $long=strlen($cadeneta)-1;
    $cadeneta=substr($cadeneta, 0 ,$long);
    }
//echo $cadeneta;
$array=explode(" ",$cadeneta);
//echo count($array);


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
//echo "html primero";
//echo $html;

//echo "ahora el strip";
// espaciosEnMedio('asddsa   asdadads');
$striped=strip_tags($html);
//echo $striped;
*/
?>