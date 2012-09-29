<?
/**
 * UserEdit.php
 *
 * This page is for users to edit their account information
 * such as their password, email address, etc. Their
 * usernames can not be edited. When changing their
 * password, they must first confirm their current password.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 26, 2004
 */
include("include/session.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="styling/style.css"/>
<title>Επεξ/σία Στοιχείων <? echo $session->username ?> | <? echo $session->web_name ?></title>
</head>
<body>
<div id="wrapper">
	
    <div id="left">
    <div class="content-box">
    <div class="content-box-header side-header"><h3>User Panel</h3></div>
    <div class="content-box-content upanel">
        <div id="user">
        	Έχετε εισέλθει ως:<br />
        	<span id="username"><? echo $session->username ?></span><a href="useredit.php">(edit)</a>
                <img src="<? echo ($session->userinfo[avatar]=="")?GENERIC_AVATAR:$session->userinfo["avatar"] ?>" width="160"  />
            <a id="logout" href="process.php">Logout</a><br />
            Last login:<br />
			<? echo $session->time_last ?><br />
            <a href="#">History</a>
        </div>
        <div id="main_menu">
        	<a href="main.php">Back to Main Menu</a>
        </div>
        <?
		if($session->isSuperAdmin()){
			?>
			<div id="admincenter">
				<a href="admin/admin.php">Admin Center</a>
			</div>
			<?
		}
		?>
    </div>
    </div>
    </div>
    
        
	<div id="center">
    	<div class="content-box">
       
<?
/**
 * User has submitted form without errors and user's
 * account has been edited successfully.
 */
if(isset($_SESSION['useredit'])){
   unset($_SESSION['useredit']);
   ?>
   <div class="content-box-header"><h2>User Account Edit Success!</h2></div>
   
   <div class="content-box-content">
   <p><b><? echo $session->username ?></b>, your account has been successfully updated.<br />
   <a href="main.php">Main</a>.</p>
   </div>
<?
}
else{
?>

<?
/**
 * If user is not logged in, then do not display anything.
 * If user is logged in, then display the form to edit
 * account information, with the current email address
 * already in the field.
 */
if($session->logged_in){
?>
<div class="content-box-header"><h2>User Account Edit : <? echo $session->username; ?></h2></div>
<div class="content-box-content">
<?
if($form->num_errors > 0){
   echo "<span><font size=\"2\" color=\"#ff0000\">".$form->num_errors." error(s) found</font></span>";
}
?>
<form action="process.php" method="POST">
<table class="user_details" border="0" cellspacing="0" cellpadding="3">
    <col />
    <col style="width:314px" />
    <col />

<tr>
<td>Current Password:</td>
<td><input type="password" name="curpass" maxlength="30" value="
<?echo $form->value("curpass"); ?>" /></td>
<td><? echo $form->error("curpass"); ?></td>
</tr>
<tr>
<td>New Password:</td>
<td><input type="password" name="newpass" maxlength="30" value="
<? echo $form->value("newpass"); ?>" /></td>
<td><? echo $form->error("newpass"); ?></td>
</tr>
<tr>
<td>Email:</td>
<td><input type="text" name="email" maxlength="30" value="
<?
if($form->value("email") == ""){
   echo $session->userinfo['email'];
}else{
   echo $form->value("email");
}
?>" />
</td>
<td><? echo $form->error("email"); ?></td>
</tr>
<tr>
<td>Avatar URL:</td>
<td><input id="avatar_url" type="text" name="avatar_url" value="
<?
if($form->value("avatar_type") == "") {
   if (!preg_match("%^http://".$_SERVER["HTTP_HOST"]."%",$session->userinfo['avatar'])){
       echo $session->userinfo['avatar'];
       $avatar_type = "url";
   }
   else {
       $avatar_type = "predefined";
       $class = "disabled";
   }
}
else{
    if ($form->value("avatar_type") == "url"){
        echo $form->value("avatar_url");
        $avatar_type = "url";
    }
    else{
        $avatar_type = "predefined";
        $class = "disabled";
    }
}
?>" class="<? echo $class ?>"/>
</td>
<td><? echo $form->error("avatar"); ?></td>
</tr>
<tr>
<td>(predefined)</td>
<td id="avatars_td" colspan="2" <? if ($class!="disabled") echo "class=\"disabled\"" ?> >
    <?  $handle = opendir('styling/avatars');
        $counter = -1;
    ?>
    <table id="0" class="avatars current_avat" cellpadding="0" cellspacing="0" style="display:block" >
        <tr>
        <?
        while (false !== ($file = readdir($handle))) {
            if (($file != '.') && ($file != '..')){
            $counter++;
            if (($counter%8 == 0) && ($counter != 0)){
                echo "</tr></table><table id=\"".($counter/8)."\" class=\"avatars\" cellpadding=\"0\" cellspacing=\"0\" ><tr>";
            }
            if (($counter%4 == 0) && ($counter != 0)){
                echo "</tr><tr>";
            }
            ?>
            <td>
                <a class="select_avatar" href="#!">
                <img src="styling/avatars/<? echo "$file\n"; ?>" />
                
                <br />
                <input type="radio" name="avatar" value="styling/avatars/<? echo $file; ?>" <?
                $debug = "http://".$_SERVER["PHP_SELF"]."/styling/avatars/".$file;
                if (($session->userinfo['avatar']=="http://".$_SERVER["HTTP_HOST"]."/styling/avatars/".$file) && ($form->value("avatar")=='')){
                    echo "checked=\"checked\"";
                    $current = floor($counter/8)+1;
                }
                if (($avatar_type=="predefined") && ($form->value("avatar")==("styling/avatars/".$file))){
                    echo "checked=\"checked\"";
                    $current = floor($counter/8)+1;
                }

                ?>  />
                </a>
            </td>
        <? } } ?>
        </tr>
    </table>
    <div id="pagination_avatars">
        <? $max = floor($counter/8)+1 ?>
        <input type="button" id="prev_avatar_page" value="Previous" class="blue_btn"/>
        <span id="pager">Page 1 of <? echo $max ?></span>
        <input type="button" id="next_avatar_page" value="Next" class="blue_btn"/>
        <input type="hidden" id="no_pages" value="<? echo $max  ?>" />
        <input type="hidden" id="curr_page" value="<? echo $current ?>" />
        <input type="hidden" id="avatar_type" name="avatar_type" value="<? echo $avatar_type ?>" />
    </div>
</td>
</tr>
<tr><td colspan="3" align="right">
<input type="hidden" name="subedit" value="1" />
<input type="submit" value="Edit Account" class="green_btn" /></td></tr>
</table>
</form>
</div>
<?
}
}
?>
</div>
</div>

<div id="right">
    <div class="content-box">
        <div class="content-box-header side-header">
            <h3>Gan-Web</h3>
        </div>
        <div class="content-box-content clock-tab">
            <script type="text/javascript">var clocksize='150px';</script>
            <script type="text/javascript" src="http://gheos.net/js/clock.js"></script>
            <br />
            <div id="miniclock"><noscript>Enable JS to see clock</noscript></div>
            <script type="text/javascript" src="js/date-clock.js"></script>
        </div>
    </div> 
</div>
    <div style="clear:both"></div>

</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.corner.js"></script>
<script type="text/javascript" src="js/document.js"></script>
</body>
</html>