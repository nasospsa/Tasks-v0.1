<?php
include("include/session.php");
include("include/notifications.php");
if(!$session->isAdmin() or !$session->logged_in){
   header("Location: main.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Δημιουργία νέου Project | <? echo $session->web_name ?></title>
<link rel="stylesheet" type="text/css" href="styling/style.css"/>
<link rel="stylesheet" type="text/css" href="js/jdpicker_1.0.3/jdpicker.css"/>
</head>

<body>
<div id="wrapper">
    <div id="top">
        <div id="logo"><span>Gan Web Internet Solutions</span><br/><span>Project Management Software</span></div>
        <div id="info">
            <div id="userInfo">					
                <a ref="#!" class="userInfo_button">
                    <img id="avatar" src="<? echo ($session->userinfo[avatar] == "") ? GENERIC_AVATAR : $session->userinfo["avatar"] ?>" width="32"/>
                    <span id="userNav">Welcome, <strong><? echo $session->username ?>!</strong>
                        <img id="userSet" src="styling/images/settings.png" width="17"/>
                    </span>
                    <div class="clr"></div>
                </a>							
            </div>
            <div id="notificationsWrapper">
                <a id="notificationsBtn">
                    <img id="userNotificationsIMG" src="styling/images/activities.png" height="32" />
                </a>
            </div>
            <div class="clr"></div>
            <div class="userMenu">
                <ul> 
                    <? if ($session->isSuperAdmin()) { ?>
                        <li><a href="admin/admin.php">Admin Center</a></li>
                    <? } ?>
                    <li><a href="useredit.php">Edit Profile</a></li>
                    <li><a id="logout" href="process.php">Logout</a></li>
                    <li class="no_border"><h4>System Information</h4>
                        <p>Last login: <? echo $session->time_last ?><br/>
                            Συνολικά Μέλη: <? echo $database->getNumMembers() ?><br/>
                            Συν/μένοι Χρήστες: <span id="current_users"><? echo $database->num_active_users ?></span><br/>
                            Επισκέπτες: <span id="current_guests"><? echo $database->num_active_guests ?></span></p>
                    </li>
                </ul>
            </div>
            <div id="notifications">
                <? if (count($notifications->getUserNotifications(SERIALIZED)) > 0): ?>
                <ul class="notificationsList">
                    <?
                    foreach ($notifications->getUserNotifications(SERIALIZED) as $notification) {
                        echo "<li class=\"$notification[class]\">$notification[message]</li>";
                    }
                    ?>
                    <? endif; ?>
                </ul>
            </div>
        </div>
        <div class="clr"></div>
    </div>
    <div id="mainmenu">
        <ul>
        <? if ($session->isWokring()) { ?>
            <li><a href="main.php?v=myTasks">My Tasks</a></li>
            <li id="current"><a href="main.php?v=All">All</a></li>
            <li><a href="main.php?v=Authored">Authored</a></li>
            <? } else { ?>
            <li id="current"><a href="main.php?v=All">Current</a></li>
            <? } ?>
            <li><a href="main.php?v=Completed">Completed</a></li>
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
                <h2>Δημιουργία νέου Project</h2>
            </div>
            <div class="content-box-content">
                <form name="newProject_form" action="process.php" method="POST">
                <table class="newproject_tbl">
                <col />
                <col />
                <tr>
                    <td style="text-align:right;">Τίτλος Project:</td>
                    <td><input type="text" name="project_title" value="<? echo $form->value("project_title") ?>" /></td>
                    <td><? echo $form->error("title"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Περιγραφή:</td>
                    <td><textarea name="project_desc" rows="5" cols="40"><? echo $form->value("project_desc") ?></textarea></td>
                    <td><? echo $form->error("desc"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">URL:</td>
                    <td><input type="text" class="url" name="project_url" value="<? echo $form->value("project_url") ?>" /></td>
                    <td><? echo $form->error("url"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Λήξη Domain:</td>
                    <td><input type="text" value="<? echo $form->value("project_domain_exp") ?>" class="temp_date disabled" name="project_domain_exp" disabled="disabled"  /></td>
                    <td><? echo $form->error("domain"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Λήξη Hosting:</td>
                    <td><input type="text" value="<? echo $form->value("project_host_exp") ?>" class="temp_date disabled" name="project_host_exp" disabled="disabled"  /></td>
                    <td><? echo $form->error("host"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;" colspan="2"><input type="submit" value="Δημιουργία" /></td>
                    <td></td>
                </tr>
                </table>
                <input type="hidden" name="subnewproject" value="1" />
                </form>
				<?
                if(isset($_SESSION['success'])){?>
                    <? echo $_SESSION['header']; ?>
                    <span class="error">* Υπήρξε πρόβλημα με το νέο project, προσπαθήστε ξανά!</span>
                    <? unset($_SESSION['success']);
				} ?>
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