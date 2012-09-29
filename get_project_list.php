<?php
include("include/session.php");
$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "select DISTINCT title as proj_title from projects where title LIKE '%$q%'";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$cname = $rs['proj_title'];
	echo "$cname\n";
}
?>