<?php
include("include/session.php");
$selected_project=$_GET["p"];
if (is_array($session->avail_projects)){
	if (in_array($selected_project,array_keys($session->avail_projects))) $found=true;
}


if(!$session->isAdmin() or !$session->logged_in or !$found){
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
	<div id="main_one">
        <div class="deleteproject">
            <form name="editProject_form" action="process.php" method="POST">
                <h1>Διαγραφή <? echo $selected_project; ?></h1>
                <div class="content-left">
                <p>Είστε σίγουρος πως θέλετε να διαγράψετε<br />
                το συγκεκριμένο project?</p>
                
                <table class="newproject_tbl">
                <col />
                <col />
                <tr>
                    <td style="text-align:right;">Τίτλος Project:</td>
                    <td><input type="text" disabled="disabled" name="project" value="<? echo $session->avail_projects[$selected_project][title] ?>" /></td>
                    <td><? echo $form->error("title"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Περιγραφή:</td>
                    <td><textarea disabled="disabled" rows="5" cols="40"><? echo $session->avail_projects[$selected_project][description] ?></textarea></td>
                    <td><? echo $form->error("desc"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">URL:</td>
                    <td><input disabled="disabled" type="text" value="<?
                    if (!empty($session->avail_projects[$selected_project][URL])){echo $session->avail_projects[$selected_project][URL];}
                    else echo "http://"; ?>" /></td>
                    <td><? echo $form->error("url"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Λήξη Domain:</td>
                    <td><input disabled="disabled" type="text" value="<? echo $session->avail_projects[$selected_project][domain_exp] ?>" /></td>
                    <td><? echo $form->error("domain"); ?></td>
                </tr>
                <tr>
                    <td style="text-align:right;">Λήξη Hosting:</td>
                    <td><input disabled="disabled" type="text" value="<? echo $session->avail_projects[$selected_project][host_exp] ?>" /></td>
                    <td><? echo $form->error("host"); ?></td>
                </tr>
                </table>
                <input type="hidden" name="subdeleteproject" value="1" />
                <input type="hidden" name="project_title" value="<? echo $session->avail_projects[$selected_project][title] ?>" />
            	</div>
      			<div class="content-left">
                    <div class="delete final">
                        <input class="confirm" type="submit" value="" />
                        <br />
                        <span>Διαγραφή του Project</span>
                    </div>
                    <div>
                        <a class="return_btn" href="editProject.php?p=<? echo $session->avail_projects[$selected_project][title] ?>">Επιστροφή</a>
                    </div>
            	</div>
            
            	<div style="clear:both"></div>
            
        	</form>
        </div>
        
        
    </div>
    <div style="clear:both"></div>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.corner.js"></script>
<script type="text/javascript" src="js/jdpicker_1.0.3/jquery.jdpicker.js"></script>
<script type="text/javascript" src="js/document.js"></script>
</body>
</html>