<?php
include("../include/session.php");
$q = strtolower($_GET["q"]);
if (!$q) return;

$sql = "select DISTINCT username as user_name from users where username LIKE '%$q%' and userlevel<7";
$rsd = mysql_query($sql);
while($rs = mysql_fetch_array($rsd)) {
	$cname = $rs['user_name'];
	echo "$cname\n";
}
?>
