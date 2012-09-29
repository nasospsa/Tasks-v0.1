<?php




// Working Code for active user except yourself


if(!defined('TBL_ACTIVE_USERS')) {
  die("Error processing page");
}

$q = "SELECT username FROM ".TBL_ACTIVE_USERS
    ." ORDER BY timestamp DESC,username";
$result = $database->query($q);

$active_users = array();

// Error occurred, return given name by default
$num_rows = mysql_numrows($result);
if(!$result || ($num_rows < 0)){
   //echo "Error displaying info";

}
else if($num_rows > 0){
    for($i=0; $i<$num_rows; $i++){
        $user = mysql_result($result,$i,"username");
        if ($user!=$session->username){
            $active_users[] = $user;
        }
    }
}
$active_users_count = count($active_users);


?>
