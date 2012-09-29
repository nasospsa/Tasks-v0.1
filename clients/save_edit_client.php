<?php
include("../include/session.php");
$id = $_POST["elementid"];
$value = $_POST["newvalue"];
$id = explode("@", $id);

$field = $id[0];
$id = $id[1];

$q = "UPDATE " . TBL_CLIENTS . " SET " . $field  . " = '$value' WHERE ID = '$id'";
       if ($database->query($q)){
           echo $value;
       } else echo "Error";
?>
