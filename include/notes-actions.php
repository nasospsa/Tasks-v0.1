<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$_GET["action"] = "notes";
include_once("session.php");


$id = $_POST["id"];
$note = $_POST["text"];
$action = $_POST["action"];

if ($id==""){
    //INSERT STM
    $q = "INSERT INTO notes VALUES (null,'".$note."','".$session->username."')";
    $query = $database->query($q);
    $id = mysql_insert_id();
}
else{
    if ($action!="delete"){
        //UPDATE STM
        $q = "UPDATE notes SET note = '".$note."' WHERE id = ".$id;
    }
    else{
        //DELETE STM
        $q = "DELETE FROM notes WHERE id = ".$id;
    }
    $query = $database->query($q);
}
echo $id;

?>
