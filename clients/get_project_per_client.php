<?php
include("../include/session.php");
$q = $_POST["id"];
$q = explode(":", $q);
$name = explode(" ", $q[1]);

if (!preg_match("/^[0-9]{1,}$/",$q[0])) {
                echo "<option value='0'>No Client Name</option>";
            }
else{
	$id = $q[0];
	$name = $name[1];
	$sql = "select title as project from projects inner join clients on projects.Client_ID = clients.ID where projects.Client_ID ='$id' and clients.lName='$name'";
	//$sql = "select title as project from projects where Client_ID ='$id'";
	//$sql = "select fName as project from clients where lName ='$name'";
	$rsd = mysql_query($sql);
	$num_projects = mysql_num_rows($rsd);
	if ($num_projects >0){
	while($rs = mysql_fetch_array($rsd)) {
	    $input_lenght = strlen($rs['project']);
		echo "<option value='".$rs['project']."' class='dropDown'>".$rs['project']."</option>\n";
			//$cname[] = $rs['project'];
	}
	} else {
		echo "<option value='0' class='dropDown'>This Client has no Projects!!</option>";
	}
}
?>

