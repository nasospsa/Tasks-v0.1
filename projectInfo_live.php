<?php
include("include/session.php");

$selected_project=$_GET["p"];
if (is_array($session->avail_projects)){
	if (in_array($selected_project,array_keys($session->avail_projects))) $found=true;
}

if(!$session->isPA() or !$session->logged_in or !$found){
   header("Location: main.php");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Πληροφορίες του project "<? echo $selected_project; ?>" | <? echo $session->web_name ?></title>
<link rel="stylesheet" type="text/css" href="styling/style.css"/>
<link rel="stylesheet" type="text/css" href="js/jdpicker_1.0.3/jdpicker.css"/>
</head>

<body>
<div id="wrapper">
    <div id="mainmenu">
        <ul>
            <? if ($session->isWokring()){ ?>
            <li><a href="main.php?v=myTasks">My Tasks</a></li>
            <li id="current"><a href="main.php?v=All">All</a></li>
            <li><a href="main.php?v=Authored">Authored</a></li>
            <? }
            else {?>
            <li><a href="main.php?v=All">Current</a></li>
            <? }?>
            <li><a href="main.php?v=Completed">Completed</a></li>
            <? if($session->isSuperAdmin()){?>
            <li><a href="admin/admin.php">Admin Center</a></li>
            <? }?>
        </ul>
    </div>

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
    </div>
    </div>
    </div>

    <div id="center">
        <?
				if(isset($_SESSION['success'])){
				   /* Successful Message */
				   if ($_SESSION['success']) {?>
                   <div class="success">
                   	<span class="ico"></span>
                    <span class="message success_txt"><?  echo $_SESSION['header'] ?></span>
                   </div>
                   <? }
				   /* Failure Message*/
                   else  {?>
                   <div class="failure">
                   	<span class="ico"></span>
                    <span class="message fail_txt"><? echo $_SESSION['header'] ?></span>  
                   </div> 
				   <? 
				   }
				   unset($_SESSION['success']);
				   unset($_SESSION['header']);
				}
				?>
        
        <div class="content-box">
            <div class="content-box-header">
                <h2>Πληροφορίες:  <a href="main.php?p=<? echo $selected_project; ?>"><? echo $selected_project; ?></a></h2>
                <a href="#!" class="credentialsSave_btn"></a>
            </div>
            <div class="content-box-content">
                <pre>Feature in Alpha Stage.. soon to be implemented
            --decide on div making for groups--
                </pre>
                <form name="infoProject_form" action="process.php" method="POST">
                <? $credentials = $session->getCredentialsOfProject($selected_project);
                $group = $credentials[0][properties_group];
                $properties_counter = 0;
                
                foreach($credentials as $property){
                	if ($group!=$property[properties_group]){
                	$group=$property[properties_group];
                	$properties_counter = 0;
                ?>
                </table>
                </div>
                <div class="properties_group">
                    <input name="group_name" type="text" class="group_header" value="<? echo $property[properties_group] ?>" id="no_focus" style="width:130px" />
                    <a href="#!" class="add_group_property"></a>
                    <a href="#!" class="del_group_property"></a>
                <table class="properties_table">
                    <col class="column1" />
                    <col class="column2" />
                    <col class="column3" />
                    <col class="column4" />
                <? }
                $properties_counter++; ?>
                <tr class="property">
                    <td style="text-align:right;">
                    	<input name="property_name<? echo $properties_counter ?>" style="text-align: right; width:150px" type="text" value="<? echo $property[property_name] ?>" class="property_label" />:</td>
                    <td><input name="property_value<? echo $properties_counter ?>" type="text" value="<? echo $property[property_value] ?>" class="property_value" /></td>
                    <td></td>
                    <td>
                        <span class="property_icons">
                        <img class="handle_property" src="styling/images/move_property_16.png" />
                        <img class="delete_property" src="styling/images/delete_property_16.png" />
                        <img class="add_property" src="styling/images/add_property_16.png" />
                        <img class="add_seperator_property" src="styling/images/seperator_16.png" />
                        </span>
                    </td>
                </tr>
                <? } ?>
                
                </table>
                </div>
                <div style="text-align:right; padding-right: 20px">
	                <input type="hidden" name="subinfoproject" value="1" />
    	            <input type="hidden" name="project" value="<? echo $selected_project ?>" />
        	        <input type="hidden" id="properties_counter" name="properties_counter" value="<? echo $properties_counter ?>" />            
                    <input class="green_btn editproject_form_btn" type="submit" value="Αποθήκευση" />
                </div>
                </form>
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
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.lavalamp.js"></script>
<script type="text/javascript" src="js/jquery.corner.js"></script>
<script type="text/javascript" src="js/jdpicker_1.0.3/jquery.jdpicker.js"></script>
<script type="text/javascript" src="js/autogrow_textbox.js"></script>
<script type="text/javascript" src="js/document.js"></script>
</body>
</html>