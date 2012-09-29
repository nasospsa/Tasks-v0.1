<?php
include("include/session.php");
/* @var $session Session */
$database->db_getProjectUsers;
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
<title>Επεξεργασία του project "<? echo $selected_project; ?>" | <? echo $session->web_name ?></title>
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
                <h2>Επεξεργασία <a href="main.php?p=<? echo $selected_project; ?>"><? echo $selected_project; ?></a></h2></div>
            <div class="content-box-content">
                <div>
                    <form name="editProject_form" action="process.php" method="POST">
                    <table class="newproject_tbl">
                    <col />
                    <col />
                    <tr>
                        <td style="text-align:right;">Τίτλος Project:</td>
                        <td><input type="text" name="project_title" value="<? echo $session->avail_projects[$selected_project][title] ?>" /></td>
                        <td><? echo $form->error("title"); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Περιγραφή:</td>
                        <td><textarea name="project_desc" rows="5" cols="40"><? echo $session->avail_projects[$selected_project][description] ?></textarea></td>
                        <td><? echo $form->error("desc"); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">URL:</td>
                        <td><input type="text" name="project_url" value="<?
                        if (!empty($session->avail_projects[$selected_project][URL])){echo $session->avail_projects[$selected_project][URL];}
                        else echo "http://"; ?>" /></td>
                        <td><? echo $form->error("url"); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Λήξη Domain:</td>
                        <td><input type="text" class="temp_date" name="project_domain_exp" value="<? echo $session->avail_projects[$selected_project][domain_exp] ?>" /></td>
                        <td><? echo $form->error("domain"); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Λήξη Hosting:</td>
                        <td><input type="text" class="temp_date" name="project_host_exp" value="<? echo $session->avail_projects[$selected_project][host_exp] ?>" /></td>
                        <td><? echo $form->error("host"); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;" colspan="2"><input class="green_btn editproject_form_btn" type="submit" value="Αποθήκευση" /></td>
                        <td></td>
                    </tr>
                    </table>
                    <input type="hidden" name="subeditproject" value="1" />
                    <input type="hidden" name="old_title" value="<? echo $selected_project ?>" />
                    </form>
                </div>
                <? if ($session->isAdmin()){ ?>
                <div class="delete">
                    <a href="deletePro.php?p=<? echo $session->avail_projects[$selected_project][title] ?>">
                        <img src="styling/images/x_button.png" width="100" height="100" />
                        <br />
                        <span>Διαγραφή του Project</span>
                    </a>
                    
                </div>
                <? } ?>
                
                <? $avail_users=$session->availableUsers_for_project_assignement($selected_project, GROUPED);
                //var_dump($session->getUsers_on_Project($selected_project,GROUPED));
                if (count($avail_users)>0){?>
                <div id="available_users_div">
                    <form action="process.php" method="POST"><?
                    $counter=0;     
                    foreach ($avail_users as $users_type=>$users){ ?>
                        <div class="users_column rounded">
                            <table>
                            <tr><th><? echo $users_type; ?></th></tr><?
                            foreach ($users as $user=>$data){
                            $counter++;?>
                            <tr>
                            <td><? echo $user ?></td><td><input type="checkbox" name="add<? echo $counter ?>" value="<? echo $user ?>" /></td>
                            </tr>
                            <? }?>
                            </table>
                        </div>
                    <? }?>

                    <div style="clear:left; margin-bottom: 10px;"></div>
                    <input type="hidden" name="project" value="<? echo $selected_project?>" />
                    <input type="hidden" name="sub_addto_project" value="1" />
                    <input type="submit" class="blue_btn confirm" value="Καταχώρηση Νέων Χρηστών" /><? echo $form->error("add_users"); ?>
                    </form>
                </div>
                <? } ?>
                
                <? $avail_users=$session->availableUsers_for_project_removal($selected_project, GROUPED);
                if (count($avail_users)>0){?>
                <div id="remove_users_div">
                    <form action="process.php" method="POST"><?
                    $counter=0;
                    foreach ($avail_users as $users_type=>$users){ ?>
                        <div class="users_column rounded">
                            <table>
                            <tr><th><? echo $users_type; ?></th></tr><?
                            foreach ($users as $user=>$data){
                            $counter++;?>
                            <tr>
                            <td><? echo $user ?></td><td><input type="checkbox" name="remove<? echo $counter ?>" value="<? echo $user ?>" /></td>
                            </tr>
                            <? }?>
                            </table>
                        </div>
                    <? }?>

                    <div style="clear:left; margin-bottom: 10px;"></div>
                    <input type="hidden" name="project" value="<? echo $selected_project?>" />
                    <input type="hidden" name="sub_removefrom_project" value="1"  />
                    <input type="submit" class="red_btn confirm" value="Διαγραφή Χρηστών" /><? echo $form->error("remove_users"); ?>
                    </form>
                </div>
                <? } ?>
                <div style="clear:both"></div>
           </div>
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
<script type="text/javascript" src="js/document.js"></script>
</body>
</html>