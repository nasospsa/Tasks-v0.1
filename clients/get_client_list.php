<?php
include("../include/session.php");
$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "select DISTINCT lName as client_lname, fname as client_fname, Company_Name as client_company, ID as client_id from clients where lName LIKE '%$q%' or fname LIKE '%$q%' or Company_Name LIKE '%$q%'";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$lname = $rs['client_lname'];
        $fname = $rs['client_fname'];
        $cname = $rs['client_company'];
        $clientId = $rs['client_id'];
        
        
        //$str =  mb_strlen("$clientId: $lname $fname - $cname",'utf-8');

	echo "$clientId: $lname $fname - $cname\n";
}
?>
