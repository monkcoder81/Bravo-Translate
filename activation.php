<?php
function BRAVOTRAN__activation(){

global $wpdb;
$sql="CREATE TABLE `".DB_NAME."`.`".$wpdb->base_prefix."bravo_translate` ( `ID` INT NOT NULL AUTO_INCREMENT , `searchFor` TEXT NOT NULL , `replaceBy` TEXT NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB;";
echo $sql;
$wpdb->query($sql);
ENCDATA__createKeys();
}

register_activation_hook(BRAVOTRAN_FILE,"BRAVOTRAN__activation");

?>