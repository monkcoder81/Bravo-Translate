<?php

function BRAVOTRAN_add_the_admin(){
    add_menu_page('Bravo Translate', 'Bravo Translate', 'activate_plugins', 'bravo-translate', 'BRAVOTRAN_admin', 'dashicons-translation');
    }
   
 add_action('admin_menu', 'BRAVOTRAN_add_the_admin');

    function BRAVOTRAN_admin(){
    ?>
<style>
.bravoTable{
width: 800px;
max-width:96%;
margin:40px auto;    
}
.bravoCell{
   text-align:center; 
   border-right:1px;
}
.bravoCellHeader{
 font-size:16px!important;
 font-weight:600;   
}
.BRAVOTRANminiButton{
  cursor:pointer;  
}
.BRAVOTRANminiButton a:hover{
color:#a00; 
}
</style>
<script>
function BRAVOTRAN_create() {
    document.getElementById("BRAVOTRANbutton").style.display='none';
    document.getElementById("BRAVOTRANgif").style.display='inline';
    textTo=document.getElementById("textToId").value;
    yourT=document.getElementById("YourTrId").value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        document.getElementById("BRAVOTRAN_table_container").innerHTML=this.responseText;
        document.getElementById("BRAVOTRANbutton").style.display='inline';
        document.getElementById("BRAVOTRANgif").style.display='none';
        document.getElementById("BRAVOTRANgif").value='';
        document.getElementById("textToId").value='';
        document.getElementById("YourTrId").value='';
        textToId
    }
  };
  xhttp.open("GET", "http://pruebascris.es/wp-json/wp/v2/BRAVOTRAN_create?textTo="+textTo+"&yourTranslation="+yourT, true);
  xhttp.send();
}

function BRAVOTRAN_edit(id){
    document.getElementById("BRAVOTRANbutton").style.display='none';
    text=document.getElementById("forID"+id).innerHTML;
    text=text.slice(0, -1);
    text=text.substring(1);
    document.getElementById("textToId").value=text;
    text=document.getElementById("toID"+id).innerHTML;
    text=text.slice(0, -1);
    text=text.substring(1);
    document.getElementById("YourTrId").value=text;
    document.getElementById("BRAVOTRANbutton_edit").style.display='inline';
    document.getElementById("BRAVOTRAN_edit_hidden").value=id;
    document.getElementById("textToId").focus();
  };
  function BRAVOTRAN_edit_ajax(id) {
    id=document.getElementById("BRAVOTRAN_edit_hidden").value;
    document.getElementById("BRAVOTRANbutton_edit").style.display='none';
    document.getElementById("BRAVOTRANgif").style.display='inline';
    textTo=document.getElementById("textToId").value;
    yourT=document.getElementById("YourTrId").value;
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        document.getElementById("BRAVOTRAN_table_container").innerHTML=this.responseText;
        document.getElementById("BRAVOTRANbutton").style.display='inline';
        document.getElementById("BRAVOTRANgif").style.display='none';
        document.getElementById("BRAVOTRANgif").value='';
        document.getElementById("textToId").value='';
        document.getElementById("YourTrId").value='';
    }
  };
  xhttp.open("GET", "http://pruebascris.es/wp-json/wp/v2/BRAVOTRAN_update?textTo="+textTo+"&yourTranslation="+yourT+"&id="+id, true);
  xhttp.send();
}
function BRAVOTRANdismiss(){
document.getElementById("message").style.display="none";
}

function BRAVOTRAN_delete(id) {
    document.getElementById("BRAVOTRANbutton").style.display='none';
    document.getElementById("BRAVOTRANbutton_edit").style.display='none';
    document.getElementById("textToId").focus();
    document.getElementById("textToId").value='';
    document.getElementById("YourTrId").value='';
    document.getElementById("BRAVOTRANgif").style.display='inline';

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        document.getElementById("BRAVOTRAN_table_container").innerHTML=this.responseText;
        document.getElementById("BRAVOTRANbutton").style.display='inline';
        document.getElementById("BRAVOTRANgif").style.display='none';
    }
  };
  xhttp.open("GET", "http://pruebascris.es/wp-json/wp/v2/BRAVOTRAN_delete?ID="+id, true);
  xhttp.send();
}
</script>

<div id="wp-content">
<input type="hidden" id="BRAVOTRAN_edit_hidden" value="0">

<table style="position:relative" class="wp-list-table widefat fixed striped table-view-list pages bravoTable">
<tr>
    <td class="bravoCell bravoCellHeader">TEXT TO TRANSLATE</td>
    <td class="bravoCell bravoCellHeader">YOUR TRANSLATION</td>
</tr>
<tr>
    <td class="bravoCell"><input id="textToId" type="text" style="width:100%"></td>
    <td class="bravoCell"><input id="YourTrId" type="text" style="width:100%"></td>
   
</tr>



</table>


<div style="text-align:center;height:80px">
<button type="button" id="BRAVOTRANbutton" onclick="BRAVOTRAN_create()" class="button button-primary">Add Translation</button>
<button type="button" id="BRAVOTRANbutton_edit" style="display:none" onclick="BRAVOTRAN_edit_ajax()" class="button button-primary">Edit Translation</button>
<?php echo '<img src="https://media.giphy.com/media/swhRkVYLJDrCE/giphy.gif" id="BRAVOTRANgif" style="display:none;width:80px;">'; ?>
</div> 

<div id="BRAVOTRAN_table_container">
 <!--begin of BRAVOTRANtablexss-->
    <table class="wp-list-table widefat fixed striped table-view-list pages bravoTable">
    <tr>
        <td class="bravoCell bravoCellHeader">TEXT TO TRANSLATE</td>
        <td class="bravoCell bravoCellHeader">YOUR TRANSLATION</td>
        <td style="width:40px"></td>
    </tr>
   
    <?php
    global $wp;
    
     $sql="SELECT * FROM `wp_bravo_translate` ORDER BY `wp_bravo_translate`.`ID` DESC";
     global $wpdb;
     $results=$wpdb->get_results($sql);
     if($wpdb->num_rows>0){
    foreach($results as $result){
       echo  '<tr id="trID"'.$result->ID.'"><td id=forID'.$result->ID.' class="bravoCell">-'.$result->searchFor.'-</td>
       <td id="toID'.$result->ID.'"'." class='bravoCell'>-".$result->replaceBy."-</td>
       <td style='width:40px'><span class='edit BRAVOTRANminiButton'><a onclick='BRAVOTRAN_edit(".$result->ID.")'>Edit</a> <br><span class='trash BRAVOTRANminiButton'><a onclick='BRAVOTRAN_delete(".$result->ID.")'> Delete</a></td></tr>";
    }
}
else
echo'<tr><td class="bravoCell" colspan="2">No transaltions so far</td></tr>';
    ?>
    
    </table>
     <!--end of BRAVOTRANtablexss-->
    </div>
   
    </div>
    <?php
}    



